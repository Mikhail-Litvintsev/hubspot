<div class="hubspot-index">< @lang('text.hubspot.back')</div>
<form class="hubspot-contact-create-form">
    <div class="form-group">
        <p class="title-create-contact">@lang('text.hubspot.create_new_client')</p>
        @foreach($contact as $key => $value)
            <div>
                @if($key === 'hs_object_id')
                    <input id="hubspot-contact-create-{{ $key }}" name="{{ $key }}" value="{{ $value }}"
                           type="hidden" style="color: black">
                @elseif($key === 'firstname')
                    <label class="create-contact-lable"
                           for="hubspot-contact-create-firstname">@lang("text.hubspot.firstname")</label>
                    <input class="create-contact-input" id="hubspot-contact-create-firstname" name="firstname"
                           @empty($value) value="{{ $contact['email'] ?? '' }}" @endempty>
                @else
                    <label class="create-contact-lable"
                           for="hubspot-contact-create-{{ $key }}">@lang("text.hubspot.$key") @if($key === 'email')
                            *
                        @endif</label>
                    <input class="create-contact-input" id="hubspot-contact-create-{{ $key }}" name="{{ $key }}"
                           value="{{ $value }}">
                @endif
            </div>
        @endforeach

    </div>
</form>
<button class="hubspot-contact-create-button">@lang('text.hubspot.create')</button>
<script src="/assets/app/user/js/dynamic_blocks/hubspot/index.js"></script>