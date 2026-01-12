@extends('layouts.adminlte')

@section('title', 'Performance Review Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-star mr-2"></i>Performance Review</h1>
        <div class="btn-group">
            @if(auth()->user()->hasPermission('hr.performance.edit'))
            <a href="{{ route('hr.performance.edit', $performanceReview) }}" class="btn btn-warning"><i class="fas fa-edit mr-1"></i>Edit</a>
            @endif
            @if(auth()->user()->hasPermission('hr.performance.delete'))
            <form action="{{ route('hr.performance.destroy', $performanceReview) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this performance review?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" title="Delete">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>
            </form>
            @endif
            <a href="{{ route('hr.performance.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i>Back</a>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Review Information</h3></div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr><th width="30%">Employee:</th><td><a href="{{ route('hr.employees.show', $performanceReview->employee) }}">{{ $performanceReview->employee->full_name }}</a></td></tr>
                        <tr><th>Reviewer:</th><td><a href="{{ route('hr.employees.show', $performanceReview->reviewer) }}">{{ $performanceReview->reviewer->full_name }}</a></td></tr>
                        <tr><th>Review Period:</th><td>{{ $performanceReview->review_period_start->format('M d, Y') }} - {{ $performanceReview->review_period_end->format('M d, Y') }}</td></tr>
                        <tr><th>Review Date:</th><td>{{ $performanceReview->review_date->format('M d, Y') }}</td></tr>
                        <tr><th>Overall Rating:</th><td>
                            @if($performanceReview->overall_rating)
                                <span class="badge badge-primary badge-lg">{{ number_format($performanceReview->overall_rating, 2) }}/5.00</span>
                            @else
                                -
                            @endif
                        </td></tr>
                        <tr><th>Status:</th><td>
                            <span class="badge badge-{{ $performanceReview->status == 'completed' ? 'success' : 'warning' }} badge-lg">
                                {{ ucfirst($performanceReview->status) }}
                            </span>
                        </td></tr>
                    </table>
                </div>
            </div>

            @if($performanceReview->criteria->count() > 0)
            <div class="card">
                <div class="card-header"><h3 class="card-title">Review Criteria</h3></div>
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Criterion</th>
                                <th>Rating</th>
                                <th>Weight</th>
                                <th>Comments</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($performanceReview->criteria as $criterion)
                            <tr>
                                <td><strong>{{ $criterion->criterion_name }}</strong></td>
                                <td>{{ $criterion->rating ? number_format($criterion->rating, 2) : '-' }}/5.00</td>
                                <td>{{ number_format($criterion->weight, 2) }}</td>
                                <td>{{ $criterion->comments ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            @if($performanceReview->goals_achieved || $performanceReview->strengths || $performanceReview->areas_for_improvement)
            <div class="card">
                <div class="card-header"><h3 class="card-title">Review Details</h3></div>
                <div class="card-body">
                    @if($performanceReview->goals_achieved)
                    <h6>Goals Achieved:</h6>
                    <p>{{ $performanceReview->goals_achieved }}</p>
                    @endif

                    @if($performanceReview->strengths)
                    <h6>Strengths:</h6>
                    <p>{{ $performanceReview->strengths }}</p>
                    @endif

                    @if($performanceReview->areas_for_improvement)
                    <h6>Areas for Improvement:</h6>
                    <p>{{ $performanceReview->areas_for_improvement }}</p>
                    @endif

                    @if($performanceReview->comments)
                    <h6>Comments:</h6>
                    <p>{{ $performanceReview->comments }}</p>
                    @endif

                    @if($performanceReview->employee_comments)
                    <h6>Employee Comments:</h6>
                    <p>{{ $performanceReview->employee_comments }}</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@stop
