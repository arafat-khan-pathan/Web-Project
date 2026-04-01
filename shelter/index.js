let currentFilter = "all";

document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("userNameDisplay").textContent =
        localStorage.getItem("userName") || "Arafat";
    lucide.createIcons();
});

function setFilter(type, btn) {
    currentFilter = type;
    document.querySelectorAll(".filter-btn").forEach(b => b.classList.remove("active"));
    btn.classList.add("active");
    filterPets();
}

function filterPets() {
    const search = document.getElementById("petSearch").value.toLowerCase();
    document.querySelectorAll(".pet-card").forEach(card => {
        const species = card.dataset.species;
        const text = card.innerText.toLowerCase();

        const matchSpecies = currentFilter === "all" || species === currentFilter;
        const matchSearch = text.includes(search);

        card.style.display = matchSpecies && matchSearch ? "block" : "none";
    });
}

function logout() {
    localStorage.clear();
    window.location.href = "login__.html";
}
