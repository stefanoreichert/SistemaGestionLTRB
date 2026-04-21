<?php

namespace App\Http\Controllers;

use App\Models\Order;

class TicketController extends Controller
{
    /**
     * Vista imprimible del ticket de un pedido cerrado.
     * El ticket se genera DESPUÉS de cerrar el pedido.
     * No tiene side effects: solo muestra datos (bug del legado corregido).
     * El auto-print lo ejecuta window.print() en el JS de la vista.
     */
    public function show(Order $order)
    {
        if (! $order->isClosed()) {
            return redirect()->route('orders.show', $order->table)
                ->with('error', 'El pedido debe estar cerrado para imprimir el ticket.');
        }

        $order->load('items.product', 'table', 'user');

        return view('tickets.show', compact('order'));
    }
}
