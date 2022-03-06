<ul>
    @foreach ($socialLinks as $link)
        <li class="list-none">
            <a href="{{ $link->social_link }}" target="_blank" class="link-primary">
                {{ $link->social_network }}
            </a>
        </li>
    @endforeach
</ul>
