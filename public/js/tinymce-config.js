function loadTinyMCE(callback) {
    if (typeof tinymce !== 'undefined') {
        callback();
    } else {
        const script = document.createElement('script');
        script.src = '/assets/js/tinymce/tinymce.min.js'; // Ensure this path is correct
        script.onload = callback;
        document.head.appendChild(script);
    }
}

function initTinyMCE(selector, mode) {
    loadTinyMCE(() => {
        tinymce.init({
            selector: selector,
            plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
            toolbar_mode: mode,
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat',
            height: 300,
            external_plugins: {
                'advlist': '/assets/js/tinymce/plugins/advlist/plugin.min.js',
                'autolink': '/assets/js/tinymce/plugins/autolink/plugin.min.js',
                'lists': '/assets/js/tinymce/plugins/lists/plugin.min.js',
                'link': '/assets/js/tinymce/plugins/link/plugin.min.js',
                'image': '/assets/js/tinymce/plugins/image/plugin.min.js',
                'charmap': '/assets/js/tinymce/plugins/charmap/plugin.min.js',
                'preview': '/assets/js/tinymce/plugins/preview/plugin.min.js',
                'anchor': '/assets/js/tinymce/plugins/anchor/plugin.min.js',
                'pagebreak': '/assets/js/tinymce/plugins/pagebreak/plugin.min.js'
            },
            icons: 'default',
            icons_url: '/assets/js/tinymce/icons/default/icons.min.js',
            theme: 'silver',
            theme_url: '/assets/js/tinymce/themes/silver/theme.min.js',
            models: {
                'dom': '/assets/js/tinymce/models/dom/model.min.js'
            }
        });
    });
}
