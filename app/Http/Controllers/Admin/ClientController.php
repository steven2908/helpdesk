<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::with('users')->get();
        $filterCompanyId = $request->get('company_id');
        $users = User::with('company')
            ->when($filterCompanyId, fn($q) => $q->where('company_id', $filterCompanyId))
            ->get();

        return view('admin.clients.index', compact('users', 'companies', 'filterCompanyId'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|unique:users,phone',
            'company_id' => 'required|exists:companies,id',
            'password' => 'required|min:6',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company_id' => $request->company_id,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.clients.index')->with('success', 'User berhasil ditambahkan');
    }

    public function create()
    {
        $companies = Company::all();
        $roles = Role::pluck('name');
        return view('admin.clients.create', compact('companies', 'roles'));
    }

    public function edit(User $user)
    {
        $companies = Company::all();
        $roles = Role::pluck('name');
        $userRole = $user->roles->pluck('name')->first();

        return view('admin.clients.edit', compact('user', 'companies', 'roles', 'userRole'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|unique:users,phone,' . $user->id,
            'company_id' => 'nullable|exists:companies,id',
            'role' => 'required|exists:roles,name',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company_id' => $request->company_id,
        ]);

        $user->syncRoles([$request->role]);

        return redirect()->route('admin.clients.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
{
    $user->delete();

    return redirect()->route('admin.clients.index')
        ->with('success', 'User berhasil dihapus.');
}

}
