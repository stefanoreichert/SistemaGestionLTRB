<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }} — Mesa {{ $order->table->number }} — Los Troncos</title>
    <style>
        /* ── Reset ─────────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        /* ── Pantalla: fondo gris, ticket centrado con sombra ─────── */
        body {
            background: #e5e7eb;
            font-family: 'Courier New', Courier, monospace;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 24px 12px 48px;
            min-height: 100vh;
            color: #111;
        }

        /* ── Barra de acciones (solo en pantalla) ────────────────── */
        .action-bar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 20px;
            width: 100%;
            max-width: 340px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: opacity .15s;
        }
        .btn:hover { opacity: .85; }

        .btn-print   { background: #1d4ed8; color: #fff; }
        .btn-raw     { background: #0f766e; color: #fff; }
        .btn-escpos  { background: #7c3aed; color: #fff; }
        .btn-back    { background: #6b7280; color: #fff; }

        /* ── Papel del ticket ────────────────────────────────────── */
        .receipt-wrap {
            background: #fff;
            width: 300px;           /* aprox 58mm en pantalla */
            padding: 14px 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,.18);
            border-radius: 3px;
            position: relative;
        }

        /* Borde dentado superior e inferior (efecto papel térmico) */
        .receipt-wrap::before,
        .receipt-wrap::after {
            content: '';
            display: block;
            height: 8px;
            background: radial-gradient(circle, #e5e7eb 3px, transparent 4px) repeat-x;
            background-size: 12px 8px;
            margin: 0 -12px;
        }
        .receipt-wrap::before { margin-bottom: 6px; }
        .receipt-wrap::after  { margin-top: 6px; }

        /* ── Tipografía del ticket ───────────────────────────────── */
        .receipt {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11.5px;
            line-height: 1.45;
            white-space: pre;
            overflow: hidden;
            color: #111;
        }

        /* ── Secciones ───────────────────────────────────────────── */
        .t-header {
            text-align: center;
            font-weight: 700;
            font-size: 13px;
            letter-spacing: .5px;
            line-height: 1.6;
        }

        .t-restaurant-name {
            font-size: 14.5px;
            font-weight: 900;
            letter-spacing: 1px;
        }

        .t-sub {
            font-size: 10.5px;
            color: #555;
        }

        .t-divider-dbl  { color: #333; font-size: 11px; letter-spacing: .5px; }
        .t-divider-sng  { color: #888; font-size: 11px; }

        .t-info-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            line-height: 1.5;
        }

        .t-info-label { color: #444; }
        .t-info-value { font-weight: 600; text-align: right; }

        .t-col-header {
            display: flex;
            font-size: 10px;
            font-weight: 700;
            color: #333;
            border-top: 1px solid #555;
            border-bottom: 1px solid #555;
            padding: 2px 0;
            margin: 2px 0;
        }

        .col-name    { flex: 1; }
        .col-qty     { width: 28px; text-align: right; }
        .col-price   { width: 62px; text-align: right; }

        /* ── Ítems ───────────────────────────────────────────────── */
        .t-items { margin: 2px 0; }

        .t-item {
            border-bottom: 1px dotted #ccc;
            padding: 3px 0;
        }

        .t-item-main {
            display: flex;
            align-items: baseline;
            gap: 2px;
        }

        .item-name {
            flex: 1;
            font-size: 11.5px;
            font-weight: 600;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .item-qty {
            width: 28px;
            text-align: right;
            font-size: 11px;
            color: #555;
        }

        .item-subtotal {
            width: 62px;
            text-align: right;
            font-size: 11.5px;
            font-weight: 700;
        }

        .item-unitprice {
            font-size: 10px;
            color: #666;
            padding-left: 4px;
            line-height: 1.3;
        }

        /* Notas del ítem — destacadas visualmente */
        .item-notes {
            margin: 2px 0 1px 8px;
            font-size: 10px;
            color: #c2410c;
            font-style: italic;
            line-height: 1.4;
            background: #fff7ed;
            border-left: 2px solid #fb923c;
            padding: 1px 4px;
        }

        .item-notes::before {
            content: '>> ';
            font-weight: 700;
        }

        /* ── Totales ─────────────────────────────────────────────── */
        .t-totals {
            margin-top: 4px;
            border-top: 2px solid #333;
            padding-top: 4px;
        }

        .t-total-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            line-height: 1.6;
            color: #444;
        }

        .t-total-final {
            display: flex;
            justify-content: space-between;
            font-size: 15px;
            font-weight: 900;
            margin-top: 2px;
            padding-top: 3px;
            border-top: 1px dashed #555;
            color: #111;
        }

        /* ── Pie ─────────────────────────────────────────────────── */
        .t-footer {
            margin-top: 10px;
            text-align: center;
            border-top: 1px dashed #555;
            padding-top: 6px;
            font-size: 10.5px;
            color: #555;
            line-height: 1.7;
        }

        .t-footer strong {
            display: block;
            font-size: 12px;
            font-weight: 800;
            color: #222;
            letter-spacing: .5px;
            margin-bottom: 2px;
        }

        /* ── Separadores visuales ────────────────────────────────── */
        .mt-1 { margin-top: 4px; }
        .mb-1 { margin-bottom: 4px; }
        .hr-dbl { border: none; border-top: 2px solid #333; margin: 5px 0; }
        .hr-sng { border: none; border-top: 1px dashed #aaa; margin: 4px 0; }

        /* ── Modo impresión: 58mm real, sin botones ──────────────── */
        @media print {
            @page {
                size: 58mm auto;
                margin: 0mm 2mm;
            }

            body {
                background: #fff;
                display: block;
                padding: 0;
                margin: 0;
                min-height: 0 !important;
                height: auto !important;
            }

            .action-bar { display: none !important; }

            .receipt-wrap {
                width: 54mm;
                padding: 2mm 1mm;
                box-shadow: none;
                border-radius: 0;
            }

            .receipt-wrap::before,
            .receipt-wrap::after { display: none; }

            .t-restaurant-name { font-size: 12pt; }
            .t-sub             { font-size: 8pt; }
            .t-info-row        { font-size: 8pt; }
            .t-col-header      { font-size: 7.5pt; }
            .item-name         { font-size: 8.5pt; }
            .item-qty          { font-size: 8pt; }
            .item-subtotal     { font-size: 8.5pt; }
            .item-unitprice    { font-size: 7.5pt; }
            .item-notes        { font-size: 7.5pt; }
            .t-total-row       { font-size: 8pt; }
            .t-total-final     { font-size: 11pt; }
            .t-footer          { font-size: 7.5pt; }
        }
    </style>
</head>
<body>

{{-- ── Barra de acciones (solo pantalla) ──────────────────────────────── --}}
<div class="action-bar" id="action-bar">
    <button class="btn btn-print" onclick="window.print()">
        🖨️ Imprimir
    </button>
    <a class="btn btn-raw" href="{{ route('tickets.raw', $order) }}" target="_blank">
        📄 Texto plano
    </a>
    <a class="btn btn-escpos" href="{{ route('tickets.escpos', $order) }}">
        ⬇ ESC/POS
    </a>
    <a class="btn btn-back" href="{{ route('tables.index') }}">
        ← Mesas
    </a>
</div>

{{-- ── Ticket ───────────────────────────────────────────────────────────── --}}
<div class="receipt-wrap">
<div class="receipt">

    {{-- Encabezado --}}
    <div class="t-header mb-1">
        <div class="t-restaurant-name">{{ config('restaurant.name', 'LOS TRONCOS RESTO BAR') }}</div>
        @if(config('restaurant.address'))
            <div class="t-sub">{{ config('restaurant.address') }}</div>
        @endif
        @if(config('restaurant.contact'))
            <div class="t-sub">{{ config('restaurant.contact') }}</div>
        @endif
    </div>

    <hr class="hr-dbl">

    {{-- Datos del pedido --}}
    @php $closedAt = $order->closed_at ?? now(); @endphp

    <div class="t-info-row">
        <span class="t-info-label">Ticket #</span>
        <span class="t-info-value">{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
    </div>
    <div class="t-info-row">
        <span class="t-info-label">Mesa</span>
        <span class="t-info-value">{{ $order->table->number }}</span>
    </div>
    <div class="t-info-row">
        <span class="t-info-label">Mozo</span>
        <span class="t-info-value">{{ Str::limit($order->user?->name ?? 'Sistema', 18) }}</span>
    </div>
    <div class="t-info-row">
        <span class="t-info-label">Fecha</span>
        <span class="t-info-value">{{ $closedAt->format('d/m/Y') }}</span>
    </div>
    <div class="t-info-row">
        <span class="t-info-label">Hora</span>
        <span class="t-info-value">{{ $closedAt->format('H:i') }}</span>
    </div>

    <hr class="hr-sng mt-1">

    {{-- Cabecera de columnas --}}
    <div class="t-col-header">
        <span class="col-name">PRODUCTO</span>
        <span class="col-qty">CANT</span>
        <span class="col-price">IMPORTE</span>
    </div>

    {{-- Ítems --}}
    <div class="t-items">
        @foreach($order->items as $item)
        <div class="t-item">
            <div class="t-item-main">
                <span class="item-name" title="{{ $item->product->name }}">
                    {{ Str::limit($item->product->name, 20) }}
                </span>
                <span class="item-qty">{{ $item->quantity }}</span>
                <span class="item-subtotal">${{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</span>
            </div>
            <div class="item-unitprice">
                c/u: ${{ number_format($item->unit_price, 0, ',', '.') }}
            </div>
            {{-- Notas del ítem — MUY IMPORTANTE: siempre mostrar si existen --}}
            @if(!empty($item->notes))
            <div class="item-notes">{{ $item->notes }}</div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Totales --}}
    @php
        $subtotal = $order->items->sum(fn($i) => $i->quantity * $i->unit_price);
        $total    = $order->total ?? $subtotal;
        $discount = $subtotal - $total;
    @endphp

    <div class="t-totals">
        <div class="t-total-row">
            <span>Subtotal</span>
            <span>${{ number_format($subtotal, 0, ',', '.') }}</span>
        </div>

        @if($discount > 0.01)
        <div class="t-total-row">
            <span>Descuento</span>
            <span>-${{ number_format($discount, 0, ',', '.') }}</span>
        </div>
        @endif

        <div class="t-total-final">
            <span>TOTAL</span>
            <span>${{ number_format($total, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Pie --}}
    <div class="t-footer">
        <strong>GRACIAS POR SU VISITA</strong>
        @if(config('restaurant.message'))
            <span>{{ config('restaurant.message') }}</span><br>
        @endif
        <span style="font-size:9.5px;color:#999;">Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
    </div>

</div>{{-- .receipt --}}
</div>{{-- .receipt-wrap --}}

<script>
    // Auto-imprimir al cargar (solo una vez, evita bucle con beforeprint)
    if (!sessionStorage.getItem('printed_{{ $order->id }}')) {
        sessionStorage.setItem('printed_{{ $order->id }}', '1');
        window.addEventListener('load', () => window.print());
    }
</script>

</body>
</html>

