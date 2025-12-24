@extends('layouts.app')

@section('content')
    <div class="container">
        <button class="btn btn-primary mb-3" onclick="copiarReporte()">
            <i class="fas fa-copy"></i> Copiar reporte
        </button>

        <h1>Atenciones {{ \Carbon\Carbon::now()->format('d-m-y') }}</h1>

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
