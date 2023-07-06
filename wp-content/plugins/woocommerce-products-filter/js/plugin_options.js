"use strict";

(function ($, window) {

    'use strict';

    $.fn.woofTabs = function (options) {

        if (!this.length)
            return;

        return this.each(function () {

            var $this = $(this);

            ({
                init: function () {
                    this.tabsNav = $this.children('nav');
                    this.items = $this.children('.content-wrap').children('section');
                    this._show();
                    this._initEvents();
                },
                _initEvents: function () {
                    var self = this;
                    this.tabsNav.on('click', 'a', function (e) {
                        e.preventDefault();
                        self._show($(this));
                    });
                },
                _show: function (element) {

                    if (element == undefined) {
                        this.firsTab = this.tabsNav.find('li').first();
                        this.firstSection = this.items.first();

                        if (!this.firsTab.hasClass('tab-current')) {
                            this.firsTab.addClass('tab-current');
                        }

                        if (!this.firstSection.hasClass('content-current')) {
                            this.firstSection.addClass('content-current');
                        }
                    }

                    var $this = $(element),
                            $to = $($this.attr('href'));

                    if ($to.length) {
                        $this.parent('li').siblings().removeClass().end().addClass('tab-current');
                        $to.siblings().removeClass().end().addClass('content-current');
                    }

                }

            }).init();

        });
    };

})(jQuery, window);


/*	Popup
 /* --------------------------------------------- */

/**
 * woofPopupPrepare v1.0.0
 */
(function ($) {

    $.woof_popup_prepare = function (el, options) {
        this.el = el;
        this.options = $.extend({}, $.woof_popup_prepare.DEFAULTS, options);
        this.init();
    };

    $.woof_popup_prepare.DEFAULTS = {};
    $.woof_popup_prepare.openInstance = [];

    $.woof_popup_prepare.prototype = {
        init: function () {

            $.woof_popup_prepare.openInstance.unshift(this);

            var base = this;
            base.scope = false;
            base.body = $('body');
            base.wrap = $('#wpwrap');
            base.modal = $('<div class="woof-modal woof-style"></div>');
            base.overlay = $('<div class="woof-modal-backdrop"></div>');
            base.container = $('.woof-tabs');
            base.instance = $.woof_popup_prepare.openInstance.length;
            base.namespace = '.popup_modal_' + base.instance;
            base.eventtype = 'click';
            base.loadPopup();
        },
        loadPopup: function () {
            this.container.on(this.eventtype, this.el, (e) => {
                if (!this.scope) {
                    this.body.addClass('woof-noscroll');
                    this.openPopup(e);
                }
                this.scope = true;
            });
        },
        openPopup: function (e) {
            e.preventDefault();

            //fix for new admin 29-10-2021
            if (e.target.classList.contains('icon-cog-outline')) {
                var el = $(e.target).parent();
            } else {
                var el = $(e.target);
            }

            var base = this,
                    data = el.data();

            if (el.hasClass('js_woof_options')) {
                //for 'by-' items
                var key = data['key'],
                        name = data['name'] + ' [' + data['key'] + ']',
                        type = false,
                        info = $("#woof-modal-content-" + key),
                        content = info.html();
            } else {
                //for taxonomies
                var type = el.parent().find('.woof_select_tax_type').val();
                var key = data['taxonomy'];
                var name = data['taxonomyName'] + ' [' + key + ']';
                var info = $("#woof-modal-content");
		
                info.find('.woof_option_container').hide();
                info.find('.woof_option_all').show();
                info.find('.woof_option_' + type).show();
		

		if(type.indexOf('woof_sd_') == 0) {
		   
		    info.find( ".woof_option_woof_sd_all").show(); 
		}
		
                var content = info.html();
            }

            base.create_html(key, name, content, info, type);
            base.add_behavior(key, name, content, info, type);
        },
        create_html: function (key, name, content, info, type) {

            var base = this,
                    title = name ? '<h3 class="woof-modal-title"> ' + name + '</h3>' : '',
                    loading = ' preloading ',
                    output = '<div class="woof-modal-inner">';
            output += '<div class="woof-modal-inner-header">' + title + '<a href="javascript:void(0)" class="woof-modal-close"></a></div>';
            output += '<div class="woof-modal-inner-content ' + loading + '">' + content + '</div>';
            output += '<div class="woof-modal-inner-footer">';
            output += '<a href="javascript:void(0)" class="woof-modal-save button button-primary button-large">Apply</a>';
            output += '</div>';
            output += '</div>';

            base.wrap.append(base.modal).append(base.overlay);
            base.modal.html(output);
            base.modal.find('.woof-modal-inner-content').removeClass('preloading');

            var multiplier = base.instance - 1,
                    old = parseInt(base.modal.css('zIndex'), 10);
            base.modal.css({margin: (30 * multiplier), zIndex: (old + multiplier + 1)});
            base.overlay.css({zIndex: (old + multiplier)});

            base.on_load_callback(key, name, content, info, type);
        },
        closeModal: function () {
            var base = this;

            $.woof_popup_prepare.openInstance.shift();

            base.modal.remove();
            base.overlay.remove();

            base.body.removeClass('woof-noscroll');
            base.scope = false;
        },
        add_behavior: function (key, name, content, info, type) {
            var base = this;

            base.modal.on(base.eventtype + base.namespace, '.woof-modal-save', function (e) {
                e.preventDefault();
                base.on_close_callback(key, name, content, info, type);
                base.closeModal();
            });
            $(document).keydown(function (e) {
                // ESCAPE key pressed
                if (e.keyCode == 27) {
                    base.closeModal();
                }
            });

            base.modal.on(base.eventtype + base.namespace, '.woof-modal-close', function (e) {
                e.preventDefault();
                base.closeModal();
            });

            base.overlay.on(base.eventtype + base.namespace, function (e) {
                e.preventDefault();
                base.closeModal();
            });

        },
        on_load_callback: function (key, name, content, info, type) {

            if (type) {

                info.find('.woof_option_container').hide();
                info.find('.woof_option_all').show();
                info.find('.woof_option_' + type).show();

                $.each($('.woof_popup_option', this.modal), function () {
                    var option = $(this).data('option'),
                            val = $('input[name="woof_settings[' + option + '][' + key + ']"]').val();
                    $(this).val(val);
                });

            } else {

                $.each($('.woof_popup_option', this.modal), function () {
                    var option = $(this).data('option'),
                            val = $('input[name="woof_settings[' + key + '][' + option + ']"]').val();
                    $(this).val(val);
                });

            }

        },
        on_close_callback: function (key, name, content, info, type) {

            if (type) {

                $.each($('.woof_popup_option', this.modal), function () {
                    var option = $(this).data('option'), val = $(this).val();
                    $('input[name="woof_settings[' + option + '][' + key + ']"]').val(val);
                });

            } else {

                $.each($('.woof_popup_option', this.modal), function () {
                    var option = $(this).data('option'), val = $(this).val();
                    $('input[name="woof_settings[' + key + '][' + option + ']"]').val(val);
                });

            }

        }
    };

})(jQuery);

var woof_sort_order = [];

(function ($) {


    $.woof_mod = $.woof_mod || {};

    $.woof_mod.popup_prepare = function () {
        new $.woof_popup_prepare('.js_woof_options');
        new $.woof_popup_prepare('.js_woof_add_options');
    };

    $(function () {

        $('.woof-tabs').woofTabs();

        $.woof_mod.popup_prepare();

        try {
            $('.woof-color-picker').wpColorPicker();
        } catch (e) {
            console.log(e);
        }

        $("#woof_options").sortable({
            update: function (event, ui) {
                woof_sort_order = [];
                $.each($('#woof_options').children('li'), function (index, value) {
                    var key = $(this).data('key');
                    woof_sort_order.push(key);
                });
                $('input[name="woof_settings[items_order]"]').val(woof_sort_order.toString());
            },
            opacity: 0.8,
            cursor: "crosshair",
            handle: '.woof_drag_and_drope',
            placeholder: 'woof-options-highlight'
        });


        //options saving
        $('#mainform').on('submit', function () {
            $('input[name=save]').hide();
            woof_show_info_popup(woof_lang_saving);
            var data = {
                action: "woof_save_options",
                formdata: $(this).serialize()
            };
            $.post(ajaxurl, data, function () {
                window.location = woof_save_link;
            });

            return false;
        });


        $('.woof_reset_order').on('click', function () {
            if (prompt('To reset order of items write word "reset". The page will be reloaded!') == 'reset') {
                $('input[name="woof_settings[items_order]"]').val('');
                //document.getElementById("mainform").submit();
                $('.woocommerce-save-button').trigger('click');
            }
        });


        $('.js_cache_count_data_clear').on('click', function () {
            $(this).next('span').html('clearing ...');
            var _this = this;
            var data = {
                action: "woof_cache_count_data_clear"
            };
            $.post(ajaxurl, data, function () {
                $(_this).next('span').html('cleared!');
            });

            return false;
        });


        $('.js_cache_terms_clear').on('click', function () {
            $(this).next('span').html('clearing ...');
            var _this = this;
            var data = {
                action: "woof_cache_terms_clear"
            };
            $.post(ajaxurl, data, function () {
                $(_this).next('span').html('cleared!');
            });

            return false;
        });

        $('.js_price_transient_clear').on('click', function () {
            $(this).next('span').html('clearing ...');
            var _this = this;
            var data = {
                action: "woof_price_transient_clear"
            };
            $.post(ajaxurl, data, function () {
                $(_this).next('span').html('cleared!');
            });

            return false;
        });


        //in extension tab
        $('#woof_manipulate_with_ext').change(function () {
            var val = parseInt($(this).val(), 10);
            switch (val) {
                case 1:
                    $('ul.woof_extensions li').hide();
                    $('ul.woof_extensions li.is_enabled').show();
                    break;
                case 2:
                    $('ul.woof_extensions li').hide();
                    $('ul.woof_extensions li.is_disabled').show();
                    break;
                default:
                    $('ul.woof_extensions li').show();
                    break;
            }
        });

        //***

        jQuery('body').on('click', '.woof_select_image', function ()
        {
            var input_object = jQuery(this).prev('input[type=text]');

            var image = wp.media({
                title: 'Media for WOOF',
                multiple: false,
                library: {
                    type: ['image']
                }
            }).open().on('select', function (e) {
                let uploaded_image = image.state().get('selection').first();
                uploaded_image = uploaded_image.toJSON();

                if (typeof uploaded_image.sizes.thumbnail !== 'undefined') {
                    jQuery(input_object).val(uploaded_image.sizes.thumbnail.url);
                } else {
                    jQuery(input_object).val(uploaded_image.url);
                }

                jQuery(input_object).trigger('change');
                return false;

            });


            return false;
        });

        //***

        $('body').on('click', '.woof_ext_remove', function () {

            if (confirm('Sure?')) {
                woof_show_info_popup('Extension removing ...');
                var _this = this;
                var data = {
                    action: "woof_remove_ext",
                    idx: $(this).data('idx'),
                    rm_ext_nonce: $('#rm-ext-nonce').val(),
                };
                $.post(ajaxurl, data, function (e) {
                    woof_show_info_popup('Extension is removed!');
                    $(_this).parents('.woof_ext_li').remove();
                    woof_hide_info_popup();
                });
            }

            return false;
        });

        //***

        $('#toggle_type').change(function () {
            if ($(this).val() == 'text') {
                $('.toggle_type_text').show(200);
                $('.toggle_type_image').hide(200);
            } else {
                $('.toggle_type_image').show(200);
                $('.toggle_type_text').hide(200);
            }
        });
	$('#more_less_type').change(function () {
            if ($(this).val() == 'text') {
                $('.more_less_type_text').show(200);
                $('.more_less_type_image').hide(200);
            } else {
                $('.more_less_type_image').show(200);
                $('.more_less_type_text').hide(200);
            }
        });

        //***
        //to avoid logic errors with the count options
        $('#woof_hide_dynamic_empty_pos').change(function () {
            if ($(this).val() == 1) {
                $('#woof_show_count').val(1);
                $('#woof_show_count_dynamic').val(1);
            }
        });

        $('#woof_show_count_dynamic').change(function () {
            if ($(this).val() == 1) {
                $('#woof_show_count').val(1);
            } else {
                $('#woof_hide_dynamic_empty_pos').val(0);
            }
        });

        $('#woof_show_count').change(function () {
            if ($(this).val() == 0) {
                $('#woof_show_count_dynamic').val(0);
                $('#woof_hide_dynamic_empty_pos').val(0);
            }
        });

        //***

	/*Woof shortcode generator */
	jQuery('.woof_show_shortcode_generator').on('click',function (){  
	    jQuery('#woof-modal-shortcode-generator').toggleClass('woof-modal-shortcode-generator-opened');
	});
        jQuery('.woof_select_taxonomy').on('change',function ()
        {
	    $('.woof_select_term').find('option.woof_select_term_item').remove();
	    let option = $(this).val();
	    jQuery('.woof_select_term_wrapper').css('opacity', '0.2');
	    if (option && option !=-1){
                var data = {
                    action: "woof_get_taxonomy_terms",
                    taxonomy: option
                };		
                $.post(ajaxurl, data, function (terms) {
		    $.each(terms, function( index, value ) {
			$('.woof_select_term').append($('<option>', { 
			    value: value.term_id,
			    text : value.name,
			    class: 'woof_select_term_item'
			}));
			jQuery('.woof_select_term_wrapper').css('opacity', '1');
		    });

                });		
	    }	    
	});
        jQuery('.woof_select_term').on('change',function ()
        {
	    let option = $(this).val();
	    if (option && option !=-1){
		jQuery('.woof_add_taxonomies_shortcode').show();
	    }else{
		jQuery('.woof_add_taxonomies_shortcode').hide();
	    }    
	});
        jQuery('.woof_add_taxonomies_shortcode').on('click',function ()
        {
	
	      var tax = jQuery('.woof_select_taxonomy').val();
	      var terms = jQuery('.woof_select_term').val();
	      if (tax  && terms ) {
		  var text = jQuery('.woof-form-shortcode-generator input[name="taxonomies"]').val();
		  
		  var new_tax = tax +":"+terms.join(',');
		  
		  if (text){
		      text+= "+" + new_tax;    
		  } else {
		      text = new_tax;
		  }
		  
		  jQuery('.woof-form-shortcode-generator input[name="taxonomies"]').val(text);
	      }
	      
	});
	
        jQuery('.generate_woof_shortcode').on('click',function ()
        {
	 var values = jQuery('.woof-form-shortcode-generator').find('.shortcode-generator-value');
	 var sortcode_txt = "";
	 $.each( values, function( index, element ) {
	     console.log(jQuery(element).attr("name"));
	     console.log(jQuery(element).val());
	     var value = jQuery(element).val();
	     var name = jQuery(element).attr("name");
	     if (Array.isArray(value) &&  value.length){
		 sortcode_txt+=  " " + name + "='" + value.join(',') + "'";
	     }else if(value != '') {
		 sortcode_txt+=  " " + name + "='" + value + "'";
	     }
	     
	 });
	    jQuery('.woof-form-shortcode-generate-result').val('');
	    jQuery('.woof-form-shortcode-generate-result').val('[woof ' + sortcode_txt + ' ]');
	    jQuery('.copy_woof_shortcode').show();
	    
	});
	jQuery('.copy_woof_shortcode').on('click', function (){
	    var copyText = jQuery('.woof-form-shortcode-generate-result');

	    copyText.select();
	   //  copyText.setSelectionRange(0, 99999); // For mobile devices

	   navigator.clipboard.writeText(copyText.val());

	   jQuery('.copy_woof_shortcode_info').fadeIn("slow");
	   setTimeout(() => {
		 jQuery('.copy_woof_shortcode_info').fadeOut("slow");
	      }, "1000");	    
	});
        //loader
        $(".woof-admin-preloader").fadeOut("slow");

    });

    $('select[name="woof_settings[show_images_by_attr_show]"]').change(function () {

        if ($(this).val() == 0) {
            $('select[name="woof_settings[show_images_by_attr][]"]').parents('.select-wrap').hide();
        } else {
            $('select[name="woof_settings[show_images_by_attr][]"]').parents('.select-wrap').show();
        }
    });

})(jQuery);


function woof_show_info_popup(text) {
    jQuery("#woof_html_buffer").text(text);
    jQuery("#woof_html_buffer").fadeTo(333, 0.9);
}

function woof_hide_info_popup() {
    window.setTimeout(function () {
        jQuery("#woof_html_buffer").fadeOut(500);
    }, 333);
}


jQuery(document).ready(function () {
    if (woof_ext_custom) {
	//woof_init_ext_uploader(woof_abspath, woof_ext_path, woof_ext_url);
    }
    if (woof_show_notes) {
        jQuery(function () {
            //for premium only
            jQuery('#woof_filter_btn_txt').prop('disabled', true);
            jQuery('#override_no_products').prop('disabled', true);
            jQuery('#woof_filter_btn_txt').val('In the premium version');
            jQuery('#woof_reset_btn_txt').prop('disabled', true);
            jQuery('#woof_reset_btn_txt').val('In the premium version');
            jQuery('#woof_hide_dynamic_empty_pos').prop('disabled', true);
            jQuery('#woof_hide_dynamic_empty_pos_turbo_mode').prop('disabled', true);
            jQuery('select[name="woof_settings[hide_terms_count_txt]"]').prop('disabled', true);
            jQuery('select[name="woof_settings[show_images_by_attr_show]"]').prop('disabled', true);
            //***
            jQuery('#swoof_search_slug').prop('disabled', true);
            jQuery('#swoof_search_slug').val('In the premium version');
            jQuery('#swoof_search_slug').parents('.woof-control-section').addClass('woof_premium_only');
            jQuery('#override_no_products').parents('.woof-control-section').addClass('woof_premium_only');
            jQuery('#hide_terms_count_txt').prop('disabled', true);
            jQuery('#hide_terms_count_txt').parents('.woof-control-section').addClass('woof_premium_only');
        });
    }
});