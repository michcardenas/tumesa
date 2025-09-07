<div class="chef-sidebar">
    <ul class="chef-menu">
        <li class="menu-item">
            @if(request()->routeIs('chef.dashboard'))
                <!-- Si estamos en dashboard, abrir modal -->
                <a href="#" class="menu-link" data-bs-toggle="modal" >
                    <i class="fas fa-plus-circle"></i>
                    Mis Cenas
                </a>
            @else
                <!-- Si estamos en otra página, ir al dashboard -->
                <a href="{{ route('chef.dashboard') }}" class="menu-link-real">
                    <i class="fas fa-plus-circle"></i>
                    Mis Cenas
                </a>
            @endif
        </li>
      
        <li class="menu-item">
            <a href="{{ route('chef.ingresos') }}" class="menu-link-real {{ request()->routeIs('chef.ingresos') ? 'active' : '' }}">
                <i class="fas fa-dollar-sign"></i>
                Ingresos
            </a>
        </li>
        <li class="menu-item">
           <a href="{{ route('chef.profile.edit') }}" class="menu-link-real {{ request()->routeIs('chef.profile.edit') ? 'active' : '' }}">
                <i class="fas fa-user-edit"></i>
                Editar Perfil
            </a>
        </li>
   <li class="menu-item">
    <a href="{{ route('resenas.index') }}" class="menu-link">
        <i class="fas fa-star"></i>
        Reseñas
    </a>
</li>
    </ul>
</div>

<style>
/* Clase que copia los estilos de menu-link pero no interfiere con JavaScript */
.menu-link-real {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    color: #6c757d;
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
    cursor: pointer;
}

.menu-link-real:hover {
    background: #f1f5f9;
    color: #2563eb;
}

.menu-link-real.active {
    background: #2563eb;
    color: white;
}

.menu-link-real i {
    margin-right: 0.75rem;
    width: 16px;
    text-align: center;
}
</style>