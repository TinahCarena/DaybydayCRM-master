<?php

namespace App\Imports;

use App\Models\TempProjectTask;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Throwable;

class ProjectTasksImport implements 
    ToModel, 
    WithHeadingRow, 
    WithValidation,
    SkipsOnError,
    SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;
    
    private $rowNumber = 0;

    public function model(array $row)
    {
        $this->rowNumber++;
        if (empty($row['project_title']) ){
            throw new \Exception("Le titre du projet est requis à la ligne {$this->rowNumber}");
        }
        
        if (empty($row['task_title'])) {
            throw new \Exception("Le nom du taches est requis à la ligne {$this->rowNumber}");
        }
        
        return new TempProjectTask([
            'project_title' => $row['project_title'],
            'task_title'   => $row['task_title'],
            'import_row'   => $this->rowNumber
        ]);
    }

    // public function rules(): array
    // {
    //     return [
    //         'project_title' => 'required|string|max:255',
    //         'task_title' => 'required|string|max:255',
    //     ];
    // }


    public function rules(): array
    {
        return [
            'project_title' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (empty($value)) {
                        $fail("Le titre du projet est requis.");
                    }
                }
            ],
            'task_title' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (empty($value)) {
                        $fail("Le nom de la tâche est requis.");
                    }
                }
            ],
        ];
    }


    public function onError(Throwable $e)
    {
        logger()->error('Erreur lors de l\'import: '.$e->getMessage());
    }
}