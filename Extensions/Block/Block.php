<?php

namespace App\Extensions\Block;

use Simflex\Admin\Fields\FieldText;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
use Simflex\Admin\Structure\Data\ParamGroup;
use Simflex\Core\ModuleBase;

/**
 *
 * Output html block in any place
 *
 */

class Block extends ModuleBase
{
    public static function adminParams(): array
    {
        return [
            new ParamGroup(
                name: 'content',
                label: 'Контент',
                fields: [
                    new FieldDefinition(
                        name: 'content',
                        class: FieldText::class,
                        label: 'Текст',
                        params: ['main' => new FieldParams(editorFull: true)],
                    ),
                ],
            ),
        ];
    }

    protected function content(): void
    {
        echo empty($this->params['content']) ? '' : $this->params['content'];
    }
}
