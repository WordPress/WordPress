jQuery(function() {
    var ajaxurl = window.ajaxurl;
    jQuery('.w3tc-widget-ps-view-all').click(function() {
        window.open('admin.php?page=w3tc_dashboard&w3tc_test_pagespeed_results&_wpnonce=' + jQuery(this).metadata().nonce, 'pagespeed_results', 'width=800,height=600,status=no,toolbar=no,menubar=no,scrollbars=yes');

        return false;
    });

    jQuery('.w3tc-widget-ps-refresh').click(function() {
        document.location.href = 'admin.php?page=w3tc_dashboard&w3tc_widget_pagespeed_force=1';
    });

    jQuery(document).ready(function() {
        var forumLoading = jQuery('#w3tc_latest').find('div.inside:visible').find('.widget-loading');
        if (forumLoading.length) {
            var forumLoadingParent = forumLoading.parent();
            setTimeout(function() {
                forumLoadingParent.load(
                    ajaxurl + '?action=w3tc_widget_latest_ajax&_wpnonce=' +
                        jQuery(forumLoading).metadata().nonce,
                    function () {
                        forumLoadingParent.hide().slideDown('normal',
                            function() {
                                jQuery(this).css('display', '');
                            });
                    });
            }, 500);
        }
        var newsLoading = jQuery('#w3tc_latest_news').find('div.inside:visible').find('.widget-loading');
        if (newsLoading.length) {
            var newsLoadingParent = newsLoading.parent();
            setTimeout(function() {
                newsLoadingParent.load(
                    ajaxurl + '?action=w3tc_widget_latest_news_ajax&_wpnonce=' +
                        jQuery(newsLoading).metadata().nonce,
                    function () {
                        newsLoadingParent.hide().slideDown('normal',
                            function() {
                                jQuery(this).css('display', '');
                            });
                    });
            }, 500);
        }

        jQuery('.w3tc-service').click(function () {
            var request_type = jQuery(this);
            var type = request_type.val();
            var service = jQuery('#buy-w3-service');
            service.attr("disabled", "disabled");
            jQuery.getJSON(ajaxurl +'?action=w3tc_action_payment_code&request_type=' + type + '&_wpnonce=' + request_type.metadata().nonce,
                function(data) {
                    var area = jQuery('#buy-w3-service-area');
                    area.empty();
                    jQuery.each(data, function (key, val) {
                        jQuery('<input>').attr({
                            type: 'hidden',
                            id: key,
                            name: key,
                            value: val.replace(/&amp;/g, '&')
                        }).appendTo('#buy-w3-service-area');
                    })
                }
            );
            service.removeAttr("disabled");

        });

        jQuery('#buy-w3-service-cancel').live('click', function() {
            jQuery('input:radio[name=service]:checked').prop('checked', false);
            jQuery('#buy-w3-service-area').empty();
            jQuery('#buy-w3-service').attr("disabled", "disabled");
        });

        jQuery('#buy-w3-service').live('click', function() {
            alert('Do not forget to fill out the support form after purchasing.');
        });
        var nr_widget = jQuery('#new-relic-widget');
        nr_widget.find('div.top-five').hide();
        nr_widget.find('h5').click(function () {
            jQuery(this).find('div').toggleClass('close');
            jQuery(this).parents('.wrapper').find("div.top-five").toggle();
        });
    });
});
