<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    // Pantalla para elegir qué reporte generar
    public function index()
    {
        $usuarios = User::orderBy('name')->get();
        return view('reportes.index', compact('usuarios'));
    }

    // Reporte de ventas agrupado por mes
    public function porMes(Request $request)
    {
        $pedidos = Pedido::where('estado', 'confirmado')
            ->selectRaw("strftime('%Y-%m', fecha_compra) as mes, COUNT(*) as total_pedidos, SUM(monto_total) as total_ventas")
            ->groupBy('mes')
            ->orderBy('mes', 'desc')
            ->get();

        $pdf = Pdf::loadView('reportes.pdf-mes', compact('pedidos'));
        return $pdf->download('reporte-ventas-por-mes.pdf');
    }

    // Reporte de ventas agrupado por cliente
    public function porCliente(Request $request)
    {
        $pedidos = Pedido::with('user')
            ->where('estado', 'confirmado')
            ->selectRaw('user_id, COUNT(*) as total_pedidos, SUM(monto_total) as total_ventas')
            ->groupBy('user_id')
            ->orderByDesc('total_ventas')
            ->get();

        $pdf = Pdf::loadView('reportes.pdf-cliente', compact('pedidos'));
        return $pdf->download('reporte-ventas-por-cliente.pdf');
    }
}
