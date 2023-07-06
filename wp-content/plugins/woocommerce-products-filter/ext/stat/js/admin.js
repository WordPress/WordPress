"use strict";
var woof_stat_data = new Array();
var woof_operative_tables = null;
//***
jQuery(function ($) {
    woof_stat_init_calendars();

    //under dev
    $('#woof_stat_snippet').change(function () {
        var taxonomies = $(this).val();
        if (taxonomies !== null && taxonomies.length > 0) {

            $.each(taxonomies, function (i, slug) {
                var id = 'woof_stat_snippet_' + slug;
                if (!$('#' + id).length) {
                    $('#woof_stat_snippets_tags').prepend('<li id="' + id + '" data-slug="' + slug + '"><label>' + slug + ' terms:</label><br /><input type="text" placeholder="' + woof_stat_vars.woof_stat_leave_empty + '" /></li>');
                }
            });

            //removing term inputs
            $.each($('#woof_stat_snippets_tags li'), function (i, li) {
                var slug = $(li).data('slug');
                if ($.inArray(slug, taxonomies) == -1) {
                    $(li).remove();
                }
            });

        } else {
            $('#woof_stat_snippets_tags').html("");
        }
    });
});

function woof_stat_get_request_snippets() {
    //*** assemble request_snippets
    var request_snippets = {};
    jQuery.each(jQuery('#woof_stat_snippets_tags li'), function (i, li) {
        var slug = jQuery(li).data('slug');
        var terms = jQuery(li).find('input').val();
        request_snippets[slug] = terms;
    });

    return request_snippets;
}

function woof_stat_calculate() {

    var calendar_from = parseInt(jQuery('#woof_stat_calendar_from').val(), 10);
    var calendar_to = parseInt(jQuery('#woof_stat_calendar_to').val(), 10);
    var request_snippets = woof_stat_get_request_snippets();

    jQuery('#chart_div_1').html("");
    jQuery('#chart_div_1_set').html("");
    jQuery('#woof_stat_print_btn').hide();

    if (calendar_from == 0 || calendar_to == 0) {
        alert(woof_stat_vars.woof_stat_sel_date_range);
        return false;
    }



    woof_stat_data = new Array();
    woof_show_info_popup(woof_stat_vars.woof_stat_calc);
    jQuery('#woof_stat_get_monitor').html("");
    woof_stat_process_monitor(woof_stat_vars.woof_stat_get_oper_tbls);
    var data = {
        action: "woof_get_operative_tables",
        calendar_from: calendar_from,
        calendar_to: calendar_to
    };
    jQuery.post(ajaxurl, data, function (tables) {
        tables = JSON.parse(tables);
        if (tables.length > 0) {
            woof_stat_process_monitor(woof_stat_vars.woof_stat_oper_tbls_prep);
            if (tables.length) {
                woof_stat_request_tables_data(0, tables);
            }
        } else {
            woof_hide_info_popup();
            woof_stat_process_monitor(woof_stat_vars.woof_stat_done);
            alert(woof_stat_vars.woof_stat_no_data);
        }
    });

    return false;
}

function woof_stat_request_tables_data(index, tables) {
    var calendar_from = parseInt(jQuery('#woof_stat_calendar_from').val(), 10);
    var calendar_to = parseInt(jQuery('#woof_stat_calendar_to').val(), 10);

    woof_stat_process_monitor(woof_stat_vars.woof_stat_getting_dftbls + ' ' + tables[index] + ' ...');
    var data = {
        action: "woof_get_stat_data",
        table: tables[index],
        request_snippets: woof_stat_get_request_snippets(),
        calendar_from: calendar_from,
        calendar_to: calendar_to
    };
    jQuery.post(ajaxurl, data, function (stat_data) {
        stat_data = JSON.parse(stat_data);
        woof_stat_data.push(stat_data);
        //+++
        if ((index + 1) < tables.length) {
            woof_stat_request_tables_data(index + 1, tables);
        } else {
            if (Object.keys(woof_stat_get_request_snippets()).length === 0) {
                var data = {
                    action: "woof_get_top_terms",
                    woof_stat_data: woof_stat_data
                };
                jQuery.post(ajaxurl, data, function (stat_data) {
                    woof_stat_data = JSON.parse(stat_data);
                    woof_hide_info_popup();
                    woof_stat_process_monitor(woof_stat_vars.woof_stat_done);
                    woof_stat_draw_graphs();
                });
            } else {
                woof_hide_info_popup();
                woof_stat_process_monitor(woof_stat_vars.woof_stat_done);
                woof_stat_draw_graphs();
            }
        }
    });
}


function woof_stat_process_monitor(text) {
    jQuery('#woof_stat_get_monitor').prepend('<li>' + text + '</li>');
}

function woof_stat_init_calendars() {
    jQuery(".woof_stat_calendar").datepicker(
            {
                showWeek: true,
                firstDay: woof_stat_vars.week_first_day,
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                maxDate: 'today',
                //maxDate: new Date(2017, 11 - 1, 30), //comment it, for tests only
                onSelect: function (selectedDate, self) {
                    var date = new Date(parseInt(self.currentYear, 10), parseInt(self.currentMonth, 10), parseInt(self.currentDay, 10), 23, 59, 59);
                    var mktime = (date.getTime() / 1000);
                    var css_class = 'woof_stat_calendar_from';
                    if (jQuery(this).hasClass('woof_stat_calendar_from')) {
                        css_class = 'woof_stat_calendar_to';
                        jQuery(this).parent().find('.' + css_class).datepicker("option", "minDate", selectedDate);
                    } else {
                        jQuery(this).parent().find('.' + css_class).datepicker("option", "maxDate", selectedDate);
                    }
                    jQuery(this).prev('input[type=hidden]').val(mktime);
                }
            }
    );
    jQuery(".woof_stat_calendar").datepicker("option", "minDate", new Date(woof_stat_vars.min_year, woof_stat_vars.min_month - 1, 1));
    jQuery(".woof_stat_calendar").datepicker("option", "dateFormat", woof_stat_vars.calendar_date_format);
    jQuery(".woof_stat_calendar").datepicker("option", "showAnim", 'fadeIn');
    //+++
    jQuery('body').on('keyup', ".woof_stat_calendar", function (e) {
        if (e.keyCode == 8 || e.keyCode == 46) {
            jQuery.datepicker._clearDate(this);
            jQuery(this).prev('input[type=hidden]').val("");
        }
    });

    jQuery(".woof_stat_calendar").each(function () {
        var mktime = parseInt(jQuery(this).prev('input[type=hidden]').val(), 10);
        if (mktime > 0) {
            var date = new Date(mktime * 1000);
            jQuery(this).datepicker('setDate', new Date(date));
        }
    });

}
    function woof_stat_draw_graphs() {
        woof_stat_process_monitor(woof_stat_vars.woof_stat_graphs);

       //  try {
            if (woof_stat_data.length) {
                var graph1 = {};
                //***
                var counter = 1;
                if (Object.keys(woof_stat_get_request_snippets()).length === 0) {
                    var data1 = woof_stat_data[0];
                    counter = 1;
                    for (let tn in data1) {
                        if (counter > parseInt(woof_stat_vars.max_items_per_graph, 10)) {
                            break;
                        }
                        graph1[tn] = data1[tn];
                        counter++;
                    }

                    //+++
                    var data2 = woof_stat_data[1];
                    counter = 1;
                    var graph_count = 0;
                    for (let i in data2) {

                        var graph = {};
                        var html = "";
                        var id = 'chart_div_1_set_' + graph_count;
                        html = '<div class="woof_stat_one_graph"><span class="woof_stat_graph_title">' + data2[i]['tax_name'] + '</span>';
                        //inline must be as it hidden, FIX
                        html += "<div id='" + id + "' style='width: 100%; height: 500px;'></div></div>";
                        jQuery('#chart_div_1_set').append(html);
                        counter = 1;

                        for (let term_name in data2[i]['terms']) {
                            if (counter > parseInt(woof_stat_vars.max_items_per_graph, 10)) {
                                break;
                            }
                            //+++
                            graph[term_name] = parseInt(data2[i]['terms'][term_name], 10);
                            counter++;
                        }

                        drawChart1(graph, id);
                        graph_count++;
                    }

                } else {
                    var counter = 1;
                    jQuery(woof_stat_data).each(function (i, request_block) {
                        //counter = 0;
                        jQuery(request_block).each(function (ii, item) {
                            if (counter > parseInt(woof_stat_vars.max_items_per_graph, 10)) {
                                return;
                            }
                            //+++
                            if (graph1[item.vname] !== undefined) {
                                graph1[item.vname] = graph1[item.vname] + parseInt(item.val, 10);
                            } else {
                                graph1[item.vname] = parseInt(item.val, 10);
                            }

                            counter++;
                        });
                    });
                }
                drawChart1(graph1, 'chart_div_1');
                //***

            }

            woof_stat_process_monitor(woof_stat_vars.woof_stat_finished);
            jQuery('#woof_stat_print_btn').show(200);
      //  } catch (e) {
      //      console.log(woof_stat_vars.woof_stat_troubles);
     //   }

        return false;
    }
    function drawChart1(graph1, id) {

        var data = new google.visualization.DataTable();
        data.addColumn('string', 'X');
        data.addColumn('number', 'Y');
        var rows_data = [];

        jQuery.each(graph1, function (index, value) {
            rows_data.push([index + " (" + value + ")", value]);
        });
        data.addRows(rows_data);


        // Set chart options
        var options = {
            'title': 'Graph 1',
            chartArea: {left: 0, top: 0, width: "100%", height: "100%"}
        };

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById(id));
        chart.draw(data, options);
    }


    function drawChart2(graph2) {
        var data = google.visualization.arrayToDataTable(graph2);

        // Set chart options
        var options = {
            'title': 'Graph 2',
            chartArea: {left: 0, top: 0, width: "100%", height: "100%"}
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div_2'));
        chart.draw(data, options);

    }
jQuery(document).ready(function () {
    //reset cache of "Statistical parameters" drop-down
    jQuery("#woof_stat_snippet option[selected]").removeAttr("selected");

    //+++
    //*** Load the Visualization API and the corechart package.
    try {
        google.charts.load('current', {'packages': ['corechart', 'bar']});
    } catch (e) {
        console.log(woof_stat_vars.woof_stat_google);
    }
    //+++
    jQuery('.woof_cron_system').change(function () {
        var state = parseInt(jQuery(this).val(), 10);
        if (state === 1) {
            //external
            jQuery('.woof_external_cron_option').show(200);
            jQuery('.woof_wp_cron_option').hide(200);
        } else {
            jQuery('.woof_external_cron_option').hide(200);
            jQuery('.woof_wp_cron_option').show(200);
        }
    });

    //+++
    jQuery('#woof_stat_connection').on('click', function () {
        var data = {
            action: "woof_stat_check_connection",
            woof_stat_host: jQuery("input[name='woof_settings[woof_stat][server_options][host]']").val(),
            woof_stat_user: jQuery("input[name='woof_settings[woof_stat][server_options][host_user]']").val(),
            woof_stat_name: jQuery("input[name='woof_settings[woof_stat][server_options][host_db_name]']").val(),
            woof_stat_pswd: jQuery("input[name='woof_settings[woof_stat][server_options][host_pass]']").val(),

        };
        jQuery.post(ajaxurl, data, function (content) {
            alert(content);
        });
    });
    jQuery('#woof_update_db').on('click', function () {
        var data = {
            action: "woof_stat_update_db"
        };
        jQuery.post(ajaxurl, data, function (content) {
            alert(content);
        });
    });

    //+++

});