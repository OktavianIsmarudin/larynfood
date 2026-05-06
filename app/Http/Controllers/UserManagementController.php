<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    /**
     * Display a listing of all users (admin and customer)
     */
    public function index()
    {
        $users = User::whereIn('role', ['super_admin', 'admin', 'customer'])
            ->orderBy('role', 'asc') // super_admin first, then admin, then customer
            ->orderBy('created_at', 'desc')
            ->get();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new admin user
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user (admin or customer only, no super_admin)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:admin,customer'], // Super admin tidak bisa ditambahkan
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dibuat!');
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        // Pastikan yang diedit adalah admin atau customer (bukan super admin)
        if (!in_array($user->role, ['admin', 'customer'])) {
            return redirect()->route('users.index')
                ->with('error', 'Super Admin tidak dapat diedit!');
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user (admin or customer only)
     */
    public function update(Request $request, User $user)
    {
        // Pastikan yang diedit adalah admin atau customer (bukan super admin)
        if (!in_array($user->role, ['admin', 'customer'])) {
            return redirect()->route('users.index')
                ->with('error', 'Super Admin tidak dapat diupdate!');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,customer'], // Super admin tidak bisa diset
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diupdate!');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Super admin tidak bisa dihapus
        if ($user->role === 'super_admin') {
            return redirect()->route('users.index')
                ->with('error', 'Super Admin tidak dapat dihapus!');
        }

        // Tidak bisa menghapus diri sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus!');
    }
}
