@extends('layouts.app')
@section('styles')
@section('content')
<div class="container my-4">
    <h2 class="text-primary fw-bold mb-4">Select Member</h2>

    <div class="row">
        @forelse($members as $member)
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">{{ $member->name }}</h5>
                            <p class="text-muted small mb-2">Member ID: #{{ $member->id }}</p>
                            <p class="mb-1"><i class="bi bi-telephone me-1"></i> {{ $member->mobile ?? '-' }}</p>
                            <p class="mb-0"><i class="bi bi-envelope me-1"></i> {{ $member->email ?? '-' }}</p>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('repayments.index', ['member_id' => $member->id]) }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-credit-card-2-front me-1"></i> View Repayments
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-4 text-muted">
                <i class="bi bi-people"></i> No members found.
            </div>
        @endforelse
    </div>
</div>
@endsection
