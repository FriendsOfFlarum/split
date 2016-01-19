<?php
/*
 * This file is part of flagrow/flarum-ext-split.
 *
 * Copyright (c) Flagrow.
 *
 * http://flagrow.github.io
 *
 * For the full copyright and license information, please view the license.md
 * file that was distributed with this source code.
 */

namespace Flagrow\Split\Commands;

use Flagrow\Split\Validators\SplitDiscussionValidator;
use Flarum\Core\Access\AssertPermissionTrait;
use Flarum\Core\Discussion;
use Flarum\Core\Repository\PostRepository;
use Flarum\Core\Repository\UserRepository;
use Flarum\Core\Support\DispatchEventsTrait;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;

class UploadImageHandler
{

    use DispatchEventsTrait;
    use AssertPermissionTrait;

    /**
     * UploadImageHandler constructor.
     *
     * @param Dispatcher                  $events
     * @param UserRepository              $users
     * @param PostRepository              $posts
     * @param SettingsRepositoryInterface $settings
     * @param SplitDiscussionValidator    $validator
     */
    public function __construct(
        Dispatcher $events,
        UserRepository $users,
        PostRepository $posts,
        SettingsRepositoryInterface $settings,
        SplitDiscussionValidator $validator
    ) {
        $this->events    = $events;
        $this->users     = $users;
        $this->posts     = $posts;
        $this->settings  = $settings;
        $this->validator = $validator;
    }

    /**
     * @param SplitDiscussion $command
     * @return \Flarum\Core\Discussion
     */
    public function handle(SplitDiscussion $command)
    {
        $this->assertCan($command->actor, 'canSplit');

        $this->validator->assertValid([
            'discussion_id' => $command->discussionId,
            'posts'         => $command->posts,
            'title'         => $command->title
        ]);

        sort($command->posts, SORT_NUMERIC);
        $firstSplittedPostId = head($command->posts);

        $firstSplittedPost = $this->posts->findOrFail($firstSplittedPostId);

        // create a new discussion for the user of the first splitted reply.
        $discussion = Discussion::start($command->title, $firstSplittedPost->user);
        // now find all splitted posts and assign these to the new discussion.
        $splittedPosts = $this->posts->findByIds($command->posts);
        $splittedPosts->update(['discussion_id' => $discussion->id]);

        return $discussion;
    }
}