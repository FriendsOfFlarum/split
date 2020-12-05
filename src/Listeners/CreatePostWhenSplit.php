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

namespace FoF\Split\Listeners;

use FoF\Split\Events\DiscussionWasSplit;
use FoF\Split\Posts\DiscussionSplitPost;

class CreatePostWhenSplit
{
    /**
     * @param DiscussionWasSplit $event
     */
    public function handle(DiscussionWasSplit $event)
    {
        foreach (['to', 'from'] as $direction) {
            forward_static_call_array(
                [
                    DiscussionSplitPost::class,
                    $direction,
                ],
                [
                    $event->newDiscussion,
                    $event->originalDiscussion,
                    $event->actor,
                    $event->posts,
                ]
            );
        }
    }
}
