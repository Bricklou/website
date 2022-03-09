@extends('layouts.base')

@section('content')
    <h1 class="text-4xl font-bold mb-8">Mes posts</h1>

    <div class="flex flex-col gap-y-4 px-2 md:mx-4 w-full">
        @forelse ($posts as $post)
            @include('components.postCard', ['post' => $post])
        @empty
            <p class="text-center">Aucun post</p>
        @endforelse
    </div>

    {{ $posts->links() }}
@endsection
