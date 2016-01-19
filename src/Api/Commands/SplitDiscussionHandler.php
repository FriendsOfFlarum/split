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

namespace Flagrow\Split\Commands;

use Flagrow\Split\Validators\SplitDiscussionValidator;
use Flarum\Core\Access\AssertPermissionTrait;
use Flarum\Core\Repository\PostRepository;
use Flarum\Core\Repository\UserRepository;
use Flarum\Core\Support\DispatchEventsTrait;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;

class UploadImageHandler
{

    use DispatchEventsTrait;
    use AssertPermissionTrait;

    /**
     * UploadImageHandler constructor.
     *
     * @param Dispatcher                  $events
     * @param UserRepository              $users
     * @param PostRepository              $posts
     * @param Application                 $app
     * @param SettingsRepositoryInterface $settings
     * @param SplitDiscussionValidator    $validator
     */
    public function __construct(
        Dispatcher $events,
        UserRepository $users,
        PostRepository $posts,
        Application $app,
        SettingsRepositoryInterface $settings,
        SplitDiscussionValidator $validator
    ) {
        $this->events    = $events;
        $this->users     = $users;
        $this->posts     = $posts;
        $this->app       = $app;
        $this->settings  = $settings;
        $this->validator = $validator;
    }

    /**
     * @param SplitDiscussion $command
     */
    public function handle(SplitDiscussion $command)
    {
        $this->validator->assertValid([
            'discussion_id' => $command->discussionId,
            'posts'         => $command->posts,
            'title'         => $command->title
        ]);
    }
}