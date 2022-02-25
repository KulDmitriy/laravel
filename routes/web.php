<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Models\User;

Route::get('/', function () {
    App::setLocale('ru');
    return view('welcome');
});

Route::get('/dashboard', [AuthenticatedSessionController::class, 'showDashboard'])
                ->middleware('auth')
                ->name('dashboard');

Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function(){
    Route::get('/edit-user/{id}', [AuthenticatedSessionController::class, 'userEdit'])
                ->name('user-info-edit');

    Route::post('/edit-user/{id}', [AuthenticatedSessionController::class, 'userEditSubmit'])
                ->name('user-info-edit-submit');

    Route::post('/users-info', [AuthenticatedSessionController::class, 'showUsersInfoByFilter'])
                ->name('show-users-info-by-filter');

    Route::post('/users-info-abc', [AuthenticatedSessionController::class, 'showUsersInfoByAbc'])
                ->name('show-users-info-by-abc');
});

Route::get('/language/{lg}', function ($lg) {
    App::setLocale($lg);
    return view('welcome');
});

Route::get('/language/{lang}', [AuthenticatedSessionController::class, 'switchLang'])
                ->name('lang-switch');

require __DIR__.'/auth.php';
