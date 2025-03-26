<?php

namespace App\Http\Controllers;

use App\Services\Reset\ResetTableService;
use Illuminate\Http\JsonResponse;

class ResetTableController extends Controller
{
    protected $resetTableService;

    public function __construct(ResetTableService $resetTableService)
    {
        $this->resetTableService = $resetTableService;
    }

    public function resetTables(): JsonResponse
    {
        $this->resetTableService->resetTables();

        return response()->json([
            'message' => 'Les tables sélectionnées ont été vidées avec succès.'
        ]);
    }
}
