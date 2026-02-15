<?php

namespace App\Http\Controllers\Api\Admin\Meta;

use App\Http\Controllers\Controller;

class MetaController extends Controller
{
    public function difficulties()
    {
        return response()->json([
            'data' => config('constants.difficulties'),
        ]);
    }

    public function questionTypes()
    {
        return response()->json([
            'data' => config('constants.question_types')
        ]);
    }

    public function testStatuses()
    {
        return response()->json([
            'data' => config('constants.test_statuses')
        ]);
    }

    public function yesNo()
    {
        return response()->json([
            'data' => config('constants.yes_no')
        ]);
    }
}
