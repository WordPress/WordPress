(function ($) {
    $(document).ready(function () {
        var targetElement = 'tr[data-plugin="autoptimize/autoptimize.php"] span.deactivate a';
        var redirectUrl = $(targetElement).attr('href');
        if ($('.ao-feedback-overlay').length === 0) {
            $('body').prepend('<div class="ao-feedback-overlay"></div>');
        }
        $('#ao_uninstall_feedback_popup').appendTo($(targetElement).parent());

        $(targetElement).on('click', function (e) {
			e.preventDefault();
            $('#ao_uninstall_feedback_popup ').addClass('active');
            $('body').addClass('ao-feedback-open');
            $('.ao-feedback-overlay').on('click', function () {
                $('#ao_uninstall_feedback_popup ').removeClass('active');
                $('body').removeClass('ao-feedback-open');
            });
        });

        $('#ao_uninstall_feedback_popup .info-disclosure-link').on('click', function (e) {
            e.preventDefault();
            $(this).parent().find('.info-disclosure-content').toggleClass('active');
        });

        $('#ao_uninstall_feedback_popup input[type="radio"]').on('change', function () {
            var radio = $(this);
            $('p.last-attempt').hide();
            if (radio.parent().find('textarea').length > 0 &&
                radio.parent().find('textarea').val().length === 0) {
                $('#ao_uninstall_feedback_popup #ao-deactivate-yes').attr('disabled', 'disabled');
                radio.parent().find('textarea').on('keyup', function (e) {
                    if ($(this).val().length === 0) {
                        $('#ao_uninstall_feedback_popup #ao-deactivate-yes').attr('disabled', 'disabled');
                    } else if ( $('#ao_feedback998')[0].checkValidity() == true ) {
                        $('#ao_uninstall_feedback_popup #ao-deactivate-yes').removeAttr('disabled');
                    }
                });
            } else {
                if ( $('#ao_feedback998')[0].checkValidity() == true ) {
                    $('#ao_uninstall_feedback_popup #ao-deactivate-yes').removeAttr('disabled');
                }
                $(this).siblings('p.last-attempt').show();
            }
        });

        $('#ao_feedback998').on('keyup', function (e) {
            email_node = $(this);
            email_val = email_node.val();
            if ( email_val.length > 0 && email_node[0].checkValidity() == false ) {
                $('#ao_uninstall_feedback_popup #ao-deactivate-yes').attr('disabled', 'disabled');
            } else if ( $( '#ao_uninstall_feedback_popup input[name="ao-deactivate-option"]:checked' ).length > 0 ) {
                $('#ao_uninstall_feedback_popup #ao-deactivate-yes').removeAttr('disabled');
            }
        });

        $('#ao_uninstall_feedback_popup #ao-deactivate-no').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(targetElement).unbind('click');
            $('body').removeClass('ao-feedback-open');
            $('#ao_uninstall_feedback_popup').remove();
            if (redirectUrl !== '') {
                location.href = redirectUrl;
            }
        });

        $('#ao_uninstall_feedback_popup #ao-deactivate-cancel').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $('#ao_uninstall_feedback_popup ').removeClass('active');
            $('body').removeClass('ao-feedback-open');
        });
        
        $('#ao_feedback_email_toggle').on('click', function (e) {
            $('#ao_feedback998').toggle();
        });

        $('#ao_uninstall_feedback_popup #ao-deactivate-yes').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(targetElement).unbind('click');

            var modal_data = JSON.parse(atob($('#ao_uninstall_feedback_popup').data('modal')))

            var selectedOption = $( '#ao_uninstall_feedback_popup input[name="ao-deactivate-option"]:checked' );

            var reason;

            if( selectedOption.attr("id") === "ao_feedback999" ){
                reason = 'Other: ' + selectedOption.parent().find('textarea').val().trim()
            }else{
                reason = selectedOption.parent().find('label').attr('data-reason').trim()
            }

            var data = {
                'url': modal_data.home,
                'reason': reason,
                'type': 'WP ' + $('#core_version').text().trim(),
                'version' : 'AO ' + $('#ao_plugin_version').text().trim(),
                'email': $('#ao_feedback998').val().trim(),
            };

            $.ajax({
                type: 'POST',
                url: atob( modal_data.dest ),
                data: data,
                complete() {
                    $('body').removeClass('ao-feedback-open');
                    $('#ao_uninstall_feedback_popup').remove();
                    if (redirectUrl !== '') {
                        location.href = redirectUrl;
                    }
                },
                beforeSend() {
                    $('#ao_uninstall_feedback_popup').addClass('sending-feedback');
                    $('#ao_uninstall_feedback_popup .popup--footer').remove();
                    $('#ao_uninstall_feedback_popup .popup--body').html('<i class="dashicons dashicons-update-alt"></i>');
                }
            });
        });
    });
})(jQuery);
