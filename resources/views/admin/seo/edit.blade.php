@extends('layouts.app')

@section('content')
<div class="admin-container">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="admin-content">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2>SEO: {{ $pageInfo['titulo'] }}</h2>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item active">SEO {{ $pageInfo['titulo'] }}</li>
                                </ol>
                            </nav>
                        </div>
                        <div>
                            <a href="{{ $pageInfo['url_publica'] }}" target="_blank" class="btn btn-outline-primary me-2">
                                <i class="fas fa-external-link-alt me-2"></i>Ver Página
                            </a>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Volver
                            </a>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Formulario SEO -->
                    <form action="{{ route('admin.seo.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id_pagina" value="{{ $pageInfo['id_pagina'] }}">

                        <!-- Meta Tags Básicos -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Meta Tags Básicos</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Meta Title</label>
                                        <input type="text" name="meta_title" class="form-control" maxlength="60"
                                               value="{{ old('meta_title', $seoData ? $seoData->meta_title : '') }}"
                                               placeholder="Título que aparece en Google (máx. 60 caracteres)">
                                        <div class="form-text">
                                            <span id="meta-title-count">{{ $seoData ? strlen($seoData->meta_title ?? '') : 0 }}</span>/60 caracteres
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Meta Description</label>
                                        <textarea name="meta_description" class="form-control" rows="3" maxlength="160"
                                                  placeholder="Descripción que aparece en Google (máx. 160 caracteres)">{{ old('meta_description', $seoData ? $seoData->meta_description : '') }}</textarea>
                                        <div class="form-text">
                                            <span id="meta-desc-count">{{ $seoData ? strlen($seoData->meta_description ?? '') : 0 }}</span>/160 caracteres
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Palabra Clave Principal</label>
                                        <input type="text" name="focus_keyword" class="form-control"
                                               value="{{ old('focus_keyword', $seoData ? $seoData->focus_keyword : '') }}"
                                               placeholder="chef, experiencias gastronómicas">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Meta Keywords</label>
                                        <input type="text" name="meta_keywords" class="form-control"
                                               value="{{ old('meta_keywords', $seoData ? $seoData->meta_keywords : '') }}"
                                               placeholder="chef, cocina, experiencias, gastronomía">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">URL Canónica</label>
                                        <input type="url" name="canonical_url" class="form-control"
                                               value="{{ old('canonical_url', $seoData ? $seoData->canonical_url : $pageInfo['url_publica']) }}"
                                               placeholder="https://tumesa.ar/experiencias">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Robots</label>
                                        <select name="robots" class="form-control">
                                            <option value="index,follow" {{ ($seoData && $seoData->robots == 'index,follow') ? 'selected' : '' }}>index,follow</option>
                                            <option value="noindex,follow" {{ ($seoData && $seoData->robots == 'noindex,follow') ? 'selected' : '' }}>noindex,follow</option>
                                            <option value="index,nofollow" {{ ($seoData && $seoData->robots == 'index,nofollow') ? 'selected' : '' }}>index,nofollow</option>
                                            <option value="noindex,nofollow" {{ ($seoData && $seoData->robots == 'noindex,nofollow') ? 'selected' : '' }}>noindex,nofollow</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Open Graph -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fab fa-facebook me-2"></i>Open Graph (Facebook, LinkedIn)</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">OG Title</label>
                                        <input type="text" name="og_title" class="form-control" maxlength="60"
                                               value="{{ old('og_title', $seoData ? $seoData->og_title : '') }}"
                                               placeholder="Título para redes sociales">
                                        <div class="form-text">
                                            <span id="og-title-count">{{ $seoData ? strlen($seoData->og_title ?? '') : 0 }}</span>/60 caracteres
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">OG Type</label>
                                        <select name="og_type" class="form-control">
                                            <option value="website" {{ ($seoData && $seoData->og_type == 'website') ? 'selected' : '' }}>website</option>
                                            <option value="article" {{ ($seoData && $seoData->og_type == 'article') ? 'selected' : '' }}>article</option>
                                            <option value="product" {{ ($seoData && $seoData->og_type == 'product') ? 'selected' : '' }}>product</option>
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">OG Description</label>
                                        <textarea name="og_description" class="form-control" rows="2" maxlength="160"
                                                  placeholder="Descripción para redes sociales">{{ old('og_description', $seoData ? $seoData->og_description : '') }}</textarea>
                                        <div class="form-text">
                                            <span id="og-desc-count">{{ $seoData ? strlen($seoData->og_description ?? '') : 0 }}</span>/160 caracteres
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">OG Image</label>
                                        <input type="url" name="og_image" class="form-control"
                                               value="{{ old('og_image', $seoData ? $seoData->og_image : '') }}"
                                               placeholder="https://tumesa.ar/imagen-social.jpg">
                                        <small class="text-muted">Recomendado: 1200x630px</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Twitter Cards -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fab fa-twitter me-2"></i>Twitter Cards</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Twitter Title</label>
                                        <input type="text" name="twitter_title" class="form-control" maxlength="60"
                                               value="{{ old('twitter_title', $seoData ? $seoData->twitter_title : '') }}"
                                               placeholder="Título para Twitter">
                                        <div class="form-text">
                                            <span id="twitter-title-count">{{ $seoData ? strlen($seoData->twitter_title ?? '') : 0 }}</span>/60 caracteres
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Twitter Description</label>
                                        <textarea name="twitter_description" class="form-control" rows="2" maxlength="160"
                                                  placeholder="Descripción para Twitter">{{ old('twitter_description', $seoData ? $seoData->twitter_description : '') }}</textarea>
                                        <div class="form-text">
                                            <span id="twitter-desc-count">{{ $seoData ? strlen($seoData->twitter_description ?? '') : 0 }}</span>/160 caracteres
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Twitter Image</label>
                                        <input type="url" name="twitter_image" class="form-control"
                                               value="{{ old('twitter_image', $seoData ? $seoData->twitter_image : '') }}"
                                               placeholder="https://tumesa.ar/imagen-twitter.jpg">
                                        <small class="text-muted">Recomendado: 1200x600px</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Schema Markup -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-code me-2"></i>Schema Markup (JSON-LD)</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">JSON-LD Schema</label>
                                    <textarea name="schema_markup" class="form-control" rows="8"
                                              placeholder='{"@context": "https://schema.org", "@type": "LocalBusiness", "name": "TuMesa"}'>{{ old('schema_markup', $seoData && $seoData->schema_markup ? json_encode($seoData->schema_markup, JSON_PRETTY_PRINT) : '') }}</textarea>
                                    <small class="text-muted">Formato JSON válido para datos estructurados</small>
                                </div>
                            </div>
                        </div>

                        <!-- Preview -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Vista Previa Google</h5>
                            </div>
                            <div class="card-body">
                                <div id="google-preview" class="border p-3 bg-light">
                                    <div class="preview-url text-success">{{ $pageInfo['url_publica'] }}</div>
                                    <div class="preview-title text-primary fs-5" id="preview-title">
                                        {{ $seoData && $seoData->meta_title ? $seoData->meta_title : 'Título de la página' }}
                                    </div>
                                    <div class="preview-description text-muted" id="preview-description">
                                        {{ $seoData && $seoData->meta_description ? $seoData->meta_description : 'Descripción de la página que aparece en los resultados de búsqueda...' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar SEO
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.admin-container {
    background-color: #f8f9fa;
    min-height: 100vh;
    padding: 20px 0;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    margin-bottom: 1.5rem;
}

.card-header {
    background-color: #fff;
    border-bottom: 1px solid #dee2e6;
    font-weight: 600;
}

.form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-control {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    padding: 0.75rem;
}

.form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.btn {
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    border-radius: 0.375rem;
}

.breadcrumb {
    background-color: transparent;
    padding: 0;
    margin-bottom: 0;
}

#google-preview {
    font-family: arial, sans-serif;
}

.preview-url {
    font-size: 14px;
}

.preview-title {
    cursor: pointer;
    text-decoration: underline;
    margin: 2px 0;
}

.preview-description {
    font-size: 14px;
    line-height: 1.4;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Contadores de caracteres
    function setupCharCounter(inputId, counterId, maxLength) {
        const input = document.querySelector(`[name="${inputId}"]`);
        const counter = document.getElementById(counterId);
        
        if (input && counter) {
            input.addEventListener('input', function() {
                const length = this.value.length;
                counter.textContent = length;
                
                if (length > maxLength * 0.9) {
                    counter.style.color = '#dc3545';
                } else if (length > maxLength * 0.7) {
                    counter.style.color = '#ffc107';
                } else {
                    counter.style.color = '#28a745';
                }
            });
        }
    }
    
    setupCharCounter('meta_title', 'meta-title-count', 60);
    setupCharCounter('meta_description', 'meta-desc-count', 160);
    setupCharCounter('og_title', 'og-title-count', 60);
    setupCharCounter('og_description', 'og-desc-count', 160);
    setupCharCounter('twitter_title', 'twitter-title-count', 60);
    setupCharCounter('twitter_description', 'twitter-desc-count', 160);
    
    // Vista previa de Google
    const metaTitleInput = document.querySelector('[name="meta_title"]');
    const metaDescInput = document.querySelector('[name="meta_description"]');
    const previewTitle = document.getElementById('preview-title');
    const previewDesc = document.getElementById('preview-description');
    
    if (metaTitleInput && previewTitle) {
        metaTitleInput.addEventListener('input', function() {
            previewTitle.textContent = this.value || 'Título de la página';
        });
    }
    
    if (metaDescInput && previewDesc) {
        metaDescInput.addEventListener('input', function() {
            previewDesc.textContent = this.value || 'Descripción de la página que aparece en los resultados de búsqueda...';
        });
    }
});
</script>
@endsection