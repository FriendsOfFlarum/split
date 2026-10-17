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
use Flarum\Discussion\UserState;
use Flarum\Extension\ExtensionManager;
use Flarum\Foundation\ValidationException;
use Flarum\Locale\TranslatorInterface;
use Flarum\Post\Post;
use Flarum\Post\PostRepository;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\UserRepository;
use FoF\Split\Events\DiscussionWasSplit;
use FoF\Split\Validators\SplitDiscussionValidator;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\ConnectionInterface;

class SplitDiscussionHandler
{
    public function __construct(
        protected UserRepository $users,
        protected PostRepository $posts,
        protected SettingsRepositoryInterface $settings,
        protected Dispatcher $events,
        protected ConnectionInterface $db,
        protected SplitDiscussionValidator $validator,
        protected ExtensionManager $extensions,
        protected TranslatorInterface $translator,
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
        // Nobody sees a half-done split, e.g. the new discussion before the
        // listeners have made it private, and a failed split leaves nothing behind.
        return $this->db->transaction(fn () => $this->split($command));
    }

    protected function split(SplitDiscussion $command): Discussion
    {
        $this->validator->assertValid([
            'start_post_id'   => $command->start_post_id,
            'end_post_number' => $command->end_post_number,
            'title'           => $command->title,
        ]);

        // load the first selected post to split.
        $startPost = $this->posts->findOrFail($command->start_post_id, $command->actor);

        $command->actor->assertCan('split', $startPost->discussion);

        // Splitting from the opening post would leave the original discussion without
        // one, and a range that ends before it starts would leave the new one without posts.
        if ($startPost->id === $startPost->discussion->first_post_id) {
            throw new ValidationException(['start_post_id' => $this->translator->trans('fof-split.api.errors.first_post')]);
        }

        if ($command->end_post_number < $startPost->number) {
            throw new ValidationException(['end_post_number' => $this->translator->trans('fof-split.api.errors.end_before_start')]);
        }

        /** @var Discussion $originalDiscussion */
        $originalDiscussion = $startPost->discussion;

        // create a new discussion for the user of the first splitted reply. Not through
        // Discussion::start(), which needs a User: the author may have been deleted, and
        // setFirstPost() takes the author (or its absence) from the post either way.
        $discussion = new Discussion();
        $discussion->title = $command->title;
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
        $this->rewindReadPositions($originalDiscussion);
        $this->renumberDiscussion($discussion);
        $discussion = $this->refreshDiscussion($discussion);

        $this->events->dispatch(
            new DiscussionWasSplit($command->actor, $affectedPosts, $originalDiscussion, $discussion)
        );

        // Listeners can change what the saving hooks work out: fof/byobu copies the
        // recipients across, and they decide is_private. Save the new discussion again
        // from a fresh instance, so no relation loaded before the listeners ran is reused.
        return $this->refreshDiscussion(Discussion::query()->findOrFail($discussion->id));
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
     * Pulls read positions past the posts that remain back to the last of them.
     *
     * When the tail of a discussion is split off, users who had read into it keep
     * a read position past the new end. Replies are numbered from that end again,
     * and would count as read until they passed the old position.
     *
     * @param Discussion $discussion
     */
    protected function rewindReadPositions(Discussion $discussion): void
    {
        $lastNumber = $discussion->posts()->max('number');

        UserState::query()
            ->where('discussion_id', $discussion->id)
            ->where('last_read_post_number', '>', $lastNumber)
            ->update(['last_read_post_number' => $lastNumber]);
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
