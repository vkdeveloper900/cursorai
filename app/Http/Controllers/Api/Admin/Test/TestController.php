<?php

namespace App\Http\Controllers\Api\Admin\Test;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Test;
use App\Services\Test\QuestionGeneratorService;
use Exception;
use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * List all tests
     */
    public function index()
    {
        return response()->json([
            'data' => Test::latest()->get()
        ]);
    }

    /**
     * Create new test (DRAFT)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'intro' => 'nullable|string',
            'instructions' => 'nullable|string',
            'difficulty' => 'required|in:easy,medium,hard,mixed',
            'total_time' => 'required|integer|min:1',
            'configuration' => 'nullable|array',
        ]);

        $test = Test::create([
            ...$data,
            'status' => 'draft'
        ]);

        return response()->json([
            'message' => 'Test created successfully (Draft)',
            'data' => $test
        ], 201);
    }

    /**
     * Show test details
     */
    public function show($id)
    {
        return response()->json([
            'data' => Test::with('testSections.rules')->findOrFail($id)
        ]);
    }

    /**
     * Update test
     */
    public function update(Request $request, $id)
    {
        $test = Test::findOrFail($id);

        if ($test->status === 'published') {
            return response()->json([
                'message' => 'Published test cannot be edited'
            ], 422);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'intro' => 'nullable|string',
            'instructions' => 'nullable|string',
            'difficulty' => 'required|in:easy,medium,hard,mixed',
            'total_time' => 'required|integer|min:1',
            'configuration' => 'nullable|array',
        ]);

        $test->update($data);

        return response()->json([
            'message' => 'Test updated successfully',
            'data' => $test
        ]);
    }

    /**
     * Publish test
     */
    public function publish($id)
    {
        $test = Test::findOrFail($id);

        if ($test->testSections()->count() === 0) {
            return response()->json([
                'message' => 'Cannot publish test without sections'
            ], 422);
        }

        $test->update(['status' => 'published']);

        return response()->json([
            'message' => 'Test published successfully'
        ]);
    }

    /**
     * Delete test
     */
    public function destroy($id)
    {
        $test = Test::findOrFail($id);

        if ($test->status === 'published') {
            return response()->json([
                'message' => 'Published test cannot be deleted'
            ], 422);
        }

        $test->delete();

        return response()->json([
            'message' => 'Test deleted successfully'
        ]);
    }


    public function debugGenerateQuestions($id, QuestionGeneratorService $service)
    {
        $test = Test::with('testSections.rules', 'testSections.section')
            ->findOrFail($id);

        try {
            $questionIds = $service->generate($test);

            $questions = Question::with('section')
                ->whereIn('id', $questionIds)
                ->get()
                ->groupBy(function ($q) {
                    return $q->section->name . ' | ' . $q->difficulty;
                });

            return response()->json([
                'message' => 'Questions generated successfully',
                'total_questions' => count($questionIds),
                'question_ids' => $questionIds,
                'grouped_preview' => $questions,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Generation failed',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

}
