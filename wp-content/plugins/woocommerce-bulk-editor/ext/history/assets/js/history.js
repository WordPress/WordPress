"use strict";

//flags
var woobe_history_reverted = false;
var woobe_history_reverting_going = false;//to block products tab
var woobe_history_data_is_changed = true;//for updating history list by ajax when its tab clicked

/*pagination*/
var woobe_history_per_page = 10;
var woobe_history_page_count = 0;

jQuery(function ($) {
    //redraw products if bulk revert done and clicked on tab Products
    //https://learn.jquery.com/events/introduction-to-custom-events/
    jQuery(document).on("do_tabs-products", {
        //foo: "bar"
    }, function () {
        if (woobe_history_reverting_going) {
            alert(lang.history.wait_until_finish);
            return false;
        } else {
            if (woobe_history_reverted) {
                //console.log( event.data.foo );
                woobe_history_reverted = false;
                data_table.draw('page');
            }
        }

        __trigger_resize();
        return true;
    });

    //***

    jQuery(document).on("woobe_page_field_updated", {}, function (event, product_id, field_key) {
        woobe_history_data_is_changed = true;
        return true;
    });

    jQuery(document).on("woobe_bulk_completed", {}, function (event) {
        woobe_history_data_is_changed = true;
        return true;
    });

    //***
    //for history updating if data changed
    jQuery(document).on("do_tabs-history", {}, function () {
        if (woobe_history_data_is_changed && !woobe_history_reverting_going) {
            woobe_history_update_list();
        }
        return true;
    });

    //***

    jQuery('#woobe_history_show_types').on('change',function () {
        switch (parseInt(jQuery(this).val(), 10)) {
            case 1:
                jQuery('#woobe_history_list li.solo_li').show();
                jQuery('#woobe_history_list li.bulk_li').hide();
                break;
            case 2:
                jQuery('#woobe_history_list li.solo_li').hide();
                jQuery('#woobe_history_list li.bulk_li').show();
                break;
            default:
                //0
                jQuery('#woobe_history_list li').show();
                break;
        }

        return true;
    });

});

function woobe_history_update_list() {
    jQuery('#woobe_history_list_container').html('<h5>' + lang.loading + '</h5>');
    jQuery.ajax({
        method: "POST",
        url: ajaxurl,
        data: {
            action: 'woobe_get_history_list'
        },
        success: function (content) {
            jQuery('#woobe_history_list_container').html(content);
            woobe_history_init_pagination();
        },
        error: function () {
            alert(lang.error);
        }
    });

    //***
    //should be here!!
    woobe_history_data_is_changed = false;
}

function woobe_history_revert_solo(id, product_id) {
    if (confirm(lang.sure)) {

        woobe_disable_bind_editing();

        //***

        woobe_message(lang.history.reverting, 'warning', 999999);
        jQuery('.woobe_history_btn').hide();
        woobe_history_is_going();
        jQuery.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'woobe_history_revert_product',
                id: id
            },
            success: function () {
                woobe_message(lang.history.reverted, 'notice');

                if (jQuery('#product_row_' + product_id).length > 0) {
                    woobe_redraw_table_row(jQuery('#product_row_' + product_id));
                }

                //woobe_history_reverted = true;
                jQuery('#woobe_history_' + id).remove();
                jQuery('.woobe_history_btn').show();
                woobe_history_is_going(true);
            },
            error: function () {
                alert(lang.error);
                woobe_history_is_going(true);
            }
        });
    }
}

function woobe_history_revert_bulk(bulk_key, bulk_id) {
    if (confirm(lang.sure)) {

        if (woobe_bind_editing) {
            jQuery("[data-numcheck='woobe_bind_editing']").trigger('click');
            woobe_bind_editing = 0;
        }

        //***

        woobe_message(lang.history.reverting, 'warning', 999999);
        woobe_history_reverting_going = true;
        jQuery('.woobe_history_btn').hide();
        woobe_set_progress('woobe_bulk_progress_' + bulk_id, 0);
        woobe_history_is_going();

        jQuery.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'woobe_history_get_bulk_count',
                bulk_key: bulk_key
            },
            success: function (total_count) {
                woobe_history_revert_bulk_portion(bulk_id, bulk_key, total_count, 0);
            },
            error: function () {
                alert(lang.error);
                woobe_history_reverting_going = false;
                woobe_history_is_going(true);
            }
        });
    }
}

function woobe_history_revert_bulk_portion(bulk_id, bulk_key, total_count, removed) {
    var step = 10;

    jQuery.ajax({
        method: "POST",
        url: ajaxurl,
        data: {
            action: 'woobe_history_revert_bulk_portion',
            bulk_key: bulk_key,
            limit: step,
            removed_count: removed,
            total_count: total_count
        },
        success: function () {

            woobe_set_progress('woobe_bulk_progress_' + bulk_id, (removed + step) * 100 / total_count);

            if ((total_count - (removed + step)) <= 0) {
                woobe_message(lang.history.reverted, 'notice');
                woobe_history_reverted = true;
                woobe_history_reverting_going = false;
                jQuery('#woobe_history_' + bulk_key).remove();
                jQuery('.woobe_history_btn').show();
                woobe_history_is_going(true);
            } else {
                woobe_history_revert_bulk_portion(bulk_id, bulk_key, total_count, removed + step);
            }

        },
        error: function () {
            woobe_history_is_going(true);
            woobe_history_reverting_going = false;
            alert(lang.error);
        }
    });
}

function woobe_history_clear() {

    if (confirm(lang.sure)) {
        woobe_message(lang.history.clearing, 'warning', 999999);
        jQuery.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'woobe_history_clear'
            },
            success: function () {
                woobe_message(lang.history.cleared, 'notice');
                jQuery('#woobe_history_list_container').html('<h5>' + lang.history.cleared + '</h5>');
            },
            error: function () {
                alert(lang.error);
            }
        });
    }

}

function woobe_history_delete_solo(id) {
    if (confirm(lang.sure)) {
        woobe_message(lang.deleting, 'warning', 999999);
        jQuery.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'woobe_history_delete_solo',
                id: id
            },
            success: function () {
                woobe_message(lang.deleted, 'notice');
                jQuery('#woobe_history_' + id).remove();
            },
            error: function () {
                alert(lang.error);
            }
        });
    }
}

function woobe_history_delete_bulk(bulk_key) {
    if (confirm(lang.sure)) {
        woobe_message(lang.deleting, 'warning', 999999);
        jQuery.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'woobe_history_delete_bulk',
                bulk_key: bulk_key
            },
            success: function () {
                woobe_message(lang.deleted, 'notice');
                jQuery('#woobe_history_' + bulk_key).remove();
            },
            error: function () {
                alert(lang.error);
            }
        });
    }
}


function woobe_history_is_going(clear = false) {
    if (clear) {
        jQuery('#woobe_history_is_going').remove();
    } else {
        jQuery('#wp-admin-bar-root-default').append("<li id='woobe_history_is_going'>" + lang.history.history_is_going + "</li>");
}

}

/* pagination */

function  woobe_history_init_pagination() {
    /*actions*/
    jQuery("#woobe_history_pagination_number").on("change", function () {
        woobe_history_per_page = jQuery(this).val();
        if (woobe_history_per_page == -1) {
            woobe_history_per_page = 99999;
        }
        woobe_history_check_pagination();
    });
    jQuery(".woobe_history_pagination_prev").on("click", function () {
        woobe_history_page_count -= woobe_history_per_page;
        if (woobe_history_page_count < 0) {
            woobe_history_page_count = 0;
        }
        woobe_history_check_pagination();
        return false;
    });
    jQuery(".woobe_history_pagination_next").on("click", function () {
        woobe_history_page_count += woobe_history_per_page;
        woobe_history_check_pagination();
        return false;
    });
    jQuery(".woobe_calendar_clear").on("click", function () {
        var id = jQuery(this).data("val-id");
        jQuery(".woobe_calendar[data-val-id='" + id + "']").val('').trigger('change');
        return false;
    });

    jQuery("#woobe_history_filter_submit").on("click", function () {
        var filters = {};

        filters['author'] = "mselect_woobe_history_filter_author";
        filters['date_from'] = "woobe_history_filter_date_from";
        filters['date_to'] = "woobe_history_filter_date_to";
        filters['fields'] = "woobe_history_filter_field";
        filters['types'] = "woobe_history_show_types";
        jQuery.each(filters, function (i, item) {
            var val = jQuery("#" + item).val();

            filters[i] = val;
        });

        /*reset pagination and do search*/
        woobe_history_page_count = 0;
        woobe_history_do_search(filters);
        woobe_history_check_pagination();

    });

    jQuery("#woobe_history_filter_reset").on("click", function () {
        woobe_history_page_count = 0;
        woobe_history_cleare_filters();
        woobe_history_do_search(null);
        woobe_history_check_pagination();

    });

    woobe_history_check_pagination();
}

function woobe_history_cleare_filters() {

    jQuery(".woobe_history_filters .woobe_calendar").val('').trigger('change');
    jQuery("#woobe_history_filter_field").val('');
    jQuery(".woobe_history_filter_author").val(-1);
    jQuery("#woobe_history_show_types").val(0);

}

function   woobe_history_check_pagination() {
    var items = jQuery("li.woobe_history_li_show");
    var show_item = woobe_history_per_page;
    jQuery("li.woobe_history_item").hide();
    jQuery.each(items, function (i, item) {
        if (i >= woobe_history_page_count && show_item) {
            jQuery(item).show();
            show_item--;
        } else {
            jQuery(item).hide();
        }
    });

    if (woobe_history_page_count <= 0) {
        jQuery(".woobe_history_pagination_prev").hide();
    } else {
        jQuery(".woobe_history_pagination_prev").show();
    }

    if (woobe_history_page_count + woobe_history_per_page >= items.length) {
        jQuery(".woobe_history_pagination_next").hide();
    } else {
        jQuery(".woobe_history_pagination_next").show();
    }
    jQuery(".woobe_history_pagination_count").text(" " + items.length);
    var from = 0
    var to = items.length;
    from = woobe_history_page_count;
    if (woobe_history_page_count + woobe_history_per_page < items.length) {
        to = parseInt(woobe_history_page_count) + parseInt(woobe_history_per_page);
    }

    jQuery(".woobe_history_pagination_current_count").text(from + "-" + to + " ");
}

/*filter*/



function woobe_history_do_search(filters) {
    var histories = jQuery(".woobe_history_item");
    //console.log(filters);
    if (filters) {
        jQuery.each(histories, function (i, item) {
            var data = jQuery(item).find(".woobe_history_data")
            var author = data.data("author");
            var date = data.data("date");
            var fields = data.data("fields");
            var type = data.data("types");
            var hide = false;


            if (type != filters['types'] && filters['types'] != 0) {
                hide = true;
            }
            if (author != filters['author'] && filters['author'] != -1) {
                hide = true;
            }
            if (!hide && filters['date_from'] && (new Date(filters['date_from']).getTime() / 1000) > date) {

                hide = true;
            }
            if (!hide && filters['date_to'] && (new Date(filters['date_to']).getTime() / 1000) < date && filters['date_to'] != 0) {
                hide = true;
            }

            if (!hide && filters['fields']) {
                if (filters['fields'].indexOf(',') !== -1) {
                    hide = true;
                    var tmp = filters['fields'].split(',');

                    if (tmp.length) {
                        tmp.forEach(function (f) {
                            if (fields.indexOf(f.trim()) !== -1) {
                                hide = false;
                            }
                        });
                    }

                } else {
                    if (fields.indexOf(filters['fields']) === -1) {
                        hide = true;
                    }
                }
            }

            if (hide) {
                jQuery(item).removeClass("woobe_history_li_show");
            } else {
                jQuery(item).addClass("woobe_history_li_show");
            }

        });
    } else {
        jQuery(".woobe_history_item").addClass("woobe_history_li_show");
    }
}