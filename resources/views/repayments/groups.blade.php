@extends('layouts.app')
@section('styles')
<style>
    .text-primary {
        color: #0d6efd !important;
    }
</style>
@endsection
@section('content')
<div class="container py-4">
    <h2 class="text-primary fw-bold mb-0">Groups</h2>


    <form method="GET" action="{{ url()->current() }}" class="mb-3">
        <input type="hidden" name="filter_mode" value="1">
        <div class="row g-2 align-items-end">

            <!-- Group Filter -->
            <div class="col-md-3">
                <label class="form-label">Select Group</label>
                <select id="groupSelect" name="group_id" class="form-select">
                    <option value="">-- All Groups --</option>
                    @foreach($allGroups as $grp)
                    <option value="{{ $grp->id }}" {{ request('group_id') == $grp->id ? 'selected' : '' }}>
                        {{ $grp->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="daySelect" class="form-label">Select Day</label>
                <select id="daySelect" name="day" class="form-select">
                    <option value="">-- Select Day --</option>
                    {{-- Iterate over the $days array passed from the controller --}}
                    @foreach($days as $dayValue => $dayName)
                    <option value="{{ $dayValue }}"
                        {{-- Retain selection on page reload/validation fail --}}
                        {{ (old('day', $selectedDay ?? '') == $dayValue) ? 'selected' : '' }}>
                        {{ $dayName }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
        </div>
    </form>



    <div class="row g-3">
        @foreach($groups as $group)
        <div class="col-md-3">
            <a href="{{ route('repayments.index', ['group_id' => $group->id]) }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100 group-card">
                    <div class="card-body">
                        <h5 class="card-title text-primary fw-semibold">
                            <i class="bi bi-people"></i> {{ $group->name }}
                        </h5>
                        <h6 class="card-title text-primary fw-semibold">
                            <i class="bi bi-people"></i> {{ $group->day }}
                        </h6>
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


@section('scripts')

<script>
    const groupSelect = document.getElementById('groupSelect');
    const daySelect = document.getElementById('daySelect');
    const form = groupSelect.closest('form');

    groupSelect.addEventListener('change', () => form.submit());
    daySelect.addEventListener('change', () => form.submit());
</script>

@endsection