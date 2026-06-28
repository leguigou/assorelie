<!-- ===== HERO ===== -->
<section id="accueil" class="hero-photo relative overflow-hidden flex items-center">
  <div class="hero-orb hero-orb-one"></div>
  <div class="hero-orb hero-orb-two"></div>

  <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 py-24 lg:py-32 w-full">
    <div class="hero-copy">
      <div class="hero-badge">
        <span aria-hidden="true">♥</span> Association loi 1901 — Toulon
      </div>
      <h1><?= htmlspecialchars($asso['name']) ?></h1>
      <p class="hero-slogan"><?= htmlspecialchars($asso['slogan']) ?></p>
      <p class="hero-intro">
        Nous créons du lien, partageons nos savoirs, célébrons la culture
        et agissons pour l'environnement à Toulon et dans ses environs.
      </p>
      <div class="flex flex-wrap gap-4 justify-center lg:justify-start">
        <a href="#activites" class="hero-primary">
          <span aria-hidden="true">♥</span> Découvrir l'association
        </a>
        <a href="<?= htmlspecialchars($social['helloasso']) ?>" target="_blank" rel="noopener" class="hero-secondary">
          Nous rejoindre →
        </a>
      </div>
    </div>

    <div class="hero-facts" aria-label="ASSORELIE en bref">
      <div><span>●</span><strong>Ancrée à Toulon</strong><small>et ses environs</small></div>
      <div><span>●</span><strong>Ouverte à toutes</strong><small>et à tous</small></div>
      <div><span>●</span><strong>Agir ensemble</strong><small>au quotidien</small></div>
    </div>
  </div>

  <a href="#activites" class="hero-scroll-cue" aria-label="Découvrir nos activités">
    <svg class="hero-scroll-icon" aria-hidden="true" viewBox="0 0 24 24">
      <path d="m6 9 6 6 6-6"/>
    </svg>
  </a>
</section>
