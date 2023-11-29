<input class="hubspot-pipelines" type="hidden" value="{{ json_encode($pipelines) }}">

@isset($hs_contact_id)
    <div data-id="{{ $hs_contact_id }}" class="hubspot-contact-frame">< @lang('text.hubspot.back')</div>
@else
    <div class="hubspot-index">< @lang('text.hubspot.back')</div>
@endisset

<form class="hubspot-contact-deal-create-form">
    <input name="hs_contact_id" type="hidden" value="{{ $hs_contact_id }}">
    <div class="form-group" style="color: black">
        <p class="create-deal-title">@lang('text.hubspot.create_new_deal')</p>
        <div>
            <label class="create-deal-label" for="dealname">@lang('text.hubspot.dealname')
                *</label>
            <input class="create-deal-input" id="dealname" name="dealname">
        </div>
        <div>
            <label class="create-deal-label"
                   for="hubspot-contact-deal-create-pipeline">@lang('text.hubspot.pipeline')
                *</label>
            <select name="pipeline" id="hubspot-contact-deal-create-pipeline"
                    class="hubspot-contact-deal-create-pipeline create-deal-input">

                <option value="{{ null }}" @if(count($pipelines) > 1) selected="selected"
                        @endif></option>
                @foreach($pipelines as $pipeline)
                    <option value="{{ $pipeline['pipelineId'] }}" @if(count($pipelines) === 1) selected="selected"
                            @endif>{{ $pipeline['label'] }} </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="create-deal-label"
                   for="hubspot-contact-deal-create-dealstage">@lang('text.hubspot.dealstage')
                *</label>
            <select name="dealstage" id="hubspot-contact-deal-create-dealstage"
                    class="hubspot-contact-deal-create-dealstage create-deal-input" @if(count($pipelines) > 1) disabled @endif>
                @if(count($pipelines) === 1)
                    <option value="{{ null }}"></option>
                    @foreach(array_values($pipelines)[0]['stages'] as $stage)
                        <option value="{{ $stage['stageId'] }}">{{ $stage['label'] }}</option>
                    @endforeach
                @else
                    <option value="{{ null }}">@lang('text.hubspot.require_pipeline')</option>
                @endif>
            </select>
        </div>
        <div>
            <label class="create-deal-label" for="hubspot-contact-deal-create-amount">@lang('text.hubspot.amount')</label>
            <input class="create-deal-input" id="hubspot-contact-deal-create-amount" name="amount" value="{{ $deal['amount'] ?? '' }}">
        </div>
        <div>
            <label class="create-deal-label" for="hubspot-contact-deal-create-closedate">@lang('text.hubspot.closedate')</label>
            <input class="create-deal-input" type="datetime-local" id="hubspot-contact-deal-create-closedate" name="closedate"
                   value="{{ $deal['closedate'] ?? ''  }}">
        </div>
        <div>
            <label class="create-deal-label" for="hubspot-contact-deal-create-owner">@lang('text.hubspot.owner')</label>
            <select name="hubspot_owner_id" id="hubspot-contact-deal-create-owner" class="create-deal-input">
                <option value="{{ null }}"></option>
                @foreach($owners as $owner_id => $owner)
                    <option value="{{ $owner_id }}">{{ $owner['firstName'] }} {{ $owner['lastName'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="create-deal-label" for="hubspot-contact-deal-create-dealtype">@lang('text.hubspot.dealtype')</label>
            <select name="dealtype" id="hubspot-contact-deal-create-dealtype" class="create-deal-input">
                <option value="{{ null }}"></option>
                @foreach($deal_types as $type)
                    <option value="{{ $type }}">@lang("text.hubspot.$type")</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="create-deal-label" for="hubspot-contact-deal-create-priority">@lang('text.hubspot.hs_priority')</label>
            <select name="hs_priority" id="hubspot-contact-deal-create-priority" class="create-deal-input">
                <option value="{{ null }}"></option>
                @foreach($priorities as $priority)
                    <option value="{{ $priority }}">@lang("text.hubspot.$priority")</option>
                @endforeach
            </select>
        </div>
    </div>
</form>
<button class="hubspot-contact-deal-store-button">@lang('text.hubspot.create')</button>
<script src="/assets/app/user/js/dynamic_blocks/hubspot/index.js"></script>