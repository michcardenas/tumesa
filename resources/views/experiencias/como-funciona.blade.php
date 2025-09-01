@extends('layouts.app')

@section('title', 'Cómo Funciona')

@section('content')
<style>
:root {
  --primary: #0C3558;
  --accent: #3CB28B;
  --ink: #0f172a;
  --muted: #667085;
  --card: #ffffff;
  --bg: #f6f7fb;
  --border: #e6e8ef;
}

body {
  background: var(--bg);
}

.page {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

/* ========== HERO ========== */
.hero {
  background: linear-gradient(135deg, #f3f7ff 0%, #eef6ff 100%);
  border-radius: 20px;
  padding: 4rem 2rem;
  text-align: center;
  margin-bottom: 4rem;
  box-shadow: 0 10px 30px rgba(12, 53, 88, 0.08);
}

.hero h1 {
  font-size: 3rem;
  font-weight: 900;
  margin: 0 0 1rem;
  color: var(--ink);
}

.hero p {
  max-width: 700px;
  margin: 0 auto 2rem;
  color: var(--muted);
  font-size: 1.125rem;
  line-height: 1.6;
}

.actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.875rem 1.5rem;
  border-radius: 12px;
  font-weight: 700;
  text-decoration: none;
  border: 2px solid transparent;
  transition: all 0.2s ease;
}

.btn:hover {
  transform: translateY(-1px);
}

.btn.primary {
  background: var(--primary);
  color: white;
  box-shadow: 0 6px 16px rgba(12, 53, 88, 0.15);
}

.btn.primary:hover {
  background: #1e4a73;
  box-shadow: 0 8px 20px rgba(12, 53, 88, 0.2);
}

.btn.ghost {
  background: white;
  color: var(--primary);
  border-color: var(--primary);
  box-shadow: 0 4px 12px rgba(12, 53, 88, 0.08);
}

.btn.ghost:hover {
  background: var(--primary);
  color: white;
}

/* ========== SECTIONS ========== */
.section-title {
  font-size: 2.25rem;
  font-weight: 900;
  text-align: center;
  margin: 3rem 0 0.5rem;
  color: var(--ink);
}

.section-sub {
  color: var(--muted);
  text-align: center;
  margin: 0 0 2rem;
  font-size: 1.125rem;
}

/* ========== TABS ========== */
.tabs {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
  flex-wrap: wrap;
  margin-bottom: 2rem;
  background: white;
  padding: 0.5rem;
  border-radius: 16px;
  border: 1px solid var(--border);
  box-shadow: 0 4px 12px rgba(12, 53, 88, 0.06);
  width: fit-content;
  margin-left: auto;
  margin-right: auto;
}

.tab {
  background: transparent;
  color: var(--muted);
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}

.tab.active {
  background: var(--primary);
  color: white;
}

.tab:not(.active):hover {
  color: var(--primary);
  background: rgba(12, 53, 88, 0.05);
}

/* ========== GRIDS ========== */
.grid-3 {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
}

.grid-4 {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.5rem;
}

/* ========== CARDS ========== */
.card {
  background: white;
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 4px 12px rgba(12, 53, 88, 0.06);
  transition: all 0.2s ease;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(12, 53, 88, 0.1);
  border-color: var(--accent);
}

.card h4 {
  margin: 1rem 0 0.75rem;
  font-size: 1.125rem;
  font-weight: 800;
  color: var(--ink);
}

.card p {
  margin: 0;
  color: var(--muted);
  line-height: 1.5;
}

.icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, rgba(60, 178, 139, 0.1), rgba(12, 53, 88, 0.1));
  color: var(--primary);
}

.icon svg {
  width: 24px;
  height: 24px;
}

/* ========== STEPS ========== */
.step {
  background: white;
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 4px 12px rgba(12, 53, 88, 0.06);
  transition: all 0.2s ease;
  text-align: center;
}

.step:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(12, 53, 88, 0.1);
}

.n {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--accent);
  color: white;
  font-weight: 900;
  margin: 0 auto 1rem;
}

.step h5 {
  margin: 0 0 0.75rem;
  font-size: 1rem;
  font-weight: 800;
  color: var(--ink);
}

.step p {
  margin: 0;
  color: var(--muted);
  line-height: 1.5;
  font-size: 0.9rem;
}

/* ========== TIMELINE ========== */
.timeline {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 1rem;
  margin-top: 1rem;
}

.titem {
  background: white;
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 1rem;
  box-shadow: 0 2px 8px rgba(12, 53, 88, 0.04);
  text-align: center;
  transition: all 0.2s ease;
}

.titem:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(12, 53, 88, 0.08);
}

.titem .dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: var(--accent);
  margin: 0 auto 0.75rem;
}

.titem strong {
  color: var(--ink);
  font-weight: 700;
  display: block;
  margin-bottom: 0.25rem;
  font-size: 0.9rem;
}

.titem .muted {
  color: var(--muted);
  font-size: 0.8rem;
}

/* ========== KPI ========== */
.kpi {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  background: rgba(255, 255, 255, 0.8);
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 0.75rem 1rem;
  margin-top: 0.75rem;
}

.kpi .badge {
  background: #eaf8f2;
  color: #059669;
  border-radius: 8px;
  padding: 0.25rem 0.5rem;
  font-weight: 700;
  font-size: 0.75rem;
}

/* ========== FAQ ========== */
.faq {
  max-width: 700px;
  margin: 1rem auto;
}

.faq details {
  background: white;
  border: 1px solid var(--border);
  border-radius: 12px;
  margin-bottom: 0.75rem;
  box-shadow: 0 2px 8px rgba(12, 53, 88, 0.04);
}

.faq details:hover {
  box-shadow: 0 4px 12px rgba(12, 53, 88, 0.08);
}

.faq summary {
  cursor: pointer;
  list-style: none;
  padding: 1rem 1.25rem;
  font-weight: 700;
  color: var(--ink);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.faq summary::-webkit-details-marker {
  display: none;
}

.faq .a {
  padding: 0 1.25rem 1rem;
  color: var(--muted);
  line-height: 1.5;
  border-top: 1px solid var(--border);
  margin-top: -1px;
}

.chev {
  transition: transform 0.2s ease;
  color: var(--accent);
}

details[open] .chev {
  transform: rotate(180deg);
}

/* ========== UTILITIES ========== */
.cta {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
  margin: 2rem 0;
}

.small {
  font-size: 0.875rem;
  color: var(--muted);
  text-align: center;
  margin-top: 0.5rem;
}

.hidden {
  display: none;
}

/* ========== RESPONSIVE ========== */
@media (max-width: 768px) {
  .hero h1 {
    font-size: 2.25rem;
  }
  
  .hero {
    padding: 2.5rem 1.5rem;
  }
  
  .section-title {
    font-size: 1.875rem;
  }
  
  .tabs {
    width: 100%;
  }
  
  .tab {
    flex: 1;
    text-align: center;
  }
}

@media (max-width: 480px) {
  .actions {
    flex-direction: column;
    width: 100%;
  }
  
  .btn {
    width: 100%;
    justify-content: center;
  }
  
  .cta {
    flex-direction: column;
  }
  
  .cta .btn {
    width: 100%;
  }
}
</style>

<div class="page">
  {{-- HERO --}}
  <section class="hero">
    <h1>Cómo Funciona TuMesa</h1>
    <p>Descubre el flujo completo: desde encontrar una experiencia hasta realizar la reserva y compartir tu comida con personas increíbles. Simple, seguro y pensado para ti.</p>
    <div class="actions">
      <a class="btn primary" href="{{ route('experiencias') }}">Ver Experiencias</a>
      <a class="btn ghost" href="{{ route('ser-chef') }}">Ser Chef Anfitrión</a>
    </div>
  </section>

  {{-- TABS --}}
  <h2 class="section-title">Elige tu camino</h2>
  <p class="section-sub">Te mostramos el proceso paso a paso.</p>
  <div class="tabs">
    <button type="button" class="tab active" data-flow="guest">Para Invitados</button>
    <button type="button" class="tab" data-flow="chef">Para Chefs Anfitriones</button>
  </div>

  {{-- PARA INVITADOS --}}
  <section data-section="guest">
    <div class="grid-4 steps">
      <div class="step">
        <div class="n">1</div>
        <h5>Explora experiencias</h5>
        <p>Filtra por ciudad, precio y fecha. Revisa fotos, menú y reseñas del chef.</p>
      </div>
      <div class="step">
        <div class="n">2</div>
        <h5>Reserva tu lugar</h5>
        <p>Elige cantidad de personas y confirma con un pago seguro.</p>
      </div>
      <div class="step">
        <div class="n">3</div>
        <h5>Confirmación inmediata</h5>
        <p>Recibes los detalles por email y tu panel. El chef se prepara para recibirte.</p>
      </div>
      <div class="step">
        <div class="n">4</div>
        <h5>Disfruta y califica</h5>
        <p>Vive la experiencia, deja una reseña y ayuda a otros a elegir.</p>
      </div>
    </div>

    <h3 class="section-title">Tu recorrido como invitado</h3>
    <div class="section-sub">Así se ve de principio a fin.</div>
    <div class="timeline">
      <div class="titem">
        <div class="dot"></div>
        <strong>Buscar</strong>
        <p class="muted">Usa filtros inteligentes</p>
      </div>
      <div class="titem">
        <div class="dot"></div>
        <strong>Seleccionar</strong>
        <p class="muted">Lee menú y políticas</p>
      </div>
      <div class="titem">
        <div class="dot"></div>
        <strong>Reservar</strong>
        <p class="muted">Pago seguro</p>
      </div>
      <div class="titem">
        <div class="dot"></div>
        <strong>Asistir</strong>
        <p class="muted">Llega puntualmente</p>
      </div>
      <div class="titem">
        <div class="dot"></div>
        <strong>Calificar</strong>
        <p class="muted">Comparte tu opinión</p>
      </div>
    </div>
  </section>

  {{-- PARA CHEFS --}}
  <section data-section="chef" class="hidden">
    <div class="grid-4 steps">
      <div class="step">
        <div class="n">1</div>
        <h5>Crea tu perfil</h5>
        <p>Completa información, fotos y tu especialidad culinaria.</p>
      </div>
      <div class="step">
        <div class="n">2</div>
        <h5>Publica una experiencia</h5>
        <p>Define menú, precio por persona, cupos y fecha/hora.</p>
      </div>
      <div class="step">
        <div class="n">3</div>
        <h5>Gestiona reservas</h5>
        <p>Confirma y organiza la logística desde tu panel.</p>
      </div>
      <div class="step">
        <div class="n">4</div>
        <h5>Recibe pagos</h5>
        <p>Liquidez transparente según las políticas establecidas.</p>
      </div>
    </div>

    <h3 class="section-title">Flujo para Anfitriones</h3>
    <div class="section-sub">Publica, recibe invitados y crece tu comunidad.</div>
    <div class="timeline">
      <div class="titem">
        <div class="dot"></div>
        <strong>Perfil</strong>
        <p class="muted">Identidad y verificación</p>
      </div>
      <div class="titem">
        <div class="dot"></div>
        <strong>Experiencia</strong>
        <p class="muted">Menú y disponibilidad</p>
      </div>
      <div class="titem">
        <div class="dot"></div>
        <strong>Reservas</strong>
        <p class="muted">Notificaciones y control</p>
      </div>
      <div class="titem">
        <div class="dot"></div>
        <strong>Evento</strong>
        <p class="muted">Anfitriona con confianza</p>
      </div>
      <div class="titem">
        <div class="dot"></div>
        <strong>Liquidación</strong>
        <p class="muted">Ingresos en tu cuenta</p>
      </div>
    </div>
  </section>

  {{-- PAGOS Y SEGURIDAD --}}
  <h2 class="section-title">Pagos y Seguridad</h2>
  <p class="section-sub">Transparencia y soporte en cada paso.</p>
  <div class="grid-3">
    <div class="card">
      <span class="icon">
        <svg viewBox="0 0 24 24" fill="none">
          <path d="M3 7h18v10H3z" stroke="currentColor" stroke-width="1.6"/>
          <path d="M7 15h3M14 12h3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
      </span>
      <h4>Métodos de pago</h4>
      <p>Operamos con pasarelas seguras. Tus datos se procesan de forma cifrada.</p>
      <div class="kpi">
        <span class="badge">Seguro</span>
        <span class="muted">PCI-DSS / 3-D Secure</span>
      </div>
    </div>
    <div class="card">
      <span class="icon">
        <svg viewBox="0 0 24 24" fill="none">
          <path d="M4 12a8 8 0 1 0 16 0A8 8 0 0 0 4 12Z" stroke="currentColor" stroke-width="1.6"/>
          <path d="M12 8v5l3 2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
      </span>
      <h4>Política de cancelación</h4>
      <p>Las cancelaciones y reembolsos dependen de la política definida en cada experiencia.</p>
      <div class="kpi">
        <span class="badge">Claridad</span>
        <span class="muted">Se muestra antes de pagar</span>
      </div>
    </div>
    <div class="card">
      <span class="icon">
        <svg viewBox="0 0 24 24" fill="none">
          <path d="M12 3l7 4v5c0 5-3.3 7.7-7 9-3.7-1.3-7-4-7-9V7l7-4Z" stroke="currentColor" stroke-width="1.6"/>
          <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
      </span>
      <h4>Protección y soporte</h4>
      <p>Asistencia 24/7 y lineamientos de seguridad para invitados y anfitriones.</p>
      <div class="kpi">
        <span class="badge">24/7</span>
        <span class="muted">Acompañamiento continuo</span>
      </div>
    </div>
  </div>

  {{-- FAQ --}}
  <h2 class="section-title">Preguntas Frecuentes</h2>
  <section class="faq">
    <details>
      <summary>
        ¿Puedo cambiar mi reserva?
        <svg class="chev" width="18" height="18" viewBox="0 0 24 24">
          <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2"/>
        </svg>
      </summary>
      <div class="a">
        Sí, según disponibilidad del anfitrión y la política de cambios/cancelación de esa experiencia.
      </div>
    </details>
    <details>
      <summary>
        ¿Cuándo se realiza el cobro?
        <svg class="chev" width="18" height="18" viewBox="0 0 24 24">
          <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2"/>
        </svg>
      </summary>
      <div class="a">
        Al confirmar la reserva. La liquidación al anfitrión se efectúa según las condiciones acordadas.
      </div>
    </details>
    <details>
      <summary>
        ¿Qué pasa si el evento se cancela?
        <svg class="chev" width="18" height="18" viewBox="0 0 24 24">
          <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2"/>
        </svg>
      </summary>
      <div class="a">
        Te notificamos y aplicamos la política de reembolso o reprogramación correspondiente.
      </div>
    </details>
    <details>
      <summary>
        ¿Cómo reporto un problema?
        <svg class="chev" width="18" height="18" viewBox="0 0 24 24">
          <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2"/>
        </svg>
      </summary>
      <div class="a">
        Desde tu panel o vía soporte 24/7 indicando el ID de tu reserva y lo ocurrido.
      </div>
    </details>
  </section>

  {{-- CTA FINAL --}}
  <div class="cta">
    <a class="btn primary" href="{{ route('experiencias') }}">Explorar experiencias</a>
    <a class="btn ghost" href="{{ route('ser-chef') }}">Quiero ser chef anfitrión</a>
  </div>
  <div class="small">¿Necesitas ayuda adicional? Escríbenos y te acompañamos en el proceso.</div>
</div>

<script>
// Simple tab functionality
(function(){
  const tabs = document.querySelectorAll('.tab');
  const sections = {
    guest: document.querySelector('[data-section="guest"]'),
    chef:  document.querySelector('[data-section="chef"]')
  };

  function activate(which) {
    tabs.forEach(t => t.classList.toggle('active', t.dataset.flow === which));
    sections.guest.classList.toggle('hidden', which !== 'guest');
    sections.chef.classList.toggle('hidden', which !== 'chef');
  }

  tabs.forEach(t => t.addEventListener('click', () => activate(t.dataset.flow)));
})();
</script>
@endsection