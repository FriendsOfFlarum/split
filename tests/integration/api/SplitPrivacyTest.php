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
use Flarum\Extend;
use Flarum\Post\Post;
use Flarum\Testing\integration\TestCase;
use Flarum\User\User;
use FoF\Split\Events\DiscussionWasSplit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;

class SplitPrivacyTest extends TestCase
{
    /**
     * Discussions a DiscussionWasSplit listener has marked private.
     *
     * @var int[]
     */
    public static array $privateIds = [];

    protected function setUp(): void
    {
        parent::setUp();

        self::$privateIds = [];

        // Mimics fof/byobu: a DiscussionWasSplit listener stores state (byobu copies the
        // recipients) that a ModelPrivate checker reads when the discussion is saved.
        $this->extend(
            (new Extend\Event())
                ->listen(DiscussionWasSplit::class, function (DiscussionWasSplit $event) {
                    self::$privateIds[] = $event->newDiscussion->id;
                }),
            (new Extend\ModelPrivate(Discussion::class))
                ->checker(fn (Discussion $discussion) => in_array($discussion->id, self::$privateIds, true)),
            // Core shows private discussions to nobody unless a viewPrivate scoper allows it.
            (new Extend\ModelVisibility(Discussion::class))
                ->scope(function (User $actor, Builder $query) {
                    if ($actor->isAdmin()) {
                        $query->whereRaw('TRUE');
                    }
                }, 'viewPrivate'),
        );

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

    #[Test]
    public function new_discussion_is_private_when_a_split_listener_makes_it_so()
    {
        $response = $this->send($this->request('POST', '/api/split', [
            'authenticatedAs' => 1,
            'json'            => [
                'title'           => 'Split',
                'start_post_id'   => 3,
                'end_post_number' => 4,
            ],
        ]));

        $body = $response->getBody()->getContents();

        $this->assertEquals(200, $response->getStatusCode(), $body);

        $newDiscussionId = (int) Arr::get(json_decode($body, true), 'data.id');

        $this->assertEquals([$newDiscussionId], self::$privateIds);
        $this->assertEquals(1, Discussion::query()->where('id', $newDiscussionId)->value('is_private'));

        // The public original stays visible to guests; the private split-off half must not be.
        $this->assertEquals(200, $this->send($this->request('GET', '/api/discussions/10'))->getStatusCode());
        $this->assertEquals(404, $this->send($this->request('GET', "/api/discussions/$newDiscussionId"))->getStatusCode());
    }
}
