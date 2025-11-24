<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Mobile</th>
            <th>Aadhaar</th>
            <th>PAN</th>
            <th>Group</th>
            <th>Branch</th>
            <th>Bank Name</th>
            <th>Account No</th>
            <th>IFSC Code</th>
        </tr>
    </thead>
    <tbody>
        @foreach($members as $key => $member)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $member->name }}</td>
            <td>{{ $member->mobile }}</td>
            <td>{{ $member->aadhaar_encrypted }}</td>
            <td>{{ $member->pan_encrypted }}</td>
            <td>{{ $member->group->name ?? '-' }}</td>
            <td>{{ $member->group->branch->name ?? '-' }}</td>
            <td>{{ $member->bank_name ?? '-' }}</td>
            <td>{{ $member->account_number ?? '-' }}</td>
            <td>{{ $member->ifsc_code ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>