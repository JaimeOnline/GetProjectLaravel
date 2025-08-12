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
        // Obtener todas las actividades con sus usuarios y requerimientos
        $activities = Activity::with('users', 'requirements')->get();
        return view('activities.index', compact('activities'));
    }
    public function create()
    {
        // Obtener todos los usuarios
        $users = User::all();
        // Pasar la variable $users a la vista
        return view('activities.create', compact('users'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
            'user_id' => 'required|array', // Asegúrate de que sea un array
            'user_id.*' => 'exists:users,id', // Validar que cada ID de usuario exista
            'requirements' => 'nullable|array', // Solo array, permitiendo que esté vacío
            //'requirements.*' => 'string', // Validar que cada requerimiento sea una cadena si se proporciona
            'fecha_recepcion' => 'nullable|date', // Validar que la fecha de recepción sea una fecha válida si se proporciona
        ]);
        // Crear la actividad
        $activity = Activity::create($request->only(['name', 'description', 'status', 'fecha_recepcion']));
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
        $users = User::all(); // Obtener todos los usuarios
        return view('activities.edit', compact('activity', 'users')); // Pasar tanto la actividad como los usuarios
    }
    public function update(Request $request, Activity $activity)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
            'user_id' => 'required|array', // Asegúrate de que sea un array
            'user_id.*' => 'exists:users,id', // Validar que cada ID de usuario exista
            'requirements' => 'nullable|array', // Permitir que sea un array, permitiendo que esté vacío
            // 'requirements.*' => 'string', // CValidar que cada requerimiento sea una cadena si se proporciona
            'fecha_recepcion' => 'nullable|date', // Validar que la fecha de recepción sea una fecha válida si se proporciona

        ]);
        // Actualizar la actividad
        $activity->update($request->only(['name', 'description', 'status', 'fecha_recepcion']));

        // Asignar usuarios a la actividad
        $activity->users()->sync($request->user_id); // Usar sync para actualizar la relación

        // Limpiar los requerimientos existentes y agregar los nuevos solo si existen
        $activity->requirements()->delete(); // Eliminar los requerimientos existentes
        if ($request->has('requirements')) {
            foreach ($request->requirements as $requirementDescription) {
                // Solo agregar si el requerimiento no está vacío
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
