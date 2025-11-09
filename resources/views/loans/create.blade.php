@extends('layouts.app')

@section('content')
<h2 class="text-primary mb-3">Add Loan</h2>

<div class="card p-4">
<form action="{{ route('loans.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label class="form-label text-primary">Member</label>
        <select name="member_id" class="form-select" required>
            <option value="">-- Select Member --</option>
            @foreach($members as $member)
                <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                    {{ $member->name }} ({{ $member->group->name ?? '-' }} - {{ $member->group->branch->name ?? '-' }})
                </option>
            @endforeach
        </select>
        @error('member_id') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label text-primary">Branch</label>
        <select name="branch_id" class="form-select" required>
            <option value="">-- Select Branch --</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                    {{ $branch->name }}
                </option>
            @endforeach
        </select>
        @error('branch_id') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label text-primary">Principal</label>
        <input type="number" name="principal" class="form-control" value="{{ old('principal') }}" required step="0.01">
        @error('principal') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label text-primary">Interest Rate (%)</label>
        <input type="number" name="interest_rate" class="form-control" value="{{ old('interest_rate') }}" required step="0.01">
        @error('interest_rate') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label text-primary">Tenure (Months)</label>
        <input type="number" name="tenure_months" class="form-control" value="{{ old('tenure_months') }}" required>
        @error('tenure_months') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label text-primary">Disbursed Date</label>
        <input type="date" name="disbursed_at" class="form-control" value="{{ old('disbursed_at', now()->toDateString()) }}" required>
        @error('disbursed_at') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label text-primary">Status</label>
        <select name="status" class="form-select" required>
            <option value="pending" {{ old('status')=='pending' ? 'selected' : '' }}>Pending</option>
            <option value="active" {{ old('status')=='active' ? 'selected' : '' }}>Active</option>
            <option value="closed" {{ old('status')=='closed' ? 'selected' : '' }}>Closed</option>
        </select>
        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <button class="btn btn-primary">Save</button>
    <a href="{{ route('loans.index') }}" class="btn btn-secondary">Cancel</a>
</form>
</div>
@endsection
