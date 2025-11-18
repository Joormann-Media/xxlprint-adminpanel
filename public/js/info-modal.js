document.addEventListener("DOMContentLoaded", function () {
    const openModalBtn = document.getElementById("openModalBtn");
    const modalElement = document.getElementById("meinModal");
    const closeModalBtn = document.getElementById("closeModalBtn");
    const modalTitle = document.getElementById("meinModalLabel");
    const modalBody = document.getElementById("modalBody");

    // Modal öffnen
    if (openModalBtn) {
        openModalBtn.addEventListener("click", function () {
            // Setze den Inhalt des Modals aus Twig-Variablen (diese Daten kommen aus dem Controller)
            modalTitle.textContent = "{{ modal_title }}";  // Titel aus Twig
            modalBody.innerHTML = "{{ modal_content|raw }}"; // Inhalt aus Twig

            modalElement.classList.add("show");
            modalElement.style.display = "block";
            modalElement.setAttribute("aria-hidden", "false");
        });
    }

    // Modal schließen
    if (closeModalBtn) {
        closeModalBtn.addEventListener("click", function () {
            modalElement.classList.remove("show");
            modalElement.style.display = "none";
            modalElement.setAttribute("aria-hidden", "true");
        });
    }
});
