<?php

namespace App\Http\Controllers;

use App\Models\CaseLock;
use Illuminate\Http\Request;

class CaseLockController extends Controller
{
    public function index()
    {
        if (auth()->user()->hasRole('admin')) {
            // Admin melihat semua case lock yang dibuat oleh staff
            $cases = CaseLock::with('user')
                ->whereHas('user', function ($query) {
                    $query->role('staff'); // hanya staff
                })
                ->latest()
                ->paginate(10);

            return view('admin.case_locks.index', compact('cases'));
        }

        // Staff hanya melihat case lock milik sendiri
        $cases = CaseLock::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('staff.case_locks.index', compact('cases'));
    }

    public function create()
    {
        // Admin tidak boleh membuat case lock
        if (auth()->user()->hasRole('admin')) {
            abort(403, 'Admin tidak boleh membuat Case Lock');
        }

        return view('staff.case_locks.create');
    }

    public function store(Request $request)
    {
        // Admin tidak boleh membuat case lock
        if (auth()->user()->hasRole('admin')) {
            abort(403, 'Admin tidak boleh membuat Case Lock');
        }

        $request->validate([
            'date' => 'required|date',
            'technician_name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'reason' => 'required|string',
            'impact' => 'required|string',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();

        $data['date'] = \Carbon\Carbon::parse($request->date);

        // simpan file gambar
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('case_locks', 'public');
        }

        CaseLock::create($data);

        return redirect()->route('staff.case_locks.index')
            ->with('success', 'Case Lock berhasil dibuat');
    }

    public function show(CaseLock $caseLock)
    {
        // Admin bisa melihat semua, staff hanya melihat milik sendiri
        if (auth()->user()->hasRole('staff') && $caseLock->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses');
        }

        if (auth()->user()->hasRole('admin')) {
            return view('admin.case_locks.show', compact('caseLock'));
        }

        return view('staff.case_locks.show', compact('caseLock'));
    }

    public function edit(CaseLock $caseLock)
    {
        // Admin tidak boleh mengedit
        if (auth()->user()->hasRole('admin')) {
            abort(403, 'Admin tidak boleh mengedit Case Lock');
        }

        // Staff hanya bisa edit miliknya
        if ($caseLock->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses');
        }

        return view('staff.case_locks.edit', compact('caseLock'));
    }

    public function update(Request $request, CaseLock $caseLock)
    {
        // Admin tidak boleh update
        if (auth()->user()->hasRole('admin')) {
            abort(403, 'Admin tidak boleh mengedit Case Lock');
        }

        // Staff hanya bisa update miliknya
        if ($caseLock->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses');
        }

        $request->validate([
            'date' => 'required|date',
            'technician_name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'reason' => 'required|string',
            'impact' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $caseLock->update($request->all());

        return redirect()->route('staff.case_locks.index')
            ->with('success', 'Case Lock berhasil diperbarui');
    }

    public function destroy(CaseLock $caseLock)
    {
        // Admin tidak boleh hapus
        if (auth()->user()->hasRole('admin')) {
            abort(403, 'Admin tidak boleh menghapus Case Lock');
        }

        // Staff hanya bisa hapus miliknya
        if ($caseLock->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses');
        }

        $caseLock->delete();

        return redirect()->route('staff.case_locks.index')
            ->with('success', 'Case Lock berhasil dihapus');       
    }
}
