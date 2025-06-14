<template>
  <div class="homepage-content-wrapper animate-in">
    <section class="homepage-hero">
      <div class="hero-bg-decoration" aria-hidden="true"></div>
      <h2 class="reveal-on-scroll">
        Krijo CV-në që të Dallon.<br class="desktop-only" />
        Shpejt dhe Lehtë.
      </h2>
      <p class="reveal-on-scroll" data-reveal-delay="100">
        Ndërto një Curriculum Vitae profesional dhe modern në pak minuta. Zgjidh nga modelet tona të kuruara dhe prezanto aftësitë tua në mënyrën më të mirë.
      </p>
      <div class="hero-cta-container reveal-on-scroll" data-reveal-delay="200">
        <router-link to="/register" class="btn-create btn btn-primary">
          <i class="fas fa-plus-circle"></i> Fillo Krijo CV Tënde Tani
        </router-link>
      </div>
      <div class="hero-visual-element reveal-on-scroll" data-reveal-delay="300">
        <svg width="400" height="280" viewBox="0 0 400 280" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="50" y="40" width="300" height="200" rx="8" fill="currentColor" fill-opacity="0.1" />
          <rect x="75" y="70" width="250" height="15" rx="4" fill="currentColor" fill-opacity="0.2" />
          <rect x="75" y="95" width="200" height="15" rx="4" fill="currentColor" fill-opacity="0.15" />
          <rect x="75" y="130" width="250" height="40" rx="4" fill="currentColor" fill-opacity="0.25" />
          <rect x="75" y="180" width="180" height="15" rx="4" fill="currentColor" fill-opacity="0.15" />
          <rect x="75" y="205" width="220" height="15" rx="4" fill="currentColor" fill-opacity="0.2" />
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
          <p>Filloni duke zgjedhur një nga modelet tona profesionale që i përshtatet stilit tuaj.</p>
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
            <h3 class="reveal-on-scroll" data-reveal-delay="200">E Dizajnuar për Suksesin Tuaj Profesional.</h3>
          </div>
          <div class="value-prop-text">
            <p class="reveal-on-scroll" data-reveal-delay="300">
              CV Maker është krijuar me praktikat më të mira në mendje. Ne ofrojmë mjetet dhe modelet që ju ndihmojnë të prezantoni veten në mënyrën më bindëse, duke u fokusuar në qartësi, profesionalizëm dhe rezultate.
            </p>
            <p class="reveal-on-scroll" data-reveal-delay="400">
              Nga fillestarët deri te profesionistët me përvojë, platforma jonë është intuitive dhe e fuqishme, duke ju lejuar të krijoni një CV që vërtet reflekton potencialin tuaj.
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
        <p>Zgjidhni nga një koleksion i modeleve moderne dhe të dizajnuara profesionalisht që i përshtatet stilit tuaj dhe profesionit.</p>
      </div>
      <div class="feature-item reveal-on-scroll glassmorphic-card" data-reveal-delay="200">
        <div class="feature-icon-wrapper">
          <i class="fas fa-magic"></i>
        </div>
        <h3>Redaktim Intuitiv</h3>
        <p>Shtoni dhe modifikoni informacionin tuaj shpejt me një ndërfaqe të thjeshtë për t'u përdorur, pa pasur nevojë për aftësi teknike.</p>
      </div>
      <div class="feature-item reveal-on-scroll glassmorphic-card" data-reveal-delay="300">
        <div class="feature-icon-wrapper">
          <i class="fas fa-cloud-download-alt"></i>
        </div>
        <h3>Shkarko & Ndaj</h3>
        <p>Shkarkoni CV-në tuaj si PDF me cilësi të lartë, gati për ta ndarë me punëdhënësit potencialë dhe për të lënë përshtypje.</p>
      </div>
    </section>

    <section class="homepage-final-cta">
      <div class="cta-content">
        <h4 class="reveal-on-scroll">Gati për të krijuar CV-në tuaj të ardhshme?</h4>
        <p class="reveal-on-scroll" data-reveal-delay="100">Bashkohuni me mijëra përdorues që kanë krijuar CV mbresëlënëse me CV Maker.</p>
        <router-link to="/signup" class="btn-create btn-large btn-primary reveal-on-scroll" data-reveal-delay="200">
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
  }
};
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
</style> 