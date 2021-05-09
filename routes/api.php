<?php

use App\Http\Controllers\sporteventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//sportevent routes
Route::middleware('api')->group(function () {
    Route::get('search', [sporteventController::class, 'search'])->name('sportevent.search');

    //pages api
    Route::get('organisator-city', [sporteventController::class, 'getcity'])->name('sportevent.getcity');
    Route::get('sportevent-titles', [sporteventController::class, 'getAllByTitle'])->name('sportevent.getAllByTitle');
    Route::get('organisators', [sporteventController::class, 'getAllOrganization'])->name('sportevent.getAllOrganization');
});
