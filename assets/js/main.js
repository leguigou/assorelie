/**
 * ASSORELIE - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
  initMobileMenu();
  initScrollReveal();
  loadEvents();
  initSmoothScroll();
});

/* ===== Mobile Menu ===== */
function initMobileMenu() {
  const btn = document.getElementById('menu-btn');
  const nav = document.getElementById('nav-mobile');
  if (!btn || !nav) return;

  btn.addEventListener('click', () => {
    nav.classList.toggle('open');
    const isOpen = nav.classList.contains('open');
    btn.setAttribute('aria-expanded', String(isOpen));
    btn.setAttribute('aria-label', isOpen ? 'Fermer le menu' : 'Ouvrir le menu');
    btn.innerHTML = isOpen
      ? '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>'
      : '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>';
  });

  // Close on link click
  nav.querySelectorAll('a').forEach(a => {
    a.addEventListener('click', () => {
      nav.classList.remove('open');
      btn.setAttribute('aria-expanded', 'false');
      btn.setAttribute('aria-label', 'Ouvrir le menu');
      btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>';
    });
  });
}

/* ===== Scroll Reveal ===== */
function initScrollReveal() {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
}

/* ===== Agenda AJAX ===== */
function loadEvents() {
  const container = document.getElementById('events-container');
  if (!container) return;

  container.innerHTML = '<div class="flex justify-center py-8"><div class="spinner"></div></div>';

  fetch('api/events.php')
    .then(res => res.json())
    .then(events => {
      if (!events.length) {
        container.innerHTML = '<p class="text-gray-500 text-center py-8">Aucun événement à venir pour le moment. Revenez bientôt !</p>';
        return;
      }

      const months = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
      const days = ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'];

      container.innerHTML = events.map(e => {
        const d = new Date(e.date);
        const dayName = days[d.getDay()];
        const dayNum = String(d.getDate()).padStart(2, '0');
        const month = months[d.getMonth()];
        const hasLink = e.link && e.link.length > 0;
        const isPast = e.past === true;

        return `
          <div class="event-card bg-white rounded-xl p-5 mb-4 flex items-start gap-4 reveal ${isPast ? 'opacity-40' : ''}">
            <div class="text-center min-w-[60px]">
              <div class="text-xs uppercase font-bold ${isPast ? 'text-gray-300' : 'text-rose-400'}">${dayName}</div>
              <div class="text-2xl font-bold ${isPast ? 'text-gray-400' : 'text-gray-800'}">${dayNum}</div>
              <div class="text-xs font-medium ${isPast ? 'text-gray-300' : 'text-gray-500'}">${month}</div>
            </div>
            <div class="flex-1">
              <h3 class="font-semibold text-lg ${isPast ? 'text-gray-400' : 'text-gray-800'}">${e.title}</h3>
              <p class="text-sm mt-1 ${isPast ? 'text-gray-300' : 'text-gray-500'}">${e.description}</p>
              <div class="flex items-center gap-4 mt-2 text-sm text-gray-400">
                <span>🕐 ${e.time}</span>
                <span>📍 ${e.location}</span>
              </div>
            </div>
            ${hasLink && !isPast
              ? `<a href="${e.link}" target="_blank" rel="noopener" class="shrink-0 mt-1 px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-500 rounded-lg text-sm font-medium transition-colors">Je participe →</a>`
              : ''
            }
          </div>
        `;
      }).join('');

      // Re-init reveal for new elements
      initScrollReveal();
    })
    .catch(() => {
      container.innerHTML = '<p class="text-red-500 text-center py-8">Impossible de charger les événements. Réessayez plus tard.</p>';
    });
}

/* ===== Smooth Scroll ===== */
function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });
}
