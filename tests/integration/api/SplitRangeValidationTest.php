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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class SplitRangeValidationTest extends TestCase
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
                ['id' => 2, 'number' => 2, 'discussion_id' => 10, 'created_at' => $date, 'type' => 'comment', 'content' => '<t><p>B</p></t>', 'user_id' => 1],
                ['id' => 3, 'number' => 3, 'discussion_id' => 10, 'created_at' => $date, 'type' => 'comment', 'content' => '<t><p>C</p></t>', 'user_id' => 1],
                ['id' => 4, 'number' => 4, 'discussion_id' => 10, 'created_at' => $date, 'type' => 'comment', 'content' => '<t><p>D</p></t>', 'user_id' => 1],
            ],
        ]);
    }

    public static function invalidRanges(): array
    {
        return [
            'from the opening post'     => [1, 3],
            'ending before it starts'   => [3, 2],
            'without an end'            => [3, null],
            'with a non-numeric end'    => [3, 'last'],
        ];
    }

    #[Test]
    #[DataProvider('invalidRanges')]
    public function rejects_a_range_that_would_break_the_discussions(int $startPostId, int|string|null $endPostNumber)
    {
        $response = $this->send($this->request('POST', '/api/split', [
            'authenticatedAs' => 1,
            'json'            => [
                'title'           => 'Split',
                'start_post_id'   => $startPostId,
                'end_post_number' => $endPostNumber,
            ],
        ]));

        $this->assertEquals(422, $response->getStatusCode(), $response->getBody()->getContents());

        // Nothing was split off.
        $this->assertEquals(1, Discussion::query()->count());
        $this->assertEquals(4, Post::query()->where('discussion_id', 10)->count());
    }

    #[Test]
    public function splits_a_single_post()
    {
        $response = $this->send($this->request('POST', '/api/split', [
            'authenticatedAs' => 1,
            'json'            => [
                'title'           => 'Split',
                'start_post_id'   => 3,
                'end_post_number' => 3,
            ],
        ]));

        $this->assertEquals(200, $response->getStatusCode(), $response->getBody()->getContents());
    }
}
