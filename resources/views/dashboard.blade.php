<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


<!-- <table class="table table-bordered text-center align-middle" style="font-size: 13px;">
        <thead>
            <tr>
                <th rowspan="2">ID</th>
                <th rowspan="2">MEMBER</th>

                //LOAN BALANCES 
                <th colspan="2">LOAN BALANCES</th>

                // DUES 
                <th colspan="2">DUES</th>

                <th rowspan="2">MEMBER ADV</th>

                // LOANS 
                <th colspan="4">LOANS</th>
            </tr>

            <tr>
                <th>LOAN INSTANCE</th>
                <th>TOTAL</th>

                <th>LOAN INSTANCE</th>
                <th>TOTAL</th>

                <th>DUE DISB</th>
                <th>SPOUSE KYC</th>
                <th>PR</th>
                <th>SANCHAY PRODUCT DUE</th>

                {{-- last column --}}
                <th>LP/P/A/L</th>
            </tr>
        </thead>

        <tbody>
            @foreach($loan->repayments as $row)
            <tr>
                <td>{{ $row->id }}</td>
                <td>{{ $loan->member->name }}</td>

                // LOAN BALANCE values 
                <td>{{ $row->loan_instance }}</td>
                <td>{{ number_format($row->balance,2) }}</td>

                <!-- DUES 
                <td>{{ $row->due_instance }}</td>
                <td>{{ number_format($row->due_total,2) }}</td>

                <!-- Member ADV 
                <td>{{ $row->member_adv ?? 0 }}</td>

                <!-- LOANS 
                <td>{{ $row->due_disb ?? '-' }}</td>
                <td>{{ $row->spouse_kyc ?? '-' }}</td>
                <td>{{ $row->pr ?? '-' }}</td>
                <td>{{ $row->sanchay_due ?? '-' }}</td>

                <td>{{ $row->lp_pal ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table> -->