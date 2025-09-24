<?php /* /financial-protection.php */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Phoenix Adventures | Financial Protection</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet"/>

  <style>
    :root{
      --brand:#2d8fdd;
      --brand-dark:#1b6fb8;
      --cta:#ff4500;
      --cta-dark:#e63900;
      --ink:#0b3a66;
    }
    html{ scroll-behavior:smooth; }
    body{ font-family:"Poppins",sans-serif; color:#333; background:#f9fbfe; }

    /* Hero */
    .hero{
      position:relative; color:#fff;
      background:linear-gradient(180deg, rgba(11,58,102,.7), rgba(11,58,102,.85)),
        url('/img/hero/financial-protection.jpg') center/cover no-repeat;
      min-height:240px; display:flex; align-items:center;
    }
    .hero .container{ padding:3rem 0; }
    .hero h1{ font-weight:800; }
    .hero .kicker{ font-weight:700; background:rgba(255,255,255,.15); padding:.25rem .5rem; border-radius:.5rem; display:inline-block; }

    /* Cards / sections */
    .doc-card{
      border:1px solid #e6effa; border-radius:.75rem; background:#fff;
      box-shadow:0 2px 10px rgba(0,0,0,.03);
    }
    .checklist li::marker{ color:var(--brand); }
    .badge-pill{
      background:#eef6ff; color:#0b3a66; border:1px solid #d7e9ff; border-radius:999px;
      padding:.35rem .7rem; font-weight:600;
    }

    /* Video wrapper */
    .ratio{ border-radius:.75rem; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,.06); }

    /* Sticky TOC on lg+ if you ever add it */
    @media (min-width:992px){ .sticky-col{ position:sticky; top:88px; } }

    /* Footer */
    footer{ background:#0b3a66; color:#cfe6ff; }
    footer a{ color:#fff; text-decoration:none; }
    footer a:hover{ text-decoration:underline; }

    .small-muted{ color:#6c757d; }
  </style>

  <!-- JSON-LD: WebPage + Video -->
  <script type="application/ld+json">
  {
    "@context":"https://schema.org",
    "@type":"WebPage",
    "name":"Financial Protection",
    "description":"Your payments are protected in a secure trust account at no cost to you. Learn how our trust account works and how Section 75 may offer extra protection.",
    "publisher":{"@type":"Organization","name":"Phoenix Adventures LTD"},
    "mainEntity":{
      "@type":"VideoObject",
      "name":"Phoenix Adventures Financial Protection",
      "description":"Overview of how the trust account protects your money.",
      "thumbnailUrl":["https://i.ytimg.com/vi/I_406S99Bmc/hqdefault.jpg"],
      "uploadDate":"2025-01-01",
      "embedUrl":"https://www.youtube.com/embed/I_406S99Bmc"
    }
  }
  </script>
</head>
<body>

<?php include $_SERVER['DOCUMENT_ROOT'].'/partials/header.php'; ?>

<!-- Hero -->
<section class="hero">
  <div class="container">
    <span class="kicker mb-2">Financial Protection — at no cost to you!</span>
    <h1 class="display-5 mt-2">Your money is safe</h1>
    <p class="mb-0">Protected in a secure trust account until your trip is complete.</p>
  </div>
</section>

<main id="main" class="container py-5">
  <!-- Intro + Video -->
  <div class="row g-4 align-items-start">
    <div class="col-lg-7">
      <div class="p-4 doc-card h-100">
        <h2 class="h4">How our protection works</h2>
        <p class="mb-3">If your trip or tour is classed as a <strong>package holiday</strong>:</p>
        <ul class="mb-3 checklist">
          <li>All customer funds are placed into a <strong>trust account</strong> in line with The Package Travel and Linked Travel Arrangements Regulations 2018.</li>
          <li>An independent <strong>trustee</strong> oversees the account and releases funds only once your travel has been completed.</li>
          <li>In the unlikely event of insolvency, your money remains safeguarded and available for a refund.</li>
        </ul>
        <div class="d-flex flex-wrap gap-2 mb-3">
          <span class="badge-pill">No extra cost</span>
          <span class="badge-pill">Independent trustee</span>
          <span class="badge-pill">Compliant with 2018 Regulations</span>
        </div>
        <div class="ratio ratio-16x9 mb-2">
          <iframe
            src="https://www.youtube.com/embed/I_406S99Bmc"
            title="Financial Protection | Phoenix Adventures"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            referrerpolicy="strict-origin-when-cross-origin"
            allowfullscreen></iframe>
        </div>
        <p class="small-muted mb-0">Video: quick overview of the trust account process.</p>
      </div>
    </div>

    <!-- Explainer image -->
    <div class="col-lg-5">
      <figure class="p-3 doc-card">
        <img src="/img/finance/trust-account-explainer.jpg"
             class="img-fluid rounded"
             alt="Diagram showing how your payment goes into a secure trust account, then is released by an independent trustee after your trip" />
        <figcaption class="small-muted mt-2">
          Trust account explainer — swap this image for your own visual if preferred.
        </figcaption>
      </figure>
    </div>
  </div>

  <!-- What is a trust account / How it works / What it means -->
  <section class="mt-5">
    <div class="row g-4">
      <div class="col-md-4">
        <div class="p-4 doc-card h-100">
          <h3 class="h5">What is a trust fund account?</h3>
          <p class="mb-0">It’s a separate account where your money is kept safely. A trustee will only release funds to us when your trip is complete.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-4 doc-card h-100">
          <h3 class="h5">How does this work?</h3>
          <ol class="mb-0">
            <li>You pay and funds go to the trust account.</li>
            <li>The trustee safeguards your money during the lead-up and travel.</li>
            <li>After your trip completes, the trustee releases funds.</li>
          </ol>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-4 doc-card h-100">
          <h3 class="h5">What does this mean for you?</h3>
          <p class="mb-0">If a trip cannot proceed and you don’t move to an alternative, your money is returned. If any part of the chain were to fail, the trustee refunds you — giving full financial protection.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Section 75 info -->
  <section class="mt-5">
    <div class="p-4 doc-card">
      <h3 class="h5 mb-2">How we protect your money</h3>
      <p class="mb-3">Top tip: if you have a credit card, you’re welcome to pay by this method at no extra cost, which may offer additional protection.</p>
      <h4 class="h6">Section 75 of the Consumer Credit Act</h4>
      <p class="mb-0">For purchases between <strong>£100 and £30,000</strong>, your credit card provider is jointly liable with the merchant for breach of contract or misrepresentation. If you have issues with goods or services and the retailer won’t resolve them, you can seek a refund or compensation from your card provider.</p>
    </div>
  </section>

  <!-- CTA -->
  <section class="mt-5">
    <div class="p-4 doc-card text-center">
      <h3 class="h5 mb-2">Book now with confidence</h3>
      <p class="mb-4">Phoenix Adventures LTD has you covered — your payments are protected in a secure trust account controlled by an independent trustee. If your trip cannot go ahead, the trustee returns your funds. Please keep your booking confirmation safe.</p>
      <a class="btn btn-primary px-4 py-2 fw-bold" style="background:var(--brand);border:none" href="/trips.html">View Trips</a>
    </div>
  </section>

  <!-- Confirmation form -->
  <section class="mt-5">
    <div class="p-4 doc-card">
      <h3 class="h5 mb-3">Confirm your protection</h3>
      <p class="mb-4">If you want written confirmation that your funds are protected, complete this form. We’ll respond within <strong>5 working days</strong>.</p>

      <form id="confirmForm" class="row g-3 needs-validation" method="post" action="/forms/confirm-protection" novalidate>
        <div class="col-md-6">
          <label for="name" class="form-label">Name *</label>
          <input type="text" class="form-control" id="name" name="name" required />
          <div class="invalid-feedback">Please enter your name.</div>
        </div>
        <div class="col-md-6">
          <label for="email" class="form-label">E-mail *</label>
          <input type="email" class="form-control" id="email" name="email" required />
          <div class="invalid-feedback">Please enter a valid email address.</div>
        </div>
        <div class="col-md-6">
          <label for="booking" class="form-label">Booking confirmation number *</label>
          <input type="text" class="form-control" id="booking" name="booking_number" required />
          <div class="invalid-feedback">Please add your booking number.</div>
        </div>
        <div class="col-md-6">
          <label for="postcode" class="form-label">Post code *</label>
          <input type="text" class="form-control" id="postcode" name="postcode" required />
          <div class="invalid-feedback">Please add your post code.</div>
        </div>
        <div class="col-12">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="consent" name="consent" required />
            <label class="form-check-label" for="consent">
              I agree that my data will be stored and processed for the purpose of establishing contact. I am aware that I can revoke my consent at any time. *
            </label>
            <div class="invalid-feedback">You must agree before submitting.</div>
          </div>
        </div>

        <!-- Honeypot (spam protection) -->
        <div class="d-none">
          <label>Leave this field empty</label>
          <input type="text" name="hp_field" tabindex="-1" autocomplete="off" />
        </div>

        <div class="col-12">
          <button type="submit" class="btn btn-success fw-bold">Confirm my protection</button>
        </div>

        <p class="small-muted mb-0 mt-2">* Indicates required fields</p>
      </form>
    </div>
  </section>

  <p class="small-muted mt-4 mb-0">Last updated: 24 September 2025</p>
</main>

<?php include $_SERVER['DOCUMENT_ROOT'].'/partials/footer.php'; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Bootstrap validation
  (() => {
    'use strict';
    const form = document.getElementById('confirmForm');
    form.addEventListener('submit', (e) => {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  })();
</script>
</body>
</html>
