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

        <!-- PRODUCT NAME -->
        <div class="mb-3">
            <label class="form-label text-primary">Product Name</label>
            <input type="text" name="product_name" class="form-control" placeholder="Example: Pragati Plus Loan" required>
        </div>

        <!-- LOAN PURPOSE -->
        <div class="mb-3">
            <label class="form-label text-primary">Loan Purpose</label>
            <input type="text" name="purpose" class="form-control" placeholder="Example: Tailoring Machine" required>
        </div>

        <div class="mb-3">
            <label class="form-label text-primary">Principal</label>
            <input type="number" id="principal" name="principal" class="form-control" value="{{ old('principal') }}" required step="0.01">
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

        <!-- REPAYMENT FREQUENCY -->
        <div class="mb-3">
            <label class="form-label text-primary">Repayment Frequency</label>
            <select name="repayment_frequency" class="form-select" required>
                <option value="monthly">Monthly</option>
                <option value="weekly">Weekly</option>
            </select>
        </div>

        <!-- PROCESSING FEE -->
        <div class="mb-3">
            <label class="form-label text-primary">
                Processing Fee (1.5% + GST Auto)
            </label>
            <input type="number" id="processing_fee" name="processing_fee" class="form-control" readonly>
        </div>

        <!-- INSURANCE -->
        <div class="mb-3">
            <label class="form-label text-primary">Insurance Amount</label>
            <input type="number" name="insurance_amount" class="form-control" placeholder="Enter amount" required>
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

@section('scripts')
<script>
    document.getElementById("principal").addEventListener("input", function() {
        let principal = parseFloat(this.value);
        if (!isNaN(principal)) {
            let pf = principal * 0.015; // 1.5%
            let gst = pf * 0.18; // 18% GST
            let total = pf + gst;
            document.getElementById("processing_fee").value = total.toFixed(2);
        }
    });
</script>
@endsection