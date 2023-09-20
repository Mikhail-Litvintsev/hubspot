@if(empty($contacts))
    <span class="contacts-not-found">{{ trans('text.hubspot.no_contacts_found') }}</span>
@else()
    <p class="deals-lable">{{ trans('text.hubspot.select_contact') }}</p>
    @foreach($contacts as $contact)
        <div data-id="{{ $contact['hs_object_id'] }}" class="hubspot-contact-frame mb-2">
            <p class="deals-lable">{{ $contact['firstname'] }} {{ $contact['lastname'] }}</p>
            @foreach($contact as $key => $property)
                @if($key !== 'hs_object_id' && $property && $key !== 'lastname' && $key !== 'firstname')
                    <div>
                        {{ trans("text.hubspot.$key") ?? $key }}: {{ $property }}
                    </div>
                @endif
            @endforeach
        </div>
    @endforeach
@endif
<button class="hubspot-create-new-contact">{{ trans('text.hubspot.create') }}</button>
<script src="/assets/app/user/js/dynamic_blocks/hubspot/index.js"></script>