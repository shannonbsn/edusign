<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use App\Models\Cours;
use App\Models\Presence;
use App\Http\Controllers\Api\QrScanController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\CoursController;
use App\Http\Controllers\SalleController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user());
});

Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'nom' => 'required|string',
        'prenom' => 'required|string',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6',
        'role' => 'required|string|in:admin,intervenant,etudiant',
        'classe_id' => 'nullable|exists:classes,id'
    ]);

    $user = User::create([
        'nom' => $validated['nom'],
        'prenom' => $validated['prenom'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => $validated['role'],
        'classe_id' => $validated['classe_id']
    ]);

    return response()->json(['message' => 'Inscription réussie', 'user' => $user]);
});

Route::post('/login', function (Request $request) {
    $user = User::where('email', $request->email)->first();

    if ($user && Hash::check($request->password, $user->password)) {
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ]);
    }

    return response()->json(['error' => 'Identifiants incorrects'], 401);
});

Route::post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Déconnexion réussie']);
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('classes', ClasseController::class);
    Route::apiResource('cours', CoursController::class);
    Route::apiResource('salles', SalleController::class);

    Route::get('/presences/user/{id}', [PresenceController::class, 'userPresences']);
    Route::get('/presences/cours/{id}', [PresenceController::class, 'coursPresences']);
});

Route::middleware(['auth:sanctum'])->get('/cours', function () {
    return response()->json(Cours::all());
});

Route::middleware('auth:sanctum')->get('/cours/{id}/qrcode', function ($id) {
    $cours = Cours::findOrFail($id);
    return response()->json([
        'title' => $cours->title,
        'qrCodeUrl' => $cours->qr_code_url
    ]);
});

Route::middleware('auth:sanctum')->get('/generate-temporary-token/{cours}', function ($coursId) {
    $cours = Cours::findOrFail($coursId);

    $payload = [
        'cours_id' => $cours->id,
        'expires_at' => now()->addSeconds(10)->timestamp,
    ];

    $token = Crypt::encrypt($payload);

    return response()->json(['token' => $token]);
});

Route::middleware('auth:sanctum')->post('/scan-presence/{token}', function ($token, Request $request) {
    try {
        $data = Crypt::decrypt($token);

        if (now()->timestamp > $data['expires_at']) {
            return response()->json(['message' => 'QR expiré'], 403);
        }

        Presence::create([
            'user_id' => $request->user()->id,
            'cours_id' => $data['cours_id'],
            'presene' => true,
        ]);

        return response()->json(['message' => 'Présence enregistrée']);
    } catch (\Exception $e) {
        return response()->json(['message' => 'QR invalide'], 400);
    }
});
