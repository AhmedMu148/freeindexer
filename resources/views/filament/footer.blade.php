<footer class="fi-custom-footer">
  <div class="fi-custom-footer__inner">
    <nav class="fi-custom-footer__nav">
      <a class="fi-custom-footer__link" href="{{ url('/') }}">HOME</a>
      <a class="fi-custom-footer__link" href="{{ url('/about') }}">ABOUT US</a>
      <a class="fi-custom-footer__link" href="{{ url('/contact') }}">CONTACT US</a>
    </nav>

    <div class="fi-custom-footer__copy">
      © {{ date('Y') }}, Free Indexer
    </div>
  </div>

  <style>
    /* Footer container */
    .fi-custom-footer {
      background: #2b2b2b;
      border-top: 1px solid rgba(255, 255, 255, .06);
      color: #fff;
    }

    /* Tailwind-ish layout without relying only on classes */
    .fi-custom-footer__inner {
      max-width: 1536px;
      margin: 0 auto;
      padding: 14px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
    }

    .fi-custom-footer__nav {
      display: flex;
      align-items: center;
      gap: 28px;
      text-transform: uppercase;
      letter-spacing: .5px;
      font-size: 13px;
      font-weight: 600;
    }

    .fi-custom-footer__link {
      color: #fff;
      text-decoration: none;
      opacity: .95;
    }

    .fi-custom-footer__link:hover {
      text-decoration: underline;
      opacity: 1;
    }

    .fi-custom-footer__copy {
      font-size: 13px;
      opacity: .95;
      white-space: nowrap;
    }

    /* Responsive */
    @media (max-width: 640px) {
      .fi-custom-footer__inner {
        flex-direction: column;
        align-items: flex-start;
      }

      .fi-custom-footer__nav {
        flex-wrap: wrap;
        gap: 14px;
      }
    }
  </style>
</footer>