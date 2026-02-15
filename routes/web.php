<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;

// Redirect root to web login
Route::redirect('/', '/web-login');

// Web login page that uses API auth
Route::get('/web-login', function () {
    return view('auth.web-login');
})->name('web.login');

// Server-side book management (no auth required for now)
Route::resource('books', BookController::class);

require __DIR__.'/auth.php';
