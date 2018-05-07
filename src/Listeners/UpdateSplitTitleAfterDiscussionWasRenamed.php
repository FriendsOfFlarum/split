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
use Flarum\Core\Repository\PostRepository;
use Flarum\Forum\UrlGenerator;

class UpdateSplitTitleAfterDiscussionWasRenamed {

    const CHUNK_LIMIT = 10;
    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(DiscussionWasRenamed::class, [$this, 'whenDiscussionWasRenamed']);
    }

    /**
     * @param \Flarum\Event\DiscussionWasRenamed $event
     */
    public function whenDiscussionWasRenamed(DiscussionWasRenamed $event) {
            
	$url = app(UrlGenerator::class);
	// get the url of the discussion that was just renamed (without slug)
        $modifiedPostUrlPretty = $url->toRoute('discussion', [
                'id' => "{$event->discussion->id}"
        ]);
	// escape the strings for mysql search
        $modifiedPostUrlEscaped = str_replace("/","\\\\/",$modifiedPostUrlPretty);
	// generate the new url to be used for the split posts that are connected to
	// the renamed discussion
        $newPostUrl = $url->toRoute('discussion', [
                'id' => "{$event->discussion->id}-{$event->discussion->slug}"
        ]);
        $scopeArr = [
            'newPostUrl' => $newPostUrl,
            'event' => $event
        ];
        $this->posts = app(PostRepository::class);
	// find all the posts that have been split from the renamed discussion
	$this->posts->
            query()->
            where('type',"=","discussionSplit")->
            where('content',"like","%".$modifiedPostUrlEscaped."%")->
            chunk(self::CHUNK_LIMIT, function($allPosts) use (&$scopeArr) {
                foreach($allPosts as $thisPost){
		    // update their relative discussion's title and url
                    $oldContent = $thisPost->getContent();
                    $oldContent['title'] = $scopeArr['event']->discussion->title;
                    $oldContent['url'] = $scopeArr['newPostUrl'];
                    $thisPost->setContent($oldContent);
                    $thisPost->save();
                }
            });
    }
}
