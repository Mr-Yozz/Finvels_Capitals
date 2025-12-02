@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-4">Add Member </h3>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- MEMBER FORM --}}

    <div class="card p-4">
        <h5 class="fw-bold mb-3">Enter Member Details</h5>
        <!-- route('members.sendOtp') -->
        <form action="{{ route('members.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Member Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Mobile</label>
                <input type="text" name="mobile" class="form-control"
                    value="{{ session('member_form_data.mobile') }}" required>
            </div>

            <div class="mb-3">
                <label>Aadhaar</label>
                <input type="text" name="aadhaar_encrypted" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>PAN</label>
                <input type="text" name="pan_encrypted" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Account Number</label>
                <input type="text" name="account_number" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Bank Name</label>
                <input type="text" name="bank_name" class="form-control">
            </div>

            <!-- <div class="mb-3">
                <label>Select Branch</label>
                <select name="branch_id" class="form-select" required>
                    <option value="">-- Choose --</option>
                    @foreach(\App\Models\Branch::all() as $g)
                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                    @endforeach
                </select>
            </div> -->

            <div class="mb-3">
                <label>IFSC</label>
                <input type="text" name="ifsc_code" class="form-control" required>
            </div>

            <select name="group_id" class="form-select" required>
                <option value="">Select Group</option>
                @foreach($groups as $group)
                <option value="{{ $group->id }}"
                    {{ (old('group_id', $selectedGroupId ?? '') == $group->id) ? 'selected' : '' }}>
                    {{ $group->name }}
                </option>
                @endforeach
            </select>

            <select name="role" class="form-select" required>
                <option value="">Select Role</option>
                <option value="leader" {{ old('role') == 'leader' ? 'selected' : '' }}>Leader</option>
                <option value="sub_leader" {{ old('role') == 'sub_leader' ? 'selected' : '' }}>Sub Leader</option>
                <option value="member" {{ old('role') == 'member' ? 'selected' : '' }}>Member</option>
            </select>


            <button class="btn btn-success w-100">Create Member</button>
        </form>
    </div>

    @if(session('otp_sent'))
    <div class="card p-4 mt-4">
        <h4>Verify OTP</h4>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('members.verifyOtp') }}" method="POST">
            @csrf

            <input type="text" name="otp" class="form-control w-25 mb-3" placeholder="Enter OTP" required>

            <button class="btn btn-success">Verify & Create Member</button>
        </form>

        <!-- RESEND OTP -->
        <div class="mt-3">
            <form id="resendForm" action="{{ route('members.resendOtp') }}" method="POST">
                @csrf
                <button id="resendBtn" class="btn btn-warning" disabled>Resend OTP (<span id="timer">30</span>s)</button>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let timeLeft = 30;
    let timer = document.getElementById('timer');
    let resendBtn = document.getElementById('resendBtn');

    let countdown = setInterval(() => {
        timeLeft--;
        timer.textContent = timeLeft;

        if (timeLeft <= 0) {
            clearInterval(countdown);
            resendBtn.disabled = false;
            resendBtn.textContent = "Resend OTP";
        }
    }, 1000);
</script>
@endsection