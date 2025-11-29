<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,manager,user',
            'number' => 'nullable|regex:/^\+?[0-9]{10,15}$/',
        ]);

        $creatorRole = Auth::user()->role;

        // Hierarchy rules:
        if ($creatorRole === 'admin') {
            // admin can create anyone
            if (!in_array($request->role, ['manager', 'user'])) {
                abort(403, 'Admin can only create Manager or User');
            }
        } elseif ($creatorRole === 'manager') {
            // manager can only create user
            if ($request->role !== 'user') {
                abort(403, 'Manager can only create User');
            }
        } else {
            // normal user cannot create
            abort(403, 'User cannot create another account');
        }

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'number'   => $request->number,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role'  => 'required|in:admin,manager,user',
            'number' => 'nullable|regex:/^\+?[0-9]{10,15}$/'
        ]);

        $creatorRole = Auth::user()->role;

        if ($creatorRole === 'admin') {
            if (!in_array($request->role, ['manager', 'user'])) {
                abort(403, 'Admin can only assign Manager or User');
            }
        } elseif ($creatorRole === 'manager') {
            if ($request->role !== 'user') {
                abort(403, 'Manager can only assign User');
            }
        } else {
            abort(403, 'User cannot update accounts');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'number' => $request->number,
        ]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id == Auth::id()) {
            abort(403, 'You cannot delete yourself.');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}
