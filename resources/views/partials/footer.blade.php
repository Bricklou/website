<footer class="footer">
    <div class="container">
        <div class="socials">
            @foreach ($owner_socials as $social)
                <a href="{{ $social['link'] }}" target="_blank">
                    @include('components.icon', ['iconName' => $social['network']])
                    <span>{{ $social['name'] }}</span>
                </a>
            @endforeach
        </div>

        <div class="credits">
            <p>Créé par Bricklou © 2022</p>
            <p>Tout le projet est sous licence GPLv3</p>
        </div>
    </div>
</footer>
