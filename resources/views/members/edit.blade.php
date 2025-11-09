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

    <button class="btn btn-primary">Update</button>
    <a href="{{ route('members.index') }}" class="btn btn-secondary">Cancel</a>
</form>
</div>
@endsection
