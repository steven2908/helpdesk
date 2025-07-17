<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::latest()->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $companies
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:companies,name',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $company = Company::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Perusahaan berhasil ditambahkan.',
            'data' => $company
        ]);
    }

    public function show($id)
    {
        $company = Company::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $company
        ]);
    }

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|unique:companies,name,' . $company->id,
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $company->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Perusahaan berhasil diperbarui.',
            'data' => $company
        ]);
    }

    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();

        return response()->json([
            'success' => true,
            'message' => 'Perusahaan berhasil dihapus.'
        ]);
    }

    public function updateSla(Request $request, $id)
    {
        $company = Company::findOrFail($id);
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

        return response()->json([
            'success' => true,
            'message' => 'SLA perusahaan berhasil diperbarui.',
            'data' => $company
        ]);
    }
}
