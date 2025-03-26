<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempOffersTable extends Migration
{
    public function up()
    {
        Schema::create('temp_offers', function (Blueprint $table) {
            $table->id();
            $table->integer('import_row');
            $table->string('client_name');
            $table->string('lead_title');
            $table->string('type');
            $table->string('produit');
            $table->decimal('prix', 10, 2);
            $table->integer('quantite');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('temp_offers');
    }
}
