<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Requirement;
use App\Models\Activity;

class RequirementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Requirement::with(['activity']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('notas', 'like', "%{$search}%")
                    ->orWhereHas('activity', function ($actQuery) use ($search) {
                        $actQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('caso', 'like', "%{$search}%");
                    });
            });
        }

        $requirements = $query
            ->orderByRaw("
                CASE
                    WHEN status = 'pendiente' THEN 1
                    WHEN status = 'recibido' THEN 2
                    ELSE 3
                END
            ")
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        $activities = Activity::orderBy('name')->get();

        // Estadísticas
        $stats = [
            'total' => Requirement::count(),
            'pendientes' => Requirement::pendientes()->count(),
            'recibidos' => Requirement::recibidos()->count(),
        ];

        return view('requirements.index', compact('requirements', 'activities', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $activities = Activity::orderBy('name')->get();
        $selectedActivityId = $request->get('activity_id');

        return view('requirements.create', compact('activities', 'selectedActivityId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'description' => 'required|string|max:65535',
            'status' => 'required|in:pendiente,recibido',
            'fecha_recepcion' => 'nullable|date',
            'notas' => 'nullable|string|max:2000',
        ]);

        $data = $request->all();

        // Si el estado es recibido y no se proporcionó fecha, usar la fecha actual
        if ($data['status'] === 'recibido' && empty($data['fecha_recepcion'])) {
            $data['fecha_recepcion'] = now();
        }

        $requirement = Requirement::create($data);

        // Si el requerimiento es pendiente, agregar estado "En espera de insumos" a la actividad
        if ($requirement->status === 'pendiente') {
            $activity = $requirement->activity;
            $status = \App\Models\Status::where('name', 'en_espera_de_insumos')->first();
            if ($status && !$activity->hasStatus('en_espera_de_insumos')) {
                $activity->statuses()->attach($status->id);
            }
        }

        return redirect()->route('requirements.index')
            ->with('success', 'Requerimiento creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Requirement $requirement)
    {
        $requirement->load('activity');
        return view('requirements.show', compact('requirement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Requirement $requirement)
    {
        $activities = Activity::orderBy('name')->get();
        return view('requirements.edit', compact('requirement', 'activities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Requirement $requirement)
    {
        $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'description' => 'required|string|max:65535',
            'status' => 'required|in:pendiente,recibido',
            'fecha_recepcion' => 'nullable|date',
            'notas' => 'nullable|string|max:2000',
        ]);

        $data = $request->all();

        // Si cambia a recibido y no se proporcionó fecha, usar la fecha actual
        if ($data['status'] === 'recibido' && empty($data['fecha_recepcion']) && $requirement->status !== 'recibido') {
            $data['fecha_recepcion'] = now();
        }

        // Si cambia a pendiente, limpiar la fecha de recepción
        if ($data['status'] === 'pendiente') {
            $data['fecha_recepcion'] = null;
        }

        $requirement->update($data);

        $activity = $requirement->activity;
        $status = \App\Models\Status::where('name', 'en_espera_de_insumos')->first();

        if ($data['status'] === 'pendiente') {
            // Si hay al menos un requerimiento pendiente, asegúrate de que el estado esté presente
            if ($status && !$activity->hasStatus('en_espera_de_insumos')) {
                $activity->statuses()->attach($status->id);
            }
        } else {
            // Si ya no hay requerimientos pendientes, quita el estado y agrega "En ejecución"
            $pendientes = $activity->requirements()->where('status', 'pendiente')->count();
            if ($status && $pendientes === 0 && $activity->hasStatus('en_espera_de_insumos')) {
                $activity->statuses()->detach($status->id);

                // Agregar "En ejecución" si no lo tiene
                $ejecucion = \App\Models\Status::where('name', 'en_ejecucion')->first();
                if ($ejecucion && !$activity->hasStatus('en_ejecucion')) {
                    $activity->statuses()->attach($ejecucion->id);
                }
            }
        }

        return redirect()->route('requirements.index')
            ->with('success', 'Requerimiento actualizado exitosamente.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Requirement $requirement)
    {
        $activity = $requirement->activity;
        $requirement->delete();

        // Si ya no hay requerimientos pendientes, quita el estado "En espera de insumos" y agrega "En ejecución"
        $status = \App\Models\Status::where('name', 'en_espera_de_insumos')->first();
        $pendientes = $activity->requirements()->where('status', 'pendiente')->count();
        if ($status && $pendientes === 0 && $activity->hasStatus('en_espera_de_insumos')) {
            $activity->statuses()->detach($status->id);

            // Agregar "En ejecución" si no lo tiene
            $ejecucion = \App\Models\Status::where('name', 'en_ejecucion')->first();
            if ($ejecucion && !$activity->hasStatus('en_ejecucion')) {
                $activity->statuses()->attach($ejecucion->id);
            }
        }

        return redirect()->route('requirements.index')
            ->with('success', 'Requerimiento eliminado exitosamente.');
    }


    /**
     * Marcar requerimiento como recibido
     */
    public function markAsReceived(Requirement $requirement, Request $request)
    {
        $requirement->update([
            'status' => 'recibido',
            'fecha_recepcion' => now(),
        ]);

        // Si ya no hay requerimientos pendientes, quita el estado "En espera de insumos" y agrega "En ejecución"
        $activity = $requirement->activity;
        $status = \App\Models\Status::where('name', 'en_espera_de_insumos')->first();
        $pendientes = $activity->requirements()->where('status', 'pendiente')->count();
        if ($status && $pendientes === 0 && $activity->hasStatus('en_espera_de_insumos')) {
            $activity->statuses()->detach($status->id);

            // Agregar "En ejecución" si no lo tiene
            $ejecucion = \App\Models\Status::where('name', 'en_ejecucion')->first();
            if ($ejecucion && !$activity->hasStatus('en_ejecucion')) {
                $activity->statuses()->attach($ejecucion->id);
            }
        }


        // Si viene desde la edición de actividad, redirigir a la pestaña de requerimientos
        if ($request->has('from_activity')) {
            return redirect()->route('activities.edit', $requirement->activity_id)
                ->with('success', 'Requerimiento marcado como recibido.')
                ->with('active_tab', 'requirements');
        }

        return back()->with('success', 'Requerimiento marcado como recibido.');
    }


    /**
     * Marcar requerimiento como pendiente
     */
    public function markAsPending(Requirement $requirement, Request $request)
    {
        $requirement->update([
            'status' => 'pendiente',
            'fecha_recepcion' => null,
        ]);

        // Si hay al menos un requerimiento pendiente, asegúrate de que el estado esté presente
        $activity = $requirement->activity;
        $status = \App\Models\Status::where('name', 'en_espera_de_insumos')->first();
        if ($status && !$activity->hasStatus('en_espera_de_insumos')) {
            $activity->statuses()->attach($status->id);
        }

        // Si viene desde la edición de actividad, redirigir a la pestaña de requerimientos
        if ($request->has('from_activity')) {
            return redirect()->route('activities.edit', $requirement->activity_id)
                ->with('success', 'Requerimiento marcado como pendiente.')
                ->with('active_tab', 'requirements');
        }

        return back()->with('success', 'Requerimiento marcado como pendiente.');
    }


    /**
     * Mostrar reporte de requerimientos
     */
    public function report(Request $request)
    {
        $query = Requirement::with(['activity', 'activity.parent', 'activity.cliente']);

        // Filtros para el reporte

        // Estado por defecto: 'pendiente' si no se envía ninguno
        if (!$request->filled('status')) {
            $request->merge(['status' => 'pendiente']);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }

        if ($request->filled('cliente_id')) {
            $query->whereHas('activity', function ($q) use ($request) {
                $q->where('cliente_id', $request->cliente_id);
            });
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        if ($request->filled('fecha_recepcion_desde')) {
            $query->whereDate('fecha_recepcion', '>=', $request->fecha_recepcion_desde);
        }

        if ($request->filled('fecha_recepcion_hasta')) {
            $query->whereDate('fecha_recepcion', '<=', $request->fecha_recepcion_hasta);
        }

        // Ordenamiento: primero pendientes, luego recibidos, luego el resto;
        // dentro de cada grupo se respeta sort_by / sort_order
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $query->orderByRaw("
            CASE
                WHEN status = 'pendiente' THEN 1
                WHEN status = 'recibido' THEN 2
                ELSE 3
            END
        ");

        // Aplicar orden secundario solo si la columna es válida
        if (in_array($sortBy, ['created_at', 'fecha_recepcion', 'status'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $requirements = $query->get();

        $activities = Activity::orderBy('name')->get();
        $clientes = \App\Models\Cliente::orderBy('nombre')->get();

        // Estadísticas del reporte
        $stats = [
            'total' => $requirements->count(),
            'pendientes' => $requirements->where('status', 'pendiente')->count(),
            'recibidos' => $requirements->where('status', 'recibido')->count(),
            'tiempo_promedio_respuesta' => $this->calculateAverageResponseTime($requirements->where('status', 'recibido')),
            'requerimientos_vencidos' => $requirements->where('status', 'pendiente')
                ->filter(function ($req) {
                    return $req->created_at->diffInDays(now()) > 7;
                })->count(),
        ];

        // Agrupación por actividad
        $requirementsByActivity = $requirements->groupBy('activity.name');

        // Agrupación por estado y mes
        $requirementsByMonth = $requirements->groupBy(function ($requirement) {
            return $requirement->created_at->format('Y-m');
        });

        return view('requirements.report', compact(
            'requirements',
            'activities',
            'clientes',
            'stats',
            'requirementsByActivity',
            'requirementsByMonth'
        ));
    }

    /**
     * Calcular tiempo promedio de respuesta
     */
    private function calculateAverageResponseTime($receivedRequirements)
    {
        if ($receivedRequirements->count() === 0) {
            return 0;
        }

        $totalDays = $receivedRequirements->sum(function ($requirement) {
            return $requirement->created_at->diffInDays($requirement->fecha_recepcion);
        });

        return round($totalDays / $receivedRequirements->count(), 1);
    }

    /**
     * Exportar reporte a CSV
     */
    public function exportReport(Request $request)
    {
        $query = Requirement::with(['activity', 'activity.parent']);

        // Aplicar los mismos filtros que en el reporte
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        // Mismo orden que en el reporte: primero pendientes, luego recibidos, luego el resto
        $requirements = $query
            ->orderByRaw("
                CASE
                    WHEN status = 'pendiente' THEN 1
                    WHEN status = 'recibido' THEN 2
                    ELSE 3
                END
            ")
            ->orderBy('created_at', 'desc')
            ->get();


        $filename = 'reporte_requerimientos_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($requirements) {
            $file = fopen('php://output', 'w');

            // Escribir BOM para UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Encabezados
            fputcsv($file, [
                'ID',
                'Actividad',
                'Caso',
                'Actividad Padre',
                'Descripción',
                'Estado',
                'Fecha Creación',
                'Fecha Recepción',
                'Días Transcurridos',
                'Notas'
            ], ';');

            foreach ($requirements as $requirement) {
                $diasTranscurridos = $requirement->status === 'recibido' && $requirement->fecha_recepcion
                    ? $requirement->created_at->diffInDays($requirement->fecha_recepcion)
                    : $requirement->created_at->diffInDays(now());

                fputcsv($file, [
                    $requirement->id,
                    $requirement->activity->name,
                    $requirement->activity->caso ?? '',
                    $requirement->activity->parent ? $requirement->activity->parent->name : '',
                    $requirement->description,
                    $requirement->status_label,
                    $requirement->created_at->format('d/m/Y H:i:s'),
                    $requirement->fecha_recepcion ? $requirement->fecha_recepcion->format('d/m/Y H:i:s') : '',
                    $diasTranscurridos,
                    $requirement->notas ?? ''
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Exportar reporte a Excel (XLSX)
     */
    public function exportReportExcel(Request $request)
    {
        // Mantener esta ruta para compatibilidad (por si la usas en otro lado)
        // Se comporta igual que antes: todos según filtros
        $query = Requirement::with(['activity', 'activity.parent']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }

        if ($request->filled('cliente_id')) {
            $query->whereHas('activity', function ($q) use ($request) {
                $q->where('cliente_id', $request->cliente_id);
            });
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $requirements = $query
            ->orderByRaw("
                CASE
                    WHEN status = 'pendiente' THEN 1
                    WHEN status = 'recibido' THEN 2
                    ELSE 3
                END
            ")
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->buildExcelFromRequirements($requirements);
    }

    public function exportReportExcelSelected(Request $request)
    {
        $query = Requirement::with(['activity', 'activity.parent']);

        // Si vienen IDs seleccionados, filtramos por ellos
        $selectedIds = $request->input('selected_ids', []);
        if (!empty($selectedIds)) {
            $query->whereIn('id', $selectedIds);
        } else {
            // Si no hay selección, aplicar mismos filtros que en report()
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('activity_id')) {
                $query->where('activity_id', $request->activity_id);
            }

            if ($request->filled('cliente_id')) {
                $query->whereHas('activity', function ($q) use ($request) {
                    $q->where('cliente_id', $request->cliente_id);
                });
            }

            if ($request->filled('fecha_desde')) {
                $query->whereDate('created_at', '>=', $request->fecha_desde);
            }

            if ($request->filled('fecha_hasta')) {
                $query->whereDate('created_at', '<=', $request->fecha_hasta);
            }
        }

        // Mismo orden que en el reporte
        $requirements = $query
            ->orderByRaw("
                CASE
                    WHEN status = 'pendiente' THEN 1
                    WHEN status = 'recibido' THEN 2
                    ELSE 3
                END
            ")
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->buildExcelFromRequirements($requirements);


        return $this->buildExcelFromRequirements($requirements);
    }

    /**
     * Construye y devuelve el Excel a partir de una colección de requerimientos
     */
    private function buildExcelFromRequirements($requirements)
    {
        $filename = 'reporte_requerimientos_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Requerimientos');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => '0070C0'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ];

        $activityHeaderStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => 'D9E1F2'],
            ],
        ];

        // Encabezados (fila 1) - sin columna ID
        $headers = [
            'A1' => 'Caso',
            'B1' => 'Actividad',
            'C1' => 'Actividad Padre',
            'D1' => 'Descripción',
            'E1' => 'Estado',
            'F1' => 'Fecha Creación',
            'G1' => 'Fecha Recepción',
            'H1' => 'Días Transcurridos',
            'I1' => 'Notas',
        ];
        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Agrupar por actividad (igual que en la vista)
        $grouped = $requirements->groupBy(function ($req) {
            return $req->activity ? $req->activity->name : 'Sin actividad';
        });

        $row = 2;
        foreach ($grouped as $activityName => $activityRequirements) {
            $first = $activityRequirements->first();
            $caso = $first->activity->caso ?? '';
            $parentName = $first->activity->parent->name ?? '';

            // Fila de cabecera de actividad (merge de columnas A-I)
            $sheet->mergeCells("A{$row}:I{$row}");
            $sheet->setCellValue("A{$row}", trim(($caso ? "{$caso} " : '') . $activityName));
            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray($activityHeaderStyle);
            $row++;

            // Filas de requerimientos de esa actividad
            foreach ($activityRequirements as $requirement) {
                $diasTranscurridos = $requirement->status === 'recibido' && $requirement->fecha_recepcion
                    ? $requirement->created_at->diffInDays($requirement->fecha_recepcion)
                    : $requirement->created_at->diffInDays(now());

                $sheet->setCellValue("A{$row}", $requirement->activity->caso ?? '');
                $sheet->setCellValue("B{$row}", $requirement->activity->name ?? '');
                $sheet->setCellValue("C{$row}", $parentName);
                $sheet->setCellValue("D{$row}", $requirement->description);
                $sheet->setCellValue("E{$row}", $requirement->status_label);
                $sheet->setCellValue("F{$row}", $requirement->created_at->format('d/m/Y H:i:s'));
                $sheet->setCellValue("G{$row}", $requirement->fecha_recepcion ? $requirement->fecha_recepcion->format('d/m/Y H:i:s') : '');
                $sheet->setCellValue("H{$row}", $diasTranscurridos);
                $sheet->setCellValue("I{$row}", $requirement->notas ?? '');

                // Colorear estado
                if ($requirement->status === 'pendiente') {
                    $sheet->getStyle("E{$row}")->getFont()->getColor()->setRGB('FFC000');
                } else {
                    $sheet->getStyle("E{$row}")->getFont()->getColor()->setRGB('00B050');
                }

                // Resaltar vencidos
                if ($requirement->status === 'pendiente' && $diasTranscurridos > 7) {
                    $sheet->getStyle("H{$row}")->getFont()->getColor()->setRGB('FF0000');
                }

                $row++;
            }

            // Línea en blanco entre actividades
            $row++;
        }

        // Ajustar anchos de columnas y envoltura de texto
        $sheet->getColumnDimension('A')->setWidth(14);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(50);
        $sheet->getColumnDimension('E')->setWidth(14);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(18);
        $sheet->getColumnDimension('I')->setWidth(40);

        $lastRow = $row > 2 ? $row - 1 : 2;
        $sheet->getStyle("A1:I{$lastRow}")->getAlignment()->setWrapText(true);
        $sheet->getStyle("A2:I{$lastRow}")->getAlignment()->setVertical(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
        );

        $sheet->setAutoFilter("A1:I1");

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
