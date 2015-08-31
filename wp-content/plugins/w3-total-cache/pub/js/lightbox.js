var W3tc_Lightbox = {
    window: jQuery(window),
    container: null,
    options: null,

    create: function() {
        var me = this;

        this.container = jQuery('<div class="' + this.options.id + ' lightbox-loading"><div class="lightbox-close">' + this.options.close + '</div><div class="lightbox-content"></div></div>').css({
            top: 0,
            left: 0,
            width: 0,
            height: 0,
            position: 'fixed',
            'z-index': 9991,
            display: 'none'
        });

        jQuery('#w3tc').append(this.container);
        me.resize();
        this.window.resize(function() {
            me.resize();
        });

        this.window.scroll(function() {
            me.resize();
        });

        this.container.find('.lightbox-close').click(function() {
            me.close();
        });

        jQuery(document).keyup(function(e) {
            if (e.keyCode == 27) { me.close(); }   // esc
        });
    },

    open: function(options) {
        this.options = jQuery.extend({
            id: 'lightbox',
            close: 'Close window',
            width: 0,
            height: 0,
            maxWidth: 0,
            maxHeight: 0,
            minWidth: 0,
            minHeight: 0,
            widthPercent: 0.6,
            heightPercent: 0.8,
            content: null,
            url: null,
            callback: null
        }, options);

        this.create();
        this.resize();

        if (this.options.content) {
            this.content(this.options.content);
        } else if (this.options.url) {
            this.load(this.options.url, this.options.callback);
        }

        W3tc_Overlay.show();
        this.container.show();
    },

    close: function() {
        this.container.remove();
        W3tc_Overlay.hide();
    },

    resize: function() {
        var width = (this.options.width ? this.options.width : this.window.width() * this.options.widthPercent);
        var height = (this.options.height ? this.options.height : this.window.height() * this.options.heightPercent);

        if (this.options.maxWidth && width > this.options.maxWidth) {
            width = this.options.maxWidth;
        } else if (width < this.options.minWidth) {
            width = this.options.minWidth;
        }

        if (this.options.maxHeight && height > this.options.maxHeight) {
            height = this.options.maxHeight;
        } else if (height < this.options.minHeight) {
            height = this.options.minHeight;
        }

        this.container.css({
            top: (this.window.height() / 2 - this.container.outerHeight() / 2)>=0 ? this.window.height() / 2 - this.container.outerHeight() / 2 : 0,
            left: (this.window.width() / 2 - this.container.outerWidth() / 2)>=0 ? this.window.width()  / 2 - this.container.outerWidth()  / 2 : 0
        });

        this.container.css({
            width: width,
            height: height
        });

        jQuery('.lightbox-content', this.container).css({
            width: width,
            height: height
        });
    },

    load: function(url, callback) {
        this.content('');
        this.loading(true);
        var me = this;
        jQuery.get(url, {}, function(content) {
            me.loading(false);
            me.content(content);
            if (callback) {
                callback.call(this, me);
            }
        });
    },

    content: function(content) {
        return this.container.find('.lightbox-content').html(content);
    },

    width: function(width) {
        if (width === undefined) {
            return this.container.width();
        } else {
            this.container.css('width', width);
            return this.resize();
        }
    },

    height: function(height) {
        if (height === undefined) {
            return this.container.height();
        } else {
            this.container.css('height', height);
            return this.resize();
        }
    },

    loading: function(loading) {
        return (loading === undefined ? this.container.hasClass('lightbox-loader') : (loading ? this.container.addClass('lightbox-loader') : this.container.removeClass('lightbox-loader')));
    }
};

var W3tc_Overlay = {
    window: jQuery(window),
    container: null,

    create: function() {
        var me = this;

        this.container = jQuery('<div id="overlay" />').css({
            top: 0,
            left: 0,
            width: 0,
            height: 0,
            position: 'fixed',
            'z-index': 9990,
            display: 'none',
            opacity: 0.6
        });

        jQuery('#w3tc').append(this.container);

        this.window.resize(function() {
            me.resize();
        });

        this.window.scroll(function() {
            me.resize();
        });
    },

    show: function() {
        this.create();
        this.resize();
        this.container.show();
    },

    hide: function() {
        this.container.remove();
    },

    resize: function() {
        this.container.css({
            width: this.window.width(),
            height: this.window.height()
        });
    }
};

function w3tc_lightbox_support_us(nonce) {
    W3tc_Lightbox.open({
        id: 'w3tc-overlay',
        close: '',
        width: 800,
        height: 420,
        url: 'admin.php?page=w3tc_dashboard&w3tc_support_us&_wpnonce=' + nonce
    });
}

var w3tc_minify_recommendations_checked = {};

function w3tc_lightbox_minify_recommendations(nonce) {
    W3tc_Lightbox.open({
        width: 1000,
        url: 'admin.php?page=w3tc_minify&w3tc_test_minify_recommendations&_wpnonce=' + nonce,
        callback: function(lightbox) {
            var theme = jQuery('#recom_theme').val();

            if (jQuery.ui && jQuery.ui.sortable) {
                jQuery("#recom_js_files,#recom_css_files").sortable({
                    axis: 'y',
                    stop: function() {
                        jQuery(this).find('li').each(function(index) {
                            jQuery(this).find('td:eq(1)').html((index + 1) + '.');
                        });
                    }
                });
            }

            if (w3tc_minify_recommendations_checked[theme] !== undefined) {
                jQuery('#recom_js_files :text,#recom_css_files :text').each(function() {
                    var hash = jQuery(this).parents('li').find('[name=recom_js_template]').val() + ':' + jQuery(this).val();

                    if (w3tc_minify_recommendations_checked[theme][hash] !== undefined) {
                        var checkbox = jQuery(this).parents('li').find(':checkbox');

                        if (w3tc_minify_recommendations_checked[theme][hash]) {
                            checkbox.attr('checked', 'checked');
                        } else {
                            checkbox.removeAttr('checked');
                        }
                    }
                });
            }

            jQuery('#recom_theme').change(function() {
                jQuery('#recom_js_files :checkbox,#recom_css_files :checkbox').each(function() {
                    var li = jQuery(this).parents('li');
                    var hash = li.find('[name=recom_js_template]').val() + ':' + li.find(':text').val();

                    if (w3tc_minify_recommendations_checked[theme] === undefined) {
                        w3tc_minify_recommendations_checked[theme] = {};
                    }

                    w3tc_minify_recommendations_checked[theme][hash] = jQuery(this).is(':checked');
                });

                lightbox.load('admin.php?page=w3tc_minify&w3tc_test_minify_recommendations&theme_key=' + jQuery(this).val() + '&_wpnonce=' + nonce, lightbox.options.callback);
            });

            jQuery('#recom_js_check').click(function() {
                if (jQuery('#recom_js_files :checkbox:checked').size()) {
                    jQuery('#recom_js_files :checkbox').removeAttr('checked');
                } else {
                    jQuery('#recom_js_files :checkbox').attr('checked', 'checked');
                }

                return false;
            });

            jQuery('#recom_css_check').click(function() {
                if (jQuery('#recom_css_files :checkbox:checked').size()) {
                    jQuery('#recom_css_files :checkbox').removeAttr('checked');
                } else {
                    jQuery('#recom_css_files :checkbox').attr('checked', 'checked');
                }

                return false;
            });

            jQuery('.recom_apply', lightbox.container).click(function() {
                var theme = jQuery('#recom_theme').val();

                jQuery('#js_files li').each(function() {
                    if (jQuery(this).find(':text').attr('name').indexOf('js_files[' + theme + ']') != -1) {
                        jQuery(this).remove();
                    }
                });

                jQuery('#css_files li').each(function() {
                    if (jQuery(this).find(':text').attr('name').indexOf('css_files[' + theme + ']') != -1) {
                        jQuery(this).remove();
                    }
                });

                jQuery('#recom_js_files li').each(function() {
                    if (jQuery(this).find(':checkbox:checked').size()) {
                        w3tc_minify_js_file_add(theme, jQuery(this).find('[name=recom_js_template]').val(), jQuery(this).find('[name=recom_js_location]').val(), jQuery(this).find('[name=recom_js_file]').val());
                    }
                });

                jQuery('#recom_css_files li').each(function() {
                    if (jQuery(this).find(':checkbox:checked').size()) {
                        w3tc_minify_css_file_add(theme, jQuery(this).find('[name=recom_css_template]').val(), jQuery(this).find('[name=recom_css_file]').val());
                    }
                });

                w3tc_minify_js_theme(theme);
                w3tc_minify_css_theme(theme);

                w3tc_input_enable('.js_enabled', jQuery('#minify_js_enable:checked').size());
                w3tc_input_enable('.css_enabled', jQuery('#minify_css_enable:checked').size());

                lightbox.close();
            });
        }
    });
}

function w3tc_lightbox_use_edge_mode(nonce) {
    W3tc_Lightbox.open({
        id:'w3tc-overlay',
      close: '',
      width: 800,
        height: 210,
        url: 'admin.php?page=w3tc_dashboard&w3tc_test_use_edge_mode&_wpnonce=' + nonce,
        callback: function(lightbox) {
            jQuery('.button-primary', lightbox.container).click(function() {
                lightbox.close();
            });
          jQuery('.button-cancel', lightbox.container).click(function() {
            lightbox.close();
          });
        }
    });
}

function w3tc_lightbox_self_test(nonce) {
    W3tc_Lightbox.open({
        width: 800,
        minHeight: 300,
        url: 'admin.php?page=w3tc_dashboard&w3tc_test_self&_wpnonce=' + nonce,
        callback: function(lightbox) {
                jQuery('.button-primary', lightbox.container).click(function() {
                lightbox.close();
            });
        }
    });
}

function w3tc_lightbox_upgrade(nonce) {
  W3tc_Lightbox.open({
    id: 'w3tc-overlay',
    close: '',
    width: 800,
    height: 350,
    url: 'admin.php?page=w3tc_dashboard&w3tc_licensing_upgrade&_wpnonce=' + nonce,
    callback: function(lightbox) {
      jQuery('.button-primary', lightbox.container).click(function() {
        lightbox.close();
      });
      jQuery('#w3tc-purchase', lightbox.container).click(function() {
        lightbox.close();
        w3tc_lightbox_buy_plugin(nonce);
      });
    }
  });
}

function w3tc_lightbox_buy_plugin(nonce) {
    W3tc_Lightbox.open({
        width: 800,
        minHeight: 350,
        url: 'admin.php?page=w3tc_dashboard&w3tc_licensing_buy_plugin&_wpnonce=' + nonce,
        callback: function(lightbox) {
            var w3tc_license_listener = function(event) {
                if (event.origin !== "http://www.w3-edge.com" && event.origin !== "https://www.w3-edge.com")
                    return;
                if (event.data.substr(0, 7) != 'license')
                    return;

                lightbox.close();
              var key = event.data.substr(8);
              w3tc_lightbox_save_licence_key(key, nonce);
            }

            if (window.addEventListener) {
                addEventListener("message", w3tc_license_listener, false)
            } else if (attachEvent) {
                attachEvent("onmessage", w3tc_license_listener);
            }

            jQuery('.button-primary', lightbox.container).click(function() {
                lightbox.close();
            });
        }
    });
}

function w3tc_lightbox_save_licence_key(key, nonce) {
  jQuery('#plugin_license_key').val(key);
  var params = {
    w3tc_default_save_licence_key: 1,
    license_key: key,
    _wpnonce: nonce
  };

  jQuery.post('admin.php?page=w3tc_dashboard', params, function(data) {
  }, 'json');
}

function w3tc_lightbox_cdn_s3_bucket_location(type, nonce) {
    W3tc_Lightbox.open({
        width: 500,
        height: 130,
        url: 'admin.php?page=w3tc_dashboard&w3tc_cdn_s3_bucket_location&type=' + type + '&_wpnonce=' + nonce,
        callback: function(lightbox) {
            jQuery('.button', lightbox.container).click(function() {
                lightbox.close();
            });
        }
    });
}

function w3tc_lightbox_netdna_maxcdn_pull_zone(type, nonce) {
    W3tc_Lightbox.open({
        width: 500,
        height: 400,
        url: 'admin.php?page=w3tc_dashboard&w3tc_cdn_create_netdna_maxcdn_pull_zone_form&type=' + type + '&_wpnonce=' + nonce,
        callback: function(lightbox) {
            jQuery('#create_pull_zone', lightbox.container).click(function() {
                var loader = jQuery('#pull-zone-loading');
                loader.addClass('w3tc-loading');
                var pull_button = jQuery(this);
                pull_button.attr("disabled", "disabled");
                jQuery('.create-error').text('');
                var name_val = jQuery('#name', lightbox.container).val();
                var name_filter = /^[a-zA-Z\d\-]*$/;
                if (name_val == '') {
                    jQuery('#name', lightbox.container).addClass('w3tc-error');
                    jQuery('.name_message', lightbox.container).text('Cannot be empty.');
                } else if(name_val.length < 3) {
                    jQuery('#name', lightbox.container).addClass('w3tc-error');
                    jQuery('.name_message', lightbox.container).text('Too short.');
                } else if (name_val.length > 32) {
                    jQuery('#name', lightbox.container).addClass('w3tc-error');
                    jQuery('.name_message', lightbox.container).text('Too long.');
                } else if (!name_filter.test(name_val)) {
                    jQuery('#name', lightbox.container).addClass('w3tc-error');
                    jQuery('.name_message', lightbox.container).text('Cannot use unsupported characters.');
                } else {
                    jQuery('#name', lightbox.container).removeClass('w3tc-error');
                    jQuery('.name_message', lightbox.container).text('');
                }

                var label_val = jQuery('#label', lightbox.container).val();
                if (label_val == '') {
                    jQuery('#label', lightbox.container).addClass('w3tc-error');
                    jQuery('.label_message', lightbox.container).text('Cannot be empty.');
                } else if(label_val.length < 1) {
                    jQuery('#label', lightbox.container).addClass('w3tc-error');
                    jQuery('.label_message', lightbox.container).text('Too short.');
                } else if (label_val.length > 255) {
                    jQuery('#label', lightbox.container).addClass('w3tc-error');
                    jQuery('.label_message', lightbox.container).text('Too long.');
                } else {
                    jQuery('#label', lightbox.container).removeClass('w3tc-error');
                    jQuery('.label_message', lightbox.container).text('');
                }
                if (!jQuery('#label').hasClass('w3tc-error') && !jQuery('#name').hasClass('w3tc-error')) {
                    jQuery.post('admin.php?page=w3tc_dashboard&w3tc_cdn_create_netdna_maxcdn_pull_zone', {name:name_val, label: label_val, nonce: jQuery('#_wp_nonce').val(), type: type},function(data) {
                            loader.removeClass('w3tc-loading');
                            if (data['status'] == 'error') {
                                jQuery('.create-error').show();
                                jQuery('.create-error').html('<p>Something is wrong:<br />' + data['message'] + '</p>');
                                pull_button.removeAttr("disabled");
                            } else {
                                if (jQuery('#cdn_cnames > :first-child > :first-child').val() == '') {
                                    jQuery('#cdn_cnames > :first-child > :first-child').val(data['temporary_url']);
                                    jQuery('.netdna-maxcdn-form').html('<p>Pull zone was successfully created. Following url was added as default "Replace site\'s hostname with:" '
                                      + data['temporary_url']
                                      + '</p><p><input class="button-primary" onclick="window.location = \'admin.php?page=w3tc_dashboard&w3tc_cdn_save_activate&_wpnonce=' + nonce + '\'" value="Save, Activate & Close" />'
                                      + '</p>'
                                    );
                                } else {
                                    jQuery('.netdna-maxcdn-form').html('<p>Pull zone was successfully created. cnames were already set so "Replace site\'s hostname with:" were not replaced with '
                                      + data['temporary_url']
                                      + '</p><p><input class="button-primary" onclick="window.location = \'admin.php?page=w3tc_dashboard&w3tc_cdn_save_activate&_wpnonce=' + nonce + '\'" value="Save, Activate & Close" />'
                                      + '</p>'
                                    );
                                }
                            }
                        },
                    'json');
                } else {
                    loader.removeClass('w3tc-loading');
                    pull_button.removeAttr("disabled");
                }
            });
            jQuery('.button', lightbox.container).click(function() {
                lightbox.close();
            });
        }
    });
}

jQuery(function() {
    jQuery('.button-minify-recommendations').click(function() {
        var nonce = jQuery(this).metadata().nonce;
        w3tc_lightbox_minify_recommendations(nonce);
        return false;
    });

    jQuery('.button-self-test').click(function() {
        var nonce = jQuery(this).metadata().nonce;
        w3tc_lightbox_self_test(nonce);
        return false;
    });

    jQuery('.button-buy-plugin').click(function() {
        var nonce = jQuery(this).metadata().nonce;
        w3tc_lightbox_upgrade(nonce);
        jQuery('#w3tc-license-instruction').show();
        return false;
    });

    jQuery('.button-cdn-s3-bucket-location,.button-cdn-cf-bucket-location').click(function() {
        var type = '';
        var nonce = jQuery(this).metadata().nonce;

        if (jQuery(this).hasClass('cdn_s3')) {
            type = 's3';
        } else if (jQuery(this).hasClass('cdn_cf')) {
            type = 'cf';
        }

        w3tc_lightbox_cdn_s3_bucket_location(type, nonce);
        return false;
    });

    jQuery('#netdna-maxcdn-create-pull-zone').click(function() {
        var type = jQuery(this).metadata().type;
        var nonce = jQuery(this).metadata().nonce;
        w3tc_lightbox_netdna_maxcdn_pull_zone(type, nonce);
        return false;
    });
});
