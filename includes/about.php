<!-- ===== À PROPOS ===== -->
<section id="a-propos" class="py-20 bg-warm-50">
  <div class="max-w-4xl mx-auto px-6">
    <div class="text-center mb-12 reveal">
      <span class="text-rose-400 font-semibold text-sm uppercase tracking-widest">À propos</span>
      <h2 class="text-4xl font-bold font-quicksand text-gray-800 mt-3">L'association</h2>
    </div>

    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 reveal">
      <div class="prose max-w-none text-gray-600 leading-relaxed">
        <p class="text-lg">
          <strong>ASSORELIE</strong> est une association loi 1901 créée le <strong>4 octobre 2024</strong> à Toulon, 
          sous le numéro RNA <strong>W832022270</strong>.
        </p>
        <p class="mt-4">
          Notre mission : <strong>créer du lien social</strong> en proposant des activités accessibles, 
          variées et bienveillantes. Nous croyons que chacun a quelque chose à apporter et à recevoir, 
          et que la richesse d'une communauté réside dans la diversité des personnes qui la composent.
        </p>
        <p class="mt-4">
          Que vous soyez Toulonnais de longue date ou nouvel arrivant, jeune ou moins jeune, 
          en couple ou solo, vous trouverez chez ASSORELIE une porte ouverte et un sourire.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8 pt-8 border-t border-gray-100">
        <div>
          <h3 class="font-semibold text-gray-800 mb-3">Informations</h3>
          <ul class="space-y-2 text-sm text-gray-500">
            <li><strong class="text-gray-600">RNA :</strong> <?= htmlspecialchars($asso['rna']) ?></li>
            <li><strong class="text-gray-600">Création :</strong> <?= htmlspecialchars($asso['creation_date']) ?></li>
            <li><strong class="text-gray-600">Ville :</strong> <?= htmlspecialchars($asso['city']) ?></li>
            <li><strong class="text-gray-600">Adresse :</strong> <?= htmlspecialchars($asso['address']) ?></li>
          </ul>
        </div>
        <div>
          <h3 class="font-semibold text-gray-800 mb-3">Nous suivre</h3>
          <div class="flex flex-wrap gap-3">
            <a href="<?= htmlspecialchars($social['instagram']) ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-xl text-sm font-medium hover:opacity-90 transition-opacity">
              📸 Instagram
            </a>
            <a href="<?= htmlspecialchars($social['facebook']) ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-medium hover:opacity-90 transition-opacity">
              👍 Facebook
            </a>
            <a href="<?= htmlspecialchars($social['helloasso']) ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 bg-teal-500 text-white rounded-xl text-sm font-medium hover:opacity-90 transition-opacity">
              💚 HelloAsso
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
