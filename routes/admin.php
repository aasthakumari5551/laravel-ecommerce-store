<?php

use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
     ->name('admin.')
     ->middleware(['auth', 'admin'])
     ->group(function () {

    Route::get('/', fn () => redirect()->route('admin.products.index'));

    // Products full CRUD
    Route::resource('products', ProductController::class);
});