<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\RequirementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('activities/search', [ActivityController::class, 'search'])->name('activities.search');
Route::resource('activities', ActivityController::class);

// Rutas para gestión de estados múltiples
Route::put('activities/{activity}/statuses', [ActivityController::class, 'updateStatuses'])->name('activities.statuses.update');
Route::get('statuses', [ActivityController::class, 'getStatuses'])->name('statuses.index');
Route::get('activities/{activity}/statuses', [ActivityController::class, 'getActivityStatuses'])->name('activities.statuses.show');
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

// Rutas para gestión completa de requerimientos
Route::get('requirements-report', [RequirementController::class, 'report'])->name('requirements.report');
Route::get('requirements-report/export', [RequirementController::class, 'exportReport'])->name('requirements.report.export');
Route::resource('requirements', RequirementController::class);
Route::patch('requirements/{requirement}/mark-received', [RequirementController::class, 'markAsReceived'])->name('requirements.mark-received');
Route::patch('requirements/{requirement}/mark-pending', [RequirementController::class, 'markAsPending'])->name('requirements.mark-pending');

// Rutas para actualización en linea de Orden y Prioridad
Route::patch('/activities/{activity}/inline-update', [App\Http\Controllers\ActivityController::class, 'inlineUpdate'])
    ->name('activities.inline-update');
