<?php

namespace App\Http\Controllers\Api\Admin\Meta;

use App\Exports\QuestionSampleExport;
use App\Http\Controllers\Controller;
use App\Imports\QuestionExcelImport;
use App\Services\Import\QuestionExcelImportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class QuestionImportExportController extends Controller
{
    public function downloadSample()
    {
        return Excel::download(
            new QuestionSampleExport,
            'question_import_sample.xlsx'
        );
    }

    public function import(Request $request, QuestionExcelImportService $service)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        // ✅ THIS IS THE FIX
        $rows = Excel::toArray(
            new QuestionExcelImport,
            $request->file('file')
        )[0];

        $result = $service->import($rows);

        return response()->json([
            'message' => 'Import completed',
            'inserted' => $result['inserted'],
            'errors' => $result['errors'],
        ]);
    }
}
