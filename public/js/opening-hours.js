$(document).ready(function() {
    // Wenn der Bearbeiten-Button geklickt wird
    $('.edit-button').click(function() {
        const id = $(this).data('id');
        const row = $(this).closest('tr');
        
        // Wenn das Formular bereits existiert, entferne es
        if (row.next().hasClass('edit-form-row')) {
            row.next().remove();
            return; // Formular entfernen, falls es schon existiert
        }

        // Erstelle das Formular und füge es direkt unter der Zeile ein
        const editFormHtml = `
            <tr class="edit-form-row">
                <td colspan="4">
                    <form id="editOpeningHoursForm">
                        <input type="hidden" id="openingHourId" value="${id}">
                        
                        <label for="morningStart">Vormittag Start:</label>
                        <input type="time" id="morningStart" name="morningStart" required>
        
                        <label for="morningEnd">Vormittag Ende:</label>
                        <input type="time" id="morningEnd" name="morningEnd" required>
        
                        <label for="afternoonStart">Nachmittag Start:</label>
                        <input type="time" id="afternoonStart" name="afternoonStart" required>
        
                        <label for="afternoonEnd">Nachmittag Ende:</label>
                        <input type="time" id="afternoonEnd" name="afternoonEnd" required>
        
                        <button type="submit" id="saveChanges">Speichern</button>
                    </form>
                </td>
            </tr>
        `;
        
        // Füge das Formular direkt unter der Zeile ein
        row.after(editFormHtml);

        // Hole die aktuellen Werte der Öffnungszeiten und fülle das Formular
        const morningStart = row.find('td:nth-child(2)').text().split('-')[0].trim();
        const morningEnd = row.find('td:nth-child(2)').text().split('-')[1].trim();
        const afternoonStart = row.find('td:nth-child(3)').text().split('-')[0].trim();
        const afternoonEnd = row.find('td:nth-child(3)').text().split('-')[1].trim();

        $('#morningStart').val(morningStart);
        $('#morningEnd').val(morningEnd);
        $('#afternoonStart').val(afternoonStart);
        $('#afternoonEnd').val(afternoonEnd);
    });

    // Wenn der Speichern-Button geklickt wird
    $('#editOpeningHoursForm').submit(function(event) {
        event.preventDefault(); // Verhindert das Neuladen der Seite

        // Validierung der Eingabefelder
        const isValid = $('#morningStart').val() && $('#morningEnd').val() && $('#afternoonStart').val() && $('#afternoonEnd').val();
        if (!isValid) {
            showMessage('Bitte füllen Sie alle Felder aus!', 'error');
            return;
        }

        // Sende die neuen Werte via AJAX an den Controller
        $.ajax({
            url: '/opening/hours/' + $('#openingHourId').val() + '/edit/ajax',
            method: 'POST',
            data: {
                morningStart: $('#morningStart').val(),
                morningEnd: $('#morningEnd').val(),
                afternoonStart: $('#afternoonStart').val(),
                afternoonEnd: $('#afternoonEnd').val(),
            },
            success: function(response) {
                // Zeige eine Erfolgsmeldung
                showMessage('Erfolgreich gespeichert', 'success');

                // Aktualisiere die Tabelle mit den neuen Werten
                const row = $('tr[data-id="' + response.id + '"]');
                row.find('td:nth-child(2)').text(response.updatedData.morningStart + ' - ' + response.updatedData.morningEnd);
                row.find('td:nth-child(3)').text(response.updatedData.afternoonStart + ' - ' + response.updatedData.afternoonEnd);

                // Entferne das Formular nach dem Speichern
                row.next().remove();
            },
            error: function(xhr, status, error) {
                // Fehlerbehandlung
                showMessage('Es gab ein Problem beim Speichern. Bitte versuchen Sie es erneut.', 'error');
            }
        });
    });

    // Funktion zur Anzeige von Erfolg- oder Fehlermeldungen
    function showMessage(message, type) {
        const messageBox = $('#message');
        messageBox.text(message);
        if (type === 'success') {
            messageBox.css('background-color', 'lightgreen');
        } else if (type === 'error') {
            messageBox.css('background-color', 'lightcoral');
        }
        messageBox.fadeIn();
        setTimeout(function() {
            messageBox.fadeOut();
        }, 3000);
    }
});
