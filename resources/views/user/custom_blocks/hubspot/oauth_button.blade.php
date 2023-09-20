@extends('user.custom_blocks.hubspot.layout')
@section('body')
<div class="flex-container-center">
    <button onclick="window.open('{{ $oauth_url }}', 'amo_auth', 'width=600,height=700')">
        {{ trans('text.hubspot.login_to_hubspot') }}
    </button>
</div>
@endsection