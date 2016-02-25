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
namespace Flagrow\Split\Posts;

use Flarum\Core\Post;
use Flarum\Core\Post\AbstractEventPost;
use Flarum\Core\Post\MergeableInterface;
use Illuminate\Database\Eloquent\Collection;

class DiscussionSplitPost extends AbstractEventPost implements MergeableInterface {

    /**
     * @var string
     */
    public static $type = 'discussionSplit';

    /**
     * Save the model, given that it is going to appear immediately after the
     * passed model.
     *
     * @param Post $previous
     * @return Post The model resulting after the merge. If the merge is
     *     unsuccessful, this should be the current model instance. Otherwise,
     *     it should be the model that was merged into.
     */
    public function saveAfter(Post $previous)
    {
        // TODO: Implement saveAfter() method.
    }


    /**
     * @param            $discussionId
     * @param            $userId
     * @param Collection $posts
     * @return static
     */
    public static function reply($discussionId, $userId, Collection $posts)
    {
        $post = new Static;

        $post->time = time();
        $post->user_id = $userId;
        $post->discussion_id = $discussionId;

        $post->content = static::buildContent($posts);

        return $post;
    }

    /**
     * @param Collection $posts
     * @return array
     */
    protected static function buildContent(Collection $posts)
    {
        return implode(', ', $posts->lists('id')->toArray());
    }
}