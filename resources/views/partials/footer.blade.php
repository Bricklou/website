<footer class="footer">
    <div class="container">
        <div class="socials">
            <a href="{{ env('GITHUB_LINK', '') }}">
                @include('components.icon', ['iconName' => 'github'])
                <span>Github</span>
            </a>

            <a href="{{ env('YOUTUBE_LINK', '') }}">
                @include('components.icon', [
                    'iconName' => 'youtube',
                ])
                <span>Youtube</span>
            </a>
        </div>

        <div class="credits">
            <p>Créé par Bricklou © 2022</p>
            <p>Tout le projet est sous licence GPLv3</p>
        </div>
    </div>
</footer>
