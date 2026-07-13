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

<script>
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
