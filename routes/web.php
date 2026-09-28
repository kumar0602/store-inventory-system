<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\OrderController;

Route::get('/billing', [OrderController::class, 'indexView']);

Route::get('/', [OrderController::class, 'indexView']);