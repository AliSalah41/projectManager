<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['guest'])->group(function (){

    Route::post('login',[AuthController::class,'login'])->name('login');
});

Route::middleware(['auth:sanctum'])->group(function (){

    Route::get('project.index',[ProjectController::class,'index'])->name('project.index');
    Route::get('project.show/{project}',[ProjectController::class,'show'])->name('project.show');
    Route::post('project.store',[ProjectController::class,'store'])->name('project.store');

});
