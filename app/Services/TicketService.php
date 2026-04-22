<?php

namespace App\Services;

use App\Models\Order;

/**
 * TicketService — Generación de tickets para impresora térmica 58mm.
 *
 * No modifica la lógica de órdenes, cocina ni mesas.
 * Solo lee datos del pedido y genera el texto/bytes del comprobante.
 */
class TicketService
{
    // ── Comandos ESC/POS ─────────────────────────────────────────────────────
    private const ESC = "\x1B";
    private const GS  = "\x1D";
    private const LF  = "\x0A";

    // ── Config ────────────────────────────────────────────────────────────────

    private int    $width;
    private string $name;
    private string $address;
    private string $contact;
    private string $message;

    public function __construct()
    {
        $this->width   = (int) config('restaurant.thermal_width', 32);
        $this->name    = (string) config('restaurant.name',    'LOS TRONCOS RESTO BAR');
        $this->address = (string) config('restaurant.address', 'Av. Principal 123');
        $this->contact = (string) config('restaurant.contact', 'Tel: (011) 1234-5678');
        $this->message = (string) config('restaurant.message', 'Gracias por elegirnos');
    }

    // ── API pública ───────────────────────────────────────────────────────────

    /**
     * Genera un array de líneas de texto para el ticket.
     * Cada línea tiene exactamente $width caracteres o menos.
     */
    public function generateLines(Order $order): array
    {
        $w     = $this->width;
        $lines = [];

        // ── Encabezado ────────────────────────────────────────────────────────
        $lines[] = $this->center($this->name, $w);

        if ($this->address !== '') {
            $lines[] = $this->center($this->address, $w);
        }
        if ($this->contact !== '') {
            $lines[] = $this->center($this->contact, $w);
        }

        $lines[] = $this->divider('=', $w);

        // ── Datos del pedido ──────────────────────────────────────────────────
        $lines[] = $this->leftRight('Ticket #:', str_pad((string) $order->id, 6, '0', STR_PAD_LEFT), $w);
        $lines[] = $this->leftRight('Mesa:',    (string) $order->table->number, $w);

        $waiter = mb_substr($order->user?->name ?? 'Sistema', 0, 18);
        $lines[] = $this->leftRight('Mozo:', $waiter, $w);

        $closedAt = $order->closed_at ?? now();
        $lines[]  = $this->leftRight('Fecha:', $closedAt->format('d/m/Y'), $w);
        $lines[]  = $this->leftRight('Hora:',  $closedAt->format('H:i'), $w);

        $lines[] = $this->divider('-', $w);

        // ── Cabecera de columnas ──────────────────────────────────────────────
        // "PRODUCTO          CANT  IMPORTE"  = 32 chars
        //  18 chars           4      10
        $lines[] = str_pad('PRODUCTO', 18)
            . str_pad('CANT',    4, ' ', STR_PAD_LEFT)
            . str_pad('IMPORTE', 10, ' ', STR_PAD_LEFT);

        $lines[] = $this->divider('-', $w);

        // ── Ítems ─────────────────────────────────────────────────────────────
        foreach ($order->items as $item) {
            $name    = mb_substr($item->product->name ?? '?', 0, 18);
            $qty     = (string) $item->quantity;
            $subStr  = '$' . number_format($item->quantity * $item->unit_price, 0, ',', '.');
            $unitStr = '  c/u: $' . number_format($item->unit_price, 0, ',', '.');

            $lines[] = str_pad($name, 18)
                . str_pad($qty,    4, ' ', STR_PAD_LEFT)
                . str_pad($subStr, 10, ' ', STR_PAD_LEFT);

            $lines[] = $unitStr;

            // Notas del ítem (muy importante — siempre incluir si existen)
            if (!empty($item->notes)) {
                $prefix   = '  >> ';
                $maxWidth = $w - mb_strlen($prefix);
                foreach ($this->wrapText($item->notes, $maxWidth) as $noteLine) {
                    $lines[] = $prefix . $noteLine;
                }
            }
        }

        $lines[] = $this->divider('=', $w);

        // ── Totales ───────────────────────────────────────────────────────────
        $subtotal = $order->items->sum(fn($i) => $i->quantity * $i->unit_price);
        $total    = $order->total ?? $subtotal;

        $lines[] = $this->leftRight('Subtotal:', '$' . number_format($subtotal, 0, ',', '.'), $w);

        if (abs($total - $subtotal) > 0.01) {
            $discount = $subtotal - $total;
            $lines[]  = $this->leftRight('Descuento:', '-$' . number_format($discount, 0, ',', '.'), $w);
        }

        $lines[] = $this->divider('-', $w);
        $lines[] = $this->center('TOTAL: $' . number_format($total, 0, ',', '.'), $w);
        $lines[] = $this->divider('=', $w);

        // ── Pie ───────────────────────────────────────────────────────────────
        $lines[] = $this->center('GRACIAS POR SU VISITA', $w);

        if ($this->message !== '') {
            $lines[] = $this->center($this->message, $w);
        }

        $lines[] = '';
        $lines[] = '';

        return $lines;
    }

    /**
     * Genera el ticket completo como texto plano (listo para socket/archivo).
     */
    public function generateTicket(int $orderId): string
    {
        $order = \App\Models\Order::with(['items.product', 'table', 'user'])->findOrFail($orderId);
        return $this->generatePlainText($order);
    }

    /**
     * Genera el texto plano del ticket a partir de un modelo Order cargado.
     */
    public function generatePlainText(Order $order): string
    {
        return implode("\n", $this->generateLines($order));
    }

    /**
     * Formatea texto arbitrario para impresora térmica:
     * normaliza saltos de línea y trunca cada línea al ancho máximo.
     */
    public function formatForThermalPrinter(string $text): string
    {
        $lines  = explode("\n", str_replace(["\r\n", "\r"], "\n", $text));
        $result = array_map(fn($l) => mb_substr($l, 0, $this->width), $lines);
        return implode("\n", $result);
    }

    /**
     * Genera bytes ESC/POS binarios para impresora térmica 58mm.
     * Compatible con impresoras Epson TM-T20, Star SP, Bixolon, HOIN, etc.
     *
     * Headers recomendados para la respuesta HTTP:
     *   Content-Type: application/octet-stream
     *   Content-Disposition: attachment; filename="ticket-{id}.bin"
     */
    public function generateEscPos(Order $order): string
    {
        $ESC = self::ESC;
        $GS  = self::GS;
        $LF  = self::LF;
        $w   = $this->width;
        $out = '';

        // Inicializar impresora
        $out .= $ESC . '@';

        // Código de página PC850 (Multilingual — soporta ñ, á, é, í, ó, ú)
        $out .= $ESC . 't' . chr(2);

        // ── Encabezado: centrado, negrita, doble alto/ancho ──────────────────
        $out .= $ESC . 'a' . chr(1);          // alinear centro
        $out .= $ESC . '!' . chr(0x38);        // doble alto + doble ancho + negrita
        $out .= $this->toCP850($this->name) . $LF;
        $out .= $ESC . '!' . chr(0x00);        // normal
        $out .= $this->toCP850($this->address) . $LF;
        $out .= $this->toCP850($this->contact) . $LF;
        $out .= str_repeat('=', $w) . $LF;

        // ── Datos del pedido: izquierda ───────────────────────────────────────
        $out .= $ESC . 'a' . chr(0); // alinear izquierda

        $closedAt = $order->closed_at ?? now();
        $waiter   = mb_substr($order->user?->name ?? 'Sistema', 0, 18);

        $out .= $this->toCP850('Ticket #: ' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT)) . $LF;
        $out .= $this->toCP850('Mesa    : ' . $order->table->number) . $LF;
        $out .= $this->toCP850('Mozo    : ' . $waiter) . $LF;
        $out .= $this->toCP850('Fecha   : ' . $closedAt->format('d/m/Y')) . $LF;
        $out .= $this->toCP850('Hora    : ' . $closedAt->format('H:i')) . $LF;
        $out .= str_repeat('-', $w) . $LF;

        // ── Cabecera columnas ─────────────────────────────────────────────────
        $out .= str_pad('PRODUCTO', 18)
            . str_pad('CANT',    4, ' ', STR_PAD_LEFT)
            . str_pad('IMPORTE', 10, ' ', STR_PAD_LEFT) . $LF;
        $out .= str_repeat('-', $w) . $LF;

        // ── Ítems ─────────────────────────────────────────────────────────────
        foreach ($order->items as $item) {
            $name   = mb_substr($item->product->name ?? '?', 0, 18);
            $qty    = (string) $item->quantity;
            $subStr = '$' . number_format($item->quantity * $item->unit_price, 0, ',', '.');

            $out .= $this->toCP850(
                str_pad($name, 18)
                . str_pad($qty,    4, ' ', STR_PAD_LEFT)
                . str_pad($subStr, 10, ' ', STR_PAD_LEFT)
            ) . $LF;

            $out .= $this->toCP850('  c/u: $' . number_format($item->unit_price, 0, ',', '.')) . $LF;

            if (!empty($item->notes)) {
                $prefix   = '  >> ';
                $maxWidth = $w - mb_strlen($prefix);
                foreach ($this->wrapText($item->notes, $maxWidth) as $noteLine) {
                    $out .= $this->toCP850($prefix . $noteLine) . $LF;
                }
            }
        }

        $out .= str_repeat('=', $w) . $LF;

        // ── Totales ───────────────────────────────────────────────────────────
        $subtotal = $order->items->sum(fn($i) => $i->quantity * $i->unit_price);
        $total    = $order->total ?? $subtotal;

        $out .= $this->toCP850($this->leftRight('Subtotal:', '$' . number_format($subtotal, 0, ',', '.'), $w)) . $LF;

        if (abs($total - $subtotal) > 0.01) {
            $discount = $subtotal - $total;
            $out .= $this->toCP850(
                $this->leftRight('Descuento:', '-$' . number_format($discount, 0, ',', '.'), $w)
            ) . $LF;
        }

        $out .= str_repeat('-', $w) . $LF;

        // Total: centrado, negrita, doble tamaño
        $out .= $ESC . 'a' . chr(1);
        $out .= $ESC . '!' . chr(0x08); // negrita
        $out .= $this->toCP850('TOTAL: $' . number_format($total, 0, ',', '.')) . $LF;
        $out .= $ESC . '!' . chr(0x00);
        $out .= str_repeat('=', $w) . $LF;

        // ── Pie ───────────────────────────────────────────────────────────────
        $out .= $this->toCP850('GRACIAS POR SU VISITA') . $LF;

        if ($this->message !== '') {
            $out .= $this->toCP850($this->message) . $LF;
        }

        $out .= $LF;

        // Avanzar papel y cortar
        $out .= $ESC . 'd' . chr(4);        // avanzar 4 líneas
        $out .= $GS  . 'V' . chr(66) . chr(0); // corte parcial

        return $out;
    }

    // ── Helpers de formato ────────────────────────────────────────────────────

    /**
     * Centra texto dentro de $width caracteres.
     */
    public function center(string $text, int $width): string
    {
        $len = mb_strlen($text);
        if ($len >= $width) {
            return mb_substr($text, 0, $width);
        }
        $pad = intdiv($width - $len, 2);
        return str_repeat(' ', $pad) . $text;
    }

    /**
     * Texto alineado a izquierda y derecha dentro de $width caracteres.
     */
    public function leftRight(string $left, string $right, int $width): string
    {
        $available = $width - mb_strlen($left);
        if ($available < 1) {
            return mb_substr($left, 0, $width);
        }
        return $left . str_pad($right, $available, ' ', STR_PAD_LEFT);
    }

    /**
     * Línea separadora de $width caracteres.
     */
    public function divider(string $char = '-', int $width = 32): string
    {
        return str_repeat($char, $width);
    }

    // ── Helpers privados ──────────────────────────────────────────────────────

    /**
     * Word wrap respetando multibyte.
     *
     * @return string[]
     */
    private function wrapText(string $text, int $maxWidth): array
    {
        $wrapped = wordwrap($text, $maxWidth, "\n", true);
        return explode("\n", $wrapped);
    }

    /**
     * Convierte UTF-8 a CP850 para ESC/POS.
     * Usa transliteración para caracteres sin equivalente exacto.
     */
    private function toCP850(string $text): string
    {
        $converted = @iconv('UTF-8', 'CP850//TRANSLIT//IGNORE', $text);
        return $converted !== false ? $converted : $text;
    }
}
