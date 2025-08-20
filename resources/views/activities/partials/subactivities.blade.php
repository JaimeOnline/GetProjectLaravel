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
            @if ($subactivity->users->isEmpty())
                Sin usuarios asignados
            @else
                @foreach ($subactivity->users as $user)
                    <span>{{ $user->name }}</span>
                    @if (!$loop->last)
                        ,
                    @endif
                @endforeach
            @endif
        </td>
        <td>{{ $subactivity->fecha_recepcion ? $subactivity->fecha_recepcion->format('d-m-Y') : 'No asignada' }}</td>
        <td>
            <a href="{{ route('activities.create', ['parentId' => $subactivity->id]) }}" class="btn btn-secondary btn-sm">Crear Subactividad</a>
            <a href="{{ route('activities.edit', $subactivity) }}" class="btn btn-warning btn-sm">Editar</a>
            <form action="{{ route('activities.destroy', $subactivity) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta subactividad?')">Eliminar</button>
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