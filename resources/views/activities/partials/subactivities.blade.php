{{-- Partial para mostrar subactividades de forma recursiva --}}
@foreach ($subactivities as $subactivity)
    <tr class="subactivity-row level-{{ $level }}" data-parent-id="{{ $parentId }}">
        <td>{{ $subactivity->caso }}</td>
        <td>
            <span style="margin-left: {{ ($level - 1) * 20 }}px;">
                @if ($subactivity->subactivities->count() > 0)
                    <span class="toggle-subactivities" style="cursor: pointer; margin-right: 5px;" data-subactivity-id="{{ $subactivity->id }}">
                        <i class="fas fa-chevron-right" id="icon-sub-{{ $subactivity->id }}"></i>
                    </span>
                @endif
                <strong>{{ $subactivity->name }}</strong>
                @if ($subactivity->subactivities->count() > 0)
                    <small class="text-muted">({{ $subactivity->subactivities->count() }} subactividades)</small>
                @endif
            </span>
        </td>
        <td>{{ $subactivity->description }}</td>
        <td>{{ $subactivity->status_label }}</td>
        <td>
            @if ($subactivity->analistas->isEmpty())
                Sin analistas asignados
            @else
                @foreach ($subactivity->analistas as $analista)
                    <span>{{ $analista->name }}</span>
                    @if (!$loop->last)
                        ,
                    @endif
                @endforeach
            @endif
        </td>
        <td>
            @if ($subactivity->comments->count() > 0)
                <a href="{{ route('activities.comments', $subactivity) }}" class="text-decoration-none">
                    <span class="badge badge-secondary">{{ $subactivity->comments->count() }} comentario(s)</span>
                </a>
                <div class="mt-1">
                    <small class="text-muted">
                        Último: {{ $subactivity->comments->last()->created_at->format('d/m/Y H:i') }}
                    </small>
                </div>
            @else
                <span class="text-muted">Sin comentarios</span>
            @endif
        </td>
        <td>
            @if ($subactivity->emails->count() > 0)
                <a href="{{ route('activities.emails', $subactivity) }}" class="text-decoration-none">
                    <span class="badge badge-success">{{ $subactivity->emails->count() }} correo(s)</span>
                </a>
                <div class="mt-1">
                    <small class="text-muted">
                        @php
                            $lastEmail = $subactivity->emails->sortByDesc('created_at')->first();
                            $sentCount = $subactivity->emails->where('type', 'sent')->count();
                            $receivedCount = $subactivity->emails->where('type', 'received')->count();
                        @endphp
                        <i class="fas fa-paper-plane text-primary"></i> {{ $sentCount }} 
                        <i class="fas fa-inbox text-success ml-1"></i> {{ $receivedCount }}
                        @if($lastEmail)
                            <br>Último: {{ $lastEmail->created_at->format('d/m/Y H:i') }}
                        @endif
                    </small>
                </div>
            @else
                <span class="text-muted">Sin correos</span>
            @endif
        </td>
        <td>{{ $subactivity->fecha_recepcion ? $subactivity->fecha_recepcion->format('d-m-Y') : 'No asignada' }}</td>
        <td>
            <div class="btn-group-vertical btn-group-sm" role="group">
                <a href="{{ route('activities.edit', $subactivity) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="{{ route('activities.emails', $subactivity) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-envelope"></i> Correos
                </a>
                <a href="{{ route('activities.create', ['parentId' => $subactivity->id]) }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-plus"></i> Subactividad
                </a>
            </div>
            <form action="{{ route('activities.destroy', $subactivity) }}" method="POST" style="display:inline;" class="mt-1">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta subactividad?')">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </form>
        </td>
    </tr>
    
    {{-- Mostrar subactividades anidadas recursivamente --}}
    @if ($subactivity->subactivities->count() > 0)
        @include('activities.partials.subactivities', [
            'subactivities' => $subactivity->subactivities, 
            'parentId' => $subactivity->id, 
            'level' => $level + 1
        ])
    @endif
@endforeach