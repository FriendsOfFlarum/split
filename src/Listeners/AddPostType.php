<?php

/*
 * This file is part of fof/split.
 *
 * Copyright (c) 2020 FriendsOfFlarum
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace FoF\Split\Listeners;

use Flarum\Event\ConfigurePostTypes;
use FoF\Split\Posts\DiscussionSplitPost;

class AddPostType
{
    public function handle(ConfigurePostTypes $event)
    {
        $event->add(DiscussionSplitPost::class);
    }
}