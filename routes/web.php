<?php

use App\Http\Controllers\InvoiceController;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::group(['middleware' => ['web', Authenticate::class]], function () {
   Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
});
