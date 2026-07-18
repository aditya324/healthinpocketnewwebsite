<?php include __DIR__ . '/includes/header.php'; ?>

<!-- ============ HERO ============ -->
<section class="relative overflow-hidden bg-white py-16 lg:py-24" id="hero">

  <!-- Decorative rings (bottom-left) -->
  <img src="./assets/images/rings.png" alt="" aria-hidden="true"
    class="pointer-events-none select-none absolute -bottom-22  left-0 w-40 sm:w-52 lg:w-64 opacity-90">

  <!-- Decorative rings (right, mirrored) -->
  <img src="./assets/images/rings.png" alt="" aria-hidden="true"
    class="pointer-events-none select-none absolute top-1/4 -translate-y-1/2 right-0 w-40 sm:w-52 lg:w-62 opacity-90 -scale-x-100">

  <div class="relative max-w-7xl mx-auto px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-[1fr_1.25fr] items-center gap-12 lg:gap-8">

    <!-- Left: content -->
    <div class="flex flex-col  lg:items-start text-center lg:text-left gap-7 lg:gap-8">

      <div class="inline-flex items-center gap-2.5 px-5 py-2 rounded-full border border-gray-400/60 text-hp-dark text-[15px] font-medium" data-reveal="up" data-reveal-load data-reveal-delay="0">
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
          <path d="M9.2 1.2a.6.6 0 0 1 .55.38l1.8 4.5 3.15-.9a.6.6 0 0 1 .74.74l-4.2 7.2a.6.6 0 0 1-1.04-.1L8.4 9.2 4.5 10.1a.6.6 0 0 1-.68-.82l4.4-8.1a.6.6 0 0 1 .98-.08Z"/>
        </svg>
        Say Hello To HIP
      </div>

      <h1 class="text-[2rem] sm:text-[2.75rem] lg:text-[2.25rem] xl:text-[2.75rem] font-medium leading-[1.15] tracking-tight" data-reveal="up" data-reveal-load data-reveal-delay="100">
        <span class="block lg:whitespace-nowrap"><span class="text-hp-dark">India's First </span><span class="text-hp-blue">White-Labelled</span></span>
        <span class="block lg:whitespace-nowrap text-hp-blue">Healthcare Growth OS</span>
      </h1>

      <p class="text-hp-muted text-lg sm:text-xl max-w-xl leading-relaxed" data-reveal="up" data-reveal-load data-reveal-delay="200">
        Helping hospitals retain patients, strengthen relationships,<br class="hidden sm:inline"> and grow sustainably.
      </p>

      <a href="#" class="inline-flex items-center gap-3 bg-hp-blue text-white text-[15px] font-semibold tracking-[0.12em] uppercase px-10 py-4 rounded-full hover:bg-hp-blue-dark transition-colors" data-reveal="up" data-reveal-load data-reveal-delay="300">
        Book a Demo
        <svg class="w-5 h-5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path d="M4 12L12 4M12 4H6M12 4V10" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </a>

    </div>

    <!-- Right: banner image -->
    <div class="relative" data-reveal="up" data-reveal-load data-reveal-delay="200">
      <img src="./assets/images/banner.png" alt="Doctor consulting with a patient"
        class="w-full max-w-3xl mx-auto lg:max-w-none lg:scale-125 lg:translate-x-10 lg:origin-left h-auto select-none">
    </div>

  </div>
</section>

<!-- ============ APP SHOWCASE ============ -->
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

<!-- ============ PLATFORM ============ -->
<section class="relative overflow-hidden bg-[linear-gradient(135deg,#EAF4FB_0%,#F7FAFD_45%,#EDF5FB_100%)] py-16 sm:py-20 lg:py-24" id="platform">
  <div class="absolute -bottom-24 -left-24 w-72 h-72 rounded-full  opacity-70 pointer-events-none" aria-hidden="true"></div>

  <!-- Rings straddling the seam: top halves (continue in workflows below) -->
  <img src="./assets/images/rings.png" alt="" aria-hidden="true"
    class="pointer-events-none select-none absolute bottom-0 translate-y-1/2 -left-24 w-64 sm:w-80 lg:w-[32rem] opacity-90">
  <img src="./assets/images/rings.png" alt="" aria-hidden="true"
    class="pointer-events-none select-none absolute bottom-0 translate-y-1/2 -right-24 w-64 sm:w-80 lg:w-[26rem] opacity-90 -scale-x-100">

  <div class="relative w-full mx-auto px-6 sm:px-10 lg:px-16 xl:px-20">

    <div class="text-center mb-10 lg:mb-14" data-reveal="up" data-reveal-delay="0">
      <h2 class="text-3xl sm:text-4xl lg:text-[2.5rem] font-bold tracking-tight leading-tight">
        <span class="block text-hp-dark">Everything Your Hospital Needs.</span>
        <span class="block text-hp-blue">One Connected Platform.</span>
      </h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-6 max-w-6xl mx-auto">

      <article class="bg-white/40 backdrop-blur-md border border-white/60 rounded-2xl shadow-[0_8px_30px_rgba(16,24,40,0.08)] p-8 flex flex-col items-center text-center" data-reveal="up" data-reveal-delay="60">
        <img src="./assets/icons/Background.png" alt="" aria-hidden="true" class="w-16 h-16 rounded-xl mb-5">
        <h3 class="text-hp-dark text-base sm:text-lg font-semibold tracking-tight">Multi-Speciality</h3>
      </article>

      <article class="bg-white/40 backdrop-blur-md border border-white/60 rounded-2xl shadow-[0_8px_30px_rgba(16,24,40,0.08)] p-8 flex flex-col items-center text-center" data-reveal="up" data-reveal-delay="140">
        <img src="./assets/icons/Background2.png" alt="" aria-hidden="true" class="w-16 h-16 rounded-xl mb-5">
        <h3 class="text-hp-dark text-base sm:text-lg font-semibold tracking-tight">Super Speciality</h3>
      </article>

      <article class="bg-white/40 backdrop-blur-md border border-white/60 rounded-2xl shadow-[0_8px_30px_rgba(16,24,40,0.08)] p-8 flex flex-col items-center text-center" data-reveal="up" data-reveal-delay="220">
        <img src="./assets/icons/Background3.png" alt="" aria-hidden="true" class="w-16 h-16 rounded-xl mb-5">
        <h3 class="text-hp-dark text-base sm:text-lg font-semibold tracking-tight">Clinics</h3>
      </article>

      <article class="bg-white/40 backdrop-blur-md border border-white/60 rounded-2xl shadow-[0_8px_30px_rgba(16,24,40,0.08)] p-8 flex flex-col items-center text-center" data-reveal="up" data-reveal-delay="300">
        <img src="./assets/icons/Background4.png" alt="" aria-hidden="true" class="w-16 h-16 rounded-xl mb-5">
        <h3 class="text-hp-dark text-base sm:text-lg font-semibold tracking-tight">Diagnostic Centers</h3>
      </article>

    </div>
  </div>
</section>

<!-- ============ WORKFLOWS ============ -->
<section class="relative overflow-hidden bg-white py-14 lg:py-20" id="workflows">

  <!-- Rings straddling the seam: bottom halves (continue from platform above) -->
  <img src="./assets/images/rings.png" alt="" aria-hidden="true"
    class="pointer-events-none select-none absolute top-0 -translate-y-1/2 -left-24 w-64 sm:w-80 lg:w-[32rem] opacity-90">
  <img src="./assets/images/rings.png" alt="" aria-hidden="true"
    class="pointer-events-none select-none absolute top-0 -translate-y-1/2 -right-24 w-64 sm:w-80 lg:w-[26rem] opacity-90 -scale-x-100">

  <div class="relative container w-full max-w-7xl mx-auto px-8 sm:px-12 lg:px-16 xl:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">

      <!-- Left: copy -->
      <div data-reveal="left" data-reveal-delay="0">
        <div class="inline-flex items-center px-3.5 py-1 rounded-full border border-hp-dark/80 text-hp-dark text-sm font-medium mb-4">
          Workflows
        </div>

        <h2 class="text-3xl sm:text-4xl lg:text-[2.5rem] font-bold leading-tight tracking-tight mb-4">
          <span class="block text-hp-dark">Healthcare Has Changed.</span>
          <span class="block text-hp-blue">Hospital Software Should Too.</span>
        </h2>

        <p class="text-hp-muted text-base leading-relaxed mb-5 max-w-lg">
          HealthInPocket brings everything together in one intelligent platform &mdash; helping hospitals build lasting patient relationships and achieve sustainable growth.
        </p>

        <ul class="flex flex-wrap gap-x-6 gap-y-2 mb-8">
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

      <!-- Right: photo with floating stat badges -->
      <div class="relative w-full max-w-xl mx-auto lg:max-w-none" data-reveal="right" data-reveal-delay="120">

        <img src="./assets/images/healthcaresoftware.png" alt="Doctor showing a tablet to a mother and her child"
          class="w-full h-auto rounded-[1.75rem] object-cover select-none">

        <!-- Badge: top-left -->
        <div class="absolute top-12 -left-4 sm:-left-10 flex items-center gap-4 bg-white/0 backdrop-blur-3xl rounded-[1.75rem] shadow-[0_16px_44px_rgba(16,24,40,0.16)] pl-4 pr-7 py-4" data-reveal="up" data-reveal-delay="300">
          <span class="flex items-center justify-center w-12 h-12 rounded-full bg-[#DCEFFB] shrink-0" aria-hidden="true">
            <svg class="w-5 h-5 text-hp-blue" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75">
              <path d="M2 11l4-4.5 3 2.5L14 4" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M10.5 4H14v3.5" stroke-linecap="round" stroke-linejoin="round"/> 
            </svg>
          </span>
          <span>
            <span class="block text-hp-dark text-lg font-bold leading-snug">+12.4%</span>
            <span class="block text-hp-muted text-sm leading-snug">Weekly Booking Increase</span>
          </span>
        </div>

        <!-- Badge: bottom-right -->
        <div class="absolute bottom-12 -right-12 sm:-right-6 flex  gap-4 bg-white/70 backdrop-blur-3xl rounded-[1.75rem] shadow-[0_16px_44px_rgba(16,24,40,0.16)] pl-4 pr-7 py-4" data-reveal="up" data-reveal-delay="420">
          <span class="flex items-center justify-center w-12 h-12 rounded-full bg-[#DCEFFB] shrink-0" aria-hidden="true">
            <svg class="w-5 h-5 text-hp-blue" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75">
              <path d="M2 11l4-4.5 3 2.5L14 4" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M10.5 4H14v3.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </span>
          <span>
            <span class="block text-hp-dark text-lg font-bold leading-snug">+12.4%</span>
            <span class="block text-hp-muted text-sm leading-snug">Weekly Booking Increase</span>
          </span>
        </div>

      </div>
    </div>
  </div>
</section>

<!-- ============ CTA ============ -->
<section class="relative overflow-hidden bg-[#41BCEC] pt-16 sm:pt-20 lg:pt-24" id="cta">
  <div
    class="absolute inset-0 pointer-events-none bg-[linear-gradient(180deg,#41BCEC_0%,#157AC0_100%)]"
    aria-hidden="true"
  ></div>

  <div class="relative w-full mx-auto px-6 sm:px-10 lg:px-16 xl:px-20 text-center">
    <h2 class="text-white text-5xl sm:text-5xl lg:text-[6.5rem] font-medium tracking-tight leading-tight mb-4" data-reveal="up" data-reveal-delay="0">
      Witness the Future
    </h2>

    <p class="text-white/90 text-xl sm:text-2xl max-w-2xl mx-auto leading-relaxed mb-8 lg:mb-10" data-reveal="up" data-reveal-delay="100">
      Schedule a personalized walkthrough of the HIP ecosystem with one of our Intelligence Architects.
    </p>

    <a
      href="#"
      class="inline-flex items-center gap-2.5 bg-white text-hp-dark text-sm font-semibold tracking-[0.12em] uppercase px-8 py-3.5 rounded-full hover:bg-white/90 transition-colors mb-12 lg:mb-16"
      data-reveal="up"
      data-reveal-delay="180"
    >
      Book a Demo
      <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path d="M4 8h8M9 4l4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </a>

    <!-- Preview card -->
    <div class="cta-preview mx-auto w-full max-w-3xl bg-white rounded-t-[1.75rem] sm:rounded-t-[2rem] shadow-[0_-8px_40px_rgba(0,0,0,0.12)] overflow-hidden" data-reveal="up" data-reveal-delay="280">
      <div class="flex items-center justify-between px-5 sm:px-7 pt-5 pb-4">
        <div class="flex items-center gap-3 min-w-0">
          <div
            class="w-10 h-10 sm:w-11 sm:h-11 rounded-full shrink-0 bg-gradient-to-br from-[#f9a8d4] via-[#93c5fd] to-[#c4b5fd] overflow-hidden"
            aria-hidden="true"
          >
            <div class="w-full h-full bg-[radial-gradient(circle_at_35%_30%,#fce7f3_0%,transparent_45%),linear-gradient(145deg,#fda4af_0%,#7dd3fc_55%,#a78bfa_100%)]"></div>
          </div>
          <div class="space-y-2 min-w-0">
            <div class="h-2 w-28 sm:w-36 rounded-full bg-gray-200"></div>
            <div class="h-2 w-16 sm:w-20 rounded-full bg-gray-100"></div>
          </div>
        </div>

        <button type="button" class="p-1.5 text-gray-400 hover:text-hp-dark transition-colors" aria-label="Menu">
          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
            <path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/>
          </svg>
        </button>
      </div>

      <div class="cta-art mx-4 sm:mx-6 mb-0 h-48 sm:h-64 lg:h-72 rounded-t-2xl" aria-hidden="true"></div>
    </div>
  </div>
</section>

<!-- ============ ECOSYSTEM ============ -->
<section class="relative overflow-hidden bg-[#F7F8FA] py-16 sm:py-20 lg:py-24" id="ecosystem">
  <div class="relative w-full max-w-7xl mx-auto px-6 sm:px-10 lg:px-12">

    <div class="text-center mb-10 lg:mb-14" data-reveal="up" data-reveal-delay="0">
      <p class="inline-block text-[#41BCEC] text-xs sm:text-sm font-semibold tracking-[0.2em] uppercase border-b-2 border-[#41BCEC] pb-1.5 mb-4">
        Our Ecosystem
      </p>
      <h2 class="text-3xl sm:text-4xl lg:text-[2.5rem] font-bold tracking-tight leading-tight">
        <span class="text-hp-dark">Powerful Features. </span><span class="text-hp-blue">One Intelligent Platform.</span>
      </h2>
    </div>

    <div class="eco-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">

      <!-- Patient Engagement — spans 2 -->
      <article class="eco-card lg:col-span-2 p-6 sm:p-8 flex flex-col justify-between min-h-[230px]" data-reveal="up" data-reveal-delay="60">
        <div class="eco-icon mb-6" aria-hidden="true">
          <svg class="w-5 h-5 text-[#157AC0]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
            <circle cx="12" cy="8" r="3.25"/>
            <path d="M5.5 19.5c1.2-3.2 3.5-4.8 6.5-4.8s5.3 1.6 6.5 4.8" stroke-linecap="round"/>
            <path d="M16.5 5.5c1.4.5 2.4 1.6 2.4 3s-1 2.5-2.4 3" stroke-linecap="round"/>
          </svg>
        </div>
        <div>
          <h3 class="text-hp-dark text-3xl sm:text-4xl lg:text-[2.75rem] font-bold tracking-tight leading-tight mb-3">
            Patient Engagement
          </h3>
          <p class="text-hp-muted text-sm sm:text-base leading-relaxed max-w-xl">
            Build lasting patient relationships with automated follow-ups, reminders, and personalized communication to improve retention.
          </p>
        </div>
      </article>

      <!-- HIP Coins -->
      <article class="eco-card p-6 sm:p-8 flex flex-col items-center justify-center text-center min-h-[230px]" data-reveal="up" data-reveal-delay="120">
        <div class="eco-icon eco-icon-lg mb-5" aria-hidden="true">
          <svg class="w-7 h-7 text-[#157AC0]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
            <rect x="3" y="6" width="18" height="12" rx="2"/>
            <circle cx="12" cy="12" r="2.5"/>
            <path d="M7 12h1.5M15.5 12H17" stroke-linecap="round"/>
          </svg>
        </div>
        <h3 class="text-hp-dark text-xl font-semibold tracking-tight mb-1.5">HIP Coins</h3>
        <p class="text-hp-muted text-sm leading-relaxed">Digital rewards &amp; secure payments.</p>
      </article>

      <!-- Smart Hospital Operations -->
      <article class="eco-card p-6 sm:p-8" data-reveal="up" data-reveal-delay="80">
        <div class="eco-icon mb-5" aria-hidden="true">
          <svg class="w-5 h-5 text-[#157AC0]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
            <path d="M4 20V7h16v13M4 20h16M2.5 20h19" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M12 10v4M10 12h4" stroke-linecap="round"/>
            <path d="M9 7V4.5h6V7" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <h3 class="text-hp-dark text-lg sm:text-xl font-semibold tracking-tight mb-2">Smart Hospital Operations</h3>
        <p class="text-hp-muted text-sm leading-relaxed">
          Manage appointments, EMR, billing, pharmacy, diagnostics, and administration through one connected platform.
        </p>
      </article>

      <!-- AI-Powered Automation -->
      <article class="eco-card p-6 sm:p-8" data-reveal="up" data-reveal-delay="140">
        <div class="eco-icon mb-5" aria-hidden="true">
          <svg class="w-5 h-5 text-[#157AC0]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
            <rect x="5" y="8" width="14" height="11" rx="2.5"/>
            <path d="M12 8V5M12 5a1.25 1.25 0 1 0 0-2.5A1.25 1.25 0 0 0 12 5Z" stroke-linecap="round"/>
            <path d="M9.25 13h.01M14.75 13h.01" stroke-width="2.5" stroke-linecap="round"/>
            <path d="M9.5 16h5" stroke-linecap="round"/>
          </svg>
        </div>
        <h3 class="text-hp-dark text-lg sm:text-xl font-semibold tracking-tight mb-2">AI-Powered Automation</h3>
        <p class="text-hp-muted text-sm leading-relaxed">
          Automate patient communication with AI Voice, AI Chat, WhatsApp, SMS, Email, and Push Notifications.
        </p>
      </article>

      <!-- White-Label Hospital App -->
      <article class="eco-card eco-card-tint p-6 sm:p-8" data-reveal="up" data-reveal-delay="200">
        <div class="eco-icon mb-5" aria-hidden="true">
          <svg class="w-5 h-5 text-[#157AC0]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
            <rect x="7" y="3" width="10" height="18" rx="2.5"/>
            <path d="M10.5 5.5h3" stroke-linecap="round"/>
            <path d="M12 18h.01" stroke-width="2.5" stroke-linecap="round"/>
          </svg>
        </div>
        <h3 class="text-hp-dark text-lg sm:text-xl font-semibold tracking-tight mb-2">White-Label Hospital App</h3>
        <p class="text-hp-muted text-sm leading-relaxed">
          Launch your own branded patient app, portal, and digital healthcare ecosystem under your hospital's identity.
        </p>
      </article>

      <!-- RFID Family Health Card -->
      <article class="eco-card p-6 sm:p-8" data-reveal="up" data-reveal-delay="80">
        <div class="eco-icon mb-5" aria-hidden="true">
          <svg class="w-5 h-5 text-[#157AC0]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
            <rect x="3" y="5" width="18" height="14" rx="2"/>
            <path d="M7 12a5 5 0 0 1 5-5M9.5 12a2.5 2.5 0 0 1 2.5-2.5" stroke-linecap="round"/>
            <circle cx="12" cy="12" r="0.5" fill="currentColor"/>
          </svg>
        </div>
        <h3 class="text-hp-dark text-lg sm:text-xl font-semibold tracking-tight mb-2">RFID Family Health Card</h3>
        <p class="text-hp-muted text-sm leading-relaxed">
          Provide patients with a smart digital health identity for faster registration and stronger family engagement.
        </p>
      </article>

      <!-- Patient Relationship CRM -->
      <article class="eco-card p-6 sm:p-8" data-reveal="up" data-reveal-delay="140">
        <div class="eco-icon mb-5" aria-hidden="true">
          <svg class="w-5 h-5 text-[#157AC0]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
            <path d="M4 6h16v10H9l-4 3.5V6Z" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M8 10h8M8 12.5h5" stroke-linecap="round"/>
          </svg>
        </div>
        <h3 class="text-hp-dark text-lg sm:text-xl font-semibold tracking-tight mb-2">Patient Relationship CRM</h3>
        <p class="text-hp-muted text-sm leading-relaxed">
          Manage patient interactions, follow-ups, loyalty programs, and long-term engagement from one place.
        </p>
      </article>

      <!-- Patient Relationship CRM — highlighted -->
      <article class="eco-card eco-card-blue p-6 sm:p-8 flex flex-col items-center justify-center text-center" data-reveal="up" data-reveal-delay="200">
        <h3 class="text-white text-xl sm:text-2xl font-semibold tracking-tight mb-2">Patient Relationship CRM</h3>
        <p class="text-white/90 text-sm leading-relaxed">
          Manage patient interactions, follow-ups, loyalty programs, and long-term engagement from one place.
        </p>
      </article>

      <!-- WhatsApp Automation -->
      <article class="eco-card p-6 sm:p-8" data-reveal="up" data-reveal-delay="80">
        <div class="eco-icon mb-5" aria-hidden="true">
          <svg class="w-5 h-5 text-[#157AC0]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
            <path d="M12 4a8 8 0 0 1 6.9 12L20 20l-4.2-1A8 8 0 1 1 12 4Z" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M9.5 10c.5 2 2 3.5 4 4l1-1.5 2 1" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <h3 class="text-hp-dark text-lg sm:text-xl font-semibold tracking-tight mb-2">WhatsApp Automation</h3>
        <p class="text-hp-muted text-sm leading-relaxed">
          Deliver appointment reminders, follow-ups, campaigns, and instant patient communication through WhatsApp.
        </p>
      </article>

      <!-- Smart Follow-Ups -->
      <article class="eco-card p-6 sm:p-8" data-reveal="up" data-reveal-delay="140">
        <div class="eco-icon mb-5" aria-hidden="true">
          <svg class="w-5 h-5 text-[#157AC0]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
            <rect x="3.5" y="5" width="17" height="15" rx="2"/>
            <path d="M3.5 9.5h17M8 3.5v3M16 3.5v3" stroke-linecap="round"/>
            <path d="M9 14.5l2 2 4-4" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <h3 class="text-hp-dark text-lg sm:text-xl font-semibold tracking-tight mb-2">Smart Follow-Ups</h3>
        <p class="text-hp-muted text-sm leading-relaxed">
          Automatically schedule and manage follow-ups to improve continuity of care and reduce missed visits.
        </p>
      </article>

      <!-- Phone visual — spans 2 rows -->
      <article class="relative lg:row-span-2 rounded-[1.25rem] overflow-hidden" data-reveal="up" data-reveal-delay="200">
        <img src="./assets/images/mobile.png" alt="HealthInPocket mobile app preview"
          class="w-full max-w-full h-auto lg:absolute lg:inset-0 lg:w-full lg:h-full lg:object-cover select-none">
      </article>

      <!-- Executive Analytics -->
      <article class="eco-card p-6 sm:p-8" data-reveal="up" data-reveal-delay="80">
        <div class="eco-icon mb-5" aria-hidden="true">
          <svg class="w-5 h-5 text-[#157AC0]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
            <path d="M4 19V5M4 19h16" stroke-linecap="round"/>
            <path d="M8 15l3.5-4.5 3 2.5L18 7" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <h3 class="text-hp-dark text-lg sm:text-xl font-semibold tracking-tight mb-2">Executive Analytics</h3>
        <p class="text-hp-muted text-sm leading-relaxed">
          Monitor patient retention, revenue, operational performance, and business growth with real-time dashboards.
        </p>
      </article>

      <!-- Multi-Branch Management -->
      <article class="eco-card p-6 sm:p-8" data-reveal="up" data-reveal-delay="140">
        <div class="eco-icon mb-5" aria-hidden="true">
          <svg class="w-5 h-5 text-[#157AC0]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
            <path d="M4 20V8l5-3.5V20M9 20V4.5L20 8v12" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M2.5 20h19" stroke-linecap="round"/>
          </svg>
        </div>
        <h3 class="text-hp-dark text-lg sm:text-xl font-semibold tracking-tight mb-2">Multi-Branch Management</h3>
        <p class="text-hp-muted text-sm leading-relaxed">
          Manage multiple hospitals or clinics through one centralized platform with unified reporting.
        </p>
      </article>

      <!-- Multi-Branch Management (bottom-left) -->
      <article class="eco-card p-6 sm:p-8" data-reveal="up" data-reveal-delay="80">
        <div class="eco-icon mb-5" aria-hidden="true">
          <svg class="w-5 h-5 text-[#157AC0]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
            <path d="M4 20V8l5-3.5V20M9 20V4.5L20 8v12" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M2.5 20h19" stroke-linecap="round"/>
          </svg>
        </div>
        <h3 class="text-hp-dark text-lg sm:text-xl font-semibold tracking-tight mb-2">Multi-Branch Management</h3>
        <p class="text-hp-muted text-sm leading-relaxed">
          Manage multiple hospitals or clinics through one centralized platform with unified reporting.
        </p>
      </article>

      <!-- Enterprise-Grade Security — wide, right -->
      <article class="eco-card md:col-span-2 lg:col-start-2 lg:col-span-2 p-6 sm:p-8" data-reveal="up" data-reveal-delay="160">
        <div class="eco-icon mb-5" aria-hidden="true">
          <svg class="w-5 h-5 text-[#157AC0]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
            <path d="M12 3.5l7 2.5v5.5c0 4.2-2.9 7.4-7 9-4.1-1.6-7-4.8-7-9V6l7-2.5Z" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M9 12l2 2 4-4" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <h3 class="text-hp-dark text-lg sm:text-xl font-semibold tracking-tight mb-2">Enterprise-Grade Security</h3>
        <p class="text-hp-muted text-sm leading-relaxed">
          Protect patient data with encryption, role-based access, audit logs, secure authentication, and compliance-ready infrastructure.
        </p>
      </article>

    </div>
  </div>
</section>

<!-- ============ COMPARISON ============ -->
<section class="relative overflow-hidden bg-[linear-gradient(135deg,#EAF4FB_0%,#FFFFFF_40%,#FFFFFF_70%,#EDF5FB_100%)] py-16 sm:py-20 lg:py-24" id="comparison">
  <div class="container w-full max-w-6xl mx-auto px-6 sm:px-10 lg:px-16">

    <div class="text-center mb-10 lg:mb-14" data-reveal="up" data-reveal-delay="0">
      <h2 class="text-3xl sm:text-4xl lg:text-[2.5rem] font-bold tracking-tight leading-tight">
        <span class="block text-hp-dark">More than Hospital Management.</span>
        <span class="block text-hp-blue">Built for Hospital Growth.</span>
      </h2>
    </div>

    <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgba(16,24,40,0.08)] overflow-hidden" data-reveal="up" data-reveal-delay="120">

      <!-- Header row -->
      <div class="grid grid-cols-2 bg-[#EAF6FC]">
        <div class="px-6 sm:px-10 lg:px-16 py-4 sm:py-5">
          <span class="text-hp-dark text-sm sm:text-base font-bold tracking-tight">Traditional HMS</span>
        </div>
        <div class="px-6 sm:px-10 lg:px-16 py-4 sm:py-5">
          <span class="text-hp-blue text-sm sm:text-base font-bold tracking-tight">HealthInPocket OS</span>
        </div>
      </div>

      <!-- Rows -->
      <div class="divide-y divide-gray-100">
        <div class="grid grid-cols-2 items-center">
          <div class="px-6 sm:px-10 lg:px-16 py-5 sm:py-6 text-hp-muted text-sm sm:text-[15px]">Manages hospital operations</div>
          <div class="px-6 sm:px-10 lg:px-16 py-5 sm:py-6 text-hp-blue text-sm sm:text-[15px] font-semibold">Drives hospital growth</div>
        </div>
        <div class="grid grid-cols-2 items-center">
          <div class="px-6 sm:px-10 lg:px-16 py-5 sm:py-6 text-hp-muted text-sm sm:text-[15px]">Ends after treatment</div>
          <div class="px-6 sm:px-10 lg:px-16 py-5 sm:py-6 text-hp-blue text-sm sm:text-[15px] font-semibold">Builds lifelong patient relationships</div>
        </div>
        <div class="grid grid-cols-2 items-center">
          <div class="px-6 sm:px-10 lg:px-16 py-5 sm:py-6 text-hp-muted text-sm sm:text-[15px]">Multiple disconnected systems</div>
          <div class="px-6 sm:px-10 lg:px-16 py-5 sm:py-6 text-hp-blue text-sm sm:text-[15px] font-semibold">One connected healthcare ecosystem</div>
        </div>
        <div class="grid grid-cols-2 items-center">
          <div class="px-6 sm:px-10 lg:px-16 py-5 sm:py-6 text-hp-muted text-sm sm:text-[15px]">Administrative software</div>
          <div class="px-6 sm:px-10 lg:px-16 py-5 sm:py-6 text-hp-blue text-sm sm:text-[15px] font-semibold">Healthcare Growth Operating System</div>
        </div>
        <div class="grid grid-cols-2 items-center">
          <div class="px-6 sm:px-10 lg:px-16 py-5 sm:py-6 text-hp-muted text-sm sm:text-[15px]">Focuses on records</div>
          <div class="px-6 sm:px-10 lg:px-16 py-5 sm:py-6 text-hp-blue text-sm sm:text-[15px] font-semibold">Focuses on patient retention and business growth</div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ============ IMPACT CALCULATOR ============ -->
<section class=" flex justify-center items-center overflow-hidden bg-white py-16 sm:py-20 lg:py-24" id="impact-calculator">
  <div class="container w-full lg:px-14 max-w-7xl ">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">

      <div data-reveal="left" data-reveal-delay="0">
        <h2 class="text-hp-dark  text-3xl sm:text-4xl lg:text-[2.5rem] font-semibold tracking-tight mb-4">
          Impact Calculator
        </h2>
        <p class="text-hp-muted  text-xl sm:text-xl leading-relaxed max-w-fit">
          Visualize your growth with HIP's intelligence platform. Real-time data modeling for elite practices.
        </p>
      </div>

      <div class="impact-card w-full max-w-xl lg:max-w-none lg:ml-auto p-6 sm:p-8" data-reveal="right" data-reveal-delay="120">
        <label for="patient-slider" class="block text-hp-dark text-xs font-semibold tracking-[0.16em] uppercase mb-5">
          Monthly Patients
        </label>

        <div class="mb-2">
          <input
            id="patient-slider"
            class="impact-slider w-full"
            type="range"
            min="500"
            max="5000"
            step="50"
            value="1000"
            aria-valuemin="500"
            aria-valuemax="5000"
            aria-valuenow="1000"
            aria-label="Monthly patients"
          />
        </div>

        <div class="flex justify-between text-hp-dark text-sm font-semibold mb-8 sm:mb-10">
          <span>500</span>
          <span>5000+</span>
        </div>

        <div class="grid grid-cols-2 gap-6 sm:gap-8">
          <div>
            <p class="text-hp-muted text-sm mb-2">Estimated Revenue Lift</p>
            <p id="impact-revenue" class="text-[#41BCEC] text-2xl sm:text-3xl lg:text-[2rem] font-bold tracking-tight">
              142,000/-
            </p>
          </div>
          <div>
            <p class="text-hp-muted text-sm mb-2">Time Saved / Week</p>
            <p id="impact-time" class="text-[#41BCEC] text-2xl sm:text-3xl lg:text-[2rem] font-bold tracking-tight">
              24hrs
            </p>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ============ OUTCOMES ============ -->
<section class="relative overflow-hidden bg-[linear-gradient(135deg,#EAF4FB_0%,#F0F7FC_50%,#EDF5FB_100%)] py-16 sm:py-20 lg:py-24" id="outcomes">
  <div class="container w-full max-w-6xl mx-auto px-6 sm:px-10 lg:px-16">

    <div class="text-center mb-10 lg:mb-14" data-reveal="up" data-reveal-delay="0">
      <h2 class="text-3xl sm:text-4xl lg:text-[2.5rem] font-bold tracking-tight leading-tight">
        <span class="text-hp-dark">Deliver </span><span class="text-hp-blue">Better Outcomes</span><span class="text-hp-dark"> Across Your Hospital</span>
      </h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 sm:gap-6">

      <article class="bg-white rounded-2xl shadow-[0_8px_30px_rgba(16,24,40,0.06)] p-8 sm:p-10 flex flex-col items-center text-center" data-reveal="up" data-reveal-delay="60">
        <span class="flex items-center justify-center w-16 h-16 rounded-full bg-[#DCEFFB] mb-6" aria-hidden="true">
          <svg class="w-7 h-7 text-hp-blue" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
            <circle cx="9" cy="9" r="2.75"/>
            <path d="M4 18.5c1-2.7 2.9-4 5-4s4 1.3 5 4" stroke-linecap="round"/>
            <circle cx="16.5" cy="8" r="2.25"/>
            <path d="M16.5 13.5c1.8 0 3.4 1.1 4.2 3.3" stroke-linecap="round"/>
          </svg>
        </span>
        <h3 class="text-hp-dark text-lg sm:text-xl font-semibold tracking-tight mb-2.5">Patient Loyalty</h3>
        <p class="text-hp-muted text-sm leading-relaxed">
          Turn one-time patients into lifetime advocates through superior digital convenience.
        </p>
      </article>

      <article class="bg-white rounded-2xl shadow-[0_8px_30px_rgba(16,24,40,0.06)] p-8 sm:p-10 flex flex-col items-center text-center" data-reveal="up" data-reveal-delay="140">
        <span class="flex items-center justify-center w-16 h-16 rounded-full bg-[#DCEFFB] mb-6" aria-hidden="true">
          <svg class="w-7 h-7 text-hp-blue" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
            <rect x="3" y="6.5" width="18" height="11" rx="2"/>
            <circle cx="12" cy="12" r="2.5"/>
            <path d="M6.5 9.5h.01M17.5 14.5h.01" stroke-width="2.5" stroke-linecap="round"/>
          </svg>
        </span>
        <h3 class="text-hp-dark text-lg sm:text-xl font-semibold tracking-tight mb-2.5">Zero Revenue Leakage</h3>
        <p class="text-hp-muted text-sm leading-relaxed">
          Automated billing and digital payments ensure every penny is tracked and accounted for.
        </p>
      </article>

      <article class="bg-white rounded-2xl shadow-[0_8px_30px_rgba(16,24,40,0.06)] p-8 sm:p-10 flex flex-col items-center text-center" data-reveal="up" data-reveal-delay="220">
        <span class="flex items-center justify-center w-16 h-16 rounded-full bg-[#DCEFFB] mb-6" aria-hidden="true">
          <svg class="w-7 h-7 text-hp-blue" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
            <path d="M13 4.5c3.5.5 6 3 6.5 6.5l-4 4c-.5.5-1.2.7-1.9.5l-3.1-.9-.9-3.1c-.2-.7 0-1.4.5-1.9l2.9-5.1Z" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M9.5 14.5L4.5 19.5M8 11.5l-3.5 1L7 10M12.5 16l-1 3.5 2.5-2.5" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="14.5" cy="9.5" r="1.25"/>
          </svg>
        </span>
        <h3 class="text-hp-dark text-lg sm:text-xl font-semibold tracking-tight mb-2.5">Rapid Scaling</h3>
        <p class="text-hp-muted text-sm leading-relaxed">
          Standardize operations across multiple branches with a single unified dashboard.
        </p>
      </article>

    </div>
  </div>
</section>

<!-- ============ REQUEST ACCESS ============ -->
<section class="relative overflow-hidden bg-[linear-gradient(135deg,#EAF4FB_0%,#F7FAFD_45%,#EDF5FB_100%)] py-16 sm:py-20 lg:py-24" id="request-access">
  <div class="relative w-full max-w-6xl mx-auto px-6 sm:px-10 lg:px-16">

    <div class="access-card w-full p-6 sm:p-10 lg:p-12" data-reveal="scale" data-reveal-delay="0">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-14 items-center">

        <!-- Left: copy + form -->
        <div>
          <h2 class="text-hp-dark text-3xl sm:text-4xl font-bold tracking-tight mb-3">
            Witness the Future
          </h2>
          <p class="text-hp-muted text-sm sm:text-base leading-relaxed max-w-md mb-10">
            Schedule a personalized walkthrough of the HIP ecosystem with one of our Intelligence Architects.
          </p>

          <form class="text-left" action="#" method="post">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6 mb-6">
              <div>
                <label for="access-name" class="sr-only">Full Name</label>
                <input
                  id="access-name"
                  name="full_name"
                  type="text"
                  autocomplete="name"
                  placeholder="Full Name"
                  class="access-input"
                  required
                />
              </div>
              <div>
                <label for="access-email" class="sr-only">Work Email</label>
                <input
                  id="access-email"
                  name="work_email"
                  type="email"
                  autocomplete="email"
                  placeholder="Work Email"
                  class="access-input"
                  required
                />
              </div>
            </div>

            <div class="mb-10">
              <label for="access-org" class="sr-only">Organization Name</label>
              <input
                id="access-org"
                name="organization"
                type="text"
                autocomplete="organization"
                placeholder="Organization Name"
                class="access-input"
                required
              />
            </div>

            <button
              type="submit"
              class="w-full inline-flex justify-center items-center bg-[linear-gradient(180deg,#41BCEC_0%,#157AC0_100%)] text-white text-base font-medium px-6 py-3.5 rounded-full hover:opacity-90 transition-opacity"
            >
              Request Access
            </button>
          </form>
        </div>

        <!-- Right: image -->
        <div data-reveal="right" data-reveal-delay="120">
          <img src="./assets/images/contact.png" alt="Family on a video call with a doctor"
            class="w-full h-auto rounded-[1.25rem] object-cover select-none">
        </div>

      </div>
    </div>

  </div>
</section>

<!-- ============ PRICING ============ -->
<!-- <section class="relative overflow-hidden bg-[#F7F8FA] py-16 sm:py-20 lg:py-24" id="pricing">
  <div class="relative w-full mx-auto px-6 sm:px-10 lg:px-16 xl:px-20">

    <h2 class="text-center text-hp-dark text-3xl sm:text-4xl lg:text-[2.75rem] font-bold tracking-tight mb-12 lg:mb-16" data-reveal="up" data-reveal-delay="0">
      Elite Plans
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 items-stretch max-w-6xl mx-auto">


      <article class="plan-card flex flex-col p-7 sm:p-8" data-reveal="up" data-reveal-delay="80">
        <p class="text-gray-400 text-xs font-semibold tracking-[0.18em] uppercase mb-4">Starter</p>
        <p class="text-hp-dark mb-8">
          <span class="text-4xl sm:text-5xl font-bold tracking-tight">499</span>
          <span class="text-hp-muted text-base font-medium">/mo</span>
        </p>

        <ul class="space-y-3.5 mb-10 flex-1">
          <li class="flex items-center gap-3 text-hp-dark text-sm sm:text-[15px]">
            <span class="plan-check plan-check-outline" aria-hidden="true">
              <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2.5 6L5 8.5L9.5 3.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            Up to 500 Patients
          </li>
          <li class="flex items-center gap-3 text-hp-dark text-sm sm:text-[15px]">
            <span class="plan-check plan-check-outline" aria-hidden="true">
              <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2.5 6L5 8.5L9.5 3.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            Core Dashboard
          </li>
          <li class="flex items-center gap-3 text-hp-dark text-sm sm:text-[15px]">
            <span class="plan-check plan-check-outline" aria-hidden="true">
              <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2.5 6L5 8.5L9.5 3.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            Standard AI Metrics
          </li>
        </ul>

        <a href="#" class="inline-flex justify-center items-center w-full border border-[#41BCEC] text-[#41BCEC] text-sm font-semibold px-6 py-3 rounded-full hover:bg-[#41BCEC]/5 transition-colors">
          Select Plan
        </a>
      </article>

     
      <article class="plan-card plan-card-featured relative flex flex-col p-7 sm:p-8 md:-mt-3 md:mb-[-0.75rem]" data-reveal="up" data-reveal-delay="160">
        <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-[#41BCEC] text-white text-[10px] sm:text-xs font-semibold tracking-[0.14em] uppercase px-3.5 py-1.5 rounded-full whitespace-nowrap">
          Most Popular
        </span>

        <p class="text-[#41BCEC] text-xs font-semibold tracking-[0.18em] uppercase mb-4 mt-2">Growth</p>
        <p class="text-hp-dark mb-8">
          <span class="text-4xl sm:text-5xl font-bold tracking-tight">1,299</span>
          <span class="text-hp-muted text-base font-medium">/mo</span>
        </p>

        <ul class="space-y-3.5 mb-10 flex-1">
          <li class="flex items-center gap-3 text-hp-dark text-sm sm:text-[15px]">
            <span class="plan-check plan-check-solid" aria-hidden="true">
              <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2.5 6L5 8.5L9.5 3.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            Unlimited Patients
          </li>
          <li class="flex items-center gap-3 text-hp-dark text-sm sm:text-[15px]">
            <span class="plan-check plan-check-solid" aria-hidden="true">
              <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2.5 6L5 8.5L9.5 3.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            Full White-label Suite
          </li>
          <li class="flex items-center gap-3 text-hp-dark text-sm sm:text-[15px]">
            <span class="plan-check plan-check-solid" aria-hidden="true">
              <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2.5 6L5 8.5L9.5 3.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            Advanced AI Predictor
          </li>
          <li class="flex items-center gap-3 text-hp-dark text-sm sm:text-[15px]">
            <span class="plan-check plan-check-solid" aria-hidden="true">
              <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2.5 6L5 8.5L9.5 3.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            Priority API Access
          </li>
        </ul>

        <a href="#" class="inline-flex justify-center items-center w-full bg-[linear-gradient(90deg,#41BCEC_0%,#157AC0_100%)] text-white text-sm font-semibold px-6 py-3 rounded-full hover:opacity-90 transition-opacity">
          Start 14-Day Trial
        </a>
      </article>

  
      <article class="plan-card flex flex-col p-7 sm:p-8" data-reveal="up" data-reveal-delay="240">
        <p class="text-gray-400 text-xs font-semibold tracking-[0.18em] uppercase mb-4">Enterprise</p>
        <p class="text-hp-dark text-4xl sm:text-5xl font-bold tracking-tight mb-8">Custom</p>

        <ul class="space-y-3.5 mb-10 flex-1">
          <li class="flex items-center gap-3 text-hp-dark text-sm sm:text-[15px]">
            <span class="plan-check plan-check-outline" aria-hidden="true">
              <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2.5 6L5 8.5L9.5 3.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            Dedicated Cloud Nodes
          </li>
          <li class="flex items-center gap-3 text-hp-dark text-sm sm:text-[15px]">
            <span class="plan-check plan-check-outline" aria-hidden="true">
              <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2.5 6L5 8.5L9.5 3.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            Custom AI Training
          </li>
          <li class="flex items-center gap-3 text-hp-dark text-sm sm:text-[15px]">
            <span class="plan-check plan-check-outline" aria-hidden="true">
              <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2.5 6L5 8.5L9.5 3.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            24/7 Concierge Support
          </li>
        </ul>

        <a href="#" class="inline-flex justify-center items-center w-full bg-white text-hp-dark text-sm font-semibold px-6 py-3 rounded-full shadow-[0_4px_16px_rgba(16,24,40,0.08)] hover:shadow-[0_6px_20px_rgba(16,24,40,0.12)] transition-shadow">
          Contact Sales
        </a>
      </article>

    </div>
  </div>
</section> -->

<!-- ============ FAQ ============ -->
<section class="relative overflow-hidden bg-[linear-gradient(135deg,#EAF4FB_0%,#F7FAFD_45%,#EDF5FB_100%)] py-16 sm:py-20 lg:py-24" id="faq">
  <div class="container w-full max-w-5xl mx-auto px-6 sm:px-10">

    <div class="text-center" data-reveal="up" data-reveal-delay="0">
      <div class="text-[15px] font-bold text-hp-blue tracking-widest uppercase mb-3.5">FAQ</div>
      <h2 class="text-hp-dark text-3xl sm:text-4xl lg:text-[2.5rem] font-bold tracking-tight leading-tight">
        Common Questions
      </h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mt-10 lg:mt-14">

      <details class="group bg-white rounded-2xl shadow-[0_4px_16px_rgba(16,24,40,0.06)]" data-reveal="up" data-reveal-delay="60">
        <summary class="flex items-center justify-between gap-4 cursor-pointer list-none px-6 sm:px-7 py-4 sm:py-5 [&::-webkit-details-marker]:hidden">
          <span class="text-hp-dark text-sm sm:text-[15px] font-semibold">Is Health in Pocket cloud-based?</span>
          <svg class="w-4 h-4 text-hp-dark shrink-0 transition-transform duration-200 group-open:rotate-45" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
            <path d="M8 3v10M3 8h10" stroke-linecap="round"/>
          </svg>
        </summary>
        <p class="px-6 sm:px-7 pb-5 -mt-1 text-hp-muted text-sm leading-relaxed">
          Yes. The entire platform runs securely in the cloud — accessible from anywhere, on any device. No on-premise servers required.
        </p>
      </details>

      <details class="group bg-white rounded-2xl shadow-[0_4px_16px_rgba(16,24,40,0.06)]" data-reveal="up" data-reveal-delay="100">
        <summary class="flex items-center justify-between gap-4 cursor-pointer list-none px-6 sm:px-7 py-4 sm:py-5 [&::-webkit-details-marker]:hidden">
          <span class="text-hp-dark text-sm sm:text-[15px] font-semibold">Can it integrate with existing systems?</span>
          <svg class="w-4 h-4 text-hp-dark shrink-0 transition-transform duration-200 group-open:rotate-45" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
            <path d="M8 3v10M3 8h10" stroke-linecap="round"/>
          </svg>
        </summary>
        <p class="px-6 sm:px-7 pb-5 -mt-1 text-hp-muted text-sm leading-relaxed">
          Yes. Health in Pocket supports integrations with existing healthcare workflows, lab equipment, and third-party systems including payment gateways.
        </p>
      </details>

      <details class="group bg-white rounded-2xl shadow-[0_4px_16px_rgba(16,24,40,0.06)]" data-reveal="up" data-reveal-delay="140">
        <summary class="flex items-center justify-between gap-4 cursor-pointer list-none px-6 sm:px-7 py-4 sm:py-5 [&::-webkit-details-marker]:hidden">
          <span class="text-hp-dark text-sm sm:text-[15px] font-semibold">How is patient data protected?</span>
          <svg class="w-4 h-4 text-hp-dark shrink-0 transition-transform duration-200 group-open:rotate-45" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
            <path d="M8 3v10M3 8h10" stroke-linecap="round"/>
          </svg>
        </summary>
        <p class="px-6 sm:px-7 pb-5 -mt-1 text-hp-muted text-sm leading-relaxed">
          We follow enterprise-grade security standards — encrypted storage, role-based access controls, and automated secure cloud backups for all data.
        </p>
      </details>

      <details class="group bg-white rounded-2xl shadow-[0_4px_16px_rgba(16,24,40,0.06)]" data-reveal="up" data-reveal-delay="180">
        <summary class="flex items-center justify-between gap-4 cursor-pointer list-none px-6 sm:px-7 py-4 sm:py-5 [&::-webkit-details-marker]:hidden">
          <span class="text-hp-dark text-sm sm:text-[15px] font-semibold">How long does implementation take?</span>
          <svg class="w-4 h-4 text-hp-dark shrink-0 transition-transform duration-200 group-open:rotate-45" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
            <path d="M8 3v10M3 8h10" stroke-linecap="round"/>
          </svg>
        </summary>
        <p class="px-6 sm:px-7 pb-5 -mt-1 text-hp-muted text-sm leading-relaxed">
          It depends on the size of your organization. Our team delivers a structured, milestone-based deployment with dedicated onboarding support throughout.
        </p>
      </details>

      <details class="group bg-white rounded-2xl shadow-[0_4px_16px_rgba(16,24,40,0.06)]" data-reveal="up" data-reveal-delay="220">
        <summary class="flex items-center justify-between gap-4 cursor-pointer list-none px-6 sm:px-7 py-4 sm:py-5 [&::-webkit-details-marker]:hidden">
          <span class="text-hp-dark text-sm sm:text-[15px] font-semibold">Is training included?</span>
          <svg class="w-4 h-4 text-hp-dark shrink-0 transition-transform duration-200 group-open:rotate-45" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
            <path d="M8 3v10M3 8h10" stroke-linecap="round"/>
          </svg>
        </summary>
        <p class="px-6 sm:px-7 pb-5 -mt-1 text-hp-muted text-sm leading-relaxed">
          Yes. Every implementation includes comprehensive onboarding sessions, user training, and dedicated support to help your teams get started confidently.
        </p>
      </details>

      <details class="group bg-white rounded-2xl shadow-[0_4px_16px_rgba(16,24,40,0.06)]" data-reveal="up" data-reveal-delay="260">
        <summary class="flex items-center justify-between gap-4 cursor-pointer list-none px-6 sm:px-7 py-4 sm:py-5 [&::-webkit-details-marker]:hidden">
          <span class="text-hp-dark text-sm sm:text-[15px] font-semibold">Can multiple branches share one platform?</span>
          <svg class="w-4 h-4 text-hp-dark shrink-0 transition-transform duration-200 group-open:rotate-45" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
            <path d="M8 3v10M3 8h10" stroke-linecap="round"/>
          </svg>
        </summary>
        <p class="px-6 sm:px-7 pb-5 -mt-1 text-hp-muted text-sm leading-relaxed">
          Absolutely — Health in Pocket is built for single clinics, multi-specialty hospitals, and large multi-location networks, all from one centralized platform.
        </p>
      </details>

      <details class="group bg-white rounded-2xl shadow-[0_4px_16px_rgba(16,24,40,0.06)]" data-reveal="up" data-reveal-delay="300">
        <summary class="flex items-center justify-between gap-4 cursor-pointer list-none px-6 sm:px-7 py-4 sm:py-5 [&::-webkit-details-marker]:hidden">
          <span class="text-hp-dark text-sm sm:text-[15px] font-semibold">Is this really white-labelled? Will patients see your brand or mine?</span>
          <svg class="w-4 h-4 text-hp-dark shrink-0 transition-transform duration-200 group-open:rotate-45" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
            <path d="M8 3v10M3 8h10" stroke-linecap="round"/>
          </svg>
        </summary>
        <p class="px-6 sm:px-7 pb-5 -mt-1 text-hp-muted text-sm leading-relaxed">
          100% your brand. Your logo, your colours, your hospital name — everywhere in the app. Patients will never know Health In Pocket powers it. It's your app, built on our infrastructure.
        </p>
      </details>

      <details class="group bg-white rounded-2xl shadow-[0_4px_16px_rgba(16,24,40,0.06)]" data-reveal="up" data-reveal-delay="340">
        <summary class="flex items-center justify-between gap-4 cursor-pointer list-none px-6 sm:px-7 py-4 sm:py-5 [&::-webkit-details-marker]:hidden">
          <span class="text-hp-dark text-sm sm:text-[15px] font-semibold">We already have an HIS/HMS. Will this conflict?</span>
          <svg class="w-4 h-4 text-hp-dark shrink-0 transition-transform duration-200 group-open:rotate-45" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
            <path d="M8 3v10M3 8h10" stroke-linecap="round"/>
          </svg>
        </summary>
        <p class="px-6 sm:px-7 pb-5 -mt-1 text-hp-muted text-sm leading-relaxed">
          Health In Pocket is not a replacement for your Hospital Information System. It sits on top as a patient-facing relationship and retention layer — the piece most HMS systems don't have. Integration options are discussed on the strategy call.
        </p>
      </details>

      <details class="group bg-white rounded-2xl shadow-[0_4px_16px_rgba(16,24,40,0.06)]" data-reveal="up" data-reveal-delay="380">
        <summary class="flex items-center justify-between gap-4 cursor-pointer list-none px-6 sm:px-7 py-4 sm:py-5 [&::-webkit-details-marker]:hidden">
          <span class="text-hp-dark text-sm sm:text-[15px] font-semibold">How long does it take to go live?</span>
          <svg class="w-4 h-4 text-hp-dark shrink-0 transition-transform duration-200 group-open:rotate-45" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
            <path d="M8 3v10M3 8h10" stroke-linecap="round"/>
          </svg>
        </summary>
        <p class="px-6 sm:px-7 pb-5 -mt-1 text-hp-muted text-sm leading-relaxed">
          Most hospitals go live within 30 days of signing. Our team handles setup, onboarding and staff training. You don't need a technical team internally.
        </p>
      </details>

      <details class="group bg-white rounded-2xl shadow-[0_4px_16px_rgba(16,24,40,0.06)]" data-reveal="up" data-reveal-delay="420">
        <summary class="flex items-center justify-between gap-4 cursor-pointer list-none px-6 sm:px-7 py-4 sm:py-5 [&::-webkit-details-marker]:hidden">
          <span class="text-hp-dark text-sm sm:text-[15px] font-semibold">What is the investment involved?</span>
          <svg class="w-4 h-4 text-hp-dark shrink-0 transition-transform duration-200 group-open:rotate-45" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
            <path d="M8 3v10M3 8h10" stroke-linecap="round"/>
          </svg>
        </summary>
        <p class="px-6 sm:px-7 pb-5 -mt-1 text-hp-muted text-sm leading-relaxed">
          Investment is based on your hospital's size, number of branches and modules required. We share exact pricing on the strategy call — after understanding your specific situation. There is no one-size-fits-all quote.
        </p>
      </details>

      <details class="group bg-white rounded-2xl shadow-[0_4px_16px_rgba(16,24,40,0.06)] lg:col-span-2" data-reveal="up" data-reveal-delay="460">
        <summary class="flex items-center justify-between gap-4 cursor-pointer list-none px-6 sm:px-7 py-4 sm:py-5 [&::-webkit-details-marker]:hidden">
          <span class="text-hp-dark text-sm sm:text-[15px] font-semibold">Is patient data secure and DPDP compliant?</span>
          <svg class="w-4 h-4 text-hp-dark shrink-0 transition-transform duration-200 group-open:rotate-45" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
            <path d="M8 3v10M3 8h10" stroke-linecap="round"/>
          </svg>
        </summary>
        <p class="px-6 sm:px-7 pb-5 -mt-1 text-hp-muted text-sm leading-relaxed">
          Yes. Health In Pocket is built with Indian data protection guidelines in mind. Patient consent is captured digitally, data is stored securely, and your hospital remains in control of all patient information.
        </p>
      </details>

    </div>

    <div class="mt-10 text-center" data-reveal="up" data-reveal-delay="500">
      <p class="text-lg text-hp-muted mb-4">Still have questions? Talk to our team on a free 30-minute strategy call.</p>
      <a href="#request-access" class="inline-flex items-center justify-center gap-2 bg-hp-blue text-white text-[15px] font-semibold tracking-[0.08em] uppercase px-8 py-3.5 rounded-full hover:bg-hp-blue-dark transition-colors">
        Book Your Strategy Call
        <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path d="M4 12L12 4M12 4H6M12 4V10" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </a>
    </div>

  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
