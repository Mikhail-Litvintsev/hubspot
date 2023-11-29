@extends('user.custom_blocks.hubspot.layout')

@section('body')
    <div>
        <div id="hubspot-body-{{ $block_id ?? 'non' }}" class="hubspot-body">
            @if($is_linked)
                @include('user.custom_blocks.hubspot.deals',  compact('deals',
                        'is_linked',
                        'hs_contact_id',
                        'contact'))
            @else
                @include('user.custom_blocks.hubspot.contacts',
                    compact('contacts'))
            @endif
        </div>
    </div>
@endsection
@section('scripts')
    <link rel="stylesheet" href="/assets/dist/user/css/hubspot/index/main.min.css" />
    <script src="/assets/app/user/js/dynamic_blocks/hubspot/index.js"></script>
@endsection
