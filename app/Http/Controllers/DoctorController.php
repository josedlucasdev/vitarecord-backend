<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class DoctorController extends Controller
{
    /**
     * Display a listing of doctors. (Admin Only)
     */
    public function index(Request $request)
    {
        $doctors = User::where('role', 'doctor')->get();
        return response()->json($doctors);
    }

    /**
     * Create a new doctor profile. (Admin Only)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'specialty' => 'required|string|max:255',
            'identity_card' => 'required|string|max:50|unique:users',
            'phone' => 'required|string|max:20',
            'photo' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('doctors'), $filename);
            $validated['photo_path'] = $filename;
        }

        // Default password for new doctors 
        $validated['password'] = Hash::make('password123'); // Default password
        $validated['role'] = 'doctor';

        $doctor = User::create($validated);

        return response()->json($doctor, 201);
    }

    /**
     * Update a doctor profile.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'specialty' => 'required|string|max:255',
            'identity_card' => 'required|string|max:50|unique:users,identity_card,'.$id,
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo_path) {
                $oldPath = public_path('doctors/' . $user->photo_path);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('doctors'), $filename);
            $validated['photo_path'] = $filename;
        }

        $user->update($validated);

        return response()->json($user);
    }

    /**
     * Toggle doctor active status.
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json($user);
    }
}
