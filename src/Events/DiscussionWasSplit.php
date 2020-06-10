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

namespace FoF\Split\Events;

use Flarum\Discussion\Discussion;
use Flarum\User\User;
use Illuminate\Database\Eloquent\Collection;

class DiscussionWasSplit
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var \Illuminate\Database\Eloquent\Collection
     */
    public $posts;

    /**
     * @var Discussion
     */
    public $originalDiscussion;

    /**
     * @var Discussion
     */
    public $newDiscussion;

    /**
     * DiscussionWasSplit constructor.
     *
     * @param User       $actor
     * @param Collection $posts
     * @param Discussion $originalDiscussion
     * @param Discussion $newDiscussion
     */
    public function __construct(User $actor, Collection $posts, Discussion $originalDiscussion, Discussion $newDiscussion)
    {
        $this->actor = $actor;
        $this->posts = $posts;
        $this->originalDiscussion = $originalDiscussion;
        $this->newDiscussion = $newDiscussion;
    }
}
