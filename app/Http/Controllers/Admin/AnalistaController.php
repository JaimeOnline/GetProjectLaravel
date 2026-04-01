<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Analista;
use Illuminate\Http\Request;

class AnalistaController extends Controller
{
    public function index()
    {
        $analistas = Analista::orderBy('name')->paginate(15);

        return view('admin.analistas.index', compact('analistas'));
    }

    public function create()
    {
        return view('admin.analistas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        Analista::create($validated);

        return redirect()
            ->route('admin.analistas.index')
            ->with('success', 'Analista creado correctamente.');
    }

    public function edit(Analista $analista)
    {
        return view('admin.analistas.edit', compact('analista'));
    }

    public function update(Request $request, Analista $analista)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $analista->update($validated);

        return redirect()
            ->route('admin.analistas.index')
            ->with('success', 'Analista actualizado correctamente.');
    }

    public function destroy(Analista $analista)
    {
        // Si quisieras validar que no tenga actividades asociadas, aquí es el lugar
        $analista->delete();

        return redirect()
            ->route('admin.analistas.index')
            ->with('success', 'Analista eliminado correctamente.');
    }
}
