<?php

namespace FoF\Split\Tests\integration\api;

use Flarum\Discussion\Discussion;
use Flarum\Discussion\UserState;
use Flarum\Post\Post;
use Flarum\Testing\integration\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SplitReadStateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->extension('fof-split');

        $this->prepareDatabase([
            'users' => [
                [
                    'id' => 1,
                    'username' => 'admin',
                    'email' => 'admin@example.test',
                    'is_email_confirmed' => true,
                    'password' => 'password',
                    'joined_at' => '2026-01-01 00:00:00',
                ],
                [
                    'id' => 2,
                    'username' => 'moderator',
                    'email' => 'moderator@example.test',
                    'is_email_confirmed' => true,
                    'password' => 'password',
                    'joined_at' => '2026-01-01 00:00:00',
                ],
            ],
            'group_user' => [
                ['user_id' => 1, 'group_id' => 1],
                ['user_id' => 2, 'group_id' => 4],
            ],
            'discussions' => [
                [
                    'id' => 1,
                    'title' => 'Source Discussion',
                    'slug' => 'source-discussion',
                    'user_id' => 1,
                    'comment_count' => 5,
                    'participant_count' => 1,
                    'created_at' => '2026-01-01 00:00:00',
                    'last_posted_at' => '2026-01-01 00:04:00',
                    'last_post_number' => 5,
                    'first_post_id' => 10,
                    'last_post_id' => 14,
                ],
            ],
            'posts' => [
                $this->commentPost(10, 1, 1, 1, '2026-01-01 00:00:00', '<p>Post 1</p>'),
                $this->commentPost(11, 1, 1, 2, '2026-01-01 00:01:00', '<p>Post 2</p>'),
                $this->commentPost(12, 1, 1, 3, '2026-01-01 00:02:00', '<p>Post 3</p>'),
                $this->commentPost(13, 1, 1, 4, '2026-01-01 00:03:00', '<p>Post 4</p>'),
                $this->commentPost(14, 1, 1, 5, '2026-01-01 00:04:00', '<p>Post 5</p>'),
            ],
            'discussion_user' => [
                [
                    'discussion_id' => 1,
                    'user_id' => 1,
                    'last_read_post_number' => 5,
                    'last_read_at' => '2026-01-01 00:04:00',
                ],
            ],
        ]);
    }

    #[Test]
    public function it_clamps_stale_read_state_after_split_and_keeps_the_source_unread_after_a_new_reply(): void
    {
        $splitResponse = $this->send(
            $this->request('POST', '/api/split', [
                'authenticatedAs' => 1,
                'json' => [
                    'title' => 'Split Child',
                    'start_post_id' => 12,
                    'end_post_number' => 5,
                ],
            ])
        );

        $this->assertSame(200, $splitResponse->getStatusCode());

        /** @var Discussion $sourceDiscussion */
        $sourceDiscussion = Discussion::query()->findOrFail(1);
        /** @var UserState $userState */
        $userState = UserState::query()
            ->where('discussion_id', 1)
            ->where('user_id', 1)
            ->firstOrFail();

        $this->assertSame(2, $sourceDiscussion->last_post_number);
        $this->assertSame(2, $userState->last_read_post_number);

        $replyResponse = $this->send(
            $this->request('POST', '/api/posts', [
                'authenticatedAs' => 2,
                'json' => [
                    'data' => [
                        'type' => 'posts',
                        'attributes' => [
                            'content' => 'Moderator reply after split',
                        ],
                        'relationships' => [
                            'discussion' => [
                                'data' => [
                                    'type' => 'discussions',
                                    'id' => '1',
                                ],
                            ],
                        ],
                    ],
                ],
            ])
        );

        $this->assertSame(201, $replyResponse->getStatusCode());

        $sourceDiscussion->refresh();
        $userState = UserState::query()
            ->where('discussion_id', 1)
            ->where('user_id', 1)
            ->firstOrFail();

        $this->assertSame(4, $sourceDiscussion->last_post_number);
        $this->assertSame(2, $userState->last_read_post_number);

        $showResponse = $this->send(
            $this->request('GET', '/api/discussions/1', ['authenticatedAs' => 1])
        );

        $this->assertSame(200, $showResponse->getStatusCode());

        $showJson = json_decode($showResponse->getBody()->getContents(), true);

        $this->assertSame(4, $showJson['data']['attributes']['lastPostNumber']);
        $this->assertSame(2, $showJson['data']['attributes']['lastReadPostNumber']);
    }

    protected function commentPost(
        int $id,
        int $discussionId,
        int $userId,
        int $number,
        string $createdAt,
        string $content
    ): array {
        return [
            'id' => $id,
            'discussion_id' => $discussionId,
            'user_id' => $userId,
            'type' => 'comment',
            'number' => $number,
            'created_at' => $createdAt,
            'content' => $content,
        ];
    }
}
