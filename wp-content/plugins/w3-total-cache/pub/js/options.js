function w3tc_popup(url, name, width, height) {
    if (width === undefined) {
        width = 800;
    }
    if (height === undefined) {
        height = 600;
    }

    return window.open(url, name, 'width=' + width + ',height=' + height + ',status=no,toolbar=no,menubar=no,scrollbars=yes');
}

function w3tc_input_enable(input, enabled) {
    jQuery(input).each(function() {
        var me = jQuery(this);
        if (enabled) {
            me.removeAttr('disabled');
        } else {
            me.attr('disabled', 'disabled');
        }

        if (enabled) {
            me.next('[type=hidden]').remove();
        } else {
            var t = me.attr('type');
            if ((t != 'radio' && t != 'checkbox') || me.is(':checked')) {
                me.after(jQuery('<input />').attr({
                    type: 'hidden',
                    name: me.attr('name')
                }).val(me.val()));
            }
        }
    });
}

function w3tc_minify_js_file_clear() {
    if (!jQuery('#js_files :visible').size()) {
        jQuery('#js_files_empty').show();
    } else {
        jQuery('#js_files_empty').hide();
    }
}

function w3tc_minify_css_file_clear() {
    if (!jQuery('#css_files :visible').size()) {
        jQuery('#css_files_empty').show();
    } else {
        jQuery('#css_files_empty').hide();
    }
}

function w3tc_mobile_groups_clear() {
    if (!jQuery('#mobile_groups li').size()) {
        jQuery('#mobile_groups_empty').show();
    } else {
        jQuery('#mobile_groups_empty').hide();
    }
}

function w3tc_referrer_groups_clear() {
    if (!jQuery('#referrer_groups li').size()) {
        jQuery('#referrer_groups_empty').show();
    } else {
        jQuery('#referrer_groups_empty').hide();
    }
}

function w3tc_minify_js_file_add(theme, template, location, file) {
    var append = jQuery('<li><table><tr><th>&nbsp;</th><th>File URI:</th><th>Template:</th><th colspan="3">Embed Location:</th></tr><tr><td>' + (jQuery('#js_files li').size() + 1) + '.</td><td><input class="js_enabled" type="text" name="js_files[' + theme + '][' + template + '][' + location + '][]" value="" size="70" \/></td><td><select class="js_file_template js_enabled"></select></td><td><select class="js_file_location js_enabled"><option value="include">Embed in &lt;head&gt;</option><option value="include-body">Embed after &lt;body&gt;</option><option value="include-footer">Embed before &lt;/body&gt;</option></select></td><td><input class="js_file_delete js_enabled button" type="button" value="Delete" /> <input class="js_file_verify js_enabled button" type="button" value="Verify URI" /></td></tr></table><\/li>');
    append.find('input:text').val(file);
    var select = append.find('.js_file_template');
    for (var i in minify_templates[theme]) {
        select.append(jQuery('<option />').val(i).html(minify_templates[theme][i]));
    }
    select.val(template);
    jQuery(append).find('.js_file_location').val(location);
    jQuery('#js_files').append(append).find('li:last input:first').focus();
    w3tc_minify_js_file_clear();
}

function w3tc_minify_css_file_add(theme, template, file) {
    var append = jQuery('<li><table><tr><th>&nbsp;</th><th>File URI:</th><th colspan="2">Template:</th></tr><tr><td>' + (jQuery('#css_files li').size() + 1) + '.</td><td><input class="css_enabled" type="text" name="css_files[' + theme + '][' + template + '][include][]" value="" size="70" \/></td><td><select class="css_file_template css_enabled"></select></td><td><input class="css_file_delete css_enabled button" type="button" value="Delete" /></td><td><input class="css_file_verify css_enabled button" type="button" value="Verify URI" /></td></tr></table><\/li>');
    append.find('input:text').val(file);
    var select = append.find('.css_file_template');
    for (var i in minify_templates[theme]) {
        select.append(jQuery('<option />').val(i).html(minify_templates[theme][i]));
    }
    select.val(template);
    jQuery('#css_files').append(append).find('li:last input:first').focus();
    w3tc_minify_css_file_clear();
}

function w3tc_minify_js_theme(theme) {
    jQuery('#js_themes').val(theme);
    jQuery('#js_files :text').each(function() {
        var input = jQuery(this);
        if (input.attr('name').indexOf('js_files[' + theme + ']') != 0) {
            input.parents('li').hide();
        } else {
            input.parents('li').show();
        }
    });
    w3tc_minify_js_file_clear();
}

function w3tc_minify_css_theme(theme) {
    jQuery('#css_themes').val(theme);
    jQuery('#css_files :text').each(function() {
        var input = jQuery(this);
        if (input.attr('name').indexOf('css_files[' + theme + ']') != 0) {
            input.parents('li').hide();
        } else {
            input.parents('li').show();
        }
    });
    w3tc_minify_css_file_clear();
}

function w3tc_minify_filename_test(url, success, failure) {
    var timestamp = new Date().getTime();
    jQuery.get(url + '?t=' + timestamp, function (data) {
        success(data, url);
    })
    .fail(function (data) {
        failure(data, url);
    });
}
function w3tc_minify_filename_test_once(url, filename) {
    jQuery(function($) {
        w3tc_minify_filename_test(url + filename + '.css',
            function(data, url) {
                if (data == 'retry') {
                    $.get(url, function(data2) {
                        if (data2 != 'content ok') {
                            $('#minify_auto_error').show();
                            w3tc_start_minify_try_solve();
                        } else {
                            $.get(ajaxurl, {action:'w3tc_minify_disable_filename_test'});
                        }
                    });
                } else if (data != 'content ok') {
                    $('#minify_auto_error').show();
                    w3tc_start_minify_try_solve();
                } else {
                    $.get(ajaxurl, {action:'w3tc_minify_disable_filename_test'});
                }
            },
            function (data, url) {
                $('#minify_auto_error').show();
                w3tc_start_minify_try_solve();
            }
        );
    })
}

function w3tc_filename_auto_solve(testUrl) {
    var minLength = 100, maxLength = 246;
    jQuery('#minify_auto_test_loading').toggleClass('minify_auto_test');
    w3tc_do_filename_auto_step(testUrl,minLength, maxLength, maxLength, false);
}

function w3tc_do_filename_auto_step(testUrl,minLength, maxLength, tryLength, minTestedAndSuccessful) {
    tryLength = Math.floor(tryLength);
    var testString = new Array(tryLength+1).join('X');
    var timestamp = new Date().getTime();
    var url = testUrl + testString + '.css' + '?t=' + timestamp;
    jQuery.get(url, function (data) {
        if (data == 'retry') {
            jQuery.get(url, function (retryResult) {
                if (retryResult == 'content ok') {
                    w3tc_do_success(testUrl,minLength, maxLength, tryLength, minTestedAndSuccessful);
                } else {
                    jQuery.get(ajaxurl, {action:'w3tc_minify_disable_filename_test'});
                    alert('Plugin could not solve the Minify Auto issue automatically.');
                    jQuery('#minify_auto_test_loading').toggleClass('minify_auto_test');
                    jQuery('#minify_auto_error').html('<p>Minify Auto does not work properly. Try using Minify Manual instead ' +
                        'or try another  Minify cache method.</p>');
                }
            });
        } else if (data == 'content ok') {
            w3tc_do_success(testUrl,minLength, maxLength, tryLength, minTestedAndSuccessful);
        } else {
            w3tc_do_failure(testUrl,minLength, maxLength, tryLength, minTestedAndSuccessful);
        }
    }).fail(function (data) {
            w3tc_do_failure(testUrl,minLength, maxLength, tryLength, minTestedAndSuccessful);
    });
}

function w3tc_do_success(testUrl,minLength, maxLength, tryLength, minTestedAndSuccessful) {
    if ((maxLength - tryLength) < 10) {
        w3tc_finish_with(tryLength);
        return;
    }
    w3tc_do_filename_auto_step(testUrl, tryLength, maxLength, (tryLength + maxLength) / 2, true);
}

function w3tc_do_failure(testUrl,minLength, maxLength, tryLength, minTestedAndSuccessful) {
    if ((tryLength - minLength) < 10) {
        if (minTestedAndSuccessful) {
            w3tc_finish_with(minLength);
            return;
        } else    if (tryLength <= minLength) {
            jQuery.get(ajaxurl, {action:'w3tc_minify_disable_filename_test'});
            var url;
            if (w3_use_network_link)
                url = 'network/admin.php?page=w3tc_minify#advanced';
            else
                url = 'admin.php?page=w3tc_minify#advanced';

            alert('Plugin could not solve the Minify Auto issue automatically.');
            jQuery('#minify_auto_test_loading').toggleClass('minify_auto_test');
            jQuery('#minify_auto_error').html('<p>Minify Auto does not work properly. Try using Minify Manual instead ' +
                'or try another Minify cache method. You can also try a lower filename length value manually on ' +
                '<a href="' + url + '">settings page</a> by checking "Disable the Minify Auto automatic filename test" </p>');
            return;
        }
        else {
            w3tc_do_filename_auto_step(testUrl,minLength, maxLength, minLength, false);
            return;
        }
    }
    w3tc_do_filename_auto_step(testUrl, minLength, maxLength, (tryLength + minLength) / 2, minTestedAndSuccessful);
}

function w3tc_finish_with(length) {
    jQuery.get(ajaxurl, {action:'w3tc_minify_change_filename_length', maxlength: length}, function (changeResult) {
        if (changeResult == 1) {
            jQuery('#minify_auto_filename_length').val(length);
            jQuery('#minify_auto_test_loading').toggleClass('minify_auto_test');
            alert('Minify Auto filename length changed too ' + length);
            jQuery('#minify_auto_error').hide();
        } else {
            jQuery('#minify_auto_test_loading').toggleClass('minify_auto_test');
            alert('Tried to change Minify Auto filename length too ' + length + ' but failed.');
            jQuery('#minify_auto_error').hide();
        }
    });

}

function w3tc_cdn_get_cnames() {
    var cnames = [];

    jQuery('#cdn_cnames input[type=text]').each(function() {
        var cname = jQuery(this).val();

        if (cname) {
            var match = /^\*\.(.*)$/.exec(cname);

            if (match) {
                cnames = [];
                for (var i = 1; i <= 10; i++) {
                    cnames.push('cdn' + i + '.' + match[1]);
                }
                return false;
            }

            cnames.push(cname);
        }
    });

    return cnames;
}

function w3tc_cdn_cnames_assign() {
    var li = jQuery('#cdn_cnames li'), size = li.size();

    if (size > 1) {
        li.eq(0).find('.cdn_cname_delete').show();
    } else {
        li.eq(0).find('.cdn_cname_delete').hide();
    }

    jQuery(li).each(function(index) {
        var label = '';

        if (size > 1) {
            switch (index) {
                case 0:
                    label = '(reserved for CSS)';
                    break;

                case 1:
                    label = '(reserved for JS in <head>)';
                    break;

                case 2:
                    label = '(reserved for JS after <body>)';
                    break;

                case 3:
                    label = '(reserved for JS before </body>)';
                    break;
            }
        }

        jQuery(this).find('span').text(label);
    });
}

function w3tc_cloudflare_api_request(action, value, nonce) {

    var email = jQuery('#cloudflare_email');
    var key = jQuery('#cloudflare_key');
    var zone = jQuery('#cloudflare_zone');

    if (!email.val()) {
        alert('Please enter CloudFlare E-Mail.');
        email.focus();
        return false;
    }

    if (!key.val()) {
        alert('Please enter CloudFlare API key.');
        key.focus();
        return false;
    }

    if (!zone.val()) {
        alert('Please enter CloudFlare zone.');
        zone.focus();
        return false;
    }

    jQuery.post(ajaxurl, {
        action:'w3tc_cloudflare_api_request',
        email: email.val(),
        key: key.val(),
        zone: zone.val(),
        command: action,
        value: value,
        _wpnonce: nonce
    }, function(data) {
        alert(data.result ? 'OK' : 'Request failed. Error: ' + data.error);
    }, 'json');

    return true;
}

function w3tc_toggle(name, check) {
    if (check === undefined) {
        check = true;
    }

    var id = '#' + name, cls = '.' + name;

    jQuery(cls).click(function() {
        var checked = check;

        jQuery(cls).each(function() {
            var _checked = jQuery(this).is(':checked');

            if ((check && !_checked) || (!check && _checked)) {
                checked = !check;

                return false;
            }
        });

        if (checked) {
            jQuery(id).attr('checked', 'checked');
        } else {
            jQuery(id).removeAttr('checked');
        }
    });

    jQuery(id).click(function() {
        var checked = jQuery(this).is(':checked');
        jQuery(cls).each(function() {
            if (checked) {
                jQuery(this).attr('checked', 'checked');
            } else {
                jQuery(this).removeAttr('checked');
            }
        });
    });
}

function w3tc_toggle2(name, dependent_ids) {
    var id = '#' + name, dependants = '', n;
    for (n = 0; n < dependent_ids.length; n++)
        dependants += (n > 0 ? ',' : '') + '#' + dependent_ids[n];
    
    jQuery(dependants).click(function() {
        var total_checked = true;

        jQuery(dependants).each(function() {
            var current_checked = jQuery(this).is(':checked');

            if (!current_checked)
                total_checked = false;
        });

        if (total_checked) {
            jQuery(id).attr('checked', 'checked');
        } else {
            jQuery(id).removeAttr('checked');
        }
    });

    jQuery(id).click(function() {
        var checked = jQuery(this).is(':checked');
        jQuery(dependants).each(function() {
            if (checked) {
                jQuery(this).attr('checked', 'checked');
            } else {
                jQuery(this).removeAttr('checked');
            }
        });
    });
}

function w3tc_beforeupload_bind() {
    jQuery(window).bind('beforeunload', w3tc_beforeunload);
}

function w3tc_beforeupload_unbind() {
    jQuery(window).unbind('beforeunload', w3tc_beforeunload);
}

function w3tc_beforeunload() {
    return 'Navigate away from this page without saving your changes?';
}

var setting_changes = null;
function w3tc_change_setting(key, state,network) {
    var class_id = key.replace(/\./g, '_');
    if (setting_changes == null)
        setting_changes = jQuery('.setting_changes').length;
    setting_changes--;
    jQuery.post(ajaxurl,{action:'w3tc_change_setting', setting:key, state:state, network:network}, function(data) {
        if (data == 'failure') {
            alert('Could not change the configuration setting.')
        } else {
            if ('done' == data && 'all' == key) {
                jQuery('#w3tc_new_settings').html('<p>All setting changes have been applied.</p>');
            } else if ('done' == data) {
                jQuery('li.'+class_id).html('<span>Action Applied</span>');
                jQuery('li.'+class_id+' span').fadeOut(2000, function() {
                    jQuery('li.'+class_id).remove();
                    jQuery('#w3tc_new_settings').html('<p>All setting changes have been applied.</p>');
                });
            } else {
                jQuery('li.'+class_id).html('<span>Action Applied</span>');
                jQuery('li.'+class_id+' span').fadeOut(2000, function() {
                    jQuery('li.'+class_id).remove();
                });
                if (setting_changes <= 0)
                    jQuery('#w3tc_new_settings').html('<p>All setting changes have been applied.</p>');
            }
        }
    });
}

/**
 *
 * @param type
 * @param nonce
 */
function w3tc_validate_cdn_key_result(type, nonce) {
  var key = jQuery('#cdn_' + type + '_authorization_key').val();
  jQuery('#cdn_result_message').text('');
  var result = jQuery('#validate_cdn_key_result');
  if (key.length == 0) {
    result.html('').removeClass('w3tc-error').removeClass('w3tc-success').removeClass('w3tc-checking');
    return;
  }
  result.html('Validating ...').addClass('w3tc-checking');

  if (key.split('+').length!=3) {
    result.removeClass('w3tc-checking');
    result.html('Key is invalid').addClass('w3tc-error');
    return;
  }
  var params = {
    w3tc_cdn_validate_authorization_key: 1,
    type: type,
    authorization_key: key,
    _wpnonce: nonce
  };
  jQuery('#validate_cdn_key').prop('disabled', true);
  jQuery.post('admin.php?page=w3tc_dashboard', params, function(data) {
    result.html('').removeClass('w3tc-error').removeClass('w3tc-success').removeClass('w3tc-checking');
    var element;
    if (data.result == 'create') {
      jQuery('#create_zone_area').show();
      element = jQuery('#normal-sortables');
      if (element.length)
        element.masonry('reload');
    } else if (data.result == 'single') {
      var message = data.cnames.join('<br />');
      jQuery('#cdn_result_message').html('Preexisting zone has been selected and saved for the CDN engine. Hostnames used: <br />' + message);
      jQuery('#cdn_cnames > :first-child > :first-child').val(data.cnames.shift());
      for (var i in data.cnames) {
        jQuery('#cdn_cnames').append('<li><input type="text" name="cdn_cnames[]" value="' + data.cnames[i] + '" size="60" /> <input class="button cdn_cname_delete" type="button" value="Delete" /> <span></span></li>');
        w3tc_cdn_cnames_assign();
      }
    } else if (data.result == 'many') {
      jQuery('#select_pull_zone').show();
      var mySelect = jQuery('#cdn_' + type +'_zone_id');
      mySelect.empty();
      jQuery.each(data.zones, function(val, zone) {
        mySelect.append(
          jQuery('<option></option>').val(zone.id).html(zone.name)
        );
      });
      if (data.data.id) {
        jQuery("#cdn_maxcdn_zone_id").val(data.data.id);
        var message = data.data.cnames.join('<br />');
        jQuery('#cdn_result_message').html('Preexisting zone has been selected and saved for the CDN engine. Hostnames used: <br />' + message);
        jQuery('#cdn_cnames > :first-child > :first-child').val(data.data.cnames.shift());
        for (var x in data.data.cnames) {
          jQuery('#cdn_cnames').append('<li><input type="text" name="cdn_cnames[]" value="' + data.data.cnames[x] + '" size="60" /> <input class="button cdn_cname_delete" type="button" value="Delete" /> <span></span></li>');
          w3tc_cdn_cnames_assign();
        }
      }
      element = jQuery('#normal-sortables');
        if (element.length)
          element.masonry('reload');
    }
    result.removeClass('w3tc-checking');
    if (data.result != 'error' && data.result != 'notsupported')
      result.html('Key is valid').addClass('w3tc-success');
    else
      result.html(data.message).addClass('w3tc-error');
    jQuery('#validate_cdn_key').prop('disabled', false);
  }, 'json');
}

function w3tc_create_zone(type, nonce) {
  var params = {
    w3tc_cdn_auto_create_netdna_maxcdn_pull_zone: 1,
    type: type,
    authorization_key: jQuery('#cdn_' + type + '_authorization_key').val(),
    _wpnonce: nonce
  };
  var result = jQuery('#create_pull_zone_result');
  result.html('').removeClass('w3tc-error').removeClass('w3tc-success').removeClass('w3tc-checking');
  jQuery("#create_default_zone").prop('disabled', true);
  result.html('Creating ... ').addClass('w3tc-checking');

  jQuery.post('admin.php?page=w3tc_dashboard', params, function(data) {
    if (data.cnames && data.cnames.length) {
      jQuery('#cdn_cnames > :first-child > :first-child').val(data.cnames.shift());
      for (var i in data.cnames) {
          jQuery('#cdn_cnames').append('<li><input type="text" name="cdn_cnames[]" value="' + data.cnames[i] + '" size="60" /> <input class="button cdn_cname_delete" type="button" value="Delete" /> <span></span></li>');
        w3tc_cdn_cnames_assign();
      }
      result.text('Created ').removeClass('w3tc-checking').addClass('w3tc-success');
    } else {
      result.text(data.message).removeClass('w3tc-checking').addClass('w3tc-error');
      jQuery("#create_default_zone").prop('disabled', false);

    }
  }, 'json');
}

function w3tc_use_poll_zone(type, nonce) {
  var zone_id = jQuery("#cdn_" + type +"_zone_id").val();
  var params = {
    w3tc_cdn_use_netdna_maxcdn_pull_zone: 1,
    type: type,
    zone_id: zone_id,
    authorization_key: jQuery('#cdn_' + type + '_authorization_key').val(),
    _wpnonce: nonce
  };
  jQuery.post('admin.php?page=w3tc_dashboard', params, function(data) {
    if (data.result == 'valid') {
      var message = data.cnames.join('<br />');
      jQuery('#cdn_result_message').html('Zone has been selected and saved for the CDN engine. Hostnames used: <br />' + message);
      jQuery('#cdn_cnames > :first-child > :first-child').val(data.cnames.shift());
      for (var i in data.cnames) {
        jQuery('#cdn_cnames').append('<li><input type="text" name="cdn_cnames[]" value="' + data.cnames[i] + '" size="60" /> <input class="button cdn_cname_delete" type="button" value="Delete" /> <span></span></li>');
        w3tc_cdn_cnames_assign();
      }
    } else {
      alert(data.message);
    }
  }, 'json');
}

jQuery(function() {
    // general page
    w3tc_toggle('enabled');

    jQuery('#w3tc_general').submit(function(event) {
        var el = jQuery("[was_clicked=yes]").get(0);
        if (el.id == 'flush_all' && jQuery('#cloudflare_enabled').is(':checked')) {
            if (!confirm('Purging your site\'s CloudFlare cache will remove all CloudFlare cache files. It may take up to 48 hours for the CloudFlare cache to completely rebuild on CloudFlare\'s global network. Are you sure you want to purge CloudFlare the cache? Clicking cancel will cancel "empty all caches".')) {
                event.preventDefault();
            }
        }
        jQuery(el).removeAttr("was_clicked");
    });

    jQuery('#w3tc_general [type=submit]').bind('click', function(){
        jQuery(this).attr('was_clicked','yes');
    });

    jQuery('.button-tweet').live('click', function() {
        window.open('http://twitter.com/?status=' + encodeURIComponent('YES! I optimized my #wordpress site\'s #performance using the W3 Total Cache #WPO #plugin by @w3edge. Check it out! http://j.mp/A69xX'), '_blank');
    });

    jQuery('#common_support').change(function() {
        var where= jQuery(this).val();
        jQuery.post(ajaxurl,{action:'w3tc_link_support', w3tc_common_support_us:where}, function(data) {
            alert(data);
        });
    });

    jQuery('.button-rating').live('click', function() {
        window.open('http://wordpress.org/support/view/plugin-reviews/w3-total-cache?rate=5#postform', '_blank');
    });
    jQuery('.w3tc_read_technical_info').click(function() {
        jQuery('.w3tc_technical_info').toggle();
    });

    jQuery('#newrelic_verify_api_key').click(function() {
        var api_key = jQuery('#newrelic_api_key').val();

        if (!api_key) {
            alert('Please enter an API key and try again.');
            return;
        }
        var params = {
            action: 'w3tc_verify_newrelic_api_key',
            api_key: api_key
        };

        jQuery.get(ajaxurl, params, function(data) {
            if (data) {
                jQuery('#newrelic_account_id').val(data);
                alert('API Key verified');
            }else {
                alert('The API key could not be verified. Please check it and try again.');
            }
        });
    });

    jQuery('#newrelic_retrieve_applications').click(function() {
        var api_key = jQuery('#newrelic_api_key').val();
        var account_id = jQuery('#newrelic_account_id').val();
        if (!api_key) {
            alert('Please enter an API key.');
            return;
        }

        var params = {
            action: 'w3tc_get_newrelic_applications',
            api_key: api_key,
            account_id: account_id
        };

        jQuery.getJSON(ajaxurl, params, function(data) {
            var app_id_select = jQuery('#newrelic_application_id_dropdown');
            var count = 0;
            app_id_select.empty();
            app_id_select
                .append(jQuery("<option></option>")
                .attr("value",'')
                .text('-- Select Application --'));
            jQuery.each(data, function(key, value) {
                app_id_select
                    .append(jQuery("<option></option>")
                    .attr("value",key)
                    .text(value));
                count++;
            });
            if (count == 0)
                alert('Could not retrieve any applications. Verify your API key.');
        });
    });


    jQuery('#plugin_license_key_verify').click(function() {
        original_button_value = jQuery('#plugin_license_key_verify').val();
        jQuery('#plugin_license_key_verify').val("Checking...");

        var license_key = jQuery('#plugin_license_key').val();

        if (!license_key) {
            alert('Please enter an license key and try again.');
            return;
        }
        var params = {
            action: 'w3tc_verify_plugin_license_key',
            license_key: license_key
        };

        jQuery.get(ajaxurl, params, function(data) {
            jQuery('#plugin_license_key_verify').val(original_button_value);
            if (data == 'expired') {
                alert('The license key has expired. Please renew it.');
            }else if(data == 'host_valid' || data == 'valid') {
                alert('License key is correct.');
            }else if (data == 'another_site_active') {
                alert('License key is correct but already in use on another site. See the FAQ for how to enable Pro version in development mode.');
            }else {
                alert('The license key is not valid. Please check it and try again.');
            }
        });
    });

    jQuery("#manual").click(function () {
        jQuery('#newrelic_application_name_textbox_div').show();
        jQuery('#newrelic_application_id_dropdown_div').hide();
    });

    jQuery("#dropdown").click(function () {
        jQuery('#newrelic_application_name_textbox_div').hide();
        jQuery('#newrelic_application_id_dropdown_div').show();
    });

    jQuery("#newrelic_use_network_wide_id").change(function () {
        var conf = jQuery('#newrelic_configuration_sealed');
        if (this.checked)
            conf.prop('checked', true).prop('disabled',true);
        else if(!jQuery('#common_force_master').is(':checked'))
            conf.prop('disabled', false);
    });

    // pagecache page
    w3tc_input_enable('#pgcache_reject_roles input[type=checkbox]', jQuery('#pgcache_reject_logged_roles:checked').size());
    jQuery('#pgcache_reject_logged_roles').live('click', function(){
        w3tc_input_enable('#pgcache_reject_roles input[type=checkbox]', jQuery('#pgcache_reject_logged_roles:checked').size());
    });

    if(jQuery('#pgcache_cache_nginx_handle_xml').is('*'))
        jQuery('#pgcache_cache_nginx_handle_xml').attr('checked',jQuery('#pgcache_cache_feed').is(':checked'));

    jQuery('#pgcache_cache_feed').change(function(){
        if(jQuery('#pgcache_cache_nginx_handle_xml').is('*'))
            jQuery('#pgcache_cache_nginx_handle_xml').attr('checked',this.checked);
    });

    // browsercache page
    w3tc_toggle2('browsercache_last_modified',
        ['browsercache_cssjs_last_modified', 'browsercache_html_last_modified',
            'browsercache_other_last_modified']);
    w3tc_toggle2('browsercache_expires',
        ['browsercache_cssjs_expires', 'browsercache_html_expires',
            'browsercache_other_expires']);
    w3tc_toggle2('browsercache_cache_control',
        ['browsercache_cssjs_cache_control', 'browsercache_html_cache_control',
            'browsercache_other_cache_control']);
    w3tc_toggle2('browsercache_etag',
        ['browsercache_cssjs_etag', 'browsercache_html_etag', 'browsercache_other_etag']);
    w3tc_toggle2('browsercache_w3tc',
        ['browsercache_cssjs_w3tc', 'browsercache_html_w3tc', 'browsercache_other_w3tc']);
    w3tc_toggle2('browsercache_compression',
        ['browsercache_cssjs_compression', 'browsercache_html_compression', 'browsercache_other_compression']);
    w3tc_toggle2('browsercache_replace',
        ['browsercache_cssjs_replace', 'browsercache_other_replace']);
    w3tc_toggle2('browsercache_nocookies',
        ['browsercache_cssjs_nocookies', 'browsercache_other_nocookies']);

    // minify page
    w3tc_input_enable('.html_enabled', jQuery('#minify_html_enable:checked').size());
    w3tc_input_enable('.js_enabled', jQuery('#minify_js_enable:checked').size());
    w3tc_input_enable('.css_enabled', jQuery('#minify_css_enable:checked').size());

    w3tc_minify_js_theme(jQuery('#js_themes').val());
    w3tc_minify_css_theme(jQuery('#css_themes').val());

    jQuery('#minify_html_enable').click(function() {
        w3tc_input_enable('.html_enabled', this.checked);
    });

    jQuery('#minify_js_enable').click(function() {
        w3tc_input_enable('.js_enabled', jQuery(this).is(':checked'));
    });

    jQuery('#minify_css_enable').click(function() {
        w3tc_input_enable('.css_enabled', jQuery(this).is(':checked'));
    });

    jQuery('.js_file_verify,.css_file_verify').live('click', function() {
        var file = jQuery(this).parents('li').find(':text').val();
        if (file == '') {
            alert('Empty URI');
        } else {
            var url = '';
            if (/^https?:\/\//.test(file)) {
                url = file;
            } else {
                url = '/' + file;
            }
            w3tc_popup(url, 'file_verify');
        }
    });

    jQuery('.js_file_template').live('change', function() {
        jQuery(this).parents('li').find(':text').attr('name', 'js_files[' + jQuery('#js_themes').val() + '][' + jQuery(this).val() + '][' + jQuery(this).parents('li').find('.js_file_location').val() + '][]');
    });

    jQuery('.css_file_template').live('change', function() {
        jQuery(this).parents('li').find(':text').attr('name', 'css_files[' + jQuery('#css_themes').val() + '][' + jQuery(this).val() + '][include][]');
    });

    jQuery('.js_file_location').live('change', function() {
        jQuery(this).parents('li').find(':text').attr('name', 'js_files[' + jQuery('#js_themes').val() + '][' + jQuery(this).parents('li').find('.js_file_template').val() + '][' + jQuery(this).val() + '][]');
    });

    jQuery('.js_file_delete').live('click', function() {
        var parent = jQuery(this).parents('li');
        if (parent.find('input[type=text]').val() == '' || confirm('Are you sure you want to remove this JS file?')) {
            parent.remove();
            w3tc_minify_js_file_clear();
            w3tc_beforeupload_bind();
        }

        return false;
    });

    jQuery('.css_file_delete').live('click', function() {
        var parent = jQuery(this).parents('li');
        if (parent.find('input[type=text]').val() == '' || confirm('Are you sure you want to remove this CSS file?')) {
            parent.remove();
            w3tc_minify_css_file_clear();
            w3tc_beforeupload_bind();
        }

        return false;
    });

    jQuery('#js_file_add').click(function() {
        w3tc_minify_js_file_add(jQuery('#js_themes').val(), 'default', 'include', '');
    });

    jQuery('#css_file_add').click(function() {
        w3tc_minify_css_file_add(jQuery('#css_themes').val(), 'default', '');
    });

    jQuery('#js_themes').change(function() {
        w3tc_minify_js_theme(jQuery(this).val());
    });

    jQuery('#css_themes').change(function() {
        w3tc_minify_css_theme(jQuery(this).val());
    });

    jQuery('#minify_form').submit(function() {
        var js = [], css = [], invalid_js = [], invalid_css = [], duplicate = false, query_js = [], query_css = [];

        jQuery('#js_files :text').each(function() {
            var v = jQuery(this).val(), n = jQuery(this).attr('name'), c = v + n, g = '';
            var match = /js_files\[([a-z0-9_\/]+)\]/.exec(n);
            if (match) {
                g = '[' + jQuery('#js_themes option[value=' + match[1] + ']').text() + '] ' + v;
            }
            if (v != '') {
                for (var i = 0; i < js.length; i++) {
                    if (js[i] == c) {
                        duplicate = true;
                        break;
                    }
                }

                js.push(c);

                var qindex = v.indexOf('?');
                if (qindex != -1) {
                    if (!/^(https?:)?\/\//.test(v)) {
                        query_js.push(g);
                    }
                    v = v.substr(0, qindex);
                } else if (!/\.js$/.test(v)) {
                    invalid_js.push(g);
                }
            }
        });

        jQuery('#css_files :text').each(function() {
            var v = jQuery(this).val(), n = jQuery(this).attr('name'), c = v + n, g = '';
            var match = /css_files\[([a-z0-9_\/]+)\]/.exec(n);
            if (match) {
                g = '[' + jQuery('#css_themes option[value=' + match[1] + ']').text() + '] ' + v;
            }
            if (v != '') {
                for (var i = 0; i < css.length; i++) {
                    if (css[i] == c) {
                        duplicate = true;
                        break;
                    }
                }

                css.push(c);

                var qindex = v.indexOf('?');
                if (qindex != -1) {
                    if (!/^(https?:)?\/\//.test(v)) {
                        query_css.push(g);
                    }
                    v = v.substr(0, qindex);
                } else if (!/\.css$/.test(v)) {
                    invalid_css.push(g);
                }
            }
        });

        if (jQuery('#js_enabled:checked').size()) {
            if (invalid_js.length && !confirm('The following files have invalid JS file extension:\r\n\r\n' + invalid_js.join('\r\n') + '\r\n\r\nAre you confident these files contain valid JS code?')) {
                return false;
            }

            if (query_js.length) {
                alert('We recommend using the entire URI for files with query string (GET) variables. You entered:\r\n\r\n' + query_js.join('\r\n'));
                return false;
            }
        }

        if (jQuery('#css_enabled:checked').size()) {
            if (invalid_css.length && !confirm('The following files have invalid CSS file extension:\r\n\r\n' + invalid_css.join('\r\n') + '\r\n\r\nAre you confident these files contain valid CSS code?')) {
                return false;
            }

            if (query_css.length) {
                alert('We recommend using the entire URI for files with query string (GET) variables. You entered:\r\n\r\n' + query_css.join('\r\n'));
                return false;
            }
        }

        if (duplicate) {
            alert('Duplicate files have been found in your minify settings, please check your settings and re-save.');
            return false;
        }

        return true;
    });

    jQuery('#minify_auto_disable_filename_length_test').live('click', function() {
        if(jQuery(this).attr('checked'))
            jQuery('#minify_auto_filename_length').removeAttr('disabled');
        else
            jQuery('#minify_auto_filename_length').attr('disabled','disabled');
    });

    // CDN
    jQuery('.w3tc-tab').click(function() {
        jQuery('.w3tc-tab-content').hide();
        jQuery(this.rel).show();
    });

    w3tc_input_enable('#cdn_reject_roles input[type=checkbox]', jQuery('#cdn_reject_logged_roles:checked').size());
    jQuery('#cdn_reject_logged_roles').live('click', function() {
        w3tc_input_enable('#cdn_reject_roles input[type=checkbox]', jQuery('#cdn_reject_logged_roles:checked').size());
    });

    jQuery('#cdn_export_library').click(function() {
        w3tc_popup('admin.php?page=w3tc_cdn&w3tc_cdn_export_library&_wpnonce=' + jQuery(this).metadata().nonce, 'cdn_export_library');
    });

    jQuery('#cdn_import_library').click(function() {
        w3tc_popup('admin.php?page=w3tc_cdn&w3tc_cdn_import_library&_wpnonce=' + jQuery(this).metadata().nonce, 'cdn_import_library');
    });

    jQuery('#cdn_queue').click(function() {
        w3tc_popup('admin.php?page=w3tc_cdn&w3tc_cdn_queue&_wpnonce=' + jQuery(this).metadata().nonce, 'cdn_queue');
    });

    jQuery('#cdn_rename_domain').click(function() {
        w3tc_popup('admin.php?page=w3tc_cdn&w3tc_cdn_rename_domain&_wpnonce=' + jQuery(this).metadata().nonce, 'cdn_rename_domain');
    });

    jQuery('#cdn_purge').click(function() {
        w3tc_popup('admin.php?page=w3tc_cdn&w3tc_cdn_purge&_wpnonce=' + jQuery(this).metadata().nonce, 'cdn_purge');
    });

    jQuery('.cdn_export').click(function() {
        var metadata = jQuery(this).metadata();
        w3tc_popup('admin.php?page=w3tc_cdn&w3tc_cdn_export&cdn_export_type=' + metadata.type + '&_wpnonce=' + metadata.nonce, 'cdn_export_' + metadata.type);
    });

    jQuery('#validate_cdn_key').click(function() {
      var me = jQuery(this);
      var metadata = me.metadata();
      w3tc_validate_cdn_key_result(metadata.type, metadata.nonce);
    });

    jQuery('#use_poll_zone').click(function() {
      var me = jQuery(this);
      var metadata = me.metadata();
      w3tc_use_poll_zone(metadata.type, metadata.nonce);
    });

    jQuery('#cdn_test').click(function() {
        var me = jQuery(this);
        var metadata = me.metadata();
        var cnames = w3tc_cdn_get_cnames();
        var params = {
            w3tc_cdn_test: 1,
            _wpnonce: metadata.nonce
        };

        switch (metadata.type) {
            case 'ftp':
                jQuery.extend(params, {
                    engine: 'ftp',
                    'config[host]': jQuery('#cdn_ftp_host').val(),
                    'config[user]': jQuery('#cdn_ftp_user').val(),
                    'config[path]': jQuery('#cdn_ftp_path').val(),
                    'config[pass]': jQuery('#cdn_ftp_pass').val(),
                    'config[pasv]': jQuery('#cdn_ftp_pasv:checked').size()
                });

                if (cnames.length) {
                    params['config[domain][]'] = cnames;
                }
                break;

            case 's3':
                jQuery.extend(params, {
                    engine: 's3',
                    'config[key]': jQuery('#cdn_s3_key').val(),
                    'config[secret]': jQuery('#cdn_s3_secret').val(),
                    'config[bucket]': jQuery('#cdn_s3_bucket').val()
                });

                if (cnames.length) {
                    params['config[cname][]'] = cnames;
                }
                break;

            case 'cf':
                jQuery.extend(params, {
                    engine: 'cf',
                    'config[key]': jQuery('#cdn_cf_key').val(),
                    'config[secret]': jQuery('#cdn_cf_secret').val(),
                    'config[bucket]': jQuery('#cdn_cf_bucket').val(),
                    'config[id]': jQuery('#cdn_cf_id').val()
                });

                if (cnames.length) {
                    params['config[cname][]'] = cnames;
                }
                break;

            case 'cf2':
                jQuery.extend(params, {
                    engine: 'cf2',
                    'config[key]': jQuery('#cdn_cf2_key').val(),
                    'config[secret]': jQuery('#cdn_cf2_secret').val(),
                    'config[origin]': jQuery('#cdn_cf2_origin').val(),
                    'config[id]': jQuery('#cdn_cf2_id').val()
                });

                if (cnames.length) {
                    params['config[cname][]'] = cnames;
                }
                break;

            case 'rscf':
                jQuery.extend(params, {
                    engine: 'rscf',
                    'config[user]': jQuery('#cdn_rscf_user').val(),
                    'config[key]': jQuery('#cdn_rscf_key').val(),
                    'config[location]': jQuery('#cdn_rscf_location').val(),
                    'config[container]': jQuery('#cdn_rscf_container').val(),
                    'config[id]': jQuery('#cdn_rscf_id').val()
                });

                if (cnames.length) {
                    params['config[cname][]'] = cnames;
                }
                break;

            case 'azure':
                jQuery.extend(params, {
                    engine: 'azure',
                    'config[user]': jQuery('#cdn_azure_user').val(),
                    'config[key]': jQuery('#cdn_azure_key').val(),
                    'config[container]': jQuery('#cdn_azure_container').val()
                });

                if (cnames.length) {
                    params['config[cname][]'] = cnames;
                }
                break;

            case 'mirror':
                jQuery.extend(params, {
                    engine: 'mirror'
                });

                if (cnames.length) {
                    params['config[domain][]'] = cnames;
                }
                break;

            case 'maxcdn':
                jQuery.extend(params, {
                    engine: 'maxcdn',
                    'config[authorization_key]': jQuery('#cdn_maxcdn_authorization_key').val(),
                    'config[zone_id]': jQuery('#cdn_maxcdn_zone_id').val()
                });

                if (cnames.length) {
                    params['config[domain][]'] = cnames;
                }
                break;
            case 'netdna':
                jQuery.extend(params, {
                    engine: 'netdna',
                    'config[authorization_key]': jQuery('#cdn_netdna_authorization_key').val(),
                    'config[zone_id]': jQuery('#cdn_netdna_zone_id').val()
                });

                if (cnames.length) {
                    params['config[domain][]'] = cnames;
                }
                break;

            case 'cotendo':
                var zones = [], zones_val = jQuery('#cdn_cotendo_zones').val();

                if (zones_val) {
                    zones = zones_val.split(/[\r\n,;]+/);
                }

                jQuery.extend(params, {
                    engine: 'cotendo',
                    'config[username]': jQuery('#cdn_cotendo_username').val(),
                    'config[password]': jQuery('#cdn_cotendo_password').val()
                });

                if (zones.length) {
                    params['config[zones][]'] = zones;
                }

                if (cnames.length) {
                    params['config[domain][]'] = cnames;
                }
                break;
            case 'akamai':
                var emails = [], emails_val = jQuery('#cdn_akamai_email_notification').val();

                if (emails_val) {
                    emails = emails_val.split(/[\r\n,;]+/);
                }

                jQuery.extend(params, {
                    engine: 'akamai',
                    'config[username]': jQuery('#cdn_akamai_username').val(),
                    'config[password]': jQuery('#cdn_akamai_password').val(),
                    'config[zone]': jQuery('#cdn_akamai_zone').val()
                });

                if (emails.length) {
                    params['config[email_notification][]'] = emails;
                }

                if (cnames.length) {
                    params['config[domain][]'] = cnames;
                }
                break;

            case 'edgecast':
                jQuery.extend(params, {
                    engine: 'edgecast',
                    'config[account]': jQuery('#cdn_edgecast_account').val(),
                    'config[token]': jQuery('#cdn_edgecast_token').val()
                });

                if (cnames.length) {
                    params['config[domain][]'] = cnames;
                }
                break;

            case 'att':
                jQuery.extend(params, {
                    engine: 'att',
                    'config[account]': jQuery('#cdn_att_account').val(),
                    'config[token]': jQuery('#cdn_att_token').val()
                });

                if (cnames.length) {
                    params['config[domain][]'] = cnames;
                }
                break;
        }

        var status = jQuery('#cdn_test_status');
        status.removeClass('w3tc-error');
        status.removeClass('w3tc-success');
        status.addClass('w3tc-process');
        status.html('Testing...');

        jQuery.post('admin.php?page=w3tc_dashboard', params, function(data) {
            status.addClass(data.result ? 'w3tc-success' : 'w3tc-error');
            status.html(data.error);
        }, 'json');
    });

    jQuery('#cdn_create_container').live('click', function() {
        var me = jQuery(this);
        var metadata = me.metadata();
        var cnames = w3tc_cdn_get_cnames();
        var container_id = null;
        var params = {
            w3tc_cdn_create_container: 1,
            _wpnonce: metadata.nonce
        };

        switch (metadata.type) {
            case 's3':
                jQuery.extend(params, {
                    engine: 's3',
                    'config[key]': jQuery('#cdn_s3_key').val(),
                    'config[secret]': jQuery('#cdn_s3_secret').val(),
                    'config[bucket]': jQuery('#cdn_s3_bucket').val(),
                    'config[bucket_location]': jQuery('#cdn_s3_bucket_location').val()
                });

                if (cnames.length) {
                    params['config[cname][]'] = cnames;
                }
                break;

            case 'cf':
                container_id = jQuery('#cdn_cf_id');

                jQuery.extend(params, {
                    engine: 'cf',
                    'config[key]': jQuery('#cdn_cf_key').val(),
                    'config[secret]': jQuery('#cdn_cf_secret').val(),
                    'config[bucket]': jQuery('#cdn_cf_bucket').val(),
                    'config[bucket_location]': jQuery('#cdn_cf_bucket_location').val()
                });

                if (cnames.length) {
                    params['config[cname][]'] = cnames;
                }
                break;

            case 'cf2':
                container_id = jQuery('#cdn_cf2_id');

                jQuery.extend(params, {
                    engine: 'cf2',
                    'config[key]': jQuery('#cdn_cf2_key').val(),
                    'config[secret]': jQuery('#cdn_cf2_secret').val(),
                    'config[origin]': jQuery('#cdn_cf2_origin').val(),
                    'config[bucket_location]': jQuery('#cdn_cf2_bucket_location').val()
                });

                if (cnames.length) {
                    params['config[cname][]'] = cnames;
                }
                break;

            case 'rscf':
                container_id = jQuery('#cdn_cnames input[type=text]:first');

                jQuery.extend(params, {
                    engine: 'rscf',
                    'config[user]': jQuery('#cdn_rscf_user').val(),
                    'config[key]': jQuery('#cdn_rscf_key').val(),
                    'config[location]': jQuery('#cdn_rscf_location').val(),
                    'config[container]': jQuery('#cdn_rscf_container').val()
                });

                if (cnames.length) {
                    params['config[cname][]'] = cnames;
                }
                break;

            case 'azure':
                jQuery.extend(params, {
                    engine: 'azure',
                    'config[user]': jQuery('#cdn_azure_user').val(),
                    'config[key]': jQuery('#cdn_azure_key').val(),
                    'config[container]': jQuery('#cdn_azure_container').val()
                });

                if (cnames.length) {
                    params['config[cname][]'] = cnames;
                }
                break;
        }

        var status = jQuery('#cdn_create_container_status');
        status.removeClass('w3tc-error');
        status.removeClass('w3tc-success');
        status.addClass('w3tc-process');
        status.html('Creating...');

        jQuery.post('admin.php?page=w3tc_dashboard', params, function(data) {
            status.addClass(data.result ? 'w3tc-success' : 'w3tc-error');
            status.html(data.error);

            if (container_id && container_id.size() && data.container_id) {
                container_id.val(data.container_id);
            }
        }, 'json');
    });

    jQuery('#cloudflare_purge_cache').click(function() {
        if (confirm('Purging your site\'s cache will remove all cache files. It may take up to 48 hours for the cache to completely rebuild on CloudFlare\'s global network. Are you sure you want to purge the cache?')) {
            var nonce = jQuery(this).metadata().nonce;
            w3tc_cloudflare_api_request('fpurge_ts', 1, nonce);
        }
    });

    jQuery('#memcached_test').click(function() {
        var status = jQuery('#memcached_test_status');
        status.removeClass('w3tc-error');
        status.removeClass('w3tc-success');
        status.addClass('w3tc-process');
        status.html('Testing...');
        jQuery.post('admin.php?page=w3tc_dashboard', {
            w3tc_test_memcached: 1,
            servers: jQuery('#memcached_servers').val(),
            _wpnonce: jQuery(this).metadata().nonce
        }, function(data) {
            status.addClass(data.result ? 'w3tc-success' : 'w3tc-error');
            status.html(data.error);
        }, 'json');
    });

    jQuery('.minifier_test').click(function() {
        var me = jQuery(this);
        var metadata = me.metadata();
        var params = {
            w3tc_test_minifier: 1,
            _wpnonce: metadata.nonce
        };

        switch (metadata.type) {
            case 'yuijs':
                jQuery.extend(params, {
                    engine: 'yuijs',
                    path_java: jQuery('#minify_yuijs_path_java').val(),
                    path_jar: jQuery('#minify_yuijs_path_jar').val()
                });
                break;

            case 'yuicss':
                jQuery.extend(params, {
                    engine: 'yuicss',
                    path_java: jQuery('#minify_yuicss_path_java').val(),
                    path_jar: jQuery('#minify_yuicss_path_jar').val()
                });
                break;

            case 'ccjs':
                jQuery.extend(params, {
                    engine: 'ccjs',
                    path_java: jQuery('#minify_ccjs_path_java').val(),
                    path_jar: jQuery('#minify_ccjs_path_jar').val()
                });
                break;
        }

        var status = me.next();
        status.removeClass('w3tc-error');
        status.removeClass('w3tc-success');
        status.addClass('w3tc-process');
        status.html('Testing...');

        jQuery.post('admin.php?page=w3tc_dashboard', params, function(data) {
            status.addClass(data.result ? 'w3tc-success' : 'w3tc-error');
            status.html(data.error);
        }, 'json');
    });

    // CDN cnames
    jQuery('#cdn_cname_add').click(function() {
        jQuery('#cdn_cnames').append('<li><input type="text" name="cdn_cnames[]" value="" size="60" /> <input class="button cdn_cname_delete" type="button" value="Delete" /> <span></span></li>');
        w3tc_cdn_cnames_assign();
    });

    jQuery('.cdn_cname_delete').live('click', function() {
        var p = jQuery(this).parent();
        if (p.find('input[type=text]').val() == '' || confirm('Are you sure you want to remove this CNAME?')) {
            p.remove();
            w3tc_cdn_cnames_assign();
            w3tc_beforeupload_bind();
        }
    });

    jQuery('#cdn_form').submit(function() {
        var cnames = [], ret = true;

        jQuery('#cdn_cnames input[type=text]').each(function() {
            var cname = jQuery(this).val();

            if (cname) {
                if (jQuery.inArray(cname, cnames) != -1) {
                    alert('CNAME "' + cname + '" already exists.');
                    ret = false;

                    return false;
                } else {
                    cnames.push(cname);
                }
            }
        });

        return ret;
    });

    // New relic page
    w3tc_input_enable('#newrelic_accept_roles input[type=checkbox]', jQuery('#newrelic_accept_logged_roles:checked').size());
    jQuery('#newrelic_accept_logged_roles').live('click', function() {
        w3tc_input_enable('#newrelic_accept_roles input[type=checkbox]', jQuery('#newrelic_accept_logged_roles:checked').size());
    });

    // support tabs
    jQuery('#support_more_files').live('click', function() {
        jQuery(this).before('<input type="file" name="files[]" /><br />');

        return false;
    });

    jQuery('#support_form').live('submit', function() {
        var url = jQuery('.required #support_url');
        var name = jQuery('.required #support_name');
        var email = jQuery('.required #support_email');
        var phone = jQuery('.required #support_phone');
        var subject = jQuery('.required #support_subject');
        var description = jQuery('.required #support_description');
        var wp_login = jQuery('.required #support_wp_login');
        var wp_password = jQuery('.required #support_wp_password');
        var ftp_host = jQuery('.required #support_ftp_host');
        var ftp_login = jQuery('.required #support_ftp_login');
        var ftp_password = jQuery('.required #support_ftp_password');

        if (url.size() && url.val() == '') {
            alert('Please enter the address of your site in the Site URL field.');
            url.focus();
            return false;
        }

        if (name.size() && name.val() == '') {
            alert('Please enter your name in the Name field.');
            name.focus();
            return false;
        }

        if (email.size() && !/^[a-z0-9_\-\.]+@[a-z0-9-\.]+\.[a-z]{2,5}$/.test(email.val().toLowerCase())) {
            alert('Please enter valid email address in the E-Mail field.');
            email.focus();
            return false;
        }

        if (phone.size() && !/^[0-9\-\. \(\)\+]+$/.test(phone.val())) {
            alert('Please enter your phone in the phone field.');
            phone.focus();
            return false;
        }

        if (subject.size() && subject.val() == '') {
            alert('Please enter subject in the subject field.');
            subject.focus();
            return false;
        }

        if (description.size() && description.val() == '') {
            alert('Please describe the issue in the issue description field.');
            description.focus();
            return false;
        }

        if (wp_login.size() && wp_login.val() == '') {
            alert('Please enter an administrator login. Remember you can create a temporary one just for this support case.');
            wp_login.focus();
            return false;
        }

        if (wp_password.size() && wp_password.val() == '') {
            alert('Please enter WP Admin password, be sure it\'s spelled correctly.');
            wp_password.focus();
            return false;
        }

        if (ftp_host.size() && ftp_host.val() == '') {
            alert('Please enter SSH or FTP host for your site.');
            ftp_host.focus();
            return false;
        }

        if (ftp_login.size() && ftp_login.val() == '') {
            alert('Please enter SSH or FTP login for your server. Remember you can create a temporary one just for this support case.');
            ftp_login.focus();
            return false;
        }

        if (ftp_password.size() && ftp_password.val() == '') {
            alert('Please enter SSH or FTP password for your FTP account.');
            ftp_password.focus();
            return false;
        }

        return true;
    });

    jQuery('#support_request_type').live('change', function() {
        var request_type = jQuery(this);

        if (request_type.val() == '') {
            alert('Please select request type.');
            request_type.focus();

            return false;
        }

        var type = request_type.val(), action = '';

        switch (type) {
            case 'bug_report':
            case 'new_feature':
                action = 'support_form';
                break;

            case 'email_support':
            case 'phone_support':
            case 'plugin_config':
            case 'theme_config':
            case 'linux_config':
                action = 'support_payment';
                break;
        }

        if (action) {
            jQuery('#support_container').html('<div id="support_loading">Loading...</div>').load('admin.php?page=w3tc_support&w3tc_' + action + '&request_type=' + type + '&_wpnonce=' + request_type.metadata().nonce);

            return false;
        }

        return true;
    });

    jQuery('#support_cancel').live('click', function() {
        jQuery('#support_container').html('<div id="support_loading">Loading...</div>').load('admin.php?page=w3tc_support&w3tc_support_select&_wpnonce=' + jQuery(this).metadata().nonce);
    });

    // mobile tab
    jQuery('#mobile_form').submit(function() {
        var error = false;

        jQuery('#mobile_groups li').each(function() {
            if (jQuery(this).find(':checked').size()) {
                var group = jQuery(this).find('.mobile_group').text();
                var theme = jQuery(this).find(':selected').val();
                var redirect = jQuery(this).find('input[type=text]').val();
                var agents = jQuery.trim(jQuery(this).find('textarea').val()).split("\n");

                jQuery('#mobile_groups li').each(function() {
                    if (jQuery(this).find(':checked').size()) {
                        var compare_group = jQuery(this).find('.mobile_group').text();
                        if (compare_group != group) {
                            var compare_theme = jQuery(this).find(':selected').val();
                            var compare_redirect = jQuery(this).find('input[type=text]').val();
                            var compare_agents = jQuery.trim(jQuery(this).find('textarea').val()).split("\n");

                            if (compare_redirect == '' && redirect == '' && compare_theme != '' && compare_theme == theme) {
                                alert('Duplicate theme "' + compare_theme + '" found in the group "' + group + '".');
                                error = true;
                                return false;
                            }

                            if (compare_redirect != '' && compare_redirect == redirect) {
                                alert('Duplicate redirect "' + compare_redirect + '" found in the group "' + group + '".');
                                error = true;
                                return false;
                            }

                            jQuery.each(compare_agents, function(index, value) {
                                if (jQuery.inArray(value, agents) != -1) {
                                    alert('Duplicate stem "' + value + '" found in the group "' + compare_group + '".');
                                    error = true;
                                    return false;
                                }
                            });
                        }
                    }
                });

                if (error) {
                    return false;
                }
            }
        });

        if (error) {
            return false;
        }
    });

    jQuery('#mobile_add').click(function() {
        var group = prompt('Enter group name (only "0-9", "a-z", "_" symbols are allowed).');

        if (group !== null) {
            group = group.toLowerCase();
            group = group.replace(/[^0-9a-z_]+/g, '_');
            group = group.replace(/^_+/, '');
            group = group.replace(/_+$/, '');

            if (group) {
                var exists = false;

                jQuery('.mobile_group').each(function() {
                    if (jQuery(this).html() == group) {
                        alert('Group already exists!');
                        exists = true;
                        return false;
                    }
                });

                if (!exists) {
                    var li = jQuery('<li id="mobile_group_' + group + '"><table class="form-table"><tr><th>Group name:</th><td><span class="mobile_group_number">' + (jQuery('#mobile_groups li').size() + 1) + '.</span> <span class="mobile_group">' + group + '</span> <input type="button" class="button mobile_delete" value="Delete group" /></td></tr><tr><th><label for="mobile_groups_' + group + '_enabled">Enabled:</label></th><td><input type="hidden" name="mobile_groups[' + group + '][enabled]" value="0" /><input id="mobile_groups_' + group + '_enabled" type="checkbox" name="mobile_groups[' + group + '][enabled]" value="1" checked="checked" /></td></tr><tr><th><label for="mobile_groups_' + group + '_theme">Theme:</label></th><td><select id="mobile_groups_' + group + '_theme" name="mobile_groups[' + group + '][theme]"><option value="">-- Pass-through --</option></select><br /><span class="description">Assign this group of user agents to a specific them. Leaving this option "Active Theme" allows any plugins you have (e.g. mobile plugins) to properly handle requests for these user agents. If the "redirect users to" field is not empty, this setting is ignored.</span></td></tr><tr><th><label for="mobile_groups_' + group + '_redirect">Redirect users to:</label></th><td><input id="mobile_groups_' + group + '_redirect" type="text" name="mobile_groups[' + group + '][redirect]" value="" size="60" /><br /><span class="description">A 302 redirect is used to send this group of users to another hostname (domain); recommended if a 3rd party service provides a mobile version of your site.</span></td></tr><tr><th><label for="mobile_groups_' + group + '_agents">User agents:</label></th><td><textarea id="mobile_groups_' + group + '_agents" name="mobile_groups[' + group + '][agents]" rows="10" cols="50"></textarea><br /><span class="description">Specify the user agents for this group.</span></td></tr></table></li>');
                    var select = li.find('select');

                    jQuery.each(mobile_themes, function(index, value) {
                        select.append(jQuery('<option />').val(index).html(value));
                    });

                    jQuery('#mobile_groups').append(li);
                    w3tc_mobile_groups_clear();
                    window.location.hash = '#mobile_group_' + group;
                    li.find('textarea').focus();
                }
            } else {
                alert('Empty group name!');
            }
        }
    });

    jQuery('.mobile_delete').live('click', function() {
        if (confirm('Are you sure want to delete this group?')) {
            jQuery(this).parents('#mobile_groups li').remove();
            w3tc_mobile_groups_clear();
            w3tc_beforeupload_bind();
        }
    });

    w3tc_mobile_groups_clear();

    // referrer tab
    jQuery('#referrer_form').submit(function() {
        var error = false;

        jQuery('#referrer_groups li').each(function() {
            if (jQuery(this).find(':checked').size()) {
                var group = jQuery(this).find('.referrer_group').text();
                var theme = jQuery(this).find(':selected').val();
                var redirect = jQuery(this).find('input[type=text]').val();
                var agents = jQuery.trim(jQuery(this).find('textarea').val()).split("\n");

                jQuery('#referrer_groups li').each(function() {
                    if (jQuery(this).find(':checked').size()) {
                        var compare_group = jQuery(this).find('.referrer_group').text();
                        if (compare_group != group) {
                            var compare_theme = jQuery(this).find(':selected').val();
                            var compare_redirect = jQuery(this).find('input[type=text]').val();
                            var compare_agents = jQuery.trim(jQuery(this).find('textarea').val()).split("\n");

                            if (compare_redirect == '' && redirect == '' && compare_theme != '' && compare_theme == theme) {
                                alert('Duplicate theme "' + compare_theme + '" found in the group "' + group + '".');
                                error = true;
                                return false;
                            }

                            if (compare_redirect != '' && compare_redirect == redirect) {
                                alert('Duplicate redirect "' + compare_redirect + '" found in the group "' + group + '".');
                                error = true;
                                return false;
                            }

                            jQuery.each(compare_agents, function(index, value) {
                                if (jQuery.inArray(value, agents) != -1) {
                                    alert('Duplicate stem "' + value + '" found in the group "' + compare_group + '".');
                                    error = true;
                                    return false;
                                }
                            });
                        }
                    }
                });

                if (error) {
                    return false;
                }
            }
        });

        if (error) {
            return false;
        }
    });

    jQuery('#referrer_add').click(function() {
        var group = prompt('Enter group name (only "0-9", "a-z", "_" symbols are allowed).');

        if (group !== null) {
            group = group.toLowerCase();
            group = group.replace(/[^0-9a-z_]+/g, '_');
            group = group.replace(/^_+/, '');
            group = group.replace(/_+$/, '');

            if (group) {
                var exists = false;

                jQuery('.referrer_group').each(function() {
                    if (jQuery(this).html() == group) {
                        alert('Group already exists!');
                        exists = true;
                        return false;
                    }
                });

                if (!exists) {
                    var li = jQuery('<li id="referrer_group_' + group + '"><table class="form-table"><tr><th>Group name:</th><td><span class="referrer_group_number">' + (jQuery('#referrer_groups li').size() + 1) + '.</span> <span class="referrer_group">' + group + '</span> <input type="button" class="button referrer_delete" value="Delete group" /></td></tr><tr><th><label for="referrer_groups_' + group + '_enabled">Enabled:</label></th><td><input type="hidden" name="referrer_groups[' + group + '][enabled]" value="0" /><input id="referrer_groups_' + group + '_enabled" type="checkbox" name="referrer_groups[' + group + '][enabled]" value="1" checked="checked" /></td></tr><tr><th><label for="referrer_groups_' + group + '_theme">Theme:</label></th><td><select id="referrer_groups_' + group + '_theme" name="referrer_groups[' + group + '][theme]"><option value="">-- Pass-through --</option></select><br /><span class="description">Assign this group of referrers to a specific them. Leaving this option "Active Theme" allows any plugins you have (e.g. referrer plugins) to properly handle requests for these referrers. If the "redirect users to" field is not empty, this setting is ignored.</span></td></tr><tr><th><label for="referrer_groups_' + group + '_redirect">Redirect users to:</label></th><td><input id="referrer_groups_' + group + '_redirect" type="text" name="referrer_groups[' + group + '][redirect]" value="" size="60" /><br /><span class="description">A 302 redirect is used to send this group of users to another hostname (domain); recommended if a 3rd party service provides a referrer version of your site.</span></td></tr><tr><th><label for="referrer_groups_' + group + '_referrers">Referrers:</label></th><td><textarea id="referrer_groups_' + group + '_referrers" name="referrer_groups[' + group + '][referrers]" rows="10" cols="50"></textarea><br /><span class="description">Specify the referrers for this group.</span></td></tr></table></li>');
                    var select = li.find('select');

                    jQuery.each(referrer_themes, function(index, value) {
                        select.append(jQuery('<option />').val(index).html(value));
                    });

                    jQuery('#referrer_groups').append(li);
                    w3tc_referrer_groups_clear();
                    window.location.hash = '#referrer_group_' + group;
                    li.find('textarea').focus();
                }
            } else {
                alert('Empty group name!');
            }
        }
    });

    jQuery('.referrer_delete').live('click', function() {
        if (confirm('Are you sure want to delete this group?')) {
            jQuery(this).parents('#referrer_groups li').remove();
            w3tc_referrer_groups_clear();
            w3tc_beforeupload_bind();
        }
    });

    w3tc_referrer_groups_clear();

    // add sortable
    if (jQuery.ui && jQuery.ui.sortable) {
        jQuery('#js_files,#css_files').sortable({
            axis: 'y',
            stop: function() {
                jQuery(this).find('li').each(function(index) {
                    jQuery(this).find('td:eq(0)').html((index + 1) + '.');
                });
            }
        });

        jQuery('#cdn_cnames').sortable({
            axis: 'y',
            stop: w3tc_cdn_cnames_assign
        });

        jQuery('#mobile_groups').sortable({
            axis: 'y',
            stop: function() {
                jQuery('#mobile_groups').find('.mobile_group_number').each(function(index) {
                    jQuery(this).html((index + 1) + '.');
                });
            }
        });

        jQuery('#referrer_groups').sortable({
            axis: 'y',
            stop: function() {
                jQuery('#referrer_groups').find('.referrer_group_number').each(function(index) {
                    jQuery(this).html((index + 1) + '.');
                });
            }
        });
    }

    // show hide rules
    jQuery('.w3tc-show-rules').click(function() {
        var btn = jQuery(this), rules = btn.parent().find('.w3tc-rules');

        if (rules.is(':visible')) {
            rules.css('display', 'none');
            btn.val('view code');
        } else {
            rules.css('display', 'block');
            btn.val('hide code');
        }
    });


    // show hide missing files
    jQuery('.w3tc-show-required-changes').click(function() {
        var btn = jQuery(this), rules = jQuery('.w3tc-required-changes');

        if (rules.is(':visible')) {
            rules.css('display', 'none');
            btn.val('View required changes');
        } else {
            rules.css('display', 'block');
            btn.val('Hide required changes');
        }
    });

    // show hide missing files
    jQuery('.w3tc-show-ftp-form').click(function() {
        var btn = jQuery(this), rules = jQuery('.w3tc-ftp-form');

        if (rules.is(':visible')) {
            rules.css('display', 'none');
            btn.val('Update via FTP');
        } else {
            rules.css('display', 'block');
            btn.val('Cancel FTP Update');
        }
    });

    // show hide missing files
    jQuery('.w3tc-show-technical-info').click(function() {
        var btn = jQuery(this), info = jQuery('.w3tc-technical-info');

        if (info.is(':visible')) {
            info.css('display', 'none');
            btn.val('Technical Information');
        } else {
            info.css('display', 'block');
            btn.val('Hide technical information');
        }
    });

    // add ignore class to the ftp form elements
    jQuery('#ftp_upload_form').find('input').each(function() {
        jQuery(this).addClass('w3tc-ignore-change');
    });

    // check for unsaved changes
    jQuery('#w3tc input,#w3tc select,#w3tc textarea').live('change', function() {
        var ignore = false;
        jQuery(this).parents().andSelf().each(function() {
            if (jQuery(this).hasClass('w3tc-ignore-change') || jQuery(this).hasClass('lightbox')) {
                ignore = true;
                return false;
            }
        });

        if (!ignore) {
            w3tc_beforeupload_bind();
        }
    });

    jQuery('.w3tc-button-save').click(w3tc_beforeupload_unbind);
});
