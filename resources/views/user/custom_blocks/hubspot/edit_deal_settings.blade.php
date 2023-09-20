<div class="hubspot-index">< {{ trans('text.hubspot.back') }}</div>
<div class="flex-container">
    <p class="show-deal-titles">{{ trans('text.hubspot.deal_display_settings') }}</p>
</div>
<form class="hubspot-deal-settings">
    <div class="form-group">

    </div>
        @foreach($all_settings as $value)
            <div>
                <input type="checkbox" id="hubspot-deal-settings-{{ $value }}" name="{{ $value }}" @if(in_array($value, $deal_settings)) checked @endif />
                <label for="hubspot-deal-settings-{{ $value }}">{{ $value }}</label>
            </div>
        @endforeach
    </div>

</form>
<button data-id="{{ $hs_contact_id }}" class="hubspot-deal-settings-update-button">{{ trans('text.hubspot.save') }}</button>
<script src="/assets/app/user/js/dynamic_blocks/hubspot/index.js"></script>
