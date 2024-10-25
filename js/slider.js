let currentIndex = 0;
const slides = document.querySelectorAll('.slide');
const totalSlides = slides.length;
let z = 1;
const sliderAnimation = document.getElementById('slider-animation');
const animationTime = sliderAnimation.getAttribute('data-animation-time');
function showSlide(index) {
    slides.forEach((slide, i) => {
        slide.classList.remove('active', 'next', 'first');
        if (i === index){
            slide.style.zIndex = z++;
            slide.classList.add('next'); // Додаємо клас для входження
            setTimeout(() => {
                slide.classList.add('active'); 
            }, 1000);
        }
    });
}

function nextSlide() {
    currentIndex = (currentIndex + 1) % totalSlides; 

    showSlide(currentIndex);
}

setInterval(nextSlide, animationTime*1000); 

