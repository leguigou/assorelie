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
let smoothScrollFrame = null;
let restoreScrollBehavior = null;

function stopSmoothScroll() {
  if (smoothScrollFrame !== null) {
    cancelAnimationFrame(smoothScrollFrame);
    smoothScrollFrame = null;
  }

  if (restoreScrollBehavior !== null) {
    document.documentElement.style.scrollBehavior = restoreScrollBehavior;
    restoreScrollBehavior = null;
  }
}

function animateScrollTo(target, hash) {
  stopSmoothScroll();

  const headerHeight = document.querySelector('.site-header')?.offsetHeight ?? 0;
  const targetTop = target.id === 'accueil'
    ? 0
    : target.getBoundingClientRect().top + window.scrollY - headerHeight;
  const maxScroll = document.documentElement.scrollHeight - window.innerHeight;
  const destination = Math.max(0, Math.min(targetTop, maxScroll));
  const start = window.scrollY;
  const distance = destination - start;

  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || Math.abs(distance) < 2) {
    window.scrollTo(0, destination);
    history.pushState(null, '', hash);
    return;
  }

  const duration = Math.min(1050, Math.max(550, Math.abs(distance) * 0.55));
  const startedAt = performance.now();

  restoreScrollBehavior = document.documentElement.style.scrollBehavior;
  document.documentElement.style.scrollBehavior = 'auto';

  // Courbe ease-in-out : accélération douce, puis ralentissement progressif.
  const easeInOutCubic = progress => progress < 0.5
    ? 4 * progress * progress * progress
    : 1 - Math.pow(-2 * progress + 2, 3) / 2;

  const step = now => {
    const progress = Math.min((now - startedAt) / duration, 1);
    window.scrollTo(0, start + distance * easeInOutCubic(progress));

    if (progress < 1) {
      smoothScrollFrame = requestAnimationFrame(step);
      return;
    }

    smoothScrollFrame = null;
    document.documentElement.style.scrollBehavior = restoreScrollBehavior;
    restoreScrollBehavior = null;
    history.pushState(null, '', hash);
  };

  smoothScrollFrame = requestAnimationFrame(step);
}

function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      const hash = this.getAttribute('href');
      if (!hash || hash === '#') return;

      const target = document.getElementById(decodeURIComponent(hash.slice(1)));
      if (target) {
        e.preventDefault();
        animateScrollTo(target, hash);
      }
    });
  });

  window.addEventListener('wheel', stopSmoothScroll, { passive: true });
  window.addEventListener('touchstart', stopSmoothScroll, { passive: true });
  window.addEventListener('keydown', event => {
    if (['ArrowUp', 'ArrowDown', 'PageUp', 'PageDown', 'Home', 'End', ' '].includes(event.key)) {
      stopSmoothScroll();
    }
  });
}
