<div class="hubspot-index">< {{ trans('text.hubspot.back') }}</div>
<form class="hubspot-contact-create-form">
    <div class="form-group">
        <p class="title-create-contact">{{ trans('text.hubspot.create_new_client') }}</p>
        @foreach($contact as $key => $value)
            <div>
                @if($key === 'hs_object_id')
                    <input id="hubspot-contact-create-{{ $key }}" name="{{ $key }}" value="{{ $value }}"
                           type="hidden" style="color: black">
                @else
                    <label class="create-contact-lable" for="hubspot-contact-create-{{ $key }}">{{ trans("text.hubspot.$key") }}</label>
                    <input class="create-contact-input" id="hubspot-contact-create-{{ $key }}" name="{{ $key }}" value="{{ $value }}">
                @endif
            </div>
        @endforeach

    </div>
</form>
<button class="hubspot-contact-create-button">{{ trans('text.hubspot.create') }}</button>
<script src="/assets/app/user/js/dynamic_blocks/hubspot/index.js"></script>