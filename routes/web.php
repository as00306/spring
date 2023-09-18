<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InputController;


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


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();

    Route::get('/input', [InputController::class, 'index']);
    Route::post('/import', [InputController::class, 'import'])->name('import');
});
