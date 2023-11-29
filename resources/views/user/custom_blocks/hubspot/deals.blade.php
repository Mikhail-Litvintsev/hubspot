@if($is_linked === false)
    <div data-id="{{ $hs_contact_id }}" class="hubspot-index">< @lang('text.hubspot.back')</div>
@endif
<div class="flex-container title-margin">
    <p class="contact-name">{{ $contact['firstname'] }} {{ $contact['lastname'] }}</p>
    <div data-id="{{ $hs_contact_id }}" class="hubspot-deal-settings-edit"><img src="/images/icons/extensions/gear.png" class="icon-margin" alt="settings"/></div>
</div>
@foreach($deals->items() as $deal)
    <div id="{{ json_encode(['hs_deal_id' => $deal['hs_object_id'], 'hs_contact_id' => $contact['hs_object_id'], 'last_page_number' => $deals->toArray()['current_page']]) }}" class="hubspot-deal-short">
        <div class="deal-header">{{ $deal['dealname'] ?? trans('text.hubspot.undefined_name') }}</div>
        @foreach($deal as $key => $value)
            @if($key !== 'dealname' && $key !== 'owner' && $key !== 'hubspot_owner_id' && $key !== 'hs_object_id')
                @if(in_array($key, ['hs_lastmodifieddate', 'closedate', 'createdate']))
                    <div>
                        @php
                            if (is_null($value)) {
                                $value = "";
                            } else {
                                $value = date('d.m.Y, H:i', strtotime($value));
                            }
                        @endphp
                        @lang("text.hubspot.$key"): {{ $value }}
                    </div>
                @else
                    <div>
                        @lang("text.hubspot.$key"):
                        @if(($key === 'dealtype' || $key === 'hs_priority') && $value)
                            @lang("text.hubspot.$value")
                                @elseif($key === 'pipeline' && !empty($pipelines[$value]))
                                    {{ $pipelines[$value]['label'] }}
                                @else
                                    {{ $value }}
                                @endif
                    </div>
                @endif
            @endif
        @endforeach
        @isset($deal['owner'])
            <div>@lang("text.hubspot.owner")
                : {{ ($deal['owner']) ? $deal['owner']['firstName'] ?? '' : '' }} {{ ($deal['owner']) ? $deal['owner']['lastName'] ?? '' : '' }}</div>
        @endisset
    </div>
@endforeach
<div class="deels-pagination" data-id="{{ $contact['hs_object_id'] }}">{{ $deals->onEachSide(0)->render('user.custom_blocks.hubspot.pagination') }}</div>
<button data-id="{{ $contact['hs_object_id'] }}" class="hubspot-contact-deal-create-button">{{ trans('text.hubspot.create') }}</button>
<script src="/assets/app/user/js/dynamic_blocks/hubspot/index.js"></script>