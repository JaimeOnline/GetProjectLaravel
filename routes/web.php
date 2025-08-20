<?php

use App\Http\Controllers\ActivityController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('activities', ActivityController::class);
Route::get('activities/{activity}/comments', [ActivityController::class, 'showComments'])->name('activities.comments');

