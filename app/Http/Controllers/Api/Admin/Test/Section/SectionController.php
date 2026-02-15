<?php

namespace App\Http\Controllers\Api\Admin\Test\Section;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    // LIST
    public function index()
    {
        return response()->json([
            'data' => Section::latest()->get()
        ]);
    }

    // CREATE
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:sections,name',
            'description' => 'nullable|string',
            'status'      => 'boolean'
        ]);

        $section = Section::create($data);

        return response()->json([
            'message' => 'Section created successfully',
            'data' => $section
        ], 201);
    }

    // SHOW
    public function show($id)
    {
        return response()->json([
            'data' => Section::findOrFail($id)
        ]);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $section = Section::findOrFail($id);

        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:sections,name,' . $section->id,
            'description' => 'nullable|string',
            'status'      => 'boolean'
        ]);

        $section->update($data);

        return response()->json([
            'message' => 'Section updated successfully',
            'data' => $section
        ]);
    }

    // DELETE
    public function destroy($id)
    {
        $section = Section::findOrFail($id);

        // Safety check (future proof)
        if ($section->questions()->exists()) {
            return response()->json([
                'message' => 'Section is in use and cannot be deleted'
            ], 422);
        }

        $section->delete();

        return response()->json([
            'message' => 'Section deleted successfully'
        ]);
    }
}
