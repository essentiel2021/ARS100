<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\Searchable;
use App\Traits\GlobalStatus;
use Kirschbaum\PowerJoins\PowerJoins;
use Illuminate\Database\Eloquent\Model;

class Menage extends Model
{
    use Searchable, GlobalStatus, PowerJoins;

    public function producteur()
    {
        return $this->belongsTo(Producteur::class,);
    }
    public function getAgeAttribute()
    {
        return Carbon::parse($this->dateNaiss)->age;
    }
}
