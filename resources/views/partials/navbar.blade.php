<header class="navbar-container">
    <div class="navbar">
        <a href="{{ route('home') }}" class="logo">
            <img src="/images/avatar_x2048.png" alt="Avatar">
            <span>
                {{ env('APP_NAME') }}
            </span>
        </a>
        <nav class="menu">

            <a href="{{ route('home') }}" @class([
                'link',
                'active' => Route::currentRouteName() == 'home',
            ])>
                Accueil
            </a>
            <a href="{{ route('posts.index') }}" @class([
                'link',
                'active' => str_starts_with(Route::currentRouteName(), 'posts.'),
            ])>
                Posts
            </a>

        </nav>
    </div>
</header>
