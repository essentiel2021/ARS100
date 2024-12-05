<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;
use App\Traits\HasCooperative;
use App\Traits\Searchable;
use Kirschbaum\PowerJoins\PowerJoins;

class Region extends Model
{
    use HasFactory,Searchable, GlobalStatus, PowerJoins;

    protected $table = 'regions';

    public function delegation(){
        return $this->belongsTo(DelegationRegional::class,'delegationRegionale_id');
    }
}
