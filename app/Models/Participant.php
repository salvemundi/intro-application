<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\Roles;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Participant extends Model
{
    use HasFactory, SoftDeletes, Notifiable, UsesUuid;

    protected $keyType = 'string';

    protected $appends = array('haspaid','above18','count_hours_per_shift_category');

    protected $table = 'participants';
    protected $fillable = ['firstName', 'insertion', 'lastName', 'birthday', 'email', 'fontysEmail', 'phoneNumber', 'firstNameParent', 'lastNameParent', 'addressParent', 'phoneNumberParent', 'medicalIssues', 'role', 'checkedIn'];
    public function shifts(): BelongsToMany
    {
        return $this->belongsToMany(Shift::class, 'shift_participants', 'shift_worker', 'shift_id');
    }

    public function CountHoursPerShiftCategory(): Attribute
    {
        // Eager load the related shift categories to avoid N+1 queries
        $shifts = $this->shifts()->with('shiftCategory')->get();

        $shiftCategories = [];

        foreach ($shifts as $shift) {
            $shiftCategory = $shift->shiftCategory;

            if ($shiftCategory) {
                $categoryName = $shiftCategory->name;

                if (isset($shiftCategories[$categoryName])) {
                    $shiftCategories[$categoryName] += $shift->hoursInShift();
                } else {
                    $shiftCategories[$categoryName] = $shift->hoursInShift();
                }
            }
        }
        return Attribute::make(
            get: function () use ($shiftCategories) {
                return $shiftCategories;
            }
        );
    }

    public function displayName(): string
    {
        if($this->insertion != "" || $this->insertion != null){
            $name = ucfirst($this->firstName) . " " . $this->insertion . " " . ucfirst($this->lastName);
        } else {
            $name = ucfirst($this->firstName) . " " . ucfirst($this->lastName);
        }
        return $name;
    }

    public  function getHaspaidAttribute(): bool {
        return $this->hasPaid();
    }

    public function getAbove18Attribute(): bool {
        $age = Carbon::parse($this->birthday)->diff(Carbon::now())->format('%y');
        if($age >= 18) {
            return true;
        } else {
            return false;
        }
    }

    public function verificationToken(): BelongsTo
    {
        return $this->belongsTo(VerificationToken::class,'id','participantId','verify_email');
    }

    public function confirmationToken(): BelongsTo
    {
        return $this->belongsTo(ConfirmationToken::class,'id','participantId','confirm_signup_request');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class,'participantId','id');
    }

    public function getFullName(): string
    {
        if($this->insertion != "" || $this->insertion != null){
            $name = $this->firstName . " " . $this->insertion . " " . $this->lastName;
        } else {
            $name = $this->firstName . " " . $this->lastName;
        }
        return $name;
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class,'participant_id','id');
    }

    public function hasPaid(): bool {
        if($this->role !== Roles::child) {
            return true;
        }

        foreach($this->payments()->get() as $payment) {
            if($payment->paymentStatus === PaymentStatus::paid && !$this->purpleOnly) {
                return true;
            }
        }
        return false;
    }

    public function isVerified():bool {
        foreach($this->verificationToken()->get() as $token) {
            if($token->verified) {
                return true;
            }
        }
        return false;
    }
}
