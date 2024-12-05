<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;
use App\Traits\HasCooperative;
use App\Traits\Searchable;

class SousPrefecture extends Model
{
    use HasFactory;
    protected $table = 'sousprefectures';

    public function region(){
        return $this->belongsTo(Region::class,'region_id');
    }
}
