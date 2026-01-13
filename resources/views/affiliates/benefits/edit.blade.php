@extends('layouts.adminlte')

@section('title', 'Edit Affiliate Benefit')
@section('page-title', 'Edit Benefit')
@section('breadcrumb', 'Sales / Affiliates / Benefits / Edit')

@push('styles')
<style>
    .form-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-section h5 {
        margin-bottom: 1rem;
        color: #495057;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-edit mr-2"></i>Edit Benefit: {{ $benefit->name }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('affiliates.benefits.update', $benefit->id) }}" method="POST" id="benefitForm">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="form-section">
                    <h5>Basic Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Benefit Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $benefit->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                    <option value="commission" {{ old('type', $benefit->type) == 'commission' ? 'selected' : '' }}>Commission</option>
                                    <option value="bonus" {{ old('type', $benefit->type) == 'bonus' ? 'selected' : '' }}>Bonus</option>
                                    <option value="incentive" {{ old('type', $benefit->type) == 'incentive' ? 'selected' : '' }}>Incentive</option>
                                    <option value="reward" {{ old('type', $benefit->type) == 'reward' ? 'selected' : '' }}>Reward</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Priority</label>
                                <input type="number" name="priority" class="form-control" 
                                       value="{{ old('priority', $benefit->priority) }}" min="0">
                                <small class="text-muted">Higher priority = applied first</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $benefit->description) }}</textarea>
                    </div>
                </div>

                <!-- Calculation Method -->
                <div class="form-section">
                    <h5>Calculation Method</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Method <span class="text-danger">*</span></label>
                                <select name="calculation_method" id="calculation_method" 
                                        class="form-control @error('calculation_method') is-invalid @enderror" required>
                                    <option value="percentage" {{ old('calculation_method', $benefit->calculation_method) == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                    <option value="fixed_amount" {{ old('calculation_method', $benefit->calculation_method) == 'fixed_amount' ? 'selected' : '' }}>Fixed Amount</option>
                                    <option value="tiered_percentage" {{ old('calculation_method', $benefit->calculation_method) == 'tiered_percentage' ? 'selected' : '' }}>Tiered Percentage</option>
                                    <option value="tiered_amount" {{ old('calculation_method', $benefit->calculation_method) == 'tiered_amount' ? 'selected' : '' }}>Tiered Amount</option>
                                </select>
                                @error('calculation_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Percentage -->
                    <div id="percentage_fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Percentage (%) <span class="text-danger">*</span></label>
                                    <input type="number" name="percentage" id="percentage" 
                                           class="form-control @error('percentage') is-invalid @enderror" 
                                           step="0.01" min="0" max="100" value="{{ old('percentage', $benefit->percentage) }}">
                                    @error('percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fixed Amount -->
                    <div id="fixed_amount_fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Fixed Amount ($) <span class="text-danger">*</span></label>
                                    <input type="number" name="fixed_amount" id="fixed_amount" 
                                           class="form-control @error('fixed_amount') is-invalid @enderror" 
                                           step="0.01" min="0" value="{{ old('fixed_amount', $benefit->fixed_amount) }}">
                                    @error('fixed_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tiered Structure -->
                    <div id="tiered_fields" style="display: none;">
                        <div class="form-group">
                            <label>Tier Structure (JSON) <span class="text-danger">*</span></label>
                            <textarea name="tier_structure" id="tier_structure" class="form-control" rows="6">{{ old('tier_structure', $benefit->tier_structure ? json_encode($benefit->tier_structure, JSON_PRETTY_PRINT) : '') }}</textarea>
                            <small class="text-muted">
                                For tiered_percentage: Use "percentage" field. For tiered_amount: Use "amount" field.
                            </small>
                            @error('tier_structure')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Targets -->
                <div class="form-section">
                    <h5>Targets (Optional)</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Target Revenue ($)</label>
                                <input type="number" name="target_revenue" class="form-control" 
                                       step="0.01" min="0" value="{{ old('target_revenue', $benefit->target_revenue) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Target Bookings</label>
                                <input type="number" name="target_bookings" class="form-control" 
                                       min="0" value="{{ old('target_bookings', $benefit->target_bookings) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Target Clients</label>
                                <input type="number" name="target_clients" class="form-control" 
                                       min="0" value="{{ old('target_clients', $benefit->target_clients) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conditions -->
                <div class="form-section">
                    <h5>Conditions</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Minimum Revenue ($)</label>
                                <input type="number" name="min_revenue" class="form-control" 
                                       step="0.01" min="0" value="{{ old('min_revenue', $benefit->min_revenue) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Maximum Benefit ($)</label>
                                <input type="number" name="max_benefit" class="form-control" 
                                       step="0.01" min="0" value="{{ old('max_benefit', $benefit->max_benefit) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Applicability -->
                <div class="form-section">
                    <h5>Applicability</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Specific User (Optional)</label>
                                <select name="user_id" class="form-control">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', $benefit->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->username }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Specific Floor Plan (Optional)</label>
                                <select name="floor_plan_id" class="form-control">
                                    <option value="">All Floor Plans</option>
                                    @foreach($floorPlans as $fp)
                                        <option value="{{ $fp->id }}" {{ old('floor_plan_id', $benefit->floor_plan_id) == $fp->id ? 'selected' : '' }}>
                                            {{ $fp->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Start Date (Optional)</label>
                                <input type="date" name="start_date" class="form-control" 
                                       value="{{ old('start_date', $benefit->start_date ? $benefit->start_date->format('Y-m-d') : '') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>End Date (Optional)</label>
                                <input type="date" name="end_date" class="form-control" 
                                       value="{{ old('end_date', $benefit->end_date ? $benefit->end_date->format('Y-m-d') : '') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                               value="1" {{ old('is_active', $benefit->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>Update Benefit
                    </button>
                    <a href="{{ route('affiliates.benefits.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const calculationMethod = document.getElementById('calculation_method');
    
    function toggleFields() {
        const method = calculationMethod.value;
        
        // Hide all fields
        document.getElementById('percentage_fields').style.display = 'none';
        document.getElementById('fixed_amount_fields').style.display = 'none';
        document.getElementById('tiered_fields').style.display = 'none';
        
        // Show relevant fields
        if (method === 'percentage') {
            document.getElementById('percentage_fields').style.display = 'block';
        } else if (method === 'fixed_amount') {
            document.getElementById('fixed_amount_fields').style.display = 'block';
        } else if (method === 'tiered_percentage' || method === 'tiered_amount') {
            document.getElementById('tiered_fields').style.display = 'block';
        }
    }
    
    calculationMethod.addEventListener('change', toggleFields);
    toggleFields(); // Initialize on page load
</script>
@endpush
