@extends('layouts.client-profile-react')

@section('title', __('Client') . ' #' . $client->id)

@section('before-root')
    @if(! empty($clientProfilePayload))
        <script type="application/json" id="client-profile-payload">@json($clientProfilePayload)</script>
    @endif
@endsection

@section('after-root')
    @include('clients.partials.profile-upload-modals')
@endsection

@push('scripts')
    @include('clients.partials.profile-react-scripts')
@endpush
