<?php

use App\Http\Controllers\LoginController;

Route::get('/api/api/user', [LoginController::class, 'user'])->name('user.login');
