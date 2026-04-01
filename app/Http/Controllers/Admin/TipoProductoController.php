<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoProducto;
use Illuminate\Http\Request;

class TipoProductoController extends Controller
{
    public function index()
    {
        $tipos = TipoProducto::orderBy('nombre')->paginate(15);

        return view('admin.tipos_productos.index', compact('tipos'));
    }

    public function create()
    {
        return view('admin.tipos_productos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
        ]);

        TipoProducto::create($validated);

        return redirect()
            ->route('admin.tipos-productos.index')
            ->with('success', 'Tipo de producto creado correctamente.');
    }

    public function edit(TipoProducto $tipos_producto)
    {
        // Nota: por el nombre de la ruta "tipos-productos", Laravel usa el parámetro "tipos_producto"
        $tipo = $tipos_producto;

        return view('admin.tipos_productos.edit', compact('tipo'));
    }

    public function update(Request $request, TipoProducto $tipos_producto)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
        ]);

        $tipos_producto->update($validated);

        return redirect()
            ->route('admin.tipos-productos.index')
            ->with('success', 'Tipo de producto actualizado correctamente.');
    }

    public function destroy(TipoProducto $tipos_producto)
    {
        $tipos_producto->delete();

        return redirect()
            ->route('admin.tipos-productos.index')
            ->with('success', 'Tipo de producto eliminado correctamente.');
    }
}
