let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');
    const totalSlides = slides.length;
    const slidesContainer = document.getElementById('slides');
    const indicatorsContainer = document.getElementById('indicators');

    for (let i = 0; i < totalSlides; i++) {
      const indicator = document.createElement('button');
      indicator.classList.add('indicator');
      if (i === 0) indicator.classList.add('active');
      indicator.onclick = () => goToSlide(i);
      indicatorsContainer.appendChild(indicator);
    }

    const indicators = document.querySelectorAll('.indicator');

    function updateSlide() {
      slidesContainer.style.transform = `translateX(-${currentSlide * 100}%)`;
      
      indicators.forEach((indicator, index) => {
        indicator.classList.toggle('active', index === currentSlide);
      });
    }

    function nextSlide() {
      currentSlide = (currentSlide + 1) % totalSlides;
      updateSlide();
    }

    function goToSlide(index) {
      currentSlide = index;
      updateSlide();
    }

    setInterval(nextSlide, 8000);

    let touchStartX = 0;
    let touchEndX = 0;

    slidesContainer.addEventListener('touchstart', (e) => {
      touchStartX = e.changedTouches[0].screenX;
    });

    slidesContainer.addEventListener('touchend', (e) => {
      touchEndX = e.changedTouches[0].screenX;
      if (touchStartX - touchEndX > 50) {
        currentSlide = (currentSlide + 1) % totalSlides;
        updateSlide();
      }
      if (touchEndX - touchStartX > 50) {
        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        updateSlide();
      }
    });


    window.addEventListener('scroll', function () {
    const header = document.querySelector('header');
    const logo = document.querySelector('.logo');
    const scrollPosition = window.scrollY;

    if (!header) return;

    const threshold = 556; 

    if (scrollPosition > threshold) {
        // añadimos la clase en vez de aplicar colores inline
        header.classList.add('scrolled');

        if (logo) {
            logo.style.opacity = '1';
            logo.style.visibility = 'visible';
            logo.style.transition = 'all 0.3s ease';
        }

        // removed inline nav link color changes

    } else {
        header.classList.remove('scrolled');

        if (logo) {
            logo.style.opacity = '1';
            logo.style.visibility = 'visible';
        }
    }
});

            document.addEventListener('DOMContentLoaded', function () {
                const today = new Date().toISOString().split('T')[0];
                document.getElementById('fecha-llegada').min = today;
                document.getElementById('fecha-salida').min = today;

                document.getElementById('fecha-llegada').addEventListener('change', function () {
                    document.getElementById('fecha-salida').min = this.value;
                });
            // set initial state on load
            window.dispatchEvent(new Event('scroll'));
            });