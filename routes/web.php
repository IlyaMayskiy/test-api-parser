<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataController;

Route::get('/', [DataController::class, 'index'])->name('data.index');

Route::get('/fetch', [DataController::class, 'fetch'])->name('data.fetch');
Route::get('/data/fetch/{entity}', [DataController::class, 'fetchEntity'])->whereIn('entity', ['sales', 'orders', 'stocks', 'incomes'])->name('data.fetch.entity');

Route::get('/{entity}', [DataController::class, 'show'])
    ->whereIn('entity', ['sales', 'orders', 'stocks', 'incomes'])
    ->name('data.show');