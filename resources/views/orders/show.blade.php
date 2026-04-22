<x-app-layout>
    <x-slot name="header">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">

            <div style="display:flex;align-items:center;gap:.875rem;">
                <a href="{{ route('tables.index') }}"
                   style="color:#6b7280;text-decoration:none;font-size:.78rem;padding:.35rem .7rem;
                          border:1px solid #2a2a2a;border-radius:.5rem;transition:all .15s;"
                   onmouseover="this.style.color='#e5e7eb';this.style.borderColor='#555';"
                   onmouseout="this.style.color='#6b7280';this.style.borderColor='#2a2a2a';">
                    &#8592; Mesas
                </a>
                <div>
                    <div style="font-size:1.2rem;font-weight:800;color:#fbbf24;line-height:1.1;">
                        Mesa {{ $table->number }}
                    </div>
                    <div style="font-size:.63rem;color:#6b7280;text-transform:uppercase;letter-spacing:.08em;margin-top:.1rem;">
                        @if($order)
                            {{ $order->items->count() }} item{{ $order->items->count() !== 1 ? 's' : '' }}
                            &bull; Abierto {{ $order->opened_at->format('H:i') }}
                        @else
                            Sin pedido activo
                        @endif
                    </div>
                </div>
            </div>

            @if($order)
            <div style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;">

                {{-- Botón "Entregado" solo si kitchen_status = listo --}}
                @if($order->kitchen_status === 'listo')
                <button id="btn-entregar-header"
                        onclick="marcarEntregado()"
                        style="padding:.45rem .9rem;background:rgba(234,179,8,.18);color:#fef08a;
                               border:1px solid rgba(234,179,8,.5);border-radius:.5rem;
                               font-size:.75rem;font-weight:700;cursor:pointer;transition:all .15s;"
                        onmouseover="this.style.background='rgba(234,179,8,.32)';"
                        onmouseout="this.style.background='rgba(234,179,8,.18)';">
                    ✓ Pedido entregado
                </button>
                @endif

                @if(auth()->user()->isAdmin())
                <button onclick="cerrarMesa()"
                        style="padding:.55rem 1.1rem;background:#059669;color:#fff;border:none;
                               border-radius:.6rem;font-size:.82rem;font-weight:700;cursor:pointer;transition:background .15s;"
                        onmouseover="this.style.background='#047857';"
                        onmouseout="this.style.background='#059669';">
                    Cerrar e Imprimir
                </button>
                @else
                <button onclick="cerrarMesaSinImprimir()"
                        style="padding:.55rem 1.1rem;background:#059669;color:#fff;border:none;
                               border-radius:.6rem;font-size:.82rem;font-weight:700;cursor:pointer;transition:background .15s;"
                        onmouseover="this.style.background='#047857';"
                        onmouseout="this.style.background='#059669';">
                    Cerrar Mesa
                </button>
                @endif

                <form id="form-close" method="POST" action="{{ route('orders.close', $order) }}">@csrf</form>
                <form id="form-cancel" method="POST" action="{{ route('orders.cancel', $order) }}">
                    @csrf @method('DELETE')
                </form>
                <button onclick="borrarPedido()"
                        style="padding:.55rem 1.1rem;background:transparent;color:#f87171;
                               border:1px solid rgba(239,68,68,.35);border-radius:.6rem;
                               font-size:.82rem;font-weight:700;cursor:pointer;transition:all .15s;"
                        onmouseover="this.style.background='rgba(239,68,68,.1)';"
                        onmouseout="this.style.background='transparent';">
                    Cancelar Pedido
                </button>
            </div>
            @endif
        </div>
    </x-slot>

    @if(session('error'))
    <div style="margin:.75rem 1rem 0;padding:.6rem 1rem;background:rgba(220,38,38,.08);
                border:1px solid rgba(220,38,38,.25);border-radius:.5rem;color:#f87171;font-size:.82rem;">
        {{ session('error') }}
    </div>
    @endif

    <style>
        .pos-wrap { display:flex; gap:.75rem; padding:.75rem;
                    height:calc(100vh - 7rem); overflow:hidden; }
        .panel    { display:flex; flex-direction:column; background:#141414;
                    border:1px solid #222; border-radius:.875rem; overflow:hidden; }
        .panel-hdr { padding:.6rem .875rem; background:#181818; border-bottom:1px solid #222;
                     font-size:.7rem; font-weight:700; text-transform:uppercase;
                     letter-spacing:.07em; flex-shrink:0; }
        .p-catalog { width:38%; flex-shrink:0; }
        .search-box { padding:.55rem .75rem; background:#111; border-bottom:1px solid #1e1e1e;
                      flex-shrink:0; }
        .search-inp { width:100%; box-sizing:border-box; background:#1e1e1e; border:1px solid #2a2a2a;
                      color:#e5e7eb; border-radius:.5rem; padding:.5rem .75rem; font-size:.82rem; }
        .search-inp:focus { border-color:#d97706; outline:none; box-shadow:0 0 0 2px rgba(217,119,6,.15); }
        .search-inp::placeholder { color:#4b5563; }
        .filter-row { display:flex; gap:.3rem; margin-top:.4rem; flex-wrap:wrap; }
        .fbtn { padding:.28rem .65rem; border-radius:.375rem; font-size:.72rem;
                font-weight:600; border:1px solid #2a2a2a; cursor:pointer;
                background:#1e1e1e; color:#6b7280; transition:all .15s; }
        .fbtn.active { background:#d97706; color:#fff; border-color:#d97706; }
        .cat-row { display:flex; gap:.3rem; flex-wrap:wrap; margin-top:.35rem; }
        .cbtn { padding:.18rem .5rem; border-radius:.3rem; font-size:.68rem;
                font-weight:600; border:1px solid #2a2a2a; cursor:pointer;
                background:#1e1e1e; color:#6b7280; transition:all .15s; }
        .cbtn.active { background:#d97706; color:#fff; border-color:#d97706; }
        .prod-list { flex:1; overflow-y:auto; }
        .group-lbl { padding:.25rem .875rem; background:#1c1c1c;
                     font-size:.65rem; font-weight:700; color:#4b5563;
                     text-transform:uppercase; letter-spacing:.08em; }
        .prod-row { display:flex; justify-content:space-between; align-items:center;
                    padding:.55rem .875rem; cursor:pointer; border-bottom:1px solid #1a1a1a;
                    transition:background .1s; gap:.5rem; }
        .prod-row:hover { background:#1e1e1e; }
        .prod-row.selected { background:#92400e; }
        .prod-name { color:#e5e7eb; font-weight:500; font-size:.82rem; }
        .prod-cat  { color:#4b5563; font-size:.68rem; margin-left:.25rem; }
        .prod-price { color:#4ade80; font-weight:700; font-size:.82rem; flex-shrink:0; }
        .qty-bar { display:flex; align-items:center; gap:.5rem; padding:.55rem .75rem;
                   background:#111; border-top:1px solid #1e1e1e; flex-shrink:0; }
        .qty-inp { background:#1e1e1e; border:1px solid #2a2a2a; color:#e5e7eb;
                   border-radius:.375rem; padding:.35rem; text-align:center;
                   width:3.5rem; font-size:.82rem; }
        .qty-inp:focus { border-color:#d97706; outline:none; }
        .add-btn { flex:1; background:#d97706; color:#fff; border:none;
                   border-radius:.5rem; padding:.55rem; font-size:.82rem;
                   font-weight:700; cursor:pointer; transition:background .15s; }
        .add-btn:hover { background:#f59e0b; }
        .p-order { flex:1; }
        .items-scroll { flex:1; overflow-y:auto; }
        .tbl-hdr th { padding:.5rem .5rem; color:#4b5563; font-weight:600; font-size:.75rem;
                      border-bottom:1px solid #222; position:sticky; top:0;
                      background:#1a1a1a; z-index:1; }
        .item-row { border-bottom:1px solid #1a1a1a; transition:background .1s; }
        .item-row:hover { background:rgba(217,119,6,.04); }
        .item-row td { padding:.55rem .5rem; font-size:.82rem; }
        .qty-cell { background:#1e1e1e; border:1px solid #2a2a2a; color:#e5e7eb;
                    border-radius:.375rem; padding:.3rem; text-align:center;
                    width:3.5rem; font-size:.82rem; }
        .qty-cell:focus { border-color:#d97706; outline:none; }
        .del-btn { background:none; border:none; color:#374151; font-size:1rem;
                   cursor:pointer; padding:.15rem .3rem; border-radius:.25rem;
                   transition:color .15s; line-height:1; }
        .del-btn:hover { color:#ef4444; }
        .total-bar { display:flex; justify-content:space-between; align-items:center;
                     padding:.75rem 1.25rem; background:#111; border-top:1px solid #222;
                     flex-shrink:0; }
        .empty-state { display:flex; flex-direction:column; align-items:center;
                       justify-content:center; height:100%; gap:.75rem; min-height:12rem; }
    </style>

    <div class="pos-wrap">

        {{-- CATALOGO --}}
        <div class="panel p-catalog">
            <div class="panel-hdr" style="color:#d97706;">Catalogo</div>

            <div class="search-box">
                <input id="busqueda" type="text" placeholder="Buscar producto..."
                       oninput="aplicarFiltros()" class="search-inp" autocomplete="off">
                <div class="filter-row">
                    <button id="btn-tipo-todos"  onclick="seleccionarTipo(null)"     class="fbtn active">Todos</button>
                    <button id="btn-tipo-Comida" onclick="seleccionarTipo('Comida')" class="fbtn">Comida</button>
                    <button id="btn-tipo-Bebida" onclick="seleccionarTipo('Bebida')" class="fbtn">Bebida</button>
                </div>
                <div id="cat-row" class="cat-row" style="display:none;"></div>
            </div>

            <div id="lista-productos" class="prod-list">
                <p style="color:#4b5563;text-align:center;padding:2rem;font-style:italic;font-size:.82rem;">Cargando...</p>
            </div>

            <div class="qty-bar">
                <span style="font-size:.7rem;color:#6b7280;flex-shrink:0;">Cant.</span>
                <input id="cantidad" type="number" value="1" min="1" max="99" class="qty-inp">
                <button onclick="agregarProducto()" class="add-btn">+ Agregar</button>
            </div>
        </div>

        {{-- PEDIDO --}}
        <div class="panel p-order">
            <div class="panel-hdr" style="color:#fbbf24;">
                Pedido - Mesa {{ $table->number }}
            </div>

            <div class="items-scroll">
                <table id="items-table" style="width:100%;border-collapse:collapse;{{ ($order && $order->items->count() > 0) ? '' : 'display:none' }}">
                    <thead>
                        <tr class="tbl-hdr">
                            <th style="text-align:left;padding-left:.875rem;">Producto</th>
                            <th style="text-align:center;width:5rem;">Cant.</th>
                            <th style="text-align:right;width:6rem;">Precio</th>
                            <th style="text-align:right;width:6.5rem;padding-right:.875rem;">Subtotal</th>
                            <th style="width:2.25rem;"></th>
                        </tr>
                    </thead>
                    <tbody id="tabla-items">
                        @if($order)
                        @foreach($order->items as $item)
                        <tr id="row-{{ $item->id }}" class="item-row">
                            <td style="padding-left:.875rem;">
                                <div style="color:#e5e7eb;font-weight:500;font-size:.82rem;">
                                    {{ $item->product->name }}
                                    <span style="color:#4b5563;font-size:.68rem;margin-left:.25rem;">{{ $item->product->category ?? '' }}</span>
                                </div>
                                @if(($item->product->category ?? '') !== 'Bebida')
                                <div style="margin-top:.3rem;display:flex;align-items:center;gap:.3rem;">
                                    <input type="text"
                                           id="note-{{ $item->id }}"
                                           value="{{ $item->notes ?? '' }}"
                                           placeholder="Nota (ej: sin aderezos)…"
                                           maxlength="255"
                                           style="flex:1;background:#1a1a1a;border:1px solid #2a2a2a;color:#9ca3af;
                                                  border-radius:.35rem;padding:.25rem .5rem;font-size:.7rem;
                                                  font-style:italic;min-width:0;"
                                           onfocus="this.style.borderColor='#d97706';this.style.color='#e5e7eb';"
                                           onblur="this.style.borderColor='#2a2a2a';this.style.color='#9ca3af';guardarNota({{ $item->id }}, this.value);">
                                </div>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                <input type="number" value="{{ $item->quantity }}" min="1" max="99"
                                       class="qty-cell"
                                       onchange="actualizarCantidad({{ $item->id }}, this.value, this)">
                            </td>
                            <td style="text-align:right;color:#6b7280;">
                                ${{ number_format($item->unit_price, 0, ',', '.') }}
                            </td>
                            <td style="text-align:right;padding-right:.875rem;color:#4ade80;font-weight:700;"
                                id="subtotal-{{ $item->id }}"
                                data-value="{{ $item->quantity * $item->unit_price }}">
                                ${{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}
                            </td>
                            <td style="text-align:center;">
                                <button onclick="eliminarItem({{ $item->id }})" class="del-btn" title="Eliminar">&times;</button>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
                <div id="empty-state" class="empty-state" style="{{ ($order && $order->items->count() > 0) ? 'display:none' : '' }}">
                    <div style="font-size:3rem;opacity:.08;">🧾</div>
                    <p style="color:#4b5563;font-style:italic;font-size:.82rem;">
                        {{ $order ? 'Pedido vacio. Selecciona un producto.' : 'Selecciona un producto para abrir el pedido.' }}
                    </p>
                </div>
            </div>

            <div class="total-bar">
                <span style="font-size:.7rem;color:#6b7280;text-transform:uppercase;letter-spacing:.1em;font-weight:700;">Total</span>
                <span id="total-display"
                      style="font-size:2.25rem;font-weight:900;color:#fbbf24;line-height:1;font-variant-numeric:tabular-nums;">
                    ${{ number_format($total, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <script>
        const CSRF    = document.querySelector('meta[name="csrf-token"]').content;
        const mesaNum = {{ $table->number }};
        let orderId   = @json($order?->id);

        let productos            = [];
        let productoSeleccionado = null;
        let filtroTipo           = null;
        let filtroCategoria      = null;

        /* ── Escape XSS ──────────────────────────────────── */
        function escHtml(str) {
            return String(str ?? '')
                .replace(/&/g,'&amp;').replace(/</g,'&lt;')
                .replace(/>/g,'&gt;').replace(/"/g,'&quot;')
                .replace(/'/g,'&#39;');
        }

        /* ── Catálogo de productos ───────────────────────── */
        async function cargarProductos() {
            try {
                const r = await fetch('/api/products', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
                });
                productos = await r.json();
                aplicarFiltros();
            } catch {
                document.getElementById('lista-productos').innerHTML =
                    '<p style="color:#f87171;text-align:center;padding:2rem;font-size:.82rem;">Error al cargar productos.</p>';
            }
        }

        function seleccionarTipo(tipo) {
            filtroTipo      = tipo;
            filtroCategoria = null;
            document.querySelectorAll('.fbtn').forEach(b => b.classList.remove('active'));
            document.getElementById(tipo ? 'btn-tipo-' + tipo : 'btn-tipo-todos').classList.add('active');

            const catRow = document.getElementById('cat-row');
            if (tipo) {
                const cats = [...new Set(productos.filter(p => p.type === tipo).map(p => p.category))].sort();
                let html = '<button onclick="seleccionarCategoria(null)" data-cat="" class="cbtn active">Todas</button>';
                cats.forEach(c => {
                    html += '<button onclick="seleccionarCategoria(\'' + c.replace(/'/g, "\\'") + '\')" data-cat="' + c + '" class="cbtn">' + c + '</button>';
                });
                catRow.innerHTML = html;
                catRow.style.display = 'flex';
            } else {
                catRow.innerHTML = '';
                catRow.style.display = 'none';
            }
            aplicarFiltros();
        }

        function seleccionarCategoria(cat) {
            filtroCategoria = cat;
            document.querySelectorAll('.cbtn').forEach(b => b.classList.remove('active'));
            const activo = [...document.querySelectorAll('.cbtn')].find(b => b.dataset.cat === (cat || ''));
            if (activo) activo.classList.add('active');
            aplicarFiltros();
        }

        function aplicarFiltros() {
            const q = document.getElementById('busqueda').value.toLowerCase().trim();
            let lista = productos;
            if (filtroTipo)      lista = lista.filter(p => p.type === filtroTipo);
            if (filtroCategoria) lista = lista.filter(p => p.category === filtroCategoria);
            if (q)               lista = lista.filter(p => p.name.toLowerCase().includes(q));
            renderProductos(lista);
        }

        function renderProductos(lista) {
            const container = document.getElementById('lista-productos');
            if (!lista.length) {
                container.innerHTML = '<p style="color:#4b5563;text-align:center;padding:2rem;font-style:italic;font-size:.82rem;">Sin resultados.</p>';
                return;
            }
            const agrupar = filtroTipo ? 'category' : 'type';
            const grupos  = lista.reduce((acc, p) => { (acc[p[agrupar]] = acc[p[agrupar]] || []).push(p); return acc; }, {});
            let html = '';
            for (const [grupo, prods] of Object.entries(grupos)) {
                if (!filtroCategoria) html += '<div class="group-lbl">' + grupo + '</div>';
                prods.forEach(p => {
                    const n = p.name.replace(/'/g, "\\'");
                    html += '<div class="prod-row" data-id="' + p.id + '" data-name="' + p.name + '" data-price="' + p.price + '"' +
                            ' onclick="seleccionarProducto(' + p.id + ',\'' + n + '\',' + p.price + ')"' +
                            ' ondblclick="agregarDirecto(' + p.id + ',\'' + n + '\',' + p.price + ')">' +
                            '<div style="min-width:0;overflow:hidden;">' +
                            '<span class="prod-name">' + p.name + '</span>' +
                            '<span class="prod-cat">' + p.category + '</span>' +
                            '</div>' +
                            '<span class="prod-price">$' + fmt(p.price) + '</span>' +
                            '</div>';
                });
            }
            container.innerHTML = html;
            const primero = container.querySelector('.prod-row');
            if (primero) seleccionarProducto(+primero.dataset.id, primero.dataset.name, +primero.dataset.price);
        }

        function seleccionarProducto(id, name, price) {
            productoSeleccionado = { id, name, price };
            document.querySelectorAll('.prod-row').forEach(r => r.classList.remove('selected'));
            const fila = document.querySelector('.prod-row[data-id="' + id + '"]');
            if (fila) { fila.classList.add('selected'); fila.scrollIntoView({ block: 'nearest' }); }
        }

        async function agregarProducto() {
            if (!productoSeleccionado) { alert('Selecciona un producto.'); return; }
            const qty = parseInt(document.getElementById('cantidad').value) || 1;
            if (qty < 1 || qty > 99) { alert('Cantidad invalida (1-99).'); return; }
            await enviarItem(productoSeleccionado.id, qty);
        }

        async function agregarDirecto(id, name, price) {
            seleccionarProducto(id, name, price);
            await enviarItem(id, parseInt(document.getElementById('cantidad').value) || 1);
        }

        /* ── Agregar ítem: sin reload si el pedido ya existe ── */
        async function enviarItem(productId, qty) {
            const resp = await apiRequest('/tables/' + mesaNum + '/items', 'POST', { product_id: productId, quantity: qty });
            if (!resp.success) return;

            // Primera vez que se crea el pedido → recargar para obtener
            // los botones de cierre/cancelación en el header.
            if (!orderId) {
                location.reload();
                return;
            }

            const item = resp.item;
            const existingRow = document.getElementById('row-' + item.id);

            if (existingRow) {
                // Producto ya en pedido → actualizar cantidad y subtotal en DOM
                const qtyInput = existingRow.querySelector('.qty-cell');
                if (qtyInput) { qtyInput.value = item.quantity; qtyInput.defaultValue = item.quantity; }
                const subEl = document.getElementById('subtotal-' + item.id);
                if (subEl) { subEl.dataset.value = item.subtotal; subEl.textContent = '$' + fmt(item.subtotal); }
            } else {
                // Producto nuevo en el pedido → insertar fila en DOM
                mostrarTabla();
                appendItemRow(item);
            }

            recalcularTotal();
        }

        /* ── Construir y agregar una fila al tbody ───────── */
        function appendItemRow(item) {
            const tbody = document.getElementById('tabla-items');
            if (!tbody) return;

            const noteHtml = item.category !== 'Bebida'
                ? `<div style="margin-top:.3rem;display:flex;align-items:center;gap:.3rem;">
                       <input type="text"
                              id="note-${item.id}"
                              value="${escHtml(item.notes || '')}"
                              placeholder="Nota (ej: sin aderezos)…"
                              maxlength="255"
                              style="flex:1;background:#1a1a1a;border:1px solid #2a2a2a;color:#9ca3af;
                                     border-radius:.35rem;padding:.25rem .5rem;font-size:.7rem;
                                     font-style:italic;min-width:0;"
                              onfocus="this.style.borderColor='#d97706';this.style.color='#e5e7eb';"
                              onblur="this.style.borderColor='#2a2a2a';this.style.color='#9ca3af';guardarNota(${item.id}, this.value);">
                   </div>`
                : '';

            const tr = document.createElement('tr');
            tr.id        = 'row-' + item.id;
            tr.className = 'item-row';
            tr.innerHTML = `
                <td style="padding-left:.875rem;">
                    <div style="color:#e5e7eb;font-weight:500;font-size:.82rem;">
                        ${escHtml(item.product)}
                        <span style="color:#4b5563;font-size:.68rem;margin-left:.25rem;">${escHtml(item.category)}</span>
                    </div>
                    ${noteHtml}
                </td>
                <td style="text-align:center;">
                    <input type="number" value="${item.quantity}" min="1" max="99" class="qty-cell"
                           onchange="actualizarCantidad(${item.id}, this.value, this)">
                </td>
                <td style="text-align:right;color:#6b7280;">
                    $${fmt(item.unit_price)}
                </td>
                <td style="text-align:right;padding-right:.875rem;color:#4ade80;font-weight:700;"
                    id="subtotal-${item.id}"
                    data-value="${item.subtotal}">
                    $${fmt(item.subtotal)}
                </td>
                <td style="text-align:center;">
                    <button onclick="eliminarItem(${item.id})" class="del-btn" title="Eliminar">&times;</button>
                </td>`;
            tbody.appendChild(tr);
        }

        /* ── Mostrar tabla / ocultar empty-state ─────────── */
        function mostrarTabla() {
            const t = document.getElementById('items-table');
            const e = document.getElementById('empty-state');
            if (t) t.style.display = '';
            if (e) e.style.display = 'none';
        }

        function mostrarVacio() {
            const t = document.getElementById('items-table');
            const e = document.getElementById('empty-state');
            if (t) t.style.display = 'none';
            if (e) { e.style.display = ''; e.style.flexDirection = 'column'; }
        }

        /* ── Actualizar cantidad de ítem ─────────────────── */
        async function actualizarCantidad(itemId, qty, inp) {
            const q = parseInt(qty);
            if (isNaN(q) || q < 1) { inp.value = inp.defaultValue; return; }
            const resp = await apiRequest('/order-items/' + itemId, 'PUT', { quantity: q });
            if (resp.success) {
                const sub = document.getElementById('subtotal-' + itemId);
                sub.dataset.value = resp.subtotal;
                sub.textContent   = '$' + fmt(resp.subtotal);
                inp.defaultValue  = q;
                recalcularTotal();
            } else { inp.value = inp.defaultValue; }
        }

        function recalcularTotal() {
            let t = 0;
            document.querySelectorAll('[id^="subtotal-"]').forEach(el => t += parseFloat(el.dataset.value || 0));
            document.getElementById('total-display').textContent = '$' + fmt(t);
        }

        /* ── Eliminar ítem: sin reload salvo último ítem ─── */
        async function eliminarItem(id) {
            if (!confirm('¿Eliminar este item?')) return;
            const resp = await apiRequest('/order-items/' + id, 'DELETE', {});
            if (!resp.success) return;

            document.getElementById('row-' + id)?.remove();

            const tbody = document.getElementById('tabla-items');
            if (!tbody || tbody.querySelectorAll('tr').length === 0) {
                // Último ítem eliminado → la mesa queda libre; recargar para
                // limpiar el header (botones de cierre) y reflejar el nuevo estado.
                location.reload();
                return;
            }

            recalcularTotal();
        }

        /* ── Acciones del header ─────────────────────────── */
        function cerrarMesa() {
            if (!orderId) { alert('No hay pedido abierto.'); return; }
            if (confirm('¿Cerrar la mesa e imprimir el ticket?')) document.getElementById('form-close').submit();
        }

        function cerrarMesaSinImprimir() {
            if (!orderId) { alert('No hay pedido abierto.'); return; }
            if (confirm('¿Cerrar la mesa?')) document.getElementById('form-close').submit();
        }

        function borrarPedido() {
            if (!orderId) { alert('No hay pedido activo.'); return; }
            if (confirm('¿Borrar el pedido completo? No se descontará stock.')) document.getElementById('form-cancel').submit();
        }

        async function marcarEntregado() {
            if (!orderId) return;
            const btn = document.getElementById('btn-entregar-header');
            if (btn) { btn.disabled = true; btn.textContent = '…'; }
            const resp = await apiRequest('/orders/' + orderId + '/deliver', 'PATCH', {});
            if (resp.success) {
                if (btn) btn.remove();
            } else {
                if (btn) { btn.disabled = false; btn.textContent = '✓ Pedido entregado'; }
            }
        }

        async function guardarNota(itemId, nota) {
            await apiRequest('/order-items/' + itemId + '/note', 'PATCH', { notes: nota });
        }

        /* ── API helper ──────────────────────────────────── */
        async function apiRequest(url, method, data) {
            try {
                const r = await fetch(url, {
                    method,
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify(data)
                });
                const json = await r.json();
                if (!json.success) alert(json.message || 'Error en la operacion.');
                return json;
            } catch { alert('Error de conexion.'); return { success: false }; }
        }

        function fmt(n) { return parseFloat(n).toLocaleString('es-AR'); }

        document.addEventListener('keydown', e => {
            if (e.key === 'Enter' && document.activeElement.id !== 'cantidad') {
                agregarProducto();
                return;
            }

            // Si el foco NO está en ningún input/textarea/select,
            // redirigir la escritura al campo de búsqueda automáticamente.
            const tag = document.activeElement?.tagName?.toLowerCase();
            const enInput = tag === 'input' || tag === 'textarea' || tag === 'select'
                || document.activeElement?.isContentEditable;

            if (!enInput && e.key.length === 1 && !e.ctrlKey && !e.altKey && !e.metaKey) {
                const busq = document.getElementById('busqueda');
                busq.focus();
                busq.value += e.key;           // insertar el carácter ya pulsado
                busq.dispatchEvent(new Event('input')); // disparar el filtro
                e.preventDefault();            // evitar doble escritura
            }
        });

        cargarProductos();
    </script>
</x-app-layout>
