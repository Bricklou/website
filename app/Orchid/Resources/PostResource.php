<?php

namespace App\Orchid\Resources;

use Orchid\Crud\Resource;
use Orchid\Screen\TD;
use App\Models\Post;
use App\Orchid\Screens\Fields\CKEditor;
use Illuminate\Database\Eloquent\Model;
use Orchid\Crud\ResourceRequest;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Layout;
use Orchid\Screen\Sight;

class PostResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = Post::class;

    /**
     * Get the displayable icon of the resource.
     *
     * @return string
     */
    public static function icon(): string
    {
        return 'notebook';
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            Input::make('title')
                ->type('text')
                ->title(__('Post name'))
                ->placeholder(__('Name'))
                ->required()
                ->max(255),
            Input::make('slug')
                ->type('text')
                ->title(__('Slug'))
                ->placeholder(__('Slug'))
                ->required()
                ->max(255),
            CKEditor::make('content')
                ->required(),
            CheckBox::make('publish')
                ->title(__('Publish the post'))
                ->placeholder(__('Publish the post'))
                ->sendTrueOrFalse(),
            DateTimer::make('published_at')
                ->title(__('Publish date'))
                ->format('Y-m-d H:i:s')
                ->allowInput()
                ->format24hr()
                ->enableTime()
                ->value(now()->addHour()->floorMinute()),
        ];
    }

    /**
     * Get the columns displayed by the resource.
     *
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('id', 'ID')->sort(),

            TD::make('title', 'Title')->sort(),

            TD::make('Published')->render(function (Model $post) {
                if ($post->published_at == null) {
                    return '<i class="text-danger">●</i> ' . __('No');
                }
                return $post->published_at->format('Y-m-d H:i:s');
            }),
            TD::make('created_at', 'Date of creation')
                ->width(160)
                ->render(function ($model) {
                    return $model->created_at->toDateTimeString();
                }),

            TD::make('updated_at', 'Update date')
                ->width(160)
                ->render(function ($model) {
                    return $model->updated_at->toDateTimeString();
                }),
        ];
    }

    /**
     * Get the sights displayed by the resource.
     *
     * @return Sight[]
     */
    public function legend(): array
    {
        return [
            Sight::make('id'),
            Sight::make('title'),
            Sight::make('slug'),
            Sight::make('created_at', 'Created At')->render(function ($model) {
                return $model->created_at->toDateTimeString();
            }),
            Sight::make('updated_at', 'Updated At')->render(function ($model) {
                return $model->updated_at->toDateTimeString();
            }),
            Sight::make('content')->render(function ($model) {
                return $model->content;
            }),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public function filters(): array
    {
        return [];
    }

    /**
     * Action to create and update the model
     *
     * @param ResourceRequest $request
     * @param Model           $model
     */
    public function onSave(ResourceRequest $request, Model $model)
    {
        $data = $request->all();

        // Get publish state
        $publish = $data['publish'];
        unset($data['publish']);

        // Set publish state
        if ($publish) {
            $data['published_at'] = $data['published_at'] ?? now();
        } else {
            $data['published_at'] = null;
        }

        $data['user_id'] = auth()->user()->id;
        $model->forceFill($data)->save();
    }
}
