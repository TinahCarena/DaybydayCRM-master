<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempProjectTask extends Model
{
    protected $table = 'temp_project_tasks';
    
    protected $fillable = [
        'import_row',
        'project_title',
        'task_title'
    ];
}
