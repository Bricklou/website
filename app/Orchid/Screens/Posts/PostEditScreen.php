<?php

namespace App\Orchid\Screens\Posts;

use App\Models\Post;
use App\Orchid\Layouts\Posts\PostEditLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Layout;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout as FacadesLayout;
use Orchid\Support\Facades\Toast;

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
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Post $post, Request $request)
    {
        $request->validate([
            'post.title' => 'required|max:255',
            'post.slug' => 'required|max:255',
            'post.content' => 'required',
            'post.published_at' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        $post->forceFill($request->all()['post']);
        $post->save();

        Toast::info(__('Post saved'));

        return redirect()->route('platform.systems.posts.view', [
            'post' => $post->id,
        ]);
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
        $post->published_at = $data['state'] ? now() : null;
        unset($data['state']);

        $post->forceFill($data);
        $post->save();

        Toast::info($request->get('state') ? __('Post published') : __('Post unpublished'));

        return redirect()->route('platform.systems.posts.view', [
            'post' => $post->id,
        ]);
    }
}
