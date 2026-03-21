@extends('layouts.client-profile-react')

@section('title', __('Client profile demo'))

{{-- No JSON payload: main.jsx falls back to built-in demo data --}}

@section('after-root')
    {{-- Demo only: no upload modals / delete form --}}
@endsection
