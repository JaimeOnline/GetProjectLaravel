@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button class="btn btn-primary" onclick="copiarReporte()">
                <i class="fas fa-copy"></i> Copiar reporte
            </button>

            {{-- Filtro de cliente --}}
            <form method="GET" action="{{ route('activities.insumos') }}" class="form-inline">
                <label for="cliente_id" class="mr-2 mb-0">Cliente:</label>
                <select name="cliente_id" id="cliente_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    @foreach ($clientes ?? [] as $cliente)
                        <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                            {{ \Illuminate\Support\Str::before($cliente->nombre, ' ') }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <h1>Pendientes por insumos/información:</h1>
        <p class="text-muted">
            Total de actividades en este reporte:
            <strong>{{ $activities->count() }}</strong>
        </p>

        @forelse($activities as $activity)
            <div class="mb-3">
                <strong>{{ $loop->iteration }}.
                    {{ $activity->caso ? $activity->caso . ' ' : '' }}{{ $activity->name }}</strong><br>
                Estatus: {{ $activity->estatus_operacional ?? 'Sin estatus operacional' }}
            </div>
        @empty
            <p>No hay actividades pendientes por insumos o información.</p>
        @endforelse

        <!-- Área invisible para copiar el texto plano -->
        <textarea id="reporte-texto" style="position:absolute; left:-9999px; top:-9999px;">
Pendientes por insumos/información:

@forelse($activities as $activity)
{{ $loop->iteration }}. {{ $activity->caso ? $activity->caso . ' ' : '' }}{{ $activity->name }}
Estatus: {{ $activity->estatus_operacional ?? 'Sin estatus operacional' }}

@empty
No hay actividades pendientes por insumos o información.
@endforelse
        </textarea>
    </div>
    <script>
        function copiarReporte() {
            const textarea = document.getElementById('reporte-texto');
            textarea.select();
            document.execCommand('copy');
            alert('Reporte copiado al portapapeles');
        }
    </script>
@endsection
