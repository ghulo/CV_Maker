<template>
  <div class="page-container content-page">
    <section class="section-spacing text-center">
      <div class="container">
        <h1 class="section-title">Politika e Privatësisë</h1>
        <p class="section-subtitle">
          Kuptoni si i mbledhim, përdorim dhe mbrojmë të dhënat tuaja.
        </p>
      </div>
    </section>

    <section class="container mb-xxl">
      <div class="card p-xl">
        <h2 class="card-title mb-md">1. Informacioni që Mbledhim</h2>
        <p>Ne mbledhim informacione të ndryshme për të ofruar dhe përmirësuar shërbimin tonë për ju.</p>
        <p>Këto përfshijnë:</p>
        <ul class="list-disc list-inside mb-lg">
          <li>**Informacioni Personal:** Emri, adresa e emailit, numri i telefonit, adresa (nëse i jepni).</li>
          <li>**Informacioni i CV-së:** Të dhënat që plotësoni në CV-në tuaj (përvoja e punës, edukimi, aftësitë, etj.).</li>
          <li>**Të Dhëna Përdorimi:** Informacion rreth mënyrës si hyni dhe përdorni shërbimin.</li>
        </ul>

        <h2 class="card-title mb-md">2. Si e Përdorim Informacionin tuaj</h2>
        <p>Informacionin e mbledhur e përdorim për qëllime të ndryshme, duke përfshirë:</p>
        <ul class="list-disc list-inside mb-lg">
          <li>Për të ofruar dhe mirëmbajtur Shërbimin tonë.</li>
          <li>Për të menaxhuar llogarinë tuaj.</li>
          <li>Për t'ju kontaktuar në lidhje me Shërbimin ose ofertat.</li>
          <li>Për të përmirësuar faqen tonë të internetit.</li>
        </ul>

        <h2 class="card-title mb-md">3. Ruajtja dhe Siguria e të Dhënave</h2>
        <p>Siguria e të dhënave tuaja është e rëndësishme për ne. Ne përdorim masa sigurie standarde të industrisë për të mbrojtur të dhënat tuaja personale. Megjithatë, asnjë metodë transmetimi në internet apo metodë ruajtjeje elektronike nuk është 100% e sigurt.</p>

        <h2 class="card-title mb-md">4. Ndryshimet në këtë Politikë Privatësie</h2>
        <p>Ne mund ta azhurnojmë Politikën tonë të Privatësisë herë pas here. Ne do t'ju njoftojmë për çdo ndryshim duke postuar Politikën e re të Privatësisë në këtë faqe. Këshillohet që të rishikoni këtë Politikë Privatësie periodikisht për çdo ndryshim.</p>

        <p class="text-sm text-tertiary mt-lg">Data e fundit e azhurnimit: 24 Korrik 2024</p>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'PrivacyPolicy',
}
</script>

<style scoped>
.content-page {
  padding-top: var(--header-height-compact);
  padding-bottom: var(--space-section-vertical);
  background-color: var(--bg-secondary);
}

.card {
  max-width: 900px;
  margin: var(--space-xl) auto;
  padding: var(--space-xl);
}

.section-title {
  font-family: var(--font-heading);
  font-size: var(--font-size-4xl);
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: var(--space-md);
  line-height: var(--line-height-tight);
  letter-spacing: var(--letter-spacing-tight);
}

.section-subtitle {
  font-size: var(--font-size-lg);
  color: var(--text-secondary);
  line-height: var(--line-height-normal);
  max-width: 700px;
  margin: 0 auto var(--space-xl);
}

.card-title {
  font-size: var(--font-size-xl);
  font-weight: 600;
  color: var(--primary);
  margin-bottom: var(--space-md);
}

.card-body p {
  margin-bottom: var(--space-md);
  color: var(--text-secondary);
  line-height: var(--line-height-relaxed);
}

.card-body ul {
  margin-bottom: var(--space-lg);
  color: var(--text-secondary);
  line-height: var(--line-height-normal);
}

.card-body li {
  margin-bottom: var(--space-xs);
}

.list-disc {
  list-style-type: disc;
}

.list-inside {
  list-style-position: inside;
}

.text-sm {
  font-size: var(--font-size-sm);
}

.text-tertiary {
  color: var(--text-tertiary);
}

@media (max-width: 768px) {
  .section-title {
    font-size: var(--font-size-3xl);
  }
  .section-subtitle {
    font-size: var(--font-size-base);
  }
  .card {
    padding: var(--space-lg);
  }
  .card-title {
    font-size: var(--font-size-lg);
  }
  .card-body p, .card-body ul, .card-body li {
    font-size: var(--font-size-sm);
  }
}
</style> 