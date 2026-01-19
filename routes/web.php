<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FrontendAuthController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\PeminjamanAdminController;
use App\Http\Controllers\PengembalianAdminController;
use App\Http\Controllers\LaporanAdminController;
use App\Http\Controllers\EventDashboardController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Peminjaman Sarpras
|--------------------------------------------------------------------------
*/

// =====================
// 🔹 HALAMAN UTAMA
// =====================
Route::get('/', fn() => view('index'))->name('home');

// =====================
// 🔹 AUTH
// =====================
Route::get('/login', [FrontendAuthController::class, 'showLogin'])->middleware('guest')->name('login');
Route::post('/login', [FrontendAuthController::class, 'login'])->middleware('web')->name('login.submit');
Route::get('/register', [FrontendAuthController::class, 'showRegister'])->middleware('guest')->name('register.form');
Route::post('/register', [FrontendAuthController::class, 'register'])->middleware('guest')->name('register.submit');
Route::post('/logout', [FrontendAuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/upload-cropped', [ProfileController::class, 'uploadCropped'])->name('profile.upload-cropped');
});

// =====================
// 🔹 SUPER ADMIN
// =====================
Route::middleware(['auth', 'role:super_admin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'index'])->name('dashboard');
        Route::post('/users/{id}/approve', [SuperAdminController::class, 'approve'])->name('users.approve');
        Route::post('/users/{id}/reject', [SuperAdminController::class, 'reject'])->name('users.reject');
        Route::post('/users/{id}/role', [SuperAdminController::class, 'updateRole'])->name('users.updateRole');
        Route::delete('/users/{id}', [SuperAdminController::class, 'destroy'])->name('users.destroy');
    });

// =====================
// 🔹 ADMIN SECTION
// =====================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin/barang')
    ->name('admin.barang.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard');

        // CRUD Barang
        Route::resource('/', BarangController::class)
            ->parameters(['' => 'barang'])
            ->except(['show']);

         // 🔹 Peminjaman
        Route::get('/peminjaman', [PeminjamanAdminController::class, 'index'])->name('peminjaman');
        Route::post('/peminjaman/{id}/update-status', [PeminjamanAdminController::class, 'updateStatus'])->name('peminjaman.updateStatus');
        Route::post('/peminjaman/{id}/setujui', [PeminjamanAdminController::class, 'approve'])->name('peminjaman.approve');
        Route::post('/peminjaman/{id}/tolak', [PeminjamanAdminController::class, 'reject'])->name('peminjaman.reject');
        Route::delete('/peminjaman/{id}', [PeminjamanAdminController::class, 'destroy'])->name('peminjaman.destroy');

        // ✅ Pengembalian
        Route::get('/pengembalian', [PengembalianAdminController::class, 'index'])->name('pengembalian');
        Route::get('/pengembalian/{id}', [PengembalianAdminController::class, 'show'])->name('pengembalian.show');
        Route::post('/pengembalian/{id}/verify', [PengembalianAdminController::class, 'verify'])->name('pengembalian.verify');

        // Laporan
        Route::get('/laporan', [LaporanAdminController::class, 'index'])->name('laporan');
    });

// =====================
// 🔹 ADMIN PROFILE
// =====================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    });

// =====================
// 🔹 USER SECTION
// =====================
Route::middleware(['auth', 'role:user'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
        Route::get('/peminjaman/dashboard', [PeminjamanController::class, 'dashboard'])->name('peminjaman.dashboard');
        Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
        Route::get('/peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
        Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
        Route::get('/peminjaman/{id}/kembalikan', [PeminjamanController::class, 'showFormKembalikan'])->name('peminjaman.kembalikan.form');
        Route::post('/peminjaman/{id}/kembalikan', [PeminjamanController::class, 'storeKembalikan'])->name('peminjaman.kembalikan');
    });

// =====================
// 🔹 ROLE REDIRECT HANDLER
// =====================
Route::get('/redirect-by-role', function () {
    $user = Auth::user();

    if (!$user) return redirect()->route('login');

    if ($user->hasRole('super_admin')) {
        return redirect()->route('superadmin.dashboard');
    } elseif ($user->hasRole('admin')) {
        return redirect()->route('admin.barang.dashboard');
    } elseif ($user->hasRole('user')) {
        return redirect()->route('user.peminjaman.dashboard');
    }

    Auth::logout();
    return redirect()->route('login')->with('error', 'Role tidak dikenali.');
})->middleware('auth')->name('redirect.role');

// =====================
// 🔹 DEBUG
// =====================
Route::get('/login-test', fn() => 'Route /login-test works!');
Route::get('/check-auth', function () {
    if (Auth::check()) {
        return 'Logged in as: ' . Auth::user()->username . ' (role: ' . Auth::user()->getRoleNames()->implode(', ') . ')';
    }
    return 'Not logged in';
});



// 🔹 SISTEM STOK OPNAME
// =====================
Route::get('/event/dashboard', [EventDashboardController::class, 'index'])
    ->middleware('stokopname.auth')
    ->name('event.dashboard');

Route::post('/event/logout', function () {
    session()->forget('stokopname_logged_in');
    return redirect('/')->with('success', 'Logout berhasil.');
})->name('event.logout');

Route::middleware('stokopname.auth')->group(function () {
Route::get('/event/dashboard', [EventDashboardController::class, 'index'])->name('event.dashboard');


// API endpoints used by SPA
Route::get('/event/api/dashboard', [EventDashboardController::class, 'apiDashboard']);
Route::get('/api/dashboard', [EventDashboardController::class, 'getDashboardData']);
Route::get('/event/api/items', [EventDashboardController::class, 'apiItems']);
Route::get('/event/api/transactions', [EventDashboardController::class, 'apiTransactions']);
Route::get('/event/api/transactions-by-date/{date}', [EventDashboardController::class, 'apiTransactionsByDate']);
Route::get('/event/api/recommendations', [EventDashboardController::class, 'apiRecommendations']);
});

// API route untuk SPA tab
Route::get('/api/items', [EventDashboardController::class, 'getItems']);
Route::get('/api/transactions', [EventDashboardController::class, 'getTransactions']);
Route::post('/api/transactions', [EventDashboardController::class, 'storeTransaction']);
Route::get('/api/report', [EventDashboardController::class, 'getReport']);

Route::get('/event/tabs/{tab}', function ($tab) {
    $path = resource_path("views/event/tabs/{$tab}.blade.php");
    if (!File::exists($path)) {
        abort(404);
    }
    return view("event.tabs.{$tab}");
});

// API GUDANG
Route::prefix('api/items')->group(function () {
    Route::get('/', [App\Http\Controllers\EventDashboardController::class, 'getItems']);
    Route::post('/', [App\Http\Controllers\EventDashboardController::class, 'storeItem']);
    Route::put('/{id}', [App\Http\Controllers\EventDashboardController::class, 'updateItem']);
    Route::delete('/{id}', [App\Http\Controllers\EventDashboardController::class, 'deleteItem']);
});

// API TRANSAKSI
Route::get('/event/tabs/transaksi', [TransactionController::class, 'index'])->name('transaksi.index');
Route::get('/event/tabs/tambahtransaksi', [TransactionController::class, 'create'])->name('transaksi.create');
Route::post('/event/tabs/transaksi', [TransactionController::class, 'store'])->name('transaksi.store');

