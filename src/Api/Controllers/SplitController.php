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

use FoF\Split\Api\Commands\SplitDiscussion;
use Flarum\Api\Controller\AbstractShowController;
use Flarum\Api\Serializer\DiscussionSerializer;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class SplitController extends AbstractShowController
{
    /**
     * The serializer instance for this request.
     *
     * @var string
     */
    public $serializer = DiscussionSerializer::class;

    /**
     * @var Dispatcher
     */
    protected $bus;

    /**
     * @param Dispatcher $bus
     */
    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    /**
     * Get the data to be serialized and assigned to the response document.
     *
     * @param ServerRequestInterface $request
     * @param Document               $document
     *
     * @return mixed
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $title = Arr::get($request->getParsedBody(), 'title');
        $start_post_id = Arr::get($request->getParsedBody(), 'start_post_id');
        $end_post_number = Arr::get($request->getParsedBody(), 'end_post_number');
        $actor = $request->getAttribute('actor');

        return $this->bus->dispatch(
            new SplitDiscussion($title, $start_post_id, $end_post_number, $actor)
        );
    }
}
