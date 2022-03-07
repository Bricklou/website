@if ($iconName != 'discord')
    <svg class="feather icon">
        <use href=" {{ asset('images/feather-sprite.svg') }}#{{ $iconName }}" />
    </svg>
@else
    <svg class="icon">
        <use href=" {{ asset('images/discord.svg') }}#discord" />
    </svg>
@endif
