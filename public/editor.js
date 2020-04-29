window.jQuery(function ($) {
    var idCounter = 0;

    $('[data-element="maniple-editor"]').each(function () {
        var $this = $(this);
        var id = $this.attr('id');

        if (!id) {
            id = 'maniple-editor-' + (idCounter++);
            $this.attr('id', id);
        }

        var options = $this.data('tinymce');
        options.selector = '#' + id;

        tinymce.init(options);
    });
});
