<?php

use App\Http\Controllers\PasteController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', [PasteController::class, 'create'])->name('pastes.create');
Route::post('/', [PasteController::class, 'store'])->name('pastes.store');
Route::get('/{slug}', [PasteController::class, 'show'])->name('pastes.show');