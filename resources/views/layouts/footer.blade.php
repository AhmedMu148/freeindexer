<footer id="footer" class="footer" data-background-color="black">
  <div class="container">
    <div class="row">
      <div class="col-lg-6">
        <nav class="navbar-expand-lg p-0">
          <div id="navbarNav">
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="footer-link nav-link" aria-current="page" href="{{ route('/') }}">Home</a>
              </li>
              <li class="nav-item">
                <a class="footer-link nav-link" href="{{ route('about') }}">About Us</a>
              </li>
              <li class="nav-item">
                <a class="footer-link nav-link" href="{{ route('contact.show') }}">Contact Us</a>
              </li>
            </ul>
          </div>
        </nav>
      </div>
      <div class="col-lg-6 text-lg-end">
        <div class="copyright mt-2">
          ©
          <script>document.write(new Date().getFullYear())</script>, Free Indexer
        </div>
      </div>
    </div>
  </div>
</footer>