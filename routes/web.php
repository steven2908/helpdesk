<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketReplyController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\TicketReplyController as AdminTicketReplyController;
use App\Http\Controllers\Staff\TicketController as StaffTicketController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\TelegramLogController;
use App\Http\Controllers\WAQRController;
use App\Http\Controllers\Admin\SurveyController;
use App\Http\Controllers\CaseLockController;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', [TicketController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin-only: Kelola user (client)
Route::middleware(['auth', 'role:admin'])->prefix('admin/clients')->name('admin.clients.')->group(function () {
    Route::get('/', [ClientController::class, 'index'])->name('index');
    Route::get('/create', [ClientController::class, 'create'])->name('create');
    Route::post('/store-user', [ClientController::class, 'storeUser'])->name('storeUser');
    Route::get('/{user}/edit', [ClientController::class, 'edit'])->name('edit');
    Route::put('/{user}', [ClientController::class, 'update'])->name('update');

    // Tambahkan route delete user
    Route::delete('/{user}', [ClientController::class, 'destroy'])->name('destroy');
});



// Admin-only: Kelola perusahaan
Route::middleware(['auth', 'role:admin'])->prefix('admin/companies')->name('admin.companies.')->group(function () {
    Route::get('/', [CompanyController::class, 'index'])->name('index');
    Route::get('/create', [CompanyController::class, 'create'])->name('create');
    Route::post('/', [CompanyController::class, 'store'])->name('store');
    Route::get('/{company}/edit', [CompanyController::class, 'edit'])->name('edit');
Route::put('/{company}', [CompanyController::class, 'update'])->name('update');

    Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('destroy');
   Route::patch('/{company}/update-sla', [CompanyController::class, 'updateSla'])->name('updateSla');

});

// Admin-Only: Telegram Log
Route::middleware(['auth', 'role:admin'])->prefix('admin/telegram-logs')->name('admin.telegram.logs.')->group(function () {
    Route::get('/', [TelegramLogController::class, 'index'])->name('index');
});



// Admin-only
Route::middleware(['auth', 'role:admin'])->prefix('admin/tickets')->as('admin.tickets.')->group(function () {
    Route::get('/', [AdminTicketController::class, 'index'])->name('index');
    Route::get('/{ticket}/open', [AdminTicketController::class, 'openAndRedirect'])->name('openAndRedirect');
    Route::get('/{ticket}', [AdminTicketController::class, 'show'])->name('show');
    Route::post('/{ticket}/reply', [AdminTicketReplyController::class, 'store'])->name('reply.store');
    
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('surveys/cs', [SurveyController::class, 'csIndex'])->name('surveys.cs-index');
});

// ✅ Tambahkan ini di luar prefix 'admin/tickets'
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/surveys/{survey}', [\App\Http\Controllers\Admin\SurveyController::class, 'show'])->name('surveys.show');
});



Route::middleware(['auth', 'role:staff|admin'])->prefix('staff/tickets')->as('staff.tickets.')->group(function () {
    Route::get('/', [StaffTicketController::class, 'index'])->name('index');
    Route::get('/{ticket}', [StaffTicketController::class, 'show'])->name('show');
    Route::get('/{ticket}/open', [StaffTicketController::class, 'openAndRedirect'])->name('openAndRedirect');
    Route::post('/{ticket}/reply', [TicketReplyController::class, 'store'])->name('reply.store');
    Route::patch('/{ticket}/status', [StaffTicketController::class, 'updateStatus'])->name('updateStatus');
});



// Geser keluar dari grup admin.tickets agar punya nama 'tickets.updateStatus'
Route::middleware(['auth', 'role:admin'])->patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])
    ->name('tickets.updateStatus');



// User-only: Lihat dan buat tiket + balasan
Route::middleware(['auth', 'role:user|admin|staff'])->prefix('tickets')->as('tickets.')->group(function () {
    Route::get('/', [TicketController::class, 'index'])->name('index');
    Route::get('/mine', [TicketController::class, 'index'])->name('mine');
    Route::get('/create', [TicketController::class, 'create'])->name('create');
    Route::post('/', [TicketController::class, 'store'])->name('store');
    Route::get('/{ticket}', [TicketController::class, 'show'])->name('show');
    Route::delete('/{ticket}', [TicketController::class, 'destroy'])->name('destroy');
    Route::post('/{ticket}/reply', [TicketReplyController::class, 'store'])->name('reply');
});


Route::get('/qr-wa', [WAQRController::class, 'showQR'])->name('wa.qr');

Route::get('test-survey', function () {
    return 'Survey Page OK';
});

// STAFF: CRUD Case Lock
Route::middleware(['auth', 'role:staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::resource('case_locks', CaseLockController::class);
    });

// ADMIN: List Case Lock (Read-Only)
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin/case_locks')
    ->name('admin.case_locks.')
    ->group(function () {
        Route::get('/', [CaseLockController::class, 'index'])->name('index');
        Route::get('/{caseLock}', [CaseLockController::class, 'show'])->name('show');
    });



require __DIR__.'/auth.php';
