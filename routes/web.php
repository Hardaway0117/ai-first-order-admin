<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::get('/locale/{locale}', function (\Illuminate\Http\Request $request, string $locale) {
    if (array_key_exists($locale, config('app.supported_locales'))) {
        session(['locale' => $locale]);
        // 登入者的語系偏好存進帳號，跨裝置、跨登入都記得
        $request->user()?->forceFill(['locale' => $locale])->save();
    }

    return back();
})->name('locale.switch');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', \App\Http\Controllers\ProductController::class)->except('show');
    Route::resource('customers', \App\Http\Controllers\CustomerController::class)->except('show');
    Route::resource('orders', \App\Http\Controllers\OrderController::class)->only(['index', 'create', 'store', 'show']);
    Route::patch('/orders/{order}/status', [\App\Http\Controllers\OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::resource('users', \App\Http\Controllers\UserController::class)->except('show');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
