<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\WashSlotController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OutletController;

/*
|──────────────────────────────────────────────────────
| AUTH ROUTES (Guest only)
|──────────────────────────────────────────────────────
*/
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Root redirect
Route::get('/', fn() => redirect('/dashboard'));

/*
|──────────────────────────────────────────────────────
| PROTECTED ADMIN ROUTES (Auth required)
|──────────────────────────────────────────────────────
*/
Route::middleware(['auth'])->group(function () {

    /* ── Dashboard ── */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [DashboardController::class, 'search'])->name('global-search');

    /* ── Bookings ── */
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/',             [BookingController::class, 'index'])->name('index');
        Route::get('/create',       [BookingController::class, 'create'])->name('create');
        Route::post('/',            [BookingController::class, 'store'])->name('store');
        Route::get('/export',       [BookingController::class, 'export'])->name('export');
        Route::get('/{booking}',    [BookingController::class, 'show'])->name('show');
        Route::get('/{booking}/edit', [BookingController::class, 'edit'])->name('edit');
        Route::put('/{booking}',    [BookingController::class, 'update'])->name('update');
        Route::delete('/{booking}', [BookingController::class, 'destroy'])->name('destroy');
        Route::patch('/{booking}/confirm', [BookingController::class, 'confirm'])->name('confirm');
        Route::patch('/{booking}/cancel',  [BookingController::class, 'cancel'])->name('cancel');
        Route::post('/{booking}/assign',   [BookingController::class, 'assign'])->name('assign');
    });

    /* ── Technicians ── */
    Route::prefix('technicians')->name('technicians.')->group(function () {
        Route::get('/',               [TechnicianController::class, 'index'])->name('index');
        Route::post('/',              [TechnicianController::class, 'store'])->name('store');
        Route::get('/{technician}',   [TechnicianController::class, 'show'])->name('show');
        Route::get('/{technician}/edit', [TechnicianController::class, 'edit'])->name('edit');
        Route::put('/{technician}',   [TechnicianController::class, 'update'])->name('update');
        Route::delete('/{technician}',[TechnicianController::class, 'destroy'])->name('destroy');
    });

    /* ── Outlets ── */
    Route::prefix('outlets')->name('outlets.')->group(function () {
        Route::get('/',             [OutletController::class, 'index'])->name('index');
        Route::post('/',            [OutletController::class, 'store'])->name('store');
        Route::get('/{outlet}',     [OutletController::class, 'show'])->name('show');
        Route::get('/{outlet}/edit',[OutletController::class, 'edit'])->name('edit');
        Route::put('/{outlet}',     [OutletController::class, 'update'])->name('update');
        Route::delete('/{outlet}',  [OutletController::class, 'destroy'])->name('destroy');
    });

    /* ── Wash Slots ── */
    Route::prefix('slots')->name('slots.')->group(function () {
        Route::get('/',             [WashSlotController::class, 'index'])->name('index');
        Route::post('/',            [WashSlotController::class, 'store'])->name('store');
        Route::delete('/{washSlot}',[WashSlotController::class, 'destroy'])->name('destroy');
        Route::get('/available',    [WashSlotController::class, 'available'])->name('available');
    });

    /* ── Promos ── */
    Route::prefix('promos')->name('promos.')->group(function () {
        Route::get('/',          [PromoController::class, 'index'])->name('index');
        Route::post('/',         [PromoController::class, 'store'])->name('store');
        Route::put('/{promo}',   [PromoController::class, 'update'])->name('update');
        Route::delete('/{promo}',[PromoController::class, 'destroy'])->name('destroy');
        Route::get('/validate',  [PromoController::class, 'validatePromo'])->name('validate');
    });

    /* ── Payments ── */
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/',             [PaymentController::class, 'index'])->name('index');
        Route::get('/export',       [PaymentController::class, 'export'])->name('export');
        Route::get('/{payment}',    [PaymentController::class, 'show'])->name('show');
        Route::post('/{payment}/refund',  [PaymentController::class, 'refund'])->name('refund');
        Route::post('/{payment}/confirm', [PaymentController::class, 'confirm'])->name('confirm');
    });

    /* ── Reports ── */
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/',        [ReportController::class, 'index'])->name('index');
        Route::get('/export',  [ReportController::class, 'export'])->name('export');
    });

    /* ── Customers ── */
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/',              [CustomerController::class, 'index'])->name('index');
        Route::get('/export',        [CustomerController::class, 'export'])->name('export');
        Route::get('/{customer}',    [CustomerController::class, 'show'])->name('show');
        Route::patch('/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('toggleStatus');
    });

    /* ── Settings (placeholder) ── */
    Route::get('/settings', fn() => view('settings.index'))->name('settings');
});

/*
|──────────────────────────────────────────────────────
| WEBHOOK ROUTES (No auth, validated by signature)
|──────────────────────────────────────────────────────
*/
Route::post('/webhook/payment', [PaymentController::class, 'webhook'])->name('webhook.payment');
