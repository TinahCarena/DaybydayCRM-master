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
            'business_hours',
            'industries',
            'departments',
            'department_user',
            'permission_role',
            'settings',
            'statuses',
            'permissions',
            'role_user',
            'roles',
            'users'
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