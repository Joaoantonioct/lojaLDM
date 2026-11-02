<?php

use App\Http\Controllers\PdvProductController;
use App\Http\Controllers\PdvSyncController;
use Illuminate\Support\Facades\Route;

Route::prefix('pdv')->middleware(['pdv.auth', 'throttle:600,1'])->group(function (): void {
    Route::post('/sync', [PdvSyncController::class, 'store'])->name('pdv.sync');
    Route::post('/identifiers', [PdvProductController::class, 'identifiers'])->name('pdv.identifiers');
    Route::post('/products', [PdvProductController::class, 'store'])->name('pdv.products.store');
    Route::get('/products/pending', [PdvProductController::class, 'pending'])->name('pdv.products.pending');
    Route::post('/products/ack', [PdvProductController::class, 'acknowledge'])->name('pdv.products.ack');
    Route::get('/site-orders/pending', [PdvProductController::class, 'pendingSiteOrders'])->name('pdv.site-orders.pending');
    Route::post('/site-orders/ack', [PdvProductController::class, 'acknowledgeSiteOrder'])->name('pdv.site-orders.ack');
    Route::get('/site-orders/history', [PdvProductController::class, 'siteOrderHistory'])->name('pdv.site-orders.history');
    Route::get('/products/review', [PdvProductController::class, 'review'])->name('pdv.products.review');
    Route::post('/products/link', [PdvProductController::class, 'link'])->name('pdv.products.link');
});
