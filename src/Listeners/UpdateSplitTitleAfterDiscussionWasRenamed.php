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

use Flarum\Discussion\Event\Renamed;
use Flarum\Http\UrlGenerator;
use Flarum\Post\PostRepository;
use FoF\Split\Posts\DiscussionSplitPost;

class UpdateSplitTitleAfterDiscussionWasRenamed
{
    const CHUNK_LIMIT = 10;

    protected $posts;
    protected $url;

    public function __construct(UrlGenerator $url, PostRepository $posts)
    {
        $this->url = $url;
        $this->posts = $posts;
    }

    /**
     * @param Renamed $event
     */
    public function handle(Renamed $event)
    {
        // get the url of the discussion that was just renamed (without slug)
        $shortUrl = $this->url->to('forum')->route('discussion', ['id' => $event->discussion->id]);

        // escape the strings for mysql search
        $escaped = str_replace('/', '\\\\/', $shortUrl);

        // generate the new url to be used for the split posts that are connected to
        // the renamed discussion
        $url = $this->url->to('forum')->route('discussion', ['id' => "{$event->discussion->id}-{$event->discussion->slug}"]);

        // find all the posts that have been split from the renamed discussion
        DiscussionSplitPost::query()
            ->where('type', '=', 'discussionSplit')
            ->where('content', 'like', "%$escaped%")
            ->chunk(self::CHUNK_LIMIT, function ($collection) use ($event, $url) {
                $collection->each(function (DiscussionSplitPost $post) use ($event, $url) {
                    $post->content = array_merge($post->content, [
                        'title' => $event->discussion->title,
                        'url'   => $url,
                    ]);

                    $post->save();
                });
            });
    }
}
