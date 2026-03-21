<?php

use App\Http\Controllers\API\PackageController;
use Illuminate\Support\Facades\Route;

Route::get('/packages', [PackageController::class, 'index']);
