@extends('layouts.app')

@section('title', 'Ser Chef Anfitrión')

@section('content')
<style>
:root {
  --primary: #0C3558;
  --primary-light: #1e4a73;
  --primary-gradient: linear-gradient(135deg, #0C3558 0%, #1e4a73 100%);
  --muted: #6b7280;
  --bg: #f8fafc;
  --card: #ffffff;
  --border: #e5e7eb;
  --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
  --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
  --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
  --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
}

* {
  transition: all 0.3s ease;
}

body {
  background: linear-gradient(180deg, #f8fafc 0%, #eef6ff 100%);
  min-height: 100vh;
}

.container-xl {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

/* HERO SECTION */
.hero {
  background: linear-gradient(135deg, #eef6ff 0%, #dbeafe 100%);
  border-radius: 24px;
  padding: 4rem 2rem;
  text-align: center;
  position: relative;
  overflow: hidden;
  margin-bottom: 5rem;
  box-shadow: var(--shadow-xl);
}

.hero::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(12, 53, 88, 0.05) 0%, transparent 70%);
  animation: float 6s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { transform: translate(0px, 0px) rotate(0deg); }
  33% { transform: translate(30px, -30px) rotate(120deg); }
  66% { transform: translate(-20px, 20px) rotate(240deg); }
}

.hero-content {
  position: relative;
  z-index: 2;
}

.hero h1 {
  font-size: 3.5rem;
  font-weight: 900;
  margin: 0 0 1rem;
  background: var(--primary-gradient);
  background-clip: text;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  line-height: 1.2;
}

.hero p {
  color: var(--muted);
  font-size: 1.25rem;
  max-width: 600px;
  margin: 0 auto 2rem;
  line-height: 1.6;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: var(--primary-gradient);
  color: white;
  font-weight: 700;
  font-size: 1.1rem;
  border: none;
  border-radius: 16px;
  padding: 1rem 2rem;
  text-decoration: none;
  box-shadow: var(--shadow-lg);
  position: relative;
  overflow: hidden;
  transform: translateY(0);
}

.btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
  transition: left 0.5s;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-xl);
}

.btn:hover::before {
  left: 100%;
}

.btn.secondary {
  background: white;
  color: var(--primary);
  border: 2px solid var(--primary);
  box-shadow: var(--shadow-md);
}

.btn.secondary:hover {
  background: var(--primary);
  color: white;
}

/* SECTION TITLES */
.section-title {
  font-size: 2.5rem;
  font-weight: 900;
  text-align: center;
  margin: 4rem 0 3rem;
  color: var(--primary);
  position: relative;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 80px;
  height: 4px;
  background: var(--primary-gradient);
  border-radius: 2px;
}

/* GRIDS */
.grid-4 {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  margin-bottom: 4rem;
}

.grid-3 {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  margin-bottom: 4rem;
}

/* CARDS */
.card {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 2rem;
  box-shadow: var(--shadow-md);
  position: relative;
  overflow: hidden;
  transform: translateY(0);
}

.card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: var(--primary-gradient);
  transform: scaleX(0);
  transition: transform 0.3s ease;
}

.card:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-xl);
  border-color: rgba(12, 53, 88, 0.2);
}

.card:hover::before {
  transform: scaleX(1);
}

.icon {
  width: 60px;
  height: 60px;
  border-radius: 20px;
  background: linear-gradient(135deg, #e8f0ff 0%, #dbeafe 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: var(--primary);
  margin-bottom: 1.5rem;
  box-shadow: var(--shadow-sm);
}

.card h4 {
  margin: 0 0 1rem;
  font-size: 1.25rem;
  font-weight: 800;
  color: var(--primary);
}

.card p {
  margin: 0;
  color: var(--muted);
  line-height: 1.6;
}

/* STEPS */
.steps {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  margin-bottom: 4rem;
}

.step {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 2rem;
  box-shadow: var(--shadow-md);
  text-align: center;
  position: relative;
}

.step:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
}

.step .n {
  width: 50px;
  height: 50px;
  border-radius: 16px;
  background: var(--primary-gradient);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 900;
  font-size: 1.25rem;
  margin: 0 auto 1.5rem;
  box-shadow: var(--shadow-md);
}

.step h5 {
  margin: 0 0 1rem;
  font-size: 1.125rem;
  font-weight: 800;
  color: var(--primary);
}

.step p {
  margin: 0;
  color: var(--muted);
  line-height: 1.6;
}

/* STORIES */
.story {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 2rem;
  text-align: center;
  box-shadow: var(--shadow-md);
  position: relative;
  overflow: hidden;
}

.story::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -50%;
  width: 100px;
  height: 100px;
  background: linear-gradient(135deg, rgba(12, 53, 88, 0.05), transparent);
  border-radius: 50%;
  animation: pulse 4s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% { transform: scale(1) rotate(0deg); opacity: 0.5; }
  50% { transform: scale(1.2) rotate(180deg); opacity: 0.8; }
}

.story:hover {
  transform: translateY(-6px);
  box-shadow: var(--shadow-xl);
}

.avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: var(--primary-gradient);
  color: white;
  margin: 0 auto 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 900;
  font-size: 1.5rem;
  box-shadow: var(--shadow-lg);
  position: relative;
  z-index: 2;
}

.story h6 {
  margin: 0 0 0.5rem;
  font-size: 1.25rem;
  font-weight: 800;
  color: var(--primary);
}

.story .loc {
  color: var(--muted);
  font-size: 0.875rem;
  margin-bottom: 0.5rem;
}

.story .earn {
  color: #059669;
  font-weight: 800;
  font-size: 1.125rem;
  margin-bottom: 1rem;
  background: linear-gradient(135deg, #d1fae5, #a7f3d0);
  padding: 0.5rem 1rem;
  border-radius: 12px;
  display: inline-block;
}

.story p {
  color: var(--muted);
  font-style: italic;
  margin: 0;
  line-height: 1.6;
}

/* FAQ */
.faq {
  max-width: 800px;
  margin: 2rem auto 4rem;
}

.faq-item {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: 16px;
  margin-bottom: 1rem;
  box-shadow: var(--shadow-sm);
  overflow: hidden;
}

.faq-item:hover {
  box-shadow: var(--shadow-md);
}

.faq-q {
  width: 100%;
  text-align: left;
  background: transparent;
  border: none;
  padding: 1.5rem 2rem;
  font-weight: 700;
  font-size: 1.125rem;
  cursor: pointer;
  color: var(--primary);
  position: relative;
}

.faq-q::after {
  content: '+';
  position: absolute;
  right: 2rem;
  top: 50%;
  transform: translateY(-50%);
  font-size: 1.5rem;
  font-weight: 300;
  transition: transform 0.3s ease;
}

.faq-item.open .faq-q::after {
  transform: translateY(-50%) rotate(45deg);
}

.faq-a {
  display: none;
  padding: 0 2rem 1.5rem;
  color: var(--muted);
  line-height: 1.6;
  border-top: 1px solid var(--border);
  margin-top: -1px;
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.faq-item.open .faq-a {
  display: block;
}

/* CTA BOTTOM */
.cta-bottom {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin: 3rem 0 2rem;
  flex-wrap: wrap;
}

/* RESPONSIVE */
@media (max-width: 768px) {
  .hero h1 { font-size: 2.5rem; }
  .hero p { font-size: 1.1rem; }
  .hero { padding: 3rem 1.5rem; }
  .section-title { font-size: 2rem; }
  .container-xl { padding: 1rem; }
  .card, .step, .story { padding: 1.5rem; }
}

/* LOADING ANIMATION */
.fade-in {
  animation: fadeInUp 0.6s ease forwards;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>

<div class="container-xl">
  {{-- HERO --}}
  <section class="hero fade-in">
    <div class="hero-content">
      <h1>Conviértete en Chef Anfitrión</h1>
      <p>Comparte tu pasión por la cocina, conoce gente nueva y gana dinero extra ofreciendo experiencias gastronómicas únicas en tu hogar.</p>
      <a class="btn" href="{{ auth()->check() ? route('chef.dashboard') : (Route::has('register') ? route('register') : '#') }}">
        <span>✨</span> Comenzar Ahora
      </a>
    </div>
  </section>

  {{-- BENEFICIOS --}}
  <h2 class="section-title">¿Por qué ser Chef Anfitrión?</h2>
  <section class="grid-4">
    <article class="card fade-in" style="animation-delay: 0.1s">
      <div class="icon">💰</div>
      <h4>Gana dinero extra</h4>
      <p>Los chefs anfitriones ganan en promedio $70.000–$170.000 por experiencia, dependiendo de la ciudad y demanda del mercado.</p>
    </article>
    <article class="card fade-in" style="animation-delay: 0.2s">
      <div class="icon">🤝</div>
      <h4>Conoce gente nueva</h4>
      <p>Conecta con personas de todo el mundo que comparten tu pasión por la gastronomía y crea vínculos únicos.</p>
    </article>
    <article class="card fade-in" style="animation-delay: 0.3s">
      <div class="icon">⭐</div>
      <h4>Comparte tu talento</h4>
      <p>Enseña tus recetas favoritas, técnicas culinarias únicas y transmite tu amor por la cocina a otros.</p>
    </article>
    <article class="card fade-in" style="animation-delay: 0.4s">
      <div class="icon">🛡️</div>
      <h4>Protección total</h4>
      <p>Disfruta de soporte 24/7 y pólizas integrales que te respaldan durante cada experiencia gastronómica.</p>
    </article>
  </section>

  {{-- PASOS --}}
  <h2 class="section-title" id="apply">Solicitud para Chef Anfitrión</h2>
  <p style="text-align:center;color:var(--muted);margin:-2rem 0 3rem;font-size:1.125rem;">Completa tu perfil en 4 pasos sencillos</p>
  <section class="steps">
    <article class="step fade-in" style="animation-delay: 0.1s">
      <div class="n">1</div>
      <h5>Información Personal</h5>
      <p>Cuéntanos sobre ti, tu experiencia culinaria y tu pasión por la gastronomía.</p>
    </article>
    <article class="step fade-in" style="animation-delay: 0.2s">
      <div class="n">2</div>
      <h5>Tu Espacio</h5>
      <p>Describe tu cocina equipada y el ambiente acogedor para recibir a tus invitados.</p>
    </article>
    <article class="step fade-in" style="animation-delay: 0.3s">
      <div class="n">3</div>
      <h5>Experiencia Culinaria</h5>
      <p>Detalla tu menú especial y el tipo de experiencia gastronómica única que ofrecerás.</p>
    </article>
    <article class="step fade-in" style="animation-delay: 0.4s">
      <div class="n">4</div>
      <h5>Precios y Disponibilidad</h5>
      <p>Establece precios competitivos por persona y define tus horarios disponibles.</p>
    </article>
  </section>

  {{-- HISTORIAS DE ÉXITO --}}
  <h2 class="section-title">Historias de Éxito</h2>
  <section class="grid-3">
    <article class="story fade-in" style="animation-delay: 0.1s">
      <div class="avatar">MG</div>
      <h6>María González</h6>
      <div class="loc">📍 Palermo</div>
      <div class="earn">$400.000/mes</div>
      <p>"TuMesa me permitió convertir mi amor por la cocina española en una experiencia increíble, conociendo personas maravillosas de todo el mundo."</p>
    </article>
    <article class="story fade-in" style="animation-delay: 0.2s">
      <div class="avatar">GR</div>
      <h6>Giuseppe Romano</h6>
      <div class="loc">📍 San Telmo</div>
      <div class="earn">$270.000/mes</div>
      <p>"Como chef italiano, puedo compartir las recetas auténticas de mi nonna y crear momentos únicos mientras genero ingresos adicionales."</p>
    </article>
    <article class="story fade-in" style="animation-delay: 0.3s">
      <div class="avatar">AM</div>
      <h6>Ana Martín</h6>
      <div class="loc">📍 Recoleta</div>
      <div class="earn">$200.000/mes</div>
      <p>"Lo que comenzó como un hobby se convirtió en mi segunda fuente de ingresos. Cada experiencia culinaria es mágica y gratificante."</p>
    </article>
  </section>

  {{-- FAQ --}}
  <h2 class="section-title">Preguntas Frecuentes</h2>
  <section class="faq">
    <div class="faq-item">
      <button class="faq-q" type="button">¿Cuánto puedo ganar como chef anfitrión?</button>
      <div class="faq-a">
        Los ingresos varían según tu ubicación, estrategia de precios y frecuencia de eventos. La mayoría de nuestros chefs ganan entre $70.000 y $170.000 por experiencia, con algunos superando estas cifras en ubicaciones premium.
      </div>
    </div>
    <div class="faq-item">
      <button class="faq-q" type="button">¿Qué requisitos necesito para comenzar?</button>
      <div class="faq-a">
        Necesitas ser mayor de edad, contar con un espacio limpio y seguro, cumplir con normas básicas de higiene alimentaria, y completar nuestro proceso de verificación de identidad y antecedentes.
      </div>
    </div>
    <div class="faq-item">
      <button class="faq-q" type="button">¿Cómo manejo las reservas y pagos?</button>
      <div class="faq-a">
        Todo se gestiona desde tu panel de control personalizado. Las reservas se confirman automáticamente y los pagos se procesan de forma segura, liquidándose según la política de pagos que establecemos juntos.
      </div>
    </div>
    <div class="faq-item">
      <button class="faq-q" type="button">¿Qué incluye la protección ofrecida?</button>
      <div class="faq-a">
        Ofrecemos cobertura integral para daños accidentales, asistencia 24/7 durante los eventos, y soporte completo para cualquier situación imprevista. Los detalles específicos se proporcionan al activar tu perfil de chef.
      </div>
    </div>
  </section>

  {{-- CTA final --}}
  <div class="cta-bottom">
    <a class="btn" href="{{ auth()->check() ? route('chef.dashboard') : (Route::has('register') ? route('register') : '#') }}">
      <span>🍳</span> Crear mi perfil de Chef
    </a>
    <a class="btn secondary" href="{{ route('experiencias') }}">
      <span>👀</span> Ver experiencias
    </a>
  </div>
</div>

<script>
// FAQ toggle with smooth animations
document.querySelectorAll('.faq-item .faq-q').forEach(function(btn){
  btn.addEventListener('click', function(){
    var item = btn.parentElement;
    var isOpen = item.classList.contains('open');
    
    // Close all other FAQs
    document.querySelectorAll('.faq-item.open').forEach(function(openItem){
      if(openItem !== item) {
        openItem.classList.remove('open');
      }
    });
    
    // Toggle current FAQ
    item.classList.toggle('open');
  });
});

// Intersection Observer for fade-in animations
const observerOptions = {
  threshold: 0.1,
  rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(function(entries) {
  entries.forEach(function(entry) {
    if (entry.isIntersecting) {
      entry.target.classList.add('fade-in');
    }
  });
}, observerOptions);

// Observe all cards, steps, and stories
document.querySelectorAll('.card, .step, .story').forEach(function(el) {
  observer.observe(el);
});

// Add smooth scroll behavior
document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
  anchor.addEventListener('click', function(e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if (target) {
      target.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    }
  });
});
</script>
@endsection