<div data-id="{{ $hs_contact_id }}" class="hubspot-contact-frame without-styles">< {{ trans('text.hubspot.back') }}</div>
<div class="flex-container">
    <p class="show-deal-titles">{{ $deal['dealname'] }}</p>
    <div data-id="{{ $hs_contact_id }}" class="hubspot-deal-settings-edit"><img src="/images/icons/extensions/gear.png" alt="settings"/></div>
</div>
<div>
    @foreach($deal as $key => $value)
        @if($key !== 'dealname' && $key !== 'owner' && $key !== 'hs_object_id')
            @if(in_array($key, ['hs_lastmodifieddate', 'closedate', 'createdate']))
                <div  class="deal-info-table"><span class="show-deal-span" >{{ trans("text.hubspot.$key") ?? $key }}</span> <input
                            style="border: none; outline: none; background: none;" type="datetime-local" name=""
                            value="{{ $value }}" disabled></div>
            @else
                <div class="deal-info-table"><span class="show-deal-span" >{{ trans("text.hubspot.$key") ?? $key }}</span> {{ $value }}</div>
            @endif
        @endif
    @endforeach
    <div class="deal-info-table"><span class="show-deal-span" >{{ trans("text.hubspot.owner") ?? 'Owner' }}</span>
         {{ $deal['owner']['firstName'] ?? '' }} {{ $deal['owner']['lastName'] ?? '' }}</div>
</div>
<div class="flex-container comments">
    <p class="show-deal-titles">{{ trans('text.hubspot.notes') }}</p>
    <select class="sort-select">
        <option>{{ trans("text.hubspot.last") }}</option>
        <option>{{ trans("text.hubspot.first") }}</option>
    </select>
</div>
@foreach($comments as $comment)
    <div>
        @isset($owners[$comment['hubspot_owner_id']])
            {{ $owners[$comment['hubspot_owner_id']]['firstName'] }} {{ $owners[$comment['hubspot_owner_id']]['lastName'] }}
        @endisset
            {{ $comment['hs_createdate'] }}
    </div>
    {!! html_entity_decode($comment['hs_note_body']) !!}
@endforeach
<script src="/assets/app/user/js/dynamic_blocks/hubspot/index.js"></script>
