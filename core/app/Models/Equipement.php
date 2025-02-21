<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Kirschbaum\PowerJoins\PowerJoins; 

class Equipement extends Model
{
    use HasFactory,GlobalStatus, PowerJoins;
    protected $table = 'equipements';

    public function producteur(){
        return $this->belongsTo(Producteur::class,'producteur_id');
    }
}
