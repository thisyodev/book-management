<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\BookController;

// Redirect root to books index
Route::redirect('/', '/books');

// Web login/register pages that use API auth
Route::get('/login', function () {
    return view('auth.web-login');
})->name('login');

Route::get('/register', function () {
    return view('auth.web-register');
})->name('register');

// Server-side book management (no auth required for now)
Route::resource('books', BookController::class);

require __DIR__.'/auth.php';
