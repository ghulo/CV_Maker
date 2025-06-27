<template>
  <div class="page-container content-page">
    <section class="section-spacing text-center">
      <div class="container">
        <h1 class="section-title">Kushtet e Shërbimit</h1>
        <p class="section-subtitle">
          Këto kushte rregullojnë përdorimin tuaj të shërbimit tonë.
        </p>
      </div>
    </section>

    <section class="container mb-xxl">
      <div class="card p-xl">
        <h2 class="card-title mb-md">1. Pranimi i Kushteve</h2>
        <p>Duke hyrë ose përdorur shërbimin tonë, ju pranoni të jeni të detyruar nga këto Kushte të Shërbimit. Nëse nuk pajtoheni me ndonjë pjesë të kushteve, atëherë nuk mund të përdorni Shërbimin.</p>

        <h2 class="card-title mb-md">2. Llogaritë</h2>
        <p>Kur krijoni një llogari me ne, ju garantoni se jeni mbi 18 vjeç dhe se informacioni që na jepni është i saktë, i plotë dhe aktual në çdo kohë. Informacioni i pasaktë, i paplotë ose i vjetëruar mund të rezultojë në ndërprerjen e menjëhershme të llogarisë tuaj në Shërbimin tonë.</p>

        <h2 class="card-title mb-md">3. Përmbajtja</h2>
        <p>Shërbimi ynë ju lejon të postoni, lidhni, ruani, ndani dhe ndryshe të bëni të disponueshme informacione, tekst, grafika ose materiale të tjera (më poshtë, "Përmbajtja"). Ju jeni përgjegjës për Përmbajtjen që postoni në ose përmes Shërbimit, duke përfshirë ligjshmërinë, besueshmërinë dhe përshtatshmërinë e saj.</p>

        <h2 class="card-title mb-md">4. Ndërprerja</h2>
        <p>Ne mund të ndërpresim ose pezullojmë menjëherë aksesin në Shërbimin tonë, pa njoftim paraprak ose përgjegjësi, për çfarëdo arsye, duke përfshirë pa kufizim, nëse shkelni Kushtet.</p>

        <h2 class="card-title mb-md">5. Lidhjet me Faqet e Tjera të Internetit</h2>
        <p>Shërbimi ynë mund të përmbajë lidhje me faqe interneti ose shërbime të palëve të treta që nuk janë në pronësi ose të kontrolluara nga CV Maker. Ne nuk kemi kontroll dhe nuk marrim përgjegjësi për përmbajtjen, politikat e privatësisë ose praktikat e ndonjë faqe interneti ose shërbimi të palës së tretë.</p>

        <p class="text-sm text-tertiary mt-lg">Data e fundit e azhurnimit: 24 Korrik 2024</p>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'TermsOfService',
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