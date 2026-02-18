<?php

use App\Http\Controllers\ParseHomeownersFromCsvController;
use Illuminate\Support\Facades\Route;

Route::post('/homeowners/parser', ParseHomeownersFromCsvController::class);
