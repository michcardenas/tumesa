@extends('layouts.app')

@section('title', 'Experiencias')

@section('content')
<style>
/* ========== Estilos para el Filtro de Precio Mejorado ========== */
.price-range-container {
    margin: 15px 0;
}

.price-range-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    font-weight: 600;
    color: #1f2937;
    font-size: 14px;
}

.price-range-header i {
    color: #6b7280;
    margin-right: 4px;
}

.range-slider {
    position: relative;
    width: 100%;
    height: 40px;
    margin: 20px 0;
}

.range-track {
    position: absolute;
    width: 100%;
    height: 5px;
    background: #e5e7eb;
    border-radius: 3px;
    top: 50%;
    transform: translateY(-50%);
}

.range-track-active {
    position: absolute;
    height: 5px;
    background: linear-gradient(90deg, #60a5fa, #2563eb);
    border-radius: 3px;
    top: 50%;
    transform: translateY(-50%);
    transition: all 0.3s ease;
}

.range-input {
    position: absolute;
    width: 100%;
    height: 5px;
    background: transparent;
    pointer-events: none;
    -webkit-appearance: none;
    appearance: none;
    top: 50%;
    transform: translateY(-50%);
}

.range-input::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: white;
    border: 3px solid #2563eb;
    cursor: pointer;
    pointer-events: all;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
}

.range-input::-moz-range-thumb {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: white;
    border: 3px solid #2563eb;
    cursor: pointer;
    pointer-events: all;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
    border: none;
}

.range-values-display {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
    padding: 8px 12px;
    background: #f9fafb;
    border-radius: 6px;
}

.range-value {
    font-weight: 600;
    color: #1f2937;
    font-size: 14px;
}

.range-value.min {
    color: #6b7280;
}

.range-value.max {
    color: #2563eb;
}
</style>
@endsection

@section('content')
<div class="page-wrap">
    {{-- Sidebar filtros --}}
    <aside class="card">
        <h3 style="margin:0 0 16px; font-size:18px; font-weight:800; color: #1f2937;">
            <i class="fas fa-filter" style="color: #2563eb; margin-right: 8px;"></i>
            Filtros
        </h3>

        <form method="GET">
            <div class="field">
                <label class="label" for="q">
                    <i class="fas fa-search" style="color: #6b7280; margin-right: 4px;"></i>
                    Buscar
                </label>
                <input class="input" type="text" id="q" name="q" placeholder="Ciudad, chef, tipo de cocina..."
                       value="{{ $filters['q'] ?? '' }}">
            </div>

            <div class="field">
                <label class="label" for="city">
                    <i class="fas fa-map-marker-alt" style="color: #6b7280; margin-right: 4px;"></i>
                    Ubicación
                </label>
                <select class="select" id="city" name="city">
                    <option value="">Todas las ciudades</option>
                    @foreach($cities as $opt)
                        <option value="{{ $opt }}" {{ ($filters['city'] ?? '') === $opt ? 'selected' : '' }}>
                            {{ $opt }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Rango de precio mejorado --}}
            <div class="field">
                <label class="label">
                    <span style="color: #6b7280; margin-right: 4px;">AR$</span>
                    Precio por persona
                </label>
                
                <div class="price-range-container">
                    <div class="range-slider">
                        <div class="range-track"></div>
                        <div class="range-track-active" id="rangeTrackActive"></div>
                        
                        <input type="range" 
                               class="range-input" 
                               id="price_min" 
                               name="price_min"
                               min="{{ $minPrice }}" 
                               max="{{ $maxPrice }}" 
                               value="{{ $filters['price_min'] ?? $minPrice }}">
                        
                        <input type="range" 
                               class="range-input" 
                               id="price_max" 
                               name="price_max"
                               min="{{ $minPrice }}" 
                               max="{{ $maxPrice }}" 
                               value="{{ $filters['price_max'] ?? $maxPrice }}">
                    </div>
                    
                    <div class="range-values-display">
                        <div class="range-value min">
                            Mín: <span id="lblMin">${{ number_format($filters['price_min'] ?? $minPrice, 0, ',', '.') }}</span>
                        </div>
                        <div class="range-value max">
                            Máx: <span id="lblMax">${{ number_format($filters['price_max'] ?? $maxPrice, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="field">
                <label class="label" for="guests">
                    <i class="fas fa-users" style="color: #6b7280; margin-right: 4px;"></i>
                    Tamaño del grupo
                </label>
                <select class="select" id="guests" name="guests">
                    <option value="">Cualquier tamaño</option>
                    @foreach([2,4,6,8,10,12,15,20] as $g)
                        <option value="{{ $g }}" {{ (int)($filters['guests'] ?? 0) === $g ? 'selected' : '' }}>
                            Mínimo {{ $g }} personas
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field" style="margin-top: 20px;">
                <button class="btn block" type="submit">
                    <i class="fas fa-search"></i>
                    Aplicar Filtros
                </button>
            </div>
        </form>

        @if(($filters['q'] ?? '') || ($filters['city'] ?? '') || ($filters['guests'] ?? '') || ($filters['price_min'] ?? $minPrice) != $minPrice || ($filters['price_max'] ?? $maxPrice) != $maxPrice)
            <div style="margin-top: 12px;">
                <a class="btn secondary block" href="{{ route('experiencias') }}">
                    <i class="fas fa-times"></i>
                    Limpiar Filtros
                </a>
            </div>
        @endif
    </aside>

    {{-- Contenido principal --}}
    <section>
        <div class="title-row" style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:8px;">
            <div>
                <h1 class="h1">Experiencias Gastronómicas</h1>
                <p class="results-count">
                    @if($cenas->total() > 0)
                        Mostrando {{ $cenas->count() }} de {{ $cenas->total() }} experiencias
                    @endif
                </p>
            </div>

            <form method="GET" class="controls-top">
                {{-- Conserva filtros actuales --}}
                <input type="hidden" name="q" value="{{ $filters['q'] ?? '' }}">
                <input type="hidden" name="city" value="{{ $filters['city'] ?? '' }}">
                <input type="hidden" name="price_min" value="{{ $filters['price_min'] ?? $minPrice }}">
                <input type="hidden" name="price_max" value="{{ $filters['price_max'] ?? $maxPrice }}">
                <input type="hidden" name="guests" value="{{ $filters['guests'] ?? '' }}">

                <select class="select" name="sort" onchange="this.form.submit()" style="min-width: 200px;">
                    <option value="date" {{ ($filters['sort'] ?? 'date')==='date' ? 'selected' : '' }}>
                        <i class="fas fa-calendar"></i> Próximos eventos
                    </option>
                    <option value="price_asc" {{ ($filters['sort'] ?? '')==='price_asc' ? 'selected' : '' }}>
                        <i class="fas fa-arrow-up"></i> Precio: menor a mayor
                    </option>
                    <option value="price_desc" {{ ($filters['sort'] ?? '')==='price_desc' ? 'selected' : '' }}>
                        <i class="fas fa-arrow-down"></i> Precio: mayor a menor
                    </option>
                </select>
            </form>
        </div>

        {{-- Listado --}}
        @if($cenas->count())
            <div class="grid-cards">
                @foreach($cenas as $cena)
                    <article class="card card-exp">
                        <div class="cover">
                            @php
                                $cover = $cena->cover_image_url ?: asset('images/placeholder-experience.jpg');
                                $availableSpots = $cena->guests_max - $cena->guests_current;
                            @endphp
                            <img src="{{ $cover }}" alt="{{ $cena->title }}">
                            
                            @if($cena->user && $cena->user->especialidad)
                                <span class="badge">{{ $cena->user->especialidad }}</span>
                            @endif
                            
                            @if($availableSpots <= 3 && $availableSpots > 0)
                                <span class="availability-badge">¡Últimos {{ $availableSpots }} lugares!</span>
                            @elseif($availableSpots == 0)
                                <span class="availability-badge" style="background: #ef4444;">Agotado</span>
                            @endif
                        </div>
                        
                        <div class="body">
                            <h3 class="title">{{ $cena->title }}</h3>
                            
                            @if($cena->user)
                                <div class="chef-info">
                                    @if($cena->user->avatar_url)
                                        <img src="{{ $cena->user->avatar_url }}" alt="{{ $cena->user->name }}" class="chef-avatar">
                                    @else
                                        <div class="chef-avatar" style="background: #e5e7eb; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-user" style="font-size: 10px; color: #6b7280;"></i>
                                        </div>
                                    @endif
                                    <span class="chef-name">Chef {{ $cena->user->name }}</span>
                                    @if($cena->user->rating > 0)
                                        <span style="color: #fbbf24; font-size: 12px;">
                                            <i class="fas fa-star"></i> {{ number_format($cena->user->rating, 1) }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                            
                            <div class="meta">
                                <div class="meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>{{ $cena->location }}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-users"></i>
                                    <span>{{ $cena->guests_max }} personas</span>
                                </div>
                            </div>
                            
                            <div class="meta">
                                <div class="meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>{{ $cena->datetime->format('d/m/Y') }}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ $cena->datetime->format('H:i') }}</span>
                                </div>
                                @php
                                    $daysUntil = now()->diffInDays($cena->datetime, false);
                                @endphp
                                @if($daysUntil >= 0)
                                    <div class="meta-item" style="color: #059669;">
                                        @if($daysUntil == 0)
                                            <span style="font-weight: 600;">¡Hoy!</span>
                                        @elseif($daysUntil == 1)
                                            <span style="font-weight: 600;">Mañana</span>
                                        @else
                                            <span>En {{ $daysUntil }} días</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            
                            <p class="description">
                                {{ \Illuminate\Support\Str::limit(strip_tags($cena->menu), 120) }}
                            </p>
                        </div>
                        
                        <div class="footer">
                            <div>
                                <div class="price">${{ number_format($cena->price, 0, ',', '.') }}</div>
                                <div class="price-per">por persona</div>
                            </div>
                            <a class="btn" href="{{ route('cenas.show', $cena) }}">
                                <i class="fas fa-eye"></i>
                                Ver Detalles
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            @if($cenas->hasPages())
                <div style="margin-top:24px; display: flex; justify-content: center;">
                    {{ $cenas->links() }}
                </div>
            @endif
        @else
            <div class="card no-results">
                <i class="fas fa-search"></i>
                <h3 style="color: #374151; margin: 0 0 8px 0;">No encontramos experiencias</h3>
                <p class="muted">No hay experiencias que coincidan con tus filtros. Prueba ajustar la búsqueda o explorar otras opciones.</p>
                <div style="margin-top: 20px;">
                    <a class="btn" href="{{ route('experiencias') }}">
                        <i class="fas fa-refresh"></i>
                        Ver todas las experiencias
                    </a>
                </div>
            </div>
        @endif
    </section>
</div>

<script>
function syncRanges() {
    const priceMin = document.getElementById('price_min');
    const priceMax = document.getElementById('price_max');
    const rangeTrackActive = document.getElementById('rangeTrackActive');
    const lblMin = document.getElementById('lblMin');
    const lblMax = document.getElementById('lblMax');
    
    if (!priceMin || !priceMax) return;
    
    let minVal = parseInt(priceMin.value);
    let maxVal = parseInt(priceMax.value);
    
    // Evitar cruce de valores
    if (minVal > maxVal - 1000) {
        if (event && event.target === priceMin) {
            priceMax.value = minVal + 1000;
            maxVal = minVal + 1000;
        } else {
            priceMin.value = maxVal - 1000;
            minVal = maxVal - 1000;
        }
    }
    
    // Calcular porcentajes para la barra activa
    const minPercent = ((minVal - priceMin.min) / (priceMin.max - priceMin.min)) * 100;
    const maxPercent = ((maxVal - priceMin.min) / (priceMax.max - priceMin.min)) * 100;
    
    // Actualizar barra activa
    if (rangeTrackActive) {
        rangeTrackActive.style.left = minPercent + '%';
        rangeTrackActive.style.width = (maxPercent - minPercent) + '%';
    }
    
    // Formatear y actualizar labels
    const fmt = function(n) { 
        return n.toLocaleString('es-AR'); 
    };
    
    if (lblMin) lblMin.textContent = '$' + fmt(minVal);
    if (lblMax) lblMax.textContent = '$' + fmt(maxVal);
}

// Inicializar al cargar
document.addEventListener('DOMContentLoaded', function() {
    const priceMin = document.getElementById('price_min');
    const priceMax = document.getElementById('price_max');
    
    if (priceMin && priceMax) {
        priceMin.addEventListener('input', syncRanges);
        priceMax.addEventListener('input', syncRanges);
        syncRanges();
    }
});
</script>
@endsection