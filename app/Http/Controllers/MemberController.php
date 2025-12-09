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
    public function index(Request $request)
    {
        $group_id = $request->query('group_id');
        $role     = $request->query('role'); // NEW

        $query = Member::with(['user', 'group']);

        // Filter by group
        if ($group_id) {
            $query->where('group_id', $group_id);
        }

        // Filter by role
        if ($role) {
            $query->where('role', $role);
        }

        // Default sorting: leader → sub leader → member → others
        $query->orderByRaw("FIELD(role, 'leader', 'sub_leader', 'member') ASC")
            ->orderBy('name', 'asc');

        $members = $query->paginate(15)->withQueryString();

        return view('members.index', compact('members', 'group_id', 'role'));
    }


    public function create(Request $request)
    {
        $groups = Group::all();
        $selectedGroupId = $request->query('group_id'); // read from URL

        return view('members.create', compact('groups', 'selectedGroupId'));
    }


    // public function store(Request $request)
    // {
    //     $email = $request->name . $request->mobile . '@member.local';

    //     // Create related User
    //     $user = User::create([
    //         'name' => $request->name,
    //         'phone' => $request->mobile,
    //         'email' => $email,
    //         'password' => Hash::make('123456'),
    //         'role' => 'user'
    //     ]);

    //     // Validate only allowed fields
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
    //         'role' => 'required|in:leader,sub_leader,member',
    //     ]);

    //     // Add missing column (user_id) if your Member table has user_id
    //     $data['user_id'] = $user->id;

    //     // Create member only with validated data
    //     $member = Member::create($data);

    //     return redirect()->route('members.create')
    //         ->with('success', 'Member created successfully!');
    // }

    public function store(Request $request)
    {
        // 1. GENERATE THE EMAIL
        $email = $request->name . $request->mobile . '@member.local';

        // 2. VALIDATE INPUTS AND DYNAMICALLY CHECK EMAIL UNIQUENESS
        $rules = [
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'aadhaar_encrypted' => 'required|string',
            'pan_encrypted' => 'required|string',
            'bank_name' => 'nullable|string',
            'account_number' => 'required|string',
            'branch_name' => 'nullable|string',
            'ifsc_code' => 'required|string|max:50',
            'group_id' => 'required|exists:groups,id',
            'role' => 'required|in:leader,sub_leader,member',
        ];

        // Add a temporary rule to check if the GENERATED email is unique in the 'users' table.
        // We pass the email variable as part of the request for validation.
        $request->merge(['generated_email_check' => $email]);
        $rules['generated_email_check'] = 'unique:users,email';

        // Validate the request data
        $data = $request->validate($rules, [
            // Custom message for the uniqueness check
            'generated_email_check.unique' => 'A member with this Name and Mobile number already exists (email generated is not unique).'
        ]);

        // If validation fails, it automatically redirects back to the previous page
        // (members.create) with the input and errors.

        // 3. CREATE RELATED USER
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->mobile,
            'email' => $email, // Use the generated email
            'password' => Hash::make('123456'),
            'role' => 'user'
        ]);

        // 4. CREATE MEMBER
        $data['user_id'] = $user->id;
        // Remove the temporary email check field before creating the Member record
        unset($data['generated_email_check']);

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
            'role' => 'required|in:leader,sub_leader,member',

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
            $email = $data['name'] . $data['mobile'] . '@member.local';

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
                'role'              => $data['role'],
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
            'role' => 'required|in:leader,sub_leader,member',
        ]);

        $member->update($request->all());

        // Also update related user name
        if ($member->user) {
            $member->user->update([
                'name' => $request->name,
            ]);
        }

        return back()->with('success', 'Member updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        $member->delete();
        return back()->with('success', 'Member deleted successfully!');
    }

    // PDF Export
    public function exportPdf()
    {
        $members = Member::with(['group.branch'])->get();

        $logoFile = public_path('images/finvels.png');
        $logoBase64 = file_exists($logoFile) ? base64_encode(file_get_contents($logoFile)) : null;

        $LogoFile = public_path('images/fin.jpeg');
        $LogoBase64 = file_exists($LogoFile) ? base64_encode(file_get_contents($LogoFile)) : null;

        $pdf = Pdf::loadView('exports.members_pdf', compact('members', 'logoBase64', 'LogoBase64'));
        return $pdf->download('members_report.pdf');
    }

    // Excel Export
    public function exportExcel()
    {
        $members = Member::with(['group.branch'])->get();
        return Excel::download(new MembersExport($members), 'members_report.xlsx');
    }
}
