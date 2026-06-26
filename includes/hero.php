<!-- ===== HERO ===== -->
<section id="accueil" class="hero-gradient relative overflow-hidden min-h-screen flex items-center">
  <!-- Blobs décoratifs -->
  <div class="hero-blob"></div>
  <div class="hero-blob"></div>
  <div class="hero-blob"></div>

  <div class="relative z-10 max-w-6xl mx-auto px-6 py-20 w-full">
    <div class="flex flex-col lg:flex-row items-center justify-between gap-12">
      <!-- Left: Text -->
      <div class="flex-1 text-center lg:text-left">
        <div class="inline-block bg-white/20 backdrop-blur-sm rounded-full px-5 py-2 text-white/90 text-sm font-medium mb-6">
          🤝 Association loi 1901 — Toulon
        </div>
        <h1 class="text-5xl md:text-7xl font-bold text-white font-quicksand leading-tight mb-4">
          <?= htmlspecialchars($asso['name']) ?>
        </h1>
        <p class="text-2xl md:text-3xl text-white/90 font-quicksand font-semibold mb-4">
          <?= htmlspecialchars($asso['slogan']) ?>
        </p>
        <p class="text-lg text-white/80 max-w-xl mb-8">
          Des activités variées et accessibles à tous autour de valeurs de partage, 
          de découverte et de convivialité dans la région toulonnaise.
        </p>
        <div class="flex flex-wrap gap-4 justify-center lg:justify-start">
          <a href="#activites" class="px-8 py-4 bg-white text-rose-400 rounded-xl font-semibold hover:bg-warm-50 transition-all shadow-lg hover:shadow-xl">
            Découvrir nos activités
          </a>
          <a href="<?= htmlspecialchars($social['helloasso']) ?>" target="_blank" rel="noopener" class="px-8 py-4 bg-white/15 backdrop-blur-sm text-white border-2 border-white/30 rounded-xl font-semibold hover:bg-white/25 transition-all">
            Adhérer →
          </a>
        </div>
      </div>

      <!-- Right: Logo / Illustration -->
      <div class="flex-1 flex justify-center">
        <div class="w-64 h-64 md:w-80 md:h-80 bg-white/10 backdrop-blur-md rounded-full flex items-center justify-center border-4 border-white/20">
          <div class="text-center text-white">
            <div class="text-6xl mb-3">🤝</div>
            <div class="text-xl font-bold font-quicksand">ASSORELIE</div>
            <div class="text-sm text-white/70">L'asso qui relie</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Scroll indicator -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-60">
        <path d="M7 13l5 5 5-5"/><path d="M7 6l5 5 5-5"/>
      </svg>
    </div>
  </div>
</section>
