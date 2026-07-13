(() => {
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const items = document.querySelectorAll('[data-reveal]');

  if (!items.length) return;

  if (reduced) {
    items.forEach((el) => el.classList.add('is-visible'));
    return;
  }

  const reveal = (el) => {
    const delay = Number(el.dataset.revealDelay || 0);
    window.setTimeout(() => el.classList.add('is-visible'), delay);
  };

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        reveal(entry.target);
        observer.unobserve(entry.target);
      });
    },
    { threshold: 0.15, rootMargin: '0px 0px -6% 0px' }
  );

  items.forEach((el) => {
    if (el.hasAttribute('data-reveal-load')) {
      reveal(el);
      return;
    }
    observer.observe(el);
  });
})();
