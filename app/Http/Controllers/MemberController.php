<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Group;
use App\Models\Branch;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\MembersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Services\OtpService;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = Member::with('group.branch')->paginate(15);
        return view('members.index', compact('members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::all();
        $groups = Group::all();
        return view('members.create', compact('branches', 'groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {

    //     $email = $request->name . $request->mobile . '@member.local'; // add default mail to create

    //     $user = User::create([
    //         'name' => $request->name,
    //         'phone' => $request->mobile,
    //         'email' => $email,
    //         'password' => Hash::make('123456'), // Default Password
    //         'role' => 'user'
    //     ]);

    //     $data = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'mobile' => 'required|string|max:20',
    //         'aadhaar_encrypted' => 'required|string',
    //         'pan_encrypted' => 'required|string',
    //         'bank_name' => 'nullable|string',
    //         'account_number' => 'required|string',
    //         'branch_name' => 'nullable|string',
    //         'ifsc_code' => 'required|string|max:50',
    //         'group_id' => 'required|exists:groups,id',
    //     ]);

    //     // manually add user id
    //     // $data['user_id'] = $user->id;


    //     $member = Member::create($request->all());

    //     // OPTIONAL: Link user_id in Member table if column exists
    //     if ($member->fillable && in_array('user_id', $member->getFillable())) {
    //         $member->update(['user_id' => $user->id]);
    //     }



    //     // return redirect()->route('members.index')->with('success', 'Member created successfully!');
    //     return redirect()->route('members.create')
    //         ->with('success', 'Member created successfully!');
    // }

    public function store(Request $request)
    {
        $email = $request->name . $request->mobile . '@member.local';

        // Create related User
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->mobile,
            'email' => $email,
            'password' => Hash::make('123456'),
            'role' => 'user'
        ]);

        // Validate only allowed fields
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'aadhaar_encrypted' => 'required|string',
            'pan_encrypted' => 'required|string',
            'bank_name' => 'nullable|string',
            'account_number' => 'required|string',
            'branch_name' => 'nullable|string',
            'ifsc_code' => 'required|string|max:50',
            'group_id' => 'required|exists:groups,id',
        ]);

        // Add missing column (user_id) if your Member table has user_id
        $data['user_id'] = $user->id;

        // Create member only with validated data
        $member = Member::create($data);

        return redirect()->route('members.create')
            ->with('success', 'Member created successfully!');
    }


    public function sendOtp(Request $request, OtpService $otpService)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'aadhaar_encrypted' => 'required|string',
            'pan_encrypted' => 'required|string',
            'bank_name' => 'nullable|string',
            'account_number' => 'required|string',
            'branch_id' => 'required|exists:branches,id',
            'ifsc_code' => 'required|string|max:50',
            'group_id' => 'required|exists:groups,id',
        ]);

        // Generate OTP
        $otp = rand(100000, 999999);

        // Store form data + OTP in session
        session([
            'form_data' => $request->all(),
            'otp' => $otp
        ]);

        // Send OTP
        $otpService->sendOtp($request->mobile, $otp);

        return redirect()->back()->with('otp_sent', true);
    }

    // Step 3 → Verify OTP & Create Member
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required']);

        if ($request->otp != session('otp')) {
            return back()->with('error', 'Invalid OTP');
        }

        $data = session('form_data');

        DB::beginTransaction();

        try {

            // Create user first
            $email = $data['mobile'] . '@member.local';

            $user = User::create([
                'name'      => $data['name'],
                'phone'     => $data['mobile'],
                'email'     => $email,
                'password'  => Hash::make('123456'),
                'role'      => 'user',
                'branch_id' => $data['branch_id'],
            ]);

            // Create member
            $member = Member::create([
                'name'              => $data['name'],
                'mobile'            => $data['mobile'],
                'aadhaar_encrypted' => $data['aadhaar_encrypted'],
                'pan_encrypted'     => $data['pan_encrypted'],
                'bank_name'         => $data['bank_name'],
                'account_number'    => $data['account_number'],
                'branch_id'         => $data['branch_id'],
                'ifsc_code'         => $data['ifsc_code'],
                'group_id'          => $data['group_id'],
                'user_id'           => $user->id,   // LINK HERE
            ]);

            DB::commit();

            session()->forget(['otp', 'form_data']);

            return redirect()->route('members.create')
                ->with('success', 'Member created successfully!');
        } catch (\Exception $e) {

            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function resendOtp(Request $request, OtpService $otpService)
    {
        $mobile = session('member_form_data.mobile');

        if (!$mobile) {
            return back()->with('error', 'Session expired! Please fill details again.');
        }

        // Prevent spam - allow resend every 30 seconds
        if (session('otp_last_sent') && now()->diffInSeconds(session('otp_last_sent')) < 30) {
            $wait = 30 - now()->diffInSeconds(session('otp_last_sent'));
            return back()->with('error', "Please wait $wait seconds before resending OTP.");
        }

        $otp = rand(100000, 999999);

        session([
            'otp' => $otp,
            'otp_last_sent' => now()
        ]);

        // Send OTP again
        $otpService->sendOtp($mobile, $otp);

        return back()->with('success', 'OTP resent successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        $member->load('group.branch', 'loans'); // eager load group->branch & loans
        return view('members.show', compact('member'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        $branches = Branch::all();
        $groups = Group::all();
        return view('members.edit', compact('member', 'branches', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Member $member)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'aadhaar_encrypted' => 'required|string',
            'pan_encrypted' => 'required|string',
            'bank_name' => 'nullable|string',
            'account_number' => 'required|string|max:25',
            'branch_name' => 'nullable|string',
            'ifsc_code' => 'required|string|max:50',
            'group_id' => 'required|exists:groups,id',
        ]);

        $member->update($request->all());

        // Also update related user name
        if ($member->user) {
            $member->user->update([
                'name' => $request->name,
            ]);
        }

        return redirect()->route('members.index')->with('success', 'Member updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        $member->delete();
        return redirect()->route('members.index')->with('success', 'Member deleted successfully!');
    }

    // PDF Export
    public function exportPdf()
    {
        $members = Member::with(['group.branch'])->get();
        $pdf = Pdf::loadView('exports.members_pdf', compact('members'));
        return $pdf->download('members_report.pdf');
    }

    // Excel Export
    public function exportExcel()
    {
        $members = Member::with(['group.branch'])->get();
        return Excel::download(new MembersExport($members), 'members_report.xlsx');
    }
}
