<?php

namespace Flagrow\Split\Events;

use Flarum\Core\Discussion;
use Flarum\Core\User;

class DiscussionWasSplit
{

    /**
     * @var User
     */
    public $actor;

    /**
     * @var \Illuminate\Support\Collection
     */
    public $posts;

    /**
     * @var int
     */
    public $originalDiscussionId;

    /**
     * @var Discussion
     */
    public $newDiscussion;

    /**
     * DiscussionWasSplit constructor.
     *
     * @param User       $actor
     * @param            $posts
     * @param            $originalDiscussionId
     * @param Discussion $newDiscussion
     */
    public function __construct(User $actor, $posts, $originalDiscussionId, Discussion $newDiscussion)
    {
        $this->actor                = $actor;
        $this->posts                = $posts;
        $this->originalDiscussionId = $originalDiscussionId;
        $this->newDiscussion        = $newDiscussion;
    }
}