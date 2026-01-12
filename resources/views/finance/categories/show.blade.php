@extends('layouts.adminlte')

@section('title', 'View Finance Category')
@section('page-title', 'View Finance Category #' . $category->id)
@section('breadcrumb', 'Finance / Categories / Show')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-tags mr-2"></i>Category Details</h3>
            <div class="card-tools">
                <a href="{{ route('finance.categories.edit', $category) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
                <a href="{{ route('finance.categories.index') }}" class="btn btn-default btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i>Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 200px;">ID</th>
                            <td>#{{ $category->id }}</td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <td><strong>{{ $category->name }}</strong></td>
                        </tr>
                        <tr>
                            <th>Type</th>
                            <td>
                                @php
                                    $typeColors = [
                                        'expense' => 'danger',
                                        'revenue' => 'success',
                                        'costing' => 'info'
                                    ];
                                    $typeColor = $typeColors[$category->type] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $typeColor }}">{{ ucfirst($category->type) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Color</th>
                            <td>
                                @if($category->color)
                                    <span style="display: inline-block; width: 40px; height: 40px; border-radius: 8px; border: 2px solid #ddd; background-color: {{ $category->color }}; vertical-align: middle;"></span>
                                    <span class="ml-2">{{ $category->color }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($category->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Sort Order</th>
                            <td>{{ $category->sort_order ?? 0 }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 200px;">Created By</th>
                            <td>{{ $category->createdBy->username ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $category->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $category->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Usage Count</th>
                            <td>
                                @if($category->type === \App\Models\FinanceCategory::TYPE_EXPENSE)
                                    <strong>{{ $expenseCount ?? 0 }}</strong> expense(s)
                                    @if(($expenseCount ?? 0) > 0)
                                        <a href="{{ route('finance.expenses.index', ['category_id' => $category->id]) }}" class="btn btn-sm btn-link">View Expenses</a>
                                    @endif
                                @elseif($category->type === \App\Models\FinanceCategory::TYPE_REVENUE)
                                    <strong>{{ $revenueCount ?? 0 }}</strong> revenue(s)
                                    @if(($revenueCount ?? 0) > 0)
                                        <a href="{{ route('finance.revenues.index', ['category_id' => $category->id]) }}" class="btn btn-sm btn-link">View Revenues</a>
                                    @endif
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            @if($category->description)
            <div class="row mt-3">
                <div class="col-12">
                    <h5>Description</h5>
                    <p class="text-muted">{{ $category->description }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
