@extends('layouts.app')
@section('styles')
<style>
    .icon-wrapper {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }

    .hover-shadow:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12) !important;
        transition: all 0.3s ease-in-out;
    }
</style>
@endsection
@section('content')
<div class="container mt-4">

    <h2>User Profile</h2>
    <hr>

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Profile Update Form -->
    <form action="{{ route('admin.user.update') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Name:</label>
            <input type="text" name="name" class="form-control" value="{{ $user->name }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Email:</label>
            <input type="email" name="email" class="form-control" value="{{ $user->email }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Role:</label>
            <input type="text" name="role" class="form-control" value="{{ $user->role }}">
        </div>

        <button class="btn btn-primary">Update Profile</button>
    </form>

    <hr>

    <!-- Password Update -->
    <h4>Change Password</h4>

    <form action="{{ route('admin.user.updatePassword') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Current Password:</label>
            <input type="password" name="current_password" class="form-control">
            @error('current_password') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">New Password:</label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Confirm Password:</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>

        <button class="btn btn-warning">Update Password</button>
    </form>

    <hr>

    <!-- Delete Account -->
    <h4 class="text-danger">Delete Account</h4>

    <form action="{{ route('admin.user.delete') }}" method="POST">
        @csrf
        @method('DELETE')

        <div class="mb-3">
            <label class="form-label">Confirm Password:</label>
            <input type="password" name="password" class="form-control">
            @error('password') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <button class="btn btn-danger" onclick="return confirm('Are you sure? This cannot be undone.')">
            Delete Account
        </button>
    </form>

</div>
@endsection