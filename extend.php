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

use Flarum\Api\Context;
use Flarum\Api\Resource;
use Flarum\Api\Schema;
use Flarum\Audit\AuditLogger;
use Flarum\Audit\Extend\Audit;
use Flarum\Discussion\Discussion;
use Flarum\Discussion\Event\Renamed;
use Flarum\Extend;
use FoF\Split\Events\DiscussionWasSplit;
use FoF\Split\Posts\DiscussionSplitPost;

return [
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),

    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->jsDirectory(__DIR__.'/js/dist/forum'),

    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\Routes('api'))
        ->post('/split', 'fof.split.run', Api\Controllers\SplitController::class),

    (new Extend\Event())
        ->listen(Renamed::class, Listeners\UpdateSplitTitleAfterDiscussionWasRenamed::class)
        ->listen(DiscussionWasSplit::class, Listeners\CreatePostWhenSplit::class),

    (new Extend\Post())
        ->type(DiscussionSplitPost::class),

    (new Extend\ApiResource(Resource\DiscussionResource::class))
        ->fields(fn (): array => [
            Schema\Boolean::make('canSplit')
                ->get(fn (Discussion $discussion, Context $context) => $context->getActor()->can('split', $discussion)),
        ]),

    (new Extend\Conditional())
        ->whenExtensionEnabled('flarum-audit', fn () => [
            (new Audit())
                ->group('fof-split')
                ->register('discussion.split_away', 'discussion.split_into')
                ->using(function () {
                    resolve('events')->listen(DiscussionWasSplit::class, function (DiscussionWasSplit $event) {
                        AuditLogger::log('discussion.split_away', [
                            'discussion_id'     => $event->originalDiscussion->id,
                            'new_discussion_id' => $event->newDiscussion->id,
                            'post_count'        => $event->posts->count(),
                        ]);
                        AuditLogger::log('discussion.split_into', [
                            'discussion_id'          => $event->newDiscussion->id,
                            'original_discussion_id' => $event->originalDiscussion->id,
                            'post_count'             => $event->posts->count(),
                        ]);
                    });
                }),
        ]),
];
