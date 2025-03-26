<?php

namespace App\Services\Import;

use App\Imports\OffersImport;
use App\Imports\ProjectsImport;
use App\Imports\ProjectTasksImport;
use App\Models\TempOffer;
use App\Models\TempProject;
use App\Models\TempProjectTask;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class ImportService
{
    public function importProjects($file)
    {
        $importProject = new ProjectsImport;

        try {
            $result = Excel::import($importProject, $file);
            
            $errors = $this->handleImportErrors($importProject);
            if (!empty($errors)) {
                return ['error' => true, 'message' => 'Erreurs détectées dans le fichier projets', 'errors' => $errors];
            }

            return ['error' => false, 'data' => TempProject::all(), 'imported_rows' => TempProject::count()];
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'importation des projets: ' . $e->getMessage());
            return ['error' => true, 'message' => 'Erreur fatale lors de l\'import: ' . $e->getMessage()];
        }
    }

    public function importProjectTasks($file)
    {
        $importProjectTask = new ProjectTasksImport;

        try {
            $result = Excel::import($importProjectTask, $file);

            $errors = $this->handleImportErrors($importProjectTask);
            if (!empty($errors)) {
                return ['error' => true, 'message' => 'Erreurs détectées dans le fichier tâches', 'errors' => $errors];
            }

            return ['error' => false, 'data' => TempProjectTask::all(), 'imported_rows' => TempProjectTask::count()];
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'importation des tâches: ' . $e->getMessage());
            return ['error' => true, 'message' => 'Erreur fatale lors de l\'import: ' . $e->getMessage()];
        }
    }

    public function importOffers($file)
    {
        $importOffer = new OffersImport;

        try {
            $result = Excel::import($importOffer, $file);

            $errors = $this->handleImportErrors($importOffer);
            if (!empty($errors)) {
                return ['error' => true, 'message' => 'Erreurs détectées dans le fichier offres', 'errors' => $errors];
            }

            return ['error' => false, 'data' => TempOffer::all(), 'imported_rows' => TempOffer::count()];
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'importation des offres: ' . $e->getMessage());
            return ['error' => true, 'message' => 'Erreur fatale lors de l\'import: ' . $e->getMessage()];
        }
    }

    private function handleImportErrors($importInstance)
    {
        $errors = [];
        if ($importInstance->failures()->isNotEmpty()) {
            foreach ($importInstance->failures() as $failure) {
                $errors[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'values' => $failure->values()
                ];
            }
        }
        return $errors;
    }

    public function clearAllTempData()
    {
        TempProjectTask::truncate();
        TempProject::truncate();
        TempOffer::truncate();
    }
}