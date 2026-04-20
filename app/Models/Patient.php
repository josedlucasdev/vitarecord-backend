<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'user_id',
        'name',
        'age',
        'identity_card',
        'occupation',
        'phone',
        'address',
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function baseHistory()
    {
        return $this->hasOne(BaseHistory::class);
    }

    public function evolutions()
    {
        return $this->hasMany(Evolution::class)->orderBy('date', 'desc');
    }

    public function exams()
    {
        return $this->hasMany(ExamsUltrasound::class)->orderBy('date', 'desc');
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class)->orderBy('date', 'desc');
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class);
    }
}
