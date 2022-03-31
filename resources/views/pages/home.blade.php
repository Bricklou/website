@extends('layouts.base')

@section('content')
    <h1 class="text-4xl font-bold flex-1 mb-8">Ma dernière publication</h1>

    <div class="flex flex-col lg:flex-row md:space-x-4 w-full">
        @if ($post)
            <div class="rounded-sm shadow-lg bg-white px-2 py-4 md:p-8 mb-12 text-black flex-1">
                <header class="mb-4 flex flex-col md:flex-row md:items-center mx-4 md:mx-0">
                    <h1 class="text-4xl font-bold mb-2 md:mb-0 flex-1">{{ $post->title }}</h1>

                    <p class="italic text-sm text-gray-900 ml-2 md:ml-0">
                        Publié le {{ $post->published_at->format('d/m/y à H:i') }}
                    </p>
                </header>

                <div class="my-8 mx-2 md:mx-8 content">
                    {!! $post->content !!}
                </div>
            </div>
        @else
            <div class="rounded-sm shadow-lg bg-white px-2 py-4 md:p-8 mb-12 text-black"></div>
        @endif
    </div>
@endsection
