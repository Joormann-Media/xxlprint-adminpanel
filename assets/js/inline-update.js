document.addEventListener('DOMContentLoaded', () => {
    // Füge Event-Listener zu allen Bearbeitungsfeldern hinzu
    document.querySelectorAll('.editable').forEach(element => {
        element.addEventListener('blur', event => {
            const id = event.target.dataset.id; // ID des Eintrags
            const field = event.target.dataset.field; // Feldname (z.B. 'day', 'morningStart')
            const value = event.target.textContent; // Neuer Wert

            // Sende die Daten per Ajax an den Server
            
            fetch(`/admin/opening-hours/{id}/edit/ajax`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ [field]: value }),
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Fehler beim Speichern');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    console.log('Änderung erfolgreich gespeichert.');
                }
            })
            .catch(error => {
                console.error('Fehler:', error);
                alert('Ein Fehler ist aufgetreten.');
            });
        });
    });
});
