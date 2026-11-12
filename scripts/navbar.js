const hamburger = document.getElementById("hamburger");
const mobileMenu = document.getElementById("navLinks");

/* ABRIR MENU */

hamburger.addEventListener("click", () => {
    hamburger.classList.toggle("active");
    mobileMenu.classList.toggle("active");
});

/* CERRAR MENU AL HACER CLICK */

document.querySelectorAll(".nav-links a").forEach(link => {
    link.addEventListener("click", () => {
        hamburger.classList.remove("active");
        mobileMenu.classList.remove("active");
    });
});