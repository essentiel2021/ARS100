<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class Agroevaluation extends Model
{
    use Searchable, GlobalStatus, PowerJoins;

    public function campagne()
    {
        return $this->belongsTo(Campagne::class);
    }

    public function producteur()
    {
        return $this->belongsTo(Producteur::class);
    }
}