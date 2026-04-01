@extends('layouts.adminlte')

@section('title', 'Edit Landing Page')
@section('page-title', 'Edit Landing Page')
@section('breadcrumb', 'Landing Pages / Edit')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-edit mr-2"></i>Edit Landing Page: {{ $landingPage->name }}</h3>
        </div>
        <form action="{{ route('landing-pages.update', $landingPage) }}" method="POST">
            @csrf
            @method('PUT')
            @include('landing-pages.partials.form', ['landingPage' => $landingPage])
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Update</button>
                <a href="{{ route('landing-pages.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
