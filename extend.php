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

namespace FoF\Split;

use Flarum\Discussion\Event\Renamed;
use Flarum\Extend;
use Illuminate\Contracts\Events\Dispatcher;

return [
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js'),
    (new Extend\Locales(__DIR__.'/locale')),
    (new Extend\Routes('api'))
        ->post('/split', 'fof.split.run', Api\Controllers\SplitController::class),
    function (Dispatcher $events) {
        $events->subscribe(Listeners\AddSplitApi::class);
        $events->subscribe(Listeners\CreatePostWhenSplit::class);
        $events->listen(Renamed::class, Listeners\UpdateSplitTitleAfterDiscussionWasRenamed::class);
    },
];
