<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background-color: #eee; }
        .text-end { text-align: right; }
    </style>
</head>
<body>
    <h2>Reporte de ventas por cliente</h2>
    <p>Generado: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Cantidad de pedidos</th>
                <th class="text-end">Total gastado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pedidos as $fila)
                <tr>
                    <td>{{ $fila->user->name ?? 'Usuario eliminado' }}</td>
                    <td>{{ $fila->total_pedidos }}</td>
                    <td class="text-end">&#8353;{{ number_format($fila->total_ventas, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No hay pedidos confirmados todavía.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
