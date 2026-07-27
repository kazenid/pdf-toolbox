<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;

Route::get('/', [PdfController::class, 'index']);
Route::post('/pdf/compress', [PdfController::class, 'compress']);
Route::post('/pdf/convert-to-jpg', [PdfController::class, 'convertToJpg']);
Route::post('/pdf/merge', [PdfController::class, 'merge']);
Route::post('/pdf/split', [PdfController::class, 'split']);
Route::post('/pdf/manage-pages', [PdfController::class, 'managePages']);
