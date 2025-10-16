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

namespace FoF\Split\Api\Controllers;

use Flarum\Api\JsonApi;
use Flarum\Api\Resource\DiscussionResource;
use Flarum\Http\RequestUtil;
use FoF\Split\Api\Commands\SplitDiscussion;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SplitController implements RequestHandlerInterface
{
    public function __construct(
        protected Dispatcher $bus,
        protected JsonApi $api
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $title = Arr::get($request->getParsedBody(), 'title');
        $start_post_id = Arr::get($request->getParsedBody(), 'start_post_id');
        $end_post_number = Arr::get($request->getParsedBody(), 'end_post_number');
        $actor = RequestUtil::getActor($request);

        $discussion = $this->bus->dispatch(
            new SplitDiscussion($title, $start_post_id, $end_post_number, $actor)
        );

        return $this->api
            ->forResource(DiscussionResource::class)
            ->forEndpoint('show')
            ->handle(
                $request->withUri($request->getUri()->withPath('/discussions/' . $discussion->id))
                    ->withMethod('GET')
                    ->withParsedBody([])
            );
    }
}
