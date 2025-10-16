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
     * DiscussionWasSplit constructor.
     *
     */
    public function __construct(public User $actor, public Collection $posts, public Discussion $originalDiscussion, public Discussion $newDiscussion)
    {
    }
}
