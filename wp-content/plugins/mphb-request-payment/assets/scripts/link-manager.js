(function ($) {
    "use strict";

    $(function () {
        DisableAutoRequestControl($('#mphbrp-disable-auto-request-control'));

        $('.mphbrp-payment-request').each(function (i, element) {
            PaymentRequestLink($(element));
        });

        AddPaymentRequestForm($('#mphb-add-payment-request-form'));
    });

    function mphbrp_build_notice(type, message)
    {
        if (message.length > 0) {
            let $notice = $(
                '<div class="notice notice-' + type + ' is-dismissible">'
                    + '<p>' + message + '</p>'
                    + '<button type="button" class="notice-dismiss"></button>'
                + '</div>'
            );

            $notice.children('button').on('click', function (event) {
                $(event.target).parent().remove();
            });

            return $notice;
        } else {
            return null;
        }
    }

    function mphbrp_add_link(linkHtml)
    {
        let $link = $(linkHtml);

        $('#mphbrp-payment-requests').append($link);

        PaymentRequestLink($link);
    }

    /**
     * @param {String} text
     * @returns {Promise}
     */
    function mphbrp_copy_to_clipboard(text)
    {
        // Try Clipboard API
        if (navigator.clipboard) {
            return navigator.clipboard.writeText(text);
        }
        
        // Fallback method (deprecated)
        let textarea = document.createElement('textarea');

        // Avoid scrolling to bottom
        textarea.style.position = 'fixed';
        textarea.style.top = '0';
        textarea.style.left = '0';

        textarea.value = text;

        document.body.appendChild(textarea);

        textarea.focus();
        textarea.select();

        let isCopied = false;

        try {
            isCopied = document.execCommand('copy');
        } catch (error) {
        }

        document.body.removeChild(textarea);

        return isCopied ? Promise.resolve(isCopied) : Promise.reject(isCopied);
    }

    function DisableAutoRequestControl($control)
    {
        // Elements
        let $checkbox = $control.find('input[type="checkbox"]');
        let $lastNotice = null;

        function showNotice(status, message)
        {
            clearNotices();

            let $notice = mphbrp_build_notice(status, message);

            if ($notice !== null) {
                $notice.insertAfter($control);
                $lastNotice = $notice;
            }
        }

        function clearNotices()
        {
            if ($lastNotice !== null) {
                $lastNotice.remove();
                $lastNotice = null;
            }
        }

        $checkbox.on('change', function (event) {
            $checkbox.prop('disabled', true);
            clearNotices();

            let isChecked = $checkbox.prop('checked');

            $.ajax({
                'url': MPHBRP._data.ajaxUrl,
                'type': 'POST',
                'dataType': 'json',
                'data': {
                    'action': 'mphbrp_disable_auto_request',
                    'mphb_nonce': MPHBRP._data.nonces.disableAutoRequest,
                    'booking_id': MPHBRP._data.page.bookingId,
                    'disabled': isChecked,
                },
            }).done(function (response) {
                if (response.data.status == 'error') {
                    showNotice('error', response.data.message);
                    $checkbox.prop('checked', !isChecked);
                }
            }).fail(function (response) {
                showNotice('error', response.statusText);
                $checkbox.prop('checked', !isChecked);
            }).always(function () {
                $checkbox.prop('disabled', false);
            });
        });
    }

    function PaymentRequestLink($link)
    {
        // Elements and buttons
        let $copyButton = $link.find('.button-copy');
        let $sendButton = $link.find('.button-send');
        let $deleteButton = $link.find('.button-delete');
        let $buttons = $copyButton.add($sendButton).add($deleteButton);
        let $hr = $link.find('hr').first();
        let $lastNotice = null;

        // Fields
        let id = parseInt($link.data('id')) || 0;
        let type = $link.data('type');
        let checkoutUrl = $link.find('.mphbrp-payment-request-link').attr('href');

        let buttonsDisabled = false;

        function showNotice(status, message)
        {
            clearNotices();

            let $notice = mphbrp_build_notice(status, message);

            if ($notice !== null) {
                $notice.insertBefore($hr);
                $lastNotice = $notice;
            }
        }

        function clearNotices()
        {
            if ($lastNotice !== null) {
                $lastNotice.remove();
                $lastNotice = null;
            }
        }

        function toggleButtons()
        {
            buttonsDisabled = !buttonsDisabled;

            $buttons.prop('disabled', buttonsDisabled);
        }

        $copyButton.on('click', function (event) {
            event.preventDefault();

            toggleButtons(); // Disabled buttons
            clearNotices();

            mphbrp_copy_to_clipboard(checkoutUrl).then(
                function () {
                    showNotice('success', MPHBRP._data.messages.copied);
                    toggleButtons(); // Enable buttons
                },
                function () {
                    showNotice('error', MPHBRP._data.messages.unableToCopy);
                    toggleButtons(); // Enable buttons
                }
            );
        });

        $sendButton.on('click', function (event) {
            event.preventDefault();

            toggleButtons(); // Disable buttons
            clearNotices();

            $.ajax({
                'url': MPHBRP._data.ajaxUrl,
                'type': 'POST',
                'dataType': 'json',
                'data': {
                    'action': 'mphbrp_send_payment_request',
                    'mphb_nonce': MPHBRP._data.nonces.sendPaymentRequest,
                    'booking_id': MPHBRP._data.page.bookingId,
                    'request': {
                        'id': id,
                        'type': type,
                    },
                },
            }).done(function (response) {
                // Success or error
                showNotice(response.data.status, response.data.message);
            }).fail(function (response) {
                showNotice('error', response.statusText);
            }).always(function () {
                toggleButtons(); // Enable buttons
            });
        });

        $deleteButton.on('click', function (event) {
            event.preventDefault();

            toggleButtons(); // Disable buttons
            clearNotices();

            $.ajax({
                'url': MPHBRP._data.ajaxUrl,
                'type': 'POST',
                'dataType': 'json',
                'data': {
                    'action': 'mphbrp_delete_payment_request',
                    'mphb_nonce': MPHBRP._data.nonces.deletePaymentRequest,
                    'booking_id': MPHBRP._data.page.bookingId,
                    'request': {
                        'id': id,
                    },
                },
            }).done(function (response) {
                if (response.data.status == 'success') {
                    $link.remove();
                } else {
                    showNotice('error', response.data.message);
                }
            }).fail(function (response) {
                showNotice('error', response.statusText);
            }).always(function () {
                toggleButtons(); // Enable buttons
            });
        });
    }

    function AddPaymentRequestForm($form)
    {
        if ($form.length == 0) {
            return;
        }

        // Form & elements
        let $customFields = $form.find('.mphbrp-custom-fields');
        let $newButton = $form.find('.button-new');
        let $addButton = $form.find('.button-add');
        let $cancelButton = $form.find('.button-cancel');
        let $hideElements = $customFields.add($newButton).add($addButton).add($cancelButton);
        let $lastNotice = null;

        // Inputs
        let $typeInput = $form.find('input[name="mphbrp_custom_request_type"]');
        let $amountInput = $form.find('input[name="mphbrp_custom_request_amount"]');
        let $descriptionInput = $form.find('textarea[name="mphbrp_custom_request_description"]');

        // Fields
        let buttonsDisabled = false;
        let formVisible = false;

        function getValues()
        {
            let values = {
                'type': $typeInput.filter(':checked').val(),
                'amount': parseFloat($amountInput.val()),
                'description': $descriptionInput.val(),
            };

            if (isNaN(values.amount)) {
                values.amount = 0;
            } else {
                values.amount = Math.abs(values.amount);
            }

            return values;
        }

        function showNotice(status, message)
        {
            clearNotices();

            let $notice = mphbrp_build_notice(status, message);

            if ($notice !== null) {
                $form.append($notice);
                $lastNotice = $notice;
            }
        }

        function clearNotices()
        {
            if ($lastNotice !== null) {
                $lastNotice.remove();
                $lastNotice = null;
            }
        }

        function toggleButtons()
        {
            buttonsDisabled = !buttonsDisabled;

            $addButton.prop('disabled', buttonsDisabled);
            $cancelButton.prop('disabled', buttonsDisabled);
        }

        function toggleForm()
        {
            $hideElements.toggleClass('mphb-hide');

            formVisible = !formVisible;

            if (!formVisible) {
                resetInputs();
                clearNotices();
            }
        }

        function resetInputs()
        {
            $typeInput.filter('[value="percent"]').prop('checked', true);
            $amountInput.val('');
            $descriptionInput.val('');
        }

        $newButton.on('click', function (event) {
            event.preventDefault();

            // Just show the form
            toggleForm();
        });

        $addButton.on('click', function (event) {
            event.preventDefault();

            toggleButtons(); // Disable buttons
            clearNotices();

            $.ajax({
                'url': MPHBRP._data.ajaxUrl,
                'type': 'POST',
                'dataType': 'json',
                'data': {
                    'action': 'mphbrp_add_payment_request',
                    'mphb_nonce': MPHBRP._data.nonces.addPaymentRequest,
                    'booking_id': MPHBRP._data.page.bookingId,
                    'request': getValues(),
                },
            }).done(function (response) {
                if (response.data.status == 'success') {
                    mphbrp_add_link(response.data.html);
                    toggleForm(); // Hide form
                } else {
                    showNotice('error', response.data.message);
                }

            }).fail(function (response) {
                showNotice('error', response.statusText);

            }).always(function () {
                toggleButtons(); // Enable buttons
            });
        });

        $cancelButton.on('click', function (event) {
            event.preventDefault();

            toggleForm(); // Hide form
        });

        $form.find('input, textarea').on('keydown', function (event) {
            // Block [Enter] to submit the form and update the post
            if (event.key == 'Enter') {
                event.preventDefault();
            }
        });
    }
})(jQuery);
