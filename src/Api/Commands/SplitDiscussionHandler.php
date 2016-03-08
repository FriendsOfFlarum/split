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

namespace Flagrow\Split\Api\Commands;

use Flagrow\Split\Events\DiscussionWasSplit;
use Flagrow\Split\Validators\SplitDiscussionValidator;
use Flarum\Core\Access\AssertPermissionTrait;
use Flarum\Core\Discussion;
use Flarum\Core\Repository\PostRepository;
use Flarum\Core\Repository\UserRepository;
use Flarum\Core\Support\DispatchEventsTrait;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;

class SplitDiscussionHandler
{

    use DispatchEventsTrait;
    use AssertPermissionTrait;

    protected $users;
    protected $posts;
    protected $settings;
    protected $validator;

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
        $this->assertCan($command->actor, 'split');

        $this->validator->assertValid([
            'start_post_id' => $command->start_post_id,
            'end_post_id'   => $command->end_post_id,
            'title'         => $command->title
        ]);

        // load the first selected post to split.
        $startPost = $this->posts->findOrFail($command->start_post_id, $command->actor);

        // load the discussion this split action is taking place on.
        $originalDiscussion = $startPost->discussion;

        // create a new discussion for the user of the first splitted reply.
        $discussion = Discussion::start($command->title, $startPost->user);
        $discussion->setStartPost($startPost);

        if (!empty($originalDiscussion->tags))
        {
            $discussion->tags()->sync($originalDiscussion->tags);
        }

        // persist the new discussion.
        $discussion->save();

        // update all posts that are split.
        $affectedPosts = $this->assignPostsToDiscussion($originalDiscussion, $discussion, $startPost->id, $command->end_post_id);

        $originalDiscussion = $this->refreshDiscussion($originalDiscussion);
        $discussion = $this->refreshDiscussion($discussion);

        $this->events->fire(
            new DiscussionWasSplit($command->actor, $affectedPosts, $originalDiscussion, $discussion)
        );

        return $discussion;
    }

    /**
     * Assign the specific range to a new Discussion.
     *
     * @param Discussion $originalDiscussion
     * @param Discussion $discussion
     * @param            $start_post_id
     * @param            $end_post_id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function assignPostsToDiscussion(Discussion $originalDiscussion, Discussion $discussion, $start_post_id, $end_post_id)
    {
        $this->posts
            ->query()
            ->where('discussion_id', $originalDiscussion->id)
            ->whereBetween('id', [$start_post_id, $end_post_id])
            ->update(['discussion_id' => $discussion->id]);

        // Update relationship posts on new discussion.
        $discussion->load('posts');

        return $discussion->posts;
    }

    /**
     * Refreshes count and last Post for the discussion.
     *
     * @param Discussion $discussion
     */
    protected function refreshDiscussion(Discussion $discussion)
    {
        $discussion->refreshLastPost();
        $discussion->refreshCommentsCount();
        $discussion->refreshParticipantsCount();

        // Persist the new statistics.
        $discussion->save();

        return Discussion::find($discussion->id);
    }
}
