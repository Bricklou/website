<?php

namespace App\Orchid\Screens\Posts;

use App\Models\Post;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Pandoc\Pandoc;

class PostViewScreen extends Screen
{
    /**
     * @var Post
     */
    public $post;

    /**
     * Query data.
     *
     * @return array
     */
    public function query(Post $post): iterable
    {
        $post->load(['user']);

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
        return $this->post->title;
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('Download PDF'))
                ->icon('arrow-down-circle')
                ->method('download')
                ->disabled(!$this->post->exists)
                ->rawClick(),
            Button::make(
                isset($this->post->published_at)
                    ? __('Unpublish')
                    : __('Publish')
            )
                ->icon('eye')
                ->confirm(
                    isset($this->post->published_at)
                        ? __('Are you sure you want to unpublish this post?')
                        : __('Are you sure you want to publish this post?')
                )
                ->method('publishPost', [
                    'id' => $this->post->id,
                    'published' => !isset($this->post->published_at)
                ]),
            Link::make(__('Edit'))
                ->route('platform.systems.posts.edit', $this->post->id)
                ->icon('pencil'),
            Button::make(__('Delete'))
                ->icon('trash')
                ->confirm(__('Do you really want to delete?'))
                ->method('remove', ['id' => $this->post->id]),
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
            Layout::legend('post', [
                Sight::make('id', __('ID'))->popover('Identifier, a symbol which uniquely identifies an object or record'),
                Sight::make('title', __('Title')),
                Sight::make('slug', __('Slug')),
                Sight::make('user.name', __('Author')),
                Sight::make('published_at', __('Published'))
                    ->render(function (Post $post) {
                        if ($post->published_at == null) {
                            return '<i class="text-danger">●</i> ' . __('No');
                        }
                        return $post->published_at->format('Y-m-d H:i:s');
                    }),
                Sight::make('created_at', __('Created'))
                    ->render(function (Post $post) {
                        return $post->created_at->toDateTimeString();
                    }),
                Sight::make('updated_at', __('Updated'))
                    ->render(function (Post $post) {
                        return $post->updated_at->toDateTimeString();
                    }),

                Sight::make('content')->popover('Content of the post')
                    ->render(function (Post $post) {
                        return $post->content;
                    }),
            ]),

        ];
    }

    public function publishPost(Request $request)
    {
        $id = $request->get('id');
        $published = $request->get('published');

        if (!isset($id) || !isset($published)) {
            Toast::error(__('Post publish state change failed'));
            return;
        }

        $post = Post::findOrFail($id);
        $post->published_at = $published ? now() : null;
        $post->save();

        Toast::info($published ? __('Post published') : __('Post unpublished'));
    }

    /**
     * @param Post $post
     * @param Request $request
     */
    public function download(Post $post, Request $request)
    {
        try {
            $outputFile = 'public/pdfs/' . $post->slug . '.pdf';
            $path = Storage::path($outputFile);
            $parent_path = dirname($path);

            if (!file_exists($parent_path)) {
                mkdir($parent_path, 0777, true);
            } else if (!is_dir($parent_path)) {
                unlink($parent_path);
                mkdir($parent_path, 0777, true);
            }

            (new Pandoc())
                ->from('html')
                ->input($post->content)
                ->to('latex')
                ->option('pdf-engine', '/usr/bin/pdflatex')
                ->output($path)
                ->run();

            Toast::success('PDF file generated. Downloading...');
            return Storage::download($outputFile, basename($outputFile), [
                "Content-Disposition: attachment"
            ]);
        } catch (\Exception $e) {
            Toast::error("Failed to generate PDF: " . $e->getMessage());
        }
    }
}
