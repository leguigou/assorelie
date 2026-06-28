<?php $displayCreationDate = str_replace('/', '.', $asso['creation_date']); ?>

<!-- ===== L'ESPRIT ASSORELIE ===== -->
<section id="a-propos" class="story-section">
  <div class="max-w-7xl mx-auto px-6 lg:px-8">
    <div class="story-layout">
      <div class="story-visual reveal">
        <div class="story-photo-card">
          <img src="assets/images/atelier-creatif.webp"
               alt="Des participants de toutes générations réunis lors d'un atelier ASSORELIE"
               width="1280" height="853" loading="lazy">
        </div>

        <div class="story-date-card">
          <strong><?= htmlspecialchars($displayCreationDate) ?></strong>
          <span>Date de création<br>de l'asso</span>
        </div>
      </div>

      <div class="story-content reveal">
        <span class="story-eyebrow">L'esprit ASSORELIE</span>
        <h2>Des moments qui nous relient au quotidien</h2>

        <div class="story-copy">
          <p>
            <strong>ASSORELIE</strong> est une association loi 1901 créée à
            <?= htmlspecialchars($asso['city']) ?>. Notre mission est simple :
            <strong>créer du lien social</strong> en proposant des activités
            accessibles, variées et bienveillantes.
          </p>
          <p>
            Nous croyons que chacun a quelque chose à apporter et à recevoir,
            et que la richesse d'une communauté réside dans la diversité des
            personnes qui la composent.
          </p>
          <p>
            Que vous soyez Toulonnais de longue date ou nouvel arrivant, jeune
            ou moins jeune, vous trouverez chez nous une porte ouverte et un sourire.
          </p>
        </div>

        <div class="story-pillars">
          <article class="story-pillar story-pillar-social">
            <div class="story-pillar-title">
              <span aria-hidden="true">🤝</span>
              <h3>Lien social</h3>
            </div>
            <p>Créer des rencontres et tisser des liens entre les personnes de tous horizons.</p>
          </article>

          <article class="story-pillar story-pillar-knowledge">
            <div class="story-pillar-title">
              <span aria-hidden="true">📖</span>
              <h3>Savoirs</h3>
            </div>
            <p>Partager et transmettre des connaissances dans une démarche d'entraide.</p>
          </article>

          <article class="story-pillar story-pillar-culture">
            <div class="story-pillar-title">
              <span aria-hidden="true">🎨</span>
              <h3>Art &amp; culture</h3>
            </div>
            <p>Développer l'accès à l'art et à la culture pour tous dans la région toulonnaise.</p>
          </article>

          <article class="story-pillar story-pillar-environment">
            <div class="story-pillar-title">
              <span aria-hidden="true">🌲</span>
              <h3>Environnement</h3>
            </div>
            <p>Sensibiliser à la protection de notre environnement et du patrimoine naturel.</p>
          </article>
        </div>

        <p class="story-legal">
          RNA <?= htmlspecialchars($asso['rna']) ?> ·
          <?= htmlspecialchars($asso['city']) ?> ·
          <a href="<?= htmlspecialchars($social['instagram']) ?>" target="_blank" rel="noopener">Nous suivre sur Instagram →</a>
        </p>
      </div>
    </div>
  </div>
</section>
