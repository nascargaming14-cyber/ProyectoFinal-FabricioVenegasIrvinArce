<x-app-layout>
    <div class="container py-4">
        <h3>Reportes de ventas</h3>

        <div class="row mt-4">
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5>Ventas por mes</h5>
                        <p class="text-muted">Totales agrupados por mes de compra.</p>
                        <a href="{{ route('reportes.mes') }}" class="btn btn-primary">Descargar PDF</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5>Ventas por cliente</h5>
                        <p class="text-muted">Totales agrupados por cliente.</p>
                        <a href="{{ route('reportes.cliente') }}" class="btn btn-primary">Descargar PDF</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
