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
              <svg class="story-pillar-icon" aria-hidden="true" viewBox="0 0 24 24">
                <path d="m11 17 2 2a1 1 0 0 0 3-3"/>
                <path d="m14 14 2.5 2.5a1 1 0 0 0 3-3l-4.1-4.1a2 2 0 0 0-2.8 0l-1.1 1.1a2 2 0 0 1-2.8-2.8l2.6-2.6a4 4 0 0 1 5.7 0L19 7"/>
                <path d="m5 6 3 3"/>
                <path d="m2 9 4-4 3 3-4 4Z"/>
                <path d="m15 8 3-3 4 4-4 4"/>
                <path d="m8 14 1.5 1.5"/>
                <path d="m6 16 1.5 1.5"/>
              </svg>
              <h3>Lien social</h3>
            </div>
            <p>Créer des rencontres et tisser des liens entre les personnes de tous horizons.</p>
          </article>

          <article class="story-pillar story-pillar-knowledge">
            <div class="story-pillar-title">
              <svg class="story-pillar-icon" aria-hidden="true" viewBox="0 0 24 24">
                <path d="M2 4.5A2.5 2.5 0 0 1 4.5 2H11a1 1 0 0 1 1 1v17a2.5 2.5 0 0 0-2.5-2.5H2Z"/>
                <path d="M22 4.5A2.5 2.5 0 0 0 19.5 2H13a1 1 0 0 0-1 1v17a2.5 2.5 0 0 1 2.5-2.5H22Z"/>
                <path d="M5 6h4"/>
                <path d="M15 6h4"/>
              </svg>
              <h3>Savoirs</h3>
            </div>
            <p>Partager et transmettre des connaissances dans une démarche d'entraide.</p>
          </article>

          <article class="story-pillar story-pillar-culture">
            <div class="story-pillar-title">
              <svg class="story-pillar-icon" aria-hidden="true" viewBox="0 0 24 24">
                <circle cx="13.5" cy="6.5" r=".5"/>
                <circle cx="17.5" cy="10.5" r=".5"/>
                <circle cx="8.5" cy="7.5" r=".5"/>
                <circle cx="6.5" cy="12.5" r=".5"/>
                <path d="M12 2a10 10 0 0 0 0 20c1.1 0 2-.9 2-2 0-.5-.2-.9-.5-1.3-.3-.4-.5-.8-.5-1.2a2 2 0 0 1 2-2h1.8A5.2 5.2 0 0 0 22 10.3C22 5.7 17.5 2 12 2Z"/>
              </svg>
              <h3>Art &amp; culture</h3>
            </div>
            <p>Développer l'accès à l'art et à la culture pour tous dans la région toulonnaise.</p>
          </article>

          <article class="story-pillar story-pillar-environment">
            <div class="story-pillar-title">
              <svg class="story-pillar-icon" aria-hidden="true" viewBox="0 0 24 24">
                <path d="m17 14 3 4H4l3-4"/>
                <path d="m15 10 3 4H6l3-4"/>
                <path d="m13 6 3 4H8l4-6 1 2Z"/>
                <path d="M12 18v4"/>
              </svg>
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
