@extends('layouts.app_chef')

@section('title', 'Editar cena')

@section('content')
<div class="container" style="max-width: 1100px; padding: 24px 16px;">
    @if(session('error'))
        <div style="background:#fdecea;color:#611a15;padding:12px 16px;border-radius:8px;margin-bottom:16px;">
            {{ session('error') }}
        </div>
    @endif

    <h1 style="margin:0 0 8px; font-size:28px; font-weight:700;">Editar cena</h1>
    <p style="margin:0 0 24px; color:#555;">
        Chef: <strong>{{ $user->name }}</strong> &mdash; Cena: <strong>#{{ $cena->id }}</strong>
    </p>

    {{-- Mensajes de validación --}}
    @if ($errors->any())
        <div style="background:#fff7ed;color:#7c2d12;padding:12px 16px;border-radius:8px;margin-bottom:16px;">
            <strong>Revisa los campos:</strong>
            <ul style="margin:8px 0 0 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('chef.dinners.update', $cena->id) }}" method="POST" enctype="multipart/form-data" id="form-edit-cena">
        @csrf
        @method('PUT')

        <style>
            .grid-2 { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; }
            .field { margin-bottom: 16px; }
            .field label { display:block; font-weight:600; margin-bottom:6px; }
            .field input[type="text"],
            .field input[type="number"],
            .field input[type="datetime-local"],
            .field textarea,
            .field select {
                width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px;
            }
            .muted { color:#6b7280; font-size:12px; }
            .card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; }
            .preview-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 12px; }
            .thumb { border:1px solid #e5e7eb; border-radius:8px; overflow:hidden; background:#fafafa; }
            .thumb img { width:100%; height:120px; object-fit:cover; display:block; }
            .row-actions { display:flex; gap:12px; flex-wrap:wrap; }
            .btn {
                display:inline-block; padding:10px 14px; border-radius:8px; border:1px solid transparent;
                background:#0C3558; color:#fff; font-weight:600; text-decoration:none; cursor:pointer;
            }
            .btn.secondary { background:#fff; color:#0C3558; border-color:#0C3558; }
            .btn.danger { background:#fee2e2; color:#991b1b; border-color:#fecaca; }

            /* --- Mapa --- */
            #map { height: 280px; border-radius: 12px; border:1px solid #e5e7eb; }
            .map-actions { display:flex; gap:8px; margin:8px 0 12px; }
            .btn.small { padding:8px 10px; font-size:13px; }
            #quill-editor-edit .ql-editor {
    min-height: 150px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    font-size: 14px;
}

#quill-editor-edit .ql-toolbar {
    border-top: 1px solid #ccc;
    border-left: 1px solid #ccc;
    border-right: 1px solid #ccc;
    border-bottom: none;
    border-radius: 8px 8px 0 0;
    background: #f8f9fa;
}

#quill-editor-edit .ql-container {
    border-bottom: 1px solid #ccc;
    border-left: 1px solid #ccc;
    border-right: 1px solid #ccc;
    border-top: none;
    border-radius: 0 0 8px 8px;
}

/* Ajustar si tienes estilos específicos del campo */
.field {
    margin-bottom: 1rem;
}

.field label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #374151;
}

.form-text {
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: #6b7280;
}

/* Responsive */
@media (max-width: 768px) {
    #quill-editor-edit {
        height: 150px !important;
    }
    
    #quill-editor-edit .ql-toolbar {
        font-size: 14px;
    }
}

/* Para que se vea bien en tu formulario existente */
#quill-editor-edit {
    margin-bottom: 0.5rem;
}
        </style>

        <div class="grid-2">
            {{-- Columna izquierda: datos principales --}}
            <div class="card">
                <div class="field">
                    <label for="title">Título</label>
                    <input type="text" id="title" name="title" maxlength="255"
                           value="{{ old('title', $cena->title) }}" required>
                </div>

                <div class="field">
                    <label for="datetime">Fecha y hora</label>
                    <input
                        type="datetime-local"
                        id="datetime"
                        name="datetime"
                        value="{{ old('datetime', $cena->datetime ? $cena->datetime->format('Y-m-d\TH:i') : '') }}"
                        required
                    >
                    <div class="muted">Debe ser futura (<code>after:now</code> en el servidor).</div>
                </div>

                <div class="field" style="display:grid; grid-template-columns: repeat(3, 1fr); gap:12px;">
                    <div>
                        <label for="guests">Cupos (máx.)</label>
                        <input type="number" id="guests" name="guests" min="1" max="50"
                               value="{{ old('guests', $cena->guests_max) }}" required>
                        <div class="muted">Ocupados: {{ $cena->guests_current }} &middot; Disponibles: {{ $cena->available_spots }}</div>
                    </div>
                    <div>
                        <label for="price">Precio</label>
                        <input type="number" id="price" name="price" step="0.01" min="0"
                               value="{{ old('price', $cena->price) }}" required>
                        <div class="muted">Actual: {{ $cena->formatted_price }}</div>
                    </div>
                  
                </div>

           
            <div class="field">
                <label for="menu">Menú / descripción</label>
                
                <!-- Editor Quill -->
                <div id="quill-editor-edit" style="height: 200px; background: white;"></div>
                
                <!-- Campo oculto para guardar el contenido -->
                <input type="hidden" name="menu" id="menu-content-edit" value="{{ old('menu', $cena->menu) }}" required>
                
                <!-- Texto de ayuda -->
                <div class="form-text">
                    Puedes usar formato: <strong>negritas</strong>, <em>cursiva</em>, listas, títulos, etc.
                </div>
            </div>
            

                <div class="row-actions" style="margin-top: 8px;">
                    <a href="{{ route('chef.dashboard') }}" class="btn secondary">Volver al panel</a>
                    <button type="submit" class="btn">Guardar cambios</button>
                </div>
            </div>

            {{-- Columna derecha: ubicación e imágenes --}}
            <div class="card">
                <h3 style="margin:0 0 12px; font-size:18px; font-weight:700;">Ubicación</h3>

                <div class="field">
                    <label for="location">Dirección/Referencia</label>
                    <input type="text" id="location" name="location" maxlength="500"
                           value="{{ old('location', $cena->location) }}" required>
                    <div class="map-actions">
                        <button class="btn small secondary" type="button" id="btn-geocode">Buscar en el mapa</button>
                        <button class="btn small secondary" type="button" id="btn-geolocate">Usar mi ubicación</button>
                    </div>
                </div>

                {{-- Mapa --}}
                <div id="map"></div>

                <div  class="field" style="display:none; grid-template-columns: 1fr 1fr; gap:12px; margin-top:12px;">
                    <div>
                        <label for="latitude">Latitud</label>
                        <input type="number" id="latitude" name="latitude" step="0.000001" min="-90" max="90"
                               value="{{ old('latitude', $cena->latitude) }}" required>
                    </div>
                    <div>
                        <label for="longitude">Longitud</label>
                        <input type="number" id="longitude" name="longitude" step="0.000001" min="-180" max="180"
                               value="{{ old('longitude', $cena->longitude) }}" required>
                    </div>
                </div>

                <hr style="border:none; border-top:1px solid #e5e7eb; margin:16px 0;">

                <h3 style="margin:0 0 12px; font-size:18px; font-weight:700;">Imágenes</h3>

                {{-- Portada actual --}}
                <div class="field">
                    <label>Portada actual</label>
                    @if($cena->cover_image_url)
                        <div class="thumb" style="margin-bottom:8px;">
                            <img src="{{ $cena->cover_image_url }}" alt="Portada actual">
                        </div>
                        <label style="display:flex; gap:8px; align-items:center; font-weight:500;">
                            <input type="checkbox" name="remove_cover" value="1">
                            Eliminar portada
                        </label>
                    @else
                        <div class="muted">No hay portada establecida.</div>
                    @endif
                </div>

                {{-- Subir nueva portada --}}
                <div class="field">
                    <label for="cover_image">Nueva portada (opcional)</label>
                    <input type="file" id="cover_image" name="cover_image" accept="image/jpeg,image/png,image/webp">
                    <div class="muted">Formatos: JPG/PNG/WEBP &middot; Tamaño máx: 5MB</div>
                    <div id="cover-preview" class="thumb" style="margin-top:8px; display:none;">
                        <img src="" alt="Vista previa portada">
                    </div>
                </div>

                {{-- Galería actual --}}
                <div class="field">
                    <label>Galería actual</label>
                    @php $galleryUrls = $cena->gallery_image_urls; @endphp
                    @if($galleryUrls && $galleryUrls->count())
                        <div class="preview-grid" style="margin-bottom:8px;">
                            @foreach($galleryUrls as $idx => $url)
                                <div class="thumb">
                                    <img src="{{ $url }}" alt="Imagen {{ $idx+1 }}">
                                </div>
                                <div style="margin:6px 0 10px;">
                                    <label style="display:flex; gap:8px; align-items:center; font-weight:500;">
                                        <input type="checkbox" name="remove_gallery[]" value="{{ $idx }}">
                                        Eliminar esta imagen
                                    </label>
                                    <input type="hidden" name="gallery_existing[{{ $idx }}]" value="{{ $cena->gallery_images[$idx] }}">
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="muted">No hay imágenes en la galería.</div>
                    @endif
                </div>

                {{-- Subir nuevas imágenes a galería --}}
                <div class="field">
                    <label for="gallery_images">Agregar imágenes a la galería (máx. 5)</label>
                    <input type="file" id="gallery_images" name="gallery_images[]" accept="image/jpeg,image/png,image/webp" multiple>
                    <div class="muted">Puedes seleccionar varias a la vez.</div>
                    <div id="gallery-preview" class="preview-grid" style="margin-top:8px;"></div>
                </div>

                <hr style="border:none; border-top:1px solid #e5e7eb; margin:16px 0;">

            

            </div>
        </div>

        {{-- opcional: enviar timezone del cliente si luego lo usas en backend --}}
        <input type="hidden" id="timezone" name="timezone" value="">
    </form>
</div>

{{-- Leaflet: CSS + JS desde CDN --}}
<link
  rel="stylesheet"
  href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
  integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
  crossorigin=""
/>
<script
  src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
  integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
  crossorigin=""
></script>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    initializeQuillEditForm();
});

function initializeQuillEditForm() {
    const editorContainer = document.getElementById('quill-editor-edit');
    const hiddenInput = document.getElementById('menu-content-edit');
    
    if (!editorContainer || !hiddenInput) {
        console.error('Editor container o input oculto no encontrado');
        return;
    }
    
    // Obtener contenido existente
    const existingContent = hiddenInput.value || '';
    
    // Configuración del editor
    const quillEdit = new Quill('#quill-editor-edit', {
        theme: 'snow',
        placeholder: 'Describe los platos que incluirás en esta cena...\n\nEjemplo:\n🥗 Entrada: Ensalada de burrata con tomates cherry\n🍝 Plato principal: Risotto de hongos con trufa\n🍰 Postre: Tiramisú casero',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link'],
                ['clean']
            ]
        }
    });
    
    // Cargar contenido existente en el editor
    if (existingContent) {
        // Si el contenido es HTML, lo insertamos directamente
        quillEdit.root.innerHTML = existingContent;
    }
    
    // Sincronizar con el campo oculto
    quillEdit.on('text-change', function() {
        const content = quillEdit.root.innerHTML;
        hiddenInput.value = content;
    });
    
    // Sincronizar inicialmente
    hiddenInput.value = quillEdit.root.innerHTML;
    
    console.log('✅ Editor Quill para edición inicializado');
    
    // Hacer el editor global para debugging
    window.quillEdit = quillEdit;
}

// Función para validar contenido antes del envío
function validateMenuContent() {
    const hiddenInput = document.getElementById('menu-content-edit');
    const content = hiddenInput.value.trim();
    
    // Eliminar tags HTML para verificar si hay contenido real
    const textContent = content.replace(/<[^>]*>/g, '').trim();
    
    if (!textContent) {
        // Si usas SweetAlert2
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Campo requerido',
                text: 'Por favor describe el menú de la cena',
                icon: 'error',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#dc3545'
            });
        } else {
            alert('Por favor describe el menú de la cena');
        }
        return false;
    }
    
    return true;
}

// Si tu formulario tiene un evento submit, agregar validación
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form'); // Ajusta el selector según tu formulario
    
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateMenuContent()) {
                e.preventDefault();
                return false;
            }
        });
    }
});
(function() {
    // ---- Helpers generales
    var tz = document.getElementById('timezone');
    if (tz) { try { tz.value = Intl.DateTimeFormat().resolvedOptions().timeZone; } catch(e) {} }

    // Establece el mínimo del datetime-local al "ahora" del navegador
    var dt = document.getElementById('datetime');
    if (dt && !dt.hasAttribute('min')) {
        try { dt.min = new Date().toISOString().slice(0,16); } catch(e) {}
    }

    // Preview portada
    var coverInput = document.getElementById('cover_image');
    var coverPrev  = document.getElementById('cover-preview');
    if (coverInput && coverPrev) {
        coverInput.addEventListener('change', function(e) {
            var file = e.target.files && e.target.files[0];
            if (!file) { coverPrev.style.display = 'none'; return; }
            var reader = new FileReader();
            reader.onload = function(ev) {
                coverPrev.querySelector('img').src = ev.target.result;
                coverPrev.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    }

    // Preview galería
    var galleryInput = document.getElementById('gallery_images');
    var galleryPrev  = document.getElementById('gallery-preview');
    if (galleryInput && galleryPrev) {
        galleryInput.addEventListener('change', function(e) {
            galleryPrev.innerHTML = '';
            var files = Array.from(e.target.files || []);
            files.forEach(function(file) {
                var reader = new FileReader();
                reader.onload = function(ev) {
                    var wrap = document.createElement('div');
                    wrap.className = 'thumb';
                    var img = document.createElement('img');
                    img.src = ev.target.result;
                    img.alt = file.name;
                    wrap.appendChild(img);
                    galleryPrev.appendChild(wrap);
                };
                reader.readAsDataURL(file);
            });
        });
    }

    // ---- MAPA (Leaflet + OSM)
    var latInput = document.getElementById('latitude');
    var lonInput = document.getElementById('longitude');
    var locInput = document.getElementById('location');
    var btnGeocode = document.getElementById('btn-geocode');
    var btnGeolocate = document.getElementById('btn-geolocate');

    function parseCoord(val, fallback) {
        var n = parseFloat(val);
        return isFinite(n) ? n : fallback;
    }

    // Valores iniciales: si no hay, centro por defecto en Buenos Aires (por tu ejemplo)
    var initialLat = parseCoord(latInput && latInput.value, -34.6036739);
    var initialLon = parseCoord(lonInput && lonInput.value, -58.3821215);
    var initialZoom = (latInput && latInput.value && lonInput && lonInput.value) ? 13 : 12;

    // Crear mapa
    var map = L.map('map', { scrollWheelZoom: true });
    var osmTiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a target="_blank" rel="noopener" href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    map.setView([initialLat, initialLon], initialZoom);

    // Marcador draggable
    var marker = L.marker([initialLat, initialLon], { draggable: true }).addTo(map);

    function setLatLon(lat, lon, moveMarker = true, fit = false) {
        var latF = Number(lat).toFixed(6);
        var lonF = Number(lon).toFixed(6);
        if (latInput) latInput.value = latF;
        if (lonInput) lonInput.value = lonF;
        if (moveMarker && marker) marker.setLatLng([lat, lon]);
        if (fit) map.setView([lat, lon], 15);
    }

    // Reverse geocoding -> location
    async function reverseGeocode(lat, lon) {
        try {
            const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&accept-language=es&lat=${lat}&lon=${lon}`;
            const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
            if (!res.ok) return;
            const data = await res.json();
            if (data && data.display_name && locInput) {
                locInput.value = data.display_name;
            }
        } catch(e) { /* silencioso */ }
    }

    // Forward geocoding -> desde location
    async function geocode(q) {
        if (!q || q.trim().length < 3) return;
        try {
            const url = `https://nominatim.openstreetmap.org/search?format=jsonv2&accept-language=es&q=${encodeURIComponent(q)}`;
            const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
            if (!res.ok) return;
            const data = await res.json();
            if (Array.isArray(data) && data.length) {
                const best = data[0];
                const lat = parseFloat(best.lat);
                const lon = parseFloat(best.lon);
                setLatLon(lat, lon, true, true);
                // Opcional: normalizar el texto al display_name del resultado top
                if (locInput && best.display_name) locInput.value = best.display_name;
            }
        } catch(e) { /* silencioso */ }
    }

    // Eventos: arrastrar marcador
    marker.on('dragend', function() {
        var latlng = marker.getLatLng();
        setLatLon(latlng.lat, latlng.lng, false);
        reverseGeocode(latlng.lat, latlng.lng);
    });

    // Click en mapa: reposicionar
    map.on('click', function(e) {
        var lat = e.latlng.lat, lon = e.latlng.lng;
        setLatLon(lat, lon, true);
        reverseGeocode(lat, lon);
    });

    // Cambios manuales en inputs de coord
    if (latInput) latInput.addEventListener('change', function() {
        var lat = parseCoord(latInput.value, initialLat);
        var lon = parseCoord(lonInput.value, initialLon);
        setLatLon(lat, lon, true, true);
    });
    if (lonInput) lonInput.addEventListener('change', function() {
        var lat = parseCoord(latInput.value, initialLat);
        var lon = parseCoord(lonInput.value, initialLon);
        setLatLon(lat, lon, true, true);
    });

    // Botón: buscar por dirección
    if (btnGeocode) {
        btnGeocode.addEventListener('click', function() {
            if (locInput && locInput.value) geocode(locInput.value);
        });
    }

    // Botón: usar geolocalización del navegador
    if (btnGeolocate) {
        btnGeolocate.addEventListener('click', function() {
            if (!navigator.geolocation) return;
            navigator.geolocation.getCurrentPosition(function(pos) {
                var lat = pos.coords.latitude;
                var lon = pos.coords.longitude;
                setLatLon(lat, lon, true, true);
                reverseGeocode(lat, lon);
            });
        });
    }

    // Ajuste por si el mapa está en un contenedor que se redimensiona
    setTimeout(function(){ map.invalidateSize(); }, 300);
})();
</script>
@endsection
