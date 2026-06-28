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
function escapeHtml(value) {
  return String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

function safeUrl(value, fallback = '') {
  if (!value) return fallback;

  try {
    const url = new URL(String(value), window.location.origin);
    return ['http:', 'https:'].includes(url.protocol) ? url.href : fallback;
  } catch {
    return fallback;
  }
}

function loadEvents() {
  const container = document.getElementById('events-container');
  if (!container) return;

  container.innerHTML = '<div class="flex justify-center py-8"><div class="spinner"></div></div>';

  fetch('api/events.php')
    .then(res => res.json())
    .then(events => {
      const upcomingEvents = Array.isArray(events)
        ? events.filter(event => event.past !== true)
        : [];

      if (!upcomingEvents.length) {
        container.innerHTML = '<p class="text-gray-500 text-center py-8">Aucun événement à venir pour le moment. Revenez bientôt !</p>';
        return;
      }

      const months = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
      const days = ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'];

      container.innerHTML = upcomingEvents.map((e, index) => {
        const d = new Date(`${e.date}T12:00:00`);
        const dayName = days[d.getDay()];
        const dayNum = String(d.getDate()).padStart(2, '0');
        const month = months[d.getMonth()];
        const eventLink = safeUrl(e.link);
        const image = safeUrl(
          e.image,
          `${window.location.origin}/assets/images/hero-toulon.webp`
        );
        const imageAlt = e.image_alt || `Ambiance de l'événement ${e.title}`;

        return `
          <article class="event-card ${index === 0 ? 'event-card-featured' : ''} reveal">
            <div class="event-media">
              <img src="${escapeHtml(image)}" alt="${escapeHtml(imageAlt)}" loading="${index === 0 ? 'eager' : 'lazy'}">
              <div class="event-date">
                <span>${escapeHtml(dayName)}</span>
                <strong>${escapeHtml(dayNum)}</strong>
                <small>${escapeHtml(month)}</small>
              </div>
            </div>
            <div class="event-body">
              ${index === 0 ? '<span class="event-next">Prochain rendez-vous</span>' : ''}
              <h3>${escapeHtml(e.title)}</h3>
              <div class="event-meta">
                <span>🕐 ${escapeHtml(e.time)}</span>
                <span>📍 ${escapeHtml(e.location)}</span>
              </div>
              <p>${escapeHtml(e.description)}</p>
              ${eventLink
                ? `<a href="${escapeHtml(eventLink)}" target="_blank" rel="noopener" class="event-action">Je participe <span>→</span></a>`
                : '<span class="event-contact-note">Contactez-nous pour participer</span>'
              }
            </div>
          </article>
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
