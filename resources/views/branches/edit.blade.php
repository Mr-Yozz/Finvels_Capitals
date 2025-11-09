@extends('layouts.app')

@section('content')
<h2 class="text-primary mb-3">Edit Branch</h2>

<div class="card p-4">
    <form action="{{ route('branches.update', $branch->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label text-primary">Branch Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $branch->name) }}" required>
            @error('name')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="mb-3">
            <label for="address" class="form-label text-primary">Address</label>
            <textarea id="address" name="address" rows="4" placeholder="123 Main St, City, State, ZIP, Country" value="{{ old('address', $branch->address) }}" required></textarea>
            @error('address')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <button class="btn btn-primary">Update</button>
        <a href="{{ route('branches.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection