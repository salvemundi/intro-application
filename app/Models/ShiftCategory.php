<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShiftCategory extends Model
{
    use HasFactory;

    protected $table = 'shift_categories';

    protected $fillable = ['name', 'shift_leader'];

    public function shiftLeader(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'shift_leader');
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class, 'shift_cat');
    }
}
