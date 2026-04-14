<?php

/*
 * This file is part of fof/split.
 *
 * Copyright (c) Flagrow.
 * Copyright (c) 2020 FriendsOfFlarum
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace FoF\Split\Tests\integration\api;

use Flarum\Testing\integration\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SplitDiscussionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->extension('fof-split');

        $this->prepareDatabase([
            'discussions' => [
                [
                    'id'                  => 1,
                    'title'               => 'Original Discussion',
                    'slug'                => 'original-discussion',
                    'comment_count'       => 5,
                    'participant_count'   => 1,
                    'created_at'          => '2024-01-01 00:00:00',
                    'user_id'             => 1,
                    'first_post_id'       => 1,
                    'last_posted_at'      => '2024-01-01 00:04:00',
                    'last_posted_user_id' => 1,
                    'last_post_id'        => 5,
                    'last_post_number'    => 5,
                ],
            ],
            'posts' => [
                [
                    'id'            => 1,
                    'discussion_id' => 1,
                    'number'        => 1,
                    'created_at'    => '2024-01-01 00:00:00',
                    'user_id'       => 1,
                    'type'          => 'comment',
                    'content'       => '<p>Opening post</p>',
                    'ip_address'    => '127.0.0.1',
                    'is_private'    => 0,
                ],
                [
                    'id'            => 2,
                    'discussion_id' => 1,
                    'number'        => 2,
                    'created_at'    => '2024-01-01 00:01:00',
                    'user_id'       => null,
                    'type'          => 'comment',
                    'content'       => '<p>Deleted author reply</p>',
                    'ip_address'    => '127.0.0.1',
                    'is_private'    => 0,
                ],
                [
                    'id'            => 3,
                    'discussion_id' => 1,
                    'number'        => 3,
                    'created_at'    => '2024-01-01 00:02:00',
                    'user_id'       => null,
                    'type'          => 'comment',
                    'content'       => '<p>Deleted author follow-up</p>',
                    'ip_address'    => '127.0.0.1',
                    'is_private'    => 0,
                ],
                [
                    'id'            => 4,
                    'discussion_id' => 1,
                    'number'        => 4,
                    'created_at'    => '2024-01-01 00:03:00',
                    'user_id'       => 1,
                    'type'          => 'comment',
                    'content'       => '<p>Existing author reply</p>',
                    'ip_address'    => '127.0.0.1',
                    'is_private'    => 0,
                ],
                [
                    'id'            => 5,
                    'discussion_id' => 1,
                    'number'        => 5,
                    'created_at'    => '2024-01-01 00:04:00',
                    'user_id'       => 1,
                    'type'          => 'comment',
                    'content'       => '<p>Existing author follow-up</p>',
                    'ip_address'    => '127.0.0.1',
                    'is_private'    => 0,
                ],
            ],
        ]);
    }

    #[Test]
    public function it_can_split_posts_when_the_selected_start_post_author_has_been_deleted(): void
    {
        $response = $this->sendSplitRequest('Split Discussion', 2, 3);

        $this->assertSame(200, $response->getStatusCode());

        $payload = json_decode((string) $response->getBody(), true);

        $this->assertIsArray($payload);
        $this->assertSame('Split Discussion', $payload['data']['attributes']['title']);

        $newDiscussionId = (int) $payload['data']['id'];

        $discussion = $this->database()->table('discussions')->where('id', $newDiscussionId)->first();

        $this->assertNotNull($discussion);
        $this->assertSame(2, $discussion->first_post_id);
        $this->assertSame(1, $discussion->user_id);

        $splitPosts = $this->database()->table('posts')
            ->where('discussion_id', $newDiscussionId)
            ->where('type', 'comment')
            ->orderBy('number')
            ->pluck('id')
            ->all();

        $this->assertSame([2, 3], $splitPosts);
    }

    #[Test]
    public function it_still_uses_the_normal_creation_path_when_the_start_post_author_exists(): void
    {
        $response = $this->sendSplitRequest('Normal Split Discussion', 4, 5);

        $this->assertSame(200, $response->getStatusCode());

        $payload = json_decode((string) $response->getBody(), true);

        $this->assertIsArray($payload);

        $newDiscussionId = (int) $payload['data']['id'];

        $discussion = $this->database()->table('discussions')->where('id', $newDiscussionId)->first();

        $this->assertNotNull($discussion);
        $this->assertSame(4, $discussion->first_post_id);
        $this->assertSame(1, $discussion->user_id);

        $renumberedPosts = $this->database()->table('posts')
            ->where('discussion_id', $newDiscussionId)
            ->where('type', 'comment')
            ->orderBy('id')
            ->pluck('number')
            ->all();

        $this->assertSame([1, 2], $renumberedPosts);
    }

    #[Test]
    public function it_refreshes_original_discussion_metadata_and_creates_split_event_posts(): void
    {
        $response = $this->sendSplitRequest('Split Discussion', 2, 3);

        $this->assertSame(200, $response->getStatusCode());

        $payload = json_decode((string) $response->getBody(), true);

        $this->assertIsArray($payload);

        $newDiscussionId = (int) $payload['data']['id'];

        $originalDiscussion = $this->database()->table('discussions')->where('id', 1)->first();

        $this->assertNotNull($originalDiscussion);
        $this->assertSame(3, $originalDiscussion->comment_count);
        $this->assertSame(1, $originalDiscussion->participant_count);
        $this->assertSame(5, $originalDiscussion->last_post_id);
        $this->assertSame(5, $originalDiscussion->last_post_number);

        $originalSplitPosts = $this->database()->table('posts')
            ->where('discussion_id', 1)
            ->where('type', 'discussionSplit')
            ->count();

        $newSplitPosts = $this->database()->table('posts')
            ->where('discussion_id', $newDiscussionId)
            ->where('type', 'discussionSplit')
            ->count();

        $this->assertSame(1, $originalSplitPosts);
        $this->assertSame(1, $newSplitPosts);
    }

    protected function sendSplitRequest(string $title, int $startPostId, int $endPostNumber)
    {
        $request = $this->request('POST', '/api/split', [
            'authenticatedAs' => 1,
            'json'            => [
                'title'           => $title,
                'start_post_id'   => $startPostId,
                'end_post_number' => $endPostNumber,
            ],
        ]);

        return $this->send($request);
    }
}
