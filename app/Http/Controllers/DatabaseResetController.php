<?php

namespace App\Http\Controllers;

use App\Services\Database\DatabaseResetService;
use Illuminate\Http\Request;

class DatabaseResetController extends Controller
{
    protected $databaseResetService;

    // Inject the DatabaseResetService into the controller
    public function __construct(DatabaseResetService $databaseResetService)
    {
        $this->databaseResetService = $databaseResetService;
    }

    // Method to call the resetDatabase service
    public function reset()
    {
        // Call the resetDatabase method
        $this->databaseResetService->resetDatabase();

        // Return a response or redirect as needed
        return view('roles.create');
    }
}
