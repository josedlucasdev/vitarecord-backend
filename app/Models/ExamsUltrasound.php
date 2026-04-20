<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamsUltrasound extends Model
{
    use HasFactory;
    
    // Explicitly defining the table name since it doesn't follow standard pluralization perfectly
    protected $table = 'exams_ultrasounds';

    protected $fillable = [
        'patient_id',
        'evolution_id',
        'date',
        'exam_type',
        'findings_uterus',
        'findings_ovaries',
        'findings_cul_de_sac',
        'general_findings',
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
