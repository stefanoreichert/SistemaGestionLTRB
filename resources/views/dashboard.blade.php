<x-app-layout>
    {{-- Redirige automáticamente al panel de mesas (ver routes/web.php) --}}
    <script>window.location.replace('{{ route("tables.index") }}');</script>
</x-app-layout>
