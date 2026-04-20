<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PatientPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'doctor';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Patient $patient): bool
    {
        // El paciente en sí mismo está viendo su cuenta
        if ($user->role === 'patient' && $patient->user_id === $user->id) {
            return true;
        }

        // El doctor es el dueño directo
        if ($user->role === 'doctor' && $patient->doctor_id === $user->id) {
            return true;
        }

        // Si es un doctor secundario y existe una Referral activa
        if ($user->role === 'doctor') {
            $hasReferral = $patient->referrals()
                ->where('to_doctor_id', $user->id)
                ->where('status', 'accepted')
                ->exists();
            if ($hasReferral) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'doctor';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Patient $patient): bool
    {
        return $user->role === 'doctor' && $patient->doctor_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Patient $patient): bool
    {
        return $user->role === 'doctor' && $patient->doctor_id === $user->id;
    }
}
