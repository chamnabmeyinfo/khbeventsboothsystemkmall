@extends('layouts.app')

@section('title', 'Setup Required')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Database Setup Required</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle me-2"></i>Booth Booking Tables Not Found</h5>
                    <p>The database is missing the required tables for the booth booking system.</p>
                </div>
                
                <h5>To fix this, you have two options:</h5>
                
                <div class="mt-4">
                    <h6><strong>Option 1: Run Migrations</strong></h6>
                    <p>Run the following command in your terminal:</p>
                    <pre class="bg-light p-3"><code>php artisan migrate --force</code></pre>
                </div>
                
                <div class="mt-4">
                    <h6><strong>Option 2: Import SQL File</strong></h6>
                    <p>Import the booth booking SQL file from the old project:</p>
                    <ul>
                        <li>File: <code>kmall-yii/DB/khb_booth_kmall.sql</code></li>
                        <li>Import it to your database: <code>khbeventskmall</code></li>
                    </ul>
                </div>
                
                <div class="mt-4">
                    <h6><strong>Error Details:</strong></h6>
                    <pre class="bg-light p-3 text-danger"><code>{{ $error ?? 'Unknown error' }}</code></pre>
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-refresh me-2"></i>Refresh Page
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
