@if(empty($contacts))
    <div class="contacts-not-found">@lang('text.hubspot.no_contacts_found')</div>
    <button class="hubspot-create-new-contact right">@lang('text.hubspot.create')</button>
@else()
    <p class="deals-lable-title">@lang('text.hubspot.select_contact')</p>
    <hr class="hubspot-line">
    @foreach($contacts as $contact)
        <span data-id="{{ $contact['hs_object_id'] }}"
              class="hubspot-contact-frame mb-2 deals-lable">{{ $contact['firstname'] }} {{ $contact['lastname'] }}</span>
        <button data-id="{{ $contact['hs_object_id'] }}" class="hubspot-contact-link"
        >Link
        </button>
        @foreach($contact as $key => $property)
            @if($key !== 'hs_object_id' && $property && $key !== 'lastname' && $key !== 'firstname')
                <div data-id="{{ $contact['hs_object_id'] }}" class="hubspot-contact-frame mb-2">
                    @lang("text.hubspot.$key"): {{ $property }}
                </div>
            @endif
        @endforeach
        <hr class="contact-line">
    @endforeach
    <button class="hubspot-create-new-contact margin-contact">@lang('text.hubspot.create')</button>
@endif
<script src="/assets/app/user/js/dynamic_blocks/hubspot/index.js"></script>