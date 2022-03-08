@component($typeForm, get_defined_vars())
    <div data-controller="ckeditor">
        <textarea name="{{ $attributes['name'] }}" class="ckeditor" id="{{ $attributes['id'] }}"
            style="min-height: {{ $attributes['height'] }}">
                {!! $value !!}
            </textarea>
    </div>
@endcomponent
