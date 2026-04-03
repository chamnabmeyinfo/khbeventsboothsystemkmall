@extends('layouts.adminlte')

@section('title', 'Create Landing Page')
@section('page-title', 'Create Landing Page')
@section('breadcrumb', 'Landing Pages / Create')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-rocket mr-2"></i>Create Landing Page</h3>
        </div>
        <form id="lpLandingPageAdminForm" action="{{ route('landing-pages.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            @include('landing-pages.partials.form', ['landingPage' => null])
            <div class="card-footer lp-sticky-card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Create</button>
                <a href="{{ route('landing-pages.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
