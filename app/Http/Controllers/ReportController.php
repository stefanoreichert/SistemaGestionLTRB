<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Reporte del día: ítems de pedidos cerrados HOY, agrupados por mesa.
     */
    public function daily()
    {
        $items = OrderItem::with(['product', 'order.table'])
            ->whereHas('order', function ($q) {
                $q->where('status', 'closed')
                  ->whereDate('closed_at', today());
            })
            ->get();

        // Agrupar por número de mesa para la vista
        $byTable = $items->groupBy(fn($i) => $i->order->table->number)->sortKeys();
        $total   = $items->sum(fn($i) => $i->quantity * $i->unit_price);

        return view('reports.daily', compact('byTable', 'total'));
    }

    /**
     * Reporte del mes: ventas del mes actual agrupadas por producto, ordenadas
     * de mayor a menor (ranking de ventas). Equivalente al reporte mensual original.
     */
    public function monthly()
    {
        $items = OrderItem::with('product')
            ->whereHas('order', function ($q) {
                $q->where('status', 'closed')
                  ->whereYear('closed_at', now()->year)
                  ->whereMonth('closed_at', now()->month);
            })
            ->select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(quantity * unit_price) as total_amount'),
                DB::raw('MIN(unit_price) as unit_price') // precio de referencia
            )
            ->groupBy('product_id')
            ->orderByDesc('total_amount')
            ->get();

        $total = $items->sum('total_amount');

        return view('reports.monthly', compact('items', 'total'));
    }

    /**
     * Eliminar permanentemente todos los pedidos cerrados de HOY.
     * Los order_items se eliminan en cascada por la FK.
     * Equivalente a "Limpiar y Reiniciar" del reporte diario original.
     */
    public function clearDaily()
    {
        Order::where('status', 'closed')
            ->whereDate('closed_at', today())
            ->delete();

        return redirect()->route('reports.daily')
            ->with('success', 'Registros del día eliminados correctamente.');
    }

    /**
     * Eliminar permanentemente todos los pedidos cerrados del MES ACTUAL.
     * Equivalente a "Limpiar y Reiniciar" del reporte mensual original.
     */
    public function clearMonthly()
    {
        Order::where('status', 'closed')
            ->whereYear('closed_at', now()->year)
            ->whereMonth('closed_at', now()->month)
            ->delete();

        return redirect()->route('reports.monthly')
            ->with('success', 'Registros del mes eliminados correctamente.');
    }
}
