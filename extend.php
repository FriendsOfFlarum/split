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

namespace FoF\Split;

use Flarum\Api\Event\Serializing;
use Flarum\Discussion\Event\Renamed;
use Flarum\Event\ConfigurePostTypes;
use Flarum\Extend;
use FoF\Split\Events\DiscussionWasSplit;

return [
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),

    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js'),

    (new Extend\Locales(__DIR__.'/locale')),

    (new Extend\Routes('api'))
        ->post('/split', 'fof.split.run', Api\Controllers\SplitController::class),

    (new Extend\Event())
        ->listen(Renamed::class, Listeners\UpdateSplitTitleAfterDiscussionWasRenamed::class)
        ->listen(Serializing::class, Listeners\AddSplitApi::class)
        ->listen(ConfigurePostTypes::class, Listeners\AddPostType::class)
        ->listen(DiscussionWasSplit::class, Listeners\CreatePostWhenSplit::class),
];
