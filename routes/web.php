<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\RequirementController;

Route::get('/', function () {
    return redirect()->route('activities.index');
});

Route::get('activities/search', [ActivityController::class, 'search'])->name('activities.search');
Route::get('/activities/export', [ActivityController::class, 'export'])->name('activities.export');
Route::get('activities/excel-template', [ActivityController::class, 'downloadExcelTemplate'])->name('activities.excelTemplate');
Route::post('activities/import-excel', [ActivityController::class, 'importExcel'])->name('activities.importExcel');
Route::get('/activities/export-word', [ActivityController::class, 'exportWord'])->name('activities.exportWord');
Route::put('activities/{activity}/analysts', [ActivityController::class, 'updateAnalysts'])->name('activities.updateAnalysts');
Route::get('/activities/analistas', [ActivityController::class, 'porAnalistas'])->name('activities.analistas');
Route::get('/activities/analistas/{analista}/actividades', [ActivityController::class, 'actividadesPorAnalista'])->name('activities.analistas.actividades');
Route::get('/activities/hoy', [ActivityController::class, 'enAtencionHoy'])->name('activities.hoy');
Route::get('/activities/insumos', [ActivityController::class, 'enEsperaInsumos'])->name('activities.insumos');
Route::get('/activities/insumos/{activity}', [ActivityController::class, 'insumoItem'])->name('activities.insumos.item');

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
Route::get('/emails/historico', [ActivityController::class, 'showAllEmails'])->name('emails.historico');

// Rutas para gestión completa de requerimientos
Route::get('requirements-report', [RequirementController::class, 'report'])->name('requirements.report');
Route::get('requirements-report/export', [RequirementController::class, 'exportReport'])->name('requirements.report.export');
Route::resource('requirements', RequirementController::class);
Route::patch('requirements/{requirement}/mark-received', [RequirementController::class, 'markAsReceived'])->name('requirements.mark-received');
Route::patch('requirements/{requirement}/mark-pending', [RequirementController::class, 'markAsPending'])->name('requirements.mark-pending');

// Rutas para actualización en linea de Orden y Prioridad
Route::patch('/activities/{activity}/inline-update', [App\Http\Controllers\ActivityController::class, 'inlineUpdate'])
    ->name('activities.inline-update');

// Ruta para actualización en línea de estatus_operacional
Route::patch('/activities/{activity}/inline-estatus', [App\Http\Controllers\ActivityController::class, 'inlineUpdateEstatus'])
    ->name('activities.inline-estatus');

// Rutas Proyectos
Route::get('/proyectos', [ProyectoController::class, 'index'])->name('projects.index');
Route::get('/proyectos/crear', [ProyectoController::class, 'create'])->name('projects.create');
Route::post('/proyectos', [ProyectoController::class, 'store'])->name('projects.store');

Route::get('/activities/{activity}/json', [ActivityController::class, 'showJson']);

// Ruta para reordenar actividades por analista (drag & drop)
Route::post('/activities/reorder', [ActivityController::class, 'reorder'])->name('activities.reorder');
