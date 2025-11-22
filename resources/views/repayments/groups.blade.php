@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">📋 Groups</h4>

    <div class="row g-3">
        @foreach($groups as $group)
        <div class="col-md-3">
            <a href="{{ route('repayments.index', ['group_id' => $group->id]) }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100 group-card">
                    <div class="card-body">
                        <h5 class="card-title text-primary fw-semibold">
                            <i class="bi bi-people"></i> {{ $group->name }}
                        </h5>
                        <p class="mb-0 text-muted">Members: {{ $group->members->count() }}</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="mt-3">
            <a href="{{ route('collection.sheet', ['groupId' => $group->id]) }}"
                class="btn btn-primary btn-sm">
                Full Repayments
            </a>
        </div>
        @endforeach
    </div>


    <div class="mt-3">
        {{ $groups->links('pagination::bootstrap-5') }}
    </div>
</div>

<style>
    .group-card:hover {
        background-color: #f8f9fa;
        transform: translateY(-2px);
        transition: 0.2s;
    }
</style>
@endsection