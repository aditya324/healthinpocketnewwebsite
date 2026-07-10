<header class="bg-white border-b-2 border-hp-blue">
  <nav class="max-w-7xl mx-auto px-6 lg:px-8" aria-label="Main navigation">
    <div class="grid grid-cols-[1fr_auto_1fr] items-center h-[72px]">

      <!-- Logo -->
      <a href="/" class="flex items-center shrink-0 justify-self-start">
        <img
          src="./assets/images/logo/logo-light.png"
          alt="Healthin Pocket"
          class="h-10 sm:h-11 w-auto"
          width="160"
          height="54"
        >
      </a>

      <!-- Desktop nav links -->
      <ul class="hidden lg:flex items-center gap-10 justify-self-center">
        <li><a href="#" class="text-hp-dark text-[15px] font-medium hover:text-hp-blue transition-colors">Home</a></li>
        <li><a href="#" class="text-hp-dark text-[15px] font-medium hover:text-hp-blue transition-colors">Pricing</a></li>
        <li><a href="#" class="text-hp-dark text-[15px] font-medium hover:text-hp-blue transition-colors">About</a></li>
        <li><a href="#" class="text-hp-dark text-[15px] font-medium hover:text-hp-blue transition-colors">Contact</a></li>
        <li class="relative group">
          <button type="button" class="flex items-center gap-1.5 text-hp-dark text-[15px] font-medium hover:text-hp-blue transition-colors cursor-pointer">
            Pages
            <svg class="w-3.5 h-3.5 mt-0.5 transition-transform group-hover:rotate-180" viewBox="0 0 12 12" fill="none" aria-hidden="true">
              <path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
          <ul class="absolute top-full left-1/2 -translate-x-1/2 pt-3 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 min-w-[180px]">
            <li class="bg-white rounded-lg shadow-lg border border-gray-100 py-2">
              <a href="#" class="block px-4 py-2 text-sm text-hp-dark hover:bg-hp-blue/5 hover:text-hp-blue transition-colors">Services</a>
              <a href="#" class="block px-4 py-2 text-sm text-hp-dark hover:bg-hp-blue/5 hover:text-hp-blue transition-colors">Blog</a>
              <a href="#" class="block px-4 py-2 text-sm text-hp-dark hover:bg-hp-blue/5 hover:text-hp-blue transition-colors">FAQ</a>
            </li>
          </ul>
        </li>
      </ul>

      <!-- CTA + mobile toggle -->
      <div class="flex items-center gap-4 justify-self-end">
        <a href="#" class="hidden sm:inline-flex items-center justify-center bg-hp-blue text-white text-sm font-semibold tracking-wider uppercase px-6 py-2.5 rounded-full hover:bg-hp-blue-dark transition-colors">
          Book a Demo
        </a>

        <button type="button" id="mobile-menu-btn" class="lg:hidden p-2 text-hp-dark hover:text-hp-blue transition-colors" aria-label="Open menu" aria-expanded="false">
          <svg class="w-6 h-6" id="menu-icon-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round"/>
          </svg>
          <svg class="w-6 h-6 hidden" id="menu-icon-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobile-menu" class="lg:hidden hidden pb-6 border-t border-gray-100">
      <ul class="flex flex-col gap-1 pt-4">
        <li><a href="#" class="block px-2 py-2.5 text-hp-dark font-medium hover:text-hp-blue transition-colors">Home</a></li>
        <li><a href="#" class="block px-2 py-2.5 text-hp-dark font-medium hover:text-hp-blue transition-colors">Pricing</a></li>
        <li><a href="#" class="block px-2 py-2.5 text-hp-dark font-medium hover:text-hp-blue transition-colors">About</a></li>
        <li><a href="#" class="block px-2 py-2.5 text-hp-dark font-medium hover:text-hp-blue transition-colors">Contact</a></li>
        <li>
          <details class="group">
            <summary class="flex items-center justify-between px-2 py-2.5 text-hp-dark font-medium cursor-pointer list-none">
              Pages
              <svg class="w-3.5 h-3.5 transition-transform group-open:rotate-180" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                <path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </summary>
            <ul class="pl-4 pb-2">
              <li><a href="#" class="block py-2 text-sm text-hp-dark hover:text-hp-blue">Services</a></li>
              <li><a href="#" class="block py-2 text-sm text-hp-dark hover:text-hp-blue">Blog</a></li>
              <li><a href="#" class="block py-2 text-sm text-hp-dark hover:text-hp-blue">FAQ</a></li>
            </ul>
          </details>
        </li>
        <li class="pt-2 sm:hidden">
          <a href="#" class="inline-flex items-center justify-center w-full bg-hp-blue text-white text-sm font-semibold tracking-wider uppercase px-6 py-3 rounded-full hover:bg-hp-blue-dark transition-colors">
            Book a Demo
          </a>
        </li>
      </ul>
    </div>
  </nav>
</header>

<script>
  const menuBtn = document.getElementById('mobile-menu-btn');
  const mobileMenu = document.getElementById('mobile-menu');
  const iconOpen = document.getElementById('menu-icon-open');
  const iconClose = document.getElementById('menu-icon-close');

  menuBtn?.addEventListener('click', () => {
    const isOpen = !mobileMenu.classList.contains('hidden');
    mobileMenu.classList.toggle('hidden');
    iconOpen.classList.toggle('hidden', !isOpen);
    iconClose.classList.toggle('hidden', isOpen);
    menuBtn.setAttribute('aria-expanded', String(!isOpen));
  });
</script>
