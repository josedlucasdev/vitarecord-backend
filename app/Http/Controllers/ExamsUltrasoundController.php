<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\ExamsUltrasound;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ExamsUltrasoundController extends Controller
{
    /**
     * Store a newly created exam (Lego Module).
     */
    public function store(Request $request, Patient $patient)
    {
        Gate::authorize('update', $patient);

        $validated = $request->validate([
            'evolution_id' => 'nullable|exists:evolutions,id',
            'date' => 'required|date',
            'exam_type' => 'required|string',
            'findings_uterus' => 'nullable|string',
            'findings_ovaries' => 'nullable|string',
            'findings_cul_de_sac' => 'nullable|string',
            'general_findings' => 'nullable|string',
            'attachment_path' => 'nullable|string',
        ]);

        $exam = $patient->exams()->create($validated);

        return response()->json($exam, 201);
    }
}
