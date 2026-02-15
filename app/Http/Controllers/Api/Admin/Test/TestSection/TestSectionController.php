<?php

namespace App\Http\Controllers\Api\Admin\Test\TestSection;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestSection;
use App\Models\TestSectionRule;
use Illuminate\Http\Request;

class TestSectionController extends Controller
{
    /**
     * List all sections of a test
     */
    public function index($testId)
    {
        $test = Test::findOrFail($testId);

        return response()->json([
            'data' => $test->testSections()->with('section', 'rules')->get()
        ]);
    }

    /**
     * Add section to a test
     */
    public function store(Request $request, $testId)
    {
        $test = Test::findOrFail($testId);

        if ($test->status === 'published') {
            return response()->json([
                'message' => 'Cannot modify a published test'
            ], 422);
        }

        $data = $request->validate([
            'section_id'          => 'required|exists:sections,id',
            'total_questions'     => 'required|integer|min:1',
            'marks_per_question'  => 'required|integer|min:1',
            'section_time'        => 'nullable|integer|min:1',
            'rules'               => 'required|array|min:1',
            'rules.*.difficulty'  => 'required|in:easy,medium,hard',
            'rules.*.count'       => 'required|integer|min:0',
        ]);

        // Validate difficulty count sum
        if (collect($data['rules'])->sum('count') !== $data['total_questions']) {
            return response()->json([
                'message' => 'Sum of difficulty counts must match total questions'
            ], 422);
        }

        // Create test section
        $testSection = TestSection::create([
            'test_id'            => $test->id,
            'section_id'         => $data['section_id'],
            'total_questions'    => $data['total_questions'],
            'marks_per_question' => $data['marks_per_question'],
            'section_time'       => $data['section_time'] ?? null,
            'sequence'           => $test->testSections()->count() + 1,
        ]);

        // Save rules
        foreach ($data['rules'] as $rule) {
            TestSectionRule::create([
                'test_section_id' => $testSection->id,
                'difficulty'      => $rule['difficulty'],
                'question_count'  => $rule['count'],
            ]);
        }

        return response()->json([
            'message' => 'Section added to test successfully',
            'data' => $testSection->load('section', 'rules')
        ], 201);
    }

    /**
     * Remove section from test
     */
    public function destroy($testId, $id)
    {
        $test = Test::findOrFail($testId);

        if ($test->status === 'published') {
            return response()->json([
                'message' => 'Cannot modify a published test'
            ], 422);
        }

        $testSection = TestSection::where('test_id', $testId)
            ->where('id', $id)
            ->firstOrFail();

        $testSection->delete();

        return response()->json([
            'message' => 'Section removed from test'
        ]);
    }
}
