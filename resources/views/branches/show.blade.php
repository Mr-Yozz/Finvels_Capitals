@extends('layouts.app')

@section('content')
<h2 class="text-primary mb-3">Branch Details</h2>

<div class="card p-4">
    <p><strong class="text-primary">ID:</strong> {{ $branch->id }}</p>
    <p><strong class="text-primary">Name:</strong> {{ $branch->name }}</p>
    <p><strong class="text-primary">Address:</strong>{{ $branch->address }}</p>

    <a href="{{ route('branches.index') }}" class="btn btn-secondary">Back</a>
    <a href="{{ route('branches.edit', $branch->id) }}" class="btn btn-primary">Edit</a>
</div>
@endsection