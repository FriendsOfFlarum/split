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

use Carbon\Carbon;
use Flarum\Discussion\Discussion;
use Flarum\Discussion\UserState;
use Flarum\Post\Post;
use Flarum\Testing\integration\RetrievesAuthorizedUsers;
use Flarum\Testing\integration\TestCase;
use Flarum\User\User;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;

class SplitReadStateTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension('fof-split');

        $date = Carbon::parse('2021-01-01T12:00:00+00:00');

        $this->prepareDatabase([
            User::class => [
                $this->normalUser(),
            ],
            Discussion::class => [
                ['id' => 10, 'title' => 'A', 'created_at' => $date, 'last_posted_at' => $date, 'first_post_id' => 1, 'comment_count' => 5, 'last_post_number' => 5, 'user_id' => 1],
            ],
            Post::class => [
                ['id' => 1, 'number' => 1, 'discussion_id' => 10, 'created_at' => $date, 'type' => 'comment', 'content' => '<t><p>A</p></t>', 'user_id' => 1],
                ['id' => 2, 'number' => 2, 'discussion_id' => 10, 'created_at' => $date, 'type' => 'comment', 'content' => '<t><p>B</p></t>', 'user_id' => 1],
                ['id' => 3, 'number' => 3, 'discussion_id' => 10, 'created_at' => $date, 'type' => 'comment', 'content' => '<t><p>C</p></t>', 'user_id' => 1],
                ['id' => 4, 'number' => 4, 'discussion_id' => 10, 'created_at' => $date, 'type' => 'comment', 'content' => '<t><p>D</p></t>', 'user_id' => 1],
                ['id' => 5, 'number' => 5, 'discussion_id' => 10, 'created_at' => $date, 'type' => 'comment', 'content' => '<t><p>E</p></t>', 'user_id' => 1],
            ],
            'discussion_user' => [
                // The admin has read the whole discussion, the normal user only its first post.
                ['discussion_id' => 10, 'user_id' => 1, 'last_read_post_number' => 5, 'last_read_at' => $date],
                ['discussion_id' => 10, 'user_id' => 2, 'last_read_post_number' => 1, 'last_read_at' => $date],
            ],
        ]);
    }

    #[Test]
    public function original_discussion_becomes_unread_again_when_someone_replies_after_its_tail_was_split_off()
    {
        // Split off posts 3-5, so the original keeps posts 1-2.
        $response = $this->send($this->request('POST', '/api/split', [
            'authenticatedAs' => 1,
            'json'            => [
                'title'           => 'Split',
                'start_post_id'   => 3,
                'end_post_number' => 5,
            ],
        ]));

        $this->assertEquals(200, $response->getStatusCode(), $response->getBody()->getContents());

        // Read positions past the posts that remain are pulled back to the last of them.
        $this->assertEquals(2, $this->lastReadPostNumber(1));
        $this->assertEquals(1, $this->lastReadPostNumber(2));
        $this->assertFalse($this->isUnreadFor(1));

        $response = $this->send($this->request('POST', '/api/posts', [
            'authenticatedAs' => 2,
            'json'            => [
                'data' => [
                    'type'          => 'posts',
                    'attributes'    => ['content' => 'A reply after the split'],
                    'relationships' => ['discussion' => ['data' => ['type' => 'discussions', 'id' => '10']]],
                ],
            ],
        ]));

        $this->assertEquals(201, $response->getStatusCode(), $response->getBody()->getContents());

        $this->assertTrue($this->isUnreadFor(1));
    }

    protected function lastReadPostNumber(int $userId): ?int
    {
        return UserState::query()->where('discussion_id', 10)->where('user_id', $userId)->value('last_read_post_number');
    }

    /**
     * Whether the forum would show the original discussion as unread, the way the
     * frontend works it out: it has posts past the user's last read position.
     */
    protected function isUnreadFor(int $userId): bool
    {
        $response = $this->send($this->request('GET', '/api/discussions/10', ['authenticatedAs' => $userId]));

        $attributes = Arr::get(json_decode($response->getBody()->getContents(), true), 'data.attributes');

        return $attributes['lastPostNumber'] > $attributes['lastReadPostNumber'];
    }
}
