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

namespace FoF\Split\Api\Commands;

use FoF\Split\Events\DiscussionWasSplit;
use FoF\Split\Validators\SplitDiscussionValidator;
use Flarum\Discussion\Discussion;
use Flarum\Post\Post;
use Flarum\Post\PostRepository;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\UserRepository;
use Illuminate\Contracts\Events\Dispatcher;

class SplitDiscussionHandler
{
    /**
     * @var UserRepository
     */
    protected $users;

    /**
     * @var PostRepository
     */
    protected $posts;

    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var SplitDiscussionValidator
     */
    protected $validator;

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * @param UserRepository              $users
     * @param PostRepository              $posts
     * @param SettingsRepositoryInterface $settings
     * @param Dispatcher                  $events
     * @param SplitDiscussionValidator    $validator
     */
    public function __construct(
        UserRepository $users,
        PostRepository $posts,
        SettingsRepositoryInterface $settings,
        Dispatcher $events,
        SplitDiscussionValidator $validator
    ) {
        $this->users = $users;
        $this->posts = $posts;
        $this->settings = $settings;
        $this->events = $events;
        $this->validator = $validator;
    }

    /**
     * @param \FoF\Split\Api\Commands\SplitDiscussion $command
     *
     * @throws \Flarum\User\Exception\PermissionDeniedException
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return \Flarum\Discussion\Discussion
     */
    public function handle(SplitDiscussion $command)
    {
        $this->validator->assertValid([
            'start_post_id'   => $command->start_post_id,
            'end_post_number' => $command->end_post_number,
            'title'           => $command->title,
        ]);

        // load the first selected post to split.
        $startPost = $this->posts->findOrFail($command->start_post_id, $command->actor);

        $command->actor->assertCan('split', $startPost->discussion);

        /** @var Discussion $originalDiscussion */
        $originalDiscussion = $startPost->discussion;

        // create a new discussion for the user of the first splitted reply.
        $discussion = Discussion::start($command->title, $startPost->user);
        $discussion->setFirstPost($startPost);

        // persist the new discussion.
        $discussion->save();

        $this->assignTagsToDiscussion($originalDiscussion, $discussion);

        // update all posts that are split.
        $affectedPosts = $this->assignPostsToDiscussion(
            $originalDiscussion,
            $discussion,
            $startPost->number,
            $command->end_post_number
        );

        $originalDiscussion = $this->refreshDiscussion($originalDiscussion);
        $this->renumberDiscussion($discussion);
        $discussion = $this->refreshDiscussion($discussion);

        $this->events->dispatch(
            new DiscussionWasSplit($command->actor, $affectedPosts, $originalDiscussion, $discussion)
        );

        return $discussion;
    }

    /**
     * Assign the specific range to a new Discussion without resetting post numbers.
     *
     * @param Discussion $originalDiscussion
     * @param Discussion $discussion
     * @param            $start_post_number
     * @param            $end_post_number
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function assignPostsToDiscussion(
        Discussion $originalDiscussion,
        Discussion $discussion,
        $start_post_number,
        $end_post_number
    ) {
        $this->posts
            ->query()
            ->where('discussion_id', $originalDiscussion->id)
            ->whereBetween('number', [$start_post_number, $end_post_number])
            ->update(['discussion_id' => $discussion->id]);

        $discussion->post_number_index = $end_post_number;
        $discussion->save();

        // Update relationship posts on new discussion.
        $discussion->load('posts');

        return $discussion->posts;
    }

    /**
     * Re-assign numbers starting from one to a discussion.
     *
     * @param Discussion $discussion
     */
    protected function renumberDiscussion(Discussion $discussion)
    {
        $discussion->load('posts');

        $number = 0;

        $discussion->posts->sortBy('created_at')->each(function (Post $post) use (&$number) {
            $number++;
            $post->number = $number;
            $post->save();
        });

        $discussion->post_number_index = $number;
        $discussion->save();
    }

    /**
     * Refreshes count and last Post for the discussion.
     *
     * @param \Flarum\Discussion\Discussion $discussion
     *
     * @return mixed
     */
    protected function refreshDiscussion(Discussion $discussion)
    {
        $discussion->refreshLastPost();
        $discussion->refreshCommentCount();
        $discussion->refreshParticipantCount();

        // Persist the new statistics.
        $discussion->save();

        return Discussion::find($discussion->id);
    }

    /**
     * Sets the tags for the new discussion based on the old one.
     *
     * @param $originalDiscussion
     * @param $discussion
     */
    protected function assignTagsToDiscussion($originalDiscussion, $discussion)
    {
        if ($originalDiscussion->tags) {
            $discussion->tags()->sync($originalDiscussion->tags);
        }
    }
}
