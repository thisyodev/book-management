<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\BookController;

// Redirect root to web login
Route::redirect('/', '/book');

// Web login/register pages that use API auth
Route::get('/login', function () {
    return view('auth.web-login');
})->name('login');

Route::get('/register', function () {
    return view('auth.web-register');
})->name('register');

// Server-side book management (no auth required for now)
Route::resource('books', BookController::class);

// Friendly alias for books index
Route::get('/book', [BookController::class, 'index']);

require __DIR__.'/auth.php';
