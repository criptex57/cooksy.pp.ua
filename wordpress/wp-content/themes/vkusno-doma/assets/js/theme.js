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
});
