<!-- ===== FOOTER ===== -->
<footer class="footer-gradient text-white">
  <div class="max-w-6xl mx-auto px-6 py-16">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <!-- Brand -->
      <div>
        <div class="flex items-center gap-3 mb-4">
          <img src="assets/images/logo-assorelie.webp" alt="" width="42" height="42" class="footer-logo">
          <div>
            <div class="font-bold font-quicksand text-lg">ASSORELIE</div>
            <div class="text-white/50 text-xs">L'asso qui relie</div>
          </div>
        </div>
        <p class="text-white/60 text-sm leading-relaxed">
          Association loi 1901 — RNA W832022270<br>
          Créée le 4 octobre 2024 à Toulon.
        </p>
      </div>

      <!-- Liens rapides -->
      <div>
        <h3 class="font-semibold text-white/80 mb-4">Navigation</h3>
        <ul class="space-y-2 text-sm">
          <li><a href="#accueil" class="text-white/50 hover:text-white transition-colors">Accueil</a></li>
          <li><a href="#activites" class="text-white/50 hover:text-white transition-colors">Activités</a></li>
          <li><a href="#agenda" class="text-white/50 hover:text-white transition-colors">Agenda</a></li>
          <li><a href="#a-propos" class="text-white/50 hover:text-white transition-colors">À propos</a></li>
          <li><a href="#contact" class="text-white/50 hover:text-white transition-colors">Contact</a></li>
        </ul>
      </div>

      <!-- Contact & Réseaux -->
      <div>
        <h3 class="font-semibold text-white/80 mb-4">Nous retrouver</h3>
        <ul class="space-y-3 text-sm">
          <li>
            <a href="mailto:<?= htmlspecialchars($asso['email']) ?>" class="text-white/50 hover:text-white transition-colors flex items-center gap-2">
              <span class="text-xs">📧</span> <?= htmlspecialchars($asso['email']) ?>
            </a>
          </li>
          <li>
            <a href="<?= htmlspecialchars($social['instagram']) ?>" target="_blank" rel="noopener" class="text-white/50 hover:text-white transition-colors flex items-center gap-2">
              <span class="text-xs">📸</span> @assorelie
            </a>
          </li>
          <li>
            <a href="<?= htmlspecialchars($social['helloasso']) ?>" target="_blank" rel="noopener" class="text-white/50 hover:text-white transition-colors flex items-center gap-2">
              <span class="text-xs">💚</span> HelloAsso
            </a>
          </li>
        </ul>
      </div>
    </div>

    <div class="border-t border-white/10 mt-10 pt-6 text-center">
      <p class="text-white/30 text-xs">
        &copy; <?= date('Y') ?> ASSORELIE — Association loi 1901. Tous droits réservés.
      </p>
    </div>
  </div>
</footer>

<!-- Scripts -->
<script src="assets/js/main.js"></script>

<!-- Contact form handler -->
<script>
document.getElementById('contact-form')?.addEventListener('submit', function(e) {
  e.preventDefault();
  const btn = this.querySelector('button[type="submit"]');
  const status = document.getElementById('form-status');
  const formData = new FormData(this);

  btn.disabled = true;
  btn.textContent = 'Envoi en cours...';
  status.classList.add('hidden');

  fetch('api/contact.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    status.classList.remove('hidden');
    if (data.success) {
      status.className = 'text-sm text-center text-green-600 mt-2';
      status.textContent = '✅ Message envoyé ! Nous vous répondrons rapidement.';
      this.reset();
    } else {
      status.className = 'text-sm text-center text-red-500 mt-2';
      status.textContent = data.error || '❌ Une erreur est survenue. Réessayez ou envoyez-nous un email directement.';
    }
  })
  .catch(() => {
    status.classList.remove('hidden');
    status.className = 'text-sm text-center text-red-500 mt-2';
    status.textContent = '❌ Erreur réseau. Contactez-nous par email directement.';
  })
  .finally(() => {
    btn.disabled = false;
    btn.textContent = 'Envoyer le message';
  });
});
</script>

</body>
</html>
