@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">👥 User Management</h4>

        @if(in_array(Auth::user()->role, ['admin', 'manager']))
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add User
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive shadow-sm rounded-3 bg-white p-3">
        <table class="table table-hover align-middle text-nowrap">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created By</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge 
                                @if($user->role=='admin') bg-danger 
                                @elseif($user->role=='manager') bg-warning text-dark
                                @else bg-secondary @endif">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>{{ $user->creator->name ?? '—' }}</td>
                        <td class="text-center">
                            @if(Auth::user()->role == 'admin' || Auth::user()->role == 'manager')
                                <a href="#" class="btn btn-sm btn-outline-primary disabled">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(Auth::user()->role == 'admin')
                                    <form action="#" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger disabled">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $users->links() }}
    </div>
</div>
@endsection
