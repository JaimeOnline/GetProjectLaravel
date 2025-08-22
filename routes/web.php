<?php

use App\Http\Controllers\ActivityController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('activities/search', [ActivityController::class, 'search'])->name('activities.search');
Route::resource('activities', ActivityController::class);
Route::get('activities/{activity}/comments', [ActivityController::class, 'showComments'])->name('activities.comments');
Route::post('activities/{activity}/comments', [ActivityController::class, 'storeComment'])->name('activities.comments.store');
Route::delete('comments/{comment}', [ActivityController::class, 'destroyComment'])->name('comments.destroy');

// Rutas específicas para requerimientos
Route::post('activities/{activity}/requirements', [ActivityController::class, 'storeRequirements'])->name('activities.requirements.store');
Route::delete('requirements/{requirement}', [ActivityController::class, 'destroyRequirement'])->name('requirements.destroy');

// Rutas específicas para comentarios desde la pestaña de edición
Route::post('activities/{activity}/comments-tab', [ActivityController::class, 'storeCommentsFromTab'])->name('activities.comments.tab.store');

// Rutas para correos
Route::get('activities/{activity}/emails', [ActivityController::class, 'showEmails'])->name('activities.emails');
Route::post('activities/{activity}/emails', [ActivityController::class, 'storeEmail'])->name('activities.emails.store');
Route::delete('emails/{email}', [ActivityController::class, 'destroyEmail'])->name('emails.destroy');
Route::get('emails/{email}/attachment/{fileIndex}', [ActivityController::class, 'downloadAttachment'])->name('emails.download');

