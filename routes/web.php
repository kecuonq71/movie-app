<?php

use App\Http\Controllers\MoviesController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('index');
// });

// Route::get('/movie', function () {
//     return view('show');
// });

Route::get('/', [MoviesController::class, 'index'])->name('movies.index');
Route::get('/movies/{movie}', [MoviesController::class, 'show'])->name('movies.show');
