// Filepicker-Script für den Dateimanager
// Monkey Island Style: Code, der sogar auf Mêlée Island läuft!

(function () {
    let selectedFiles = [];

    function loadDirectory(dir = '') {
        fetch('/file-manager/list?dir=' + encodeURIComponent(dir))
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    console.error('❌ Fehler:', data.error);
                    document.getElementById('directoryTree').innerHTML = '<div class="text-danger">❌ Fehler beim Laden des Verzeichnisses.</div>';
                    document.getElementById('directoryContent').innerHTML = '';
                    return;
                }

                // Links: Verzeichnisbaum
                let treeHtml = '';
                if (data.parent !== null) {
                    treeHtml += `<div><a href="#" class="dir-link" data-path="${data.parent}">⬅️ ..</a></div>`;
                }
                data.dirs.forEach(d => {
                    treeHtml += `<div><a href="#" class="dir-link" data-path="${d.path}">📁 ${d.name}</a></div>`;
                });
                document.getElementById('directoryTree').innerHTML = treeHtml || '<div class="text-muted">Keine Ordner gefunden.</div>';

                // Mitte: Dateien
                let contentHtml = '';
                data.files.forEach(f => {
                    contentHtml += `<div>
                        <a href="#" class="file-link" data-path="${f.path}">📝 ${f.name}</a>
                    </div>`;
                });
                document.getElementById('directoryContent').innerHTML = contentHtml || '<div class="text-muted">Keine Dateien gefunden.</div>';

                // Verzeichnis-Links
                document.querySelectorAll('#directoryTree .dir-link').forEach(link => {
                    link.addEventListener('click', function (e) {
                        e.preventDefault();
                        loadDirectory(this.dataset.path);
                    });
                });
                // Datei-Links
                document.querySelectorAll('#directoryContent .file-link').forEach(link => {
                    link.addEventListener('click', function (e) {
                        e.preventDefault();
                        addFile(this.dataset.path);
                    });
                });
            })
            .catch(err => {
                console.error('❌ Fehler beim Laden des Verzeichnisses:', err);
                document.getElementById('directoryTree').innerHTML = '<div class="text-danger">❌ Fehler beim Laden des Verzeichnisses.</div>';
                document.getElementById('directoryContent').innerHTML = '';
            });
    }

    function addFile(path) {
        if (!selectedFiles.includes(path)) {
            selectedFiles.push(path);
            updateSelectedFiles();
        }
    }
    function removeFile(path) {
        selectedFiles = selectedFiles.filter(f => f !== path);
        updateSelectedFiles();
    }
    function updateSelectedFiles() {
        const ul = document.getElementById('selectedFileList');
        if (!ul) return;
        ul.innerHTML = '';
        selectedFiles.forEach(f => {
            let li = document.createElement('li');
            li.className = "list-group-item d-flex justify-content-between align-items-center";
            li.innerHTML = `
                <span>${f}</span>
                <button type="button" class="btn btn-sm btn-danger">Entfernen</button>
            `;
            li.querySelector('button').addEventListener('click', function () {
                removeFile(f);
            });
            ul.appendChild(li);
        });
    }

    // Das ist die EINZIGE Funktion, die du im Modal-Helper aufrufst!
    window.initFilePicker = function () {
        // Sicherstellen, dass die Elemente da sind (Modal ist offen und im DOM!)
        const tree = document.getElementById('directoryTree');
        const content = document.getElementById('directoryContent');
        const selected = document.getElementById('selectedFileList');
        const applyBtn = document.getElementById('applyFiles');
        if (!tree || !content || !selected || !applyBtn) {
            console.warn('Filepicker: Modal-Elemente nicht im DOM!');
            return;
        }

        selectedFiles = [];
        updateSelectedFiles();
        loadDirectory('');

        // Button-Handler sauber setzen (ggf. mehrfach, daher vorher entfernen!)
        applyBtn.onclick = function () {
            if (window.opener && window.opener.setCorrespondingFiles) {
                window.opener.setCorrespondingFiles(selectedFiles);
            } else if (window.parent && window.parent.setCorrespondingFiles) {
                window.parent.setCorrespondingFiles(selectedFiles);
            } else if (window.setCorrespondingFiles) {
                window.setCorrespondingFiles(selectedFiles);
            }
            // Modal schließen
            const modalElement = document.getElementById('globalModalViewer');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) modalInstance.hide();
        };

        console.log("🦜 Filepicker-Init läuft!");
    };
})();
