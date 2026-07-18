<!-- ============ FOOTER ============ -->
<footer class="bg-[#157AC0] text-white" id="footer">
  <div class="w-full container mx-auto px-6 sm:px-10 lg:px-16 xl:px-20 py-10 sm:py-12 lg:py-14">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-8" data-reveal="up" data-reveal-delay="0">

      <div class="max-w-xs">
        <a href="/" class="inline-block mb-4">
          <img
            src="./assets/images/logo/logo.png"
            alt="Healthin Pocket"
            class="h-11 sm:h-12 w-auto"
            width="180"
            height="60"
          >
        </a>
        <p class="text-white/75 text-sm leading-relaxed">
          Building the intelligent infrastructure for high-net-worth healthcare providers.
        </p>
      </div>

      <div>
        <h3 class="text-xs font-semibold tracking-[0.18em] uppercase mb-4">Product</h3>
        <ul class="space-y-2.5">
          <li><a href="#" class="text-white/80 text-sm hover:text-white transition-colors">Dashboard</a></li>
          <li><a href="#" class="text-white/80 text-sm hover:text-white transition-colors">Patient App</a></li>
          <li><a href="#" class="text-white/80 text-sm hover:text-white transition-colors">AI Core</a></li>
        </ul>
      </div>

      <div>
        <h3 class="text-xs font-semibold tracking-[0.18em] uppercase mb-4">Resources</h3>
        <ul class="space-y-2.5">
          <li><a href="#" class="text-white/80 text-sm hover:text-white transition-colors">API Docs</a></li>
          <li><a href="#" class="text-white/80 text-sm hover:text-white transition-colors">Security</a></li>
          <li><a href="#" class="text-white/80 text-sm hover:text-white transition-colors">Compliance</a></li>
        </ul>
      </div>

      <div class="sm:text-left lg:text-right flex flex-col justify-end lg:items-end">
        <p class="text-white/60 text-xs mb-3">
          &copy; 2024 HIP Intelligence. All rights reserved.
        </p>
        <div class="flex items-center gap-4">
          <a href="#" class="text-white/80 text-sm hover:text-white transition-colors">Privacy</a>
          <a href="#" class="text-white/80 text-sm hover:text-white transition-colors">Terms</a>
        </div>
      </div>

    </div>
  </div>
</footer>

<script src="./assets/js/reveal.js" defer></script>

<script>
  // Mobile menu toggle
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

  // App showcase animation
  (() => {
    const section = document.getElementById('app-showcase');
    if (!section) return;

    const items = section.querySelectorAll('[data-animate]');

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;

          items.forEach((item) => {
            const delay = Number(item.dataset.animateDelay || 0);
            setTimeout(() => item.classList.add('is-visible'), delay);
          });

          observer.unobserve(section);
        });
      },
      { threshold: 0.22, rootMargin: '0px 0px -8% 0px' }
    );

    observer.observe(section);
  })();

  // Workflows animation
  (() => {
    const section = document.getElementById('workflows');
    if (!section) return;

    const items = section.querySelectorAll('[data-wf-animate]');

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;

          items.forEach((item) => {
            const delay = Number(item.dataset.wfDelay || 0);
            setTimeout(() => item.classList.add('is-visible'), delay);
          });

          observer.unobserve(section);
        });
      },
      { threshold: 0.25, rootMargin: '0px 0px -8% 0px' }
    );

    observer.observe(section);
  })();

  // Impact calculator
  (() => {
    const slider = document.getElementById('patient-slider');
    const revenueEl = document.getElementById('impact-revenue');
    const timeEl = document.getElementById('impact-time');
    if (!slider || !revenueEl || !timeEl) return;

    const formatRevenue = (n) =>
      Math.round(n).toLocaleString('en-IN') + '/-';

    const update = () => {
      const patients = Number(slider.value);
      const revenue = patients * 142;
      const hours = Math.round(patients * 0.024);

      slider.setAttribute('aria-valuenow', String(patients));
      revenueEl.textContent = formatRevenue(revenue);
      timeEl.textContent = hours + 'hrs';

      const min = Number(slider.min);
      const max = Number(slider.max);
      const pct = ((patients - min) / (max - min)) * 100;
      slider.style.setProperty('--impact-pct', pct + '%');
    };

    slider.addEventListener('input', update);
    update();
  })();
</script>

</body>

</html>
