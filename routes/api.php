<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClientUserController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TicketReplyController;
use App\Http\Controllers\Api\Admin\ClientController as ApiClientController;
use App\Http\Controllers\Api\Admin\TicketController as ApiTicketController;
use App\Http\Controllers\Api\Staff\TicketController as StaffTicketController;
use App\Http\Controllers\Api\Admin\CompanyController as ApiCompanyController;
use App\Http\Controllers\TelegramController;
use Illuminate\Support\Facades\http;
use App\Http\Controllers\WAInboxController;
use App\Http\Controllers\WAQRController;


// Login TIDAK perlu pakai middleware auth
Route::post('/login', [AuthController::class, 'login']);

// Public route
Route::get('/clients/{id}/users', [ClientUserController::class, 'getUsers']);

// Route yang butuh token
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::get('/tickets/{id}', [TicketController::class, 'show']);
    Route::put('/tickets/{id}/status', [TicketController::class, 'updateStatus']);
    Route::get('/tickets/{ticket_id}/replies', [TicketReplyController::class, 'index']);
    Route::post('/tickets/{ticket_id}/replies', [TicketReplyController::class, 'store']);
    Route::delete('/tickets/{id}', [TicketController::class, 'destroy']);
});

Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    // Client
    Route::get('/clients', [ApiClientController::class, 'index']);
    Route::post('/clients', [ApiClientController::class, 'store']);
    Route::put('/clients/{id}', [ApiClientController::class, 'update']);

    // Company API
    Route::get('/companies', [ApiCompanyController::class, 'index']);
    Route::get('/companies/{id}', [ApiCompanyController::class, 'show']);
    Route::post('/companies', [ApiCompanyController::class, 'store']);
    Route::put('/companies/{id}', [ApiCompanyController::class, 'update']);
    Route::delete('/companies/{id}', [ApiCompanyController::class, 'destroy']);
    Route::patch('/companies/{id}/sla', [ApiCompanyController::class, 'updateSla']);

    // Ticket
    Route::get('/tickets', [ApiTicketController::class, 'index']);
    Route::get('/tickets/{ticket_id}', [ApiTicketController::class, 'show']);
    Route::patch('/tickets/{ticket_id}/status', [ApiTicketController::class, 'updateStatus']);
});

Route::prefix('staff')->middleware('auth:sanctum')->group(function () {
    Route::get('/tickets', [StaffTicketController::class, 'index']);
    Route::get('/tickets/{ticket_id}', [StaffTicketController::class, 'show']);
    Route::patch('/tickets/{ticket_id}/status', [StaffTicketController::class, 'updateStatus']);
    Route::post('/tickets/{ticket_id}/open', [StaffTicketController::class, 'openAndRedirect']);
});

Route::post('/telegram/webhook', [TelegramController::class, 'webhook']);

Route::post('/kirim-wa', function (\Illuminate\Http\Request $request) {
    $response = Http::post('http://localhost:3000/send-message', [
        'nomor' => $request->nomor,
        'pesan' => $request->pesan,
    ]);

    return response()->json($response->json());
});

Route::post('/wa-inbox', [WAInboxController::class, 'store']);

Route::post('/wa-qr', [WAQRController::class, 'store']);
Route::get('/wa-qr', [WAQRController::class, 'show']);
Route::get('/wa-status', [WAQRController::class, 'status']); // ✅ untuk frontend JS
Route::post('/wa-status', [WAQRController::class, 'updateStatus']); // ✅ untuk bot.js (update status login)