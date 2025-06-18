<template>
  <div class="homepage-content-wrapper animate-in">
    <section class="homepage-hero">
      <div class="hero-bg-decoration" aria-hidden="true"></div>
      <h2 class="reveal-on-scroll">
        Krijo CV-në që të Dallon.<br class="desktop-only" />
        Shpejt dhe Lehtë.
      </h2>
      <p class="reveal-on-scroll" data-reveal-delay="100">
        Ndërto një Curriculum Vitae profesional dhe modern në pak minuta. Zgjidh nga modelet tona të
        kuruara dhe prezanto aftësitë tua në mënyrën më të mirë.
      </p>
      <div class="hero-cta-container reveal-on-scroll" data-reveal-delay="200">
        <router-link to="/register" class="btn-create btn btn-primary">
          <i class="fas fa-plus-circle"></i> Fillo Krijo CV Tënde Tani
        </router-link>
      </div>
      <div class="hero-visual-element reveal-on-scroll" data-reveal-delay="300">
        <svg
          width="400"
          height="280"
          viewBox="0 0 400 280"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <rect
            x="50"
            y="40"
            width="300"
            height="200"
            rx="8"
            fill="currentColor"
            fill-opacity="0.1"
          />
          <rect
            x="75"
            y="70"
            width="250"
            height="15"
            rx="4"
            fill="currentColor"
            fill-opacity="0.2"
          />
          <rect
            x="75"
            y="95"
            width="200"
            height="15"
            rx="4"
            fill="currentColor"
            fill-opacity="0.15"
          />
          <rect
            x="75"
            y="130"
            width="250"
            height="40"
            rx="4"
            fill="currentColor"
            fill-opacity="0.25"
          />
          <rect
            x="75"
            y="180"
            width="180"
            height="15"
            rx="4"
            fill="currentColor"
            fill-opacity="0.15"
          />
          <rect
            x="75"
            y="205"
            width="220"
            height="15"
            rx="4"
            fill="currentColor"
            fill-opacity="0.2"
          />
        </svg>
      </div>
    </section>

    <section class="homepage-how-it-works">
      <h3 class="reveal-on-scroll">Si Funksionon?</h3>
      <p class="page-intro-text reveal-on-scroll" data-reveal-delay="100">
        Krijimi i CV-së tuaj të ardhshme është i thjeshtë. Ndiqni këto hapa:
      </p>
      <div class="how-it-works-steps">
        <div class="step-item reveal-on-scroll glassmorphic-card" data-reveal-delay="200">
          <div class="step-icon-wrapper"><i class="fas fa-file-alt"></i></div>
          <h4>1. Zgjidh Modelin</h4>
          <p>
            Filloni duke zgjedhur një nga modelet tona profesionale që i përshtatet stilit tuaj.
          </p>
        </div>
        <div class="step-item reveal-on-scroll glassmorphic-card" data-reveal-delay="300">
          <div class="step-icon-wrapper"><i class="fas fa-keyboard"></i></div>
          <h4>2. Plotëso Detajet</h4>
          <p>Shtoni informacionin tuaj personal, eksperiencën e punës, edukimin dhe aftësitë.</p>
        </div>
        <div class="step-item reveal-on-scroll glassmorphic-card" data-reveal-delay="400">
          <div class="step-icon-wrapper"><i class="fas fa-download"></i></div>
          <h4>3. Shkarko CV-në</h4>
          <p>Parapamje dhe shkarkoni CV-në tuaj si një dokument PDF me cilësi të lartë.</p>
        </div>
      </div>
    </section>

    <section class="homepage-value-prop">
      <div class="value-prop-container reveal-on-scroll glassmorphic-card" data-reveal-delay="100">
        <div class="value-prop-grid">
          <div class="value-prop-headline">
            <h3 class="reveal-on-scroll" data-reveal-delay="200">
              E Dizajnuar për Suksesin Tuaj Profesional.
            </h3>
          </div>
          <div class="value-prop-text">
            <p class="reveal-on-scroll" data-reveal-delay="300">
              CV Maker është krijuar me praktikat më të mira në mendje. Ne ofrojmë mjetet dhe
              modelet që ju ndihmojnë të prezantoni veten në mënyrën më bindëse, duke u fokusuar në
              qartësi, profesionalizëm dhe rezultate.
            </p>
            <p class="reveal-on-scroll" data-reveal-delay="400">
              Nga fillestarët deri te profesionistët me përvojë, platforma jonë është intuitive dhe
              e fuqishme, duke ju lejuar të krijoni një CV që vërtet reflekton potencialin tuaj.
            </p>
          </div>
        </div>
      </div>
    </section>

    <section class="homepage-features" id="features">
      <div class="feature-item reveal-on-scroll glassmorphic-card" data-reveal-delay="100">
        <div class="feature-icon-wrapper">
          <i class="fas fa-file-alt"></i>
        </div>
        <h3>Modele Profesionale</h3>
        <p>
          Zgjidhni nga një koleksion i modeleve moderne dhe të dizajnuara profesionalisht që i
          përshtatet stilit tuaj dhe profesionit.
        </p>
      </div>
      <div class="feature-item reveal-on-scroll glassmorphic-card" data-reveal-delay="200">
        <div class="feature-icon-wrapper">
          <i class="fas fa-magic"></i>
        </div>
        <h3>Redaktim Intuitiv</h3>
        <p>
          Shtoni dhe modifikoni informacionin tuaj shpejt me një ndërfaqe të thjeshtë për t'u
          përdorur, pa pasur nevojë për aftësi teknike.
        </p>
      </div>
      <div class="feature-item reveal-on-scroll glassmorphic-card" data-reveal-delay="300">
        <div class="feature-icon-wrapper">
          <i class="fas fa-cloud-download-alt"></i>
        </div>
        <h3>Shkarko & Ndaj</h3>
        <p>
          Shkarkoni CV-në tuaj si PDF me cilësi të lartë, gati për ta ndarë me punëdhënësit
          potencialë dhe për të lënë përshtypje.
        </p>
      </div>
    </section>

    <section class="homepage-final-cta">
      <div class="cta-content">
        <h4 class="reveal-on-scroll">Gati për të krijuar CV-në tuaj të ardhshme?</h4>
        <p class="reveal-on-scroll" data-reveal-delay="100">
          Bashkohuni me mijëra përdorues që kanë krijuar CV mbresëlënëse me CV Maker.
        </p>
        <router-link
          to="/signup"
          class="btn-create btn-large btn-primary reveal-on-scroll"
          data-reveal-delay="200"
        >
          <i class="fas fa-rocket"></i> Fillo Tani, Është Falas!
        </router-link>
      </div>
    </section>
  </div>
</template>

<script>
  export default {
    name: 'Homepage',
    setup() {
      // Component-specific logic can go here
    },
  }
</script>

<style scoped>
  @reference "tailwindcss/theme";

  .desktop-only {
    display: none;
  }

  @media (min-width: 768px) {
    .desktop-only {
      display: inline;
    }
  }

  /* Homepage Specific Styles */
  .homepage-content-wrapper {
      padding-top: 0;
      padding-bottom: var(--space-xxl);
      background: transparent;
      border: none;
      box-shadow: none;
      max-width: 1200px;
      margin: var(--space-xl) auto;
      position: relative;
      z-index: 2;
      opacity: 1 !important;
      transform: none !important;
  }

  .homepage-hero {
      text-align: center;
      padding: var(--space-hero-padding-top) var(--space-lg) var(--space-hero-padding-bottom) var(--space-lg);
      margin-bottom: var(--space-section-vertical);
      position: relative;
      overflow: hidden;
  }

  .hero-bg-decoration {
      position: absolute;
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      width: 100%; max-width: 1400px;
      height: 1000px;
      background: radial-gradient(ellipse at center, rgba(var(--primary-rgb), 0.07) 0%, transparent 70%);
      border-radius: 50%;
      z-index: -1;
      opacity: 0.9;
      filter: blur(100px);
       animation: pulse-glow 12s infinite alternate ease-in-out;
  }
  body.dark-theme .hero-bg-decoration {
      background: radial-gradient(ellipse at center, rgba(var(--dark-primary-rgb), 0.15) 0%, transparent 70%);
      opacity: 0.8;
  }

  @keyframes pulse-glow {
      0% { transform: translate(-50%, -50%) scale(1); opacity: 0.9; }
      100% { transform: translate(-50%, -50%) scale(1.1) rotate(2deg); opacity: 0.8; }
  }


  .homepage-hero h2 {
      font-size: clamp(3em, 7.5vw, 5em);
      font-weight: 800;
      margin-bottom: var(--space-lg);
      letter-spacing: -0.06em;
      color: var(--neutral-text);
      line-height: 1.0;
  }
  .homepage-hero h2::after { display: none; }

  body.dark-theme .homepage-hero h2 { color: var(--dark-neutral-text); }

  .homepage-hero p {
      font-size: clamp(1.15em, 3vw, 1.5em);
      color: var(--muted-text);
      margin-bottom: var(--space-xl);
      max-width: 750px;
      margin-left: auto; margin-right: auto;
      line-height: 1.7;
  }
   body.dark-theme .homepage-hero p { color: var(--dark-muted-text); }

  .hero-cta-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: var(--space-md);
      margin-top: var(--space-xl);
  }
  @media (min-width: 768px) { .hero-cta-container { flex-direction: row; justify-content: center; } }

  .homepage-hero .btn-create {
      padding: calc(var(--space-md) + 5px) calc(var(--space-xl) + 10px);
      font-size: clamp(1.15em, 2.6vw, 1.3em);
      width: auto;
      min-width: 280px;
      box-shadow: 0 6px 15px rgba(var(--primary-rgb), 0.28), 0 2px 5px rgba(0,0,0,0.08);
      transition: all 0.4s var(--animation-ease-out);
  }
  body.dark-theme .homepage-hero .btn-create {
      box-shadow: 0 6px 15px rgba(var(--dark-primary-rgb), 0.35), 0 2px 5px rgba(0,0,0,0.15);
  }
  body.dark-theme .homepage-hero .btn-create:hover {
      box-shadow: 0 9px 22px rgba(var(--primary-rgb), 0.35), 0 5px 10px rgba(0,0,0,0.1);
  }
  body.dark-theme .homepage-hero .btn-create:hover {
      box-shadow: 0 9px 22px rgba(var(--dark-primary-rgb), 0.4), 0 5px 10px rgba(0,0,0,0.2);
  }


  .hero-visual-element {
      margin-top: calc(var(--space-xxl) + var(--space-sm));
      opacity: 1;
      color: var(--neutral-border);
      padding: var(--space-md);
      background-color: rgba(var(--primary-rgb), 0.01);
      border-radius: var(--radius);
      border: 1px solid rgba(var(--primary-rgb), 0.03);
      max-width: 550px;
      margin-left: auto;
      margin-right: auto;
  }
  body.dark-theme .hero-visual-element {
      color: var(--dark-neutral-border);
      background-color: rgba(var(--dark-primary-rgb), 0.02);
      border: 1px solid rgba(var(--dark-primary-rgb), 0.06);
  }
  .hero-visual-element svg {
      display: block; margin: 0 auto; max-width: 100%;
  }

  /* "How it Works" Section */
  .homepage-how-it-works {
      padding: var(--space-section-vertical) var(--space-lg);
      margin-bottom: var(--space-section-vertical);
      text-align: center;
  }
  .homepage-how-it-works h3 {
      font-size: clamp(2em, 4.5vw, 3em);
      font-weight: 700;
      color: var(--neutral-text);
      line-height: 1.2;
      letter-spacing: -0.035em;
      margin-bottom: var(--space-xl);
  }
  body.dark-theme .homepage-how-it-works h3 { color: var(--dark-neutral-text); }

  .how-it-works-steps {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: var(--space-xl);
      margin-top: var(--space-lg);
  }

  .step-item {
      background: var(--neutral-light);
      border: 1px solid var(--neutral-border);
      border-radius: var(--radius);
      padding: var(--space-xl);
      text-align: center;
      box-shadow: var(--shadow-light);
      transition: all 0.4s var(--animation-ease-out);
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
      overflow: hidden;
      /* For tilt effect */
      transform-style: preserve-3d;
      transform: perspective(1000px) rotateX(var(--rotate-x, 0deg)) rotateY(var(--rotate-y, 0deg)) scale(var(--scale, 1));
  }
  body.dark-theme .step-item {
      background: var(--dark-neutral-light);
      border-color: var(--dark-neutral-border);
      box-shadow: 0 2px 6px rgba(0,0,0,0.2), 0 1px 3px rgba(0,0,0,0.15);
  }
  .step-item:hover {
       transform: translateY(-10px) scale(1.03);
       box-shadow: var(--shadow-hover);
       border-color: var(--primary);
       /* Add glow on hover */
       box-shadow: var(--shadow-hover), 0 0 var(--card-hover-glow-spread) rgba(var(--primary-rgb), 0.3);
  }
  body.dark-theme .step-item:hover {
      border-color: var(--dark-primary);
      box-shadow: 0 4px 10px rgba(0,0,0,0.25), 0 2px 5px rgba(0,0,0,0.15), 0 0 var(--card-hover-glow-spread) rgba(var(--dark-primary-rgb), 0.4);
  }


  .step-icon-wrapper {
      width: 65px;
      height: 65px;
      border-radius: 50%;
      background-color: rgba(var(--primary-rgb), 0.07);
      display: inline-flex;
      align-items: center; justify-content: center;
      margin-bottom: var(--space-md);
      transition: background-color 0.2s ease, transform 0.4s ease;
  }
  body.dark-theme .step-icon-wrapper { background-color: rgba(var(--dark-primary-rgb), 0.1); }
  .step-item:hover .step-icon-wrapper {
      transform: rotate(15deg) scale(1.15);
  }


  .step-item i {
      font-size: 2em;
      color: var(--primary);
  }
  body.dark-theme .step-item i { color: var(--dark-primary); }

  .step-item h4 {
      font-size: 1.25em;
      font-weight: 600;
      color: var(--neutral-text);
      margin-bottom: var(--space-sm);
      line-height: 1.3;
  }
  body.dark-theme .step-item h4 { color: var(--dark-neutral-text); }

  .step-item p {
      font-size: 1em;
      color: var(--muted-text);
      line-height: 1.6;
      flex-grow: 1;
  }
  body.dark-theme .step-item p { color: var(--dark-muted-text); }

  @media (min-width: 768px) {
      .how-it-works-steps {
          position: relative;
          padding-bottom: 30px;
      }
      .how-it-works-steps::before {
          content: '';
          position: absolute;
          bottom: 0;
          left: 50%;
          transform: translateX(-50%);
          width: calc(100% - var(--space-xl) * 2);
          height: 2px;
          background-color: var(--divider-color);
          z-index: 0;
      }
      body.dark-theme .how-it-works-steps::before { background-color: var(--dark-divider-color); }

      .step-item {
          padding-bottom: calc(var(--space-xl) + 30px);
      }
      .step-item::after {
          content: '';
          position: absolute;
          bottom: 18px;
          left: 50%;
          transform: translateX(-50%);
          width: 12px;
          height: 12px;
          background-color: var(--primary);
          border-radius: 50%;
          border: 2px solid var(--neutral-light);
          z-index: 1;
      }
      body.dark-theme .step-item::after {
          background-color: var(--dark-primary);
          border-color: var(--dark-neutral-light);
      }
  }

  /* Value Prop Section */
  .homepage-value-prop {
      padding: var(--space-section-vertical) var(--space-lg);
      margin-bottom: var(--space-section-vertical);
  }
  .value-prop-container {
      padding: var(--space-xxl) var(--space-lg);
      max-width: 900px;
      margin: 0 auto;
  }
  body.dark-theme .value-prop-container {
      background-color: var(--dark-neutral-container);
  }
  .value-prop-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: var(--space-xl);
  }
  @media (min-width: 768px) {
      .value-prop-grid {
          grid-template-columns: 1fr 1.5fr; /* Headline smaller, text larger */
          align-items: center;
      }
  }

  .value-prop-headline h3 {
      font-size: clamp(2em, 4.5vw, 3em);
      font-weight: 700;
      color: var(--neutral-text);
      line-height: 1.2;
      letter-spacing: -0.035em;
      margin-bottom: var(--space-md);
  }
  body.dark-theme .value-prop-headline h3 { color: var(--dark-neutral-text); }

  .value-prop-text p {
      font-size: clamp(1em, 2vw, 1.15em);
      color: var(--muted-text);
      line-height: 1.7;
  }
  .value-prop-text p:last-child {
      margin-bottom: 0;
  }
  body.dark-theme .value-prop-text p { color: var(--dark-muted-text); }

  /* Features Section */
  .homepage-features {
      padding: var(--space-section-vertical) var(--space-lg);
      margin-bottom: var(--space-section-vertical);
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: var(--space-xl);
      max-width: 1200px;
      margin-left: auto;
      margin-right: auto;
  }
  .feature-item {
      background: var(--neutral-light);
      border: 1px solid var(--neutral-border);
      border-radius: var(--radius);
      padding: var(--space-xl);
      text-align: center;
      box-shadow: var(--shadow-light);
      transition: all 0.4s var(--animation-ease-out);
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
      overflow: hidden;
      /* For tilt effect */
      transform-style: preserve-3d;
      transform: perspective(1000px) rotateX(var(--rotate-x, 0deg)) rotateY(var(--rotate-y, 0deg)) scale(var(--scale, 1));
  }
  body.dark-theme .feature-item {
      background: var(--dark-neutral-light);
      border-color: var(--dark-neutral-border);
      box-shadow: 0 2px 6px rgba(0,0,0,0.2), 0 1px 3px rgba(0,0,0,0.15);
  }
  .feature-item:hover {
      transform: translateY(-10px) scale(1.03);
      box-shadow: var(--shadow-hover);
      border-color: var(--primary);
      /* Add glow on hover */
      box-shadow: var(--shadow-hover), 0 0 var(--card-hover-glow-spread) rgba(var(--primary-rgb), 0.3);
  }
  body.dark-theme .feature-item:hover {
      border-color: var(--dark-primary);
      box-shadow: 0 4px 10px rgba(0,0,0,0.25), 0 2px 5px rgba(0,0,0,0.15), 0 0 var(--card-hover-glow-spread) rgba(var(--dark-primary-rgb), 0.4);
  }

  .feature-icon-wrapper {
      width: 55px;
      height: 55px;
      border-radius: 50%;
      background-color: rgba(var(--primary-rgb), 0.07);
      display: inline-flex;
      align-items: center; justify-content: center;
      margin-bottom: var(--space-md);
      transition: background-color 0.2s ease, transform 0.4s ease;
  }
  body.dark-theme .feature-icon-wrapper { background-color: rgba(var(--dark-primary-rgb), 0.1); }
  .feature-item:hover .feature-icon-wrapper {
      transform: rotate(15deg) scale(1.15);
  }

  .feature-item i {
      font-size: 1.8em;
      color: var(--primary);
  }
  body.dark-theme .feature-item i { color: var(--dark-primary); }

  .feature-item h3 {
      font-size: 1.2em;
      font-weight: 600;
      color: var(--neutral-text);
      margin-bottom: var(--space-sm);
      line-height: 1.3;
  }
  body.dark-theme .feature-item h3 { color: var(--dark-neutral-text); }

  .feature-item p {
      font-size: 0.95em;
      color: var(--muted-text);
      line-height: 1.6;
      flex-grow: 1;
  }
  body.dark-theme .feature-item p { color: var(--dark-muted-text); }

  /* Final Call to Action */
  .homepage-final-cta {
      padding: var(--space-section-vertical) var(--space-lg);
      text-align: center;
      margin-bottom: var(--space-xxl);
  }
  body.dark-theme .homepage-final-cta {
      background: radial-gradient(ellipse at center, rgba(var(--dark-primary-rgb), 0.05) 0%, transparent 70%);
  }
  .cta-content {
      max-width: 800px;
      margin: 0 auto;
  }
  .homepage-final-cta h4 {
      font-size: clamp(2em, 4vw, 2.8em);
      font-weight: 700;
      color: var(--neutral-text);
      line-height: 1.2;
      letter-spacing: -0.035em;
      margin-bottom: var(--space-md);
  }
  body.dark-theme .homepage-final-cta h4 { color: var(--dark-neutral-text); }

  .homepage-final-cta p {
      font-size: clamp(1em, 2vw, 1.15em);
      color: var(--muted-text);
      margin-bottom: var(--space-xl);
      line-height: 1.7;
  }
  body.dark-theme .homepage-final-cta p { color: var(--dark-muted-text); }

  .homepage-final-cta .btn-large {
      padding: var(--space-md) var(--space-xl);
      font-size: clamp(1em, 2.2vw, 1.15em);
      min-width: 250px;
  }
</style>
