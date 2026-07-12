document.addEventListener('DOMContentLoaded', () => {
  const carousels = document.querySelectorAll('[data-carousel]');

  carousels.forEach((carousel) => {
    const track = carousel.querySelector('[data-carousel-track]');
    const prevButton = carousel.querySelector('[data-carousel-prev]');
    const nextButton = carousel.querySelector('[data-carousel-next]');

    if (!track || !prevButton || !nextButton) {
      return;
    }

    const getStep = () => {
      const firstCard = track.querySelector('.category-card');
      if (!firstCard) {
        return track.clientWidth * 0.8;
      }

      const trackStyles = window.getComputedStyle(track);
      const gap = Number.parseFloat(trackStyles.columnGap || trackStyles.gap || '0');
      return firstCard.getBoundingClientRect().width + gap;
    };

    const updateState = () => {
      const maxScroll = track.scrollWidth - track.clientWidth - 4;
      prevButton.disabled = track.scrollLeft <= 4;
      nextButton.disabled = track.scrollLeft >= maxScroll;
    };

    const scrollTrack = (direction) => {
      track.scrollBy({
        left: getStep() * direction * 2,
        behavior: 'smooth',
      });
    };

    prevButton.addEventListener('click', () => scrollTrack(-1));
    nextButton.addEventListener('click', () => scrollTrack(1));
    track.addEventListener('scroll', updateState, { passive: true });
    window.addEventListener('resize', updateState);

    updateState();
  });

  const revealTargets = document.querySelectorAll(
    '.hero-card, .section-head, .category-card, .recipe-card, .collection-card, .feature, .page-hero, .page-hero--split, .content-list__item, .single-layout, .single-post, .comments-block, .footer-grid'
  );

  if ('IntersectionObserver' in window) {
    const revealObserver = new IntersectionObserver(
      (entries, observer) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) {
            return;
          }

          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        });
      },
      {
        threshold: 0.12,
        rootMargin: '0px 0px -8% 0px',
      }
    );

    revealTargets.forEach((target, index) => {
      target.classList.add('reveal');
      target.style.transitionDelay = `${Math.min(index % 6, 5) * 60}ms`;
      revealObserver.observe(target);
    });
  } else {
    revealTargets.forEach((target) => {
      target.classList.add('is-visible');
    });
  }
});
