@extends('layouts.app_comensal')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Header -->
            <div class="text-center mb-4">
                <h2 class="mb-3">¿Cómo fue tu experiencia?</h2>
                <div class="cena-info">
                    <h5 class="text-primary">{{ $cena->title }}</h5>
                    <p class="text-muted">{{ $cena->datetime->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <!-- Formulario -->
            <div class="card shadow">
                <div class="card-body p-4">
                    <form action="{{ route('reseñas.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_cena" value="{{ $cena->id }}">
                        <input type="hidden" name="id_reserva" value="{{ $reserva->id }}">
                        <input type="hidden" name="id_user" value="{{ auth()->id() }}">

                        <!-- Puntuación con estrellas -->
                        <div class="mb-4">
                            <label class="form-label h5 mb-3">¿Qué puntuación le das?</label>
                            <div class="star-rating">
                                <input type="hidden" name="rating" id="rating-value" required>
                                <div class="stars" id="star-container">
                                    <span class="star" data-rating="1">⭐</span>
                                    <span class="star" data-rating="2">⭐</span>
                                    <span class="star" data-rating="3">⭐</span>
                                    <span class="star" data-rating="4">⭐</span>
                                    <span class="star" data-rating="5">⭐</span>
                                </div>
                                <div class="rating-text mt-2">
                                    <small id="rating-description" class="text-muted">Haz clic en las estrellas para calificar</small>
                                </div>
                            </div>
                        </div>

                        <!-- Comentario -->
                        <div class="mb-4">
                            <label class="form-label h5">Cuéntanos sobre tu experiencia</label>
                            <textarea 
                                name="comentario" 
                                class="form-control" 
                                rows="4" 
                                placeholder="¿Qué te gustó más? ¿Cómo fue la comida? ¿Recomendarías esta experiencia?"></textarea>
                            <div class="form-text">Este comentario ayudará a otros comensales a decidir</div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('comensal.reservas.show', $reserva->id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg" id="submit-btn" disabled>
                                <i class="fas fa-star"></i> Enviar Reseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="text-center mt-4">
                <small class="text-muted">
                    Tu reseña será visible para otros usuarios y ayudará a mejorar la experiencia de la comunidad
                </small>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos para el formulario de reseña */
.cena-info {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 10px;
    border-left: 4px solid #2563eb;
}

.card {
    border: none;
    border-radius: 15px;
}

.star-rating {
    text-align: center;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 10px;
}

.stars {
    font-size: 2.5rem;
    letter-spacing: 0.5rem;
    cursor: pointer;
    margin-bottom: 0.5rem;
}

.star {
    transition: all 0.2s ease;
    filter: grayscale(100%);
    opacity: 0.3;
}

.star:hover,
.star.active {
    filter: grayscale(0%);
    opacity: 1;
    transform: scale(1.1);
}

.star.active {
    text-shadow: 0 0 10px #ffd700;
}

#rating-description {
    font-weight: 500;
    min-height: 20px;
}

.form-label.h5 {
    color: #374151;
    font-weight: 600;
}

.form-control {
    border-radius: 8px;
    border: 2px solid #e5e7eb;
    padding: 0.75rem;
}

.form-control:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
}

.btn {
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
}

.btn-primary {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    border: none;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
}

.btn-primary:disabled {
    opacity: 0.6;
    transform: none;
    box-shadow: none;
}

/* Responsive */
@media (max-width: 768px) {
    .stars {
        font-size: 2rem;
        letter-spacing: 0.3rem;
    }
    
    .container {
        padding: 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star');
    const ratingValue = document.getElementById('rating-value');
    const ratingDescription = document.getElementById('rating-description');
    const submitBtn = document.getElementById('submit-btn');
    
    const descriptions = {
        1: '😞 No me gustó',
        2: '😐 Estuvo regular',
        3: '🙂 Estuvo bien',
        4: '😊 Me gustó mucho',
        5: '🤩 ¡Excelente experiencia!'
    };
    
    stars.forEach((star, index) => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            
            // Limpiar estrellas
            stars.forEach(s => s.classList.remove('active'));
            
            // Activar estrellas hasta la seleccionada
            for (let i = 0; i < rating; i++) {
                stars[i].classList.add('active');
            }
            
            // Actualizar valor y descripción
            ratingValue.value = rating;
            ratingDescription.textContent = descriptions[rating];
            ratingDescription.style.color = '#059669';
            ratingDescription.style.fontWeight = '600';
            
            // Habilitar botón
            submitBtn.disabled = false;
            submitBtn.classList.add('btn-success');
            submitBtn.classList.remove('btn-primary');
        });
        
        // Efecto hover
        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.dataset.rating);
            
            stars.forEach((s, i) => {
                if (i < rating) {
                    s.style.filter = 'grayscale(0%)';
                    s.style.opacity = '1';
                } else {
                    s.style.filter = 'grayscale(100%)';
                    s.style.opacity = '0.3';
                }
            });
        });
        
        star.addEventListener('mouseleave', function() {
            const currentRating = parseInt(ratingValue.value);
            
            stars.forEach((s, i) => {
                if (currentRating > 0 && i < currentRating) {
                    s.style.filter = 'grayscale(0%)';
                    s.style.opacity = '1';
                } else {
                    s.style.filter = 'grayscale(100%)';
                    s.style.opacity = '0.3';
                }
            });
        });
    });
    
    // Validación del formulario
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!ratingValue.value) {
            e.preventDefault();
            alert('Por favor selecciona una puntuación');
            return false;
        }
        
        // Mostrar confirmación
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
        submitBtn.disabled = true;
    });
});
</script>
@endsection