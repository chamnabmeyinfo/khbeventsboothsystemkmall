@extends('layouts.adminlte')

@include('landing-pages.partials.admin-looker-setup')

@php
    $isEdit = ($mode ?? '') === 'edit' && $lead;
    $defaultPageId = isset($lead) ? $lead->landing_page_id : ($prefillLandingPageId ?? '');
@endphp

@section('title', $isEdit ? 'Edit lead' : 'Add lead')
@section('page-title', $isEdit ? 'Edit marketing lead' : 'Add marketing lead')
@section('breadcrumb', 'Landing Pages / Reporting / '.($isEdit ? 'Edit' : 'Create'))

@section('content')
<div class="looker-dashboard">
    <header class="looker-header">
        <div class="looker-header-title">
            <h1>{{ $isEdit ? 'Edit lead' : 'New lead' }}</h1>
            <p>Manual entry or corrections for marketing submissions.</p>
        </div>
        <div class="looker-actions flex-wrap">
            <a href="{{ route('landing-pages.reporting.index') }}" class="action-btn action-btn-secondary">Back to list</a>
            @if($isEdit)
                <a href="{{ route('landing-pages.reporting.show', $lead) }}" class="action-btn action-btn-primary">View</a>
            @endif
        </div>
    </header>

    <div class="canvas-panel">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-user-edit" aria-hidden="true"></i> Form</h2>
        </div>
        <form method="post" action="{{ $isEdit ? route('landing-pages.reporting.update', $lead) : route('landing-pages.reporting.store') }}">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="form-group">
                <label for="lp_lead_page">Landing page <span class="text-danger">*</span></label>
                <select name="landing_page_id" id="lp_lead_page" class="form-control @error('landing_page_id') is-invalid @enderror" required>
                    <option value="">— Select —</option>
                    @foreach($landingPageOptions as $opt)
                        <option value="{{ $opt->id }}" {{ (string) old('landing_page_id', $defaultPageId) === (string) $opt->id ? 'selected' : '' }}>
                            {{ $opt->name }} ({{ $opt->slug }})
                        </option>
                    @endforeach
                </select>
                @error('landing_page_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="lp_lead_name">Name</label>
                        <input type="text" name="name" id="lp_lead_name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $lead->name ?? '') }}" maxlength="255" autocomplete="name">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="lp_lead_email">Email</label>
                        <input type="email" name="email" id="lp_lead_email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $lead->email ?? '') }}" maxlength="255" autocomplete="email">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="lp_lead_phone">Phone</label>
                        <input type="text" name="phone" id="lp_lead_phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $lead->phone ?? '') }}" maxlength="255" autocomplete="tel">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="lp_lead_trip">Trip / preferred date</label>
                        <input type="text" name="preferred_trip_date" id="lp_lead_trip" class="form-control @error('preferred_trip_date') is-invalid @enderror" value="{{ old('preferred_trip_date', $lead->preferred_trip_date ?? '') }}" maxlength="500" placeholder="e.g. Phase label or date">
                        @error('preferred_trip_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="lp_lead_locale">Locale</label>
                        <input type="text" name="locale" id="lp_lead_locale" class="form-control @error('locale') is-invalid @enderror" value="{{ old('locale', $lead->locale ?? '') }}" maxlength="32" placeholder="en, km, zh">
                        @error('locale')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="lp_lead_source">Source</label>
                        <input type="text" name="source" id="lp_lead_source" class="form-control @error('source') is-invalid @enderror" value="{{ old('source', $lead->source ?? '') }}" maxlength="255" placeholder="e.g. form, modal">
                        @error('source')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="lp_lead_meta">Meta (JSON)</label>
                <textarea name="meta" id="lp_lead_meta" class="form-control font-monospace small @error('meta') is-invalid @enderror" rows="6" placeholder='{"key":"value"}'>{{ old('meta', isset($lead) && $lead->meta !== null ? (string) (json_encode($lead->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '') : '') }}</textarea>
                @error('meta')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                <p class="text-muted small mb-0">Optional. Leave empty or provide valid JSON. Used for extra fields from the public form.</p>
            </div>

            <div class="lp-form-footer-actions pt-2">
                <button type="submit" class="action-btn action-btn-primary">
                    {{ $isEdit ? 'Save changes' : 'Create lead' }}
                </button>
                <a href="{{ route('landing-pages.reporting.index') }}" class="action-btn action-btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
