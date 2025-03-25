<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserPreferenceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SportMonksController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('/register', [UserController::class, 'register'])->name('register');
Route::post('/login', [UserController::class, 'login'])->name('login');



Route::middleware('auth:sanctum')->group(function () {
    Route::post('/preferences/save', [UserPreferenceController::class, 'savePreferences']);
    Route::get('/preferences/get', [UserPreferenceController::class, 'getPreferences']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/subscription/status', [UserController::class, 'checkSubscription'])->name('subscription.status');
    Route::post('/subscription/renew', [UserController::class, 'renewSubscription']);
    Route::get('/teams/country/{country_id}', [SportMonksController::class, 'getTeamsByCountry']);
    Route::get('/leagues', [SportMonksController::class, 'getLeagues']);
    Route::get('/countries', [SportMonksController::class, 'getCountries']);
    Route::post('/user/team', [UserController::class, 'saveUserTeam']);

});





