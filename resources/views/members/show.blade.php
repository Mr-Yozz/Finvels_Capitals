@extends('layouts.app')

@section('content')
<h2 class="text-primary mb-3">Member Details</h2>

<div class="card p-4">
    <p><strong class="text-primary">ID:</strong> {{ $member->id }}</p>
    <p><strong class="text-primary">Name:</strong> {{ $member->name }}</p>
    <p><strong class="text-primary">Mobile:</strong> {{ $member->mobile }}</p>
    <p><strong class="text-primary">Aadhaar:</strong> {{ $member->aadhaar_encrypted }}</p>
    <p><strong class="text-primary">PAN:</strong> {{ $member->pan_encrypted }}</p>
    <p><strong class="text-primary">Group:</strong> {{ $member->group->name ?? '-' }}</p>
    <p><strong class="text-primary">Branch:</strong> {{ $member->group->branch->name ?? '-' }}</p>

    <a href="{{ route('members.index') }}" class="btn btn-secondary">Back</a>
    <a href="{{ route('members.edit', $member->id) }}" class="btn btn-primary">Edit</a>
</div>
@endsection
