<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostsController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.posts.index');
    }

    public function show(Request $request, $id)
    {
        return view('pages.posts.show');
    }
}
