<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    /**
     * Display a listing of received referrals for the doctor.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $referrals = Referral::where('to_doctor_id', $user->id)
            ->with(['patient', 'fromDoctor'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($referrals);
    }

    /**
     * Store a new referral.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'to_doctor_id' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $patient = Patient::findOrFail($validated['patient_id']);
        
        // Ensure the current doctor owns the patient or is already a shared doctor?
        // For simplicity, only the primary doctor can refer for now
        if ($patient->doctor_id !== $request->user()->id) {
            return response()->json(['message' => 'Solo el médico de cabecera puede referir a este paciente.'], 403);
        }

        // Check if referral already exists
        $exists = Referral::where('patient_id', $validated['patient_id'])
            ->where('to_doctor_id', $validated['to_doctor_id'])
            ->whereIn('status', ['pending', 'accepted'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Este paciente ya ha sido referido a este médico.'], 422);
        }

        $referral = Referral::create([
            'patient_id' => $validated['patient_id'],
            'from_doctor_id' => $request->user()->id,
            'to_doctor_id' => $validated['to_doctor_id'],
            'status' => 'pending',
        ]);

        return response()->json($referral->load(['patient', 'toDoctor']), 201);
    }

    /**
     * Update the status of a referral (Accept/Reject).
     */
    public function update(Request $request, Referral $referral)
    {
        if ($referral->to_doctor_id !== $request->user()->id) {
            return response()->json(['message' => 'Solo el médico receptor puede actualizar esta referencia.'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        $referral->update($validated);

        return response()->json($referral);
    }
}
