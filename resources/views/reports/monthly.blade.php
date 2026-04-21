<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-3">
                <a href="{{ route('tables.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">← Mesas</a>
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200">
                    📅 Reporte del Mes — {{ now()->locale('es')->isoFormat('MMMM YYYY') }}
                </h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('reports.daily') }}"
                   class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded transition">
                    Ver Día
                </a>
                <button onclick="window.print()"
                        class="text-sm bg-gray-600 hover:bg-gray-700 text-white px-3 py-1.5 rounded transition">
                    🖨️ Imprimir
                </button>
                @if($items->isNotEmpty())
                    <form method="POST" action="{{ route('reports.clearMonthly') }}"
                          onsubmit="return confirm('¿Eliminar TODOS los registros del mes? Esta acción es irreversible.')">
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

    <div class="py-6 px-4 max-w-4xl mx-auto">

        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-2 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($items->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-12 text-center text-gray-400 italic">
                No hay ventas registradas para este mes.
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden mb-6">
                <div class="bg-indigo-700 text-white px-4 py-2 font-semibold text-sm">
                    Ranking de ventas por producto (mayor a menor)
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-left text-gray-600 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-2">#</th>
                            <th class="px-4 py-2">Producto</th>
                            <th class="px-4 py-2 text-center">Cant. Total</th>
                            <th class="px-4 py-2 text-right">Total Recaudado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($items as $index => $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-2 text-gray-400 font-bold">{{ $index + 1 }}</td>
                                <td class="px-4 py-2 font-medium">{{ $item->product->name }}</td>
                                <td class="px-4 py-2 text-center">{{ $item->total_quantity }}</td>
                                <td class="px-4 py-2 text-right font-semibold text-green-700 dark:text-green-400">
                                    ${{ number_format($item->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Total general del mes --}}
            <div class="bg-indigo-700 text-white rounded-xl px-6 py-4 flex justify-between items-center text-lg font-bold shadow">
                <span>TOTAL DEL MES</span>
                <span>${{ number_format($total, 0, ',', '.') }}</span>
            </div>
        @endif
    </div>
</x-app-layout>
