<?php

use App\Http\Controllers\Api\V1\PengukuranController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->middleware('throttle:60,1')->group(function () {
    Route::post('/pengukuran', [PengukuranController::class, 'store'])->name('pengukuran.store');
});
