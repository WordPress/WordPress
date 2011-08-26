/**
 * PubSub
 *
 * A lightweight publish/subscribe implementation.
 * Private use only!
 */
var PubSub, fullscreen, wptitlehint;

PubSub = function() {
	this.topics = {};
};

PubSub.prototype.subscribe = function( topic, callback ) {
	if ( ! this.topics[ topic ] )
		this.topics[ topic ] = [];

	this.topics[ topic ].push( callback );
	return callback;
};

PubSub.prototype.unsubscribe = function( topic, callback ) {
	var i, l,
		topics = this.topics[ topic ];

	if ( ! topics )
		return callback || [];

	// Clear matching callbacks
	if ( callback ) {
		for ( i = 0, l = topics.length; i < l; i++ ) {
			if ( callback == topics[i] )
				topics.splice( i, 1 );
		}
		return callback;

	// Clear all callbacks
	} else {
		this.topics[ topic ] = [];
		return topics;
	}
};

PubSub.prototype.publish = function( topic, args ) {
	var i, l, broken,
		topics = this.topics[ topic ];

	if ( ! topics )
		return;

	args = args || [];

	for ( i = 0, l = topics.length; i < l; i++ ) {
		broken = ( topics[i].apply( null, args ) === false || broken );
	}
	return ! broken;
};

/**
 * Distraction Free Writing
 * (wp-fullscreen)
 *
 * Access the API globally using the fullscreen variable.
 */

(function($){
	var api, ps, bounder, s;

	// Initialize the fullscreen/api object
	fullscreen = api = {};

	// Create the PubSub (publish/subscribe) interface.
	ps = api.pubsub = new PubSub();
	timer = 0;
	block = false;

	s = api.settings = { // Settings
		visible : false,
		mode : 'tinymce',
		editor_id : 'content',
		title_id : '',
		timer : 0,
		toolbar_shown : false
	}

	/**
	 * Bounder
	 *
	 * Creates a function that publishes start/stop topics.
	 * Used to throttle events.
	 */
	bounder = api.bounder = function( start, stop, delay, e ) {
		var y, top;

		delay = delay || 1250;

		if ( e ) {
			y = e.pageY || e.clientY || e.offsetY;
			top = $(document).scrollTop();

			if ( !e.isDefaultPrevented ) // test if e ic jQuery normalized
				y = 135 + y;

			if ( y - top > 120 )
				return;
		}

		if ( block )
			return;

		block = true;

		setTimeout( function() {
			block = false;
		}, 400 );

		if ( s.timer )
			clearTimeout( s.timer );
		else
			ps.publish( start );

		function timed() {
			ps.publish( stop );
			s.timer = 0;
		}

		s.timer = setTimeout( timed, delay );
	};

	/**
	 * on()
	 *
	 * Turns fullscreen on.
	 *
	 * @param string mode Optional. Switch to the given mode before opening.
	 */
	api.on = function() {
		if ( s.visible )
			return;

		// Settings can be added or changed by defining "wp_fullscreen_settings" JS object.
		// This can be done by defining it as PHP array and passing it to JS with:
		// wp_add_script_data( 'wp-fullscreen', 'wp_fullscreen_settings', $settings_array )
		if ( typeof(wp_fullscreen_settings) != 'undefined' )
			$.extend( s, wp_fullscreen_settings );

		// enable DFW only on the Add/Edit Post screens for now
		/*
		s.editor_id = wpActiveEditor || 'content';

		if ( !s.title_id && $('input#title').length && s.editor_id == 'content' )
			s.title_id = 'title';

		if ( !s.title_id )
			$('#wp-fullscreen-title').hide();
		*/

		s.editor_id = 'content';
		s.title_id = 'title';

		s.mode = $('#' + s.editor_id).is(':hidden') ? 'tinymce' : 'html';
		s.qt_canvas = $('#' + s.editor_id).get(0);

		if ( ! s.element )
			api.ui.init();

		s.is_mce_on = s.has_tinymce && typeof( tinyMCE.get(s.editor_id) ) != 'undefined';

		api.ui.fade( 'show', 'showing', 'shown' );
	};

	/**
	 * off()
	 *
	 * Turns fullscreen off.
	 */
	api.off = function() {
		if ( ! s.visible )
			return;

		api.ui.fade( 'hide', 'hiding', 'hidden' );
	};

	/**
	 * switchmode()
	 *
	 * @return string - The current mode.
	 *
	 * @param string to - The fullscreen mode to switch to.
	 * @event switchMode
	 * @eventparam string to   - The new mode.
	 * @eventparam string from - The old mode.
	 */
	api.switchmode = function( to ) {
		var from = s.mode;

		if ( ! to || ! s.visible || ! s.has_tinymce )
			return from;

		// Don't switch if the mode is the same.
		if ( from == to )
			return from;

		ps.publish( 'switchMode', [ from, to ] );
		s.mode = to;
		ps.publish( 'switchedMode', [ from, to ] );

		return to;
	};

	/**
	 * General
	 */

	api.save = function() {
		var hidden = $('#hiddenaction'), old = hidden.val(), spinner = $('#wp-fullscreen-save img'),
			message = $('#wp-fullscreen-save span');

		spinner.show();
		api.savecontent();

		hidden.val('wp-fullscreen-save-post');

		$.post( ajaxurl, $('form#post').serialize(), function(r){
			spinner.hide();
			message.show();

			setTimeout( function(){
				message.fadeOut(1000);
			}, 3000 );

			if ( r.last_edited )
				$('#wp-fullscreen-save input').attr( 'title',  r.last_edited );

		}, 'json');

		hidden.val(old);
	}

	api.savecontent = function() {
		var ed, content;

		if ( s.title_id )
			$('#' + s.title_id).val( $('#wp-fullscreen-title').val() );

		if ( s.mode === 'tinymce' && (ed = tinyMCE.get('wp_mce_fullscreen')) ) {
			content = ed.save();
		} else {
			content = $('#wp_mce_fullscreen').val();
		}

		$('#' + s.editor_id).val( content );
		$(document).triggerHandler('wpcountwords', [ content ]);
	}

	set_title_hint = function( title ) {
		if ( ! title.val().length )
			title.siblings('label').css( 'visibility', '' );
		else
			title.siblings('label').css( 'visibility', 'hidden' );
	}

	api.dfw_width = function(n) {
		var el = $('#wp-fullscreen-wrap'), w = el.width();

		if ( !n ) { // reset to theme width
			el.width( $('#wp-fullscreen-central-toolbar').width() );
			deleteUserSetting('dfw_width');
			return;
		}

		w = n + w;

		if ( w < 200 || w > 1200 ) // sanity check
			return;

		el.width( w );
		setUserSetting('dfw_width', w);
	}

	ps.subscribe( 'showToolbar', function() {
		s.toolbars.removeClass('fade-1000').addClass('fade-300');
		api.fade.In( s.toolbars, 300, function(){ ps.publish('toolbarShown'); }, true );
		$('#wp-fullscreen-body').addClass('wp-fullscreen-focus');
		s.toolbar_shown = true;
	});

	ps.subscribe( 'hideToolbar', function() {
		s.toolbars.removeClass('fade-300').addClass('fade-1000');
		api.fade.Out( s.toolbars, 1000, function(){ ps.publish('toolbarHidden'); }, true );
		$('#wp-fullscreen-body').removeClass('wp-fullscreen-focus');
	});

	ps.subscribe( 'toolbarShown', function() {
		s.toolbars.removeClass('fade-300');
	});

	ps.subscribe( 'toolbarHidden', function() {
		s.toolbars.removeClass('fade-1000');
		s.toolbar_shown = false;
	});

	ps.subscribe( 'show', function() { // This event occurs before the overlay blocks the UI.
		var title;

		if ( s.title_id ) {
			title = $('#wp-fullscreen-title').val( $('#' + s.title_id).val() );
			set_title_hint( title );
		}

		$('#wp-fullscreen-save input').attr( 'title',  $('#last-edit').text() );

		s.textarea_obj.value = s.qt_canvas.value;

		if ( s.has_tinymce && s.mode === 'tinymce' )
			tinyMCE.execCommand('wpFullScreenInit');

		s.orig_y = $(window).scrollTop();
	});

	ps.subscribe( 'showing', function() { // This event occurs while the DFW overlay blocks the UI.
		$( document.body ).addClass( 'fullscreen-active' );
		api.refresh_buttons();

		$( document ).bind( 'mousemove.fullscreen', function(e) { bounder( 'showToolbar', 'hideToolbar', 2000, e ); } );
		bounder( 'showToolbar', 'hideToolbar', 2000 );

		api.bind_resize();
		setTimeout( api.resize_textarea, 200 );

		// scroll to top so the user is not disoriented
		scrollTo(0, 0);

		// needed it for IE7 and compat mode
		$('#wpadminbar').hide();
	});

	ps.subscribe( 'shown', function() { // This event occurs after the DFW overlay is shown
		var interim_init;

		s.visible = true;

		// init the standard TinyMCE instance if missing
		if ( s.has_tinymce && ! s.is_mce_on ) {

			interim_init = function(mce, ed) {
				var el = ed.getElement(), old_val = el.value, settings = tinyMCEPreInit.mceInit[s.editor_id];

				if ( settings && settings.wpautop && typeof(switchEditors) != 'undefined' )
					el.value = switchEditors.wpautop( el.value );

				ed.onInit.add(function(ed) {
					ed.hide();
					ed.getElement().value = old_val;
					tinymce.onAddEditor.remove(interim_init);
				});
			};

			tinymce.onAddEditor.add(interim_init);
			tinyMCE.init(tinyMCEPreInit.mceInit[s.editor_id]);

			s.is_mce_on = true;
		}
	});

	ps.subscribe( 'hide', function() { // This event occurs before the overlay blocks DFW.
		var htmled_is_hidden = $('#' + s.editor_id).is(':hidden');
		// Make sure the correct editor is displaying.
		if ( s.has_tinymce && s.mode === 'tinymce' && !htmled_is_hidden ) {
			switchEditors.go( $('#'+s.editor_id+'-tmce').get(0) );
		} else if ( s.mode === 'html' && htmled_is_hidden ) {
			switchEditors.go( $('#'+s.editor_id+'-html').get(0) );
		}

		// Save content must be after switchEditors or content will be overwritten. See #17229.
		api.savecontent();

		$( document ).unbind( '.fullscreen' );
		$(s.textarea_obj).unbind('.grow');

		if ( s.has_tinymce && s.mode === 'tinymce' )
			tinyMCE.execCommand('wpFullScreenSave');

		if ( s.title_id )
			set_title_hint( $('#' + s.title_id) );

		s.qt_canvas.value = s.textarea_obj.value;
	});

	ps.subscribe( 'hiding', function() { // This event occurs while the overlay blocks the DFW UI.

		$( document.body ).removeClass( 'fullscreen-active' );
		scrollTo(0, s.orig_y);
		$('#wpadminbar').show();
	});

	ps.subscribe( 'hidden', function() { // This event occurs after DFW is removed.
		s.visible = false;
		$('#wp_mce_fullscreen, #wp-fullscreen-title').removeAttr('style');

		if ( s.has_tinymce && s.is_mce_on )
			tinyMCE.execCommand('wpFullScreenClose');

		s.textarea_obj.value = '';
		api.oldheight = 0;
	});

	ps.subscribe( 'switchMode', function( from, to ) {
		var ed;

		if ( !s.has_tinymce || !s.is_mce_on )
			return;

		ed = tinyMCE.get('wp_mce_fullscreen');

		if ( from === 'html' && to === 'tinymce' ) {

			if ( tinyMCE.get(s.editor_id).getParam('wpautop') && typeof(switchEditors) != 'undefined' )
				s.textarea_obj.value = switchEditors.wpautop( s.textarea_obj.value );

			if ( 'undefined' == typeof(ed) )
				tinyMCE.execCommand('wpFullScreenInit');
			else
				ed.show();

		} else if ( from === 'tinymce' && to === 'html' ) {
			if ( ed )
				ed.hide();
		}
	});

	ps.subscribe( 'switchedMode', function( from, to ) {
		api.refresh_buttons(true);

		if ( to === 'html' )
			setTimeout( api.resize_textarea, 200 );
	});

	/**
	 * Buttons
	 */
	api.b = function() {
		if ( s.has_tinymce && 'tinymce' === s.mode )
			tinyMCE.execCommand('Bold');
	}

	api.i = function() {
		if ( s.has_tinymce && 'tinymce' === s.mode )
			tinyMCE.execCommand('Italic');
	}

	api.ul = function() {
		if ( s.has_tinymce && 'tinymce' === s.mode )
			tinyMCE.execCommand('InsertUnorderedList');
	}

	api.ol = function() {
		if ( s.has_tinymce && 'tinymce' === s.mode )
			tinyMCE.execCommand('InsertOrderedList');
	}

	api.link = function() {
		if ( s.has_tinymce && 'tinymce' === s.mode )
			tinyMCE.execCommand('WP_Link');
		else
			wpLink.open();
	}

	api.unlink = function() {
		if ( s.has_tinymce && 'tinymce' === s.mode )
			tinyMCE.execCommand('unlink');
	}

	api.atd = function() {
		if ( s.has_tinymce && 'tinymce' === s.mode )
			tinyMCE.execCommand('mceWritingImprovementTool');
	}

	api.help = function() {
		if ( s.has_tinymce && 'tinymce' === s.mode )
			tinyMCE.execCommand('WP_Help');
	}

	api.blockquote = function() {
		if ( s.has_tinymce && 'tinymce' === s.mode )
			tinyMCE.execCommand('mceBlockQuote');
	}

	api.refresh_buttons = function( fade ) {
		fade = fade || false;

		if ( s.mode === 'html' ) {
			$('#wp-fullscreen-mode-bar').removeClass('wp-tmce-mode').addClass('wp-html-mode');

			if ( fade )
				$('#wp-fullscreen-button-bar').fadeOut( 150, function(){
					$(this).addClass('wp-html-mode').fadeIn( 150 );
				});
			else
				$('#wp-fullscreen-button-bar').addClass('wp-html-mode');

		} else if ( s.mode === 'tinymce' ) {
			$('#wp-fullscreen-mode-bar').removeClass('wp-html-mode').addClass('wp-tmce-mode');

			if ( fade )
				$('#wp-fullscreen-button-bar').fadeOut( 150, function(){
					$(this).removeClass('wp-html-mode').fadeIn( 150 );
				});
			else
				$('#wp-fullscreen-button-bar').removeClass('wp-html-mode');
		}
	}

	/**
	 * UI Elements
	 *
	 * Used for transitioning between states.
	 */
	api.ui = {
		init: function() {
			var topbar = $('#fullscreen-topbar'), txtarea = $('#wp_mce_fullscreen'), last = 0;

			s.toolbars = topbar.add( $('#wp-fullscreen-status') );
			s.element = $('#fullscreen-fader');
			s.textarea_obj = txtarea[0];
			s.has_tinymce = typeof(tinymce) != 'undefined';

			if ( !s.has_tinymce )
				$('#wp-fullscreen-mode-bar').hide();

			if ( wptitlehint )
				wptitlehint('wp-fullscreen-title');

			$(document).keyup(function(e){
				var c = e.keyCode || e.charCode, a, data;

				if ( !fullscreen.settings.visible )
					return true;

				if ( navigator.platform && navigator.platform.indexOf('Mac') != -1 )
					a = e.ctrlKey; // Ctrl key for Mac
				else
					a = e.altKey; // Alt key for Win & Linux

				if ( 27 == c ) { // Esc
					data = {
						event: e,
						what: 'dfw',
						cb: fullscreen.off,
						condition: function(){
							if ( $('#TB_window').is(':visible') || $('.wp-dialog').is(':visible') )
								return false;
							return true;
						}
					};

					if ( ! jQuery(document).triggerHandler( 'wp_CloseOnEscape', [data] ) )
						fullscreen.off();
				}

				if ( a && (61 == c || 107 == c || 187 == c) ) // +
					api.dfw_width(25);

				if ( a && (45 == c || 109 == c || 189 == c) ) // -
					api.dfw_width(-25);

				if ( a && 48 == c ) // 0
					api.dfw_width(0);

				return false;
			});

			// word count in HTML mode
			if ( typeof(wpWordCount) != 'undefined' ) {

				txtarea.keyup( function(e) {
					var k = e.keyCode || e.charCode;

					if ( k == last )
						return true;

					if ( 13 == k || 8 == last || 46 == last )
						$(document).triggerHandler('wpcountwords', [ txtarea.val() ]);

					last = k;
					return true;
				});
			}

			topbar.mouseenter(function(e){
				s.toolbars.addClass('fullscreen-make-sticky');
				$( document ).unbind( '.fullscreen' );
				clearTimeout( s.timer );
				s.timer = 0;
			}).mouseleave(function(e){
				s.toolbars.removeClass('fullscreen-make-sticky');

				if ( s.visible )
					$( document ).bind( 'mousemove.fullscreen', function(e) { bounder( 'showToolbar', 'hideToolbar', 2000, e ); } );
			});
		},

		fade: function( before, during, after ) {
			if ( ! s.element )
				api.ui.init();

			// If any callback bound to before returns false, bail.
			if ( before && ! ps.publish( before ) )
				return;

			api.fade.In( s.element, 600, function() {
				if ( during )
					ps.publish( during );

				api.fade.Out( s.element, 600, function() {
					if ( after )
						ps.publish( after );
				})
			});
		}
	};

	api.fade = {
		transitionend: 'transitionend webkitTransitionEnd oTransitionEnd',

		// Sensitivity to allow browsers to render the blank element before animating.
		sensitivity: 100,

		In: function( element, speed, callback, stop ) {

			callback = callback || $.noop;
			speed = speed || 400;
			stop = stop || false;

			if ( api.fade.transitions ) {
				if ( element.is(':visible') ) {
					element.addClass( 'fade-trigger' );
					return element;
				}

				element.show();
				element.first().one( this.transitionend, function() {
					callback();
				});
				setTimeout( function() { element.addClass( 'fade-trigger' ); }, this.sensitivity );
			} else {
				if ( stop )
					element.stop();

				element.css( 'opacity', 1 );
				element.first().fadeIn( speed, callback );

				if ( element.length > 1 )
					element.not(':first').fadeIn( speed );
			}

			return element;
		},

		Out: function( element, speed, callback, stop ) {

			callback = callback || $.noop;
			speed = speed || 400;
			stop = stop || false;

			if ( ! element.is(':visible') )
				return element;

			if ( api.fade.transitions ) {
				element.first().one( api.fade.transitionend, function() {
					if ( element.hasClass('fade-trigger') )
						return;

					element.hide();
					callback();
				});
				setTimeout( function() { element.removeClass( 'fade-trigger' ); }, this.sensitivity );
			} else {
				if ( stop )
					element.stop();

				element.first().fadeOut( speed, callback );

				if ( element.length > 1 )
					element.not(':first').fadeOut( speed );
			}

			return element;
		},

		transitions: (function() { // Check if the browser supports CSS 3.0 transitions
			var s = document.documentElement.style;

			return ( typeof ( s.WebkitTransition ) == 'string' ||
				typeof ( s.MozTransition ) == 'string' ||
				typeof ( s.OTransition ) == 'string' ||
				typeof ( s.transition ) == 'string' );
		})()
	};


	/**
	 * Resize API
	 *
	 * Automatically updates textarea height.
	 */

	api.bind_resize = function() {
		$(s.textarea_obj).bind('keypress.grow click.grow paste.grow', function(){
			setTimeout( api.resize_textarea, 200 );
		});
	}

	api.oldheight = 0;
	api.resize_textarea = function() {
		var txt = s.textarea_obj, newheight;

		newheight = txt.scrollHeight > 300 ? txt.scrollHeight : 300;

		if ( newheight != api.oldheight ) {
			txt.style.height = newheight + 'px';
			api.oldheight = newheight;
		}
	};

})(jQuery);
