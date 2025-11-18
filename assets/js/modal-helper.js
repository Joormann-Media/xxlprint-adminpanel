// ✅ Öffnet den globalen Modal
function openGlobalModal(title = 'Hinweis', content = '') {
  const modalElement = document.getElementById('globalModalViewer');
  const modalTitle = document.getElementById('globalModalTitle');
  const modalBody = document.getElementById('globalModalBody');

  if (!modalElement || !modalTitle || !modalBody) {
    console.error('❌ globalModalViewer oder Inhalte nicht gefunden.');
    return;
  }

  // Entferne "inert" damit Modal klickbar wird
  modalElement.removeAttribute('inert');

  modalTitle.innerText = title;
  modalBody.innerHTML = `
    ${content}
    <div class="text-end mt-4">
      <button id="closeGlobalModal" type="button" class="btn btn-secondary">❌ Schließen</button>
    </div>
  `;
  setTimeout(() => {
    if (window.initFilePicker && document.getElementById('directoryTree')) {
        window.initFilePicker();
    }
}, 20);
  console.log('MODAL-BODY-INHALT:', modalBody.innerHTML);
  // Stelle sicher, dass der Modal-Body den richtigen Inhalt hat
  let modalInstance = bootstrap.Modal.getInstance(modalElement);
  if (!modalInstance) {
    modalInstance = new bootstrap.Modal(modalElement);
  }

  modalInstance.show();

  // Füge Event-Listener für 'shown.bs.modal' hinzu
  document.getElementById('globalModalViewer').addEventListener('shown.bs.modal', () => {
    if (window.initFilePicker && document.getElementById('directoryTree')) {
        window.initFilePicker();
    }
  }, { once: true });

  // Schließen-Button funktionsfähig machen
  setTimeout(() => {
    const closeButton = document.getElementById('closeGlobalModal');
    if (closeButton) {
      closeButton.addEventListener('click', () => {
        modalInstance.hide();
      });
    }
  }, 30);

  // Beim Schließen → wieder `inert` setzen
  modalElement.addEventListener(
    'hidden.bs.modal',
    () => {
        modalElement.setAttribute('inert', '');
    },
    { once: true }
  );
}

// ✅ Lädt Modal-Content via URL
function loadModalFromUrl(url, title = '🔔 Hinweis') {
  fetch(url)
    .then(response => {
      if (!response.ok) {
        throw new Error('❌ Fehler beim Laden der URL: ' + response.statusText);
      }
      return response.text();
    })
    .then(html => openGlobalModal(title, html))
    .catch(err => {
      console.error(err);
      openGlobalModal('Fehler', '<div class="text-danger">❌ Inhalt konnte nicht geladen werden.</div>');
    });
}

// 📌 Auto-Bind für alle <a class="open-popup" ...>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('a.open-popup').forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();

      const url = this.getAttribute('href');
      const title = this.getAttribute('data-title') || this.getAttribute('title') || '🔔 Hinweis';

      if (url) {
        loadModalFromUrl(url, title);
      } else {
        console.warn('⚠️ Kein href angegeben für .open-popup');
      }
    });
  });
});

// 🔄 Optional: Funktionen global verfügbar machen
window.openGlobalModal = openGlobalModal;
window.loadModalFromUrl = loadModalFromUrl;

console.log("✅ Modal-Helper wurde erfolgreich geladen.");
