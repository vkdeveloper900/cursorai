<?php

namespace App\Http\Controllers\Api\User\Test;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Services\User\Test\TestQuestionService;
use Illuminate\Http\JsonResponse;

class TestQuestionController extends Controller
{
    public function startTest(
        $testId,
        TestQuestionService $service
    ): JsonResponse {

        $test = Test::with('testSections.rules')->findOrFail($testId);

        $questions = $service->getQuestionsForUser($test);

        return response()->json([
            'message' => 'Test started successfully',
            'test' => [
                'id' => $test->id,
                'title' => $test->title,
                'total_time' => $test->total_time,
            ],
            'total_questions' => count($questions),
            'questions' => $questions,
        ]);
    }
}
