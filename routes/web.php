<?php

use Illuminate\Support\Facades\Route;

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
//     return view('dashboard');
// });

// Login Azure
Route::get('/login', [App\Http\Controllers\AuthController::class, 'signIn']);
Route::get('/callback', [App\Http\Controllers\AuthController::class, 'callback']);
Route::get('/signout', [App\Http\Controllers\AuthController::class, 'signOut']);

// Signup
Route::post('/inschrijven', [App\Http\Controllers\ParticipantController::class, 'signup']);
Route::get('/', [App\Http\Controllers\ParticipantController::class, 'signupIndex']);
Route::get('/inschrijven/verify/{token}',[App\Http\Controllers\VerificationController::class,'verify']);

// AzureAuth group
Route::middleware(['AzureAuth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index']);

    // Participants
    Route::get('/participants', [App\Http\Controllers\ParticipantController::class, 'getParticipantsWithInformation']);
    Route::get('/participants/{userId}', [App\Http\Controllers\ParticipantController::class, 'getParticipantsWithInformation']);
    Route::post('/participants/{userId}/checkIn', [App\Http\Controllers\ParticipantController::class, 'checkIn']);
    Route::post('/participants/{userId}/checkOut', [App\Http\Controllers\ParticipantController::class, 'checkOut']);
    Route::post('/participants/{userId}/delete', [App\Http\Controllers\ParticipantController::class, 'delete']);
    Route::get('/add', [App\Http\Controllers\ParticipantController::class, 'viewAdd']);
    Route::post('/add/store', [App\Http\Controllers\ParticipantController::class, 'store']);

    Route::get('/participantscheckedin', [App\Http\Controllers\ParticipantController::class, 'checkedInView']);
    Route::get('/participantscheckedin/{userId}', [App\Http\Controllers\ParticipantController::class, 'checkedInView']);

    // Bus
    Route::get('/bus', [App\Http\Controllers\BusController::class, 'index']);
    Route::post('/bus/add', [App\Http\Controllers\BusController::class, 'addBusses']);
    Route::post('/bus/reset', [App\Http\Controllers\BusController::class, 'resetBusses']);
    Route::post('/bus/addBusNumber', [App\Http\Controllers\BusController::class, 'addBusNumber']);
    Route::post('/bus/addPersons', [App\Http\Controllers\BusController::class, 'addPersonsToBus']);

    // Excel
    Route::get('/export_excel/excel', [App\Http\Controllers\ParticipantController::class, 'excel'])->name('export_excel.excel');

    // Api
    Route::get('/import', [App\Http\Controllers\APIController::class, 'GetParticipants']);
});
