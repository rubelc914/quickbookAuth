<?php

use App\Http\Controllers\BooksController;
use Illuminate\Support\Facades\Route;

Route::get('/',function(){
    return 'no code';
})->name('home');

Route::get('/quickbooks/connect', [BooksController::class, 'connect'])->name('quickbooks.connect');
Route::get('/quickbooks/callback', [BooksController::class, 'callback'])->name('quickbooks.callback');

