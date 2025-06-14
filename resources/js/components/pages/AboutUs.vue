<template>
  <main class="main">
    <div class="page-container animate-in">
      <section class="homepage-hero about-us-hero">
        <div class="hero-bg-decoration" aria-hidden="true"></div>
        <h2 class="reveal-on-scroll">Ndërtojmë Të Ardhmen Tuaj, Një CV në Një Kohë.</h2>
        <p class="reveal-on-scroll" data-reveal-delay="100">CV Maker është krijuar me pasion për të fuqizuar individët në rrugëtimin e tyre profesional. Besojmë se një CV e mirë mund të hapë dyer dhe të krijojë mundësi.</p>
      </section>

      <section class="homepage-value-prop about-us-mission">
        <div class="value-prop-container reveal-on-scroll glassmorphic-card" data-reveal-delay="100">
          <div class="value-prop-grid">
            <div class="value-prop-headline">
              <h3 class="reveal-on-scroll" data-reveal-delay="200">Misioni dhe Vizioni Ynë.</h3>
            </div>
            <div class="value-prop-text">
              <p class="reveal-on-scroll" data-reveal-delay="300">Misioni ynë është të ofrojmë një platformë intuitive dhe të fuqishme që thjeshton procesin e krijimit të CV-ve profesionale. Ne synojmë të bëjmë dizajnin e CV-së të aksesueshëm për të gjithë, duke ju ndihmuar të prezantoni aftësitë dhe përvojën tuaj në mënyrën më të mirë të mundshme.</p>
              <p class="reveal-on-scroll" data-reveal-delay="400">Vizioni ynë është të jemi zgjedhja kryesore për individët që kërkojnë të lënë një përshtypje të fortë në tregun e punës, duke ofruar mjete inovative dhe mbështetje të vazhdueshme.</p>
            </div>
          </div>
        </div>
      </section>

      <section class="homepage-how-it-works about-us-story">
        <h3 class="reveal-on-scroll">Historia Jonë</h3>
        <p class="page-intro-text reveal-on-scroll" data-reveal-delay="100">CV Maker filloi si një projekt i vogël me një ide të madhe: të thjeshtojë krijimin e CV-ve. Kemi evoluar duke u bazuar në feedback-un e përdoruesve dhe tendencat e industrisë, gjithmonë me fokusin te ju.</p>
        <div class="how-it-works-steps">
          <div class="step-item reveal-on-scroll glassmorphic-card" data-reveal-delay="200">
            <div class="step-icon-wrapper"><i class="fas fa-lightbulb"></i></div>
            <h4>Ideja</h4>
            <p>Gjithçka nisi me një ide të thjeshtë për të ndihmuar njerëzit të krijojnë CV pa mundim.</p>
          </div>
          <div class="step-item reveal-on-scroll glassmorphic-card" data-reveal-delay="300">
            <div class="step-icon-wrapper"><i class="fas fa-code"></i></div>
            <h4>Zhvillimi</h4>
            <p>Kemi punuar me zell për të ndërtuar një platformë të fuqishme dhe të lehtë në përdorim.</p>
          </div>
          <div class="step-item reveal-on-scroll glassmorphic-card" data-reveal-delay="400">
            <div class="step-icon-wrapper"><i class="fas fa-users"></i></div>
            <h4>Komuniteti</h4>
            <p>Me mbështetjen e përdoruesve tanë, ne vazhdojmë të rritemi dhe të përmirësohemi.</p>
          </div>
        </div>
      </section>

      <section class="homepage-final-cta about-us-cta">
        <div class="cta-content">
          <h4 class="reveal-on-scroll">Bashkohuni me Komunitetin Tonë!</h4>
          <p class="reveal-on-scroll" data-reveal-delay="100">Jemi këtu për t'ju mbështetur në çdo hap të rrugëtimit tuaj profesional.</p>
          <router-link to="/contact" class="btn-create btn-large btn btn-primary reveal-on-scroll" data-reveal-delay="200">
            <i class="fas fa-envelope"></i> Na Kontaktoni
          </router-link>
        </div>
      </section>
    </div>
  </main>
</template>

<script>
export default {
  name: 'AboutUs',
}
</script>

<style scoped>
@reference "tailwindcss/theme";

/* Page-specific styles that might not be in the main app.css */
.about-us-hero {
  padding-top: 4rem;
  padding-bottom: 4rem;
}

.about-us-mission {
  padding-top: 2rem;
  padding-bottom: 2rem;
}

.about-us-story {
  padding-top: 4rem;
  padding-bottom: 4rem;
}

.about-us-cta {
  margin-top: 4rem;
  margin-bottom: 4rem;
}
</style> 