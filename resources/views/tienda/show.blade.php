<x-app-layout>
    <div class="container py-4">

        <a href="{{ route('tienda.index') }}" class="btn btn-link ps-0">&larr; Volver al catálogo</a>

        <div class="card mt-2">
            <div class="card-body">
                <h3>{{ $producto->nombre }}</h3>
                <p class="text-muted mb-1">{{ $producto->categoria->nombre }} · +{{ $producto->edad_minima }} años · {{ $producto->piezas }} piezas</p>

                <p class="fs-4">
                    @if($producto->precio_oferta)
                        <span class="text-decoration-line-through text-muted">₡{{ number_format($producto->precio, 0) }}</span>
                        <span class="text-danger fw-bold">₡{{ number_format($producto->precio_oferta, 0) }}</span>
                    @else
                        <span class="fw-bold">₡{{ number_format($producto->precio, 0) }}</span>
                    @endif
                </p>

                <p>{{ $producto->descripcion }}</p>

                <p>
                    @if($producto->stock > 0)
                        <span class="badge bg-success">Disponible ({{ $producto->stock }} en stock)</span>
                    @else
                        <span class="badge bg-danger">Agotado</span>
                    @endif
                </p>

                <form action="{{ route('carrito.agregar', $producto->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-primary" @disabled($producto->stock <= 0)>Agregar al carrito</button>
                </form>
            </div>
        </div>

        {{-- Vistos recientemente (misma lógica que en el catálogo) --}}
        @php
            $idsVistos = json_decode(request()->cookie('productos_vistos', '[]'), true) ?? [];
            $idsVistos = array_diff($idsVistos, [$producto->id]); // no mostrar el que estoy viendo ahora
            $vistos = \App\Models\Producto::whereIn('id', $idsVistos)->get();
        @endphp

        @if($vistos->isNotEmpty())
            <h6 class="mt-4">Vistos recientemente</h6>
            <div class="row">
                @foreach($vistos as $p)
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('tienda.show', $p->id) }}" class="text-decoration-none">
                            <div class="card h-100">
                                <div class="card-body">
                                    <small>{{ $p->nombre }}</small><br>
                                    <span class="fw-bold">₡{{ number_format($p->precio_oferta ?? $p->precio, 0) }}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</x-app-layout>
