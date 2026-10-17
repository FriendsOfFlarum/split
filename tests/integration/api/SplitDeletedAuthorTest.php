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
use Flarum\Post\Post;
use Flarum\Testing\integration\TestCase;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;

class SplitDeletedAuthorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->extension('fof-split');

        $date = Carbon::parse('2021-01-01T12:00:00+00:00');

        $this->prepareDatabase([
            Discussion::class => [
                ['id' => 10, 'title' => 'A', 'created_at' => $date, 'last_posted_at' => $date, 'first_post_id' => 1, 'comment_count' => 4, 'user_id' => 1],
            ],
            Post::class => [
                ['id' => 1, 'number' => 1, 'discussion_id' => 10, 'created_at' => $date, 'type' => 'comment', 'content' => '<t><p>A</p></t>', 'user_id' => 1],
                // Posts whose author was deleted keep a null user_id.
                ['id' => 2, 'number' => 2, 'discussion_id' => 10, 'created_at' => $date, 'type' => 'comment', 'content' => '<t><p>B</p></t>', 'user_id' => null],
                ['id' => 3, 'number' => 3, 'discussion_id' => 10, 'created_at' => $date, 'type' => 'comment', 'content' => '<t><p>C</p></t>', 'user_id' => 1],
                ['id' => 4, 'number' => 4, 'discussion_id' => 10, 'created_at' => $date, 'type' => 'comment', 'content' => '<t><p>D</p></t>', 'user_id' => 1],
            ],
        ]);
    }

    #[Test]
    public function can_split_from_a_post_whose_author_was_deleted()
    {
        $response = $this->send($this->request('POST', '/api/split', [
            'authenticatedAs' => 1,
            'json'            => [
                'title'           => 'Split',
                'start_post_id'   => 2,
                'end_post_number' => 3,
            ],
        ]));

        $body = $response->getBody()->getContents();

        $this->assertEquals(200, $response->getStatusCode(), $body);

        $discussion = Discussion::query()->findOrFail(Arr::get(json_decode($body, true), 'data.id'));

        // Like the post it now starts with, the new discussion has no author.
        $this->assertNull($discussion->user_id);
        $this->assertEquals(2, $discussion->first_post_id);
        $this->assertEquals([2, 3], $discussion->comments()->orderBy('number')->pluck('id')->all());
    }
}
