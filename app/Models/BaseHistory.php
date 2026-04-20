<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'menarche',
        'menstrual_cycle',
        'last_menstruation_date',
        'sexarche',
        'sexual_partners',
        'contraceptive_method',
        'pregnancies_gpcav',
        'last_pap_smear',
        'last_mammography',
        'personal_pathological',
        'surgical',
        'allergies',
        'habits',
        'family_history',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
