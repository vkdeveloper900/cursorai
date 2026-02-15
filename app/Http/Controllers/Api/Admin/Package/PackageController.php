<?php

namespace App\Http\Controllers\Api\Admin\Package;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    // LIST
    public function index()
    {
        return response()->json([
            'data' => Package::with('tests:id,title')->latest()->get()
        ]);
    }

    // CREATE
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'validity_days' => 'nullable|integer|min:1',
            'attempt_limit' => 'nullable|integer|min:1',
            'status' => 'boolean',
            'test_ids' => 'required|array|min:1',
            'test_ids.*' => 'exists:tests,id',
        ]);

        $package = Package::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'validity_days' => $data['validity_days'] ?? null,
            'attempt_limit' => $data['attempt_limit'] ?? null,
            'status' => $data['status'] ?? 1,
        ]);

        $package->tests()->sync($data['test_ids']);

        return response()->json([
            'message' => 'Package created successfully',
            'data' => $package->load('tests')
        ], 201);
    }

    // SHOW
    public function show($id)
    {
        return response()->json([
            'data' => Package::with('tests')->findOrFail($id)
        ]);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $package = Package::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'validity_days' => 'nullable|integer|min:1',
            'attempt_limit' => 'nullable|integer|min:1',
            'status' => 'boolean',
            'test_ids' => 'nullable|array',
            'test_ids.*' => 'exists:tests,id',
        ]);

        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $package->update($data);

        if (isset($data['test_ids'])) {
            $package->tests()->sync($data['test_ids']);
        }

        return response()->json([
            'message' => 'Package updated successfully',
            'data' => $package->load('tests')
        ]);
    }

    // DELETE
    public function destroy($id)
    {
        Package::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Package deleted successfully'
        ]);
    }
}
