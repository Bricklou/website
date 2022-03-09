<?php

namespace App\Orchid\Layouts\Posts;

use App\Orchid\Screens\Fields\CKEditor;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class PostEditLayout extends Rows
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    /**
     * Get the fields elements to be displayed.
     *
     * @return Field[]
     */
    protected function fields(): iterable
    {
        return [
            Input::make('post.title')
                ->type('text')
                ->max(255)
                ->required()
                ->title(__('Title'))
                ->placeholder(__('Title'))
                ->help(__('Post title')),
            Input::make('post.slug')
                ->type('text')
                ->max(255)
                ->required()
                ->title(__('Slug'))
                ->placeholder(__('Slug'))
                ->help(__('Actual name in the system')),

            CKEditor::make('post.content')
                ->title(__('Content'))
                ->placeholder(__('Content'))
                ->help(__('Post content'))
                ->required(),

            DateTimer::make('post.published_at')
                ->title(__('Publish date'))
                ->help(__('Post publish date'))
                ->format24hr()
                ->hourIncrement(1)
                ->allowEmpty()
                ->allowInput()
                ->enableTime(),
        ];
    }
}
