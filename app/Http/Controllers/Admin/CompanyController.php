<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    // Menampilkan daftar perusahaan
    public function index()
    {
        $companies = Company::latest()->paginate(10);
        return view('admin.companies.index', compact('companies'));
    }

    // Menampilkan form tambah perusahaan
    public function create()
    {
        return view('admin.companies.create');
    }

    // Menyimpan perusahaan baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:companies,name',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        Company::create($validated);

        return redirect()->route('admin.companies.index')->with('success', 'Perusahaan berhasil ditambahkan.');
    }

    // Menampilkan form edit perusahaan
    public function edit(Company $company)
    {
        return view('admin.companies.edit', compact('company'));
    }

    // Memperbarui data perusahaan
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:companies,name,' . $company->id,
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $company->update($validated);

        return redirect()->route('admin.companies.index')->with('success', 'Perusahaan berhasil diperbarui.');
    }

    // Menghapus perusahaan
    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('admin.companies.index')->with('success', 'Perusahaan berhasil dihapus.');
    }

    public function updateSla(Request $request, Company $company)
{
    $useSla = $request->has('use_sla');

    if ($useSla) {
        $request->validate([
            'sla_resolution_time' => 'required|integer|min:1',
            'sla_response_time' => 'required|integer|min:1',
        ]);

        $company->sla_response_time = $request->sla_response_time;
        $company->sla_resolution_time = $request->sla_resolution_time;
    } else {
        $company->sla_response_time = null;
        $company->sla_resolution_time = null;
    }

    $company->save();

    return redirect()->back()->with('success', 'SLA perusahaan berhasil diperbarui.');
}


}
