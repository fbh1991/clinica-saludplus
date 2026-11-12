const sections = [
    document.querySelector("#clinicas"),
    document.querySelector("#tecnologia"),
    document.querySelector("#cirugia"),
    document.querySelector("#especialidades"),
    document.querySelector("#personal")
];

/* LINKS DEL MENU STICKY */

const hiperLinks = document.querySelectorAll(".linkHiper");

window.addEventListener("scroll", () => {

    let current = "";

    sections.forEach(section => {

        if (!section) return;

        const sectionTop = section.offsetTop - 220;
        const sectionHeight = section.offsetHeight;

        if (
            window.scrollY >= sectionTop &&
            window.scrollY < sectionTop + sectionHeight
        ) {
            current = section.getAttribute("id");
        }
    });

    hiperLinks.forEach(link => {

        link.classList.remove("active");

        const href = link.getAttribute("href").substring(1);

        if (href === current) {
            link.classList.add("active");
        }
    });
});