"use strict";
jQuery(function ($) {
    $('body').on('click', '.woof_meta_delete', function () {
        $(this).parents('li').remove();
        return false;
    });

    //***

    $('#woof_meta_add_new_btn').on('click', function () {
        var key = $('.woof_meta_key_input').val();

        if (key.length > 0) {
            var html = $('#woof_meta_li_tpl').html();

            html = html.replace(/__META_KEY__/gi, key);
            html = html.replace(/__TITLE__/gi, key);
            $('#woof_meta_list').prepend(html);
            $('.woof_meta_key_input').val('');
        } else {
            alert("someting wrong!!!");
        }

        return false;
    });

    $('.woof_meta_key_input').on('keydown', function (e) {

        if (e.keyCode == 13) {
            $('#woof_meta_add_new_btn').trigger('click');
            return false;
        }

    });

    //***

    $('#woof_meta_get_btn').on('click', function () {
        var id = parseInt($('.woof_meta_keys_get_input').val(), 10);

        if (id > 0) {

            $('.woof_meta_keys_get_input').val('');

            $.ajax({
                method: "POST",
                url: ajaxurl,
                data: {
                    action: 'woof_meta_get_keys',
                    product_id: id
                },
                success: function (keys) {
                    if (keys.length > 0) {
                        keys = JSON.parse(keys);
                        var html = $('#woof_meta_li_tpl').html();
                        for (var i = 0; i < keys.length; i++) {
                            var li = html.replace(/__META_KEY__/gi, keys[i]);
                            li = li.replace(/__TITLE__/gi, keys[i]);
                            $('#woof_meta_list').prepend(li);
                        }
                    } else {
                        alert("someting wrong!!!");
                    }
                }
            });

        } else {
            alert("someting wrong!!!");
        }

        return false;
    });

    $('.woof_meta_keys_get_input').keydown(function (e) {
        if (e.keyCode == 13) {
            $('#woof_meta_get_btn').trigger('click');
            return false;
        }
    });

    //***

    $('body').on('change', '.woof_meta_view_selector', function () {
        var value = $(this).val();
        var type_selector = $(this).parents('li').find('.woof_meta_type_selector');
        var show_options = $(this).find("option:selected").attr("data-show-options");
        if (show_options == "yes") {
            $(this).parents('li').find('.woof_options_item_options').show(200);
        } else {
            $(this).parents('li').find('.woof_options_item_options').hide(200);
        }
        switch (value) {
            case 'popupeditor':
                $(type_selector).val('string');
                $(type_selector).parent().hide();
                break;

            case 'switcher':
                $(type_selector).val('number');
                $(type_selector).parent().hide();
                break;

            default:
                $(type_selector).parent().show();
                break;
        }

        return true;
    });

    $('body').on('change', '.woof_meta_type_selector', function () {
        var value = $(this).val();
        var type_selector = $(this).parents('li').find('.woof_meta_view_selector');
        var curr_type = $(type_selector).find("option:selected");
        var hideif = curr_type.attr("data-hideif").split(',');

        if (jQuery.inArray(value, hideif) != -1) {
            curr_type.removeAttr("selected");
            $(type_selector).find("options[value='textinput']").attr("selected", "selected");
        }
        var all_views = $(type_selector).find("option");

        $.each(all_views, function (index, option) {
            var hideif_option = $(option).attr("data-hideif").split(',');
            if (jQuery.inArray(value, hideif_option) != -1) {
                $(option).hide();
            } else {
                $(option).show();
            }
        });


    });
});

jQuery("#woof_meta_list").sortable({
    update: function (event, ui) {
        woof_sort_order = [];
        jQuery.each(jQuery('#woof_meta_list').children('li'), function (index, value) {
            var key = jQuery(this).data('key');
            woof_sort_order.push(key);
        });
        jQuery('input[name="woof_settings[items_order]"]').val(woof_sort_order.toString());
    },
    opacity: 0.8,
    cursor: "crosshair",
    handle: '.woof_drag_and_drope',
    placeholder: 'woof-options-highlight'
});