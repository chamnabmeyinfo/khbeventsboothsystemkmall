<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $landingPage->headline ?: $landingPage->name }}</title>
    @if(!empty($landingPage->css_content))
        <style>{!! $landingPage->css_content !!}</style>
    @endif
</head>
<body>
{!! $landingPage->html_content !!}

<form id="landingContinueForm" method="POST" action="{{ route('landing-pages.public.continue', $landingPage) }}" style="display:none;">
    @csrf
    <input type="hidden" name="target" value="{{ $landingPage->redirect_url }}">
</form>

<script>
window.LandingPageConfig = {
    trackingUrl: @json(route('landing-pages.track', $landingPage)),
    leadUrl: @json(route('landing-pages.lead', $landingPage)),
    continueUrl: @json(route('landing-pages.public.continue', $landingPage)),
    csrfToken: @json(csrf_token())
};

window.trackLandingEvent = function(eventType, payload) {
    payload = payload || {};
    const body = Object.assign({ event_type: eventType }, payload);
    return fetch(window.LandingPageConfig.trackingUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': window.LandingPageConfig.csrfToken
        },
        body: JSON.stringify(body)
    }).catch(function() { return null; });
};

window.submitLandingLead = function(payload) {
    return fetch(window.LandingPageConfig.leadUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': window.LandingPageConfig.csrfToken
        },
        body: JSON.stringify(payload || {})
    }).catch(function() { return null; });
};

window.landingContinue = function(targetUrl) {
    var form = document.getElementById('landingContinueForm');
    if (!form) return;
    if (targetUrl) {
        form.querySelector('input[name="target"]').value = targetUrl;
    }
    form.submit();
};
</script>

@if($landingPage->allow_inline_scripts && !empty($landingPage->js_content))
    <script>{!! $landingPage->js_content !!}</script>
@endif
</body>
</html>
