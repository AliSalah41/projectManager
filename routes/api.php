<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DependencyController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
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

    //Project
    Route::get('project.index',[ProjectController::class,'index'])->name('project.index');
    Route::get('project.show/{project}',[ProjectController::class,'show'])->name('project.show');
    Route::post('project.store',[ProjectController::class,'store'])->name('project.store');

    //Task
    Route::get('task.show/{id}',[TaskController::class,'show'])->name('task.show');
    Route::post('task.store',[TaskController::class,'store'])->name('task.store');
    Route::put('task.update/{id}',[TaskController::class,'update'])->name('task.update');

    //History 
    Route::get('history.show/{id}',[HistoryController::class,'show'])->name('history.show');

    //Task_Dependency
    Route::post('task_dependency.store',[DependencyController::class,'store'])->name('task_dependency.store');
    Route::delete('task_dependency.delete',[DependencyController::class,'destroy'])->name('task_dependency.delete');

});
