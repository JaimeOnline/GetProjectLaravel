@extends('layouts.app')

@section('content')
    <div class="container">
        <button class="btn btn-primary mb-3" onclick="copiarReporte()">
            <i class="fas fa-copy"></i> Copiar reporte
        </button>
        <h1>Pendientes por insumos/información:</h1>
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
