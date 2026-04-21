<?php

use App\Http\Controllers\FormController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/forms/{slug}', [FormController::class, 'show'])->name('forms.show');
Route::post('/forms/{slug}', [FormController::class, 'submit'])->name('forms.submit');
Route::get('/forms/{slug}/thank-you', [FormController::class, 'thankYou'])->name('forms.thank-you');

