<section class="relative overflow-hidden bg-white py-10 lg:py-14" id="workflows">
  <div class="absolute inset-0 pointer-events-none bg-[radial-gradient(ellipse_60%_50%_at_85%_20%,rgba(34,170,222,0.08)_0%,transparent_70%)]" aria-hidden="true"></div>

  <div class="container w-full mx-auto px-8 sm:px-12 lg:px-16 xl:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-20 items-center">

      <!-- Left: copy -->
      <div data-reveal="left" data-reveal-delay="0">
        <div class="inline-flex items-center px-3.5 py-1 rounded-full border border-hp-dark/80 text-hp-dark text-sm font-medium mb-4">
          Workflows
        </div>

        <h2 class="text-3xl sm:text-4xl lg:text-[2.5rem] font-bold text-hp-dark leading-tight tracking-tight mb-3">
          Faster and easier
        </h2>

        <p class="text-hp-muted text-base leading-relaxed mb-5">
          The world's first Liquid-Glass healthcare operating system. Unify patient flow, AI-driven diagnostics, and financial intelligence in a singular crystalline interface.
        </p>

        <ul class="flex flex-wrap gap-x-6 gap-y-2 mb-6">
          <li class="flex items-center gap-2.5 text-hp-dark text-sm sm:text-[15px] font-medium">
            <span class="flex items-center justify-center w-5 h-5 rounded-full bg-gray-100 text-gray-400 shrink-0" aria-hidden="true">
              <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2.5 6L5 8.5L9.5 3.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            Customization
          </li>
          <li class="flex items-center gap-2.5 text-hp-dark text-sm sm:text-[15px] font-medium">
            <span class="flex items-center justify-center w-5 h-5 rounded-full bg-gray-100 text-gray-400 shrink-0" aria-hidden="true">
              <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2.5 6L5 8.5L9.5 3.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            100% Responsive layouts
          </li>
        </ul>

        <a href="#" class="inline-flex items-center gap-2.5 bg-hp-blue text-white text-sm font-semibold tracking-[0.12em] uppercase px-8 py-3.5 rounded-full hover:bg-hp-blue-dark transition-colors">
          Book a Demo
          <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path d="M4 12L12 4M12 4H6M12 4V10" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </a>
      </div>

      <!-- Right: progress cards -->
      <div class="flex flex-col gap-3 w-full">

        <div class="workflow-card" data-wf-animate data-wf-delay="0">
          <div class="workflow-avatar workflow-avatar-1" aria-hidden="true"></div>
          <div class="workflow-bar">
            <div class="workflow-bar-fill bg-[#34C759]" style="--bar-width: 85%"></div>
          </div>
        </div>

        <div class="workflow-card lg:ml-10" data-wf-animate data-wf-delay="120">
          <div class="workflow-avatar workflow-avatar-2" aria-hidden="true"></div>
          <div class="workflow-bar">
            <div class="workflow-bar-fill bg-[#FF9F0A]" style="--bar-width: 68%"></div>
          </div>
        </div>

        <div class="workflow-card lg:ml-10" data-wf-animate data-wf-delay="240">
          <div class="workflow-avatar workflow-avatar-3" aria-hidden="true"></div>
          <div class="workflow-bar">
            <div class="workflow-bar-fill bg-[#FF2D55]" style="--bar-width: 52%"></div>
          </div>
        </div>

        <div class="workflow-card" data-wf-animate data-wf-delay="360">
          <div class="workflow-avatar workflow-avatar-4" aria-hidden="true"></div>
          <div class="workflow-bar">
            <div class="workflow-bar-fill bg-[#64D2FF]" style="--bar-width: 40%"></div>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<script>
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
</script>
