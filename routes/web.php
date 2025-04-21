<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\ContentManager;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// })->name('home');

Route::get('/admin/content/create', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/admin/content/edit/{id}', function () {
    return view('admin.dashboard');
})->name('admin.dashboard.edit');

Route::get('/admin/contentlist', function () {
    return view('admin.contentlist');
})->name('admin.contentlist');

Route::get('/admin/leaderboard', function () {
    return view('admin.leader-board');
})->name('admin.leaderboard');

Route::get('/admin/chatmoderation', function () {
    return view('admin.chatmoderation');
})->name('admin.chatmoderation');


// Show login form (GET)
Route::get('/home', function () {
    return view('index');
})->name('login')->middleware('guest');

Route::post('/login', [UserController::class, 'login'])->name('login.custom');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');


Route::middleware(['auth'])->group(function () {


    //using higher order funcction
    Route::get('/polls', fn() => view('polls'))->name('polls');
    Route::get('/quizzes', fn() => view('quizz'))->name('quizzes');
   // Route::get('/trivia', fn() => view('trivia'))->name('trivia');
    Route::post('/user/setup-preference', [UserController::class, 'updateUserPreference'])->name('user.setup-preference');
});

Route::post('/logout', [UserController::class, 'logout'])->name('logout');