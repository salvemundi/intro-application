<?php

namespace App\Models;

use App\Http\Traits\UsesUuid;
use Carbon\Carbon;
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

    public function hoursInShift(): int
    {
        // Convert the start_time and end_time to Carbon instances
        $startTime = Carbon::parse($this->start_time);
        $endTime = Carbon::parse($this->end_time);

        // Calculate the difference in hours
        return $startTime->diffInHours($endTime);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Participant::class, 'shift_participants', 'shift_id', 'shift_worker');
    }
}

