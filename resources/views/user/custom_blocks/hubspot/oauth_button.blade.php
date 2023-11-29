@extends('user.custom_blocks.hubspot.layout')
@section('body')
    <div class="flex-container-center">
        <button class="oauth-btn" onclick="window.open('{{ $oauth_url }}', 'amo_auth', 'width=600,height=700')">
            @lang('text.hubspot.login_to_hubspot')
        </button>
    </div>
@endsection