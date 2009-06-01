
var wpWidgets;
(function($) {

wpWidgets = {
	init : function() {
        var rem;

		if ( $('body').hasClass('widgets_access') ) {
			return;
		}

		$('#widgets-right div.sidebar-name').click(function(){
            var c = $(this).siblings('.widgets-sortables');
			if ( c.is(':visible') ) {
				c.sortable('disable');
				$(this).parent().addClass('closed');
			} else {
				$(this).parent().removeClass('closed');
				c.sortable('enable').sortable('refresh');
			}
        });

        $('#widgets-left div.sidebar-name').click(function(){
			if ( $(this).siblings('.widget-holder').is(':visible') ) {
				$(this).parent().addClass('closed');
			} else {
				$(this).parent().removeClass('closed');
			}
        });

		$('#widgets-right .widget, #wp_inactive_widgets .widget').each(function(){
			wpWidgets.appendTitle(this);
		});

		this.addEvents();
        $('.widget-error').parents('.widget').find('a.widget-action').click();

		$('#available-widgets').droppable({
			tolerance: 'pointer',
			accept: function(o){
				return $(o).parent().attr('id') != 'widget-list';
			},
			drop: function(e,ui) {
				ui.draggable.addClass('deleting');
				$('#removing-widget').hide().children('span').html('');
			},
			over: function(e,ui) {
				ui.draggable.addClass('deleting');
				$('.widget-placeholder').hide();

				if ( ui.draggable.hasClass('ui-sortable-helper') )
					$('#removing-widget').show().children('span').html( ui.draggable.find('.widget-title h4').html() );
			},
			out: function(e,ui) {
				ui.draggable.removeClass('deleting');
				$('.widget-placeholder').show();
				$('#removing-widget').hide().children('span').html('');
			}
		});

		$('#widget-list .widget').draggable({
			connectToSortable: '.widgets-sortables',
			handle: '.widget-title',
			distance: 2,
			helper: 'clone',
			zIndex: 5,
			containment: 'document',
			start: function(e,ui) {
				wpWidgets.fixWebkit(1);
				ui.helper.find('.widget-description').hide();
			},
			stop: function(e,ui) {
				if ( rem )
					$(rem).hide();
				rem = '';
				wpWidgets.fixWebkit();
			}
		});

        $('.widgets-sortables').sortable({
			placeholder: 'widget-placeholder',
			connectWith: '.widgets-sortables',
			items: '.widget',
			handle: '.widget-title',
			cursor: 'move',
			distance: 2,
			containment: 'document',
			start: function(e,ui) {
				wpWidgets.fixWebkit(1);
				ui.item.find('.widget-inside').hide();
				ui.item.css({'marginLeft':'','width':''});
			},
			stop: function(e,ui) {
				if ( ui.item.hasClass('ui-draggable') )
					ui.item.draggable('destroy');

				if ( ui.item.hasClass('deleting') ) {
					wpWidgets.save( ui.item, 1, 0, 1 ); // delete widget
					ui.item.remove();
					return;
				}

				var add = ui.item.find('input.add_new').val(), n = ui.item.find('input.multi_number').val(), id = ui.item.attr('id'), sb = $(this).attr('id');

				ui.item.css({'marginLeft':'','width':''});
				wpWidgets.fixWebkit();
				if ( add ) {
					if ( 'multi' == add ) {
						ui.item.html( ui.item.html().replace(/<[^<>]+>/g, function(m){ return m.replace(/__i__|%i%/g, n); }) );
						ui.item.attr( 'id', id.replace(/__i__|%i%/g, n) );
						n++;
						$('div#' + id).find('input.multi_number').val(n);
					} else if ( 'single' == add ) {
						ui.item.attr( 'id', 'new-' + id );
						rem = 'div#' + id;
					}
					wpWidgets.addEvents(ui.item);
					wpWidgets.save( ui.item, 0, 0, 1 );
					ui.item.find('input.add_new').val('');
					ui.item.find('a.widget-action').click();
					return;
				}
				wpWidgets.saveOrder(sb);
			},
			receive: function(e,ui) {
				if ( !$(this).is(':visible') )
					$(this).sortable('cancel');
			}
		}).not(':visible').sortable('disable');
		wpWidgets.resize();
		wpWidgets.fixLabels();
	},

	saveOrder : function(sb) {
		if ( sb )
			$('#' + sb).parents('.widgets-holder-wrap').find('.ajax-feedback').css('visibility', 'visible');

		var a = {
			action: 'widgets-order',
			savewidgets: $('#_wpnonce_widgets').val(),
			sidebars: []
		};

		$('.widgets-sortables').each( function() {
			a['sidebars[' + $(this).attr('id') + ']'] = $(this).sortable('toArray').join(',');
		});

		$.post( ajaxurl, a, function() {
			$('.ajax-feedback').css('visibility', 'hidden');
		});
		this.resize();
	},

	save : function(widget, del, animate, order) {
		var sb = widget.parents('.widgets-sortables').attr('id'), data = widget.find('form').serialize(), a;
		widget = $(widget);
		widget.find('.ajax-feedback').css('visibility', 'visible');

		a = {
			action: 'save-widget',
			savewidgets: $('#_wpnonce_widgets').val(),
			sidebar: sb
		};

		if ( del )
			a['delete_widget'] = 1;

		data += '&' + $.param(a);

		$.post( ajaxurl, data, function(r){
			var id;

			if ( del ) {
				if ( !$('.widget_number', widget).val() ) {
					id = $('.widget-id', widget).val();
					$('#available-widgets .widget-id').each(function(){
						if ( $(this).val() == id )
							$(this).parents('.widget').show();
					});
				}

				if ( animate ) {
					order = 0;
					widget.slideUp('fast', function(){
						$(this).remove();
						wpWidgets.saveOrder();
					});
				} else {
					widget.remove();
					wpWidgets.resize();
				}
			} else {
				$('.ajax-feedback').css('visibility', 'hidden');
				if ( r && r.length > 2 ) {
					$('.widget-content', widget).html(r);
					wpWidgets.appendTitle(widget);
					wpWidgets.fixLabels(widget);
				}
			}
			if ( order )
				wpWidgets.saveOrder();
		});
	},

	appendTitle : function(widget) {
		$('input[type="text"]', widget).each(function(){
			var title;
			if ( this.id.indexOf('title') != -1 ) {
				title = $(this).val().replace(/<[^<>]+>/g, '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
				if ( title )
					$('.widget-title .in-widget-title', widget).html(': ' + title);
				return false;
			}
		});
	},

	resize : function() {
		$('.widgets-sortables').not('#wp_inactive_widgets').each(function(){
			var h = 50, H = $('.widget', this).length;
			h = h + parseInt(H * 48, 10);
			$(this).css( 'minHeight', h + 'px' );
		});
	},

    fixWebkit : function(n) {
        n = n ? 'none' : '';
        $('body').css({
			WebkitUserSelect: n,
			KhtmlUserSelect: n
		});
    },
    
    fixLabels : function(sc) {
		sc = sc || document;

		$('.widget-inside label', sc).each(function(){
			var f = $(this).attr('for');

			if ( f && f == $('input', this).attr('id') )
				$(this).removeAttr('for');
		});
	},
    
    close : function(widget) {
		widget.find('.widget-inside').slideUp('fast', function(){
			widget.css({'width':'','marginLeft':''});
		});
	},

    addEvents : function(sc) {
		sc = sc || document;
		$('a.widget-action', sc).click(function(){
            var w = parseInt( $(this).parents('.widget').find('.widget-width').val(), 10 ), css = {}, inside = $(this).parents('.widget-top').siblings('.widget-inside');
			if ( inside.is(':hidden') ) {
				if ( w > 250 && inside.parents('.widgets-sortables').length ) {
					css['width'] = w + 30 + 'px';
					if ( inside.parents('.widget-liquid-right').length )
						css['marginLeft'] = 235 - w + 'px';
					inside.parents('.widget').css(css);
				}
				inside.slideDown('fast');
			} else {
				inside.slideUp('fast', function(){ inside.parents('.widget').css({'width':'','marginLeft':''}); });
			}
            return false;
        });
        $('.widget-control-save', sc).click(function(){
			wpWidgets.save( $(this).parents('.widget'), 0, 1, 0 );
			return false;
		});
		$('.widget-control-remove', sc).click(function(){
			wpWidgets.save( $(this).parents('.widget'), 1, 1, 0 );
			return false;
		});
		$('.widget-control-close', sc).click(function(){
			wpWidgets.close( $(this).parents('.widget') );
			return false;
		});
	}
};
$(document).ready(function(){wpWidgets.init();});

})(jQuery);
