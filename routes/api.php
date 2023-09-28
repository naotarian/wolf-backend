<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Api\RoomController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/pusher_test', [RegisteredUserController::class, 'pusher_test']);
Route::post('/rooms/create', [RoomController::class, 'create']);
Route::get('/rooms/list', [RoomController::class, 'list']);
Route::post('/room/participation', [RoomController::class, 'participation']);
Route::post('/room/leaving', [RoomController::class, 'leaving']);
Route::post('/room/dissolution', [RoomController::class, 'dissolution']);
Route::post('/room/voiceUser/add', [RoomController::class, 'voiceUserAdd']);
Route::post('/room/voiceUser/remove', [RoomController::class, 'voiceUserRemove']);
Route::post('/room/pre_start', [RoomController::class, 'pre_start']);
Route::post('/room/select_position', [RoomController::class, 'select_position']);
Route::post('/room/ramdom_position', [RoomController::class, 'ramdom_position']);
Route::post('/room/confirmed', [RoomController::class, 'confirmed']);

require __DIR__ . '/auth.php';
