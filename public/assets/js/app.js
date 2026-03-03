document.addEventListener("DOMContentLoaded", function() {
  const splash = document.getElementById('splash');
  const splashDuration = 7000;

  function playSplash() {
    splash.style.display = 'block';

    const elements = splash.querySelectorAll('.text, .background, .frame, .particle');
    elements.forEach(el => {
      el.style.animation = 'none';
      el.offsetHeight;
      el.style.animation = '';
    });

    setTimeout(() => {
      splash.style.display = 'none';

      setTimeout(playSplash, 10);
    }, splashDuration);
  }

  playSplash();
});