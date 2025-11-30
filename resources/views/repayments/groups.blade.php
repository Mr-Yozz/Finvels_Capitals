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


    <form class="mb-3">
        <div class="row g-2 align-items-end">

            <!-- Group Filter -->
            <div class="col-md-3">
                <label class="form-label">Select Group</label>
                <select id="groupSelect" name="group_id" class="form-select">
                    <option value="">-- All Groups --</option>
                    @foreach($groups as $grp)
                    <option value="{{ $grp->id }}">{{ $grp->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Member Filter (Dynamic) -->
            <div class="col-md-3">
                <label class="form-label">Select Member</label>
                <select id="memberSelect" name="member_id" class="form-select" disabled>
                    <option value="">-- Select Member --</option>
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
    document.getElementById('groupSelect').addEventListener('change', function() {
        let groupId = this.value;
        let memberSelect = document.getElementById('memberSelect');

        memberSelect.innerHTML = '<option value="">Loading...</option>';
        memberSelect.disabled = true;

        if (!groupId) {
            memberSelect.innerHTML = '<option value="">-- Select Member --</option>';
            return;
        }

        fetch(`/group-members?group_id=${groupId}`)
            .then(res => res.json())
            .then(data => {
                memberSelect.innerHTML = '<option value="">-- Select Member --</option>';

                data.forEach(member => {
                    let opt = document.createElement('option');
                    opt.value = member.id;
                    opt.textContent = `${member.name} (${member.member_id})`;
                    memberSelect.appendChild(opt);
                });

                memberSelect.disabled = false;
            });
    });
</script>

@endsection