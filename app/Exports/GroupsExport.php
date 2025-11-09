<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class GroupsExport implements FromView
{
    protected $groups;

    public function __construct($groups)
    {
        $this->groups = $groups;
    }

    public function view(): View
    {
        return view('exports.groups_excel', [
            'groups' => $this->groups
        ]);
    }
}
