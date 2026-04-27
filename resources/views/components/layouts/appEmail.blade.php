<x-layouts.app.email :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>
    @livewireScripts
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- Preline -->
    <script src="https://unpkg.com/preline/dist/preline.js"></script>

    @stack('scripts')
</x-layouts.app.email>
