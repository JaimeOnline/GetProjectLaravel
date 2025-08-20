<?php

use App\Http\Controllers\ActivityController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('activities', ActivityController::class);
Route::get('activities/{activity}/comments', [ActivityController::class, 'showComments'])->name('activities.comments');
Route::post('activities/{activity}/comments', [ActivityController::class, 'storeComment'])->name('activities.comments.store');
Route::delete('comments/{comment}', [ActivityController::class, 'destroyComment'])->name('comments.destroy');
Route::delete('requirements/{requirement}', [ActivityController::class, 'destroyRequirement'])->name('requirements.destroy');

