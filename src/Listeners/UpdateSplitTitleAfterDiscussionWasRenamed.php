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

class UpdateSplitTitleAfterDiscussionWasRenamed {
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
            
        $this->posts = app(PostRepository::class);
        $allPosts = $this->posts->
                query()->
                where('type',"=","discussionSplit")->
                where('content',"like","%".$event->discussion->id."-%")->
                take(10)->
                get(); 
        if(count($allPosts) == 1){
            $thisPost = $allPosts[0];
            $oldContent = $thisPost->getContent();
            $oldContent['title'] = $event->discussion->title;
            $thisPost->setContent($oldContent);
            $thisPost->save();
        }
    }
}
