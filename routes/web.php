<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::get('/locale/{locale}', function (string $locale) {
    if (array_key_exists($locale, config('app.supported_locales'))) {
        session(['locale' => $locale]);
    }

    return back();
})->name('locale.switch');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 第二階段將以資源控制器（CRUD）取代下列 placeholder 頁面
    Route::view('/products', 'products.index')->name('products.index');
    Route::view('/customers', 'customers.index')->name('customers.index');
    Route::view('/orders', 'orders.index')->name('orders.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
