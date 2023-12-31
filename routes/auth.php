<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::post('/definitive_regist_token', [RegisteredUserController::class, 'token_check'])
  ->middleware('guest')
  ->name('token_check');
Route::post('/definitive_regist', [RegisteredUserController::class, 'store'])
  ->middleware('guest')
  ->name('definitive_regist');
// Route::post('/register', [RegisteredUserController::class, 'store'])
//   ->middleware('guest')
//   ->name('register');
Route::post('/temporary_regist', [RegisteredUserController::class, 'temporary_regist'])
  ->middleware('guest')
  ->name('temporary_regist');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
  ->middleware('guest')
  ->name('login_p');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
  ->middleware('guest')
  ->name('password.email');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
  ->middleware('guest')
  ->name('password.store');

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
  ->middleware(['auth', 'signed', 'throttle:6,1'])
  ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
  ->middleware(['auth', 'throttle:6,1'])
  ->name('verification.send');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
  ->middleware('auth')
  ->name('logout');

// Route::get('/user', function (Request $request) {
//   \Log::info('jjj');
//   return $request->user();
// });
