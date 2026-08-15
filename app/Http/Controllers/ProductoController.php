<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    // Catálogo público con búsqueda y filtros
    public function catalogo(Request $request)
    {
        $query = Producto::with('categoria');

        if ($request->filled('q')) {
            $query->where('nombre', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('precio_min')) {
            $query->where('precio', '>=', $request->precio_min);
        }

        if ($request->filled('precio_max')) {
            $query->where('precio', '<=', $request->precio_max);
        }

        $productos = $query->orderBy('nombre')->paginate(12)->withQueryString();
        $categorias = Categoria::orderBy('nombre')->get();

        return view('tienda.index', compact('productos', 'categorias'));
    }

    // Detalle del producto + cookie de vistos recientemente
    public function show(Request $request, Producto $producto)
    {
        $id = (int) $producto->id;

        $idsVistos = json_decode($request->cookie('productos_vistos', '[]'), true) ?? [];
        $idsVistos = array_map('intval', $idsVistos);
        $idsVistos = array_values(array_diff($idsVistos, [$id]));
        array_unshift($idsVistos, $id);
        $idsVistos = array_slice($idsVistos, 0, 5);

        $cookie = cookie('productos_vistos', json_encode($idsVistos), 45000);

        return response()
            ->view('tienda.show', compact('producto'))
            ->cookie($cookie);
    }

    // --- Administración (CRUD) ---

    public function index()
    {
        $productos = Producto::with('categoria')->orderBy('nombre')->paginate(10);
        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'categoria_id'   => 'required|exists:categorias,id',
            'nombre'         => 'required|string|max:255',
            'descripcion'    => 'nullable|string',
            'precio'         => 'required|numeric|min:0',
            'precio_oferta'  => 'nullable|numeric|min:0|lt:precio',
            'edad_minima'    => 'required|integer|min:0',
            'piezas'         => 'required|integer|min:1',
            'stock'          => 'required|integer|min:0',
        ]);

        Producto::create($validated);

        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente.');
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'categoria_id'   => 'required|exists:categorias,id',
            'nombre'         => 'required|string|max:255',
            'descripcion'    => 'nullable|string',
            'precio'         => 'required|numeric|min:0',
            'precio_oferta'  => 'nullable|numeric|min:0|lt:precio',
            'edad_minima'    => 'required|integer|min:0',
            'piezas'         => 'required|integer|min:1',
            'stock'          => 'required|integer|min:0',
        ]);

        $producto->update($validated);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('productos.index')->with('success', 'Producto eliminado.');
    }
}
