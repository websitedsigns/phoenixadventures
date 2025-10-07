/* =========================================================================
   Phoenix Adventures — Tours page script
   -------------------------------------------------------------------------
   What this file does:
   - Loads navbar fragment into #navbar-placeholder
   - Search & chip filtering for tours
   - Auto-open month with the next upcoming event
   - Deep-link support: /tours.html#<event-id> expands the right month,
     scrolls to the card, and highlights it briefly
   - Builds a canonical list of events (with deep links to this page) and
     stores it in localStorage:
       * phoenix_events_all : array of all events (start, title, location, url)
       * phoenix_next_event : the next upcoming event
     The home page ticker reads these to show the next event and link
     directly to the correct card.
   ========================================================================= */

document.addEventListener("DOMContentLoaded", () => {
  /* ------------------------------
     Navbar include (HTML fragment)
     ------------------------------ */
  fetch("/navbar.html")
    .then(r => r.text())
    .then(html => {
      const el = document.getElementById("navbar-placeholder");
      if (el) el.innerHTML = html;
    })
    .catch(() => { /* ignore */ });

  /* ------------------------------
     Elements
     ------------------------------ */
  const searchInput = document.getElementById("searchInput");
  const accordion = document.getElementById("toursAccordion");
  const items = accordion.querySelectorAll(".accordion-item");
  const chips = document.querySelectorAll(".chip");
  const resultCount = document.getElementById("resultCount");
  const clearFilters = document.getElementById("clearFilters");
  const emptyState = document.getElementById("emptyState");
  const emptyClear = document.getElementById("emptyClear");

  let activeFilters = new Set();

  /* ------------------------------
     Helpers
     ------------------------------ */

  const normalise = (s) => (s || "").toLowerCase().trim();

  // Collapses/expands months based on visibility
  function runFilter(resetMonths = false) {
    const q = normalise(searchInput.value);
    let visibleCardCount = 0;

    items.forEach((monthItem) => {
      const collapse = monthItem.querySelector(".accordion-collapse");
      const button = monthItem.querySelector(".accordion-button");
      const cards = monthItem.querySelectorAll(".tour-card");

      let monthHasVisible = false;

      cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        const title = (card.dataset.title || "").toLowerCase();
        const location = (card.dataset.location || "").toLowerCase();
        const tags = (card.dataset.tags || "").toLowerCase() + " " + (card.dataset.theme || "").toLowerCase();

        const matchesText = !q || text.includes(q) || title.includes(q);
        const matchesFilters =
          activeFilters.size === 0 ||
          [...activeFilters].every(f => location.includes(f) || tags.includes(f));

        const show = matchesText && matchesFilters;
        card.style.display = show ? "" : "none";
        if (show) { monthHasVisible = true; visibleCardCount++; }
      });

      if (monthHasVisible) {
        if (resetMonths || q || activeFilters.size > 0) {
          if (!collapse.classList.contains("show")) {
            new bootstrap.Collapse(collapse, { show: true, toggle: false });
            collapse.classList.add("show");
          }
          button.classList.add("bg-warning", "text-dark");
        } else {
          button.classList.remove("bg-warning", "text-dark");
        }
        monthItem.style.display = "";
      } else {
        monthItem.style.display = "none";
        button.classList.remove("bg-warning", "text-dark");
        if (collapse.classList.contains("show")) {
          new bootstrap.Collapse(collapse, { hide: true, toggle: false });
          collapse.classList.remove("show");
        }
      }
    });

    // Results + empty state
    if (visibleCardCount === 0) {
      resultCount.textContent = "No results";
      emptyState.classList.remove("d-none");
    } else {
      resultCount.textContent = visibleCardCount + " tour" + (visibleCardCount > 1 ? "s" : "");
      emptyState.classList.add("d-none");
    }

    // If nothing typed and no chips, open upcoming month
    if (!q && activeFilters.size === 0 && !resetMonths) {
      openNextMonth();
    }
  }

  // Find and open the month that contains the soonest future event
  function openNextMonth() {
    const now = new Date();
    let next = null;
    accordion.querySelectorAll(".tour[data-event-start]").forEach(node => {
      const d = new Date(node.getAttribute("data-event-start"));
      if (!isNaN(d) && d >= now) {
        if (!next || d < next.date) { next = { date: d, node }; }
      }
    });

    const targetMonthItem = next ? next.node.closest(".accordion-item") : items[0];

    items.forEach(item => {
      const collapse = item.querySelector(".accordion-collapse");
      if (item === targetMonthItem) {
        if (!collapse.classList.contains("show")) {
          new bootstrap.Collapse(collapse, { show: true, toggle: false });
          collapse.classList.add("show");
        }
      } else {
        collapse.classList.remove("show");
      }
    });

    // Scroll into view if it's not the first month
    if (targetMonthItem && targetMonthItem !== items[0]) {
      setTimeout(() => targetMonthItem.scrollIntoView({ behavior: "smooth", block: "start" }), 50);
    }
  }

  // Slugify for deterministic element IDs
  const slug = (s) =>
    (s || "")
      .toLowerCase()
      .normalize("NFKD")
      .replace(/[\u0300-\u036f]/g, "")
      .replace(/[^a-z0-9]+/g, "-")
      .replace(/^-+|-+$/g, "")
      .slice(0, 70);

  /* ------------------------------
     Chip interactions
     ------------------------------ */
  chips.forEach(chip => {
    chip.addEventListener("click", () => {
      const tag = chip.dataset.filter.toLowerCase();
      if (chip.classList.contains("active")) {
        chip.classList.remove("active");
        activeFilters.delete(tag);
      } else {
        chip.classList.add("active");
        activeFilters.add(tag);
      }
      runFilter();
    });
  });

  clearFilters.addEventListener("click", () => {
    searchInput.value = "";
    chips.forEach(c => c.classList.remove("active"));
    activeFilters.clear();
    runFilter(true);
  });

  emptyClear?.addEventListener("click", () => clearFilters.click());
  searchInput.addEventListener("input", runFilter);

  /* ------------------------------
     Build & store deep-link events
     ------------------------------ */
  (function buildAndStoreEvents() {
    const nodes = document.querySelectorAll(".tour[data-event-start]");
    const now = new Date();
    const events = [];
    const usedIds = new Set();

    nodes.forEach(node => {
      const startStr = node.getAttribute("data-event-start");
      const start = new Date(startStr);
      if (isNaN(start)) return;

      const title =
        node.getAttribute("data-title") ||
        node.querySelector(".tour-title")?.textContent?.trim() ||
        "Tour";

      // Deterministic ID: evt-YYYYMMDD-<slug>
      const yyyymmdd = (startStr || "").slice(0, 10).replace(/-/g, "");
      let baseId = `evt-${yyyymmdd}-${slug(title)}` || `evt-${yyyymmdd}`;
      let id = baseId, n = 2;
      while (usedIds.has(id) || document.getElementById(id)) { id = `${baseId}-${n++}`; }
      usedIds.add(id);
      if (!node.id) node.id = id;

      // Always deep-link to the card itself, not the booking page
      events.push({
        start: start.toISOString(),
        title,
        location: node.getAttribute("data-location") || "",
        url: `/tours.html#${id}`
      });
    });

    events.sort((a, b) => new Date(a.start) - new Date(b.start));

    if (events.length) {
      localStorage.setItem("phoenix_events_all", JSON.stringify(events));
      const next = events.find(ev => new Date(ev.start) >= now);
      if (next) {
        localStorage.setItem("phoenix_next_event", JSON.stringify(next));
      } else {
        localStorage.removeItem("phoenix_next_event");
      }
    } else {
      localStorage.removeItem("phoenix_events_all");
      localStorage.removeItem("phoenix_next_event");
    }
  })();

  /* ------------------------------
     Deep-link support:
     - If URL hash points to a card, expand month & highlight
     ------------------------------ */
  function revealHashTarget() {
    if (!location.hash) return;
    const target = document.getElementById(location.hash.slice(1));
    if (!target) return;

    const monthItem = target.closest(".accordion-item");
    if (monthItem) {
      const collapse = monthItem.querySelector(".accordion-collapse");
      if (collapse && !collapse.classList.contains("show")) {
        new bootstrap.Collapse(collapse, { show: true, toggle: false });
        collapse.classList.add("show");
      }
    }

    // Smooth scroll + flash highlight
    target.scrollIntoView({ behavior: "smooth", block: "center" });
    target.classList.add("flash");
    setTimeout(() => target.classList.remove("flash"), 2200);
  }

  // Initial pass
  runFilter(true);
  // Reveal hash target on load and when hash changes
  revealHashTarget();
  window.addEventListener("hashchange", revealHashTarget);
});
