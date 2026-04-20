<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'from_doctor_id',
        'to_doctor_id',
        'status',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function fromDoctor()
    {
        return $this->belongsTo(User::class, 'from_doctor_id');
    }

    public function toDoctor()
    {
        return $this->belongsTo(User::class, 'to_doctor_id');
    }
}
