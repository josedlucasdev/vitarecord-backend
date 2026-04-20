<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PrescriptionController extends Controller
{
    /**
     * Store a newly created prescription (Lego Module).
     */
    public function store(Request $request, Patient $patient)
    {
        Gate::authorize('update', $patient);

        $validated = $request->validate([
            'evolution_id' => 'nullable|exists:evolutions,id',
            'date' => 'required|date',
            'medications' => 'required|string',
            'indications' => 'nullable|string',
        ]);

        $prescription = $patient->prescriptions()->create($validated);

        return response()->json($prescription, 201);
    }
}
