<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Email;
use App\Models\Status;
use App\Models\Cliente;
use App\Models\Comment;
use App\Models\Activity;
use App\Models\Analista;
use App\Models\TipoProducto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Exports\ActivitiesExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Requirement; // Asegúrate de importar el modelo Requirement
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;



class ActivityController extends Controller
{
    public function downloadExcelTemplate()
    {
        // Busca primero .xlsm, luego .xlsx, luego sin extensión
        $base = public_path('modelo_actividades');
        $file = null;
        $downloadName = null;

        if (file_exists($base . '.xlsm')) {
            $file = $base . '.xlsm';
            $downloadName = 'modelo_actividades.xlsm';
        } elseif (file_exists($base . '.xlsx')) {
            $file = $base . '.xlsx';
            $downloadName = 'modelo_actividades.xlsx';
        } elseif (file_exists($base)) {
            $file = $base;
            $downloadName = 'modelo_actividades';
        } else {
            abort(404, 'El archivo modelo no está disponible. Contacta al administrador.');
        }

        return response()->download($file, $downloadName);
    }


    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xlsm',
        ]);

        $file = $request->file('excel_file');
        $rows = \Maatwebsite\Excel\Facades\Excel::toArray([], $file)[0];

        // Encabezados esperados
        $expectedHeaders = [
            'CASO',
            'ESTADOS',
            'PRIORIDAD',
            'ORDEN ANALISTA',
            'NOMBRE ACTIVIDAD',
            'DESCRIPCION',
            'ESTATUS OPERACIONAL',
            'ANALISTAS',
            'ACTIVIDAD PADRE',
            'FECHA RECEPCION',
            'PROYECTO',
            'CLIENTE',
            'TIPO DE PRODUCTO',
            'CATEGORIA',
        ];

        // Normalizador: quita espacios, guiones, guiones bajos y pone mayúsculas
        $normalize = function ($str) {
            return strtoupper(str_replace([' ', '-', '_'], '', trim($str)));
        };

        $headerRaw = $rows[0];
        $header = array_map($normalize, $headerRaw);
        $expectedHeadersNorm = array_map($normalize, $expectedHeaders);

        foreach ($expectedHeadersNorm as $col) {
            if (!in_array($col, $header)) {
                return back()->withErrors(['excel_file' => "Falta la columna '$col' en el archivo Excel."]);
            }
        }

        $rowCount = 0;
        for ($i = 1; $i < count($rows); $i++) {
            // Mapear los datos de la fila a los encabezados normalizados
            $rowAssoc = [];
            foreach ($header as $idx => $col) {
                $rowAssoc[$col] = isset($rows[$i][$idx]) ? $rows[$i][$idx] : null;
            }

            // Saltar filas vacías (sin caso)
            if (empty($rowAssoc['CASO']) || trim($rowAssoc['CASO']) === '') {
                continue;
            }

            // Ahora puedes acceder así:
            // $rowAssoc['CASO'], $rowAssoc['ESTADOS'], etc. (ya normalizados)

            // Buscar IDs por nombre
            $statusIds = \App\Models\Status::whereIn('label', array_map('trim', explode(',', $rowAssoc['ESTADOS'])))->pluck('id')->toArray();
            $analistaIds = \App\Models\Analista::whereIn('name', array_map('trim', explode(',', $rowAssoc['ANALISTAS'])))->pluck('id')->toArray();

            $parentId = null;
            if (!empty($rowAssoc['ACTIVIDADPADRE'])) {
                $parent = \App\Models\Activity::where('name', trim($rowAssoc['ACTIVIDADPADRE']))->first();
                if ($parent) {
                    $parentId = $parent->id;
                }
            }

            // Procesar la fecha correctamente
            $fechaRecepcion = null;
            if (!empty($rowAssoc['FECHARECEPCION'])) {
                if (is_numeric($rowAssoc['FECHARECEPCION'])) {
                    $fechaRecepcion = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rowAssoc['FECHARECEPCION'])->format('Y-m-d');
                } else {
                    $fecha = \DateTime::createFromFormat('Y-m-d', $rowAssoc['FECHARECEPCION']);
                    if (!$fecha) {
                        $fecha = \DateTime::createFromFormat('d/m/Y', $rowAssoc['FECHARECEPCION']);
                    }
                    if ($fecha) {
                        $fechaRecepcion = $fecha->format('Y-m-d');
                    } else {
                        $fechaRecepcion = null;
                    }
                }
            }

            // Buscar el ID del proyecto por nombre
            $proyectoId = null;
            if (!empty($rowAssoc['PROYECTO'])) {
                $proyecto = \App\Models\Proyecto::where('nombre', trim($rowAssoc['PROYECTO']))->first();
                if ($proyecto) {
                    $proyectoId = $proyecto->id;
                } else {
                    return back()->withErrors(['excel_file' => "El proyecto '{$rowAssoc['PROYECTO']}' no existe en la base de datos."]);
                }
            }

            // Buscar el ID del cliente por nombre
            $clienteId = null;
            if (!empty($rowAssoc['CLIENTE'])) {
                $cliente = \App\Models\Cliente::where('nombre', trim($rowAssoc['CLIENTE']))->first();
                if ($cliente) {
                    $clienteId = $cliente->id;
                } else {
                    return back()->withErrors(['excel_file' => "El cliente '{$rowAssoc['CLIENTE']}' no existe en la base de datos."]);
                }
            }

            // Buscar el ID del tipo de producto por nombre
            $tipoProductoId = null;
            if (!empty($rowAssoc['TIPODEPRODUCTO'])) {
                $tipoProducto = \App\Models\TipoProducto::where('nombre', trim($rowAssoc['TIPODEPRODUCTO']))->first();
                if ($tipoProducto) {
                    $tipoProductoId = $tipoProducto->id;
                } else {
                    return back()->withErrors(['excel_file' => "El tipo de producto '{$rowAssoc['TIPODEPRODUCTO']}' no existe en la base de datos."]);
                }
            }

            // Procesar la(s) categoría(s)
            $categorias = [];
            if (!empty($rowAssoc['CATEGORIA'])) {
                $categorias = array_map('trim', explode(',', $rowAssoc['CATEGORIA']));
            } else {
                $categorias = $proyectoId ? ['proyecto'] : ['incidencia'];
            }

            // Validar y convertir prioridad y orden_analista a enteros, o asignar valor por defecto si están vacíos
            $prioridad = isset($rowAssoc['PRIORIDAD']) && is_numeric($rowAssoc['PRIORIDAD']) ? (int)$rowAssoc['PRIORIDAD'] : 10;
            $ordenAnalista = isset($rowAssoc['ORDENANALISTA']) && is_numeric($rowAssoc['ORDENANALISTA']) ? (int)$rowAssoc['ORDENANALISTA'] : 10;

            // Crear la actividad
            $activity = \App\Models\Activity::create([
                'caso' => $rowAssoc['CASO'] ?? '',
                'prioridad' => $prioridad,
                'orden_analista' => $ordenAnalista,
                'name' => $rowAssoc['NOMBREACTIVIDAD'] ?? '',
                'description' => $rowAssoc['DESCRIPCION'] ?? '',
                'estatus_operacional' => $rowAssoc['ESTATUSOPERACIONAL'] ?? '',
                'parent_id' => $parentId,
                'fecha_recepcion' => $fechaRecepcion,
                'proyecto_id' => $proyectoId,
                'cliente_id' => $clienteId,
                'tipo_producto_id' => $tipoProductoId,
            ]);

            // Asignar categoría desde el Excel o por defecto
            if ($activity) {
                \DB::table('activity_categoria')->where('activity_id', $activity->id)->delete();
                foreach ($categorias as $cat) {
                    \DB::table('activity_categoria')->insert([
                        'activity_id' => $activity->id,
                        'categoria' => $cat,
                    ]);
                }

                // Relacionar estados
                if (!empty($statusIds)) {
                    $activity->statuses()->sync($statusIds);
                }

                // Relacionar analistas
                if (!empty($analistaIds)) {
                    $activity->analistas()->sync($analistaIds);
                }

                $rowCount++;
            }
        }

        return redirect()->route('activities.index')->with('success', "Se importaron $rowCount actividades correctamente.");
    }

    public function index(Request $request)
    {
        $proyectoId = $request->get('proyecto_id');
        $query = Activity::whereNull('parent_id')
            ->with([
                'analistas',
                'comments',
                'emails',
                'requirements',
                'statuses',
                'subactivities.analistas',
                'subactivities.comments',
                'subactivities.emails',
                'subactivities.requirements',
                'subactivities.statuses',
                'subactivities.subactivities.analistas',
                'subactivities.subactivities.comments',
                'subactivities.subactivities.emails',
                'subactivities.subactivities.requirements',
                'subactivities.subactivities.statuses'
            ]);
        if ($proyectoId) {
            $query->where('proyecto_id', $proyectoId);
        }
        $activities = $query->get();

        // Analistas para el filtro
        $analistas = Analista::all();

        // Proyectos para el filtro
        $proyectos = \App\Models\Proyecto::all();

        // Filtros de estado (array asociativo para la tabla)
        $statusLabels = [
            'no_iniciada' => 'No Iniciada',
            'en_ejecucion' => 'En Ejecución',
            'en_espera_de_insumos' => 'En Espera de Insumos',
            'en_certificacion_por_cliente' => 'En Certificación',
            'pases_enviados' => 'Pases Enviados',
            'culminada' => 'Culminada',
            'pausada' => 'Pausada',
            'reiterar' => 'Reiterar',
            'atendiendo_hoy' => 'Atendiendo hoy'
        ];

        // Colores de estado para los filtros
        $statusColors = [
            'no_iniciada' => '#6c757d',
            'en_ejecucion' => '#17a2b8',
            'en_espera_de_insumos' => '#ffc107',
            'en_certificacion_por_cliente' => '#fd7e14',
            'pases_enviados' => '#20c997',
            'culminada' => '#28a745',
            'pausada' => '#343a40',
            'reiterar' => '#ff5722',
            'atendiendo_hoy' => '#007bff'
        ];

        // Estados para el modal (colección de objetos)
        $statuses = Status::orderBy('order')->get();

        return view('activities.index', compact(
            'activities',
            'analistas',
            'proyectos',
            'statusLabels',
            'statusColors',
            'statuses'
        ));
    }


    /**
     * Búsqueda AJAX en tiempo real (devuelve el partial Blade)
     */
    public function search(Request $request)
    {
        $query = $request->get('query', '');

        // Construir la consulta base
        $activitiesQuery = Activity::with([
            'analistas',
            'comments',
            'emails',
            'requirements',
            'statuses',
            'subactivities.analistas',
            'subactivities.comments',
            'subactivities.emails',
            'subactivities.requirements',
            'subactivities.statuses',
            'subactivities.subactivities.analistas',
            'subactivities.subactivities.comments',
            'subactivities.subactivities.emails',
            'subactivities.subactivities.requirements',
            'subactivities.subactivities.statuses'
        ]);

        // Aplicar búsqueda por texto si existe
        if (!empty($query)) {
            $activitiesQuery->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%")
                    ->orWhere('caso', 'LIKE', "%{$query}%")
                    ->orWhere('status', 'LIKE', "%{$query}%")
                    ->orWhere('fecha_recepcion', 'LIKE', "%{$query}%")
                    ->orWhereHas('analistas', function ($subQ) use ($query) {
                        $subQ->where('name', 'LIKE', "%{$query}%");
                    })
                    ->orWhereHas('comments', function ($subQ) use ($query) {
                        $subQ->where('comment', 'LIKE', "%{$query}%");
                    })
                    ->orWhereHas('emails', function ($subQ) use ($query) {
                        $subQ->where('subject', 'LIKE', "%{$query}%")
                            ->orWhere('content', 'LIKE', "%{$query}%");
                    })
                    ->orWhereHas('statuses', function ($subQ) use ($query) {
                        $subQ->where('name', 'LIKE', "%{$query}%")
                            ->orWhere('label', 'LIKE', "%{$query}%");
                    });
            });
        }

        // Aplicar filtros directamente desde los parámetros de consulta
        if ($request->has('status') && !empty($request->get('status'))) {
            $statusFilter = $request->get('status');
            if (is_array($statusFilter)) {
                $activitiesQuery->whereHas('statuses', function ($q) use ($statusFilter) {
                    $q->whereIn('name', $statusFilter);
                });
            } else {
                $activitiesQuery->whereHas('statuses', function ($q) use ($statusFilter) {
                    $q->where('name', $statusFilter);
                })->orWhere('status', $statusFilter);
            }
        }

        if ($request->has('analista_id') && !empty($request->get('analista_id'))) {
            $activitiesQuery->whereHas('analistas', function ($q) use ($request) {
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

        // Variables necesarias para el partial
        $analistas = Analista::all();
        $statusLabels = [
            'no_iniciada' => 'No Iniciada',
            'en_ejecucion' => 'En Ejecución',
            'en_espera_de_insumos' => 'En Espera de Insumos',
            'en_certificacion_por_cliente' => 'En Certificación',
            'pases_enviados' => 'Pases Enviados',
            'culminada' => 'Culminada',
            'pausada' => 'Pausada',
            'reiterar' => 'Reiterar',
            'atendiendo_hoy' => 'Atendiendo hoy'
        ];
        $statusColors = [
            'no_iniciada' => '#6c757d',
            'en_ejecucion' => '#17a2b8',
            'en_espera_de_insumos' => '#ffc107',
            'en_certificacion_por_cliente' => '#fd7e14',
            'pases_enviados' => '#20c997',
            'culminada' => '#28a745',
            'pausada' => '#343a40',
            'reiterar' => '#ff5722',
            'atendiendo_hoy' => '#007bff'
        ];

        // Renderiza el partial y lo devuelve como HTML
        return response()->view('activities.partials.activity_table', [
            'activities' => $activities,
            'statusLabels' => $statusLabels,
            'statusColors' => $statusColors,
            'analistas' => $analistas,
        ]);
    }
    public function create(Request $request)
    {
        // Obtener todos los analistas
        $analistas = Analista::all();

        // Obtener todas las actividades para el campo de actividad padre
        $activities = Activity::all();

        // Obtener todos los proyectos
        $proyectos = \App\Models\Proyecto::all();

        // Obtener todos los estados
        $statuses = Status::active()->ordered()->get();

        // Obtener todos los clientes y tipos de productos
        $clientes = Cliente::all();
        $tipos_productos = TipoProducto::all();

        // Obtener el parentId desde la query string
        $parentId = $request->query('parentId');

        // Si se pasa un parentId, lo usamos como padre predeterminado
        $parentActivity = $parentId ? Activity::findOrFail($parentId) : null;

        // Si hay actividad padre, obtener caso y analistas para preseleccionar
        $defaultCaso = $parentActivity ? $parentActivity->caso : null;
        $defaultAnalistas = $parentActivity ? $parentActivity->analistas->pluck('id')->toArray() : [];

        // Detectar si viene desde un proyecto
        $proyectoId = $request->get('proyecto_id');
        $defaultCategoria = $proyectoId ? ['proyecto'] : ['incidencia'];

        // Si no viene en la query, intenta obtenerlo de la ruta anterior (referer)
        if (!$proyectoId && $request->server('HTTP_REFERER')) {
            $referer = $request->server('HTTP_REFERER');
            $parsed = parse_url($referer);
            if (isset($parsed['query'])) {
                parse_str($parsed['query'], $queryParams);
                if (isset($queryParams['proyecto_id'])) {
                    $proyectoId = $queryParams['proyecto_id'];
                    $defaultCategoria = ['proyecto'];
                }
            }
        }

        // Pasar las variables a la vista
        return view('activities.create', [
            'analistas' => $analistas,
            'activities' => $activities,
            'proyectos' => $proyectos,
            'statuses' => $statuses,
            'parentActivity' => $parentActivity,
            'defaultCaso' => $defaultCaso,
            'defaultAnalistas' => $defaultAnalistas,
            'clientes' => $clientes,
            'tipos_productos' => $tipos_productos,
            'proyectoId' => $proyectoId,
            'defaultCategoria' => $defaultCategoria,
        ]);
    }
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'status_ids' => 'required|array|min:1',
            'status_ids.*' => 'exists:statuses,id',
            'analista_id' => 'required|array',
            'analista_id.*' => 'exists:analistas,id', // Validar que cada ID de analista exista
            'requirements' => 'nullable|array', // Solo array, permitiendo que esté vacío
            'comments' => 'nullable|array', // Validar comentarios como array
            'fecha_recepcion' => 'nullable|date', // Validar que la fecha de recepción sea una fecha válida si se proporciona
            'parent_id' => 'nullable|exists:activities,id', // Validar que el parent_id exista si se proporciona
            'estatus_operacional' => 'nullable|string|max:1000', // Validar el nuevo campo estatus_operacional
            'prioridad' => 'required|integer|min:1',
            'orden_analista' => 'required|integer|min:1',
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_producto_id' => 'nullable|exists:tipos_productos,id',
            'proyecto_id' => 'nullable|exists:proyectos,id',
            'categoria' => 'nullable|array',
            'categoria.*' => 'in:proyecto,incidencia,mejora_continua',
        ];

        // Solo exigir unicidad de 'caso' si es actividad principal (sin parent_id)
        if (is_null($request->input('parent_id'))) {
            $rules['caso'] = 'required|unique:activities,caso';
        } else {
            $rules['caso'] = 'required';
        }

        $request->validate($rules);

        // Determinar la categoría por defecto si no viene del formulario
        $categoria = $request->input('categoria');
        if (!$categoria || count($categoria) === 0) {
            $categoria = $request->has('proyecto_id') && $request->input('proyecto_id') ? ['proyecto'] : ['incidencia'];
        }

        // Crear la actividad (sin el campo status ya que ahora usamos la tabla pivot)
        $activity = new Activity();
        $activity->caso = $request->input('caso');
        $activity->name = $request->input('name');
        $activity->description = $request->input('description');
        $activity->estatus_operacional = $request->input('estatus_operacional');
        $activity->fecha_recepcion = $request->input('fecha_recepcion');
        $activity->parent_id = $request->input('parent_id');
        $activity->prioridad = $request->input('prioridad');
        $activity->orden_analista = $request->input('orden_analista');
        $activity->cliente_id = $request->input('cliente_id');
        $activity->tipo_producto_id = $request->input('tipo_producto_id');
        $activity->proyecto_id = $request->input('proyecto_id');
        $activity->save();

        // Guardar categorías seleccionadas (siempre, aunque el array esté vacío)
        \DB::table('activity_categoria')->where('activity_id', $activity->id)->delete();
        foreach ($categoria as $cat) {
            \DB::table('activity_categoria')->insert([
                'activity_id' => $activity->id,
                'categoria' => $cat,
            ]);
        }


        // Asignar analistas a la actividad
        $activity->analistas()->attach($request->analista_id);

        // Asignar estados a la actividad
        $activity->statuses()->attach($request->status_ids);
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

        return redirect()->route('activities.edit', $activity->id)->with('success', 'Actividad creada con éxito.');
    }
    public function edit(Activity $activity)
    {
        // Obtener todos los analistas
        $analistas = Analista::all();
        // Obtener todas las actividades para el campo de actividad padre (excluyendo la actividad actual)
        $activities = Activity::where('id', '!=', $activity->id)->get();
        // Obtener todos los proyectos
        $proyectos = \App\Models\Proyecto::all();
        // Obtener todos los clientes y tipos de productos
        $clientes = Cliente::all();
        $tipos_productos = TipoProducto::all();
        // Cargar la actividad con subactividades y todas sus relaciones recursivas
        $activity->load([
            'comments',
            'emails',
            'analistas',
            'requirements',
            'statuses',
            'subactivities.analistas',
            'subactivities.comments',
            'subactivities.emails',
            'subactivities.requirements',
            'subactivities.statuses',
            'subactivities.subactivities.analistas',
            'subactivities.subactivities.comments',
            'subactivities.subactivities.emails',
            'subactivities.subactivities.requirements',
            'subactivities.subactivities.statuses'
        ]);

        // Filtros de estado (array asociativo para la tabla)
        $statusLabels = [
            'no_iniciada' => 'No Iniciada',
            'en_ejecucion' => 'En Ejecución',
            'en_espera_de_insumos' => 'En Espera de Insumos',
            'en_certificacion_por_cliente' => 'En Certificación',
            'pases_enviados' => 'Pases Enviados',
            'culminada' => 'Culminada',
            'pausada' => 'Pausada',
            'reiterar' => 'Reiterar',
            'atendiendo_hoy' => 'Atendiendo hoy'
        ];

        // Colores de estado para los filtros
        $statusColors = [
            'no_iniciada' => '#6c757d',
            'en_ejecucion' => '#17a2b8',
            'en_espera_de_insumos' => '#ffc107',
            'en_certificacion_por_cliente' => '#fd7e14',
            'pases_enviados' => '#20c997',
            'culminada' => '#28a745',
            'pausada' => '#343a40',
            'reiterar' => '#ff5722',
            'atendiendo_hoy' => '#007bff'
        ];

        // Pasar las variables a la vista
        return view('activities.edit', compact(
            'activity',
            'analistas',
            'activities',
            'proyectos',
            'statusLabels',
            'statusColors',
            'clientes',
            'tipos_productos'
        ));
    }


    public function update(Request $request, Activity $activity)
    {
        // Log para depuración: Verifica que los campos lleguen correctamente
        Log::info('REQUEST UPDATE ACTIVITY', [
            'prioridad' => $request->input('prioridad'),
            'orden_analista' => $request->input('orden_analista'),
            'all' => $request->all()
        ]);

        Log::info('UPDATE AJAX', [
            'is_ajax' => $request->ajax(),
            'method' => $request->method(),
            'analista_id' => $request->input('analista_id'),
            'all' => $request->all()
        ]);
        // Si solo se está actualizando analistas desde el modal AJAX (NO desde el formulario principal)
        if (($request->ajax() || $request->wantsJson()) && $request->has('analista_id') && !$request->has('categoria')) {
            try {
                $request->validate([
                    'analista_id' => 'required|array|min:1',
                    'analista_id.*' => 'exists:analistas,id',
                ]);
                $activity->analistas()->sync($request->analista_id);

                // Si es AJAX o el cliente espera JSON, devolver JSON con los analistas actualizados
                if ($request->ajax() || $request->wantsJson()) {
                    $analistas = $activity->analistas()->get(['analistas.id', 'analistas.name']);
                    return response()->json([
                        'success' => true,
                        'analistas' => $analistas
                    ]);
                }

                // Si el formulario pide volver a la edición, redirige ahí
                if ($request->has('redirect_to_edit')) {
                    // Si viene el id de la actividad principal, redirige ahí
                    if ($request->has('parent_activity_id')) {
                        $parentId = $request->input('parent_activity_id');
                        return redirect()
                            ->route('activities.edit', $parentId)
                            ->withFragment('subactivities-table')
                            ->with('success', 'Analistas actualizados correctamente.')
                            ->with('active_tab', 'basic');
                    }
                    // Si no, redirige a la actividad editada
                    return redirect()->route('activities.edit', $activity)
                        ->with('success', 'Analistas actualizados correctamente.')
                        ->with('active_tab', 'basic');
                }

                // Por defecto, redirigir al index
                return redirect()->route('activities.index')->with('success', 'Analistas actualizados correctamente.');
            } catch (\Illuminate\Validation\ValidationException $e) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $e->errors(),
                        'message' => 'Error de validación'
                    ], 422);
                }
                throw $e;
            } catch (\Exception $e) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error inesperado: ' . $e->getMessage()
                    ], 500);
                }
                throw $e;
            }
        }

        $rules = [
            'name' => 'required|string|max:255',
            'status' => 'nullable|in:no_iniciada,en_ejecucion,en_espera_de_insumos,pausada,en_certificacion_por_cliente,pases_enviados,culminada,cancelada,en_revision,reiterar,atendiendo_hoy',
            'analista_id' => 'required|array|min:1',
            'analista_id.*' => 'exists:analistas,id',
            'requirements' => 'nullable|array',
            'requirements.*' => 'nullable|string|max:10000',
            'comments' => 'nullable|array',
            'comments.*' => 'nullable|string|max:10000',
            'fecha_recepcion' => 'nullable|date',
            'parent_id' => 'nullable|exists:activities,id',
            'description' => 'nullable|string|max:10000',
            'estatus_operacional' => 'nullable|string|max:1000',
            'prioridad' => 'required|integer|min:1',
            'orden_analista' => 'required|integer|min:1',
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_producto_id' => 'nullable|exists:tipos_productos,id',
            'proyecto_id' => 'nullable|exists:proyectos,id',
            'categoria' => 'nullable|array',
            'categoria.*' => 'in:proyecto,incidencia,mejora_continua',
        ];

        // Solo exigir unicidad de 'caso' si es actividad principal (sin parent_id)
        if (is_null($request->input('parent_id'))) {
            $rules['caso'] = [
                'required',
                'string',
                'max:255',
                Rule::unique('activities', 'caso')
                    ->whereNull('parent_id')
                    ->ignore($activity->id)
            ];
        } else {
            $rules['caso'] = 'required|string|max:255';
        }
        $request->validate($rules);

        try {
            // Actualizar la actividad (sin el campo categoria)
            $activity->caso = $request->input('caso');
            $activity->name = $request->input('name');
            $activity->description = $request->input('description');
            $activity->estatus_operacional = $request->input('estatus_operacional');
            $activity->status = $request->input('status');
            $activity->fecha_recepcion = $request->input('fecha_recepcion');
            $activity->parent_id = $request->input('parent_id');
            $activity->prioridad = $request->input('prioridad');
            $activity->orden_analista = $request->input('orden_analista');
            $activity->cliente_id = $request->input('cliente_id');
            $activity->tipo_producto_id = $request->input('tipo_producto_id');
            $activity->proyecto_id = $request->input('proyecto_id');

            // Log para verificar antes de guardar
            Log::info('ANTES DE GUARDAR ACTIVITY', [
                'prioridad' => $activity->prioridad,
                'orden_analista' => $activity->orden_analista
            ]);

            $activity->save();

            // Log para verificar después de guardar
            Log::info('DESPUES DE GUARDAR ACTIVITY', [
                'prioridad' => $activity->prioridad,
                'orden_analista' => $activity->orden_analista
            ]);

            // Sincronizar categorías seleccionadas
            // Guardar categorías seleccionadas
            $categorias = $request->input('categoria');
            if (!$categorias || count(array_filter($categorias)) === 0) {
                $categorias = $request->has('proyecto_id') && $request->input('proyecto_id') ? ['proyecto'] : ['incidencia'];
            }
            Log::info('Antes de guardar categorías', ['data' => $categorias]);
            \DB::table('activity_categoria')->where('activity_id', $activity->id)->delete();
            foreach ($categorias as $cat) {
                \DB::table('activity_categoria')->insert([
                    'activity_id' => $activity->id,
                    'categoria' => $cat,
                ]);
            }
            Log::info('Después de guardar categorías');

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
            'comments.*' => 'nullable|string|max:10000',
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

    /**
     * Actualizar los estados de una actividad (AJAX)
     */
    public function updateStatuses(Request $request, Activity $activity)
    {
        $request->validate([
            'status_ids' => 'required|array',
            'status_ids.*' => 'exists:statuses,id'
        ]);

        try {
            // Sincronizar los estados (esto reemplaza todos los estados existentes)
            $activity->statuses()->sync($request->status_ids);

            // Cargar los estados actualizados
            $activity->load('statuses');

            return response()->json([
                'success' => true,
                'message' => 'Estados actualizados correctamente',
                'statuses' => $activity->statuses,
                'status_badges' => $activity->status_badges
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar los estados: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener todos los estados disponibles (AJAX)
     */
    public function getStatuses()
    {
        $statuses = Status::active()->ordered()->get();

        return response()->json([
            'success' => true,
            'statuses' => $statuses
        ]);
    }

    /**
     * Obtener los estados actuales de una actividad (AJAX)
     */
    public function getActivityStatuses(Activity $activity)
    {
        $activity->load('statuses');

        return response()->json([
            'success' => true,
            'statuses' => $activity->statuses,
            'status_badges' => $activity->status_badges
        ]);
    }

    /**
     * Editar prioridad y orden en la tabla
     */
    public function inlineUpdate(Request $request, Activity $activity)
    {
        $request->validate([
            'field' => 'required|in:prioridad,orden_analista',
            'value' => 'required|integer|min:1'
        ]);

        $activity->{$request->field} = $request->value;
        $activity->save();

        return response()->json(['success' => true, 'value' => $activity->{$request->field}]);
    }

    /**
     * Editar solo analista en la tabla subactividades
     */
    public function updateAnalysts(Request $request, Activity $activity)
    {
        $request->validate([
            'analista_id' => 'required|array|min:1',
            'analista_id.*' => 'exists:analistas,id',
        ]);

        $activity->analistas()->sync($request->analista_id);

        if ($request->ajax() || $request->wantsJson()) {
            $analistas = $activity->analistas()->get(['analistas.id', 'analistas.name']);
            return response()->json([
                'success' => true,
                'analistas' => $analistas
            ]);
        }

        return back()->with('success', 'Analistas actualizados correctamente.');
    }



    /**
     * Exportar actividades filtradas
     */
    public function export(Request $request)
    {
        // Prioriza los filtros de columna si existen
        $statusFilter = $request->get('status_column') ?: $request->get('status');
        if (is_string($statusFilter) && str_contains($statusFilter, ',')) {
            $statusFilter = explode(',', $statusFilter);
        }
        $analistaFilter = $request->get('analista_column') ?: $request->get('analista_id');
        if (is_string($analistaFilter) && str_contains($analistaFilter, ',')) {
            $analistaFilter = explode(',', $analistaFilter);
        }
        $fechaDesde = $request->get('fecha_desde_column') ?: $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta_column') ?: $request->get('fecha_hasta');

        // Aplica los mismos filtros que en el método index o search
        // Filtros para actividades principales
        $mainQuery = Activity::with(['analistas', 'statuses'])
            ->whereNull('parent_id');
        // Filtros para subactividades
        $subQuery = Activity::with(['analistas', 'statuses'])
            ->whereNotNull('parent_id');

        // Filtro por estado
        if (!is_null($statusFilter) && $statusFilter !== '') {
            if (is_array($statusFilter)) {
                $mainQuery->where(function ($q) use ($statusFilter) {
                    $q->whereHas('statuses', function ($subQ) use ($statusFilter) {
                        $subQ->whereIn('name', $statusFilter);
                    })
                        ->orWhereIn('status', $statusFilter);
                });
                $subQuery->where(function ($q) use ($statusFilter) {
                    $q->whereHas('statuses', function ($subQ) use ($statusFilter) {
                        $subQ->whereIn('name', $statusFilter);
                    })
                        ->orWhereIn('status', $statusFilter);
                });
            } else {
                $mainQuery->where(function ($q) use ($statusFilter) {
                    $q->whereHas('statuses', function ($subQ) use ($statusFilter) {
                        $subQ->where('name', $statusFilter);
                    })
                        ->orWhere('status', $statusFilter);
                });
                $subQuery->where(function ($q) use ($statusFilter) {
                    $q->whereHas('statuses', function ($subQ) use ($statusFilter) {
                        $subQ->where('name', $statusFilter);
                    })
                        ->orWhere('status', $statusFilter);
                });
            }
        }

        // Filtro por analista
        if (!is_null($analistaFilter) && $analistaFilter !== '') {
            $mainQuery->whereHas('analistas', function ($q) use ($analistaFilter) {
                $q->whereIn('analistas.id', (array)$analistaFilter);
            });
            $subQuery->whereHas('analistas', function ($q) use ($analistaFilter) {
                $q->whereIn('analistas.id', (array)$analistaFilter);
            });
        }

        // Filtro por fecha desde
        if (!is_null($fechaDesde) && $fechaDesde !== '') {
            $mainQuery->where('fecha_recepcion', '>=', $fechaDesde);
            $subQuery->where('fecha_recepcion', '>=', $fechaDesde);
        }

        // Filtro por fecha hasta
        if (!is_null($fechaHasta) && $fechaHasta !== '') {
            $mainQuery->where('fecha_recepcion', '<=', $fechaHasta);
            $subQuery->where('fecha_recepcion', '<=', $fechaHasta);
        }

        // Filtro por caso
        $caso = $request->get('caso');
        if (!is_null($caso) && $caso !== '') {
            $mainQuery->where('caso', 'LIKE', "%{$caso}%");
            $subQuery->where('caso', 'LIKE', "%{$caso}%");
        }

        // Obtener todas las actividades (padres y subactividades) con filtros y relaciones
        $filteredActivities = Activity::with(['statuses', 'analistas'])
            ->when($request->get('query'), function ($queryBuilder, $query) {
                $queryBuilder->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                        ->orWhere('description', 'LIKE', "%{$query}%")
                        ->orWhere('caso', 'LIKE', "%{$query}%")
                        ->orWhere('status', 'LIKE', "%{$query}%")
                        ->orWhere('fecha_recepcion', 'LIKE', "%{$query}%")
                        ->orWhereHas('analistas', function ($subQ) use ($query) {
                            $subQ->where('name', 'LIKE', "%{$query}%");
                        })
                        ->orWhereHas('statuses', function ($subQ) use ($query) {
                            $subQ->where('name', 'LIKE', "%{$query}%")
                                ->orWhere('label', 'LIKE', "%{$query}%");
                        });
                });
            })
            // Filtro por estado: si tiene estados en la relación, filtra por la relación; si no, por el campo antiguo
            ->when($statusFilter, function ($query) use ($statusFilter) {
                $query->where(function ($q) use ($statusFilter) {
                    $q->whereHas('statuses', function ($subQ) use ($statusFilter) {
                        if (is_array($statusFilter)) {
                            $subQ->whereIn('name', $statusFilter);
                        } else {
                            $subQ->where('name', $statusFilter);
                        }
                    })
                        ->orWhere(function ($subQ) use ($statusFilter) {
                            $subQ->whereDoesntHave('statuses')
                                ->where(function ($subQ2) use ($statusFilter) {
                                    if (is_array($statusFilter)) {
                                        $subQ2->whereIn('status', $statusFilter);
                                    } else {
                                        $subQ2->where('status', $statusFilter);
                                    }
                                });
                        });
                });
            })
            // Filtro por analista (usando $analistaFilter procesado)
            ->when($analistaFilter, function ($query) use ($analistaFilter) {
                $query->whereHas('analistas', function ($q) use ($analistaFilter) {
                    $q->whereIn('analistas.id', (array)$analistaFilter);
                });
            })
            // Filtro por fecha desde (usando $fechaDesde procesado)
            ->when($fechaDesde, function ($query) use ($fechaDesde) {
                $query->where('fecha_recepcion', '>=', $fechaDesde);
            })
            // Filtro por fecha hasta (usando $fechaHasta procesado)
            ->when($fechaHasta, function ($query) use ($fechaHasta) {
                $query->where('fecha_recepcion', '<=', $fechaHasta);
            })
            // Filtro por caso
            ->when($request->get('caso'), function ($query, $caso) {
                $query->where('caso', 'LIKE', "%{$caso}%");
            })
            ->get();

        // Ordena por parent_id y luego por id para mantener cierto orden jerárquico
        $ordered = $filteredActivities->sortBy([
            ['parent_id', 'asc'],
            ['id', 'asc'],
        ])->values();

        // Exportar usando Laravel Excel
        return Excel::download(
            new ActivitiesExport($ordered, $request->get('status')),
            'actividades.xlsx'
        );
    }

    public function exportWord(Request $request)
    {
        // Copia la lógica de filtrado EXACTA del método export()
        $statusFilter = $request->get('status_column') ?: $request->get('status');
        if (is_string($statusFilter) && str_contains($statusFilter, ',')) {
            $statusFilter = explode(',', $statusFilter);
        }
        $analistaFilter = $request->get('analista_column') ?: $request->get('analista_id');
        if (is_string($analistaFilter) && str_contains($analistaFilter, ',')) {
            $analistaFilter = explode(',', $analistaFilter);
        }
        $fechaDesde = $request->get('fecha_desde_column') ?: $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta_column') ?: $request->get('fecha_hasta');

        $filteredActivities = Activity::with(['statuses', 'analistas'])
            ->when($request->get('query'), function ($queryBuilder, $query) {
                $queryBuilder->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                        ->orWhere('description', 'LIKE', "%{$query}%")
                        ->orWhere('caso', 'LIKE', "%{$query}%")
                        ->orWhere('status', 'LIKE', "%{$query}%")
                        ->orWhere('fecha_recepcion', 'LIKE', "%{$query}%")
                        ->orWhereHas('analistas', function ($subQ) use ($query) {
                            $subQ->where('name', 'LIKE', "%{$query}%");
                        })
                        ->orWhereHas('statuses', function ($subQ) use ($query) {
                            $subQ->where('name', 'LIKE', "%{$query}%")
                                ->orWhere('label', 'LIKE', "%{$query}%");
                        });
                });
            })
            // Filtro por estado: si tiene estados en la relación, filtra por la relación; si no, por el campo antiguo
            ->when($statusFilter, function ($query) use ($statusFilter) {
                $query->where(function ($q) use ($statusFilter) {
                    $q->whereHas('statuses', function ($subQ) use ($statusFilter) {
                        if (is_array($statusFilter)) {
                            $subQ->whereIn('name', $statusFilter);
                        } else {
                            $subQ->where('name', $statusFilter);
                        }
                    })
                        ->orWhere(function ($subQ) use ($statusFilter) {
                            $subQ->whereDoesntHave('statuses')
                                ->where(function ($subQ2) use ($statusFilter) {
                                    if (is_array($statusFilter)) {
                                        $subQ2->whereIn('status', $statusFilter);
                                    } else {
                                        $subQ2->where('status', $statusFilter);
                                    }
                                });
                        });
                });
            })
            // Filtro por analista (usando $analistaFilter procesado)
            ->when($analistaFilter, function ($query) use ($analistaFilter) {
                $query->whereHas('analistas', function ($q) use ($analistaFilter) {
                    $q->whereIn('analistas.id', (array)$analistaFilter);
                });
            })
            // Filtro por fecha desde (usando $fechaDesde procesado)
            ->when($fechaDesde, function ($query) use ($fechaDesde) {
                $query->where('fecha_recepcion', '>=', $fechaDesde);
            })
            // Filtro por fecha hasta (usando $fechaHasta procesado)
            ->when($fechaHasta, function ($query) use ($fechaHasta) {
                $query->where('fecha_recepcion', '<=', $fechaHasta);
            })
            // Filtro por caso
            ->when($request->get('caso'), function ($query, $caso) {
                $query->where('caso', 'LIKE', "%{$caso}%");
            })
            ->get();

        // Ordena igual que en export
        $ordered = $filteredActivities->sortBy([
            ['parent_id', 'asc'],
            ['id', 'asc'],
        ])->values();

        // Crear el documento Word
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();

        foreach ($ordered as $activity) {
            $estados = $activity->statuses->count()
                ? $activity->statuses->map(function ($status) {
                    return $status->label ?: $status->name;
                })->implode(', ')
                : ($activity->status_label ?? $activity->status ?? '');

            $analistas = $activity->analistas->pluck('name')->implode(', ');

            /* $section->addText("ID: " . $activity->id); */
            /* $section->addText("Caso:" . ($activity->caso ?? '')); */
            /* $section->addText("Nombre:" . ($activity->name ?? '')); */
            $section->addText(($activity->caso ?? '') . ' ' . ($activity->name ?? '')); /* Esta linea concatena Caso y Nombre */
            /* $section->addText("Descripción: " . ($activity->description ?? '')); */
            $section->addText("Estatus Operacional: " . ($activity->estatus_operacional ?? ''));
            /* $section->addText("Fecha Recepción: " . ($activity->fecha_recepcion ?? '')); */
            /* $section->addText("Prioridad: " . ($activity->prioridad ?? '')); */
            /* $section->addText("Orden Analista: " . ($activity->orden_analista ?? '')); */
            $section->addText("Estado: " . $estados);
            $section->addText("Analista: " . $analistas);

            // Línea separadora entre actividades
            $section->addText(str_repeat('-', 40));
            $section->addTextBreak(1);
        }

        // Descargar el archivo
        $fileName = 'actividades_' . date('Ymd_His') . '.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'word');
        $phpWord->save($tempFile, 'Word2007');

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
