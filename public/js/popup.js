document.addEventListener("DOMContentLoaded", function () {
    document.body.addEventListener("click", function (event) {
        if (event.target.classList.contains("open-popup")) {
            let popupId = event.target.getAttribute("data-id");
            let modalBody = document.getElementById("popupContent");

            // Ladeanzeige setzen
            modalBody.innerHTML = "<p>Lade Inhalte...</p>";

            // AJAX-Request zum Symfony-Controller
            fetch(`/popup/view/${popupId}`)
                .then(response => response.text())
                .then(data => {
                    modalBody.innerHTML = data; // Inhalt in das Modal setzen
                })
                .catch(error => {
                    console.error("Fehler beim Laden des Popups:", error);
                    modalBody.innerHTML = "<p>Fehler beim Laden des Inhalts.</p>";
                });
        }
    });
});
