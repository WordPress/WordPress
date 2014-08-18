/**
 * Option Tree UI
 * 
 * Dependencies: jQuery, jQuery UI, ColorPicker
 *
 * @author Derek Herman (derek@valendesigns.com)
 */
;(function($) {
  OT_UI = {
    processing: false,
    init: function() {
      this.init_hide_body();
      this.init_sortable();
      this.init_add();
      this.init_edit();
      this.init_remove();
      this.init_edit_title()
      this.init_edit_id();
      this.init_activate_layout();
      this.init_conditions();
      this.init_upload();
      this.init_upload_remove();
      this.init_numeric_slider();
      this.init_tabs();
      this.init_radio_image_select();
      this.init_select_wrapper();
      this.fix_upload_parent();
      this.fix_textarea();
      this.replicate_ajax();
      this.reset_settings();
      this.css_editor_mode();      
    },
    init_hide_body: function(elm,type) {
      var css = '.option-tree-setting-body';
      if ( type == 'parent' ) {
        $(css).not( elm.parent().parent().children(css) ).hide();
      } else if ( type == 'child' ) {
        elm.closest('ul').find(css).not( elm.parent().parent().children(css) ).hide();
      } else if ( type == 'child-add' ) {
        elm.children().find(css).hide();
      } else if ( type == 'toggle' ) {
        elm.parent().parent().children(css).toggle();
      } else {
        $(css).hide();
      }
    },
    init_remove_active: function(elm,type) {
      var css = '.option-tree-setting-edit';
      if ( type == 'parent' ) {
        $(css).not(elm).removeClass('active');
      } else if ( type == 'child' ) {
        elm.closest('ul').find(css).not(elm).removeClass('active');
      } else if ( type == 'child-add' ) {
        elm.children().find(css).removeClass('active');
      } else {
        $(css).removeClass('active');
      }
    },
    init_sortable: function() {
      $('.option-tree-sortable').each( function() {
        if ( $(this).children('li').length ) {
          var elm = $(this);
          elm.show();
          elm.sortable({
            items: 'li:not(.ui-state-disabled)',
            handle: 'div.open',
            placeholder: 'ui-state-highlight',
            start: function (event, ui) {
              ui.placeholder.height(ui.item.height()-2);
            },
            stop: function(evt, ui) {
              setTimeout(
                function(){
                  OT_UI.update_ids(elm);
                },
                200
              )
            }
          });
        }
      });
    },
    init_add: function() {
      $(document).on('click', '.option-tree-section-add', function(e) {
        e.preventDefault();
        OT_UI.add(this,'section');
      });
      $(document).on('click', '.option-tree-setting-add', function(e) {
        e.preventDefault();
        OT_UI.add(this,'setting');
      });
      $(document).on('click', '.option-tree-help-add', function(e) {
        e.preventDefault();
        OT_UI.add(this,'the_contextual_help');
      });
      $(document).on('click', '.option-tree-choice-add', function(e) {
        e.preventDefault();
        OT_UI.add(this,'choice');
      });
      $(document).on('click', '.option-tree-list-item-add', function(e) {
        e.preventDefault();
        OT_UI.add(this,'list_item');
      });
      $(document).on('click', '.option-tree-social-links-add', function(e) {
        e.preventDefault();
        OT_UI.add(this,'social_links');
      });
      $(document).on('click', '.option-tree-list-item-setting-add', function(e) {
        e.preventDefault();
        if ( $(this).parents('ul').parents('ul').hasClass('ui-sortable') ) {
          alert(option_tree.setting_limit);
          return false;
        }
        OT_UI.add(this,'list_item_setting');
      });
    },
    init_edit: function() {
      $(document).on('click', '.option-tree-setting-edit', function(e) {
        e.preventDefault();
        if ( $(this).parents().hasClass('option-tree-setting-body') ) {
          OT_UI.init_remove_active($(this),'child');
          OT_UI.init_hide_body($(this),'child');
        } else {
          OT_UI.init_remove_active($(this),'parent');
          OT_UI.init_hide_body($(this), 'parent');
        }
        $(this).toggleClass('active');
        OT_UI.init_hide_body($(this), 'toggle');
      });
    },
    init_remove: function() {
      $(document).on('click', '.option-tree-setting-remove', function(event) {
        event.preventDefault();
        if ( $(this).parents('li').hasClass('ui-state-disabled') ) {
          alert(option_tree.remove_no);
          return false;
        }
        var agree = confirm(option_tree.remove_agree);
        if (agree) {
          var list = $(this).parents('ul');
          OT_UI.remove(this);
          setTimeout( function() { 
            OT_UI.update_ids(list); 
          }, 200 );
        }
        return false;
      });
    },
    init_edit_title: function() {
      $(document).on('keyup', '.option-tree-setting-title', function() {
        OT_UI.edit_title(this);
      });
    },
    init_edit_id: function() {
      $(document).on('keyup', '.section-id', function(){
        OT_UI.update_id(this);
      });
    },
    init_activate_layout: function() {
      $(document).on('click', '.option-tree-layout-activate', function() { 
        var active = $(this).parents('.option-tree-setting').find('.open').text();
        $('.option-tree-layout-activate').removeClass('active');
        $(this).toggleClass('active');
        $('.active-layout-input').attr({'value':active});
      });
      $(document).on('change', '#option-tree-options-layouts-form select', function() {
        var agree = confirm(option_tree.activate_layout_agree);
        if (agree) {
          $('#option-tree-options-layouts-form').submit();
        } else {
          var active = $('#the_current_layout').attr('value');
          $('#option-tree-options-layouts-form select option[value="' + active + '"]').attr({'selected':'selected'});
          $('#option-tree-options-layouts-form select').prev('span').replaceWith('<span>' + active + '</span>');
        }
      });
    },
    add: function(elm,type) {
      var self = this, 
          list = '', 
          list_class = '',
          name = '', 
          post_id = 0, 
          get_option = '', 
          settings = '';
      if ( type == 'the_contextual_help' ) {
        list = $(elm).parent().find('ul:last');
        list_class = 'list-contextual-help';
      } else if ( type == 'choice' ) {
        list = $(elm).parent().children('ul');
        list_class = 'list-choice';
      } else if ( type == 'list_item' ) {
        list = $(elm).parent().children('ul');
        list_class = 'list-sub-setting';
      } else if ( type == 'list_item_setting' ) {
        list = $(elm).parent().children('ul');
        list_class = 'list-sub-setting';
      } else if ( type == 'social_links' ) {
        list = $(elm).parent().children('ul');
        list_class = 'list-sub-setting';
      } else {
        list = $(elm).parent().find('ul:first');
        list_class = ( type == 'section' ) ? 'list-section' : 'list-setting';
      }
      name = list.data('name');
      post_id = list.data('id');
      get_option = list.data('getOption');
      settings = $('#'+name+'_settings_array').val();
      if ( this.processing === false ) {
        this.processing = true;
        var count = parseInt(list.children('li').length);
        if ( type == 'list_item' || type == 'social_links' ) {
          list.find('li input.option-tree-setting-title', self).each(function(){
            var setting = $(this).attr('name'),
                regex = /\[([0-9]+)\]/,
                matches = setting.match(regex),
                id = null != matches ? parseInt(matches[1]) : 0;
            id++;
            if ( id > count) {
              count = id;
            }
          });
        }
        $.ajax({
          url: option_tree.ajax,
          type: 'post',
          data: {
            action: 'add_' + type,
            count: count,
            name: name,
            post_id: post_id,
            get_option: get_option,
            settings: settings,
            type: type
          },
          complete: function( data ) {
            if ( type == 'choice' || type == 'list_item_setting' ) {
              OT_UI.init_remove_active(list,'child-add');
              OT_UI.init_hide_body(list,'child-add');
            } else {
              OT_UI.init_remove_active();
              OT_UI.init_hide_body();
            }
            list.append('<li class="ui-state-default ' + list_class + '">' + data.responseText + '</li>');
            list.children().last().find('.option-tree-setting-edit').toggleClass('active');
            list.children().last().find('.option-tree-setting-body').toggle();
            list.children().last().find('.option-tree-setting-title').focus();
            if ( type != 'the_contextual_help' ) {
              OT_UI.update_ids(list);
            }
            setTimeout( function() {
              OT_UI.init_sortable();
              OT_UI.init_select_wrapper();
              OT_UI.init_numeric_slider();
              OT_UI.parse_condition();
            }, 500);
            self.processing = false;
          }
        });
      }
    },
    remove: function(e) {
      $(e).parent().parent().parent('li').remove();
    },
    edit_title: function(e) {
      if ( this.timer ) {
        clearTimeout(e.timer);
      }
      this.timer = setTimeout( function() {
        $(e).parent().parent().parent().parent().parent().children('.open').text(e.value);
      }, 100);
      return true;
    },
    update_id: function(e) {
      if ( this.timer ) {
        clearTimeout(e.timer);
      }
      this.timer = setTimeout( function() {
        OT_UI.update_ids($(e).parents('ul'));
      }, 100);
      return true;
    },
    update_ids: function(list) {
      var last_section, section, list_items = list.children('li');
      list_items.each(function(index) {
        if ( $(this).hasClass('list-section') ) {
          section = $(this).find('.section-id').val().trim().toLowerCase().replace(/[^a-z0-9]/gi,'_');
          if (!section) {
            section = $(this).find('.section-title').val().trim().toLowerCase().replace(/[^a-z0-9]/gi,'_');
          }
          if (!section) {
            section = last_section;
          }
        }
        if ($(this).hasClass('list-setting') ) {
          $(this).find('.hidden-section').attr({'value':section});
        }
        last_section = section;
      });
    },
    condition_objects: function() {
      return 'select, input[type="radio"]:checked, input[type="text"], input[type="hidden"], input.ot-numeric-slider-hidden-input';
    },
    match_conditions: function(condition) {
      var match;
      var regex = /(.+?):(is|not|contains|less_than|less_than_or_equal_to|greater_than|greater_than_or_equal_to)\((.*?)\),?/g;
      var conditions = [];

      while( match = regex.exec( condition ) ) {
        conditions.push({
          'check': match[1], 
          'rule':  match[2], 
          'value': match[3] || ''
        });
      }

      return conditions;
    },
    parse_condition: function() {
      $( '.format-settings[id^="setting_"][data-condition]' ).each(function() {

        var passed;
        var conditions = OT_UI.match_conditions( $( this ).data( 'condition' ) );
        var operator = ( $( this ).data( 'operator' ) || 'and' ).toLowerCase();

        $.each( conditions, function( index, condition ) {

          var target   = $( '#setting_' + condition.check );
          var targetEl = !! target.length && target.find( OT_UI.condition_objects() ).first();

          if ( ! target.length || ( ! targetEl.length && condition.value.toString() != '' ) ) {
            return;
          }

          var v1 = targetEl.length ? targetEl.val().toString() : '';
          var v2 = condition.value.toString();
          var result;

          switch ( condition.rule ) {
            case 'less_than':
              result = ( parseInt( v1 ) < parseInt( v2 ) );
              break;
            case 'less_than_or_equal_to':
              result = ( parseInt( v1 ) <= parseInt( v2 ) );
              break;
            case 'greater_than':
              result = ( parseInt( v1 ) > parseInt( v2 ) );
              break;
            case 'greater_than_or_equal_to':
              result = ( parseInt( v1 ) >= parseInt( v2 ) );
              break;
            case 'contains':
              result = ( v1.indexOf(v2) !== -1 ? true : false );
              break; 
            case 'is':
              result = ( v1 == v2 );
              break;
            case 'not':
              result = ( v1 != v2 );
              break;
          }

          if ( 'undefined' == typeof passed ) {
            passed = result;
          }

          switch ( operator ) {
            case 'or':
              passed = ( passed || result );
              break;
            case 'and':
            default:
              passed = ( passed && result );
              break;
          }
          
        });

        if ( passed ) {
          $(this).animate({opacity: 'show' , height: 'show'}, 200);
        } else {
          $(this).animate({opacity: 'hide' , height: 'hide'}, 200);
        }
        
        delete passed;

      });
    },
    init_conditions: function() {
      $('.format-settings[id^="setting_"]').on( 'change.conditionals, keyup.conditionals', OT_UI.condition_objects(), function() {
        OT_UI.parse_condition();
      });
      OT_UI.parse_condition();
    },
    init_upload: function() {
      $(document).on('click', '.ot_upload_media', function() {
        var field_id            = $(this).parent('.option-tree-ui-upload-parent').find('input').attr('id'),
            post_id             = $(this).attr('rel'),
            save_attachment_id  = $('#'+field_id).hasClass('ot-upload-attachment-id'),
            btnContent          = '';
        if ( window.wp && wp.media ) {
          window.ot_media_frame = window.ot_media_frame || new wp.media.view.MediaFrame.Select({
            title: $(this).attr('title'),
            button: {
              text: option_tree.upload_text
            }, 
            multiple: false
          });
          window.ot_media_frame.on('select', function() {
            var attachment = window.ot_media_frame.state().get('selection').first(), 
                href = attachment.attributes.url,
                attachment_id = attachment.attributes.id,
                mime = attachment.attributes.mime,
                regex = /^image\/(?:jpe?g|png|gif|x-icon)$/i;
            if ( mime.match(regex) ) {
              btnContent += '<div class="option-tree-ui-image-wrap"><img src="'+href+'" alt="" /></div>';
            }
            btnContent += '<a href="javascript:(void);" class="option-tree-ui-remove-media option-tree-ui-button button button-secondary light" title="'+option_tree.remove_media_text+'"><span class="icon ot-icon-minus-circle"></span>'+option_tree.remove_media_text+'</a>';
            if ( save_attachment_id ) {
              $('#'+field_id).val(attachment_id).attr('disabled','disabled');
              $('#'+field_id).parents('.type-upload').addClass('ot-upload-attachment-id-wrap')
            } else {
              $('#'+field_id).val(href);
            }
            $('#'+field_id+'_media').remove();
            $('#'+field_id).parent().parent('div').append('<div class="option-tree-ui-media-wrap" id="'+field_id+'_media" />');
            $('#'+field_id+'_media').append(btnContent).slideDown();
            window.ot_media_frame.off('select');
          }).open();
        } else {
          var backup = window.send_to_editor,
              intval = window.setInterval( 
                function() {
                  if ( $('#TB_iframeContent').length > 0 && $('#TB_iframeContent').attr('src').indexOf( "&field_id=" ) !== -1 ) {
                    $('#TB_iframeContent').contents().find('#tab-type_url').hide();
                  }
                  $('#TB_iframeContent').contents().find('.savesend .button').val(option_tree.upload_text); 
                }, 50);
          tb_show('', 'media-upload.php?post_id='+post_id+'&field_id='+field_id+'&type=image&TB_iframe=1');
          window.send_to_editor = function(html) {
            var href = $(html).find('img').attr('src');
            if ( typeof href == 'undefined') {
              href = $(html).attr('src');
            } 
            if ( typeof href == 'undefined') {
              href = $(html).attr('href');
            }
            var image = /\.(?:jpe?g|png|gif|ico)$/i;
            if (href.match(image) && OT_UI.url_exists(href)) {
              btnContent += '<div class="option-tree-ui-image-wrap"><img src="'+href+'" alt="" /></div>';
            }
            btnContent += '<a href="javascript:(void);" class="option-tree-ui-remove-media option-tree-ui-button button button-secondary light" title="'+option_tree.remove_media_text+'"><span class="icon ot-icon-minus-circle"></span>'+option_tree.remove_media_text+'</a>';
            $('#'+field_id).val(href);
            $('#'+field_id+'_media').remove();
            $('#'+field_id).parent().parent('div').append('<div class="option-tree-ui-media-wrap" id="'+field_id+'_media" />');
            $('#'+field_id+'_media').append(btnContent).slideDown();
            OT_UI.fix_upload_parent();
            tb_remove();
            window.clearInterval(intval);
            window.send_to_editor = backup;
          };
        }
        return false;
      });
    },
    init_upload_remove: function() {
      $(document).on('click', '.option-tree-ui-remove-media', function(event) {
        event.preventDefault();
        var agree = confirm(option_tree.remove_agree);
        if (agree) {
          OT_UI.remove_image(this);
          return false;
        }
        return false;
      });
    },
    init_upload_fix: function(elm) {
      var id  = $(elm).attr('id'),
          val = $(elm).val(),
          img = $(elm).parent().next('.option-tree-ui-media-wrap').find('img'),
          src = img.attr('src'),
          btnContent = '';
      if ( val == src ) {
        return;
      }
      if ( val != src ) {
        img.attr('src', val);
      }
      if ( val !== '' && ( typeof src == 'undefined' || src == false ) && OT_UI.url_exists(val) ) {
        var image = /\.(?:jpe?g|png|gif|ico)$/i;
        if (val.match(image)) {
          btnContent += '<div class="option-tree-ui-image-wrap"><img src="'+val+'" alt="" /></div>';
        }
        btnContent += '<a href="javascript:(void);" class="option-tree-ui-remove-media option-tree-ui-button button button-secondary light" title="'+option_tree.remove_media_text+'"><span class="icon ot-icon-minus-circle">'+option_tree.remove_media_text+'</span></a>';
        $('#'+id).val(val);
        $('#'+id+'_media').remove();
        $('#'+id).parent().parent('div').append('<div class="option-tree-ui-media-wrap" id="'+id+'_media" />');
        $('#'+id+'_media').append(btnContent).slideDown();
      } else if ( val == '' || ! OT_UI.url_exists(val) ) {
        $(elm).parent().next('.option-tree-ui-media-wrap').remove();
      }
    },
    init_numeric_slider: function() {
      $(".ot-numeric-slider-wrap").each(function() {
        var hidden = $(".ot-numeric-slider-hidden-input", this),
            value  = hidden.val(),
            helper = $(".ot-numeric-slider-helper-input", this);
        if ( ! value ) {
          value = hidden.data("min");
          helper.val(value)
        }
        $(".ot-numeric-slider", this).slider({
          min: hidden.data("min"),
          max: hidden.data("max"),
          step: hidden.data("step"),
          value: value, 
          slide: function(event, ui) {
            hidden.add(helper).val(ui.value);
          },
          change: function() {
            OT_UI.init_conditions();
          }
        });
      });
    },
    init_tabs: function() {
      $(".wrap.settings-wrap .ui-tabs").tabs({ 
        fx: { 
          opacity: "toggle", 
          duration: "fast"
        }
      });
      $(".wrap.settings-wrap .ui-tabs a.ui-tabs-anchor").on("click", function(event, ui) {
        var obj = "input[name='_wp_http_referer']";
        if ( $(obj).length > 0 ) {
          var url = $(obj).val(),
              hash = $(this).attr('href');
          if ( url.indexOf("#") != -1 ) {
            var o = url.split("#")[1],
                n = hash.split("#")[1];
            url = url.replace(o, n);
          } else {
            url = url + hash;
          }
          $(obj).val(url);
        }
      });
    },
    init_radio_image_select: function() {
      $(document).on('click', '.option-tree-ui-radio-image', function() {
        $(this).closest('.type-radio-image').find('.option-tree-ui-radio-image').removeClass('option-tree-ui-radio-image-selected');
        $(this).toggleClass('option-tree-ui-radio-image-selected');
        $(this).parent().find('.option-tree-ui-radio').prop('checked', true).trigger('change');
      });
    },
    init_select_wrapper: function() {
      $('.option-tree-ui-select').each(function () {
        if ( ! $(this).parent().hasClass('select-wrapper') ) {
          $(this).wrap('<div class="select-wrapper" />');
          $(this).parent('.select-wrapper').prepend('<span>' + $(this).find('option:selected').text() + '</span>');
        }
      });
      $(document).on('change', '.option-tree-ui-select', function () {
        $(this).prev('span').replaceWith('<span>' + $(this).find('option:selected').text() + '</span>');
      })
      $(document).on($.browser.msie ? 'click' : 'change', '.option-tree-ui-select', function(event) {
        $(this).prev('span').replaceWith('<span>' + $(this).find('option:selected').text() + '</span>');
      });
    },
    bind_colorpicker: function(field_id) {
      $('#'+field_id).wpColorPicker();
    },
    bind_date_picker: function(field_id, date_format) {
      $('#'+field_id).datepicker({
        showOtherMonths: true,
        showButtonPanel: true,
        currentText: option_tree.date_current,
        closeText: option_tree.date_close,
        dateFormat: date_format
      });
    },
    bind_date_time_picker: function(field_id, date_format) {
      $('#'+field_id).datetimepicker({
        showOtherMonths: true,
        closeText: option_tree.date_close,
        dateFormat: date_format
      });
    },
    fix_upload_parent: function() {
      $('.option-tree-ui-upload-input').not('.ot-upload-attachment-id').on('focus blur', function(){
        $(this).parent('.option-tree-ui-upload-parent').toggleClass('focus');
        OT_UI.init_upload_fix(this);
      });
    },
    remove_image: function(e) {
      $(e).parent().parent().find('.option-tree-ui-upload-input').attr('value','');
      $(e).parent('.option-tree-ui-media-wrap').remove();
    },
    fix_textarea: function() {
      $('.wp-editor-area').focus( function(){
        $(this).parent('div').css({borderColor:'#bbb'});
      }).blur( function(){
        $(this).parent('div').css({borderColor:'#ccc'});
      });
    },
    replicate_ajax: function() {
      if (location.href.indexOf("#") != -1) {
        var url = $("input[name=\'_wp_http_referer\']").val(),
            hash = location.href.substr(location.href.indexOf("#"));
        $("input[name=\'_wp_http_referer\']").val( url + hash );
        this.scroll_to_top();
      }
      setTimeout( function() {
        $(".wrap.settings-wrap .fade").fadeOut("fast");
      }, 3000 );
    },
    reset_settings: function() {
      $(document).on("click", ".reset-settings", function(event){
        var agree = confirm(option_tree.reset_agree);
        if (agree) {
          return true;
        } else {
          return false;
        }
        event.preventDefault();
      });
    },
    css_editor_mode: function() {
      $('.ot-css-editor').each(function() {
        var editor = ace.edit($(this).attr('id'));
        var this_textarea = $('#textarea_' + $(this).attr('id'));
        editor.setTheme("ace/theme/chrome");
        editor.getSession().setMode("ace/mode/css");
        editor.setShowPrintMargin( false );
    
        editor.getSession().setValue(this_textarea.val());
        editor.getSession().on('change', function(){
          this_textarea.val(editor.getSession().getValue());
        });
        this_textarea.on('change', function(){
          editor.getSession().setValue(this_textarea.val());
        });
      });
    },
    url_exists: function(url) {
      var link = document.createElement('a')
      link.href = url
      if ( link.hostname != window.location.hostname ) {
        return true; // Stop the code from checking across domains.
      }
      var http = new XMLHttpRequest();
      http.open('HEAD', url, false);
      http.send();
      return http.status!=404;
    },
    scroll_to_top: function() {
      setTimeout( function() {
        $(this).scrollTop(0);
      }, 50 );
    }
  };
  $(document).ready( function() {
    OT_UI.init();
  });
})(jQuery);

/* Gallery */
!function ($) {
  
  ot_gallery = {
      
    frame: function (elm) {
      
      var selection = this.select(elm)
      
      this._frame = wp.media({
        id:         'ot-gallery-frame'
      , frame:      'post'
      , state:      'gallery-edit'
      , title:      wp.media.view.l10n.editGalleryTitle
      , editing:    true
      , multiple:   true
      , selection:  selection
      })
      
      this._frame.on('update', function () {
        var controller = ot_gallery._frame.states.get('gallery-edit')
          , library = controller.get('library')
          , ids = library.pluck('id')
          , parent = $(elm).parents('.format-setting-inner')
          , input = parent.children('.ot-gallery-value')
          , shortcode = wp.media.gallery.shortcode( selection ).string().replace(/\"/g,"'")
        
        input.attr('value', ids)
                        
        if ( parent.children('.ot-gallery-list').length <= 0 )
          input.after('<ul class="ot-gallery-list" />')
        
        $.ajax({
          type: 'POST',
          url: ajaxurl,
          dataType: 'html',
          data: {
            action: 'gallery_update'
          , ids: ids
          },
          success: function(res) {
            parent.children('.ot-gallery-list').html(res)
            if ( input.hasClass('ot-gallery-shortcode') ) 
              input.val(shortcode)
            if ( $(elm).parent().children('.ot-gallery-delete').length <= 0 ) {
              $(elm).parent().append('<a href="#" class="option-tree-ui-button button button-secondary hug-left ot-gallery-delete">' + option_tree.delete + '</a>')
            }
            $(elm).text(option_tree.edit)
            OT_UI.init_conditions()
          }
        })
      })
        
      return this._frame
      
    }
      
  , select: function (elm) {
      var input = $(elm).parents('.format-setting-inner').children('.ot-gallery-value')
        , ids = input.attr('value')
        , _shortcode = input.hasClass('ot-gallery-shortcode') ? ids : '[gallery ids=\'' + ids + '\]'
        , shortcode = wp.shortcode.next('gallery', ( ids ? _shortcode : wp.media.view.settings.ot_gallery.shortcode ) )
        , defaultPostId = wp.media.gallery.defaults.id
        , attachments
        , selection
        
      // Bail if we didn't match the shortcode or all of the content.
      if ( ! shortcode )
        return
      
      // Ignore the rest of the match object.
      shortcode = shortcode.shortcode
      
      if ( _.isUndefined( shortcode.get('id') ) && ! _.isUndefined( defaultPostId ) )
        shortcode.set( 'id', defaultPostId )
      
      if ( _.isUndefined( shortcode.get('ids') ) && ! input.hasClass('ot-gallery-shortcode') && ids )
        shortcode.set( 'ids', ids )
      
      if ( _.isUndefined( shortcode.get('ids') ) )
        shortcode.set( 'ids', '0' )
      
      attachments = wp.media.gallery.attachments( shortcode )

      selection = new wp.media.model.Selection( attachments.models, {
        props:    attachments.props.toJSON()
      , multiple: true
      })
      
      selection.gallery = attachments.gallery
    
      // Fetch the query's attachments, and then break ties from the query to allow for sorting.
      selection.more().done( function () {
        selection.props.set({ query: false })
        selection.unmirror()
        selection.props.unset('orderby')
      })
      
      return selection
      
    }
    
  , open: function (elm) {
      
      ot_gallery.frame(elm).open()
      
    }
  
  , remove: function (elm) {
      
      if ( confirm( option_tree.confirm ) ) {
        
        $(elm).parents('.format-setting-inner').children('.ot-gallery-value').attr('value', '')
        $(elm).parents('.format-setting-inner').children('.ot-gallery-list').remove()
        $(elm).next('.ot-gallery-edit').text( option_tree.create )
        $(elm).remove()
        OT_UI.init_conditions()
        
      }

    }
  
  }

  // Gallery delete
  $(document).on('click.ot_gallery.data-api', '.ot-gallery-delete', function (e) {
    e.preventDefault()
    ot_gallery.remove($(this))
  })
  
  // Gallery edit
  $(document).on('click.ot_gallery.data-api', '.ot-gallery-edit', function (e) {
    e.preventDefault()
    ot_gallery.open($(this))
  })
  
}(window.jQuery);

/*!
 * Adds metabox tabs
 */
!function ($) {

  $(document).on('ready', function () {
    
    // Loop over the metaboxes
    $('.ot-metabox-wrapper').each( function() {
    
      // Only if there is a tab option
      if ( $(this).find('.type-tab').length ) {
        
        // Add .ot-metabox-panels
        $(this).find('.type-tab').parents('.ot-metabox-wrapper').wrapInner('<div class="ot-metabox-panels" />')
        
        // Wrapp with .ot-metabox-tabs & add .ot-metabox-nav before .ot-metabox-panels
        $(this).find('.ot-metabox-panels').wrap('<div class="ot-metabox-tabs" />').before('<ul class="ot-metabox-nav" />')
        
        // Loop over settings and build the tabs nav
        $(this).find('.format-settings').each( function() {
      
          if ( $(this).find('.type-tab').length > 0 ) {
            var title = $(this).find('.type-tab').prev().find('label').text()
              , id = $(this).attr('id')
  
            // Add a class, hide & append nav item 
            $(this).addClass('is-panel').hide()
            $(this).parents('.ot-metabox-panels').prev('.ot-metabox-nav').append('<li><a href="#' + id + '">' + title + '</a></li>')
            
          }
          
        })
        
        // Loop over the panels and wrap and ID them.
        $(this).find('.is-panel').each( function() {
          var id = $(this).attr('id')
          
          $(this).add( $(this).nextUntil('.is-panel') ).wrapAll('<div id="' + id + '" class="tab-content" />')
          
        })
        
        // Create the tabs
        $(this).find('.ot-metabox-tabs').tabs({
          activate: function( event, ui ) {
            var parent = $(this).outerHeight(),
                child = $(this).find('.ot-metabox-panels').outerHeight() + 8,
                minHeight = parent - 34
            if ( $(this).find('.ot-metabox-panels').css('padding') == '12px' && child < parent ) {
              $(this).find('.ot-metabox-panels').css({ minHeight: minHeight })
            }
            OT_UI.css_editor_mode();
          }
        })
        
        // Move the orphaned settings to the top
        $(this).find('.ot-metabox-panels > .format-settings').prependTo($(this))
        
        // Remove a bunch of classes to stop style conflicts.
        $(this).find('.ot-metabox-tabs').removeClass('ui-widget ui-widget-content ui-corner-all')
        $(this).find('.ot-metabox-nav').removeClass('ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all')
        $(this).find('.ot-metabox-nav li').removeClass('ui-state-default ui-corner-top ui-tabs-active ui-tabs-active')
        $(this).find('.ot-metabox-nav li').on('hover', function() { $(this).removeClass('ui-state-hover') })

      }
    
    })
     
  })
  
}(window.jQuery);

/*!
 * Adds theme option tabs
 */
!function ($) {

  $(document).on('ready', function () {
    
    // Loop over the theme options
    $('#option-tree-settings-api .inside').each( function() {
    
      // Only if there is a tab option
      if ( $(this).find('.type-tab').length ) {
        
        // Add .ot-theme-option-panels
        $(this).find('.type-tab').parents('.inside').wrapInner('<div class="ot-theme-option-panels" />')
        
        // Wrap with .ot-theme-option-tabs & add .ot-theme-option-nav before .ot-theme-option-panels
        $(this).find('.ot-theme-option-panels').wrap('<div class="ot-theme-option-tabs" />').before('<ul class="ot-theme-option-nav" />')
        
        // Loop over settings and build the tabs nav
        $(this).find('.format-settings').each( function() {
      
          if ( $(this).find('.type-tab').length > 0 ) {
            var title = $(this).find('.type-tab').prev().find('.label').text()
              , id = $(this).attr('id')
  
            // Add a class, hide & append nav item 
            $(this).addClass('is-panel').hide()
            $(this).parents('.ot-theme-option-panels').prev('.ot-theme-option-nav').append('<li><a href="#' + id + '">' + title + '</a></li>')
            
          } else {
          
          }
          
        })
        
        // Loop over the panels and wrap and ID them.
        $(this).find('.is-panel').each( function() {
          var id = $(this).attr('id')
          
          $(this).add( $(this).nextUntil('.is-panel') ).wrapAll('<div id="' + id + '" class="tab-content" />')
          
        })
        
        // Create the tabs
        $(this).find('.ot-theme-option-tabs').tabs()
        
        // Move the orphaned settings to the top
        $(this).find('.ot-theme-option-panels > .format-settings').prependTo($(this).find('.ot-theme-option-tabs'))
      
      }
    
    })
     
  })
  
}(window.jQuery);

/*!
 * Fixes the state of metabox radio buttons after a Drag & Drop event.
 */
!function ($) {
  
  $(document).on('ready', function () {

    // detect mousedown and store all checked radio buttons
    $('.hndle').on('mousedown', function () {
      
      // get parent element of .hndle selected. 
      // We only need to monitor radios insde the object that is being moved.
      var parent_id = $(this).closest('div').attr('id')
      
      // set live event listener for mouse up on the content .wrap 
      // then give the dragged div time to settle before firing the reclick function
      $('.wrap').on('mouseup', function () {
        
        var ot_checked_radios = {}
        
        // loop over all checked radio buttons inside of parent element
        $('#' + parent_id + ' input[type="radio"]').each( function () {
          
          // stores checked radio buttons
          if ( $(this).is(':checked') ) {
            
            ot_checked_radios[$(this).attr('name')] = $(this).val()
          
          }
          
          // write to the object
          $(document).data('ot_checked_radios', ot_checked_radios)
          
        })
        
        // restore all checked radio buttons 
        setTimeout( function () {
      
          // get object of checked radio button names and values
          var checked = $(document).data('ot_checked_radios')
          
          // step thru each object element and trigger a click on it's corresponding radio button
          for ( key in checked ) {
            
            $('input[name="' + key + '"]').filter('[value="' + checked[key] + '"]').trigger('click')
            
          }
          
          $('.wrap').unbind('mouseup')
          
        }, 50 )
      
      })
      
    })
  
  })
  
}(window.jQuery);

/*!
 * postformats.js v1.0
 */
!function ($) {

  "use strict"; // jshint ;_;

  /* POSTFORMATS CLASS DEFINITION
   * ====================== */
  var formats = "input.post-format"
    , metaboxes = [
          '#ot-post-format-gallery'
        , '#ot-post-format-link'
        , '#ot-post-format-image'
        , '#ot-post-format-quote'
        , '#ot-post-format-video'
        , '#ot-post-format-audio'
      ]
    , ids = metaboxes.join(',')
    , insertAfter = '#titlediv'
    , imageBox = '#postimagediv'
    , placeholder = 'postimagediv-placeholder'
    , Postformats = function (element, options) {
        this.$element = $(element)
          .on('click.postformats.data-api', $.proxy(this.toggle, this))
        this.$id = this.$element.attr('id')
        this.init()
      }

  Postformats.prototype = {

    constructor: Postformats
  
  , init: function () {

      // Moves the metaboxes into place
      $( '#ot-' + this.$id ).insertAfter( $( insertAfter ) ).hide()
      
      // Show the checked metabox
      if ( this.$element.is(':checked') ) {
      
        this.show()
        
      }
      
    }
    
  , toggle: function () {

      // Hides all the post format metaboxes
      $(ids).each(function() {
      
        $(this).hide()
        
      })
      
      // Shows the clicked post format metabox
      this.show()
      
    }
  
  , show: function () {
      
      // Featured image is never really hidden so it requires different code 
      if ( this.$id == 'post-format-image' ) {
        
        if ( $( '#' + placeholder ).length == 0 )
          $( imageBox ).after( '<div id="' + placeholder + '"></div>' ).insertAfter( insertAfter ).find('h3 span').text(option_tree.with)
        
      // Revert image
      } else {

        $( '#' + placeholder ).replaceWith( $( imageBox ) )
        $( imageBox ).find('h3 span').text(option_tree.replace)
        
      }
      
      // Show the metabox
      $( '#ot-' + this.$id ).show()
      
    }
  
  }
    
  /* POSTFORMATS PLUGIN DEFINITION
   * ======================= */
  var old = $.fn.postformats

  $.fn.postformats = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('postformats')
        , options = typeof option == 'object' && option
      if (!data) $this.data('postformats', (data = new Postformats(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.postformats.Constructor = Postformats
  
  /* POSTFORMATS NO CONFLICT
   * ================= */
  $.fn.postformats.noConflict = function () {
    $.fn.postformats = old
    return this
  }

  /* POSTFORMATS DATA-API
   * ============== */
  $(document).on('ready.postformats.data-api', function () {
    $(formats).each(function () {
      $(this).postformats()
    })
  })

}(window.jQuery);