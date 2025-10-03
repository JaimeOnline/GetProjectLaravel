<?php

namespace App\Exports;

use App\Models\Activity;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ActivitiesExport implements FromCollection, WithHeadings
{
    protected $activities;

    protected $statusFilter;

    public function __construct($activities, $statusFilter = null)
    {
        $this->activities = $activities;
        $this->statusFilter = $statusFilter;
    }

    public function collection()
    {
        return $this->activities->map(function ($activity) {
            // Si hay filtro de estado, solo mostrar ese/estos estados
            if ($this->statusFilter) {
                $filteredStatuses = $activity->statuses->filter(function ($status) {
                    if (is_array($this->statusFilter)) {
                        return in_array($status->name, $this->statusFilter);
                    }
                    return $status->name == $this->statusFilter;
                });
                $estados = $filteredStatuses->map(function ($status) {
                    return $status->label ?: $status->name;
                })->implode(', ');
                // Fallback al campo antiguo si no hay en la relación
                if (!$estados && ($activity->status_label ?? $activity->status ?? '')) {
                    // Solo mostrar el campo antiguo si coincide con el filtro
                    if (is_array($this->statusFilter)) {
                        if (in_array($activity->status, $this->statusFilter)) {
                            $estados = $activity->status_label ?? $activity->status ?? '';
                        }
                    } else {
                        if ($activity->status == $this->statusFilter) {
                            $estados = $activity->status_label ?? $activity->status ?? '';
                        }
                    }
                }
            } else {
                $estados = $activity->statuses->count()
                    ? $activity->statuses->map(function ($status) {
                        return $status->label ?: $status->name;
                    })->implode(', ')
                    : ($activity->status_label ?? $activity->status ?? '');
            }

            return [
                'ID' => $activity->id,
                'Caso' => $activity->caso,
                'Nombre' => $activity->name,
                'Descripción' => $activity->description,
                'Estatus Operacional' => $activity->estatus_operacional, // <-- NUEVA COLUMNA
                'Fecha Recepción' => $activity->fecha_recepcion,
                'Prioridad' => $activity->prioridad,
                'Orden Analista' => $activity->orden_analista,
                'Estados' => $estados,
                'Analistas' => $activity->analistas->pluck('name')->implode(', '),
            ];
        })->filter(function ($row) {
            // Solo exportar filas donde la columna "Estados" no esté vacía
            return !empty($row['Estados']);
        })->values();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Caso',
            'Nombre',
            'Descripción',
            'Estatus Operacional', // <-- NUEVO ENCABEZADO
            'Fecha Recepción',
            'Prioridad',
            'Orden Analista',
            'Estados',
            'Analistas',
        ];
    }
}
