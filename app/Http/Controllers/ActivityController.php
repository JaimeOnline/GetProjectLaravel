<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use App\Models\Analista;
use App\Models\Requirement; // Asegúrate de importar el modelo Requirement
use App\Models\Comment;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        // Obtener solo las actividades padre (sin parent_id) con sus analistas y subactividades anidadas
        $activities = Activity::whereNull('parent_id')
            ->with(['analistas', 'comments', 'subactivities.analistas', 'subactivities.comments', 'subactivities.subactivities.analistas', 'subactivities.subactivities.comments'])
            ->get();
        return view('activities.index', compact('activities'));
    }
    public function create(Request $request)
    {
        // Obtener todos los analistas
        $analistas = Analista::all();

        // Obtener todas las actividades para el campo de actividad padre
        $activities = Activity::all();
        
        // Obtener el parentId desde la query string
        $parentId = $request->query('parentId');
        
        // Si se pasa un parentId, lo usamos como padre predeterminado
        $parentActivity = $parentId ? Activity::findOrFail($parentId) : null;
        
        // Pasar las variables a la vista
        return view('activities.create', compact('analistas', 'activities', 'parentActivity'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
            'analista_id' => 'required|array',
            'analista_id.*' => 'exists:analistas,id', // Validar que cada ID de analista exista
            'requirements' => 'nullable|array', // Solo array, permitiendo que esté vacío
            'comments' => 'nullable|array', // Validar comentarios como array
            'fecha_recepcion' => 'nullable|date', // Validar que la fecha de recepción sea una fecha válida si se proporciona
            'caso' => 'required|unique:activities,caso', // Validar que el campo 'caso' sea único en la tabla 'activities'
            'parent_id' => 'nullable|exists:activities,id', // Validar que el parent_id exista si se proporciona
        ]);
        // Crear la actividad
        $activity = Activity::create($request->only(['caso', 'name', 'description', 'status', 'fecha_recepcion', 'parent_id']));
        // Asignar analistas a la actividad
        $activity->analistas()->attach($request->analista_id);
        // Agregar los requerimientos solo si existen
        if ($request->has('requirements')) {
            foreach ($request->requirements as $requirementDescription) {
                if (!empty($requirementDescription)) {
                    Requirement::create([
                        'activity_id' => $activity->id,
                        'description' => $requirementDescription,
                    ]);
                }
            }
        }

        // Agregar los comentarios solo si existen
        if ($request->has('comments')) {
            foreach ($request->comments as $commentText) {
                if (!empty($commentText)) {
                    Comment::create([
                        'activity_id' => $activity->id,
                        'comment' => $commentText,
                    ]);
                }
            }
        }

        return redirect()->route('activities.index')->with('success', 'Actividad creada con éxito.');
    }
    public function edit(Activity $activity)
    {
        // Obtener todos los analistas
        $analistas = Analista::all();
        // Obtener todas las actividades para el campo de actividad padre
        $activities = Activity::all();
        // Cargar los comentarios de la actividad
        $activity->load('comments');
        // Pasar las variables a la vista
        return view('activities.edit', compact('activity', 'analistas', 'activities'));
    }
    public function update(Request $request, Activity $activity)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:en_ejecucion,culminada,en_espera_de_insumos',
            'analista_id' => 'required|array|min:1',
            'analista_id.*' => 'exists:analistas,id',
            'requirements' => 'nullable|array',
            'requirements.*' => 'nullable|string|max:1000',
            'comments' => 'nullable|array',
            'comments.*' => 'nullable|string|max:1000',
            'fecha_recepcion' => 'nullable|date',
            'caso' => 'required|string|max:255|unique:activities,caso,' . $activity->id,
            'parent_id' => 'nullable|exists:activities,id',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            // Actualizar la actividad
            $activity->update($request->only(['caso', 'name', 'description', 'status', 'fecha_recepcion', 'parent_id']));
            
            // Asignar analistas a la actividad
            $activity->analistas()->sync($request->analista_id);
            
            // Limpiar los requerimientos existentes y agregar los nuevos solo si existen
            $activity->requirements()->delete();
            if ($request->has('requirements')) {
                foreach ($request->requirements as $requirementDescription) {
                    if (!empty($requirementDescription)) {
                        Requirement::create([
                            'activity_id' => $activity->id,
                            'description' => $requirementDescription,
                        ]);
                    }
                }
            }

            // Agregar nuevos comentarios (no eliminar los existentes para mantener el historial)
            if ($request->has('comments')) {
                foreach ($request->comments as $commentText) {
                    if (!empty($commentText)) {
                        Comment::create([
                            'activity_id' => $activity->id,
                            'comment' => $commentText,
                        ]);
                    }
                }
            }

            return redirect()->route('activities.index')->with('success', 'Actividad actualizada con éxito.');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error al actualizar la actividad: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return redirect()->route('activities.index')->with('success', 'Actividad eliminada con éxito.');
    }

    public function showComments(Activity $activity)
    {
        $activity->load('comments');
        return view('activities.comments', compact('activity'));
    }

    public function storeComment(Request $request, Activity $activity)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        Comment::create([
            'activity_id' => $activity->id,
            'comment' => $request->comment,
        ]);

        return redirect()->route('activities.comments', $activity)
            ->with('success', 'Comentario agregado exitosamente.');
    }

    public function destroyComment(Comment $comment)
    {
        $activity = $comment->activity;
        $comment->delete();
        
        // Verificar de dónde viene la petición para redirigir apropiadamente
        $referer = request()->headers->get('referer');
        if (strpos($referer, '/edit') !== false) {
            return redirect()->route('activities.edit', $activity)
                ->with('success', 'Comentario eliminado exitosamente.');
        }
        
        return redirect()->route('activities.comments', $activity)
            ->with('success', 'Comentario eliminado exitosamente.');
    }

    public function destroyRequirement(Requirement $requirement)
    {
        $activity = $requirement->activity;
        $requirement->delete();
        
        return redirect()->route('activities.edit', $activity)
            ->with('success', 'Requerimiento eliminado exitosamente.');
    }
}
