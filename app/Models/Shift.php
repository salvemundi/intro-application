<?php

namespace App\Models;

use App\Http\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Shift extends Model
{
    use HasFactory, UsesUuid;

    protected $table = 'shifts';
    protected $fillable = ['name', 'start_time', 'end_time', 'max_participants', 'shift_cat'];

    public function shiftCategory(): BelongsTo
    {
        return $this->belongsTo(ShiftCategory::class, 'shift_cat');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Participant::class, 'shift_participants', 'shift_id', 'shift_worker');
    }
}

