<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TempOffer extends Model
{
    protected $table = 'temp_offers';

    protected $fillable = ['import_row','client_name', 'lead_title', 'type', 'produit', 'prix', 'quantite'];
}
