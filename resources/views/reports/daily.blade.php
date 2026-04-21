<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-3">
                <a href="{{ route('tables.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">← Mesas</a>
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200">
                    📊 Reporte del Día — {{ now()->format('d/m/Y') }}
                </h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('reports.monthly') }}"
                   class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded transition">
                    Ver Mes
                </a>
                <button onclick="window.print()"
                        class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded transition">
                    🖨️ Imprimir
                </button>
                @if($byTable->isNotEmpty())
                    <form method="POST" action="{{ route('reports.clearDaily') }}"
                          onsubmit="return confirm('¿Eliminar TODOS los registros de hoy? Esta acción es irreversible.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="text-sm bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded transition">
                            🗑️ Limpiar y Reiniciar
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6 px-4 max-w-5xl mx-auto">

        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-2 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($byTable->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-12 text-center text-gray-400 italic">
                No hay ventas registradas para hoy.
            </div>
        @else
            @foreach($byTable as $mesaNum => $items)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow mb-4 overflow-hidden">
                    <div class="bg-gray-700 text-white px-4 py-2 font-semibold text-sm flex justify-between">
                        <span>🪑 Mesa {{ $mesaNum }}</span>
                        <span>
                            Subtotal Mesa:
                            ${{ number_format($items->sum(fn($i) => $i->quantity * $i->unit_price), 0, ',', '.') }}
                        </span>
                    </div>
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-left text-gray-600 dark:text-gray-300">
                            <tr>
                                <th class="px-4 py-2">Producto</th>
                                <th class="px-4 py-2 text-center">Cantidad</th>
                                <th class="px-4 py-2 text-right">Precio Unit.</th>
                                <th class="px-4 py-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($items as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-2">{{ $item->product->name }}</td>
                                    <td class="px-4 py-2 text-center">{{ $item->quantity }}</td>
                                    <td class="px-4 py-2 text-right text-gray-500">
                                        ${{ number_format($item->unit_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-2 text-right font-semibold">
                                        ${{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach

            {{-- Total general del día --}}
            <div class="bg-green-700 text-white rounded-xl px-6 py-4 flex justify-between items-center text-lg font-bold shadow">
                <span>TOTAL DEL DÍA</span>
                <span>${{ number_format($total, 0, ',', '.') }}</span>
            </div>
        @endif
    </div>
</x-app-layout>
