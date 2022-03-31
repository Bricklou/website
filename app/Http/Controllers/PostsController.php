<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    public function home(Request $request)
    {
        $post = Post::query()
            ->where('published_at', '!=', null)
            ->orderBy('published_at', 'desc')
            ->first();

        return view('pages.home', [
            'post' => $post
        ]);
    }

    public function index(Request $request)
    {
        $posts = Post::query()
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'asc')
            ->cursorPaginate(15);

        return view('pages.posts.index', [
            'posts' => $posts,
        ]);
    }

    public function show(Request $request, $id)
    {
        $post = Post::query()->where('slug', $id)
            ->where('published_at', '<=', now())
            ->first();

        if (!$post) {
            abort(404);
        }

        return view('pages.posts.show', [
            'post' => $post,
        ]);
    }
}
