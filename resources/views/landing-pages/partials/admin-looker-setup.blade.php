{{-- Shared Looker / ios-dashboard-mode assets for landing-pages admin (AdminLTE). --}}
@push('body-class')
ios-dashboard-mode lp-landing-admin
@endpush
@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.6">
<link rel="stylesheet" href="{{ asset('css/landing-pages-admin.css') }}?v=1.5">
@endpush
