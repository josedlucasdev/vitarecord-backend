<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\BaseHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = $request->query('q');
        
        $patientsQuery = Patient::query()->with('baseHistory');

        if ($user->role === 'doctor') {
            $patientsQuery->where(function($q) use ($user) {
                $q->where('doctor_id', $user->id)
                  ->orWhereHas('referrals', function($rq) use ($user) {
                      $rq->where('to_doctor_id', $user->id)
                         ->where('status', 'accepted');
                  });
            });
        }

        if ($query) {
            $patientsQuery->where(function($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                  ->orWhere('identity_card', 'like', "%$query%");
            });
        }

        return $patientsQuery->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        Gate::authorize('create', Patient::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer',
            'identity_card' => 'required|string|unique:patients,identity_card',
            'occupation' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            // Base History Data
            'menarche' => 'nullable|integer',
            'menstrual_cycle' => 'nullable|string',
            'last_menstruation_date' => 'nullable|date',
            'sexarche' => 'nullable|integer',
            'sexual_partners' => 'nullable|integer',
            'contraceptive_method' => 'nullable|string',
            'pregnancies_gpcav' => 'nullable|string',
            'last_pap_smear' => 'nullable|string',
            'last_mammography' => 'nullable|string',
            'personal_pathological' => 'nullable|string',
            'surgical' => 'nullable|string',
            'allergies' => 'nullable|string',
            'habits' => 'nullable|string',
            'family_history' => 'nullable|string',
        ]);

        $patient = Patient::create([
            'doctor_id' => $user->id,
            'name' => $validated['name'],
            'age' => $validated['age'],
            'identity_card' => $validated['identity_card'],
            'occupation' => $validated['occupation'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        // Crear Historia Base inicial
        $patient->baseHistory()->create($request->only([
            'menarche', 'menstrual_cycle', 'last_menstruation_date', 'sexarche', 
            'sexual_partners', 'contraceptive_method', 'pregnancies_gpcav', 
            'last_pap_smear', 'last_mammography', 'personal_pathological', 
            'surgical', 'allergies', 'habits', 'family_history'
        ]));

        return response()->json($patient->load('baseHistory'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Patient $patient)
    {
        Gate::authorize('view', $patient);
        return $patient->load('baseHistory');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        Gate::authorize('update', $patient);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer',
            'identity_card' => 'required|string|unique:patients,identity_card,' . $patient->id,
            'occupation' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            // Base History Data
            'menarche' => 'nullable|integer',
            'menstrual_cycle' => 'nullable|string',
            'last_menstruation_date' => 'nullable|date',
            'sexarche' => 'nullable|integer',
            'sexual_partners' => 'nullable|integer',
            'contraceptive_method' => 'nullable|string',
            'pregnancies_gpcav' => 'nullable|string',
            'last_pap_smear' => 'nullable|string',
            'last_mammography' => 'nullable|string',
            'personal_pathological' => 'nullable|string',
            'surgical' => 'nullable|string',
            'allergies' => 'nullable|string',
            'habits' => 'nullable|string',
            'family_history' => 'nullable|string',
        ]);

        $patient->update([
            'name' => $validated['name'],
            'age' => $validated['age'],
            'identity_card' => $validated['identity_card'],
            'occupation' => $validated['occupation'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        // Actualizar Historia Base
        $patient->baseHistory()->update($request->only([
            'menarche', 'menstrual_cycle', 'last_menstruation_date', 'sexarche', 
            'sexual_partners', 'contraceptive_method', 'pregnancies_gpcav', 
            'last_pap_smear', 'last_mammography', 'personal_pathological', 
            'surgical', 'allergies', 'habits', 'family_history'
        ]));

        return response()->json($patient->load('baseHistory'));
    }

    /**
     * LÍNEA DE TIEMPO: El endpoint más importante del proyecto.
     * Retorna todas las evoluciones con los módulos Lego integrados (Recetas, Exámenes)
     * ordenados cronológicamente de forma descendente.
     */
    public function timeline(Request $request, Patient $patient)
    {
        Gate::authorize('view', $patient);

        // Cargamos las evoluciones y acoplamos los módulos que tengan vinculados
        $evolutions = $patient->evolutions()
            ->with(['prescriptions', 'exams', 'doctor'])
            ->orderBy('id', 'desc')
            ->get();
            
        // Podríamos también retornar recursos "huerfanos" (recetas hechas fuera de una evolución) 
        // pero por lo general se muestran en el timeline ordenadas por fecha.
        // Para simplificar, la vista centralizará todo vía evolutions

        return response()->json([
            'patient' => $patient->load('baseHistory'),
            'timeline' => $evolutions
        ]);
    }
}
