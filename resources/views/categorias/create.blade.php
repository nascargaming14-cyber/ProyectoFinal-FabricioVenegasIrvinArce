<x-app-layout>
    <div class="container py-4">
        <h3>Nueva categoría</h3>

        <form action="{{ route('categorias.store') }}" method="POST">
            @csrf
            @include('categorias.form')
            <button class="btn btn-primary">Guardar</button>
        </form>
    </div>
</x-app-layout>
