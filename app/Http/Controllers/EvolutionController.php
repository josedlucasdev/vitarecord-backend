<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Evolution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EvolutionController extends Controller
{
    /**
     * Store a newly created evolution (Lego Module).
     */
    public function store(Request $request, Patient $patient)
    {
        Gate::authorize('update', $patient);

        $validated = $request->validate([
            'date' => 'required|date',
            'reason_for_consultation' => 'required|string',
            'current_illness' => 'nullable|string',
            'weight' => 'nullable|string',
            'height' => 'nullable|string',
            'fetal_heart_rate' => 'nullable|string',
            'uterine_height' => 'nullable|string',
            'vital_signs' => 'nullable|string',
            'physical_exam' => 'nullable|string',
            'breast_exam' => 'nullable|string',
            'abdominal_exam' => 'nullable|string',
            'gynecological_exam_external' => 'nullable|string',
            'gynecological_exam_speculum' => 'nullable|string',
            'gynecological_exam_bimanual' => 'nullable|string',
            'diagnostic_impression' => 'nullable|string',
            'prescriptions' => 'nullable|string', // Se envía como string JSON cuando se usa FormData
            'eco_attachments.*' => 'nullable|image|max:20480',
            'manual_prescription_file' => 'nullable|image|max:20480',
        ]);

        return \DB::transaction(function () use ($validated, $patient, $request) {
            $validated['doctor_id'] = $request->user()->id;
            
            // 1. Crear la evolución base
            $evolutionData = \Illuminate\Support\Arr::except($validated, ['prescriptions', 'eco_attachments', 'manual_prescription_file']);
            $evolution = $patient->evolutions()->create($evolutionData);

            // 2. Procesar Recetas Digitales (si vienen en el JSON dentro del FormData)
            if ($request->has('prescriptions')) {
                $prescriptions = json_decode($request->input('prescriptions'), true);
                if (is_array($prescriptions)) {
                    foreach ($prescriptions as $rx) {
                        $patient->prescriptions()->create([
                            'evolution_id' => $evolution->id,
                            'date' => $validated['date'],
                            'medications' => $rx['medication'],
                            'indications' => "Dosis: {$rx['dosage']}, Frecuencia: {$rx['frequency']}, Duración: {$rx['duration']}"
                        ]);
                    }
                }
            }

            // 3. Procesar Receta Manual (Imagen)
            if ($request->hasFile('manual_prescription_file')) {
                $path = $request->file('manual_prescription_file')->store('clinical_attachments', 'public');
                $patient->prescriptions()->create([
                    'evolution_id' => $evolution->id,
                    'date' => $validated['date'],
                    'medications' => 'Receta Manual Adjunta',
                    'indications' => 'Ver imagen adjunta en la historia clínica.',
                    'attachment_path' => $path
                ]);
            }

            // 4. Procesar Ecos (Múltiples imágenes)
            if ($request->hasFile('eco_attachments')) {
                foreach ($request->file('eco_attachments') as $file) {
                    $path = $file->store('clinical_attachments', 'public');
                    $patient->exams()->create([
                        'evolution_id' => $evolution->id,
                        'date' => $validated['date'],
                        'exam_type' => 'ultrasound',
                        'general_findings' => 'Imagen de ecografía adjunta durante la evolución.',
                        'attachment_path' => $path
                    ]);
                }
            }

            return response()->json($evolution->load(['prescriptions', 'exams']), 201);
        });
    }
}
