<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempProjectTasksTable extends Migration
{
    public function up()
    {
        Schema::create('temp_project_tasks', function (Blueprint $table) {
            $table->id();
            $table->integer('import_row');
            $table->string('project_title');
            $table->string('task_title');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('temp_project_tasks');
    }
}