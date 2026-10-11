<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\AffiliateClickController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('produk', [CatalogController::class, 'index'])->name('products.index');
Route::get('produk/{product:slug}', [CatalogController::class, 'show'])->name('products.show');

Route::get('go/{product:slug}', [AffiliateClickController::class, 'redirect'])->name('affiliate.go');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.attempt');
    });

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('statistik', [StatisticsController::class, 'index'])->name('statistics.index');

        Route::get('review', [ReviewController::class, 'index'])->name('reviews.index');

        Route::get('pengaturan', [SettingController::class, 'index'])->name('settings.index');
        Route::put('pengaturan', [SettingController::class, 'update'])->name('settings.update');

        Route::patch('categories/{category}/toggle', [CategoryController::class, 'toggleStatus'])
            ->name('categories.toggle');
        Route::resource('categories', CategoryController::class)->except(['show']);

        Route::resource('products', ProductController::class)->except(['show']);
    });
});
