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

namespace Flagrow\Split\Api\Serializers;
use Flarum\Api\Serializer\AbstractSerializer;

class SplitSerializer extends AbstractSerializer
{
    /**
     * @var string
     */
    protected $type = 'flagrow-split';
    /**
     * Get the default set of serialized attributes for a model.
     *
     * @param object|array $model
     * @return array
     */
    protected function getDefaultAttributes($model)
    {

        return [
            'new_discussion_title' => $model->new_discussion_title,
            'actor' => (int) $model->actor,

        ];
    }
}
