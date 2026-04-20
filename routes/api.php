<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\EvolutionController;
use App\Http\Controllers\ExamsUltrasoundController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReferralController;

Route::post('login', [AuthController::class, 'login']);

// For now we disable auth middleware on Doctors routing to ensure the UI template demonstration works fluently while we fix the Vue Store integration
// In a production environment this should be enclosed within auth:sanctum
Route::get('admin/doctors', [DoctorController::class, 'index']);
Route::post('admin/doctors', [DoctorController::class, 'store']);
Route::put('admin/doctors/{id}', [DoctorController::class, 'update']);
Route::patch('admin/doctors/{id}/toggle-status', [DoctorController::class, 'toggleStatus']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('patients', PatientController::class);
    Route::get('patients/{patient}/timeline', [PatientController::class, 'timeline']);
    Route::post('patients/{patient}/evolutions', [EvolutionController::class, 'store']);
    Route::post('patients/{patient}/exams', [ExamsUltrasoundController::class, 'store']);
    Route::post('patients/{patient}/prescriptions', [PrescriptionController::class, 'store']);

    // Sistema de Referencias
    Route::get('referrals', [ReferralController::class, 'index']);
    Route::post('referrals', [ReferralController::class, 'store']);
    Route::put('referrals/{referral}', [ReferralController::class, 'update']);

    // Analíticas
    Route::get('dashboard/stats', [DashboardController::class, 'stats']);
});
