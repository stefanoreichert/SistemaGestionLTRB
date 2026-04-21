<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket — Mesa {{ $order->table->number }} — Los Troncos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            width: 80mm;
            margin: 0 auto;
            background: #fff;
            color: #000;
        }

        .ticket {
            width: 80mm;
            padding: 4mm;
        }

        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .header p {
            font-size: 11px;
            color: #444;
        }

        .mesa-info {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            padding: 4px 0;
            border-bottom: 1px dashed #000;
            margin-bottom: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        thead tr {
            border-bottom: 1px solid #000;
        }

        th {
            padding: 2px 0;
            text-align: left;
            font-weight: bold;
        }

        th.right, td.right { text-align: right; }
        th.center, td.center { text-align: center; }

        tbody tr { border-bottom: 1px dotted #ccc; }
        td { padding: 3px 0; }

        .total-section {
            margin-top: 8px;
            border-top: 2px solid #000;
            padding-top: 4px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            font-weight: bold;
        }

        .footer {
            margin-top: 10px;
            text-align: center;
            border-top: 1px dashed #000;
            padding-top: 6px;
            font-size: 10px;
            color: #555;
        }

        /* Solo en pantalla: mostrar botón para imprimir manualmente */
        .print-btn {
            display: block;
            margin: 10px auto;
            padding: 6px 16px;
            background: #1d4ed8;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        @media print {
            .print-btn { display: none; }
            body { width: 80mm; }
        }
    </style>
</head>
<body>

<div class="ticket">

    {{-- Cabecera --}}
    <div class="header">
        <h1>LOS TRONCOS</h1>
        <p>Sistema de Gestión de Restaurante</p>
        <p>{{ $order->closed_at->format('d/m/Y H:i') }}</p>
    </div>

    {{-- Info de mesa --}}
    <div class="mesa-info">
        MESA N° {{ $order->table->number }}
    </div>

    {{-- Ítems --}}
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th class="center">Cant</th>
                <th class="right">Precio</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td class="center">{{ $item->quantity }}</td>
                    <td class="right">${{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="right">${{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Total --}}
    <div class="total-section">
        <div class="total-row">
            <span>TOTAL:</span>
            <span>${{ number_format($order->total, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Pie --}}
    <div class="footer">
        <p>Pedido #{{ $order->id }}</p>
        <p>Atendido por: {{ $order->user?->name ?? 'Sistema' }}</p>
        <p>¡Gracias por su visita!</p>
    </div>

</div>

<button class="print-btn" onclick="window.print()">🖨️ Imprimir</button>

<script>
    // Auto-imprimir al cargar la página
    window.onload = function () {
        window.print();
    };
</script>

</body>
</html>
