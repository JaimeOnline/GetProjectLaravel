<aside class="sidebar">
    <div class="contenedor-sidebar">
        <h2>GetProject</h2>
        {{-- <div class="cerrar-menu">
            <img id="cerrar-menu" src="{{ asset('build/img/cerrar.svg') }}" alt="imagen cerrar menu">
        </div> --}}
    </div>
    <nav class="sidebar-nav">
        <a class="{{ request()->routeIs('activities.*') ? 'activo' : '' }}"
            href="{{ route('activities.index') }}">Actividades</a>
        <div class="sidebar-dropdown">
            <a href="#" class="sidebar-dropdown-toggle {{ request()->routeIs('requirements.*') ? 'activo' : '' }}">
                Requerimientos
                <span style="float:right;"><i class="fas fa-chevron-down"></i></span>
            </a>
            <div class="sidebar-dropdown-menu">
                <a class="{{ request()->routeIs('requirements.create') ? 'activo' : '' }}"
                    href="{{ route('requirements.create') }}">Nuevo Requerimiento</a>
                <a class="{{ request()->routeIs('requirements.index') ? 'activo' : '' }}"
                    href="{{ route('requirements.index') }}">Lista de Requerimientos</a>
                <a class="{{ request()->routeIs('requirements.report') ? 'activo' : '' }}"
                    href="{{ route('requirements.report') }}">Reporte de Requerimientos</a>
            </div>
        </div>
        <div class="sidebar-dropdown">
            <a href="#" class="sidebar-dropdown-toggle {{ request()->routeIs('projects.*') ? 'activo' : '' }}">
                Proyectos
                <span style="float:right;"><i class="fas fa-chevron-down"></i></span>
            </a>
            <div class="sidebar-dropdown-menu">
                <a class="{{ request()->routeIs('projects.index') ? 'activo' : '' }}"
                    href="{{ route('projects.index') }}">Ver Proyectos</a>
                <a class="{{ request()->routeIs('projects.create') ? 'activo' : '' }}"
                    href="{{ route('projects.create') }}">Crear Proyecto</a>
            </div>
        </div>
    </nav>
    <div class="cerrar-sesion-mobile">
        <form method="POST" action="{{ url('/logout') }}">
            @csrf
            <button type="submit" class="cerrar-sesion">Cerrar Sesión</button>
        </form>
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggles = document.querySelectorAll('.sidebar-dropdown-toggle');
        toggles.forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const menu = this.nextElementSibling;
                menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
            });
        });
    });
</script>
