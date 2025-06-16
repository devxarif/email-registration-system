<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GmailController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/gmail/authorize', [GmailController::class, 'authorize'])->name('gmail.authorize');
Route::get('/gmail/send', [GmailController::class, 'sendEmail'])->name('gmail.send');

Route::get('/getcode', [GmailController::class, 'callback'])->name('gmail.callback');
