/* =========================================================================
   Phoenix Adventures — Home (index.html) page script
   -------------------------------------------------------------------------
   What this file does:
   - Includes the announcement bar and navbar HTML fragments
   - Handles the mailing list modal pop-up (only after cookies accepted)
   - Shows cookie consent and stores the user's choice
   - Renders the Next Event ticker using data saved by tours.html:
       * `phoenix_events_all`  – list of all events with deep links
       * `phoenix_next_event`  – the next upcoming event (convenience)
     The ticker *deep-links* to the exact tour card on /tours.html#<id>
   ========================================================================= */

/* ------------------------------
   Utilities
   ------------------------------ */

// Safe JSON read from localStorage (returns null on parse error)
function readJSON(key) {
  try { return JSON.parse(localStorage.getItem(key)); }
  catch { return null; }
}

// Helper: pick the next upcoming event from a list
function pickUpcoming(list) {
  if (!Array.isArray(list)) return null;
  const now = new Date();
  const valid = list
    .filter(ev => ev && ev.start && !isNaN(Date.parse(ev.start)))
    .sort((a, b) => new Date(a.start) - new Date(b.start));
  return valid.find(ev => new Date(ev.start) >= now) || null;
}

/* ------------------------------
   HTML fragment includes
   ------------------------------ */

document.addEventListener("DOMContentLoaded", () => {
  // 1) Announcement bar (server fragment)
  fetch("/announcement.html")
    .then(r => r.text())
    .then(html => {
      const el = document.getElementById("announcement-placeholder");
      if (el) el.innerHTML = html;
    })
    .catch(() => { /* silently ignore */ });

  // 2) Navbar (server fragment)
  fetch("/navbar.html")
    .then(r => r.text())
    .then(html => {
      const el = document.getElementById("navbar-placeholder");
      if (el) el.innerHTML = html;
    })
    .catch(() => { /* silently ignore */ });

  /* ------------------------------
     Mailing list modal (optional)
     - Only appears if user has accepted cookies
     - Delayed 5 seconds to avoid being annoying
     ------------------------------ */
  if (localStorage.getItem("cookiesAccepted") === "true") {
    setTimeout(() => {
      const modalEl = document.getElementById("mailingListModal");
      if (!modalEl || typeof bootstrap === "undefined") return;
      const modal = new bootstrap.Modal(modalEl);
      modal.show();
    }, 5000);
  }

  const form = document.getElementById("mailingListForm");
  if (form) {
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      const name = (document.getElementById("name")?.value || "there").trim();
      alert("Thanks for subscribing, " + name + "!");
      const modalEl = document.getElementById("mailingListModal");
      if (modalEl && typeof bootstrap !== "undefined") {
        bootstrap.Modal.getInstance(modalEl)?.hide();
      }
      form.reset();
    });
  }

  /* ------------------------------
     Cookie consent banner
     ------------------------------ */
  const cookieBanner = document.getElementById("cookieConsent");
  const acceptBtn = document.getElementById("acceptCookies");
  const declineBtn = document.getElementById("declineCookies");

  // Show banner if no previous choice
  if (cookieBanner &&
      !localStorage.getItem("cookiesAccepted") &&
      !localStorage.getItem("cookiesDeclined")) {
    cookieBanner.style.display = "block";
  }

  acceptBtn?.addEventListener("click", () => {
    localStorage.setItem("cookiesAccepted", "true");
    if (cookieBanner) cookieBanner.style.display = "none";
  });

  declineBtn?.addEventListener("click", () => {
    localStorage.setItem("cookiesDeclined", "true");
    if (cookieBanner) cookieBanner.style.display = "none";
  });

  /* ------------------------------
     Next Event ticker (deep-link)
     ------------------------------ */

  const tickerText = document.getElementById("tickerText");
  const tickerCta  = document.getElementById("tickerCta");

  function renderTicker() {
    if (!tickerText || !tickerCta) return;

    // Prefer a live calculation from the *full* list if we have it
    const all = readJSON("phoenix_events_all");          // saved by tours.html
    const bestFromAll = pickUpcoming(all);

    // Fallback to the preselected single event if needed
    const savedNext = readJSON("phoenix_next_event");

    // Choose the event (from all if possible; else saved single; else none)
    const ev = bestFromAll || savedNext;

    if (ev && ev.start) {
      const when = new Date(ev.start);
      const dateStr = when.toLocaleDateString(undefined, {
        weekday: "short", day: "2-digit", month: "short"
      });
      const timeStr = when.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });

      // Marquee text
      tickerText.textContent = `${dateStr} — ${ev.title || "Tour"}${ev.location ? " @ " + ev.location : ""} (${timeStr})`;

      // Deep link to the exact event card on /tours.html
      // (this is the bugfix: use ev.url, not nextEvent.url)
      tickerCta.href = ev.url || "/tours.html#toursAccordion";
    } else {
      // No event data yet (e.g. user hasn’t visited /tours.html)
      tickerText.textContent = "See our upcoming tours and special departures.";
      tickerCta.href = "/tours.html#toursAccordion";
    }
  }

  // Initial render
  renderTicker();

  // Re-render if another tab updates the stored events
  window.addEventListener("storage", (e) => {
    if (e.key === "phoenix_events_all" || e.key === "phoenix_next_event") {
      renderTicker();
    }
  });

  // Optional: re-check shortly after midnight so it rolls to the next event next day
  // (avoids waiting for a /tours.html visit)
  function scheduleMidnightRefresh() {
    const now = new Date();
    const next = new Date(now);
    next.setHours(24, 0, 5, 0); // 5 seconds after midnight
    const ms = next - now;
    setTimeout(() => {
      renderTicker();
      scheduleMidnightRefresh();
    }, Math.max(1000, ms));
  }
  scheduleMidnightRefresh();
});
