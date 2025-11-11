@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">Categories</h4>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('account.categories.store') }}" method="POST" class="mb-3">
        @csrf
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="name" class="form-control" placeholder="Category name" required>
            </div>
            <div class="col-md-3">
                <select name="type" class="form-select" required>
                    <option value="expense">Expense</option>
                    <option value="income">Income</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="description" class="form-control" placeholder="Description (optional)">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Add</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Type</th>
                <th>Description</th>
                <th width="100">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $key => $cat)
            <tr>
                <td>{{ $key+1 }}</td>
                <td>{{ $cat->name }}</td>
                <td><span class="badge bg-{{ $cat->type == 'income' ? 'success' : 'danger' }}">{{ ucfirst($cat->type) }}</span></td>
                <td>{{ $cat->description ?? '-' }}</td>
                <td>
                    <form action="{{ route('account.categories.delete', $cat->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Del</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection