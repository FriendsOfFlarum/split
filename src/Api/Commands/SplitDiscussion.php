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

use Flarum\User\User;

class SplitDiscussion
{
    /**
     * SplitDiscussion constructor.
     *
     * @param string $title
     * @param int    $start_post_id
     * @param int    $end_post_number
     **/
    public function __construct(public $title, public $start_post_id, public $end_post_number, public User $actor)
    {
    }
}
