@extends('layouts.app')

@section('content')

<h4>Edit User</h4>

<form action="{{ route('users.update', $user->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control"
            value="{{ $user->name }}" required>
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control"
            value="{{ $user->email }}" required>
    </div>

    <div class="mb-3">
        <label>Phone Number</label>
        <input type="text" name="number" class="form-control"
            value="{{ $user->number }}">
    </div>

    <div class="mb-3">
        <label class="form-label">Role</label>
        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
            <option value="manager" selected>Manager</option>
        </select>
        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <button class="btn btn-primary">Update</button>
</form>

@endsection