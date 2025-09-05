@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Dejar Reseña</h2>
    <p><strong>{{ $cena->title }}</strong> - {{ $cena->datetime->format('d/m/Y H:i') }}</p>

    <form action="{{ route('reseñas.store') }}" method="POST">
        @csrf
        <input type="hidden" name="id_cena" value="{{ $cena->id }}">
        <input type="hidden" name="id_reserva" value="{{ $reserva->id }}">
        <input type="hidden" name="id_user" value="{{ auth()->id() }}">

        <div class="mb-3">
            <label class="form-label">Puntuación:</label>
            <select name="rating" class="form-control" required>
                <option value="">Selecciona...</option>
                @for($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}">{{ $i }} ⭐</option>
                @endfor
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Comentario (opcional):</label>
            <textarea name="comentario" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Enviar Reseña
        </button>
    </form>
</div>
@endsection
