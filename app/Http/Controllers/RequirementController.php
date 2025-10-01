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
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('notas', 'like', "%{$search}%")
                  ->orWhereHas('activity', function($actQuery) use ($search) {
                      $actQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('caso', 'like', "%{$search}%");
                  });
            });
        }

        $requirements = $query->orderBy('created_at', 'desc')->paginate(15);
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

        Requirement::create($data);

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

        return redirect()->route('requirements.index')
                        ->with('success', 'Requerimiento actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Requirement $requirement)
    {
        $requirement->delete();

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
        $query = Requirement::with(['activity', 'activity.parent']);

        // Filtros para el reporte
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

        if ($request->filled('fecha_recepcion_desde')) {
            $query->whereDate('fecha_recepcion', '>=', $request->fecha_recepcion_desde);
        }

        if ($request->filled('fecha_recepcion_hasta')) {
            $query->whereDate('fecha_recepcion', '<=', $request->fecha_recepcion_hasta);
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $requirements = $query->get();
        $activities = Activity::orderBy('name')->get();

        // Estadísticas del reporte
        $stats = [
            'total' => $requirements->count(),
            'pendientes' => $requirements->where('status', 'pendiente')->count(),
            'recibidos' => $requirements->where('status', 'recibido')->count(),
            'tiempo_promedio_respuesta' => $this->calculateAverageResponseTime($requirements->where('status', 'recibido')),
            'requerimientos_vencidos' => $requirements->where('status', 'pendiente')
                                                    ->filter(function($req) {
                                                        return $req->created_at->diffInDays(now()) > 7;
                                                    })->count(),
        ];

        // Agrupación por actividad
        $requirementsByActivity = $requirements->groupBy('activity.name');

        // Agrupación por estado y mes
        $requirementsByMonth = $requirements->groupBy(function($requirement) {
            return $requirement->created_at->format('Y-m');
        });

        return view('requirements.report', compact(
            'requirements', 
            'activities', 
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

        $totalDays = $receivedRequirements->sum(function($requirement) {
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

        $requirements = $query->orderBy('created_at', 'desc')->get();

        $filename = 'reporte_requerimientos_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($requirements) {
            $file = fopen('php://output', 'w');
            
            // Escribir BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
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
}
