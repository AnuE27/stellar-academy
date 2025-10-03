const cards = document.querySelectorAll(".card");

cards.forEach((card) => {
    let seeMoreBtn = card.querySelector(".see-more-btn");
    let textContent = card.querySelector(".card-content .text");

    seeMoreBtn.addEventListener("click", () => {
        card.classList.toggle("active");

        if (card.classList.contains("active")) {
            seeMoreBtn.innerHTML = "See Less";
            textContent.style.height = textContent.scrollHeight + "px";
        } else {
            seeMoreBtn.innerHTML = "See More";
            textContent.style.height = "100px";
        }
    });
});

