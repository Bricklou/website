<?php

namespace App\Orchid\Screens\Posts;

use App\Models\Post;
use App\Orchid\Layouts\Posts\PostEditLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;
use Pandoc\Pandoc;

class PostEditScreen extends Screen
{
    /**
     * @var Post
     */
    public $post;

    /**
     * Query data.
     *
     * @param Post $post
     *
     * @return array
     */
    public function query(Post $post): iterable
    {
        return [
            'post' => $post,
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->post->exists ? 'Edit Post' : 'Create Post';
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return 'Details such as title, content and publish date';
    }

    /**
     * @return iterable|null
     */
    public function permission(): ?iterable
    {
        return [
            'manage_posts',
        ];
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        $post_published = $this->post->exists && isset($this->post->published_at);
        return [
            Link::make(__('Close'))
                ->icon('close')
                ->route('platform.systems.posts.view', [
                    'post' => $this->post->id,
                ])
                ->canSee($this->post->exists),
            Button::make(__('Remove'))
                ->icon('trash')
                ->confirm(__('Are you sure you want to delete this post?'))
                ->method('remove')
                ->canSee($this->post->exists),
            Button::make(__('Save'))
                ->icon('save')
                ->method('save'),

            Button::make($post_published ? __('Unpublish') : __('Save and publish'))
                ->icon('eye')
                ->confirm($post_published ? __("Are you sure you want to unpublish this post?") : __("Are you sure you want to save and publish this post?"))
                ->method('publish', [
                    'state' => !$post_published,
                ]),
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            PostEditLayout::class,
        ];
    }

    /**
     * @param Post $post
     * @param Request $request
     */
    public function save(Post $post, Request $request)
    {
        $creating = !$post->exists;
        $request->validate([
            'post.title' => 'required|max:255',
            'post.slug' => [
                'required', 'max:255',
                Rule::unique('posts', 'slug')->ignore($post->id),
            ],
            'post.content' => 'required',
            'post.published_at' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        $p = $request->all()['post'];
        $p['user_id'] = auth()->user()->id;

        $post->forceFill($p);
        $post->save();

        Toast::info(__('Post saved'));

        if ($creating) {
            return redirect()->route('platform.systems.posts.view', [
                'post' => $post->id,
            ]);
        }
    }

    /**
     * @param Post $post
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(Post $post)
    {
        $post->delete();

        Toast::info(__('Post was removed'));

        return redirect()->route('platform.systems.posts');
    }

    /**
     * @param Post $post
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function publish(Post $post, Request $request)
    {
        $data = $request->all();

        $post->forceFill($data['post']);
        $post->published_at = $data['state'] ? now() : null;
        $post->save();

        Toast::info($request->get('state') ? __('Post published') : __('Post unpublished'));

        return redirect()->route('platform.systems.posts.view', [
            'post' => $post->id,
        ]);
    }
}
