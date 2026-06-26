<!-- ===== CONTACT ===== -->
<section id="contact" class="py-20 bg-white">
  <div class="max-w-4xl mx-auto px-6">
    <div class="text-center mb-12 reveal">
      <span class="text-rose-400 font-semibold text-sm uppercase tracking-widest">Contact</span>
      <h2 class="text-4xl font-bold font-quicksand text-gray-800 mt-3">Envie de nous rejoindre ?</h2>
      <p class="text-gray-500 mt-3">Une question, une idée d'activité, une envie de participer ? Contactez-nous !</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <!-- Contact Info -->
      <div class="space-y-6 reveal">
        <div class="bg-warm-50 rounded-2xl p-6">
          <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center">
              <span class="text-xl">📧</span>
            </div>
            <div>
              <p class="text-sm text-gray-500">Email</p>
              <a href="mailto:<?= htmlspecialchars($asso['email']) ?>" class="font-medium text-gray-800 hover:text-rose-400 transition-colors">
                <?= htmlspecialchars($asso['email']) ?>
              </a>
            </div>
          </div>

          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center">
              <span class="text-xl">📞</span>
            </div>
            <div>
              <p class="text-sm text-gray-500">Téléphone</p>
              <a href="tel:<?= htmlspecialchars($asso['phone']) ?>" class="font-medium text-gray-800 hover:text-rose-400 transition-colors">
                <?= htmlspecialchars($asso['phone']) ?>
              </a>
            </div>
          </div>
        </div>

        <div class="bg-warm-50 rounded-2xl p-6">
          <h3 class="font-semibold text-gray-800 mb-3">📍 Nous trouver</h3>
          <p class="text-gray-500 text-sm"><?= htmlspecialchars($asso['address']) ?></p>
          <p class="text-gray-400 text-xs mt-2">Nos activités se déroulent dans différents lieux autour de Toulon. Contactez-nous pour connaître les prochains rendez-vous.</p>
        </div>

        <div class="bg-gradient-to-r from-rose-50 to-amber-50 rounded-2xl p-6 text-center">
          <p class="text-gray-700 font-medium mb-3">Prêt à nous rejoindre ?</p>
          <a href="<?= htmlspecialchars($social['helloasso']) ?>" target="_blank" rel="noopener" class="inline-block px-6 py-3 bg-rose-400 text-white rounded-xl font-semibold hover:bg-rose-500 transition-colors shadow-md">
            Adhérer en ligne →
          </a>
          <p class="text-xs text-gray-400 mt-2">Via HelloAsso, sécurisé et instantané</p>
        </div>
      </div>

      <!-- Formulaire -->
      <div class="reveal">
        <form id="contact-form" class="bg-warm-50 rounded-2xl p-6 space-y-4">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Votre nom</label>
            <input type="text" id="name" name="name" required
              class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-200 focus:border-rose-400 outline-none transition-all text-sm">
          </div>
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Votre email</label>
            <input type="email" id="email" name="email" required
              class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-200 focus:border-rose-400 outline-none transition-all text-sm">
          </div>
          <div>
            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Votre message</label>
            <textarea id="message" name="message" rows="4" required
              class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-200 focus:border-rose-400 outline-none transition-all text-sm resize-none"></textarea>
          </div>
          <button type="submit"
            class="w-full py-3 bg-rose-400 text-white rounded-xl font-semibold hover:bg-rose-500 transition-all shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
            Envoyer le message
          </button>
          <p id="form-status" class="text-sm text-center hidden"></p>
        </form>
      </div>
    </div>
  </div>
</section>
