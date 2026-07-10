<section class="relative overflow-hidden bg-[#F7F8FA] py-16 sm:py-20 lg:py-24" id="request-access">
  <div class="relative w-full mx-auto px-6 sm:px-10 lg:px-16 xl:px-20">

    <div class="access-card mx-auto w-full max-w-2xl px-8 sm:px-12 py-10 sm:py-12 text-center">
      <h2 class="text-hp-dark text-3xl sm:text-4xl font-bold tracking-tight mb-3">
        Witness the Future
      </h2>
      <p class="text-hp-muted text-sm sm:text-base leading-relaxed max-w-md mx-auto mb-10">
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

  </div>
</section>
