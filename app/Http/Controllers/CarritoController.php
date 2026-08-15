<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Carrito;
use App\Models\CarritoItem;
use App\Models\Producto;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    // Obtiene (o crea) el carrito del usuario logueado
    private function carritoActual()
    {
        return Carrito::firstOrCreate(['user_id' => Auth::id()]);
    }

    public function index()
    {
        $carrito = $this->carritoActual();
        $items = $carrito->items()->with('producto')->get();

        $subtotal = $items->sum(fn($item) => $item->cantidad * $item->precio_unitario);
        $impuesto = round($subtotal * 0.13, 2); // IVA 13%
        $total = $subtotal + $impuesto;

        return view('carrito.index', compact('items', 'subtotal', 'impuesto', 'total'));
    }

    public function agregar(Request $request, Producto $producto)
    {
        $carrito = $this->carritoActual();

        $item = $carrito->items()->where('producto_id', $producto->id)->first();

        if ($item) {
            $item->increment('cantidad');
        } else {
            $precio = $producto->precio_oferta ?? $producto->precio;
            $carrito->items()->create([
                'producto_id'     => $producto->id,
                'cantidad'        => 1,
                'precio_unitario' => $precio,
            ]);
        }

        return redirect()->route('carrito.index')->with('success', 'Producto agregado al carrito.');
    }

    public function actualizar(Request $request, CarritoItem $item)
    {
        $request->validate(['cantidad' => 'required|integer|min:1']);
        $item->update(['cantidad' => $request->cantidad]);

        return redirect()->route('carrito.index')->with('success', 'Carrito actualizado.');
    }

    public function eliminar(CarritoItem $item)
    {
        $item->delete();
        return redirect()->route('carrito.index')->with('success', 'Producto eliminado del carrito.');
    }

        public function checkout(Request $request)
    {
        $request->validate([
            'metodo_pago' => 'required|in:tarjeta,paypal',
        ]);

        $carrito = $this->carritoActual();
        $items = $carrito->items()->with('producto')->get();

        if ($items->isEmpty()) {
            return redirect()->route('carrito.index')->with('error', 'Tu carrito está vacío.');
        }

        $subtotal = $items->sum(fn($item) => $item->cantidad * $item->precio_unitario);
        $impuesto = round($subtotal * 0.13, 2);
        $costoEnvio = $subtotal >= 50000 ? 0 : 3000; // envío gratis sobre ₡50,000
        $total = $subtotal + $impuesto + $costoEnvio;

        $pedido = \App\Models\Pedido::create([
            'user_id'            => Auth::id(),
            'fecha_compra'       => now(),
            'monto_total'        => $total,
            'impuesto'           => $impuesto,
            'costo_envio'        => $costoEnvio,
            'numero_seguimiento' => strtoupper(\Illuminate\Support\Str::random(10)),
            'estado'             => 'confirmado',
            'metodo_pago'        => $request->metodo_pago,
        ]);

        foreach ($items as $item) {
            $pedido->items()->create([
                'producto_id'     => $item->producto_id,
                'cantidad'        => $item->cantidad,
                'precio_unitario' => $item->precio_unitario,
            ]);

            // descuenta stock
            $item->producto->decrement('stock', $item->cantidad);
        }

        $items->each->delete(); // vacía el carrito

        return redirect()->route('pedidos.confirmacion', $pedido)->with('success', '¡Pedido confirmado!');
    }
    public function confirmacion(\App\Models\Pedido $pedido)
    {
        return view('carrito.confirmacion', compact('pedido'));
    }
}
