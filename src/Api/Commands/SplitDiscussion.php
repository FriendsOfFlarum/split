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

use Flarum\Core\User;

class SplitDiscussion
{
    /**
     * The ID of the original discussion.
     *
     * @var int
     */
    public $discussionId;

    /**
     * The title of the new discussion.
     *
     * @var string
     */
    public $title;

    /**
     * The posts (replies) to move to the new discussion.
     *
     * @var array
     */
    public $posts;

    /**
     * The user performing the action.
     *
     * @var User
     */
    public $actor;

    /**
     * SplitDiscussion constructor.
     *
     * @param null  $discussionId
     * @param       $title
     * @param array $posts
     * @param User  $actor
     */
    public function __construct($discussionId = null, $title, $posts = [], User $actor)
    {
        $this->discussionId = $discussionId;
        $this->title = $title;
        $this->posts = $posts;
        $this->actor = $actor;
    }
}
