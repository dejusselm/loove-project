document.addEventListener("DOMContentLoaded", () => {
    const items = document.querySelectorAll(".carousel-item");
    const dots = document.querySelectorAll(".dot");

    let current = 0;

    function showSlide(index) {
        items.forEach(item => item.classList.remove("active"));
        dots.forEach(dot => dot.classList.remove("active"));

        items[index].classList.add("active");
        dots[index].classList.add("active");

        current = index;
    }

    function nextSlide() {
        showSlide((current + 1) % items.length);
    }

    let timer = setInterval(nextSlide, 4000);

    dots.forEach((dot, index) => {
        dot.addEventListener("click", () => {
            clearInterval(timer);
            showSlide(index);
            timer = setInterval(nextSlide, 4000);
        });
    });
});