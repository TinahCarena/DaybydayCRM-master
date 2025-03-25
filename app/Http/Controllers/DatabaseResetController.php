<?php

namespace App\Http\Controllers;

use App\Services\table\TableResetter;
use Illuminate\Http\Request;

class DatabaseResetController extends Controller
{
    public function resetSpecificTables()
    {
        $tableOrder = [
            'notifications',
            
            'payments', 'invoice_lines', 'appointments', 'comments', 'documents', 'mails', 'absences',
            
            'tasks', 'projects', 'leads','payments','invoice_lines', 'invoices', 'offers',
            
            'clients', 'contacts', 'products'
        ];

        $resetter = new TableResetter($tableOrder);
        $resetter->resetTableExcept('department_user', 'user_id', 1);
        $resetter->resetTableExcept('departments', 'name', 'Management');
        $resetter->resetTableExcept('users', 'id', 1);
        $results = $resetter->resetTables();


        // Au lieu de retourner du JSON, retourner une vue
        return view('csv.import');
    }
}