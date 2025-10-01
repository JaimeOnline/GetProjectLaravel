<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyecto;

class ProyectoController extends Controller
{
    public function index()
    {
        $proyectos = Proyecto::all();
        return view('projects.index', compact('proyectos'));
    }

    public function create()
    {
        // Retorna la vista para crear un proyecto
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $proyecto = new \App\Models\Proyecto();
        $proyecto->nombre = $request->nombre;
        $proyecto->save();

        return redirect()->route('projects.index')->with('success', 'Proyecto creado correctamente.');
    }
}
