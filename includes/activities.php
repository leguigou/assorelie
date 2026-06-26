<!-- ===== VALEURS ===== -->
<section class="py-20 bg-white">
  <div class="max-w-6xl mx-auto px-6">
    <div class="text-center mb-16 reveal">
      <span class="text-rose-400 font-semibold text-sm uppercase tracking-widest">Nos valeurs</span>
      <h2 class="text-4xl font-bold font-quicksand text-gray-800 mt-3">Nos 4 piliers</h2>
      <p class="text-gray-500 mt-3 max-w-2xl mx-auto">
        Ce qui nous anime au quotidien, ce qui fait d'ASSORELIE une association unique.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
      <div class="text-center reveal p-6">
        <div class="w-16 h-16 bg-rose-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
          <span class="text-3xl">🤝</span>
        </div>
        <h3 class="font-bold text-lg text-gray-800 mb-2">Lien social</h3>
        <p class="text-gray-500 text-sm">Créer des rencontres et tisser des liens entre les personnes de tous horizons.</p>
      </div>

      <div class="text-center reveal p-6">
        <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
          <span class="text-3xl">📚</span>
        </div>
        <h3 class="font-bold text-lg text-gray-800 mb-2">Savoirs & compétences</h3>
        <p class="text-gray-500 text-sm">Partager et transmettre des connaissances dans une démarche d'entraide.</p>
      </div>

      <div class="text-center reveal p-6">
        <div class="w-16 h-16 bg-purple-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
          <span class="text-3xl">🎨</span>
        </div>
        <h3 class="font-bold text-lg text-gray-800 mb-2">Art & culture</h3>
        <p class="text-gray-500 text-sm">Développer l'accès à l'art et à la culture pour tous.</p>
      </div>

      <div class="text-center reveal p-6">
        <div class="w-16 h-16 bg-green-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
          <span class="text-3xl">🌱</span>
        </div>
        <h3 class="font-bold text-lg text-gray-800 mb-2">Environnement</h3>
        <p class="text-gray-500 text-sm">Sensibiliser à la protection de notre environnement et du patrimoine naturel.</p>
      </div>
    </div>
  </div>
</section>

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

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php $icons = ['🎲', '🌲', '🤟', '🎨', '🥂', '✨']; ?>
      <?php foreach ($activities as $i => $activity): ?>
      <div class="activity-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100 reveal">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl mb-4 bg-rose-50">
          <?= $icons[$i] ?>
        </div>
        <h3 class="font-bold text-lg text-gray-800 mb-2"><?= htmlspecialchars($activity['title']) ?></h3>
        <p class="text-gray-500 text-sm leading-relaxed"><?= htmlspecialchars($activity['description']) ?></p>
      </div>
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
