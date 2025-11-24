@extends('layouts.app')

@section('content')
<h2 class="text-primary mb-3">Add Branch</h2>

<div class="card p-4">
    <form action="{{ route('branches.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label text-primary">Branch Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            @error('name')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label text-primary">Select Branch Manager</label>
            <select name="user_id" class="form-select" required>
                <option value="">-- Select Manager --</option>
                @foreach($managers as $manager)
                <option value="{{ $manager->id }}">
                    {{ $manager->name }} ({{ $manager->mobile ?? 'No Mobile' }})
                </option>
                @endforeach
            </select>
            @error('user_id')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="address" class="form-label text-primary">Address</label>
            <textarea id="address" name="address" rows="4" placeholder="123 Main St, City, State, ZIP, Country" value="{{ old('address') }}" required></textarea>
            @error('address')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('branches.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection