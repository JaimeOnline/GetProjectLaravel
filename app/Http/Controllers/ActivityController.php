<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with('users')->get();
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
        ]);
        // Crear la actividad
        $activity = Activity::create($request->all());
        // Asignar usuarios a la actividad
        $activity->users()->attach($request->user_id);
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
            'user_id' => 'required|array', // Cambia esto para permitir múltiples usuarios
            'user_id.*' => 'exists:users,id', // Validar que cada ID de usuario exista
        ]);
        $activity->update($request->all());
        // Asignar usuarios a la actividad
        $activity->users()->sync($request->user_id); // Usa sync para actualizar la relación
        return redirect()->route('activities.index')->with('success', 'Actividad actualizada con éxito.');
    }
    public function destroy(Activity $activity)
    {
        $activity->delete();
        return redirect()->route('activities.index')->with('success', 'Actividad eliminada con éxito.');
    }
}
