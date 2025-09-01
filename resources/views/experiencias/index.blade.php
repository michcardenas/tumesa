@extends('layouts.app')

@section('title', 'Experiencias')

@section('content')
<style>
/* Layout */
.page-wrap { display:grid; grid-template-columns: 320px 1fr; gap:24px; padding: 16px; }
@media (max-width: 1024px){ .page-wrap { grid-template-columns: 1fr; } }

.card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:16px; }
.h1 { font-size:28px; font-weight:800; margin:8px 0 16px; }
.muted { color:#6b7280; }
.field { margin-bottom:14px; }
.label { display:block; font-weight:600; margin-bottom:6px; }
.input, .select, .textarea {
  width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px; font-size:14px;
}
.btn {
  display:inline-flex; align-items:center; justify-content:center; gap:8px;
  padding:10px 14px; border-radius:10px; border:1px solid transparent;
  background:#0C3558; color:#fff; font-weight:700; cursor:pointer; text-decoration:none;
}
.btn.secondary { background:#fff; color:#0C3558; border-color:#0C3558; }
.btn.block { width:100%; }

.grid-cards { display:grid; grid-template-columns: repeat(3, 1fr); gap:18px; }
@media (max-width: 1200px){ .grid-cards { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px){ .grid-cards { grid-template-columns: 1fr; } }

.card-exp { overflow:hidden; display:flex; flex-direction:column; }
.cover { position:relative; width:100%; height:180px; background:#f3f4f6; border-radius:10px; overflow:hidden; }
.cover img { width:100%; height:100%; object-fit:cover; display:block; }
.badge { position:absolute; left:10px; top:10px; background:#111827; color:#fff; font-size:12px; padding:4px 8px; border-radius:999px; opacity:.85; }
.body { padding-top:12px; }
.title-row { display:flex; align-items:center; justify-content:space-between; gap:8px; }
.title { font-size:18px; font-weight:800; margin:0; }
.meta { display:flex; gap:8px; align-items:center; color:#6b7280; font-size:13px; margin:8px 0; }
.footer { display:flex; align-items:center; justify-content:space-between; margin-top:12px; }
.price { font-weight:900; }
.controls-top { display:flex; gap:12px; align-items:center; justify-content:flex-end; margin-bottom:8px; }
.range-wrap { padding:8px 0 0; }
.range-values { display:flex; justify-content:space-between; font-size:13px; color:#374151; margin-top:4px; }
.range-double { position:relative; height:36px; }
.range-double input[type=range]{
  position:absolute; left:0; right:0; top:0; bottom:0; width:100%; pointer-events:none; -webkit-appearance:none; background:transparent;
}
.range-double input[type=range]::-webkit-slider-thumb { pointer-events:auto; -webkit-appearance:none; height:16px; width:16px; border-radius:50%; background:#0C3558; border:none; }
.range-double input[type=range]::-moz-range-thumb { pointer-events:auto; height:16px; width:16px; border-radius:50%; background:#0C3558; border:none; }
.range-double input[type=range]::-webkit-slider-runnable-track { height:6px; background:#e5e7eb; border-radius:999px; }
.range-double input[type=range]::-moz-range-track { height:6px; background:#e5e7eb; border-radius:999px; }
</style>

<div class="page-wrap">
    {{-- Sidebar filtros --}}
    <aside class="card">
        <h3 style="margin:0 0 12px; font-size:18px; font-weight:800;">Filtros</h3>

        <form method="GET">
            <div class="field">
                <label class="label" for="q">Buscar</label>
                <input class="input" type="text" id="q" name="q" placeholder="Ciudad, chef, cocina..."
                       value="{{ $filters['q'] ?? '' }}">
            </div>

            <div class="field">
                <label class="label" for="city">Ubicación</label>
                <select class="select" id="city" name="city">
                    <option value="">Seleccionar ciudad</option>
                    @foreach($cities as $opt)
                        <option value="{{ $opt }}" {{ ($filters['city'] ?? '') === $opt ? 'selected' : '' }}>
                            {{ $opt }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Rango de precio (doble slider) --}}
            <div class="field">
                <label class="label">Precio por persona</label>
                <div class="range-wrap">
                    <div class="range-double">
                        <input type="range" id="price_min" name="price_min"
                               min="{{ $minPrice }}" max="{{ $maxPrice }}"
                               value="{{ $filters['price_min'] ?? $minPrice }}"
                               oninput="syncRanges()" />
                        <input type="range" id="price_max" name="price_max"
                               min="{{ $minPrice }}" max="{{ $maxPrice }}"
                               value="{{ $filters['price_max'] ?? $maxPrice }}"
                               oninput="syncRanges()" />
                    </div>
                    <div class="range-values">
                        <span id="lblMin">${{ number_format($filters['price_min'] ?? $minPrice, 0, ',', '.') }}</span>
                        <span id="lblMax">${{ number_format($filters['price_max'] ?? $maxPrice, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="field">
                <label class="label" for="guests">Tamaño del grupo</label>
                <select class="select" id="guests" name="guests">
                    <option value="">Número de personas</option>
                    @foreach([2,4,6,8,10,12] as $g)
                        <option value="{{ $g }}" {{ (int)($filters['guests'] ?? 0) === $g ? 'selected' : '' }}>
                            {{ $g }} {{ $g===2 ? 'personas' : 'personas' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <button class="btn block" type="submit">Aplicar Filtros</button>
            </div>
        </form>

        @if(($filters['q'] ?? '') || ($filters['city'] ?? '') || ($filters['guests'] ?? '') || ($filters['price_min'] ?? $minPrice) != $minPrice || ($filters['price_max'] ?? $maxPrice) != $maxPrice)
            <a class="btn secondary block" href="{{ route('experiencias') }}">Limpiar</a>
        @endif
    </aside>

    {{-- Contenido principal --}}
    <section>
        <div class="title-row" style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:8px;">
            <h1 class="h1">Experiencias Gastronómicas</h1>

            <form method="GET" class="controls-top">
                {{-- Conserva filtros actuales al cambiar orden --}}
                <input type="hidden" name="q" value="{{ $filters['q'] ?? '' }}">
                <input type="hidden" name="city" value="{{ $filters['city'] ?? '' }}">
                <input type="hidden" name="price_min" value="{{ $filters['price_min'] ?? $minPrice }}">
                <input type="hidden" name="price_max" value="{{ $filters['price_max'] ?? $maxPrice }}">
                <input type="hidden" name="guests" value="{{ $filters['guests'] ?? '' }}">

                <select class="select" name="sort" onchange="this.form.submit()">
                    <option value="date" {{ ($filters['sort'] ?? 'date')==='date' ? 'selected' : '' }}>Ordenar por fecha</option>
                    <option value="price_asc" {{ ($filters['sort'] ?? '')==='price_asc' ? 'selected' : '' }}>Precio: menor a mayor</option>
                    <option value="price_desc" {{ ($filters['sort'] ?? '')==='price_desc' ? 'selected' : '' }}>Precio: mayor a menor</option>
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
                            @endphp
                            <img src="{{ $cover }}" alt="{{ $cena->title }}">
                            {{-- Etiqueta ejemplo: podrías poner una "cocina" si luego la agregas al modelo --}}
                            <span class="badge">{{ \Illuminate\Support\Str::of($cena->title)->explode(' ')->first() }}</span>
                        </div>
                        <div class="body">
                            <div class="title-row">
                                <h3 class="title">{{ $cena->title }}</h3>
                                {{-- rating de ejemplo (si más adelante agregas reviews) --}}
                                {{-- <div class="muted">⭐ 4.9 (127)</div> --}}
                            </div>
                            <div class="meta">
                                <span>👨‍🍳 {{ $cena->chef?->name ?? 'Chef' }}</span>
                            </div>
                            <div class="meta">
                                <span>📍 {{ $cena->location }}</span>
                            </div>
                            <p class="muted" style="margin:8px 0 0;">
                                {{ \Illuminate\Support\Str::limit(strip_tags($cena->menu), 140) }}
                            </p>
                            <div class="meta" style="margin-top:10px;">
                                <span>👥 {{ $cena->guests_max }} personas</span>
                                <span>•</span>
                                <span>🕒 {{ $cena->datetime->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                        <div class="footer">
                            <div class="price">${{ number_format($cena->price, 0, ',', '.') }}/persona</div>
                            <a class="btn" href="#{{-- route('experiencias.show', $cena) --}}">
                                Ver Detalles
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div style="margin-top:16px;">
                {{ $cenas->links() }}
            </div>
        @else
            <div class="card">
                <p class="muted">No encontramos experiencias con esos filtros. Prueba ajustar la búsqueda.</p>
            </div>
        @endif
    </section>
</div>

<script>
function syncRanges(){
    var min = document.getElementById('price_min');
    var max = document.getElementById('price_max');
    if (!min || !max) return;

    var minV = parseInt(min.value);
    var maxV = parseInt(max.value);
    var realMin = parseInt(min.min), realMax = parseInt(min.max);

    // Evitar cruce
    if (minV > maxV) {
        // Si movieron el min por encima, empuja el max
        max.value = minV;
        maxV = minV;
    }

    // Etiquetas
    var fmt = function(n){ return n.toLocaleString('es-AR'); };
    var lblMin = document.getElementById('lblMin');
    var lblMax = document.getElementById('lblMax');
    if (lblMin) lblMin.textContent = '$' + fmt(minV);
    if (lblMax) lblMax.textContent = '$' + fmt(maxV);
}
document.addEventListener('DOMContentLoaded', syncRanges);
</script>
@endsection
