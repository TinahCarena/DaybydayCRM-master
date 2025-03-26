<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempProject extends Model
{
    protected $table = 'temp_projects';
    
    protected $fillable = [
        'import_row',
        'project_title',
        'client_name'
    ];
}
