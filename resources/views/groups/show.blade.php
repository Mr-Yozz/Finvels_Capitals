@extends('layouts.app')

@section('content')
<h2 class="text-primary mb-3">Group Details</h2>

<div class="card p-4">
    <p><strong class="text-primary">ID:</strong> {{ $group->id }}</p>
    <p><strong class="text-primary">Name:</strong> {{ $group->name }}</p>
    <p><strong class="text-primary">Branch:</strong> {{ $group->branch->name ?? '-' }}</p>

    <a href="{{ route('groups.index') }}" class="btn btn-secondary">Back</a>
    <a href="{{ route('groups.edit', $group->id) }}" class="btn btn-primary">Edit</a>
</div>
@endsection