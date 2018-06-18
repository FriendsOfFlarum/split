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

namespace Flagrow\Split\Listeners;

use Illuminate\Contracts\Events\Dispatcher;
use Flarum\Event\DiscussionWasRenamed;
use Flarum\Post\PostRepository;
use Flarum\Http\UrlGenerator;

class UpdateSplitTitleAfterDiscussionWasRenamed {

    const CHUNK_LIMIT = 10;
    
    protected $posts;
    protected $url;
    
    public function __construct(UrlGenerator $url, PostRepository $posts)
    {
        $this->url = $url;
        $this->posts = $posts;
    }
    
    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(DiscussionWasRenamed::class, [$this, 'whenDiscussionWasRenamed']);
    }

    /**
     * @param DiscussionWasRenamed $event
     */
    public function whenDiscussionWasRenamed(DiscussionWasRenamed $event) 
    {
	    // get the url of the discussion that was just renamed (without slug)
        $shortUrl = $this->url->toRoute('discussion', [
                'id' => "{$event->discussion->id}"
        ]);
        
	    // escape the strings for mysql search
        $escaped = str_replace("/","\\\\/",$shortUrl);
        
	    // generate the new url to be used for the split posts that are connected to
	    // the renamed discussion
        $url = $this->url->toRoute('discussion', [
                'id' => "{$event->discussion->id}-{$event->discussion->slug}"
        ]);
        
	    // find all the posts that have been split from the renamed discussion
	    $this->posts
            ->query()
            ->where('type', "=", "discussionSplit")
            ->where('content', "like","%$escaped%")
            ->chunk(self::CHUNK_LIMIT, function($collection) use ($event, $url) {
                $collection->each(function ($post) use ($event, $url) {   
                    $post->setContent(array_merge($post->getContent(), [
                        'title' => $event->discussion->title,
                        'url' => $url
                    ]));
                    
                    $post->save();
                });
            });
    }
}
