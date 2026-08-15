<x-app-layout>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Productos</h3>
            <a href="{{ route('productos.create') }}" class="btn btn-primary">Nuevo producto</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $producto)
                    <tr>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->categoria->nombre }}</td>
                        <td>₡{{ number_format($producto->precio_oferta ?? $producto->precio, 0) }}</td>
                        <td>{{ $producto->stock }}</td>
                        <td>
                            <a href="{{ route('productos.edit', $producto) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                            <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este producto?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $productos->links() }}
    </div>
</x-app-layout>
