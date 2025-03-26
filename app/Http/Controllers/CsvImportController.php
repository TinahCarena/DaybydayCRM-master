<?php
namespace App\Http\Controllers;

use App\Services\Csv\CsvImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CsvImportController extends Controller
{
    protected $csvImportService;

    public function __construct(CsvImportService $csvImportService)
    {
        $this->csvImportService = $csvImportService;
    }

    public function index()
    {
        return view('csv.importcsv');
    }

    public function import(Request $request)
    {
        $request->validate([
            'client' => 'nullable|file|mimes:csv,txt',
            'task' => 'nullable|file|mimes:csv,txt',
            'lead' => 'nullable|file|mimes:csv,txt',
        ]);

        $files = [
            'client' => $request->file('client'),
            'task' => $request->file('task'),
            'lead' => $request->file('lead'),
        ];
        $types = ['client', 'task', 'lead'];
        $dtoClasses = [
            'App\DTOs\ClientDTO',
            'App\DTOs\TaskDTO',
            'App\DTOs\LeadDTO',
        ];

        try {
            list($importedCounts, $allErrors) = $this->csvImportService->importCsvFiles($files, $types, $dtoClasses);

            $message = "Imported successfully: {$importedCounts['clients']} clients, {$importedCounts['tasks']} tasks, {$importedCounts['leads']} leads";
            if (!empty($allErrors)) {
                return back()->with('import_errors', $allErrors)->with('error', 'Errors found, no data imported');
            }
            return back()->with('success', $message);
        } catch (Exception $e) {
            Log::error('Error during CSV import: ' . $e->getMessage());
            return back()->with('error', 'Error importing data: ' . $e->getMessage());
        }
    }
}