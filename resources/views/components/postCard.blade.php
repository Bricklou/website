<div class="w-full rounded-sm p-4 shadow-lg bg-white">
    <a href="{{ route('posts.show', ['id' => $post->slug]) }}">
        <h2 class="text-xl font-semibold text-black">
            {{ $post->title }}
        </h2>
        <p class="text-gray-900 text-sm ml-2 font-light">Publié le {{ $post->published_at }}</p>

        <div class="text-gray-400">
            {{ $post->body }}
        </div>
    </a>
</div>
