<div style="color: darkred">
    <h2>@lang('text.hubspot.error')</h2>
    @foreach($errors as $key => $message)
        <div>
            @if(is_numeric($key) === false)
                {{ $key }}:
            @endif
            {{ $message }}
        </div>
    @endforeach
</div>