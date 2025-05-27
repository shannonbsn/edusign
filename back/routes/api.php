<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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


// Route::post('/tokens/create', function (Request $request) {
//     $token = $request->user()->createToken($request->token_name);

//     return ['token' => $token->plainTextToken];
// });

// modifier le chemin pour rediriger vers la bonne page de mon app native
// Route::get('/orders', function () {})->middleware(['auth:sanctum', 'abilities:check-status,place-orders']);
