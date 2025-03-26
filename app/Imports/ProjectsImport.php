<?php

namespace App\Imports;

use App\Models\TempProject;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Throwable;

class ProjectsImport implements 
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
        
        // Validation manuelle supplémentaire
        if (empty($row['project_title']) ){
            throw new \Exception("Le titre du projet est requis à la ligne {$this->rowNumber}");
        }
        
        if (empty($row['client_name'])) {
            throw new \Exception("Le nom du client est requis à la ligne {$this->rowNumber}");
        }
        
        return new TempProject([
            'project_title' => $row['project_title'],
            'client_name'  => $row['client_name'],
            'import_row'   => $this->rowNumber
        ]);
    }

    // public function rules(): array
    // {
    //     return [
    //         'project_title' => 'required|string|max:255',
    //         'client_name' => 'required|string|max:255',
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
            'client_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (empty($value)) {
                        $fail("Le nom du client est requis.");
                    }
                }
            ],
        ];
    }


    public function onError(Throwable $e)
    {
        
        Log::error('Erreur lors de l\'import CSV: '.$e->getMessage());
    }
}
