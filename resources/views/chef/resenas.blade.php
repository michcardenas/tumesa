@extends('layouts.app_chef')

@section('content')
<div class="container">
    <div class="chef-content">
        <h2 class="mb-4 d-flex align-items-center">
            <i class="fas fa-star me-2"></i> Mis Reseñas
        </h2>

        @if($resenas->count() > 0)
            <div class="row">
                @foreach($resenas as $resena)
                    <div class="col-12 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h5 class="card-title">{{ $resena->cena->titulo }}</h5>
                                        <h6 class="card-subtitle mb-2 text-muted">
                                            Por: {{ $resena->user->name }}
                                        </h6>
                                        
                                        {{-- Rating con estrellas --}}
                                        <div class="mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $resena->rating)
                                                    <i class="fas fa-star text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-muted"></i>
                                                @endif
                                            @endfor
                                            <span class="ms-2 badge bg-primary">{{ $resena->rating }}/5</span>
                                        </div>

                                        {{-- Comentario --}}
                                        @if($resena->comentario)
                                            <p class="card-text">{{ $resena->comentario }}</p>
                                        @else
                                            <p class="card-text text-muted fst-italic">Sin comentario</p>
                                        @endif
                                    </div>
                                    
                                    <div class="col-md-4 text-end">
                                        <small class="text-muted">
                                            {{ $resena->created_at->format('d/m/Y') }}<br>
                                            {{ $resena->created_at->format('H:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Estadísticas básicas --}}
            <div class="card mt-4 bg-light">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h5>{{ $resenas->count() }}</h5>
                            <small class="text-muted">Total Reseñas</small>
                        </div>
                        <div class="col-md-4">
                            <h5>{{ number_format($resenas->avg('rating'), 1) }}</h5>
                            <small class="text-muted">Rating Promedio</small>
                        </div>
                        <div class="col-md-4">
                            <h5>{{ $resenas->where('rating', '>=', 4)->count() }}</h5>
                            <small class="text-muted">Reseñas 4+ estrellas</small>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-star fa-3x text-muted mb-3"></i>
                <h4>Aún no tienes reseñas</h4>
                <p class="text-muted">Las reseñas aparecerán aquí cuando los comensales evalúen tus cenas.</p>
            </div>
        @endif
    </div>
</div>
@endsection