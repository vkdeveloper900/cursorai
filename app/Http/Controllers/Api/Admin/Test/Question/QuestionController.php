<?php

namespace App\Http\Controllers\Api\Admin\Test\Question;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    // LIST
    public function index(Request $request)
    {
        $query = Question::with('options', 'section');

        if ($request->section_id) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->difficulty) {
            $query->where('difficulty', $request->difficulty);
        }

        return response()->json([
            'data' => $query->latest()->get()
        ]);
    }

    // CREATE
    public function store(Request $request)
    {
        $data = $request->validate([
            'section_id'    => 'required|exists:sections,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,scale',
            'difficulty'    => 'required|in:easy,medium,hard',
            'options'       => 'required|array|min:2',
        ]);

        $question = Question::create($data);

        foreach ($request->options as $option) {
            QuestionOption::create([
                'question_id' => $question->id,
                'option_text' => $option['option_text'],
                'is_correct'  => $option['is_correct'] ?? null,
                'sequence'    => $option['sequence'],
                'score_value' => $option['score_value'] ?? null,
            ]);
        }

        return response()->json([
            'message' => 'Question created successfully',
            'data' => $question->load('options')
        ], 201);
    }

    // SHOW
    public function show($id)
    {
        return response()->json([
            'data' => Question::with('options')->findOrFail($id)
        ]);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        $data = $request->validate([
            'section_id'    => 'required|exists:sections,id',
            'question_text' => 'required|string',
            'difficulty'    => 'required|in:easy,medium,hard',
            'status'        => 'boolean',
            'options'       => 'required|array|min:2',
        ]);

        $question->update($data);

        // refresh options
        $question->options()->delete();

        foreach ($request->options as $option) {
            QuestionOption::create([
                'question_id' => $question->id,
                'option_text' => $option['option_text'],
                'is_correct'  => $option['is_correct'] ?? null,
                'sequence'    => $option['sequence'],
                'score_value' => $option['score_value'] ?? null,
            ]);
        }

        return response()->json([
            'message' => 'Question updated successfully',
            'data' => $question->load('options')
        ]);
    }

    // DELETE
    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();

        return response()->json([
            'message' => 'Question deleted successfully'
        ]);
    }
}
