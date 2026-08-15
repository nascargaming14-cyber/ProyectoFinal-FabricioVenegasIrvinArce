<x-app-layout>
    <div class="container py-4">
        <h3>Nuevo producto</h3>

        <form action="{{ route('productos.store') }}" method="POST">
            @csrf
            @include('productos.form')
            <button class="btn btn-primary">Guardar</button>
        </form>
    </div>
</x-app-layout>
