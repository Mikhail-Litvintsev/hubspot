<input class="hubspot-pipelines" type="hidden" value="{{ json_encode($pipelines) }}">

@isset($hs_contact_id)
    <div data-id="{{ $hs_contact_id }}" class="hubspot-contact-frame">< {{ trans('text.hubspot.back') }}</div>
@else
    <div class="hubspot-index">< {{ trans('text.hubspot.back') }}</div>
@endisset

<form class="hubspot-contact-deal-create-form">
    <input name="hs_contact_id" type="hidden" value="{{ $hs_contact_id }}">
    <div class="form-group" style="color: black">
        <p class="create-deal-title">{{ trans('text.hubspot.create_new_deal') }}</p>
        <div>
            <label class="create-deal-label" for="dealname">{{ trans('text.hubspot.dealname') ?? "Deal name" }}</label>
            <input class="create-deal-input" id="dealname" name="dealname">
        </div>
        <div>
            <label class="create-deal-label" for="hubspot-contact-deal-create-pipeline">{{ trans('text.hubspot.pipeline') ?? 'pipeline' }}</label>
            <select name="pipeline" id="hubspot-contact-deal-create-pipeline"
                    class="hubspot-contact-deal-create-pipeline create-deal-input">
                <option value="{{ null }}" selected="selected"></option>
                @foreach($pipelines as $pipeline)
                    <option value="{{ $pipeline['pipelineId'] }}">{{ $pipeline['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="create-deal-label" for="hubspot-contact-deal-create-dealstage">{{ trans('text.hubspot.dealstage') ?? 'dealstage' }}</label>
            <select name="dealstage" id="hubspot-contact-deal-create-dealstage"
                    class="hubspot-contact-deal-create-dealstage create-deal-input" disabled>
                <option value="{{ null }}">{{ trans('text.hubspot.require_pipeline') }}</option>
            </select>
        </div>
        <div>
            <label class="create-deal-label" for="hubspot-contact-deal-create-amount">{{ trans('text.hubspot.amount') ?? 'amount' }}</label>
            <input class="create-deal-input" id="hubspot-contact-deal-create-amount" name="amount" value="{{ $deal['amount'] ?? '' }}">
        </div>
        <div>
            <label class="create-deal-label" for="hubspot-contact-deal-create-closedate">{{ trans('text.hubspot.closedate') ?? 'closedate' }}</label>
            <input class="create-deal-input" type="datetime-local" id="hubspot-contact-deal-create-closedate" name="closedate"
                   value="{{ $deal['closedate'] ?? ''  }}">
        </div>
        <div>
            <label class="create-deal-label" for="hubspot-contact-deal-create-owner">{{ trans('text.hubspot.owner') ?? 'owner' }}</label>
            <select name="owner" id="hubspot-contact-deal-create-owner" class="create-deal-input">
                <option value="{{ null }}"></option>
                @foreach($owners as $owner_id => $owner)
                    <option value="{{ $owner_id }}">{{ $owner['firstName'] }} {{ $owner['lastName'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="create-deal-label" for="hubspot-contact-deal-create-dealtype">{{ trans('text.hubspot.dealtype') ?? 'dealtype' }}</label>
            <select name="dealtype" id="hubspot-contact-deal-create-dealtype" class="create-deal-input">
                <option value="{{ null }}"></option>
                @foreach($deal_types as $type)
                    <option value="{{ $type }}">{{ trans("text.hubspot.$type") ?? $type }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="create-deal-label" for="hubspot-contact-deal-create-priority">{{ trans('text.hubspot.hs_priority') ?? 'hs_priority' }}</label>
            <select name="hs_priority" id="hubspot-contact-deal-create-priority" class="create-deal-input">
                <option value="{{ null }}"></option>
                @foreach($priorities as $priority)
                    <option value="{{ $priority }}">{{ trans("text.hubspot.$priority") ?? $priority }}</option>
                @endforeach
            </select>
        </div>
    </div>
</form>
<button class="hubspot-contact-deal-store-button">{{ trans('text.hubspot.create') ?? 'Create' }}</button>
<script src="/assets/app/user/js/dynamic_blocks/hubspot/index.js"></script>