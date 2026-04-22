<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information and files.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:262144',
            'signature' => 'nullable|image|max:262144',
            'stamp' => 'nullable|image|max:262144',
        ]);

        // Handle Profile Photo (Remains public)
        if ($request->hasFile('photo')) {
            if ($user->getRawOriginal('photo_path')) {
                $oldPath = public_path('doctors/' . $user->getRawOriginal('photo_path'));
                if (file_exists($oldPath)) unlink($oldPath);
            }
            $file = $request->file('photo');
            $filename = time() . '_p_' . $file->getClientOriginalName();
            $file->move(public_path('doctors'), $filename);
            $user->photo_path = $filename;
        }

        // Handle Signature (Private Storage)
        if ($request->hasFile('signature')) {
            if ($user->getRawOriginal('signature_path')) {
                $oldPath = 'signatures/' . $user->getRawOriginal('signature_path');
                if (Storage::disk('local')->exists($oldPath)) {
                    Storage::disk('local')->delete($oldPath);
                }
            }
            $file = $request->file('signature');
            $filename = time() . '_sig_' . $file->getClientOriginalName();
            $file->storeAs('signatures', $filename, 'local');
            $user->signature_path = $filename;
        }

        // Handle Stamp (Private Storage)
        if ($request->hasFile('stamp')) {
            if ($user->getRawOriginal('stamp_path')) {
                $oldPath = 'stamps/' . $user->getRawOriginal('stamp_path');
                if (Storage::disk('local')->exists($oldPath)) {
                    Storage::disk('local')->delete($oldPath);
                }
            }
            $file = $request->file('stamp');
            $filename = time() . '_stm_' . $file->getClientOriginalName();
            $file->storeAs('stamps', $filename, 'local');
            $user->stamp_path = $filename;
        }

        if ($request->has('name')) $user->name = $validated['name'];
        if ($request->has('email')) $user->email = $validated['email'];
        if ($request->has('phone')) $user->phone = $validated['phone'];
        
        $user->save();

        return response()->json($user);
    }

    /**
     * Show the authenticated user's signature as a protected Base64 JSON response.
     */
    public function showSignature(Request $request)
    {
        $path = $request->user()->getRawOriginal('signature_path');
        
        if (!$path || !Storage::disk('local')->exists('signatures/' . $path)) {
            abort(404);
        }
        
        $fileBytes = Storage::disk('local')->get('signatures/' . $path);
        $mimeType = Storage::disk('local')->mimeType('signatures/' . $path) ?: 'image/png';
        $base64 = base64_encode($fileBytes);

        return response()->json([
            'content' => 'data:' . $mimeType . ';base64,' . $base64
        ]);
    }

    /**
     * Show the authenticated user's stamp as a protected Base64 JSON response.
     */
    public function showStamp(Request $request)
    {
        $path = $request->user()->getRawOriginal('stamp_path');
        if (!$path || !Storage::disk('local')->exists('stamps/' . $path)) {
            abort(404);
        }
        
        $fileBytes = Storage::disk('local')->get('stamps/' . $path);
        $mimeType = Storage::disk('local')->mimeType('stamps/' . $path) ?: 'image/png';
        $base64 = base64_encode($fileBytes);

        return response()->json([
            'content' => 'data:' . $mimeType . ';base64,' . $base64
        ]);
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['message' => 'Contraseña actualizada correctamente.']);
    }
}
