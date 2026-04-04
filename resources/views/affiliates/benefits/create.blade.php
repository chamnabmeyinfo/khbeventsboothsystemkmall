@extends('layouts.admin')

@section('title', 'Create Affiliate Benefit')
@section('page-title', 'Create Benefit')
@section('breadcrumb', 'Sales / Affiliates / Benefits / Create')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.7">
<link rel="stylesheet" href="{{ asset('css/affiliates-page.css') }}?v=1.1">
@endpush

@push('body-class', 'ios-dashboard-mode affiliates-page')

@section('content')
<div class="looker-dashboard">
    <header class="looker-header animate-slide-up delay-1">
        <div class="looker-header-title">
            <h1>Create benefit</h1>
            <p>Define how this rule calculates and who it applies to.</p>
        </div>
        <div class="looker-actions">
            <a href="{{ route('affiliates.benefits.index') }}" class="action-btn action-btn-secondary">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Cancel
            </a>
        </div>
    </header>

    <form action="{{ route('affiliates.benefits.store') }}" method="POST" id="benefitForm">
        @csrf

        <div class="canvas-panel mb-4 animate-slide-up delay-2">
            <div class="panel-header">
                <h2 class="panel-title"><i class="fas fa-info-circle" aria-hidden="true"></i> Basic information</h2>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="benefit_name">Benefit name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="benefit_name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required autocomplete="off">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="benefit_type">Type <span class="text-danger">*</span></label>
                    <select name="type" id="benefit_type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="commission" {{ old('type') == 'commission' ? 'selected' : '' }}>Commission</option>
                        <option value="bonus" {{ old('type') == 'bonus' ? 'selected' : '' }}>Bonus</option>
                        <option value="incentive" {{ old('type') == 'incentive' ? 'selected' : '' }}>Incentive</option>
                        <option value="reward" {{ old('type') == 'reward' ? 'selected' : '' }}>Reward</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="benefit_priority">Priority</label>
                    <input type="number" name="priority" id="benefit_priority" class="form-control"
                           value="{{ old('priority', 0) }}" min="0">
                    <span class="form-text">Higher runs first</span>
                </div>
                <div class="col-12">
                    <label class="form-label" for="benefit_description">Description</label>
                    <textarea name="description" id="benefit_description" class="form-control" rows="2">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <div class="canvas-panel mb-4 animate-slide-up delay-3">
            <div class="panel-header">
                <h2 class="panel-title"><i class="fas fa-calculator" aria-hidden="true"></i> Calculation method</h2>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="calculation_method">Method <span class="text-danger">*</span></label>
                    <select name="calculation_method" id="calculation_method" class="form-select @error('calculation_method') is-invalid @enderror" required>
                        <option value="percentage" {{ old('calculation_method') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                        <option value="fixed_amount" {{ old('calculation_method') == 'fixed_amount' ? 'selected' : '' }}>Fixed amount</option>
                        <option value="tiered_percentage" {{ old('calculation_method') == 'tiered_percentage' ? 'selected' : '' }}>Tiered percentage</option>
                        <option value="tiered_amount" {{ old('calculation_method') == 'tiered_amount' ? 'selected' : '' }}>Tiered amount</option>
                    </select>
                    @error('calculation_method')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div id="percentage_fields" class="row g-3 mt-1 d-none">
                <div class="col-md-4">
                    <label class="form-label" for="percentage">Percentage (%) <span class="text-danger">*</span></label>
                    <input type="number" name="percentage" id="percentage" class="form-control @error('percentage') is-invalid @enderror"
                           step="0.01" min="0" max="100" value="{{ old('percentage') }}">
                    @error('percentage')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div id="fixed_amount_fields" class="row g-3 mt-1 d-none">
                <div class="col-md-4">
                    <label class="form-label" for="fixed_amount">Fixed amount ($) <span class="text-danger">*</span></label>
                    <input type="number" name="fixed_amount" id="fixed_amount" class="form-control @error('fixed_amount') is-invalid @enderror"
                           step="0.01" min="0" value="{{ old('fixed_amount') }}">
                    @error('fixed_amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div id="tiered_fields" class="mt-3 d-none">
                <label class="form-label" for="tier_structure">Tier structure (JSON) <span class="text-danger">*</span></label>
                <textarea name="tier_structure" id="tier_structure" class="form-control" rows="6"
                          placeholder='[{"min": 0, "max": 10000, "percentage": 5}, {"min": 10000, "max": 50000, "percentage": 7}]'>{{ old('tier_structure') }}</textarea>
                <span class="form-text">Tiered percentage: use <code>percentage</code>. Tiered amount: use <code>amount</code>.</span>
                @error('tier_structure')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="canvas-panel mb-4 animate-slide-up delay-4">
            <div class="panel-header">
                <h2 class="panel-title"><i class="fas fa-bullseye" aria-hidden="true"></i> Targets <span class="fw-normal text-secondary fs-6">(optional)</span></h2>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="target_revenue">Target revenue ($)</label>
                    <input type="number" name="target_revenue" id="target_revenue" class="form-control" step="0.01" min="0" value="{{ old('target_revenue') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="target_bookings">Target bookings</label>
                    <input type="number" name="target_bookings" id="target_bookings" class="form-control" min="0" value="{{ old('target_bookings') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="target_clients">Target clients</label>
                    <input type="number" name="target_clients" id="target_clients" class="form-control" min="0" value="{{ old('target_clients') }}">
                </div>
            </div>
        </div>

        <div class="canvas-panel mb-4 animate-slide-up delay-5">
            <div class="panel-header">
                <h2 class="panel-title"><i class="fas fa-sliders-h" aria-hidden="true"></i> Conditions</h2>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="min_revenue">Minimum revenue ($)</label>
                    <input type="number" name="min_revenue" id="min_revenue" class="form-control" step="0.01" min="0" value="{{ old('min_revenue') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="max_benefit">Maximum benefit ($)</label>
                    <input type="number" name="max_benefit" id="max_benefit" class="form-control" step="0.01" min="0" value="{{ old('max_benefit') }}">
                </div>
            </div>
        </div>

        <div class="canvas-panel mb-4 animate-slide-up delay-5">
            <div class="panel-header">
                <h2 class="panel-title"><i class="fas fa-user-check" aria-hidden="true"></i> Applicability</h2>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="user_id">Specific user</label>
                    <select name="user_id" id="user_id" class="form-select">
                        <option value="">All users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->username }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="floor_plan_id">Specific floor plan</label>
                    <select name="floor_plan_id" id="floor_plan_id" class="form-select">
                        <option value="">All floor plans</option>
                        @foreach($floorPlans as $fp)
                            <option value="{{ $fp->id }}" {{ old('floor_plan_id') == $fp->id ? 'selected' : '' }}>
                                {{ $fp->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-md-4">
                    <label class="form-label" for="start_date">Start date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="end_date">End date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}">
                </div>
            </div>
        </div>

        <div class="canvas-panel mb-4 animate-slide-up delay-5">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-4">
                <button type="submit" class="action-btn action-btn-primary">
                    <i class="fas fa-save" aria-hidden="true"></i> Create benefit
                </button>
                <a href="{{ route('affiliates.benefits.index') }}" class="action-btn action-btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const calculationMethod = document.getElementById('calculation_method');
        const percentageFields = document.getElementById('percentage_fields');
        const fixedAmountFields = document.getElementById('fixed_amount_fields');
        const tieredFields = document.getElementById('tiered_fields');

        function toggleFields() {
            const method = calculationMethod.value;
            percentageFields.classList.add('d-none');
            fixedAmountFields.classList.add('d-none');
            tieredFields.classList.add('d-none');
            if (method === 'percentage') {
                percentageFields.classList.remove('d-none');
            } else if (method === 'fixed_amount') {
                fixedAmountFields.classList.remove('d-none');
            } else if (method === 'tiered_percentage' || method === 'tiered_amount') {
                tieredFields.classList.remove('d-none');
            }
        }

        calculationMethod.addEventListener('change', toggleFields);
        toggleFields();
    })();
</script>
@endpush
