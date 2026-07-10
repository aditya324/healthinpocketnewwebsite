<section class="app-showcase relative overflow-hidden bg-white py-10 " id="app-showcase">
  <div class="showcase-glow absolute inset-0 pointer-events-none" aria-hidden="true"></div>

  <div class="relative max-w-6xl mx-auto px-6 lg:px-8 mt-10">
    <div class="showcase-stage relative flex justify-center items-center min-h-[620px] lg:min-h-[720px]">

      <!-- Layered background panels -->
      <div class="showcase-panel showcase-panel-1" data-animate data-animate-delay="0" aria-hidden="true"></div>
      <div class="showcase-panel showcase-panel-2" data-animate data-animate-delay="80" aria-hidden="true"></div>
      <div class="showcase-panel showcase-panel-3" data-animate data-animate-delay="160" aria-hidden="true"></div>

      <!-- Phone mockup -->
      <div class="showcase-phone relative z-10 w-[min(100%,340px)]" data-animate data-animate-delay="240">
        <div class="bg-white rounded-[2.5rem] shadow-[0_24px_80px_rgba(34,170,222,0.18)] border border-gray-100 overflow-hidden">

          <!-- Phone header -->
          <div class="flex items-center justify-between px-6 pt-5 pb-3">
            <svg class="w-5 h-5 text-hp-dark" viewBox="0 0 20 20" fill="none" aria-hidden="true">
              <path d="M4 4l5.5 5.5M11.5 4L6 9.5M6 10.5l5.5 5.5M11.5 10.5L6 16" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
            </svg>
            <button type="button" class="p-1" aria-label="Menu">
              <svg class="w-5 h-5 text-hp-dark" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                <path d="M3 5h14M3 10h14M3 15h14" stroke-linecap="round"/>
              </svg>
            </button>
          </div>

          <!-- Profile row -->
          <div class="flex items-center gap-3 px-6 pb-4" data-animate data-animate-delay="320">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-sky-200 to-sky-400 overflow-hidden shrink-0 flex items-center justify-center text-white text-xs font-semibold" aria-hidden="true">JD</div>
            <div class="space-y-1.5">
              <div class="h-2 w-16 rounded-full bg-gray-200"></div>
              <div class="h-2 w-10 rounded-full bg-gray-100"></div>
            </div>
          </div>

          <!-- Chat -->
          <div class="px-5 pb-6 space-y-3">

            <div class="flex justify-end" data-animate data-animate-delay="400">
              <div class="showcase-bubble showcase-bubble-user max-w-[82%] px-4 py-2.5 text-[13px] leading-snug text-hp-dark">
                How about a new image instead ?
              </div>
            </div>

            <div class="flex justify-end" data-animate data-animate-delay="520">
              <div class="showcase-bubble showcase-bubble-user max-w-[72%] px-4 py-2.5 text-[13px] leading-snug text-hp-dark">
                Maybe something blue
              </div>
            </div>

            <div class="flex justify-start" data-animate data-animate-delay="640">
              <div class="showcase-bubble showcase-bubble-bot max-w-[78%] px-4 py-2.5 text-[13px] leading-snug text-hp-dark">
                Wow! I already worked on one
              </div>
            </div>

            <div class="flex justify-start" data-animate data-animate-delay="760">
              <div class="showcase-bubble showcase-bubble-bot max-w-[62%] px-4 py-2.5 text-[13px] leading-snug text-hp-dark">
                Check this out !!
              </div>
            </div>

            <div data-animate data-animate-delay="880">
              <div class="showcase-abstract-art w-full aspect-square rounded-2xl overflow-hidden" role="img" aria-label="Abstract blue artwork"></div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
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
</script>
