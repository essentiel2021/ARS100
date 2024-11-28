<?php

namespace App\Models;

use App\Scopes\ActiveScope;
use App\Traits\HasCooperative;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\EmployeeTeam
 *
 * @property int $id
 * @property int $team_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $icon
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeTeam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeTeam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeTeam query()
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeTeam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeTeam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeTeam whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeTeam whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeTeam whereUserId($value)
 * @property int|null $cooperative_id
 * @property-read \App\Models\Cooperative|null $cooperative
 * @method static \Illuminate\Database\Eloquent\Builder|EmployeeTeam whereCooperativeId($value)
 * @mixin \Eloquent
 */
class EmployeeTeam extends BaseModel
{

    use HasCooperative;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScope(ActiveScope::class);
    }

}
