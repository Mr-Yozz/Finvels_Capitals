@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between mb-3">
    <h4>User Management</h4>

    @if(auth()->user()->role !== 'user')
    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Add Manager
    </a>
    @endif
</div>

<table class="table table-bordered table-striped">
    <thead class="table-light">
        <tr>

            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Phone Number</th>
            <th width="150">Action</th>
        </tr>
    </thead>

    <tbody>

        @foreach($users as $u)
        @if($u->role !== 'user')
        <tr>
            <!-- <td>{{ $u->id }}</td> -->
            <td>{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td><span class="badge bg-info">{{ ucfirst($u->role) }}</span></td>
            <td>{{ $u->number ?? '-' }}</td>
            <td>
                @if(auth()->user()->role !== 'user')
                <a href="{{ route('users.edit', $u->id) }}" class="btn btn-warning btn-sm">
                    Edit
                </a>

                <form action="{{ route('users.destroy', $u->id) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Delete this user?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm">Delete</button>
                </form>
                @else
                -
                @endif
            </td>
        </tr>
        @endif
        @endforeach

    </tbody>
</table>

@endsection