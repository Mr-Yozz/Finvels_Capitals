@extends('layouts.app')

@section('content')
<h2 class="text-primary mb-3">Add Group</h2>

<div class="card p-4">
<form action="{{ route('groups.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label text-primary">Group Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        @error('name')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="mb-3">
        <label for="branch_id" class="form-label text-primary">Select Branch</label>
        <select name="branch_id" class="form-select" required>
            <option value="">-- Select Branch --</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                    {{ $branch->name }}
                </option>
            @endforeach
        </select>
        @error('branch_id')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <button class="btn btn-primary">Save</button>
    <a href="{{ route('groups.index') }}" class="btn btn-secondary">Cancel</a>
</form>
</div>
@endsection
