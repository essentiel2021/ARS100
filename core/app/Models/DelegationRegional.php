<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;
use App\Traits\HasCooperative;
use App\Traits\Searchable;
use Kirschbaum\PowerJoins\PowerJoins;

class DelegationRegional extends Model
{
    use HasFactory, Searchable, GlobalStatus, PowerJoins;
    protected $table = 'delegationregionales';
}
