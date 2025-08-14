<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use App\Models\Requirement; // Asegúrate de importar el modelo Requirement
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        // Obtener todas las actividades con sus usuarios y subactividades
        $activities = Activity::with('users', 'subactivities')->get();
        return view('activities.index', compact('activities'));
    }
    public function create()
    {
        // Obtener todos los usuarios
        $users = User::all();

        // Obtener todas las actividades para el campo de actividad padre
        $activities = Activity::all();
        // Pasar las variables a la vista
        return view('activities.create', compact('users', 'activities'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
            'user_id' => 'required|array',
            'user_id.*' => 'exists:users,id', // Validar que cada ID de usuario exista
            'requirements' => 'nullable|array', // Solo array, permitiendo que esté vacío
            'fecha_recepcion' => 'nullable|date', // Validar que la fecha de recepción sea una fecha válida si se proporciona
            'caso' => 'required|unique:activities,caso', // Validar que el campo 'caso' sea único en la tabla 'activities'
            'parent_id' => 'nullable|exists:activities,id', // Validar que el parent_id exista si se proporciona
        ]);
        // Crear la actividad
        $activity = Activity::create($request->only(['caso', 'name', 'description', 'status', 'fecha_recepcion', 'parent_id']));
        // Asignar usuarios a la actividad
        $activity->users()->attach($request->user_id);
        // Agregar los requerimientos solo si existen
        if ($request->has('requirements')) {
            foreach ($request->requirements as $requirementDescription) {
                Requirement::create([
                    'activity_id' => $activity->id,
                    'description' => $requirementDescription,
                ]);
            }
        }
        return redirect()->route('activities.index')->with('success', 'Actividad creada con éxito.');
    }
    public function edit(Activity $activity)
    {
        // Obtener todos los usuarios
        $users = User::all();
        // Obtener todas las actividades para el campo de actividad padre
        $activities = Activity::all();
        // Pasar las variables a la vista
        return view('activities.edit', compact('activity', 'users', 'activities'));
    }
    public function update(Request $request, Activity $activity)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
            'user_id' => 'required|array',
            'user_id.*' => 'exists:users,id',
            'requirements' => 'nullable|array',
            'fecha_recepcion' => 'nullable|date',
            'caso' => 'required|unique:activities,caso,' . $activity->id,
            'parent_id' => 'nullable|exists:activities,id', // Validar que el parent_id exista si se proporciona
        ]);
        // Actualizar la actividad
        $activity->update($request->only(['caso', 'name', 'description', 'status', 'fecha_recepcion', 'parent_id']));
        // Asignar usuarios a la actividad
        $activity->users()->sync($request->user_id); // Usar sync para actualizar la relación
        // Limpiar los requerimientos existentes y agregar los nuevos solo si existen
        $activity->requirements()->delete(); // Eliminar los requerimientos existentes
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
        return redirect()->route('activities.index')->with('success', 'Actividad actualizada con éxito.');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return redirect()->route('activities.index')->with('success', 'Actividad eliminada con éxito.');
    }
}
