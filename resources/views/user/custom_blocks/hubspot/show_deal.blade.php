<div data-id="{{ $hs_contact_id }}" id="{{ json_encode(['last_page_number' => $last_page_number ?? 1]) }}" class="hubspot-contact-frame without-styles">< @lang('text.hubspot.back')</div>
<div class="flex-container">
    <p class="show-deal-titles">{{ $deal['dealname'] }}</p>
    <div data-id="{{ $hs_contact_id }}" class="hubspot-deal-settings-edit"><img src="/images/icons/extensions/gear.png" alt="settings"/></div>
</div>
<div>
    @foreach($deal as $key => $value)
        @if($key !== 'dealname' && $key !== 'owner' && $key !== 'hubspot_owner_id' && $key !== 'hs_object_id')
            @if(in_array($key, ['hs_lastmodifieddate', 'closedate', 'createdate']))
                @php
                    if (is_null($value)) {
                        $value = "";
                    } else {
                        $value = date('d.m.Y, H:i', strtotime($value));
                    }
                @endphp
                <div class="deal-info-table"><span class="show-deal-span" >@lang("text.hubspot.$key")</span> {{ $value }}</div>
            @else
                <div class="deal-info-table"><span
                            class="show-deal-span">@lang("text.hubspot.$key"): </span>
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
    @if (!empty($deal['owner']))
        <div class="deal-info-table"><span class="show-deal-span" >@lang("text.hubspot.owner")</span>
            {{ $deal['owner']['firstName'] ?? '' }} {{ $deal['owner']['lastName'] ?? '' }}</div>
    @endif
</div>
<div class="flex-container comments">
    <p class="show-deal-comments">{{ trans('text.hubspot.notes') }}</p>
    <select id="{{ json_encode(['hs_deal_id' => $deal['hs_object_id'], 'hs_contact_id' => $hs_contact_id]) }}" class="sort-select">
        @if ($sort == 'DESC')
            <option value="DESC" selected>{{ trans("text.hubspot.last") }}</option>
            <option value="ASC">{{ trans("text.hubspot.first") }}</option>
        @else
            <option value="DESC">{{ trans("text.hubspot.last") }}</option>
            <option value="ASC" selected>{{ trans("text.hubspot.first") }}</option>
        @endif
    </select>
</div>
@if ($comments->items())
    <hr class="comments-first-line" />
@endif
@foreach($comments->items() as $comment)
    <div>
        @isset($owners[$comment['hubspot_owner_id']])
            <span class="comment-name">{{ $owners[$comment['hubspot_owner_id']]['firstName'] }} {{ $owners[$comment['hubspot_owner_id']]['lastName'] }}</span>
        @endisset
        <span class="comment-text">{{ date('d.m.Y, H:i', strtotime($comment['hs_createdate']))}}</span>
    </div>
    <span class="comment-text">{{ htmlspecialchars_decode(strip_tags($comment['hs_note_body'])) }}</span>
    <hr />
@endforeach
<div id="{{ json_encode(['hs_deal_id' => $deal['hs_object_id'], 'hs_contact_id' => $hs_contact_id]) }}" class="comments-pagination">{{ $comments->onEachSide(0)->render('user.custom_blocks.hubspot.pagination') }}</div>
<script src="/assets/app/user/js/dynamic_blocks/hubspot/index.js"></script>
