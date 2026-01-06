<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index()
    {
        try {
            $users = User::orderBy('username')->paginate(20);
        } catch (\Exception $e) {
            // Fallback if type column doesn't exist
            $users = User::orderBy('username')->paginate(20);
        }
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:45|unique:user,username',
            'password' => 'required|string|min:6|confirmed',
            'type' => 'required|integer|in:1,2',
            'status' => 'required|integer|in:0,1',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user
     * Matches Yii actionUpdate - only updates type and status
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'type' => 'required|integer|in:1,2',
            'status' => 'required|integer|in:0,1',
        ]);

        $user->update($validated);

        return redirect()->route('users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    /**
     * Update user password
     * Matches Yii actionUpdate_pass
     */
    public function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('users.show', $user)
            ->with('success', 'Password updated successfully.');
    }

    /**
     * Toggle user status (AJAX)
     * Matches Yii actionStatus
     */
    public function status($id, Request $request)
    {
        if (!$request->has('status')) {
            return response()->json([
                'status' => 400,
                'message' => 'Status parameter is required'
            ], 400);
        }

        $user = User::findOrFail($id);
        $status = $request->input('status');

        if ($status === 'true' || $status === true || $status === '1' || $status === 1) {
            $user->status = 1;
        } else {
            $user->status = 0;
        }

        $user->save();

        return response()->json([
            'status' => 200,
            'message' => 'Successful.'
        ]);
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
