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
namespace Flagrow\Split\Api\Controllers;

use Flagrow\Split\Commands\SplitDiscussion;
use Flarum\Api\Controller\AbstractResourceController;
use Illuminate\Contracts\Bus\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class SplitController extends AbstractResourceController
{
    /**
     * The serializer instance for this request.
     *
     * @var ImageSerializer
     */
    public $serializer = SplitSerializer::class;
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
     * @return mixed
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $originalDiscussion = array_get($request->getQueryParams(), 'discussion_id');
        $title = array_get($request->getQueryParams(), 'title');
        $posts = array_get($request->getQueryParams(), 'posts');
        $actor = $request->getAttribute('actor');

        return $this->bus->dispatch(
            new SplitDiscussion($originalDiscussion, $title, $posts, $actor)
        );
    }
}
