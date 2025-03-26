<?php

namespace App\Services\Reset;

use Illuminate\Support\Facades\DB;

class ResetTableService
{
    /**
     * Réinitialise les tables spécifiées en les vidant (TRUNCATE).
     */
    public function resetTables(): void
    {
        // Désactiver temporairement les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        // Liste des tables à vider
        $tablesToReset = [
            'leads',
            'comments',
            'mails',
            'tasks',
            'projects',
            'absences',
            'contacts',
            'invoice_lines',
            'appointments',
            'payments',
            'invoices',
            'offers',
            'clients'
        ];
        


        // Exécuter le TRUNCATE sur chaque table
        foreach ($tablesToReset as $table) {
            DB::table($table)->truncate();
        }

        // Réactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
