<?php

use App\Http\Controllers\BigCommerceController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('bigcommerce.api')->get('/bigcommerce', [BigCommerceController::class, 'index'])->name('bigcommer.index');
Route::post("sync-products", [BigCommerceController::class, 'syncProducts'])->name('bigcommerce.sync-products');