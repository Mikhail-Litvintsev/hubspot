@if($is_linked === false)
    <div data-id="{{ $hs_contact_id }}" class="hubspot-index">< {{ trans('text.hubspot.back') }}</div>
@endif
<div class="flex-container">
    <p class="contact-name">{{ $contact['firstname'] }} {{ $contact['lastname'] }}</p>
    <div data-id="{{ $hs_contact_id }}" class="hubspot-deal-settings-edit"><img src="/images/icons/extensions/gear.png" alt="settings"/></div>
</div>
@foreach($deals as $deal)
    <div id="{{ json_encode(['hs_deal_id' => $deal['hs_object_id'], 'hs_contact_id' => $contact['hs_object_id']]) }}" class="hubspot-deal-short">
        <div class="deal-header">{{ $deal['dealname'] ?? trans('text.hubspot.undefined_name') }}</div>
        @foreach($deal as $key => $value)
            @if($key !== 'dealname' && $key !== 'owner' && $key !== 'hs_object_id')
                @if(in_array($key, ['hs_lastmodifieddate', 'closedate', 'createdate']))
                    <div class="hubspot-cursor" >
                        <label for="hubspot-deal-field-{{ $key }}">{{ trans("text.hubspot.$key") ?? $key }}: </label>
                        <input id="hubspot-deal-field-{{ $key }}"
                                style="border: none; outline: none; background: none;" type="datetime-local" name=""
                                value="{{ $value }}" disabled></div>
                @else
                    <div>
                        {{ trans("text.hubspot.$key") ?? $key }}: {{ $value }}
                    </div>
                @endif
            @endif
        @endforeach
        @isset($deal['owner'])
            <div>{{ trans("text.hubspot.owner") ?? 'Owner' }}
                : {{ ($deal['owner']) ? $deal['owner']['firstName'] ?? '' : '' }} {{ ($deal['owner']) ? $deal['owner']['lastName'] ?? '' : '' }}</div>
        @endisset
    </div>
@endforeach
<button data-id="{{ $contact['hs_object_id'] }}" class="hubspot-contact-deal-create-button">{{ trans('text.hubspot.create') }}</button>
<script src="/assets/app/user/js/dynamic_blocks/hubspot/index.js"></script>