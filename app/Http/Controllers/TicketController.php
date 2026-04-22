<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\TicketService;

class TicketController extends Controller
{
    public function __construct(private readonly TicketService $ticketService) {}

    /**
     * Vista imprimible del ticket (58mm, optimizada para impresora térmica).
     * Solo accesible cuando el pedido está cerrado.
     */
    public function show(Order $order)
    {
        if (! $order->isClosed()) {
            return redirect()->route('orders.show', $order->table)
                ->with('error', 'El pedido debe estar cerrado para imprimir el ticket.');
        }

        $order->load('items.product', 'table', 'user');

        $lines = $this->ticketService->generateLines($order);

        return view('tickets.show', compact('order', 'lines'));
    }

    /**
     * Devuelve el ticket como texto plano (para enviar por socket a impresora).
     * Útil para integración con servidor de impresión o script local.
     */
    public function raw(Order $order)
    {
        if (! $order->isClosed()) {
            return response('Pedido no cerrado.', 422)->header('Content-Type', 'text/plain');
        }

        $order->load('items.product', 'table', 'user');

        $text = $this->ticketService->generatePlainText($order);

        return response($text, 200)
            ->header('Content-Type', 'text/plain; charset=utf-8')
            ->header('Content-Disposition', 'inline; filename="ticket-' . $order->id . '.txt"');
    }

    /**
     * Descarga bytes ESC/POS binarios para envío directo a impresora térmica.
     * Compatible con impresoras Epson TM-T20, Bixolon, Star, HOIN y similares.
     */
    public function escpos(Order $order)
    {
        if (! $order->isClosed()) {
            return response('Pedido no cerrado.', 422)->header('Content-Type', 'text/plain');
        }

        $order->load('items.product', 'table', 'user');

        $bytes = $this->ticketService->generateEscPos($order);

        return response($bytes, 200)
            ->header('Content-Type', 'application/octet-stream')
            ->header('Content-Disposition', 'attachment; filename="ticket-' . $order->id . '.bin"')
            ->header('Content-Length', strlen($bytes));
    }
}
