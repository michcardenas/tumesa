@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold text-primary">Política de Privacidad</h1>
                <p class="text-muted">Última actualización: Agosto 2025</p>
            </div>

            <!-- Introducción -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <p class="lead">
                        En <strong>Tu Mesa</strong>, valoramos y respetamos tu privacidad. Esta Política de Privacidad describe cómo recopilamos, 
                        utilizamos y protegemos tu información personal cuando utilizas nuestra plataforma.
                    </p>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h2 class="h4 mb-0">
                        <i class="fas fa-shield-alt me-2"></i>
                        Información sobre el Tratamiento de Datos Personales
                    </h2>
                </div>
                <div class="card-body">
                    <!-- Información que recopilamos -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-database me-2"></i>1. Información que Recopilamos
                        </h3>
                        <p class="text-justify mb-3">Recopilamos diferentes tipos de información para brindarte nuestros servicios:</p>
                        
                        <div class="ms-3">
                            <h6 class="text-secondary">Información Personal:</h6>
                            <ul>
                                <li>Nombre completo y apellido</li>
                                <li>Dirección de correo electrónico</li>
                                <li>Número de teléfono</li>
                                <li>Fecha de nacimiento (para verificar mayoría de edad)</li>
                                <li>Información de pago (procesada de forma segura a través de Mercado Pago)</li>
                            </ul>

                            <h6 class="text-secondary mt-3">Información de Uso:</h6>
                            <ul>
                                <li>Historial de reservas y experiencias</li>
                                <li>Preferencias gastronómicas e intolerancias alimentarias</li>
                                <li>Comunicaciones entre comensales y chefs</li>
                                <li>Valoraciones y comentarios</li>
                            </ul>

                            <h6 class="text-secondary mt-3">Información Técnica:</h6>
                            <ul>
                                <li>Dirección IP</li>
                                <li>Tipo de navegador y dispositivo</li>
                                <li>Información de cookies</li>
                                <li>Datos de navegación en la plataforma</li>
                            </ul>
                        </div>
                    </section>

                    <!-- Cómo utilizamos tu información -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-cogs me-2"></i>2. Cómo Utilizamos tu Información
                        </h3>
                        <p class="text-justify mb-3">Utilizamos tu información personal para:</p>
                        <ul>
                            <li>Procesar y gestionar tus reservas</li>
                            <li>Facilitar la comunicación entre comensales y chefs anfitriones</li>
                            <li>Procesar pagos de forma segura</li>
                            <li>Enviarte confirmaciones y recordatorios de tus experiencias</li>
                            <li>Mejorar nuestros servicios y personalizar tu experiencia</li>
                            <li>Cumplir con obligaciones legales y fiscales</li>
                            <li>Prevenir fraudes y actividades ilegales</li>
                            <li>Enviarte información sobre nuevas experiencias (con tu consentimiento)</li>
                        </ul>
                    </section>

                    <!-- Compartir información -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-share-alt me-2"></i>3. Compartir Información con Terceros
                        </h3>
                        <p class="text-justify mb-3">
                            <strong>NO vendemos ni alquilamos tu información personal.</strong> Solo compartimos tu información en los siguientes casos:
                        </p>
                        <div class="alert alert-info">
                            <ul class="mb-0">
                                <li><strong>Con Chefs Anfitriones:</strong> Información necesaria para coordinar tu experiencia (nombre y restricciones alimentarias)</li>
                                <li><strong>Con Mercado Pago:</strong> Para procesar pagos de forma segura</li>
                                <li><strong>Por requerimiento legal:</strong> Cuando sea requerido por autoridades competentes</li>
                                <li><strong>Para proteger derechos:</strong> Cuando sea necesario para proteger la seguridad de Tu Mesa o sus usuarios</li>
                            </ul>
                        </div>
                    </section>

                    <!-- Seguridad de datos -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-lock me-2"></i>4. Seguridad de tus Datos
                        </h3>
                        <p class="text-justify">
                            Implementamos medidas de seguridad técnicas, administrativas y físicas para proteger tu información personal contra 
                            acceso no autorizado, pérdida, alteración o divulgación. Estas medidas incluyen:
                        </p>
                        <ul>
                            <li>Encriptación de datos sensibles</li>
                            <li>Conexiones seguras (HTTPS/SSL)</li>
                            <li>Acceso restringido a información personal</li>
                            <li>Monitoreo regular de nuestros sistemas</li>
                            <li>Cumplimiento con la Ley 25.326 de Protección de Datos Personales</li>
                        </ul>
                    </section>

                    <!-- Retención de datos -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-clock me-2"></i>5. Retención de Datos
                        </h3>
                        <p class="text-justify">
                            Conservamos tu información personal solo durante el tiempo necesario para cumplir con los propósitos descritos 
                            en esta política, incluyendo cualquier requisito legal, contable o de información. Los criterios utilizados para 
                            determinar nuestros períodos de retención incluyen:
                        </p>
                        <ul>
                            <li>La duración de nuestra relación contigo como usuario</li>
                            <li>Si existe una obligación legal de conservar los datos</li>
                            <li>Si la retención es aconsejable según nuestra posición legal</li>
                        </ul>
                    </section>

                    <!-- Derechos del usuario -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-user-shield me-2"></i>6. Tus Derechos
                        </h3>
                        <p class="text-justify mb-3">
                            De acuerdo con la Ley 25.326 de Protección de Datos Personales, tienes derecho a:
                        </p>
                        <div class="alert alert-success">
                            <ul class="mb-0">
                                <li><strong>Acceso:</strong> Solicitar información sobre los datos personales que tenemos sobre ti</li>
                                <li><strong>Rectificación:</strong> Corregir datos inexactos o incompletos</li>
                                <li><strong>Supresión:</strong> Solicitar la eliminación de tus datos personales</li>
                                <li><strong>Oposición:</strong> Oponerte al tratamiento de tus datos para ciertos fines</li>
                                <li><strong>Portabilidad:</strong> Recibir tus datos en un formato estructurado</li>
                            </ul>
                        </div>
                        <p class="text-justify">
                            Para ejercer estos derechos, puedes contactarnos en: <a href="mailto:tumesarg@gmail.com">tumesarg@gmail.com</a>
                        </p>
                    </section>

                    <!-- Cookies -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-cookie-bite me-2"></i>7. Uso de Cookies
                        </h3>
                        <p class="text-justify">
                            Utilizamos cookies y tecnologías similares para mejorar tu experiencia en nuestra plataforma. Las cookies nos ayudan a:
                        </p>
                        <ul>
                            <li>Recordar tus preferencias y configuración</li>
                            <li>Mantener tu sesión activa</li>
                            <li>Analizar el uso de nuestra plataforma</li>
                            <li>Personalizar contenido y ofertas</li>
                        </ul>
                        <p class="text-justify">
                            Puedes configurar tu navegador para rechazar cookies, aunque esto podría afectar algunas funcionalidades de la plataforma.
                        </p>
                    </section>

                    <!-- Menores de edad -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-child me-2"></i>8. Menores de Edad
                        </h3>
                        <p class="text-justify">
                            Tu Mesa no está dirigido a menores de 18 años. No recopilamos intencionalmente información personal de 
                            menores de edad. Si eres padre o tutor y tienes conocimiento de que tu hijo nos ha proporcionado información 
                            personal, contáctanos para que podamos tomar las medidas necesarias.
                        </p>
                    </section>

                    <!-- Cambios en la política -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-edit me-2"></i>9. Cambios en esta Política
                        </h3>
                        <p class="text-justify">
                            Podemos actualizar esta Política de Privacidad ocasionalmente. Te notificaremos sobre cambios significativos 
                            publicando la nueva política en nuestra plataforma con una nueva fecha de "Última actualización". Te recomendamos 
                            revisar esta política periódicamente.
                        </p>
                    </section>

                    <!-- Información de contacto -->
                    <section class="mb-4">
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-envelope me-2"></i>10. Contacto
                        </h3>
                        <p class="text-justify">
                            Si tienes preguntas, inquietudes o solicitudes relacionadas con esta Política de Privacidad o el tratamiento 
                            de tus datos personales, puedes contactarnos a través de:
                        </p>
                        <div class="card bg-light mt-3">
                            <div class="card-body">
                                <p class="mb-2"><strong>Tu Mesa Argentina</strong></p>
                                <p class="mb-1"><i class="fas fa-envelope me-2"></i>Email: <a href="mailto:tumesarg@gmail.com">tumesarg@gmail.com</a></p>
                                <p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i>Ubicación: Buenos Aires, Argentina</p>
                                <p class="mb-0"><i class="fas fa-globe me-2"></i>Sitio web: www.tumesa.com.ar</p>
                            </div>
                        </div>
                    </section>

                    <!-- Marco legal -->
                    <section>
                        <h3 class="h5 text-primary mb-3">
                            <i class="fas fa-balance-scale me-2"></i>11. Marco Legal
                        </h3>
                        <p class="text-justify">
                            Esta Política de Privacidad se rige por la legislación argentina, en particular:
                        </p>
                        <ul>
                            <li>Ley N° 25.326 de Protección de Datos Personales</li>
                            <li>Decreto 1558/2001 reglamentario de la Ley 25.326</li>
                            <li>Disposiciones de la Agencia de Acceso a la Información Pública</li>
                        </ul>
                        <div class="alert alert-secondary mt-3">
                            <p class="mb-0">
                                <strong>Dirección Nacional de Protección de Datos Personales</strong><br>
                                Órgano de Control - Ley N° 25.326<br>
                                Av. Pte. Julio A. Roca 710, Piso 2° - Ciudad Autónoma de Buenos Aires
                            </p>
                        </div>
                    </section>
                </div>
            </div>

            <!-- Enlaces a otras políticas -->
            <div class="card shadow-sm mt-4">
                <div class="card-body text-center">
                    <h5 class="mb-3">Documentos Relacionados</h5>
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <a href="{{ route('terminos') }}" class="btn btn-outline-primary">
                            <i class="fas fa-file-contract me-2"></i>Términos y Condiciones
                        </a>
                        <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i>Volver al Inicio
                        </a>
                    </div>
                </div>
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
    
    .alert-success {
        border-left-color: #198754;
    }
    
    .alert-secondary {
        border-left-color: #6c757d;
    }
    
    h6.text-secondary {
        font-weight: 600;
        margin-top: 0.5rem;
    }
    
    @media (max-width: 768px) {
        .display-5 {
            font-size: 2rem;
        }
    }
</style>
@endsection