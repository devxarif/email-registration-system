<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;

Route::post('/register', [RegisterController::class, 'register']);
