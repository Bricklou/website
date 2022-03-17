@extends('layouts.base')

@section('content')
    <div class="rounded-sm shadow-lg bg-white px-2 py-4 md:p-8 text-black">
        <header class="mb-4 flex flex-col md:flex-row md:items-center mx-4 md:mx-0">
            <h1 class="text-4xl font-bold mb-2 md:mb-0 flex-1">{{ $post->title }}</h1>

            <p class="italic text-sm text-gray-900 ml-2 md:ml-0">Publié le {{ $post->published_at }}</p>
        </header>

        <div class="my-8 mx-2 md:mx-8 content">
            {!! $post->content !!}

        </div>
    </div>
@endsection
