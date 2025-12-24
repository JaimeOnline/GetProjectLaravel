@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button class="btn btn-primary" onclick="copiarReporte()">
                <i class="fas fa-copy"></i> Copiar reporte
            </button>

            {{-- Filtro de cliente --}}
            <form method="GET" action="{{ route('activities.hoy') }}" class="form-inline">
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

        <h1>Atenciones {{ \Carbon\Carbon::now()->format('d-m-y') }}</h1>
        <p class="text-muted">
            Total de actividades en este reporte:
            <strong>{{ $activities->count() }}</strong>
        </p>

        @forelse($activities as $activity)
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">
                        {{ $loop->iteration }}. {{ $activity->caso ? $activity->caso . ' ' : '' }}{{ $activity->name }}
                    </h5>
                    <p><strong>Estatus:</strong> {{ $activity->estatus_operacional ?? 'Sin estatus operacional' }}</p>
                    <p><strong>Responsable:</strong>
                        @if ($activity->analistas->count())
                            {{ $activity->analistas->pluck('name')->implode(', ') }}
                        @else
                            No asignado
                        @endif
                    </p>
                    <p><strong>Avance:</strong> {{ $activity->porcentaje_avance ?? 0 }}%</p>
                    <p><strong>Estimación de entrega:</strong>
                        {{ $activity->fecha_estimacion_entrega ? \Carbon\Carbon::parse($activity->fecha_estimacion_entrega)->format('d-m-y') : 'Sin estimación' }}
                    </p>
                </div>
            </div>
        @empty
            <p>No hay actividades atendiendo hoy.</p>
        @endforelse

        <!-- Área invisible para copiar el texto plano -->
        <textarea id="reporte-texto" style="position:absolute; left:-9999px; top:-9999px;">
Atenciones {{ \Carbon\Carbon::now()->format('d-m-y') }}

@forelse($activities as $activity)
{{ $loop->iteration }}. {{ $activity->caso ? $activity->caso . ' ' : '' }}{{ $activity->name }}
Estatus: {{ $activity->estatus_operacional ?? 'Sin estatus operacional' }}
Responsable: @if ($activity->analistas->count())
{{ $activity->analistas->pluck('name')->implode(', ') }}
@else
No asignado
@endif
Avance: {{ $activity->porcentaje_avance ?? 0 }}%
Estimación de entrega: {{ $activity->fecha_estimacion_entrega ? \Carbon\Carbon::parse($activity->fecha_estimacion_entrega)->format('d-m-y') : 'Sin estimación' }}

@empty
No hay actividades atendiendo hoy.
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
