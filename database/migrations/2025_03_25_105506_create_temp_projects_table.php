<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_projects', function (Blueprint $table) {
            $table->id();
            $table->integer('import_row');
            $table->string('project_title');
            $table->string('client_name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('temp_projects');
    }
}


