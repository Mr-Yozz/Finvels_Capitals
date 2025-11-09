<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MembersExport implements FromView
{
    protected $members;

    public function __construct($members)
    {
        $this->members = $members;
    }

    public function view(): View
    {
        return view('exports.members_excel', [
            'members' => $this->members
        ]);
    }
}
