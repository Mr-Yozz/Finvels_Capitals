<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Group Name</th>
            <th>Branch</th>
        </tr>
    </thead>
    <tbody>
        @foreach($groups as $key => $group)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $group->name }}</td>
            <td>{{ $group->branch->name ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
