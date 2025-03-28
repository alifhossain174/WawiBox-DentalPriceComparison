<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PriceComparisonController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes([
    'login' => false,
    'register' => false, // Registration Routes...
    'reset' => false, // Password Reset Routes...
    'verify' => false, // Email Verification Routes...
]);

Route::get('/', [PriceComparisonController::class, 'homePage'])->name('HomePage');
Route::post('add/another/product', [PriceComparisonController::class, 'addAnotherProduct'])->name('AddAnotherProduct');
Route::post('compare/suppliers/price', [PriceComparisonController::class, 'compareSupplierPrice'])->name('CompareSupplierPrice');

Route::get('/home', [HomeController::class, 'index'])->name('home');
