<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evolution extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'date',
        'reason_for_consultation',
        'current_illness',
        'weight',
        'height',
        'fetal_heart_rate',
        'uterine_height',
        'vital_signs',
        'physical_exam',
        'breast_exam',
        'abdominal_exam',
        'gynecological_exam_external',
        'gynecological_exam_speculum',
        'gynecological_exam_bimanual',
        'diagnostic_impression',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function exams()
    {
        return $this->hasMany(ExamsUltrasound::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
}
