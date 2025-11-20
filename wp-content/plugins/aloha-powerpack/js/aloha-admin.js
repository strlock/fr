var aloha_vars;
var editor_wait = false;
jQuery(document).ready(function ($) {
    var checkForPostFormatSelectorInterval = setInterval(checkForPostFormatSelector, 200);
    if ($('#post-formats-select').length) {
        $('#post-formats-select [type="radio"]').on('click', function () {
            var selected = $(this).val();
            hideShowMetaBoxes(aloha_vars['format_types'], selected);
        });

    }
    function checkForPostFormatSelector() {

        if ($('select#post-format-selector-0').length && $('select#post-format-selector-0 option').length > 1) {

            clearInterval(checkForPostFormatSelectorInterval);
            $('#post-format-selector-0').on('change', function (e) {
                var selected = $(this).val();
                hideShowMetaBoxes(aloha_vars['format_types'], selected);
            });
        }
    }


    hideShowMetaBoxes(aloha_vars['format_types'], aloha_vars['selected_format_type']);


    function hideShowMetaBoxes(list, selected) {
        for (var i = 0; i < list.length; i++) {
            $('#aloha_' + list[i]).hide();
        }

        if ($('#aloha_' + selected).length) {
            $('#aloha_' + selected).show('slow').removeClass('closed');
        }

        if (selected === 'image') {
            //check if a guteberg is used
            if (aloha_vars.is_gutenberg) {
                if (!$('.edit-post-visual-editor').length && !editor_wait) {
                    //wait for it
                    editor_wait = setInterval(function () {
                        if ($('.edit-post-visual-editor').length && $('.editor-post-featured-image').length) {
                            clearInterval(editor_wait);
                            $('.editor-post-featured-image').closest('.components-panel__body').addClass('is-opened').attr('id', 'postimagediv');
                            $('#postimagediv').before('<div id="featured_placeholder"/>');
                            $('.edit-post-visual-editor').attr('id', 'postdivrich');
                            $('#postdivrich').before($('#postimagediv'));
                        }
                    }, 200);
                } else {
                    $('#postdivrich').before($('#postimagediv'));
                }

            } else {
                if (!$('#featured_placeholder').length) {
                    $('#postimagediv').before('<div id="featured_placeholder"/>');
                }

                $('#postdivrich').before($('#postimagediv'));
            }

        } else {
            $('#featured_placeholder').before($('#postimagediv'));
        }
    }

    $('div[data-dismissible] button.notice-dismiss, div[data-dismissible] .dismiss-this').on("click",
            function (event) {
                event.preventDefault();
                var $this = $(this);

                var attr_value, option_name, dismissible_length, data;

                attr_value = $this.closest("div[data-dismissible]").attr('data-dismissible').split('-');

                // remove the dismissible length from the attribute value and rejoin the array.
                dismissible_length = attr_value.pop();

                option_name = attr_value.join('-');
console.log(aloha_vars);
console.log(aloha_vars['dismissible_nonce']);
                data = {
                    'action': 'aloha_dismiss_admin_notice',
                    'option_name': option_name,
                    'dismissible_length': dismissible_length,
                    'nonce': aloha_vars['dismissible_nonce']
                };

                // We can also pass the url value separately from ajaxurl for front end AJAX implementations
                $.post(ajaxurl, data);
                $this.closest("div[data-dismissible]").hide('slow');
            }
    );
});

