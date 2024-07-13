<?php

use Illuminate\Support\Facades\Route;



Route::get("login", [App\Http\Controllers\Manager\AuthController::class, 'login'])->name('login');
Route::post("login", [App\Http\Controllers\Manager\AuthController::class, 'attempt'])->name('login.attempt');

Route::middleware('manager')->group(function(){
    Route::get('/', [App\Http\Controllers\Manager\DashboardController::class, 'index'])->name('dashboard');
    Route::post('upload', [App\Http\Controllers\Manager\FileUploadController::class, 'uploadFile'])->name('upload.file');

    Route::post('/save-post', [App\Http\Controllers\Manager\ManagePostController::class, 'store'])->name('save.post');
    Route::get('/medias', [App\Http\Controllers\Manager\ManagePostController::class, 'medias'])->name('medias');
    Route::get('/view/{id}', [App\Http\Controllers\Manager\ManagePostController::class, 'assign'])->name('view');
    Route::post('/view/{id}', [App\Http\Controllers\Manager\ManagePostController::class, 'update'])->name('update');
});
