<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DistributorController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::post('uploadfile', [DistributorController::class, 'openFile'])->name('openfile');
Route::get('viewer', [DistributorController::class, 'viewer'])->name('viewer');


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
