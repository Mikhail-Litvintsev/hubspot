<body class="page-body">
<div class="hubspot-spinner-container" style="display: none">
    <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate fa-spin fa-spin2"></span>
</div>
<div>
    <ul class="alert alert-danger hubspot-print-error-msg" style="display: none"></ul>
</div>
@isset($message)
    <div style="color: darkred">
        {{ $message }}
    </div>
@endisset
@isset($ticket_id)
    <input class="hubspot-ticket-id" type="hidden" value="{{ $ticket_id }}">
@endisset
@isset($block_id)
    @isset($is_linked)
        <input class="hubspot-is-linked-{{ $block_id }}" type="hidden" value="{{ $is_linked }}">
    @endisset
@endisset
@yield('body')
</body>
@include('user.custom_blocks.hubspot.route_script')
@yield('scripts')

