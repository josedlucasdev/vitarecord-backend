<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Patient;
use App\Models\Evolution;

$patientId = 1;
$doctorId = 1;
$date = '2026-04-20';

\DB::transaction(function () use ($patientId, $doctorId, $date) {
    // 1. Create Evolution
    $evolution = Evolution::create([
        'patient_id' => $patientId,
        'doctor_id' => $doctorId,
        'date' => $date,
        'reason_for_consultation' => 'Control Prenatal - Semana 32',
        'current_illness' => '<p>Paciente refiere lumbalgia leve y edema en miembros inferiores al final del día. No refiere contracciones ni pérdida de líquido de origen vaginal.</p>',
        'weight' => '74.5 kg',
        'height' => '162 cm',
        'fetal_heart_rate' => '145 lpm',
        'uterine_height' => '31 cm',
        'vital_signs' => 'BP: 110/70, HR: 82, Temp: 36.6, SpO2: 99%',
        'physical_exam' => '<p>Abdomen globoso a expensas de útero grávido. Feto único en situación longitudinal, presentación cefálica. Movimientos fetales presentes. No se palpan contracciones. Tacto vaginal: Cuello posterior, cerrado, formado.</p>',
        'diagnostic_impression' => 'Embarazo de 32 semanas por FUM. Evolución fisiológica.'
    ]);

    // 2. Create Prescription
    $evolution->prescriptions()->create([
        'patient_id' => $patientId,
        'date' => $date,
        'medications' => 'Hierro + Ácido Fólico',
        'indications' => 'Tomar 1 tableta diaria en ayunas con jugo cítrico.'
    ]);

    // 3. Create Exam record
    $evolution->exams()->create([
        'patient_id' => $patientId,
        'date' => $date,
        'exam_type' => 'Ecografía Obstétrica',
        'general_findings' => 'Biometría fetal acorde a 32 semanas. ILA normal. Placenta fúndica anterior grado I. Peso fetal estimado: 1900g.'
    ]);

    echo "Evolución completa generada para Ivon Pacheco (ID: $patientId)\n";
});
