
var wpWidgets;
(function($) {

wpWidgets = {
	init : function() {
        var rem;

		$('#widgets-right div.sidebar-name').click(function(){
            var c = $(this).siblings('.widgets-sortables');
			if ( c.is(':visible') ) {
				$(this).parent().addClass('closed');
			} else {
				$(this).parent().removeClass('closed');
				c.sortable('refresh');
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
			opacity: 0.65,
			containment: 'document',
			start: function(e,ui) {
				wpWidgets.fixWebkit(1);
				ui.item.find('.widget-inside').hide();
				ui.item.css({'marginLeft':'','width':''});
			},
			stop: function(e,ui) {
				var add = ui.item.find('input.add_new').val(), n = ui.item.find('input.multi_number').val(), id = ui.item.attr('id'), sb = $(this).attr('id');
				ui.item.css({'marginLeft':'','width':''});
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
					wpWidgets.save( ui.item.find('form').serialize(), sb, 0, 0 );
					ui.item.find('input.add_new').val('');
					ui.item.find('a.widget-action').click();
				}
				wpWidgets.saveOrder(sb);
				wpWidgets.resize();
				wpWidgets.fixWebkit();
			},
			receive: function(e,ui) {
				if ( !$(this).is(':visible') )
					$(this).sortable('cancel');
			}
		});
		wpWidgets.resize();
	},

	saveOrder : function(sb) {
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
	},

	save : function(data, sb, del, t) {
		$('#' + sb).parents('.widgets-holder-wrap').find('.ajax-feedback').css('visibility', 'visible');

		var a = {
			action: 'save-widget',
			savewidgets: $('#_wpnonce_widgets').val(),
			sidebar: sb
		};

		if ( del )
			a['delete_widget'] = 1;

		data += '&' + $.param(a);

		$.post( ajaxurl, data, function(r){
			var id, widget;
			$('.ajax-feedback').css('visibility', 'hidden');
			if ( !t )
				return;

			widget = $(t).parents('.widget');

			if ( del ) {
				widget.slideUp('normal', function(){
					$(this).remove();
					wpWidgets.resize();
				});
				if ( !$('.widget_number', widget).val() ) {
					id = $('.widget-id', widget).val();
					$('#available-widgets .widget-id').each(function(){
						if ( $(this).val() == id )
							$(this).parents('.widget').show();
					});
				}
			} else {
				$(t).parents('.widget-inside').slideUp('normal', function(){
					widget.css({'width':'','marginLeft':''});
					wpWidgets.appendTitle(widget);
				});
			}
		});
	},

	appendTitle : function(widget) {
		$('input[type="text"]', widget).each(function(){
			if ( this.id.indexOf('title') != -1 && $(this).val() ) {
				$('.widget-title .in-widget-title', widget).html(': ' + $(this).val());
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
				inside.slideDown('normal');
			} else {
				inside.slideUp('normal', function(){ inside.parents('.widget').css({'width':'','marginLeft':''}); });
			}
            return false;
        });
        $('.widget-control-save', sc).click(function(){
			wpWidgets.save( $(this).parents('form').serialize(), $(this).parents('.widgets-sortables').attr('id'), 0, this );
			return false;
		});
		$('.widget-control-remove', sc).click(function(){
			wpWidgets.save( $(this).parents('form').serialize(), $(this).parents('.widgets-sortables').attr('id'), 1, this );
			return false;
		});
	}

};
$(document).ready(function(){wpWidgets.init();});

})(jQuery);
