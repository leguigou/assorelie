<!-- ===== ACTIVITÉS ===== -->
<section id="activites" class="py-20 bg-warm-50">
  <div class="max-w-6xl mx-auto px-6">
    <div class="text-center mb-16 reveal">
      <span class="text-rose-400 font-semibold text-sm uppercase tracking-widest">Nos activités</span>
      <h2 class="text-4xl font-bold font-quicksand text-gray-800 mt-3">Ce qu'on vous propose</h2>
      <p class="text-gray-500 mt-3 max-w-2xl mx-auto">
        Des activités variées pour tous les goûts, dans une ambiance conviviale et bienveillante.
      </p>
    </div>

    <?php
    $activityVisuals = [
        'dice' => ['image' => 'jeux-societe.webp', 'emoji' => '🎲', 'label' => 'Loisirs'],
        'tree' => ['image' => 'balade-nature.webp', 'emoji' => '🌲', 'label' => 'Nature'],
        'hands' => ['image' => 'langue-des-signes.webp', 'emoji' => '🤟', 'label' => 'Inclusion'],
        'palette' => ['image' => 'atelier-creatif.webp', 'emoji' => '🎨', 'label' => 'Art'],
        'glass' => ['image' => 'apero-partage.webp', 'emoji' => '🥂', 'label' => 'Convivialité'],
        'sparkles' => ['image' => 'evenements-thematiques.webp', 'emoji' => '✨', 'label' => 'Thématique'],
    ];
    ?>

    <div class="activity-grid">
      <?php foreach ($activities as $activity): ?>
      <?php
      $visual = $activityVisuals[$activity['icon']] ?? [
          'image' => 'hero-toulon.webp',
          'emoji' => '♥',
          'label' => 'ASSORELIE',
      ];
      ?>
      <article class="activity-card reveal">
        <div class="activity-media">
          <img
            src="assets/images/<?= htmlspecialchars($visual['image']) ?>"
            alt="<?= htmlspecialchars($activity['title']) ?>"
            loading="lazy"
            width="1200"
            height="800"
          >
          <span class="activity-badge">
            <span aria-hidden="true"><?= $visual['emoji'] ?></span>
            <?= htmlspecialchars($visual['label']) ?>
          </span>
        </div>
        <div class="activity-content">
          <h3><?= htmlspecialchars($activity['title']) ?></h3>
          <p><?= htmlspecialchars($activity['description']) ?></p>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===== AGENDA ===== -->
<section id="agenda" class="py-20 bg-white">
  <div class="max-w-4xl mx-auto px-6">
    <div class="text-center mb-12 reveal">
      <span class="text-rose-400 font-semibold text-sm uppercase tracking-widest">Agenda</span>
      <h2 class="text-4xl font-bold font-quicksand text-gray-800 mt-3">Nos prochains événements</h2>
      <p class="text-gray-500 mt-3">Tous les événements sont ouverts à tous, adhérents ou non.</p>
    </div>

    <div id="events-container">
      <div class="flex justify-center py-8">
        <div class="spinner"></div>
      </div>
    </div>

    <div class="text-center mt-8 reveal">
      <a href="<?= htmlspecialchars($social['helloasso']) ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-rose-500 font-medium hover:text-rose-600 transition-colors">
        Voir tous les événements sur HelloAsso →
      </a>
    </div>
  </div>
</section>
