<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Branch Name</th>
            <th>Address</th>
            <th>Total Groups</th>
            <th>Total Loans</th>
            <th>Total Users</th>
        </tr>
    </thead>
    <tbody>
        @foreach($branches as $key => $branch)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $branch->name }}</td>
            <td>{{ $branch->address }}</td>
            <td>{{ $branch->groups_count }}</td>
            <td>{{ $branch->loans_count }}</td>
            <td>{{ $branch->users_count }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
