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
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
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
                                <h5 class="mb-0">Meta Tags Básicos</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Meta Title (máx. 60 caracteres)</label>
                                    <input type="text" name="meta_title" class="form-control" maxlength="60"
                                           value="{{ $seoData && $seoData->meta_title ? $seoData->meta_title : '' }}"
                                           placeholder="Título que aparece en Google">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Meta Description (máx. 160 caracteres)</label>
                                    <textarea name="meta_description" class="form-control" rows="3" maxlength="160"
                                              placeholder="Descripción que aparece en Google">{{ $seoData && $seoData->meta_description ? $seoData->meta_description : '' }}</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Palabra Clave Principal</label>
                                        <input type="text" name="focus_keyword" class="form-control"
                                               value="{{ $seoData && $seoData->focus_keyword ? $seoData->focus_keyword : '' }}"
                                               placeholder="chef, experiencias gastronómicas">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Meta Keywords</label>
                                        <input type="text" name="meta_keywords" class="form-control"
                                               value="{{ $seoData && $seoData->meta_keywords ? $seoData->meta_keywords : '' }}"
                                               placeholder="chef, cocina, experiencias">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">URL Canónica</label>
                                        <input type="url" name="canonical_url" class="form-control"
                                               value="{{ $seoData && $seoData->canonical_url ? $seoData->canonical_url : '' }}"
                                               placeholder="https://tumesa.ar/experiencias">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Robots</label>
                                        <select name="robots" class="form-control">
                                            <option value="index,follow">index,follow</option>
                                            <option value="noindex,follow">noindex,follow</option>
                                            <option value="index,nofollow">index,nofollow</option>
                                            <option value="noindex,nofollow">noindex,nofollow</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Open Graph -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Open Graph (Facebook, LinkedIn)</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">OG Title</label>
                                        <input type="text" name="og_title" class="form-control" maxlength="60"
                                               value="{{ $seoData && $seoData->og_title ? $seoData->og_title : '' }}"
                                               placeholder="Título para redes sociales">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">OG Type</label>
                                        <select name="og_type" class="form-control">
                                            <option value="website">website</option>
                                            <option value="article">article</option>
                                            <option value="product">product</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">OG Description</label>
                                    <textarea name="og_description" class="form-control" rows="2" maxlength="160"
                                              placeholder="Descripción para redes sociales">{{ $seoData && $seoData->og_description ? $seoData->og_description : '' }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">OG Image</label>
                                    <input type="url" name="og_image" class="form-control"
                                           value="{{ $seoData && $seoData->og_image ? $seoData->og_image : '' }}"
                                           placeholder="https://tumesa.ar/imagen-social.jpg">
                                    <small class="text-muted">Recomendado: 1200x630px</small>
                                </div>
                            </div>
                        </div>

                        <!-- Twitter Cards -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Twitter Cards</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Twitter Title</label>
                                    <input type="text" name="twitter_title" class="form-control" maxlength="60"
                                           value="{{ $seoData && $seoData->twitter_title ? $seoData->twitter_title : '' }}"
                                           placeholder="Título para Twitter">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Twitter Description</label>
                                    <textarea name="twitter_description" class="form-control" rows="2" maxlength="160"
                                              placeholder="Descripción para Twitter">{{ $seoData && $seoData->twitter_description ? $seoData->twitter_description : '' }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Twitter Image</label>
                                    <input type="url" name="twitter_image" class="form-control"
                                           value="{{ $seoData && $seoData->twitter_image ? $seoData->twitter_image : '' }}"
                                           placeholder="https://tumesa.ar/imagen-twitter.jpg">
                                    <small class="text-muted">Recomendado: 1200x600px</small>
                                </div>
                            </div>
                        </div>

                        <!-- Schema Markup -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Schema Markup (JSON-LD)</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">JSON-LD Schema</label>
                                    <textarea name="schema_markup" class="form-control" rows="8"
                                              placeholder='{"@context": "https://schema.org", "@type": "LocalBusiness", "name": "TuMesa"}'>@if($seoData && $seoData->schema_markup){{ json_encode($seoData->schema_markup, JSON_PRETTY_PRINT) }}@endif</textarea>
                                    <small class="text-muted">Formato JSON válido para datos estructurados</small>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Guardar SEO
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
</style>
@endsection