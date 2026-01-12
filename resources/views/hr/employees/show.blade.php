@extends('layouts.adminlte')

@section('title', 'Employee Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-user mr-2"></i>{{ $employee->full_name }}
        </h1>
        <div class="btn-group">
            @if(auth()->user()->hasPermission('hr.employees.edit'))
            <a href="{{ route('hr.employees.edit', $employee) }}" class="btn btn-warning">
                <i class="fas fa-edit mr-1"></i>Edit
            </a>
            @endif
            @if(auth()->user()->hasPermission('hr.employees.create'))
            <form action="{{ route('hr.employees.duplicate', $employee) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" title="Duplicate Employee">
                    <i class="fas fa-copy mr-1"></i>Duplicate
                </button>
            </form>
            @endif
            @if(auth()->user()->hasPermission('hr.employees.delete'))
            <form action="{{ route('hr.employees.destroy', $employee) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this employee? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" title="Delete Employee">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>
            </form>
            @endif
            <a href="{{ route('hr.employees.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i>Back
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Left Column - Employee Info -->
        <div class="col-md-4">
            <!-- Employee Card -->
            <div class="card card-primary card-outline">
                <div class="card-body text-center">
                    @if($employee->avatar)
                        <img src="{{ asset('storage/' . $employee->avatar) }}" 
                             alt="{{ $employee->full_name }}" 
                             class="img-circle img-size-128 mb-3">
                    @else
                        <div class="img-circle bg-primary img-size-128 d-flex align-items-center justify-content-center mx-auto mb-3">
                            <span class="text-white font-weight-bold" style="font-size: 48px;">
                                {{ substr($employee->first_name, 0, 1) }}
                            </span>
                        </div>
                    @endif
                    <h3>{{ $employee->full_name }}</h3>
                    <p class="text-muted">{{ $employee->employee_code }}</p>
                    @if($employee->position)
                        <p><strong>{{ $employee->position->name }}</strong></p>
                    @endif
                    @if($employee->department)
                        <p class="text-muted">{{ $employee->department->name }}</p>
                    @endif
                    @php
                        $statusColors = [
                            'active' => 'success',
                            'inactive' => 'secondary',
                            'terminated' => 'danger',
                            'on-leave' => 'warning',
                            'suspended' => 'danger'
                        ];
                        $color = $statusColors[$employee->status] ?? 'secondary';
                    @endphp
                    <span class="badge badge-{{ $color }} badge-lg">{{ ucfirst(str_replace('-', ' ', $employee->status)) }}</span>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar mr-2"></i>Quick Stats</h3>
                </div>
                <div class="card-body">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-info"><i class="fas fa-calendar-check"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Attendance Days</span>
                            <span class="info-box-number">{{ $stats['total_attendance_days'] }}</span>
                        </div>
                    </div>
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success"><i class="fas fa-calendar-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Leave Days</span>
                            <span class="info-box-number">{{ $stats['total_leave_days'] }}</span>
                        </div>
                    </div>
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pending Leaves</span>
                            <span class="info-box-number">{{ $stats['pending_leave_requests'] }}</span>
                        </div>
                    </div>
                    <div class="info-box">
                        <span class="info-box-icon bg-primary"><i class="fas fa-star"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Avg Rating</span>
                            <span class="info-box-number">{{ number_format($stats['average_rating'] ?? 0, 2) }}/5.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Details -->
        <div class="col-md-8">
            <!-- Tabs -->
            <div class="card card-primary card-outline card-tabs">
                <div class="card-header p-0 pt-1 border-bottom-0">
                    <ul class="nav nav-tabs" id="employee-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="info-tab" data-toggle="pill" href="#info" role="tab">
                                <i class="fas fa-info-circle mr-1"></i>Information
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="attendance-tab" data-toggle="pill" href="#attendance" role="tab">
                                <i class="fas fa-clock mr-1"></i>Attendance
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="leaves-tab" data-toggle="pill" href="#leaves" role="tab">
                                <i class="fas fa-calendar-alt mr-1"></i>Leaves
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="performance-tab" data-toggle="pill" href="#performance" role="tab">
                                <i class="fas fa-star mr-1"></i>Performance
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="training-tab" data-toggle="pill" href="#training" role="tab">
                                <i class="fas fa-graduation-cap mr-1"></i>Training
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="documents-tab" data-toggle="pill" href="#documents" role="tab">
                                <i class="fas fa-file-alt mr-1"></i>Documents
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="salary-tab" data-toggle="pill" href="#salary" role="tab">
                                <i class="fas fa-dollar-sign mr-1"></i>Salary History
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="employee-tabs-content">
                        <!-- Information Tab -->
                        <div class="tab-pane fade show active" id="info" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-user mr-2"></i>Personal Information</h5>
                                    <table class="table table-sm">
                                        <tr><th width="40%">Email:</th><td>{{ $employee->email ?? '-' }}</td></tr>
                                        <tr><th>Phone:</th><td>{{ $employee->phone ?? '-' }}</td></tr>
                                        <tr><th>Mobile:</th><td>{{ $employee->mobile ?? '-' }}</td></tr>
                                        <tr><th>Date of Birth:</th><td>{{ $employee->date_of_birth ? $employee->date_of_birth->format('M d, Y') : '-' }}</td></tr>
                                        <tr><th>Gender:</th><td>{{ ucfirst($employee->gender ?? '-') }}</td></tr>
                                        <tr><th>Nationality:</th><td>{{ $employee->nationality ?? '-' }}</td></tr>
                                    </table>

                                    <h5 class="mt-4"><i class="fas fa-id-card mr-2"></i>Identification</h5>
                                    <table class="table table-sm">
                                        <tr><th width="40%">ID Card:</th><td>{{ $employee->id_card_number ?? '-' }}</td></tr>
                                        <tr><th>Passport:</th><td>{{ $employee->passport_number ?? '-' }}</td></tr>
                                        <tr><th>Tax ID:</th><td>{{ $employee->tax_id ?? '-' }}</td></tr>
                                        <tr><th>SSN:</th><td>{{ $employee->social_security_number ?? '-' }}</td></tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5><i class="fas fa-building mr-2"></i>Employment Information</h5>
                                    <table class="table table-sm">
                                        <tr><th width="40%">Department:</th><td>{{ $employee->department->name ?? '-' }}</td></tr>
                                        <tr><th>Position:</th><td>{{ $employee->position->name ?? '-' }}</td></tr>
                                        <tr><th>Manager:</th><td>
                                            @if($employee->manager)
                                                <a href="{{ route('hr.employees.show', $employee->manager) }}">
                                                    {{ $employee->manager->full_name }}
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td></tr>
                                        <tr><th>Employment Type:</th><td>{{ ucfirst(str_replace('-', ' ', $employee->employment_type)) }}</td></tr>
                                        <tr><th>Hire Date:</th><td>{{ $employee->hire_date->format('M d, Y') }}</td></tr>
                                        <tr><th>Probation End:</th><td>{{ $employee->probation_end_date ? $employee->probation_end_date->format('M d, Y') : '-' }}</td></tr>
                                    </table>

                                    <h5 class="mt-4"><i class="fas fa-dollar-sign mr-2"></i>Compensation</h5>
                                    <table class="table table-sm">
                                        <tr><th width="40%">Salary:</th><td>
                                            @if($employee->salary)
                                                {{ number_format($employee->salary, 2) }} {{ $employee->currency ?? 'USD' }}
                                            @else
                                                -
                                            @endif
                                        </td></tr>
                                        <tr><th>Bank:</th><td>{{ $employee->bank_name ?? '-' }}</td></tr>
                                        <tr><th>Account:</th><td>{{ $employee->bank_account ?? '-' }}</td></tr>
                                    </table>
                                </div>
                            </div>

                            @if($employee->address || $employee->city)
                            <h5 class="mt-4"><i class="fas fa-map-marker-alt mr-2"></i>Address</h5>
                            <p>{{ $employee->address ?? '' }}<br>
                               {{ $employee->city ?? '' }}{{ $employee->state ? ', ' . $employee->state : '' }}<br>
                               {{ $employee->postal_code ?? '' }} {{ $employee->country ?? '' }}</p>
                            @endif

                            @if($employee->emergency_contact_name)
                            <h5 class="mt-4"><i class="fas fa-phone-alt mr-2"></i>Emergency Contact</h5>
                            <p><strong>{{ $employee->emergency_contact_name }}</strong><br>
                               {{ $employee->emergency_contact_phone ?? '' }}<br>
                               {{ $employee->emergency_contact_relationship ?? '' }}</p>
                            @endif

                            @if($employee->notes)
                            <h5 class="mt-4"><i class="fas fa-sticky-note mr-2"></i>Notes</h5>
                            <p>{{ $employee->notes }}</p>
                            @endif
                        </div>

                        <!-- Attendance Tab -->
                        <div class="tab-pane fade" id="attendance" role="tabpanel">
                            <div class="d-flex justify-content-between mb-3">
                                <h5>Recent Attendance</h5>
                                <a href="{{ route('hr.attendance.index', ['employee_id' => $employee->id]) }}" class="btn btn-sm btn-primary">
                                    View All
                                </a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Check In</th>
                                            <th>Check Out</th>
                                            <th>Hours</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($employee->attendance as $att)
                                        <tr>
                                            <td>{{ $att->date->format('M d, Y') }}</td>
                                            <td>{{ $att->check_in_time ? \Carbon\Carbon::parse($att->check_in_time)->format('H:i') : '-' }}</td>
                                            <td>{{ $att->check_out_time ? \Carbon\Carbon::parse($att->check_out_time)->format('H:i') : '-' }}</td>
                                            <td>{{ $att->total_hours ?? '-' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $att->status == 'present' ? 'success' : ($att->status == 'absent' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($att->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No attendance records</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Leaves Tab -->
                        <div class="tab-pane fade" id="leaves" role="tabpanel">
                            <div class="d-flex justify-content-between mb-3">
                                <h5>Recent Leave Requests</h5>
                                <a href="{{ route('hr.leaves.index', ['employee_id' => $employee->id]) }}" class="btn btn-sm btn-primary">
                                    View All
                                </a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Leave Type</th>
                                            <th>Period</th>
                                            <th>Days</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($employee->leaveRequests as $leave)
                                        <tr>
                                            <td>{{ $leave->leaveType->name }}</td>
                                            <td>{{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d, Y') }}</td>
                                            <td>{{ $leave->total_days }}</td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'warning',
                                                        'approved' => 'success',
                                                        'rejected' => 'danger',
                                                        'cancelled' => 'secondary'
                                                    ];
                                                    $color = $statusColors[$leave->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge badge-{{ $color }}">{{ ucfirst($leave->status) }}</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No leave requests</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Performance Tab -->
                        <div class="tab-pane fade" id="performance" role="tabpanel">
                            <div class="d-flex justify-content-between mb-3">
                                <h5>Performance Reviews</h5>
                                <a href="{{ route('hr.performance.index', ['employee_id' => $employee->id]) }}" class="btn btn-sm btn-primary">
                                    View All
                                </a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Review Date</th>
                                            <th>Period</th>
                                            <th>Rating</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($employee->performanceReviews as $review)
                                        <tr>
                                            <td>{{ $review->review_date->format('M d, Y') }}</td>
                                            <td>{{ $review->review_period_start->format('M d') }} - {{ $review->review_period_end->format('M d, Y') }}</td>
                                            <td>
                                                @if($review->overall_rating)
                                                    <span class="badge badge-primary">{{ number_format($review->overall_rating, 2) }}/5.00</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $review->status == 'completed' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($review->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No performance reviews</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Training Tab -->
                        <div class="tab-pane fade" id="training" role="tabpanel">
                            <div class="d-flex justify-content-between mb-3">
                                <h5>Training Records</h5>
                                <a href="{{ route('hr.training.index', ['employee_id' => $employee->id]) }}" class="btn btn-sm btn-primary">
                                    View All
                                </a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Training Name</th>
                                            <th>Provider</th>
                                            <th>Period</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($employee->training as $training)
                                        <tr>
                                            <td>{{ $training->training_name }}</td>
                                            <td>{{ $training->training_provider ?? '-' }}</td>
                                            <td>
                                                @if($training->start_date && $training->end_date)
                                                    {{ $training->start_date->format('M d') }} - {{ $training->end_date->format('M d, Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $training->status == 'completed' ? 'success' : ($training->status == 'in-progress' ? 'info' : 'warning') }}">
                                                    {{ ucfirst(str_replace('-', ' ', $training->status)) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No training records</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Documents Tab -->
                        <div class="tab-pane fade" id="documents" role="tabpanel">
                            <div class="d-flex justify-content-between mb-3">
                                <h5>Employee Documents</h5>
                                @if(auth()->user()->hasPermission('hr.documents.upload'))
                                <a href="{{ route('hr.documents.create', ['employee_id' => $employee->id]) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus mr-1"></i>Upload Document
                                </a>
                                @endif
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Document Type</th>
                                            <th>Name</th>
                                            <th>Upload Date</th>
                                            <th>Expiry Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($employee->documents as $doc)
                                        <tr>
                                            <td>{{ $doc->document_type }}</td>
                                            <td>{{ $doc->document_name }}</td>
                                            <td>{{ $doc->created_at->format('M d, Y') }}</td>
                                            <td>
                                                @if($doc->expiry_date)
                                                    @if($doc->isExpired())
                                                        <span class="badge badge-danger">Expired</span>
                                                    @elseif($doc->isExpiringSoon())
                                                        <span class="badge badge-warning">{{ $doc->expiry_date->format('M d, Y') }}</span>
                                                    @else
                                                        {{ $doc->expiry_date->format('M d, Y') }}
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('hr.documents.download', $doc) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No documents</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Salary Tab -->
                        <div class="tab-pane fade" id="salary" role="tabpanel">
                            <div class="d-flex justify-content-between mb-3">
                                <h5>Salary History</h5>
                                @if(auth()->user()->hasPermission('hr.salary.create'))
                                <a href="{{ route('hr.salary.create', ['employee_id' => $employee->id]) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus mr-1"></i>Add Salary Entry
                                </a>
                                @endif
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Effective Date</th>
                                            <th>Salary</th>
                                            <th>Currency</th>
                                            <th>Reason</th>
                                            <th>Approved By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($employee->salaryHistory as $salary)
                                        <tr>
                                            <td>{{ $salary->effective_date->format('M d, Y') }}</td>
                                            <td><strong>{{ number_format($salary->salary, 2) }}</strong></td>
                                            <td>{{ $salary->currency }}</td>
                                            <td>{{ $salary->reason ?? '-' }}</td>
                                            <td>{{ $salary->approver->username ?? '-' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No salary history</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@push('styles')
<style>
    .img-size-128 {
        width: 128px;
        height: 128px;
    }
</style>
@endpush
