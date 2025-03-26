<?php


namespace App\Services\Database;

use Illuminate\Support\Facades\DB;

class DatabaseResetService
{
    public function resetDatabase()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $tables = DB::select('SHOW TABLES');
        $databaseName = env('DB_DATABASE');
        $key = "Tables_in_$databaseName";

 
        $excludedTables = [
            'notifications',
            
            'payments', 'invoice_lines', 'appointments', 'comments', 'documents', 'mails', 'absences',
            
            'tasks', 'projects', 'leads','payments','invoice_lines', 'invoices', 'offers',
            
            'clients', 'contacts', 'products', 'users'
        ];

        foreach ($tables as $table) {
            $tableName = $table->$key;
            
            
            if (!in_array($tableName, $excludedTables)) {
                DB::table($tableName)->truncate();
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}