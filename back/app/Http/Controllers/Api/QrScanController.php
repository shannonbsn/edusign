<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cours;
use App\Models\Presence;

class QrScanController extends Controller
{
    public function scan(Request $request)
    {
        $request->validate([
            'cours_id' => 'required|exists:cours,id',
        ]);

        $user = $request->user();
        $cours = Cours::findOrFail($request->cours_id);

        if ($cours->classe_id !== $user->classe_id) {
            return response()->json(['message' => 'Vous ne faites pas partie de cette classe.'], 403);
        }

        $existing = Presence::where('user_id', $user->id)
            ->where('cours_id', $cours->id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Présence déjà enregistrée.'], 200);
        }

        Presence::create([
            'user_id' => $user->id,
            'cours_id' => $cours->id,
            'presence' => true,
        ]);

        return response()->json(['message' => 'Présence enregistrée avec succès.']);
    }
}
