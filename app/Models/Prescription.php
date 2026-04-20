<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'evolution_id',
        'date',
        'medications',
        'indications',
        'attachment_path',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function evolution()
    {
        return $this->belongsTo(Evolution::class);
    }
}
