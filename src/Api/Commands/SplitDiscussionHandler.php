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

use Flarum\Discussion\Discussion;
use Flarum\Extension\ExtensionManager;
use Flarum\Post\Post;
use Flarum\Post\PostRepository;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\UserRepository;
use FoF\Split\Events\DiscussionWasSplit;
use FoF\Split\Validators\SplitDiscussionValidator;
use Illuminate\Contracts\Events\Dispatcher;

class SplitDiscussionHandler
{
    public function __construct(
        protected UserRepository $users,
        protected PostRepository $posts,
        protected SettingsRepositoryInterface $settings,
        protected Dispatcher $events,
        protected SplitDiscussionValidator $validator,
        protected ExtensionManager $extensions,
    ) {
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

        // Imported or edited posts can have no author; fall back to the acting user
        // so the new discussion still goes through the supported core start path.
        $discussionAuthor = $startPost->user ?? $command->actor;

        $discussion = Discussion::start($command->title, $discussionAuthor);
        $discussion->setFirstPost($startPost);

        if ($startPost->user_id === null) {
            $discussion->user_id = $discussionAuthor->id;
            $discussion->setRelation('user', $discussionAuthor);
        }

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
     * @param int        $start_post_number
     * @param int        $end_post_number
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function assignPostsToDiscussion(
        Discussion $originalDiscussion,
        Discussion $discussion,
        int $start_post_number,
        int $end_post_number
    ) {
        $this->posts
            ->query()
            ->where('discussion_id', $originalDiscussion->id)
            ->whereBetween('number', [$start_post_number, $end_post_number])
            ->update(['discussion_id' => $discussion->id]);

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
    protected function renumberDiscussion(Discussion $discussion): void
    {
        $discussion->load('posts');

        $number = 0;

        $discussion->posts->sortBy('created_at')->each(function ($post) use (&$number) {
            /** @var Post $post */
            $number++;
            $post->number = $number;
            $post->save();
        });

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
     * @param Discussion $originalDiscussion
     * @param Discussion $discussion
     */
    protected function assignTagsToDiscussion(Discussion $originalDiscussion, Discussion $discussion): void
    {
        // Check if the flarum/tags extension is available and tags are present
        if ($this->extensions->isEnabled('flarum-tags')) {
            $discussion->tags()->sync($originalDiscussion->tags);
        }
    }
}
