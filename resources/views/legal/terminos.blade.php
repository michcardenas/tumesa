@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold text-primary">Términos y Condiciones</h1>
                <p class="text-muted">Última actualización: Agosto 2025</p>
            </div>

            <!-- Navegación de secciones -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Navegación rápida</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="#comensales" class="btn btn-outline-primary btn-sm">Para Comensales</a>
                        <a href="#chefs" class="btn btn-outline-primary btn-sm">Para Chefs Anfitriones</a>
                    </div>
                </div>
            </div>

            <!-- TÉRMINOS PARA COMENSALES -->
            <div id="comensales" class="card shadow-sm mb-5">
                <div class="card-header bg-primary text-white">
                    <h2 class="h3 mb-0">
                        <i class="fas fa-users me-2"></i>
                        TÉRMINOS Y CONDICIONES PARA COMENSALES
                    </h2>
                </div>
                <div class="card-body">
                    <!-- Objeto -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>Objeto
                        </h3>
                        <p class="text-justify">
                            Tu Mesa es una plataforma que conecta comensales con chefs anfitriones que organizan cenas en diferentes 
                            locaciones. Tu Mesa no es el organizador directo de la experiencia gastronómica, sino un intermediario que 
                            facilita la reserva, el cobro y la comunicación entre las partes.
                        </p>
                    </section>

                    <!-- Reserva y pago -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-credit-card me-2"></i>Reserva y pago
                        </h3>
                        <p class="text-justify">
                            El pago se realiza exclusivamente a través de la plataforma mediante Mercado Pago. Una vez confirmada la 
                            reserva, el comensal recibirá un comprobante en el correo electrónico registrado. El chef no podrá recibir pagos 
                            directamente del comensal fuera de la plataforma.
                        </p>
                    </section>

                    <!-- Cancelaciones y modificaciones -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-calendar-times me-2"></i>Cancelaciones y modificaciones
                        </h3>
                        <p class="text-justify mb-3">
                            Los plazos se cuentan en días corridos hacia atrás desde la fecha del evento:
                        </p>
                        <div class="alert alert-info">
                            <ul class="mb-0">
                                <li><strong>Hasta 7 días antes:</strong> reintegro del 100%</li>
                                <li><strong>Entre 6 y 3 días antes:</strong> reintegro del 50%</li>
                                <li><strong>Dentro de las 48 horas previas:</strong> sin reintegro</li>
                            </ul>
                        </div>
                        <p class="text-justify">
                            El comensal podrá modificar el nombre del cubierto hasta 48 horas antes del evento, pero no podrá cambiar fecha ni menú.
                        </p>
                    </section>

                    <!-- Reintegros -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-undo me-2"></i>Reintegros
                        </h3>
                        <p class="text-justify">
                            Los reintegros se realizarán por el mismo medio de pago utilizado, en un plazo estimado de entre 7 y 10 días 
                            hábiles, sujeto a los tiempos del procesador de pagos.
                        </p>
                    </section>

                    <!-- Responsabilidad y reclamos -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>Responsabilidad y reclamos
                        </h3>
                        <p class="text-justify">
                            Tu Mesa actúa solo como intermediario. No se responsabiliza por cambios en la propuesta, incumplimientos del 
                            chef, ni intolerancias alimenticias no informadas previamente. Los reclamos deberán realizarse dentro de las 48 
                            horas posteriores al evento al correo oficial: <a href="mailto:tumesarg@gmail.com">tumesarg@gmail.com</a>.
                        </p>
                    </section>

                    <!-- Conducta -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-user-check me-2"></i>Conducta
                        </h3>
                        <p class="text-justify">
                            El comensal debe respetar normas de convivencia, puntualidad y cuidado del espacio. El incumplimiento podrá 
                            derivar en expulsión sin reintegro y en la suspensión de la cuenta en la plataforma.
                        </p>
                    </section>

                    <!-- Uso de la plataforma -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-laptop me-2"></i>Uso de la plataforma
                        </h3>
                        <p class="text-justify">
                            El comensal se compromete a brindar información veraz y no usar la plataforma con fines fraudulentos. El mal 
                            uso podrá implicar la suspensión o baja definitiva de la cuenta.
                        </p>
                    </section>

                    <!-- Datos personales -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-shield-alt me-2"></i>Datos personales
                        </h3>
                        <p class="text-justify">
                            La información será usada únicamente para la gestión de la experiencia y resguardada según la Ley 25.326 de 
                            Protección de Datos Personales. La Política de Privacidad completa estará disponible en el sitio web de Tu Mesa.
                        </p>
                    </section>

                    <!-- Ubicación del evento -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-map-marker-alt me-2"></i>Ubicación del evento
                        </h3>
                        <p class="text-justify">
                            La dirección exacta del evento se revelará únicamente después de confirmada la reserva, con el fin de proteger la 
                            privacidad de los anfitriones.
                        </p>
                    </section>

                    <!-- Defensa al consumidor -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-balance-scale me-2"></i>Defensa al consumidor
                        </h3>
                        <p class="text-justify">
                            El comensal podrá recurrir a Defensa del Consumidor en caso de considerarlo necesario, conforme a la Ley 24.240.
                        </p>
                    </section>

                    <!-- Jurisdicción -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-gavel me-2"></i>Jurisdicción
                        </h3>
                        <p class="text-justify">
                            Estos términos se rigen por las leyes de la República Argentina. La jurisdicción será la de la Ciudad Autónoma de 
                            Buenos Aires, salvo que la ley indique lo contrario.
                        </p>
                    </section>
                </div>
            </div>

            <!-- TÉRMINOS PARA CHEFS ANFITRIONES -->
            <div id="chefs" class="card shadow-sm mb-5">
                <div class="card-header bg-dark text-white">
                    <h2 class="h3 mb-0">
                        <i class="fas fa-chef-hat me-2"></i>
                        TÉRMINOS Y CONDICIONES PARA CHEFS ANFITRIONES
                    </h2>
                </div>
                <div class="card-body">
                    <!-- Objeto -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>Objeto
                        </h3>
                        <p class="text-justify">
                            Los chefs anfitriones ofrecen experiencias gastronómicas en espacios seleccionados por ellos. Tu Mesa brinda la 
                            plataforma de visibilidad, reservas y cobros, a cambio de una comisión.
                        </p>
                    </section>

                    <!-- Responsabilidad del chef -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-user-tie me-2"></i>Responsabilidad del chef
                        </h3>
                        <p class="text-justify">
                            El chef es responsable de cumplir con lo publicado en su propuesta (menú, horario, ubicación y condiciones). 
                            Debe garantizar la seguridad alimentaria y cumplir normas de higiene. El chef deberá ofrecer un canal de 
                            comunicación a través de la plataforma para coordinar con los comensales. El incumplimiento podrá resultar en 
                            sanciones o baja de la plataforma.
                        </p>
                    </section>

                    <!-- Cancelaciones -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-calendar-times me-2"></i>Cancelaciones
                        </h3>
                        <p class="text-justify mb-3">El chef deberá cancelar exclusivamente a través de la plataforma:</p>
                        <div class="alert alert-warning">
                            <ul class="mb-0">
                                <li><strong>Si cancela con al menos 72 horas de antelación:</strong> los comensales serán notificados y podrán reprogramar o recibir reintegro total.</li>
                                <li><strong>Cancelaciones injustificadas con menos de 72 horas:</strong> podrán implicar sanciones como advertencias, suspensión temporal o baja definitiva.</li>
                            </ul>
                        </div>
                        <p class="text-justify">
                            Los comensales y el chef recibirán las notificaciones vía correo electrónico.
                        </p>
                    </section>

                    <!-- Cobros y comisión -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-percentage me-2"></i>Cobros y comisión
                        </h3>
                        <p class="text-justify">
                            Tu Mesa percibe un porcentaje de cada reserva confirmada. El detalle del porcentaje se informará en el panel de 
                            liquidaciones de cada chef. Los pagos al chef se realizarán en la cuenta bancaria registrada en un plazo de hasta 
                            10 días hábiles posteriores a la experiencia, descontando la comisión.
                        </p>
                    </section>

                    <!-- Conducta -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-user-check me-2"></i>Conducta
                        </h3>
                        <p class="text-justify">
                            El chef debe ofrecer un ambiente seguro, cordial y respetuoso. Conductas inapropiadas (acoso, discriminación, 
                            violencia) podrán implicar la suspensión inmediata o baja definitiva.
                        </p>
                    </section>

                    <!-- Uso de la plataforma -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-laptop me-2"></i>Uso de la plataforma
                        </h3>
                        <p class="text-justify">
                            El chef no podrá utilizar la plataforma con fines distintos a la organización de experiencias gastronómicas ni 
                            derivar comensales fuera del sistema de Tu Mesa. El incumplimiento podrá derivar en la baja inmediata.
                        </p>
                    </section>

                    <!-- Datos personales -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-shield-alt me-2"></i>Datos personales
                        </h3>
                        <p class="text-justify">
                            Los datos de los comensales estarán resguardados según la Ley 25.326 y no podrán ser utilizados para fines 
                            comerciales externos a Tu Mesa.
                        </p>
                    </section>

                    <!-- Ubicación del evento -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-map-marker-alt me-2"></i>Ubicación del evento
                        </h3>
                        <p class="text-justify">
                            La dirección del evento será compartida únicamente con comensales que hayan confirmado su reserva, 
                            protegiendo así la privacidad del anfitrión.
                        </p>
                    </section>

                    <!-- Reclamos -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-comment-alt me-2"></i>Reclamos
                        </h3>
                        <p class="text-justify">
                            El chef podrá presentar reclamos o denuncias sobre conductas inapropiadas de comensales a través del canal 
                            oficial: <a href="mailto:tumesarg@gmail.com">tumesarg@gmail.com</a>.
                        </p>
                    </section>

                    <!-- Defensa al consumidor -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-balance-scale me-2"></i>Defensa al consumidor
                        </h3>
                        <p class="text-justify">
                            Los chefs también estarán sujetos a las normativas vigentes de Defensa del Consumidor en la República Argentina.
                        </p>
                    </section>

                    <!-- Jurisdicción -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-gavel me-2"></i>Jurisdicción
                        </h3>
                        <p class="text-justify">
                            Estos términos se rigen por las leyes de la República Argentina. La jurisdicción será la de la Ciudad Autónoma de 
                            Buenos Aires, salvo disposición legal en contrario.
                        </p>
                    </section>
                </div>
            </div>

            <!-- Botón de volver -->
            <div class="text-center">
                <a href="{{ url('/') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Volver al inicio
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .text-justify {
        text-align: justify;
    }
    
    .card {
        border-radius: 10px;
    }
    
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    
    section {
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 1.5rem;
    }
    
    section:last-child {
        border-bottom: none;
    }
    
    .alert {
        border-left: 4px solid;
    }
    
    .alert-info {
        border-left-color: #0dcaf0;
    }
    
    .alert-warning {
        border-left-color: #ffc107;
    }
    
    @media (max-width: 768px) {
        .display-5 {
            font-size: 2rem;
        }
    }
</style>
@endsection