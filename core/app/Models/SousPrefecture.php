<?php

namespace App\Models;

use App\Traits\Searchable;
use App\Traits\GlobalStatus;
use App\Traits\HasCooperative;
use Kirschbaum\PowerJoins\PowerJoins;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SousPrefecture extends Model
{
    use HasFactory,Searchable, GlobalStatus, PowerJoins;
    protected $table = 'sousprefectures';

    public function region(){
        return $this->belongsTo(Region::class,'region_id');
    }
}
