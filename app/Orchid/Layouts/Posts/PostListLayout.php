<?php

namespace App\Orchid\Layouts\Posts;

use App\Models\Post;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class PostListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'posts';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('title', __('Title'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(function (Post $post) {
                    return Link::make($post->title)
                        ->route('platform.systems.posts.view', $post->id);
                }),

            TD::make('Published')
                ->align(TD::ALIGN_CENTER)
                ->width('170px')
                ->render(function (Post $post) {
                    if ($post->published_at == null) {
                        return '<i class="text-danger">●</i> ' . __('No');
                    }
                    return $post->published_at->format('Y-m-d H:i:s');
                }),

            TD::make('created_at', __('Created at'))
                ->align(TD::ALIGN_CENTER)
                ->width('170px')
                ->sort()
                ->render(function (Post $post) {
                    return $post->created_at->toDateTimeString();
                }),
            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (Post $post) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([
                            Button::make(isset($post->published_at) ? __('Unpublish') : __('Publish'))
                                ->icon('eye')
                                ->method('publishPost', [
                                    'id' => $post->id,
                                    'published' => !isset($post->published_at)
                                ]),
                            Link::make(__('Edit'))
                                ->route('platform.systems.posts.edit', $post->id)
                                ->icon('pencil'),
                            Link::make(__('Delete'))
                                ->icon('trash')
                                ->confirm(__('Do you really want to delete?'))
                                ->method('remove', ['id' => $post->id]),
                        ]);
                })
        ];
    }
}
