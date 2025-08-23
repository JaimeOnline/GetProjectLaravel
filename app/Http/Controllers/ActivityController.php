<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use App\Models\Analista;
use App\Models\Requirement; // Asegúrate de importar el modelo Requirement
use App\Models\Comment;
use App\Models\Email;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        // Obtener solo las actividades padre (sin parent_id) con sus analistas, correos, requerimientos y subactividades anidadas
        $activities = Activity::whereNull('parent_id')
            ->with(['analistas', 'comments', 'emails', 'requirements', 'subactivities.analistas', 'subactivities.comments', 'subactivities.emails', 'subactivities.requirements', 'subactivities.subactivities.analistas', 'subactivities.subactivities.comments', 'subactivities.subactivities.emails', 'subactivities.subactivities.requirements'])
            ->get();

        // Obtener datos para los filtros
        $analistas = Analista::all();
        $statuses = [
            'en_ejecucion' => 'En Ejecución',
            'culminada' => 'Culminada',
            'en_espera_de_insumos' => 'En Espera de Insumos'
        ];

        return view('activities.index', compact('activities', 'analistas', 'statuses'));
    }

    /**
     * Búsqueda AJAX en tiempo real
     */
    public function search(Request $request)
    {
        $query = $request->get('query', '');
        
        // Log para debug
        \Log::info('Search request', [
            'query' => $query,
            'all_params' => $request->all()
        ]);
        
        // Construir la consulta base
        $activitiesQuery = Activity::with([
            'analistas', 
            'comments', 
            'emails', 
            'requirements',
            'subactivities.analistas', 
            'subactivities.comments', 
            'subactivities.emails',
            'subactivities.requirements',
            'subactivities.subactivities.analistas',
            'subactivities.subactivities.comments',
            'subactivities.subactivities.emails',
            'subactivities.subactivities.requirements'
        ]);

        // Aplicar búsqueda por texto si existe
        if (!empty($query)) {
            $activitiesQuery->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhere('caso', 'LIKE', "%{$query}%")
                  ->orWhere('status', 'LIKE', "%{$query}%")
                  ->orWhere('fecha_recepcion', 'LIKE', "%{$query}%")
                  ->orWhereHas('analistas', function($subQ) use ($query) {
                      $subQ->where('name', 'LIKE', "%{$query}%");
                  })
                  ->orWhereHas('comments', function($subQ) use ($query) {
                      $subQ->where('comment', 'LIKE', "%{$query}%");
                  })
                  ->orWhereHas('emails', function($subQ) use ($query) {
                      $subQ->where('subject', 'LIKE', "%{$query}%")
                           ->orWhere('content', 'LIKE', "%{$query}%");
                  });
            });
        }

        // Aplicar filtros directamente desde los parámetros de consulta
        if ($request->has('status') && !empty($request->get('status'))) {
            $activitiesQuery->where('status', $request->get('status'));
        }

        if ($request->has('analista_id') && !empty($request->get('analista_id'))) {
            $activitiesQuery->whereHas('analistas', function($q) use ($request) {
                $q->where('analistas.id', $request->get('analista_id'));
            });
        }

        if ($request->has('fecha_desde') && !empty($request->get('fecha_desde'))) {
            $activitiesQuery->where('fecha_recepcion', '>=', $request->get('fecha_desde'));
        }

        if ($request->has('fecha_hasta') && !empty($request->get('fecha_hasta'))) {
            $activitiesQuery->where('fecha_recepcion', '<=', $request->get('fecha_hasta'));
        }

        if ($request->has('caso') && !empty($request->get('caso'))) {
            $activitiesQuery->where('caso', 'LIKE', "%{$request->get('caso')}%");
        }

        // Obtener resultados
        $activities = $activitiesQuery->get();

        // También buscar en subactividades si hay query de texto
        $subactivities = collect();
        if (!empty($query)) {
            $subactivitiesQuery = Activity::whereNotNull('parent_id')
                ->with(['analistas', 'comments', 'emails', 'parent'])
                ->where(function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%")
                      ->orWhere('caso', 'LIKE', "%{$query}%")
                      ->orWhere('status', 'LIKE', "%{$query}%")
                      ->orWhere('fecha_recepcion', 'LIKE', "%{$query}%")
                      ->orWhereHas('analistas', function($subQ) use ($query) {
                          $subQ->where('name', 'LIKE', "%{$query}%");
                      })
                      ->orWhereHas('comments', function($subQ) use ($query) {
                          $subQ->where('comment', 'LIKE', "%{$query}%");
                      })
                      ->orWhereHas('emails', function($subQ) use ($query) {
                          $subQ->where('subject', 'LIKE', "%{$query}%")
                               ->orWhere('content', 'LIKE', "%{$query}%");
                      });
                });

            $subactivities = $subactivitiesQuery->get();
        }

        return response()->json([
            'activities' => $activities,
            'subactivities' => $subactivities,
            'total_results' => $activities->count() + $subactivities->count()
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
          ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN');
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
        // Obtener todas las actividades para el campo de actividad padre (excluyendo la actividad actual)
        $activities = Activity::where('id', '!=', $activity->id)->get();
        // Cargar los comentarios, correos, analistas y requerimientos de la actividad
        $activity->load(['comments', 'emails', 'analistas', 'requirements']);
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

            return redirect()->route('activities.edit', $activity)
                ->with('success', 'Información básica actualizada con éxito.')
                ->with('active_tab', 'basic');
            
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
                ->with('success', 'Comentario eliminado exitosamente.')
                ->with('active_tab', 'comments');
        }
        
        return redirect()->route('activities.comments', $activity)
            ->with('success', 'Comentario eliminado exitosamente.');
    }

    public function storeRequirements(Request $request, Activity $activity)
    {
        $request->validate([
            'requirements' => 'nullable|array',
            'requirements.*' => 'nullable|string|max:1000',
        ]);

        // Agregar nuevos requerimientos (no eliminar los existentes para mantener el historial)
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

        return redirect()->route('activities.edit', $activity)
            ->with('success', 'Requerimientos agregados exitosamente.')
            ->with('active_tab', 'requirements');
    }

    public function destroyRequirement(Requirement $requirement)
    {
        $activity = $requirement->activity;
        $requirement->delete();
        
        return redirect()->route('activities.edit', $activity)
            ->with('success', 'Requerimiento eliminado exitosamente.')
            ->with('active_tab', 'requirements');
    }

    public function storeCommentsFromTab(Request $request, Activity $activity)
    {
        $request->validate([
            'comments' => 'nullable|array',
            'comments.*' => 'nullable|string|max:1000',
        ]);

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

        return redirect()->route('activities.edit', $activity)
            ->with('success', 'Comentarios agregados exitosamente.')
            ->with('active_tab', 'comments');
    }

    /**
     * Almacenar un nuevo correo para una actividad
     */
    public function storeEmail(Request $request, Activity $activity)
    {
        $request->validate([
            'type' => 'required|in:sent,received',
            'subject' => 'required|string|max:255',
            'sender_recipient' => 'nullable|string|max:255',
            'content' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,txt,jpg,jpeg,png,gif,zip,rar',
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if ($file && $file->isValid()) {
                    // Generar nombre único para el archivo
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $fileName = time() . '_' . uniqid() . '.' . $extension;
                    
                    // Guardar el archivo
                    $path = $file->storeAs('email_attachments', $fileName, 'public');
                    
                    // Guardar información del archivo
                    $attachments[] = [
                        'original_name' => $originalName,
                        'file_name' => $fileName,
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ];
                }
            }
        }

        $email = Email::create([
            'activity_id' => $activity->id,
            'type' => $request->type,
            'subject' => $request->subject,
            'sender_recipient' => $request->sender_recipient,
            'content' => $request->content,
            'attachments' => $attachments,
        ]);

        $typeLabel = $request->type === 'sent' ? 'enviado' : 'recibido';
        $successMessage = "Correo {$typeLabel} agregado exitosamente: \"{$email->subject}\"";
        
        // Verificar de dónde viene la petición para redirigir apropiadamente
        $referer = request()->headers->get('referer');
        if (strpos($referer, '/emails') !== false) {
            return redirect()->route('activities.emails', $activity)
                ->with('success', $successMessage);
        }
        
        return redirect()->route('activities.edit', $activity)
            ->with('success', $successMessage)
            ->with('active_tab', 'emails');
    }

    /**
     * Eliminar un correo
     */
    public function destroyEmail(Email $email)
    {
        $activity = $email->activity;
        $email->delete();
        
        // Verificar de dónde viene la petición para redirigir apropiadamente
        $referer = request()->headers->get('referer');
        if (strpos($referer, '/emails') !== false) {
            return redirect()->route('activities.emails', $activity)
                ->with('success', 'Correo eliminado exitosamente.');
        }
        
        return redirect()->route('activities.edit', $activity)
            ->with('success', 'Correo eliminado exitosamente.')
            ->with('active_tab', 'emails');
    }

    /**
     * Descargar archivo adjunto de correo
     */
    public function downloadAttachment(Email $email, $fileIndex)
    {
        if (!$email->attachments || !isset($email->attachments[$fileIndex])) {
            abort(404, 'Archivo no encontrado');
        }

        $attachment = $email->attachments[$fileIndex];
        $filePath = storage_path('app/public/' . $attachment['file_path']);

        if (!file_exists($filePath)) {
            abort(404, 'Archivo no encontrado en el servidor');
        }

        return response()->download($filePath, $attachment['original_name']);
    }

    /**
     * Mostrar todos los correos de una actividad padre y sus subactividades
     */
    public function showEmails(Activity $activity)
    {
        // Obtener todos los IDs de actividades relacionadas (padre + subactividades)
        $activityIds = [$activity->id];
        
        // Si es una actividad padre, agregar todas sus subactividades recursivamente
        if ($activity->subactivities->count() > 0) {
            $this->addSubactivityIds($activity, $activityIds);
        }
        
        // Si es una subactividad, obtener la actividad padre y todas sus subactividades
        if ($activity->parent_id) {
            $parentActivity = $activity->parent;
            $activityIds = [$parentActivity->id];
            $this->addSubactivityIds($parentActivity, $activityIds);
            $activity = $parentActivity; // Para mostrar el nombre correcto en la vista
        }

        // Obtener todos los correos de las actividades relacionadas, ordenados por fecha
        $emails = Email::whereIn('activity_id', $activityIds)
            ->with('activity')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('activities.emails', compact('activity', 'emails'));
    }

    /**
     * Método auxiliar para agregar IDs de subactividades recursivamente
     */
    private function addSubactivityIds(Activity $activity, &$activityIds)
    {
        foreach ($activity->subactivities as $subactivity) {
            $activityIds[] = $subactivity->id;
            if ($subactivity->subactivities->count() > 0) {
                $this->addSubactivityIds($subactivity, $activityIds);
            }
        }
    }
}
