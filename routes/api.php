<?php

use App\Http\Controllers\AlamatController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });



// Register dan Login
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'authenticate']);

// Middleware User
Route::group(['middleware' => ['AuthCek']], function()
{

    // Middleware Admin
    Route::group(['middleware' => ['AdminCek']], function()
    {
        // Kelola User (Admin)
        Route::get('user', [UserController::class, 'index']);
        Route::get('detail-user-admin/{user}', [UserController::class, 'detailAdmin']);
        Route::post('update-user-admin/{user}', [UserController::class, 'updateAdmin']);
        Route::post('delete-user-admin/{user}', [UserController::class, 'destroyAdmin']);

        // Kelola Menu (Admin)
        Route::post('add-menu', [MenuController::class, 'store']);
        Route::post('update-menu/{id}', [MenuController::class, 'update']);
        Route::post('delete-menu/{id}', [MenuController::class, 'destroy']);

        // Kelola Alamat (Admin)
        Route::get('alamat-admin/{user}', [AlamatController::class, 'indexAdmin']);
        // route detail pakai yang user

        // Kelola Pesanan (Admin)
        Route::get('pesanan-admin', [PesananController::class, 'indexAdmin']);
        Route::post('update-pesanan-admin/{pesanan}', [PesananController::class, 'updateAdmin']);

    });

    // Kelola User
    Route::post('logout', [UserController::class, 'addToList']);
    Route::get('detail-user', [UserController::class, 'detail']);
    Route::post('update-user', [UserController::class, 'update']);
    Route::post('delete-user', [UserController::class, 'destroy']);

    // Kelola Item
    Route::get('item', [ItemController::class, 'index']);
    Route::post('add-item', [ItemController::class, 'store']);
    Route::post('update-item', [ItemController::class, 'update']);
    Route::post('delete-item', [ItemController::class, 'destroy']);

    // Kelola Alamat
    Route::get('alamat', [AlamatController::class, 'index']);
    Route::get('detail-alamat/{alamat}', [AlamatController::class, 'show']);
    Route::post('add-alamat', [AlamatController::class, 'store']);
    Route::post('update-alamat/{id}', [AlamatController::class, 'update']);
    Route::post('delete-alamat/{id}', [AlamatController::class, 'destroy']);

    // Kelola Pesanan
    Route::get('pesanan', [PesananController::class, 'index']);
    Route::post('add-pesanan', [PesananController::class, 'store']);
    Route::post('update-pesanan/{pesanan}', [PesananController::class, 'update']);

});

// Lihat Menu
Route::get('menu', [MenuController::class, 'index']);
Route::get('menu/{menu}', [MenuController::class, 'show']);

// Lihat Pesanan
Route::get('pesanan/{pesanan}', [PesananController::class, 'show']);
