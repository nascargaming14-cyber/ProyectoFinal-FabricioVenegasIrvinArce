<x-app-layout>
    <div class="container py-4">
        <div class="alert alert-success">
            <h4>¡Gracias por tu compra!</h4>
            <p>Número de seguimiento: <strong>{{ $pedido->numero_seguimiento }}</strong></p>
        </div>

        <table class="table">
            <thead>
                <tr><th>Producto</th><th>Cantidad</th><th>Precio</th></tr>
            </thead>
            <tbody>
                @foreach($pedido->items as $item)
                    <tr>
                        <td>{{ $item->producto->nombre }}</td>
                        <td>{{ $item->cantidad }}</td>
                        <td>₡{{ number_format($item->precio_unitario, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p>Impuesto: ₡{{ number_format($pedido->impuesto, 0) }}</p>
        <p>Envío: ₡{{ number_format($pedido->costo_envio, 0) }}</p>
        <p class="fs-5 fw-bold">Total: ₡{{ number_format($pedido->monto_total, 0) }}</p>
        <p>Método de pago: {{ $pedido->metodo_pago }}</p>

        <a href="{{ route('tienda.index') }}" class="btn btn-primary">Seguir comprando</a>
    </div>
</x-app-layout>
