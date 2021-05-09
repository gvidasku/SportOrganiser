<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\AdminController;
use App\Http\Controllers\Auth\AuthorController;
use App\Http\Controllers\cityController;
use App\Http\Controllers\organisatorController;
use App\Http\Controllers\sporteventApplicationController;
use App\Http\Controllers\sporteventController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\savedsporteventController;
use Illuminate\Support\Facades\Route;

//public routes
Route::get('/', [PostController::class, 'index'])->name('post.index');
Route::get('/sportevent/{sportevent}', [PostController::class, 'show'])->name('post.show');
Route::get('Organisator/{Organisator}', [AuthorController::class, 'Organisator'])->name('account.Organisator');

//return vue page
Route::get('/search', [sporteventController::class, 'index'])->name('sportevent.index');

//auth routes
Route::middleware('auth')->prefix('account')->group(function () {
  //every auth routes AccountController
  Route::get('logout', [AccountController::class, 'logout'])->name('account.logout');
  Route::get('overview', [AccountController::class, 'index'])->name('account.index');
  Route::get('deactivate', [AccountController::class, 'deactivateView'])->name('account.deactivate');
  Route::get('change-password', [AccountController::class, 'changePasswordView'])->name('account.changePassword');
  Route::delete('delete', [AccountController::class, 'deleteAccount'])->name('account.delete');
  Route::put('change-password', [AccountController::class, 'changePassword'])->name('account.changePassword');
  //savedsportevents
  Route::get('my-saved-sportevents', [savedsporteventController::class, 'index'])->name('savedsportevent.index');
  Route::get('my-saved-sportevents/{id}', [savedsporteventController::class, 'store'])->name('savedsportevent.store');
  Route::delete('my-saved-sportevents/{id}', [savedsporteventController::class, 'destroy'])->name('savedsportevent.destroy');
  //applysportevents
  Route::get('apply-sportevent', [AccountController::class, 'applysporteventView'])->name('account.applysportevent');
  Route::post('apply-sportevent', [AccountController::class, 'applysportevent'])->name('account.applysportevent');

  //Admin Role Routes
  Route::group(['middleware' => ['role:admin']], function () {
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('account.dashboard');
    Route::get('view-all-users', [AdminController::class, 'viewAllUsers'])->name('account.viewAllUsers');
    Route::delete('view-all-users', [AdminController::class, 'destroyUser'])->name('account.destroyUser');

    Route::get('city/{city}/edit', [cityController::class, 'edit'])->name('city.edit');
    Route::post('city', [cityController::class, 'store'])->name('city.store');
    Route::put('city/{id}', [cityController::class, 'update'])->name('city.update');
    Route::delete('city/{id}', [cityController::class, 'destroy'])->name('city.destroy');
  });

  //Author Role Routes
  Route::group(['middleware' => ['role:author']], function () {
    Route::get('author-section', [AuthorController::class, 'authorSection'])->name('account.authorSection');

    Route::get('sportevent-application/{id}', [sporteventApplicationController::class, 'show'])->name('sporteventApplication.show');
    Route::delete('sportevent-application', [sporteventApplicationController::class, 'destroy'])->name('sporteventApplication.destroy');
    Route::get('sportevent-application', [sporteventApplicationController::class, 'index'])->name('sporteventApplication.index');

    Route::get('post/create', [PostController::class, 'create'])->name('post.create');
    Route::post('post', [PostController::class, 'store'])->name('post.store');
    Route::get('post/{post}/edit', [PostController::class, 'edit'])->name('post.edit');
    Route::put('post/{post}', [PostController::class, 'update'])->name('post.update');
    Route::delete('post/{post}', [PostController::class, 'destroy'])->name('post.destroy');

    Route::get('organisator/create', [organisatorController::class, 'create'])->name('organisator.create');
    Route::put('organisator/{id}', [organisatorController::class, 'update'])->name('organisator.update');
    Route::post('organisator', [organisatorController::class, 'store'])->name('organisator.store');
    Route::get('organisator/edit', [organisatorController::class, 'edit'])->name('organisator.edit');
    Route::delete('organisator', [organisatorController::class, 'destroy'])->name('organisator.destroy');
  });

  //User Role routes
  Route::group(['middleware' => ['role:user']], function () {
    Route::get('become-Organisator', [AccountController::class, 'becomeOrganisatorView'])->name('account.becomeOrganisator');
    Route::post('become-Organisator', [AccountController::class, 'becomeOrganisator'])->name('account.becomeOrganisator');
  });
});
