<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('dashboard', \App\Livewire\Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('thresholds', \App\Livewire\ThresholdSetting::class)
    ->middleware(['auth', 'verified'])
    ->name('thresholds');

// Sensor Management Routes
Route::middleware(['auth', 'verified'])->prefix('sensors')->name('sensors.')->group(function () {
    Route::get('/', \App\Livewire\Sensor\Index::class)->name('index');
    Route::get('/create', \App\Livewire\Sensor\Create::class)->name('create');
    Route::get('/{sensor}/edit', \App\Livewire\Sensor\Edit::class)->name('edit');
});

// Anggrek (Orchid) Management Routes
Route::middleware(['auth', 'verified'])->prefix('anggrek')->name('anggrek.')->group(function () {
    Route::get('/', \App\Livewire\Anggrek\Index::class)->name('index');
    Route::get('/create', \App\Livewire\Anggrek\Create::class)->name('create');
    Route::get('/{anggrek}/edit', \App\Livewire\Anggrek\Edit::class)->name('edit');
});

// Pengukuran (Measurements) Routes  
Route::middleware(['auth', 'verified'])->prefix('pengukuran')->name('pengukuran.')->group(function () {
    Route::get('/', \App\Livewire\Pengukuran\Index::class)->name('index');
    Route::get('/{pengukuran}', \App\Livewire\Pengukuran\Detail::class)->name('detail');
});

require __DIR__.'/settings.php';
