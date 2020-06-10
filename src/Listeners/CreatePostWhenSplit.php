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
use Flarum\Event\ConfigurePostTypes;
use Illuminate\Events\Dispatcher;

class CreatePostWhenSplit
{
    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(ConfigurePostTypes::class, [$this, 'addPostType']);
        $events->listen(DiscussionWasSplit::class, [$this, 'whenDiscussionWasSplit']);
    }

    /**
     * @param ConfigurePostTypes $event
     */
    public function addPostType(ConfigurePostTypes $event)
    {
        $event->add(DiscussionSplitPost::class);
    }

    /**
     * @param DiscussionWasSplit $event
     */
    public function whenDiscussionWasSplit(DiscussionWasSplit $event)
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
