
/**
 *
 * Delay
 *
 * Creates a way to delay events
 * Dependencies: jQuery
 *
 */
 
 /*
Original Plugin Name: OptionTree
Original Plugin URI: http://wp.envato.com
Original Author: Derek Herman
Original Author URI: http://valendesigns.com
*/

(function ($) {
  $.fn.delay = function(time,func){
    return this.each(function(){
      setTimeout(func,time);
    });
  };
})(jQuery);

/**
 *
 * Center AJAX
 *
 * Creates a way to center the AJAX message
 * Dependencies: jQuery
 *
 */
(function ($) {
  $.fn.ajaxMessage = function(html){
    if (html) {
      return $(this).animate({"top":( $(window).height() - $(this).height() ) / 2  - 200 + $(window).scrollTop() + "px"},100).fadeIn('fast').html(html).delay(3000, function(){$('.ajax-message').fadeOut()});
    } else {
      return $(this).animate({"top":( $(window).height() - $(this).height() ) / 2 - 200 + $(window).scrollTop() + "px"},100).fadeIn('fast').delay(3000, function(){$('.ajax-message').fadeOut()});
    }
  };
})(jQuery);

/**
 *
 * Style File
 *
 * Creates a way to cover file input with a better styled version
 * Dependencies: jQuery
 *
 */
(function ($) {
  styleFile = {
    init: function () {
      $('input.file').each(function(){
        var uploadbutton = '<input class="upload_file_button" type="button" value="Upload" />';
        $(this).wrap('<div class="file_wrap" />');
        $(this).addClass('file').css('opacity', 0); //set to invisible
        $(this).parent().append($('<div class="fake_file" />').append($('<input type="text" class="upload" />').attr('id',$(this).attr('id')+'_file')).append(uploadbutton));
       
        $(this).bind('change', function() {
          $('#'+$(this).attr('id')+'_file').val($(this).val());;
        });
        $(this).bind('mouseout', function() {
          $('#'+$(this).attr('id')+'_file').val($(this).val());;
        });
      }); 
    }
  };
  $(document).ready(function () {
    styleFile.init()
  })
})(jQuery);

/**
 *
 * Style Select
 *
 * Replace Select text
 * Dependencies: jQuery
 *
 */
(function ($) {
  styleSelect = {
    init: function () {
      $('.select_wrapper').each(function () {
        $(this).prepend('<span>' + $(this).find('.select option:selected').text() + '</span>');
      });
      $('.select').live('change', function () {
        $(this).prev('span').replaceWith('<span>' + $(this).find('option:selected').text() + '</span>');
      });
      $('.select').bind($.browser.msie ? 'click' : 'change', function(event) {
        $(this).prev('span').replaceWith('<span>' + $(this).find('option:selected').text() + '</span>');
      }); 
    }
  };
  $(document).ready(function () {
    styleSelect.init()
  })
})(jQuery);

/**
 *
 * Activate Tabs
 *
 * Tab style UI toggle
 * Dependencies: jQuery, jQuery UI Core, jQuery UI Tabs
 *
 */
(function ($) {
  activateTabs = {
    init: function () {
      // Activate
      $("#options_tabs").tabs();
      // Append Toggle Button
      $('.top-info').append('<a href="" class="toggle_tabs">Tabs</a>');
      // Toggle Tabs
      $('.toggle_tabs').toggle(function() {
        $("#options_tabs").tabs('destroy');
        $(this).addClass('off');
      }, function() {
        $("#options_tabs").tabs();
        $(this).removeClass('off');
      }); 
    }
  };
  $(document).ready(function () {
    activateTabs.init()
  })
})(jQuery);

/**
 *
 * Upload Option
 *
 * Allows window.send_to_editor to function properly using a private post_id
 * Dependencies: jQuery, Media Upload, Thickbox
 *
 */
(function ($) {
  uploadOption = {
    init: function () {
      var formfield,
          formID,
          btnContent = true;
      // On Click
      $('.upload_button').live("click", function () {
        formfield = $(this).prev('input').attr('name');
        formID = $(this).attr('rel');
        tb_show('', 'media-upload.php?post_id='+formID+'&type=image&amp;TB_iframe=1');
        return false;
      });
            
      window.original_send_to_editor = window.send_to_editor;
      window.send_to_editor = function(html) {
        if (formfield) {
          itemurl = $(html).attr('href');
          var image = /(^.*\.jpg|jpeg|png|gif|ico*)/gi;
          var document = /(^.*\.pdf|doc|docx|ppt|pptx|odt*)/gi;
          var audio = /(^.*\.mp3|m4a|ogg|wav*)/gi;
          var video = /(^.*\.mp4|m4v|mov|wmv|avi|mpg|ogv|3gp|3g2*)/gi;
          if (itemurl.match(image)) {
            btnContent = '<img src="'+itemurl+'" alt="" /><a href="" class="remove">Remove Image</a>';
          } else {
            btnContent = '<div class="no_image">'+html+'<a href="" class="remove">Remove</a></div>';
          }
          $('#' + formfield).val(itemurl);
          $('#' + formfield).next().next('div').slideDown().html(btnContent);
          tb_remove();
        } else {
          window.original_send_to_editor(html);
        }
      }
    }
  };
  $(document).ready(function () {
    uploadOption.init()
  })
})(jQuery);

/**
 *
 * Inline Edit Options
 * 
 * Creates & Updates Options via Ajax
 * Dependencies: jQuery
 *
 */
(function ($) {
  inlineEditOption = {
    init: function () {
      var c = this,
          d = $("tr.inline-edit-option");
      $('.save-options', '#the-theme-options').live("click", function () {
        inlineEditOption.save_options(this);
        return false;
      });
      $("a.edit-inline").live("click", function (event) {
        if ($("a.edit-inline").hasClass('disable')) {
          event.preventDefault();
          return false;
        } else {
          inlineEditOption.edit(this);
          return false;
        }                
      });
      $("a.save").live("click", function () {
        if ($("a.save").hasClass('add-save')) {
          inlineEditOption.addSave(this);
          return false;
        } else {
          inlineEditOption.editSave(this);
          return false;
        }
      });
      $("a.cancel").live("click", function () {
        if ($("a.cancel").hasClass('undo-add')) {
          inlineEditOption.undoAdd();
          return false;
        } else {
          inlineEditOption.revert();
          return false;
        }
      });
      $("a.add-option").live("click", function (event) {
        if ($(this).hasClass('disable')) {
          event.preventDefault();
          return false;
        } else {
          $.post( 
            ajaxurl,  
            { action:'profile_builder_next_id', _ajax_nonce: $("#_ajax_nonce").val() },
            function (response) {
              c = parseInt(response) + 1;
              inlineEditOption.add(c);
            }
          );
          return false;
        }
      });
      $('.delete-inline').live("click", function (event) {
        if ($("a.delete-inline").hasClass('disable')) {
          event.preventDefault();
          return false;
        } else {
          var agree = confirm("Are you sure you want to delete this input?");
          if (agree) {
            inlineEditOption.remove(this);
            return false;
          } else {
            return false;
          }
        }
      });
      // Fade out message div
      if ($('.ajax-message').hasClass('show')) {
        $('.ajax-message').ajaxMessage();
      }
      // Remove Uploaded Image
      $('.remove').live('click', function(event) { 
        $(this).hide();
        $(this).parents().prev().prev('.upload').attr('value', '');
        $(this).parents('.screenshot').slideUp();
      });
    },
    save_options: function (e) {
      var d = {
        action: "profile_builder_array_save"
      };
      b = $(':input', '#the-theme-options').serialize();
      d = b + "&" + $.param(d);
      $.post(ajaxurl, d, function (r) {
        if (r != -1) {
          $('.ajax-message').ajaxMessage('<div class="message"><span>&nbsp;</span>Theme Options were saved</div>');
          $(".option-tree-slider-body").hide();
          $('.option-tree-slider .edit').removeClass('down');
        } else {
          $('.ajax-message').ajaxMessage('<div class="message warning"><span>&nbsp;</span>Theme Options could not be saved</div>');
        }
      });
      return false;
    },
    remove: function (b) {
      var c = true;
      
      // Set ID
      c = $(b).parents("tr:first").attr('id');
      c = c.substr(c.lastIndexOf("-") + 1);
      
      d = {
        action: "profile_builder_delete",
        id: c,
        _ajax_nonce: $("#_ajax_nonce").val()
      };
      $.post(ajaxurl, d, function (r) {
        if (r) {
          if (r == 'removed') {
            $("#option-" + c).remove();
            $('.ajax-message').ajaxMessage('<div class="message"><span>&nbsp;</span>Input deleted.</div>');
          } else {
            $('.ajax-message').ajaxMessage('<div class="message warning"><span>&nbsp;</span>'+r+'</div>');
          }
        } else {
          $('.ajax-message').ajaxMessage('<div class="message warning"><span>&nbsp;</span>'+r+'</div>');
        }
      });
      return false;
    },
    add: function (c) {
      var e = this, 
          addRow, editRow = true, temp_select;
      e.revert();
      
      // Clone the blank main row
      addRow = $('#inline-add').clone(true);
      addRow = $(addRow).attr('id', 'option-'+c);
      
      // Clone the blank edit row
      editRow = $('#inline-edit').clone(true);
      
      $('a.cancel', editRow).addClass('undo-add');
      $('a.save', editRow).addClass('add-save');
      $('a.edit-inline').addClass('disable');
      $('a.delete-inline').addClass('disable');
      $('a.add-option').addClass('disable');
      
      // Set Colspan to 5
      $('td', editRow).attr('colspan', 5);
      
      // Add Row
      $("#framework-settings tr:last").after(addRow);
      
      // Add Row and hide
      $(addRow).hide().after(editRow);
      
      $('.item-data', addRow).attr('id', 'inline_'+c);
      
      // Show The Editor
      $(editRow).attr('id', 'edit-'+c).addClass('inline-editor').show();
      
      $('.item_title', '#edit-'+c).focus();
      
      $('.select').each(function () {
        temp_select = $(this).prev('span').text();
        if (temp_select == 'Heading') {
          $('.option-desc', '#edit-'+c).hide();
          $('.option-options', '#edit-'+c).hide();
        } 
      });
      
      $('.select').live('change', function () {
        temp_select = $(this).prev('span').text();
        if (temp_select == 'Heading') {
          $('.option-desc', '#edit-'+c).hide();
          $('.option-options', '#edit-'+c).hide();
        } else if ( 
            temp_select == 'Checkbox' || 
            temp_select == 'Radio' || 
            temp_select == 'Select'
          ) {
          $('.alternative').hide();
          $('.regular').show();
          $('.option-desc', '#edit-'+c).show();
          $('.option-options', '#edit-'+c).show();
		/* avatar */
        }else if (temp_select == 'Avatar'){
			$('.regular').hide();
            $('.alternative').show().html('<strong>Avatar Size:</strong> Enter a pair of values (between 20 and 200), separated (only) by a comma in the following format: width,height. If you only specify one number, the avatar will be square.');
            $('.option-desc', editRow).show();
            $('.option-options', editRow).show();
		/* end avatar */
        } else {
          if (temp_select == 'Textarea') {
            $('.regular').hide();
            $('.alternative').show().html('<strong>Row Count:</strong> Enter a numeric value for the number of rows in your textarea.');
            $('.option-desc', '#edit-'+c).show();
            $('.option-options', '#edit-'+c).show();
          } else if (
              temp_select == 'Custom Post' ||
              temp_select == 'Custom Posts'
            ) {
            $('.regular').hide();
            $('.alternative').show().html('<strong>Post Type:</strong> Enter your custom post_type.');
            $('.option-desc', '#edit-'+c).show();
            $('.option-options', '#edit-'+c).show();
          } else {
            $('.option-desc', '#edit-'+c).show();
            $('.option-options', '#edit-'+c).hide();
          }
        }
      });
      
      // Scroll
      $('html, body').animate({ scrollTop: 2000 }, 500);

      return false;
    },
    undoAdd: function (b) {
      var e = this,
          c = true;
      e.revert();
      c = $("#framework-settings tr:last").attr('id');
      c = c.substr(c.lastIndexOf("-") + 1);

      $("a.edit-inline").removeClass('disable');
      $("a.delete-inline").removeClass('disable');
      $("a.add-option").removeClass('disable');
      $("#option-" + c).remove();
      
      return false;
    },
    addSave: function (e) {
      var d, b, c, f, g, itemId;
      e = $("tr.inline-editor").attr("id");
      e = e.substr(e.lastIndexOf("-") + 1);
      f = $("#edit-" + e);
      g = $("#inline_" + e);
      itemId = $.trim($("input.item_id", f).val().toLowerCase()).replace(/(\s+)/g,'_');
      if (!itemId) {
        itemId = $.trim($("input.item_title", f).val().toLowerCase()).replace(/(\s+)/g,'_');
      }
      d = {
        action: "profile_builder_add",
        id: e,
        item_id: itemId,
        item_title: $("input.item_title", f).val(),
        item_desc: $("textarea.item_desc", f).val(),
        item_type: $("select.item_type", f).val(),
        item_options: $("input.item_options", f).val()
      };
      b = $("#edit-" + e + " :input").serialize();
      d = b + "&" + $.param(d);
      $.post(ajaxurl, d, function (r) {
        if (r) {
          if (r == 'updated') {
            inlineEditOption.afterSave(e);
            $("#edit-" + e).remove();
            $("#option-" + e).show();
            $('.ajax-message').ajaxMessage('<div class="message"><span>&nbsp;</span>Input added.</div>');
            $('#framework-settings').tableDnD({
              onDragClass: "dragging",
              onDrop: function(table, row) {
                d = {
                  action: "profile_builder_sort",
                  id: $.tableDnD.serialize(),
                  _ajax_nonce: $("#_ajax_nonce").val()
                };
                $.post(ajaxurl, d, function (response) {

                }, "html");
              }
            });
          } else {
            $('.ajax-message').ajaxMessage('<div class="message warning"><span>&nbsp;</span>'+r+'</div>');
          }
        } else {
          $('.ajax-message').ajaxMessage('<div class="message warning"><span>&nbsp;</span>'+r+'</div>');
        }
      });
	  
      return false;
    },
    edit: function (b) {
      var e = this, 
          c, editRow, rowData, item_title, item_id, item_type, item_desc, item_options = true, temp_select;
      e.revert();
    
      c = $(b).parents("tr:first").attr('id');
      c = c.substr(c.lastIndexOf("-") + 1);
      
      // Clone the blank row
      editRow = $('#inline-edit').clone(true);
      $('td', editRow).attr('colspan', 5);
      $("#option-" + c).hide().after(editRow);
      
      // First Option Settings 
      if ("#option-" + c == '#option-1') {
        $('.option').hide();
        $('.option-title').show().css({"paddingBottom":"1px"});
        $('.description', editRow).html('First item must be a heading.');
      }
      
      // Populate the option data
      rowData = $('#inline_' + c);
      
      // Item Title
      item_title = $('.item_title', rowData).text();
      $('.item_title', editRow).attr('value', item_title);
      
      // Item ID
      item_id = $('.item_id', rowData).text();
      $('.item_id', editRow).attr('value', item_id);
      
      // Item Type
  		item_type = $('.item_type', rowData).text();
  		$('select[name=item_type] option[value='+item_type+']', editRow).attr('selected', true);
  		var temp_item_type = $('select[name=item_type] option[value='+item_type+']', editRow).text();
  		$('.select_wrapper span', editRow).text(temp_item_type);
  		
  		// Item Description
      item_desc = $('.item_desc', rowData).text();
      $('.item_desc', editRow).attr('value', item_desc);
	  
	  // Avatar size
      item_avatar = $('.item_avatar', rowData).text();
      $('.item_avatar', editRow).attr('value', item_avatar);
      
      // Item Options
      item_options = $('.item_options', rowData).text();
      $('.item_options', editRow).attr('value', item_options);
      
      
      $('.select', editRow).each(function () {
        temp_select = $(this).prev('span').text();
        if (temp_select == 'Heading') {
          $('.option-desc', editRow).hide();
          $('.option-options', editRow).hide();
        } else if ( 
            temp_select == 'Checkbox' || 
            temp_select == 'Radio' || 
            temp_select == 'Select'
          ) {
          $('.option-desc', editRow).show();
          $('.option-options', editRow).show();
		/*avatar */
		} else if (temp_select == 'Avatar'){
			$('.regular').hide();
            $('.alternative').show().html('<strong>Avatar Size:</strong> Enter a pair of values (between 20 and 200), separated (only) by a comma in the following format: width,height. If you only specify one number, the avatar will be square.');
            $('.option-desc', editRow).show();
            $('.option-options', editRow).show();
		/* end avatar */
        } else {
          if (temp_select == 'Textarea') {
            $('.regular').hide();
            $('.alternative').show().html('<strong>Row Count:</strong> Enter a numeric value for the number of rows in your textarea.');
            $('.option-desc', editRow).show();
            $('.option-options', editRow).show();
          } else if (
              temp_select == 'Custom Post' ||
              temp_select == 'Custom Posts'
            ) {
            $('.regular').hide();
            $('.alternative').show().html('<strong>Post Type:</strong> Enter your custom post_type.');
            $('.option-desc', editRow).show();
            $('.option-options', editRow).show();
          } else {
            $('.option-desc', editRow).show();
            $('.option-options', editRow).hide();
          }
        }
      });
      
      $('.select').live('change', function () {
        temp_select = $(this).prev('span').text();
        if (temp_select == 'Heading') {
          $('.option-desc', editRow).hide();
          $('.option-options', editRow).hide();
        } else if ( 
            temp_select == 'Checkbox' || 
            temp_select == 'Radio' || 
            temp_select == 'Select'
          ) {
          $('.alternative').hide();
          $('.regular').show();
          $('.option-desc', editRow).show();
          $('.option-options', editRow).show();
		/*avatar */
		} else if (temp_select == 'Avatar'){
			$('.regular').hide();
            $('.alternative').show().html('<strong>Avatar Size:</strong> Enter a pair of values (between 20 and 200), separated (only) by a comma in the following format: width,height. If you only specify one number, the avatar will be square.');
            $('.option-desc', editRow).show();
            $('.option-options', editRow).show();
		/* end avatar */
        } else {
          if (temp_select == 'Textarea') {
            $('.regular').hide();
            $('.alternative').show().html('<strong>Row Count:</strong> Enter a numeric value for the number of rows in your textarea.');
            $('.option-desc', editRow).show();
            $('.option-options', editRow).show();
          } else if (
              temp_select == 'Custom Post' ||
              temp_select == 'Custom Posts'
            ) {
            $('.regular').hide();
            $('.alternative').show().html('<strong>Post Type:</strong> Enter your custom post_type.');
            $('.option-desc', editRow).show();
            $('.option-options', editRow).show();
          } else {
            $('.option-desc', editRow).show();
            $('.option-options', editRow).hide();
          }
        }
      });
  		
      // Show The Editor
      $(editRow).attr('id', 'edit-'+c).addClass('inline-editor').show();
      
      // Scroll
      var target = $('#edit-'+c);
      if (c > 1) {
          var top = target.offset().top;
          $('html,body').animate({scrollTop: top}, 500);
          return false;
      }
      
      return false;
    },
    editSave: function (e) {
      var d, b, c, f, g, itemId;
      e = $("tr.inline-editor").attr("id");
      e = e.substr(e.lastIndexOf("-") + 1);
      f = $("#edit-" + e);
      g = $("#inline_" + e);
      itemId = $.trim($("input.item_id", f).val().toLowerCase()).replace(/(\s+)/g,'_');
      if (!itemId) {
        itemId = $.trim($("input.item_title", f).val().toLowerCase()).replace(/(\s+)/g,'_');
      }
      d = {
        action: "profile_builder_edit",
        id: e,
        item_id: itemId,
        item_title: $("input.item_title", f).val(),
        item_desc: $("textarea.item_desc", f).val(),
        item_type: $("select.item_type", f).val(),
        item_options: $("input.item_options", f).val()
      };
      b = $("#edit-" + e + " :input").serialize();
      d = b + "&" + $.param(d);
      $.post(ajaxurl, d, function (r) {
        if (r) {
          if (r == 'updated') {
            inlineEditOption.afterSave(e);
            $("#edit-" + e).remove();
            $("#option-" + e).show();
            $('.ajax-message').ajaxMessage('<div class="message"><span>&nbsp;</span>Option Saved.</div>');
          } else {
            $('.ajax-message').ajaxMessage('<div class="message warning"><span>&nbsp;</span>'+r+'</div>');
          }
        } else {
          $('.ajax-message').ajaxMessage('<div class="message warning"><span>&nbsp;</span>'+r+'</div>');
        }
      });
      return false;
    },
    afterSave: function (e) {
      var x, y, z,
          n, m, o, p, q, r = true;
      x = $("#edit-" + e);
      y = $("#option-" + e);
      z = $("#inline_" + e);
      $('.option').show();
      $('a.cancel', x).removeClass('undo-add');
      $('a.save', x).removeClass('add-save');
      $("a.add-option").removeClass('disable');
      $('a.edit-inline').removeClass('disable');
      $('a.delete-inline').removeClass('disable');
      if (n = $("input.item_title", x).val()) {
        if ($("select.item_type", x).val() != 'heading') {
          $(y).removeClass('col-heading');
          $('.col-title', y).attr('colspan', 1);
          $(".col-key", y).show();
          $(".col-type", y).show();
          $(".col-title", y).text('- ' + n);
        } else {
          $(y).addClass('col-heading');
          $('.col-title', y).attr('colspan', 3);
          $(".col-key", y).hide();
          $(".col-type", y).hide();
          $(".col-title", y).text(n);
        }
        $(".item_title", z).text(n);
      }
      if (m = $.trim($("input.item_id", x).val().toLowerCase()).replace(/(\s+)/g,'_')) {
        $(".col-key", y).text(m);
        $(".item_id", z).text(m);
      } else {
        m = $.trim($("input.item_title", x).val().toLowerCase()).replace(/(\s+)/g,'_');
        $(".col-key", y).text(m);
        $(".item_id", z).text(m);
      }
      if (o = $("select.item_type option:selected", x).val()) {
        $(".col-type", y).text(o);
        $(".item_type", z).text(o);
      }
      if (p = $("textarea.item_desc", x).val()) {
        $(".item_desc", z).text(p);
      }
      if (r = $("input.item_options", x).val()) {
        $(".item_options", z).text(r);
      }
    },
    revert: function () {
      var b, 
          n, m, o, p, q, r = true;
      if (b = $(".inline-editor").attr("id")) {
        $('#'+ b).remove();
        b = b.substr(b.lastIndexOf("-") + 1);
        $('.option').show();
        $("#option-" + b).show();
      }
      return false;
    }
  };
  $(document).ready(function () {
    inlineEditOption.init();
  })
})(jQuery);