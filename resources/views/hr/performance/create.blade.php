@extends('layouts.adminlte')

@section('title', 'Create Performance Review')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-star mr-2"></i>Create Performance Review</h1>
        <a href="{{ route('hr.performance.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i>Back</a>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <form action="{{ route('hr.performance.store') }}" method="POST">
        @csrf
        <div class="card card-primary card-outline">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="employee_id">Employee <span class="text-danger">*</span></label>
                            <select class="form-control select2 @error('employee_id') is-invalid @enderror" 
                                    id="employee_id" name="employee_id" required>
                                <option value="">Select Employee...</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="reviewer_id">Reviewer <span class="text-danger">*</span></label>
                            <select class="form-control select2 @error('reviewer_id') is-invalid @enderror" 
                                    id="reviewer_id" name="reviewer_id" required>
                                <option value="">Select Reviewer...</option>
                                @foreach($reviewers as $rev)
                                    <option value="{{ $rev->id }}" {{ old('reviewer_id') == $rev->id ? 'selected' : '' }}>
                                        {{ $rev->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('reviewer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="review_period_start">Review Period Start <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('review_period_start') is-invalid @enderror" 
                                   id="review_period_start" name="review_period_start" value="{{ old('review_period_start') }}" required>
                            @error('review_period_start')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="review_period_end">Review Period End <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('review_period_end') is-invalid @enderror" 
                                   id="review_period_end" name="review_period_end" value="{{ old('review_period_end') }}" required>
                            @error('review_period_end')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="review_date">Review Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('review_date') is-invalid @enderror" 
                                   id="review_date" name="review_date" value="{{ old('review_date', date('Y-m-d')) }}" required>
                            @error('review_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="overall_rating">Overall Rating (0-5)</label>
                            <input type="number" step="0.01" min="0" max="5" class="form-control" 
                                   id="overall_rating" name="overall_rating" value="{{ old('overall_rating') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="submitted" {{ old('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="goals_achieved">Goals Achieved</label>
                    <textarea class="form-control" id="goals_achieved" name="goals_achieved" rows="3">{{ old('goals_achieved') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="strengths">Strengths</label>
                    <textarea class="form-control" id="strengths" name="strengths" rows="3">{{ old('strengths') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="areas_for_improvement">Areas for Improvement</label>
                    <textarea class="form-control" id="areas_for_improvement" name="areas_for_improvement" rows="3">{{ old('areas_for_improvement') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="comments">Comments</label>
                    <textarea class="form-control" id="comments" name="comments" rows="3">{{ old('comments') }}</textarea>
                </div>

                <hr>
                <h5>Review Criteria</h5>
                <div id="criteria-container">
                    <div class="criteria-item mb-3 p-3 border rounded">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="criteria[0][criterion_name]" placeholder="Criterion Name" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" step="0.01" min="0" max="5" class="form-control" name="criteria[0][rating]" placeholder="Rating (0-5)">
                            </div>
                            <div class="col-md-2">
                                <input type="number" step="0.01" min="0" max="10" class="form-control" name="criteria[0][weight]" value="1.00" placeholder="Weight">
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="criteria[0][comments]" placeholder="Comments">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-secondary" onclick="addCriterion()">
                    <i class="fas fa-plus mr-1"></i>Add Criterion
                </button>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Create Review</button>
                <a href="{{ route('hr.performance.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
@stop

@push('scripts')
<script>
    let criterionIndex = 1;
    function addCriterion() {
        const container = document.getElementById('criteria-container');
        const newItem = document.createElement('div');
        newItem.className = 'criteria-item mb-3 p-3 border rounded';
        newItem.innerHTML = `
            <div class="row">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="criteria[${criterionIndex}][criterion_name]" placeholder="Criterion Name" required>
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" min="0" max="5" class="form-control" name="criteria[${criterionIndex}][rating]" placeholder="Rating (0-5)">
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" min="0" max="10" class="form-control" name="criteria[${criterionIndex}][weight]" value="1.00" placeholder="Weight">
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="criteria[${criterionIndex}][comments]" placeholder="Comments">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.criteria-item').remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        container.appendChild(newItem);
        criterionIndex++;
    }
    $(document).ready(function() {
        $('.select2').select2({ theme: 'bootstrap4' });
    });
</script>
@endpush
