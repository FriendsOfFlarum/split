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

namespace FoF\Split\Tests\integration;

use Carbon\Carbon;
use Flarum\Audit\AuditLog;
use Flarum\Audit\AuditLogger;
use Flarum\Discussion\Discussion;
use Flarum\Post\Post;
use Flarum\Testing\integration\RetrievesAuthorizedUsers;
use Flarum\Testing\integration\TestCase;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;

class AuditTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    protected function setUp(): void
    {
        parent::setUp();

        // Lifecycle events fired outside the test transaction shouldn't create stray entries.
        AuditLogger::$testMode = true;

        $this->extension('flarum-audit', 'fof-split');

        $date = Carbon::parse('2021-01-01T12:00:00+00:00');

        $this->prepareDatabase([
            'audit_log'       => [],
            Discussion::class => [
                ['id' => 10, 'title' => 'A', 'created_at' => $date, 'last_posted_at' => $date, 'first_post_id' => 1, 'comment_count' => 4],
            ],
            Post::class => [
                ['id' => 1, 'number' => 1, 'discussion_id' => 10, 'created_at' => $date, 'type' => 'comment', 'content' => '<t><p>A</p></t>'],
                ['id' => 2, 'number' => 2, 'discussion_id' => 10, 'created_at' => $date, 'type' => 'comment', 'content' => '<t><p>B</p></t>', 'user_id' => 1],
                ['id' => 3, 'number' => 3, 'discussion_id' => 10, 'created_at' => $date, 'type' => 'comment', 'content' => '<t><p>C</p></t>'],
                ['id' => 4, 'number' => 4, 'discussion_id' => 10, 'created_at' => $date, 'type' => 'comment', 'content' => '<t><p>D</p></t>'],
            ],
        ]);
    }

    #[Test]
    public function split()
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

        $newDiscussionId = Arr::get(json_decode($body, true), 'data.id');

        $log = AuditLog::query()->where('action', 'discussion.split_away')->first();
        $this->assertNotNull($log);
        $this->assertEquals(1, $log->actor_id);
        $this->assertEquals([
            'discussion_id'     => 10,
            'new_discussion_id' => $newDiscussionId,
            'post_count'        => 2,
        ], $log->payload);
        $this->assertEquals('127.0.0.1', $log->ip_address);

        $log = AuditLog::query()->where('action', 'discussion.split_into')->first();
        $this->assertNotNull($log);
        $this->assertEquals(1, $log->actor_id);
        $this->assertEquals([
            'discussion_id'          => $newDiscussionId,
            'original_discussion_id' => 10,
            'post_count'             => 2,
        ], $log->payload);
        $this->assertEquals('127.0.0.1', $log->ip_address);
    }
}
