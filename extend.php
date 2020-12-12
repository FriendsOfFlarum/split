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

use Flarum\Api\Serializer\DiscussionSerializer;
use Flarum\Database\AbstractModel;
use Flarum\Discussion\Event\Renamed;
use Flarum\Extend;
use FoF\Split\Events\DiscussionWasSplit;
use FoF\Split\Posts\DiscussionSplitPost;

return [
    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js'),

    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js'),

    (new Extend\Locales(__DIR__ . '/locale')),

    (new Extend\Routes('api'))
        ->post('/split', 'fof.split.run', Api\Controllers\SplitController::class),

    (new Extend\Event())
        ->listen(Renamed::class, Listeners\UpdateSplitTitleAfterDiscussionWasRenamed::class)
        ->listen(DiscussionWasSplit::class, Listeners\CreatePostWhenSplit::class),

    (new Extend\Post())
        ->type(DiscussionSplitPost::class),

    (new Extend\ApiSerializer(DiscussionSerializer::class))
        ->mutate(function (DiscussionSerializer $serializer, AbstractModel $discussion, array $attributes): array {
            $attributes['canSplit'] = $serializer->getActor()->can('split', $discussion);
            return $attributes;
        }),
];
