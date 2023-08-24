<?php

use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CsvExportController;



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


Auth::routes();


Route::redirect('/', '/home');
Route::get('/home', [HomeController::class, 'index'])->name('home');



Route::group(['middleware' => 'check.admin'], function () {
    // Routes accessible only by admin users
    Route::get('/users', [AdminController::class, 'index'])->name('index');

    Route::get('/users/{id}', [AdminController::class, 'show'])->name('user');
    Route::get('/users/{id}/edit', [AdminController::class, 'edit'])->name('users.edit');

    Route::delete('/users/bulk-delete', [AdminController::class, 'bulkDelete'])->name('users.bulkDelete');
    Route::put('/users/{id}/update', [AdminController::class, 'updateProfile'])->name('update-profile');

    Route::get('/search', [SearchController::class, 'search'])->name('search');
    Route::get('/search/suggest', [SearchController::class, 'suggest'])->name('search.suggest');

    Route::get('/export-csv', [CsvExportController::class, 'export'])->name('export');
    Route::get('/export-csv-all', [CsvExportController::class, 'export_all'])->name('export_all');

});


