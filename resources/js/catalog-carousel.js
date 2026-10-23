document.addEventListener('DOMContentLoaded', () => {

    const slides = document.querySelectorAll('.carousel-slide');
    const dots = document.querySelectorAll('.carousel-dot');

    const previousButton = document.querySelector('.carousel-prev');
    const nextButton = document.querySelector('.carousel-next');

    if (!slides.length || !dots.length || !previousButton || !nextButton) {
        return;
    }

    let currentSlide = 0;

    function showSlide(index) {

        slides.forEach((slide) => {
            slide.classList.remove('active');
        });

        dots.forEach((dot) => {
            dot.classList.remove('active');
        });

        slides[index].classList.add('active');
        dots[index].classList.add('active');

        currentSlide = index;
    }


    function nextSlide() {

        let next = currentSlide + 1;

        if (next >= slides.length) {
            next = 0;
        }

        showSlide(next);
    }


    function previousSlide() {

        let previous = currentSlide - 1;

        if (previous < 0) {
            previous = slides.length - 1;
        }

        showSlide(previous);
    }


    nextButton.addEventListener('click', () => {
        nextSlide();
    });


    previousButton.addEventListener('click', () => {
        previousSlide();
    });


    dots.forEach((dot, index) => {

        dot.addEventListener('click', () => {
            showSlide(index);
        });

    });


    // Troca automática
    setInterval(() => {
        nextSlide();
    }, 5000);

});