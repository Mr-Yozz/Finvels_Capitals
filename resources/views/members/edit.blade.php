@extends('layouts.app')

@section('content')
<h2 class="text-primary mb-3">Edit Member</h2>

<div class="card p-4">
    <form action="{{ route('members.update', $member->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label text-primary">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $member->name) }}" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label text-primary">Mobile</label>
            <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $member->mobile) }}" required>
            @error('mobile') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label text-primary">Aadhaar</label>
            <input type="text" name="aadhaar_encrypted" class="form-control" value="{{ old('aadhaar_encrypted', $member->aadhaar_encrypted) }}" required>
            @error('aadhaar_encrypted') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label text-primary">PAN</label>
            <input type="text" name="pan_encrypted" class="form-control" value="{{ old('pan_encrypted', $member->pan_encrypted) }}" required>
            @error('pan_encrypted') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label text-primary">Bank Name</label>
            <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $member->bank_name) }}">
            @error('bank_name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label text-primary">Account No</label>
            <input type="number" name="account_number" class="form-control" value="{{ old('account_number', $member->account_number) }}" required>
            @error('account_number') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label text-primary">IFSC Code</label>
            <input type="text" name="ifsc_code" class="form-control" value="{{ old('ifsc_code', $member->ifsc_code) }}" required>
            @error('ifsc_code') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label text-primary">Select Group</label>
            <select name="group_id" class="form-select" required>
                <option value="">-- Select Group --</option>
                @foreach($groups as $group)
                <option value="{{ $group->id }}" {{ old('group_id', $member->group_id) == $group->id ? 'selected' : '' }}>
                    {{ $group->name }} ({{ $group->branch->name ?? '-' }})
                </option>
                @endforeach
            </select>
            @error('group_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <select name="role" class="form-select" required>
            <option value="">Select Role</option>
            <option value="leader" {{ old('role') == 'leader' ? 'selected' : '' }}>Leader</option>
            <option value="sub_leader" {{ old('role') == 'sub_leader' ? 'selected' : '' }}>Sub Leader</option>
            <option value="member" {{ old('role') == 'member' ? 'selected' : '' }}>Member</option>
        </select>

        <button class="btn btn-primary">Update</button>
        <a href="{{ route('members.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection