<?php

namespace App\Imports;

use App\Models\TempOffer;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Throwable;

class OffersImport implements 
    ToModel, 
    WithHeadingRow, 
    WithValidation,
    SkipsOnError,
    SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;
    
    private $rowNumber = 0;

    private $expectedColumns = [
        'client_name', 'lead_title', 'type', 'produit', 'prix', 'quantite'
    ];
    public function model(array $row)
    {
        $this->rowNumber++;
        

        $extraColumns = array_diff(array_keys($row), $this->expectedColumns);
        if (!empty($extraColumns)) {
            throw new \Exception("Colonnes non valides détectées : " . implode(', ', $extraColumns) . " à la ligne {$this->rowNumber}");
        }
        // Validation manuelle supplémentaire
        if (empty($row['client_name'])) {
            throw new \Exception("Le nom du client est requis à la ligne {$this->rowNumber}");
        }

        if (empty($row['lead_title'])) {
            throw new \Exception("Le titre du lead est requis à la ligne {$this->rowNumber}");
        }

        if (empty($row['type'])) {
            throw new \Exception("Le type est requis à la ligne {$this->rowNumber}");
        }

        if ($row['type']!="invoice" && $row['type']!="offers") {
            throw new \Exception("Le type doit etre soit invoice soit offers {$this->rowNumber}");
        }

        if (empty($row['produit'])) {
            throw new \Exception("Le produit est requis à la ligne {$this->rowNumber}");
        }

        // Convertir le prix avec virgule en point décimal
        $prix = $this->convertToNumeric($row['prix']);
        if ($prix === false) {
            throw new \Exception("Le prix doit être un nombre valide à la ligne {$this->rowNumber}");
        }

        // Convertir la quantité en entier
        $quantite = $this->convertToInteger($row['quantite']);
        if ($quantite === false) {
            throw new \Exception("La quantité doit être un nombre entier valide à la ligne {$this->rowNumber}");
        }

        return new TempOffer([
            'client_name' => $row['client_name'],
            'lead_title'  => $row['lead_title'],
            'type'        => $row['type'],
            'produit'     => $row['produit'],
            'prix'        => $prix,
            'quantite'    => $quantite,
            'import_row'   => $this->rowNumber
        ]);
    }

    // public function rules(): array
    // {
    //     return [
    //         'client_name' => 'required|string|max:255',
    //         'lead_title'  => 'required|string|max:255',
    //         'type'        => 'required|string|max:50',
    //         'produit'     => 'required|string|max:100',
    //         'prix'        => [
    //             'required',
    //             function ($attribute, $value, $fail) {
    //                 if ($this->convertToNumeric($value) === false) {
    //                     $fail("Le champ $attribute doit être un nombre valide.");
    //                 }
    //             }
    //         ],
    //         'quantite'    => [
    //             'required',
    //             function ($attribute, $value, $fail) {
    //                 if ($this->convertToInteger($value) === false) {
    //                     $fail("Le champ $attribute doit être un nombre entier valide.");
    //                 }
    //             }
    //         ],
    //     ];
    // }


    // public function rules(): array
    // {
    //     return [
    //         'client_name' => 'required|string|max:255',
    //         'lead_title'  => 'required|string|max:255',
    //         'type'        => 'required|string|max:50',
    //         'produit'     => 'required|string|max:100',
    //         'prix'        => 'required|integer',  // Vérifie que c'est un entier
    //         'quantite'    => 'required|integer',  // Vérifie que c'est un entier
    //     ];
    // }


    public function rules(): array
    {
        return [
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
            'lead_title' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (empty($value)) {
                        $fail("Le titre du lead est requis.");
                    }
                }
            ],
            'type' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    if (empty($value)) {
                        $fail("Le type est requis.");
                    } elseif ($value !== "invoice" && $value !== "offers") {
                        $fail("Le type doit être soit 'invoice' soit 'offers'.");
                    }
                }
            ],
            'produit' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) {
                    if (empty($value)) {
                        $fail("Le produit est requis.");
                    }
                }
            ],
            'prix' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    $prix = $this->convertToNumeric($value);
                    if ($prix === false) {
                        $fail("Le prix doit être un nombre valide.");
                    }
                }
            ],
            'quantite' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $quantite = $this->convertToInteger($value);
                    if ($quantite === false) {
                        $fail("La quantité doit être un nombre entier valide.");
                    }
                }
            ],
        ];
    }



    /**
     * Convertit une chaîne avec virgule en nombre décimal
     */
    private function convertToNumeric($value)
    {
        // Si c'est déjà un nombre, on le retourne directement
        if (is_numeric($value)) {
            return $value;
        }

        // Remplace les virgules par des points et vérifie si c'est numérique
        $value = str_replace(',', '.', str_replace(' ', '', $value));
        return is_numeric($value) ? (float) $value : false;
    }

    /**
     * Convertit une chaîne en entier
     */
    private function convertToInteger($value)
    {
        // Si c'est déjà un entier, on le retourne directement
        if (is_int($value)) {
            return $value;
        }

        // Supprime les espaces et vérifie si c'est un entier valide
        $value = str_replace(' ', '', $value);
        return ctype_digit($value) ? (int) $value : false;
    }

    public function onError(Throwable $e)
    {   
        // return ['error' => true, 'message' => 'Erreur d\'importation: ' . $e->getMessage()];
        Log::error('Erreur lors de l\'import CSV à la ligne ' . $this->rowNumber . ': ' . $e->getMessage());
        
    }

}