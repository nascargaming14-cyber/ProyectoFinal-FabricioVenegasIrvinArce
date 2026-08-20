<x-app-layout>
    <div class="container py-4">
        <h3>Mi carrito</h3>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($items->isEmpty())
            <p>Tu carrito está vacío. <a href="{{ route('tienda.index') }}">Ir a la tienda</a></p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio unitario</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $item->producto->nombre }}</td>
                            <td>₡{{ number_format($item->precio_unitario, 0) }}</td>
                            <td>
                                <form action="{{ route('carrito.actualizar', $item) }}" method="POST" class="d-flex gap-1">
                                    @csrf @method('PATCH')
                                    <input type="number" name="cantidad" value="{{ $item->cantidad }}" min="1" max="{{ $item->producto->stock }}" class="form-control form-control-sm" style="width: 70px">
                                    <button class="btn btn-sm btn-outline-secondary">↻</button>
                                </form>
                            </td>
                            <td>₡{{ number_format($item->cantidad * $item->precio_unitario, 0) }}</td>
                            <td>
                                <form action="{{ route('carrito.eliminar', $item) }}" method="POST" onsubmit="return confirm('¿Quitar este producto?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Quitar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="text-end">
                <p>Subtotal: ₡{{ number_format($subtotal, 0) }}</p>
                <p>Impuesto (13%): ₡{{ number_format($impuesto, 0) }}</p>
                <p>Envío: ₡{{ number_format($costoEnvio, 0) }}</p>
                <p class="fs-5 fw-bold">Total: ₡{{ number_format($total, 0) }}</p>

                <form action="{{ route('carrito.checkout') }}" method="POST" class="d-inline-block text-start" style="width: 260px">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">Método de pago</label>
                        <select name="metodo_pago" class="form-select" required>
                            <option value="tarjeta">Tarjeta de crédito</option>
                            <option value="paypal">PayPal</option>
                        </select>
                    </div>
                    <button class="btn btn-success w-100">Confirmar compra</button>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>

