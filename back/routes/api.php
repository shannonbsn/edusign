<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use App\Http\Controllers\Api\QrScanController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\CoursController;
use App\Http\Controllers\SalleController;

// Routes pour l'authentification et les tokens
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', function (Request $request) {
    $user = User::where('email', $request->email)->first();

    if ($user && Hash::check($request->password, $user->password)) {
        $token = $user->createToken('auth-token', ['check-status', 'place-orders'])->plainTextToken;

        return response()->json([
            'token' => $token
        ]);
    }

    return response()->json(['error' => 'Identifiants incorrects'], 401);
})->middleware(['auth:sanctum', 'abilities:check-status,place-orders']);

Route::get('/user', function (Request $request) {
    return response()->json($request->user());
})->middleware('auth:sanctum');

Route::post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Déconnexion réussie']);
})->middleware('auth:sanctum');

// Routes de mes api pour les vues et méthodes controller
Route::middleware('auth:sanctum')->post('/scan', [QrScanController::class, 'scan']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('classes', ClasseController::class);
    Route::apiResource('cours', CoursController::class);
    Route::apiResource('salles', SalleController::class);

    Route::get('/presences/user/{id}', [PresenceController::class, 'userPresences']);
    Route::get('/presences/cours/{id}', [PresenceController::class, 'coursPresences']);
});

// Route pour les qrcodes
Route::get('/generate-temporary-token/{cours}', function ($coursId) {
    $cours = \App\Models\Cours::findOrFail($coursId);

    $payload = [
        'cours_id' => $cours->id,
        'expires_at' => now()->addSeconds(10)->timestamp,
    ];

    $token = Crypt::encrypt($payload);

    return response()->json(['token' => $token]);
});
Route::post('/scan-presence/{token}', function ($token, Request $request) {
    try {
        $data = Crypt::decrypt($token);

        if (now()->timestamp > $data['expires_at']) {
            return response()->json(['message' => 'QR expiré'], 403);
        }

        \App\Models\Presence::create([
            'user_id' => $request->user()->id,
            'cours_id' => $data['cours_id'],
            'presene' => true,
        ]);

        return response()->json(['message' => 'Présence enregistrée']);
    } catch (\Exception $e) {
        return response()->json(['message' => 'QR invalide'], 400);
    }
})->middleware('auth:sanctum');
