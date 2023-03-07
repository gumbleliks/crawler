<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\ImportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/** Start Form */
Route::get('/', function () {
    return view('index');
});

/** Read File and crawl urls */
Route::post('/import', [ImportController::class, 'import'])->name('import');

/** File Download */
Route::get('/get/{fileName}', [FileController::class, 'getFile'])->name('getFile');

