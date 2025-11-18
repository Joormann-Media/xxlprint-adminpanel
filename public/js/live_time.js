document.addEventListener('DOMContentLoaded', function() {
    updateDateTime();

    function updateDateTime() {
        fetch('/get-time')
            .then(response => response.json())
            .then(data => {
                const currentDate = new Date(data.date); // Wandelt das Datum in ein Date-Objekt um
                const currentTime = data.time;

                // Formatieren des Datums im deutschen Format (DD.MM.YYYY)
                const formattedDate = currentDate.toLocaleDateString('de-DE'); // Deutsches Datumsformat

                // Setze das formatierte Datum und die Zeit in die entsprechenden Elemente
                document.getElementById('current-date').innerText = formattedDate;
                document.getElementById('current-time').innerText = currentTime;
            });
    }

    // Aktualisiere das Datum und die Uhrzeit alle 1 Sekunde
    setInterval(updateDateTime, 1000);
});
