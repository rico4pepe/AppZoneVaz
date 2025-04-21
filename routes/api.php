<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserPreferenceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SportMonksController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PollVoteController;
use App\Http\Controllers\QuizController;

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
    Route::get('/chat', [ChatController::class, 'fetchMessages']);
    Route::post('/chat', [ChatController::class, 'sendMessage']);
    Route::get('/polls', [PollVoteController::class, 'list']);
    Route::post('/poll/vote', [PollVoteController::class, 'vote']);
    Route::get('/poll/{id}/results', [PollVoteController::class, 'results']);

        // Add the new route to check if the user has voted
    Route::get('/poll/{id}/check-vote', [PollVoteController::class, 'checkVote']);

    // Route to handle  quizzes
    Route::get('/quizzes', [QuizController::class, 'list']);
    Route::post('/quiz/{content}/answer', [QuizController::class, 'submitSingleAnswer']);
    Route::get('/quiz/{id}/check-answered', [QuizController::class, 'checkAnswered']);

      


});





