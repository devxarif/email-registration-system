<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GmailController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/gmail/authorize', [GmailController::class, 'authorize'])->name('gmail.authorize');
Route::get('/getcode', [GmailController::class, 'callback'])->name('gmail.callback');
