$(document).ready(function () {
    let cropper;
    let selectedFile;

    // Live-Vorschau des Bildes
    $('#picUpl-imageInput').on('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            selectedFile = file;
            const reader = new FileReader();
            reader.onload = function (event) {
                $('#picUpl-imagePreview').attr('src', event.target.result).show();
                $('#picUpl-originalSize').text((file.size / 1024 / 1024).toFixed(2) + ' MB');
                startEditor(event.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    function startEditor(imageSrc) {
        $('#picUpl-editorContainer').show();
        $('#picUpl-imageForEditing').attr('src', imageSrc);

        if (cropper) {
            cropper.destroy(); // Bestehenden Cropper entfernen, falls vorhanden
        }

        cropper = new Cropper(document.getElementById('picUpl-imageForEditing'), {
            aspectRatio: NaN,
            viewMode: 1,
            autoCropArea: 0.8,
            movable: true,
            zoomable: true,
            scalable: true,
            ready() {
                $('#picUpl-saveButton').prop('disabled', false);
            },
            crop(event) {
                updateCropData(event.detail);
                updateLivePreview();
            }
        });
    }

    function updateCropData(data) {
        // Überprüfe, ob die Elemente existieren, bevor du sie ansteuerst
        if ($('#picUpl-imageWidth').length) $('#picUpl-imageWidth').text(Math.round(data.width));
        if ($('#picUpl-imageHeight').length) $('#picUpl-imageHeight').text(Math.round(data.height));
        if ($('#picUpl-imageX').length) $('#picUpl-imageX').text(Math.round(data.x));
        if ($('#picUpl-imageY').length) $('#picUpl-imageY').text(Math.round(data.y));
    }

    function updateLivePreview() {
        const canvas = cropper.getCroppedCanvas();
        if (canvas) {
            $('#picUpl-livePreviewContainer').show();
            $('#picUpl-livePreviewImage').attr('src', canvas.toDataURL());
        }
    }

    // Zuschneiden des Bildes
    $('#picUpl-cropButton').on('click', function () {
        if (cropper) {
            const canvas = cropper.getCroppedCanvas();
            $('#picUpl-livePreviewImage').attr('src', canvas.toDataURL());
            $('#picUpl-livePreviewContainer').show();
        }
    });

    // Speichern des bearbeiteten Bildes
    $('#picUpl-saveButton').on('click', function () {
        if (cropper) {
            const canvas = cropper.getCroppedCanvas();
            canvas.toBlob(function (blob) {
                const formData = new FormData();
                formData.append('image', blob, 'cropped-image.jpg');

                $.ajax({
                    url: '/upload', // Pfad zu deinem Upload-Skript
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        alert('Bild erfolgreich hochgeladen!');
                    },
                    error: function () {
                        alert('Fehler beim Hochladen des Bildes.');
                    }
                });
            });
        }
    });

    // Füge diesen Code am Ende der Datei ein, um das Laden zu bestätigen
    console.log('Image Upload Editor geladen!');
});
