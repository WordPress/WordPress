/**
 * @output wp-admin/js/dashboard.js
 */

/* global pagenow, ajaxurl, postboxes, wpActiveEditor:true, ajaxWidgets */
/* global ajaxPopulateWidgets, quickPressLoad,  */
window.wp = window.wp || {};

/**
 * Initializes the dashboard widget functionality.
 *
 * @since 2.7.0
 */
jQuery(document).ready( function($) {
	var welcomePanel = $( '#welcome-panel' ),
		welcomePanelHide = $('#wp_welcome_panel-hide'),
		updateWelcomePanel;

	/**
	 * Saves the visibility of the welcome panel.
	 *
	 * @since 3.3.0
	 *
	 * @param {boolean} visible Should it be visible or not.
	 *
	 * @returns {void}
	 */
	updateWelcomePanel = function( visible ) {
		$.post( ajaxurl, {
			action: 'update-welcome-panel',
			visible: visible,
			welcomepanelnonce: $( '#welcomepanelnonce' ).val()
		});
	};

	// Unhide the welcome panel if the Welcome Option checkbox is checked.
	if ( welcomePanel.hasClass('hidden') && welcomePanelHide.prop('checked') ) {
		welcomePanel.removeClass('hidden');
	}

	// Hide the welcome panel when the dismiss button or close button is clicked.
	$('.welcome-panel-close, .welcome-panel-dismiss a', welcomePanel).click( function(e) {
		e.preventDefault();
		welcomePanel.addClass('hidden');
		updateWelcomePanel( 0 );
		$('#wp_welcome_panel-hide').prop('checked', false);
	});

	// Set welcome panel visibility based on Welcome Option checkbox value.
	welcomePanelHide.click( function() {
		welcomePanel.toggleClass('hidden', ! this.checked );
		updateWelcomePanel( this.checked ? 1 : 0 );
	});

	/**
	 * These widgets can be populated via ajax.
	 *
	 * @since 2.7.0
	 *
	 * @type {string[]}
	 *
	 * @global
 	 */
	window.ajaxWidgets = ['dashboard_primary'];

	/**
	 * Triggers widget updates via AJAX.
	 *
	 * @since 2.7.0
	 *
	 * @global
	 *
	 * @param {string} el Optional. Widget to fetch or none to update all.
	 *
	 * @returns {void}
	 */
	window.ajaxPopulateWidgets = function(el) {
		/**
		 * Fetch the latest representation of the widget via Ajax and show it.
		 *
		 * @param {number} i Number of half-seconds to use as the timeout.
		 * @param {string} id ID of the element which is going to be checked for changes.
		 *
		 * @returns {void}
		 */
		function show(i, id) {
			var p, e = $('#' + id + ' div.inside:visible').find('.widget-loading');
			// If the element is found in the dom, queue to load latest representation.
			if ( e.length ) {
				p = e.parent();
				setTimeout( function(){
					// Request the widget content.
					p.load( ajaxurl + '?action=dashboard-widgets&widget=' + id + '&pagenow=' + pagenow, '', function() {
						// Hide the parent and slide it out for visual fancyness.
						p.hide().slideDown('normal', function(){
							$(this).css('display', '');
						});
					});
				}, i * 500 );
			}
		}

		// If we have received a specific element to fetch, check if it is valid.
		if ( el ) {
			el = el.toString();
			// If the element is available as AJAX widget, show it.
			if ( $.inArray(el, ajaxWidgets) !== -1 ) {
				// Show element without any delay.
				show(0, el);
			}
		} else {
			// Walk through all ajaxWidgets, loading them after each other.
			$.each( ajaxWidgets, show );
		}
	};

	// Initially populate ajax widgets.
	ajaxPopulateWidgets();

	// Register ajax widgets as postbox toggles.
	postboxes.add_postbox_toggles(pagenow, { pbshow: ajaxPopulateWidgets } );

	/**
	 * Control the Quick Press (Quick Draft) widget.
	 *
	 * @since 2.7.0
	 *
	 * @global
	 *
	 * @returns {void}
	 */
	window.quickPressLoad = function() {
		var act = $('#quickpost-action'), t;

		// Enable the submit buttons.
		$( '#quick-press .submit input[type="submit"], #quick-press .submit input[type="reset"]' ).prop( 'disabled' , false );

		t = $('#quick-press').submit( function( e ) {
			e.preventDefault();

			// Show a spinner.
			$('#dashboard_quick_press #publishing-action .spinner').show();

			// Disable the submit button to prevent duplicate submissions.
			$('#quick-press .submit input[type="submit"], #quick-press .submit input[type="reset"]').prop('disabled', true);

			// Post the entered data to save it.
			$.post( t.attr( 'action' ), t.serializeArray(), function( data ) {
				// Replace the form, and prepend the published post.
				$('#dashboard_quick_press .inside').html( data );
				$('#quick-press').removeClass('initial-form');
				quickPressLoad();
				highlightLatestPost();

				// Focus the title to allow for quickly drafting another post.
				$('#title').focus();
			});

			/**
			 * Highlights the latest post for one second.
			 *
			 * @returns {void}
 			 */
			function highlightLatestPost () {
				var latestPost = $('.drafts ul li').first();
				latestPost.css('background', '#fffbe5');
				setTimeout(function () {
					latestPost.css('background', 'none');
				}, 1000);
			}
		} );

		// Change the QuickPost action to the publish value.
		$('#publish').click( function() { act.val( 'post-quickpress-publish' ); } );

		$('#quick-press').on( 'click focusin', function() {
			wpActiveEditor = 'content';
		});

		autoResizeTextarea();
	};
	window.quickPressLoad();

	// Enable the dragging functionality of the widgets.
	$( '.meta-box-sortables' ).sortable( 'option', 'containment', '#wpwrap' );

	/**
	 * Adjust the height of the textarea based on the content.
	 *
	 * @since 3.6.0
	 *
	 * @returns {void}
	 */
	function autoResizeTextarea() {
		// When IE8 or older is used to render this document, exit.
		if ( document.documentMode && document.documentMode < 9 ) {
			return;
		}

		// Add a hidden div. We'll copy over the text from the textarea to measure its height.
		$('body').append( '<div class="quick-draft-textarea-clone" style="display: none;"></div>' );

		var clone = $('.quick-draft-textarea-clone'),
			editor = $('#content'),
			editorHeight = editor.height(),
			/*
			 * 100px roughly accounts for browser chrome and allows the
			 * save draft button to show on-screen at the same time.
			 */
			editorMaxHeight = $(window).height() - 100;

		/*
		 * Match up textarea and clone div as much as possible.
		 * Padding cannot be reliably retrieved using shorthand in all browsers.
		 */
		clone.css({
			'font-family': editor.css('font-family'),
			'font-size':   editor.css('font-size'),
			'line-height': editor.css('line-height'),
			'padding-bottom': editor.css('paddingBottom'),
			'padding-left': editor.css('paddingLeft'),
			'padding-right': editor.css('paddingRight'),
			'padding-top': editor.css('paddingTop'),
			'white-space': 'pre-wrap',
			'word-wrap': 'break-word',
			'display': 'none'
		});

		// The 'propertychange' is used in IE < 9.
		editor.on('focus input propertychange', function() {
			var $this = $(this),
				// Add a non-breaking space to ensure that the height of a trailing newline is
				// included.
				textareaContent = $this.val() + '&nbsp;',
				// Add 2px to compensate for border-top & border-bottom.
				cloneHeight = clone.css('width', $this.css('width')).text(textareaContent).outerHeight() + 2;

			// Default to show a vertical scrollbar, if needed.
			editor.css('overflow-y', 'auto');

			// Only change the height if it has changed and both heights are below the max.
			if ( cloneHeight === editorHeight || ( cloneHeight >= editorMaxHeight && editorHeight >= editorMaxHeight ) ) {
				return;
			}

			/*
			 * Don't allow editor to exceed the height of the window.
			 * This is also bound in CSS to a max-height of 1300px to be extra safe.
			 */
			if ( cloneHeight > editorMaxHeight ) {
				editorHeight = editorMaxHeight;
			} else {
				editorHeight = cloneHeight;
			}

			// Disable scrollbars because we adjust the height to the content.
			editor.css('overflow', 'hidden');

			$this.css('height', editorHeight + 'px');
		});
	}

} );

jQuery( function( $ ) {
	'use strict';

	var communityEventsData = window.communityEventsData || {},
		app;

	/**
	 * Global Community Events namespace.
	 *
	 * @since 4.8.0
	 *
	 * @memberOf wp
	 * @namespace wp.communityEvents
	 */
	app = window.wp.communityEvents = /** @lends wp.communityEvents */{
		initialized: false,
		model: null,

		/**
		 * Initializes the wp.communityEvents object.
		 *
		 * @since 4.8.0
		 *
		 * @returns {void}
		 */
		init: function() {
			if ( app.initialized ) {
				return;
			}

			var $container = $( '#community-events' );

			/*
			 * When JavaScript is disabled, the errors container is shown, so
			 * that "This widget requires JavaScript" message can be seen.
			 *
			 * When JS is enabled, the container is hidden at first, and then
			 * revealed during the template rendering, if there actually are
			 * errors to show.
			 *
			 * The display indicator switches from `hide-if-js` to `aria-hidden`
			 * here in order to maintain consistency with all the other fields
			 * that key off of `aria-hidden` to determine their visibility.
			 * `aria-hidden` can't be used initially, because there would be no
			 * way to set it to false when JavaScript is disabled, which would
			 * prevent people from seeing the "This widget requires JavaScript"
			 * message.
			 */
			$( '.community-events-errors' )
				.attr( 'aria-hidden', 'true' )
				.removeClass( 'hide-if-js' );

			$container.on( 'click', '.community-events-toggle-location, .community-events-cancel', app.toggleLocationForm );

			/**
			 * Filters events based on entered location.
			 *
			 * @returns {void}
			 */
			$container.on( 'submit', '.community-events-form', function( event ) {
				var location = $.trim( $( '#community-events-location' ).val() );

				event.preventDefault();

				/*
				 * Don't trigger a search if the search field is empty or the
				 * search term was made of only spaces before being trimmed.
				 */
				if ( ! location ) {
					return;
				}

				app.getEvents({
					location: location
				});
			});

			if ( communityEventsData && communityEventsData.cache && communityEventsData.cache.location && communityEventsData.cache.events ) {
				app.renderEventsTemplate( communityEventsData.cache, 'app' );
			} else {
				app.getEvents();
			}

			app.initialized = true;
		},

		/**
		 * Toggles the visibility of the Edit Location form.
		 *
		 * @since 4.8.0
		 *
		 * @param {event|string} action 'show' or 'hide' to specify a state;
		 *                              or an event object to flip between states.
		 *
		 * @returns {void}
		 */
		toggleLocationForm: function( action ) {
			var $toggleButton = $( '.community-events-toggle-location' ),
				$cancelButton = $( '.community-events-cancel' ),
				$form         = $( '.community-events-form' ),
				$target       = $();

			if ( 'object' === typeof action ) {
				// The action is the event object: get the clicked element.
				$target = $( action.target );
				/*
				 * Strict comparison doesn't work in this case because sometimes
				 * we explicitly pass a string as value of aria-expanded and
				 * sometimes a boolean as the result of an evaluation.
				 */
				action = 'true' == $toggleButton.attr( 'aria-expanded' ) ? 'hide' : 'show';
			}

			if ( 'hide' === action ) {
				$toggleButton.attr( 'aria-expanded', 'false' );
				$cancelButton.attr( 'aria-expanded', 'false' );
				$form.attr( 'aria-hidden', 'true' );
				/*
				 * If the Cancel button has been clicked, bring the focus back
				 * to the toggle button so users relying on screen readers don't
				 * lose their place.
				 */
				if ( $target.hasClass( 'community-events-cancel' ) ) {
					$toggleButton.focus();
				}
			} else {
				$toggleButton.attr( 'aria-expanded', 'true' );
				$cancelButton.attr( 'aria-expanded', 'true' );
				$form.attr( 'aria-hidden', 'false' );
			}
		},

		/**
		 * Sends REST API requests to fetch events for the widget.
		 *
		 * @since 4.8.0
		 *
		 * @param {Object} requestParams REST API Request parameters object.
		 *
		 * @returns {void}
		 */
		getEvents: function( requestParams ) {
			var initiatedBy,
				app = this,
				$spinner = $( '.community-events-form' ).children( '.spinner' );

			requestParams          = requestParams || {};
			requestParams._wpnonce = communityEventsData.nonce;
			requestParams.timezone = window.Intl ? window.Intl.DateTimeFormat().resolvedOptions().timeZone : '';

			initiatedBy = requestParams.location ? 'user' : 'app';

			$spinner.addClass( 'is-active' );

			wp.ajax.post( 'get-community-events', requestParams )
				.always( function() {
					$spinner.removeClass( 'is-active' );
				})

				.done( function( response ) {
					if ( 'no_location_available' === response.error ) {
						if ( requestParams.location ) {
							response.unknownCity = requestParams.location;
						} else {
							/*
							 * No location was passed, which means that this was an automatic query
							 * based on IP, locale, and timezone. Since the user didn't initiate it,
							 * it should fail silently. Otherwise, the error could confuse and/or
							 * annoy them.
							 */
							delete response.error;
						}
					}
					app.renderEventsTemplate( response, initiatedBy );
				})

				.fail( function() {
					app.renderEventsTemplate({
						'location' : false,
						'error'    : true
					}, initiatedBy );
				});
		},

		/**
		 * Renders the template for the Events section of the Events & News widget.
		 *
		 * @since 4.8.0
		 *
		 * @param {Object} templateParams The various parameters that will get passed to wp.template.
		 * @param {string} initiatedBy    'user' to indicate that this was triggered manually by the user;
		 *                                'app' to indicate it was triggered automatically by the app itself.
		 *
		 * @returns {void}
		 */
		renderEventsTemplate: function( templateParams, initiatedBy ) {
			var template,
				elementVisibility,
				l10nPlaceholder  = /%(?:\d\$)?s/g, // Match `%s`, `%1$s`, `%2$s`, etc.
				$toggleButton    = $( '.community-events-toggle-location' ),
				$locationMessage = $( '#community-events-location-message' ),
				$results         = $( '.community-events-results' );

			/*
			 * Hide all toggleable elements by default, to keep the logic simple.
			 * Otherwise, each block below would have to turn hide everything that
			 * could have been shown at an earlier point.
			 *
			 * The exception to that is that the .community-events container is hidden
			 * when the page is first loaded, because the content isn't ready yet,
			 * but once we've reached this point, it should always be shown.
			 */
			elementVisibility = {
				'.community-events'                  : true,
				'.community-events-loading'          : false,
				'.community-events-errors'           : false,
				'.community-events-error-occurred'   : false,
				'.community-events-could-not-locate' : false,
				'#community-events-location-message' : false,
				'.community-events-toggle-location'  : false,
				'.community-events-results'          : false
			};

			/*
			 * Determine which templates should be rendered and which elements
			 * should be displayed.
			 */
			if ( templateParams.location.ip ) {
				/*
				 * If the API determined the location by geolocating an IP, it will
				 * provide events, but not a specific location.
				 */
				$locationMessage.text( communityEventsData.l10n.attend_event_near_generic );

				if ( templateParams.events.length ) {
					template = wp.template( 'community-events-event-list' );
					$results.html( template( templateParams ) );
				} else {
					template = wp.template( 'community-events-no-upcoming-events' );
					$results.html( template( templateParams ) );
				}

				elementVisibility['#community-events-location-message'] = true;
				elementVisibility['.community-events-toggle-location']  = true;
				elementVisibility['.community-events-results']          = true;

			} else if ( templateParams.location.description ) {
				template = wp.template( 'community-events-attend-event-near' );
				$locationMessage.html( template( templateParams ) );

				if ( templateParams.events.length ) {
					template = wp.template( 'community-events-event-list' );
					$results.html( template( templateParams ) );
				} else {
					template = wp.template( 'community-events-no-upcoming-events' );
					$results.html( template( templateParams ) );
				}

				if ( 'user' === initiatedBy ) {
					wp.a11y.speak( communityEventsData.l10n.city_updated.replace( l10nPlaceholder, templateParams.location.description ), 'assertive' );
				}

				elementVisibility['#community-events-location-message'] = true;
				elementVisibility['.community-events-toggle-location']  = true;
				elementVisibility['.community-events-results']          = true;

			} else if ( templateParams.unknownCity ) {
				template = wp.template( 'community-events-could-not-locate' );
				$( '.community-events-could-not-locate' ).html( template( templateParams ) );
				wp.a11y.speak( communityEventsData.l10n.could_not_locate_city.replace( l10nPlaceholder, templateParams.unknownCity ) );

				elementVisibility['.community-events-errors']           = true;
				elementVisibility['.community-events-could-not-locate'] = true;

			} else if ( templateParams.error && 'user' === initiatedBy ) {
				/*
				 * Errors messages are only shown for requests that were initiated
				 * by the user, not for ones that were initiated by the app itself.
				 * Showing error messages for an event that user isn't aware of
				 * could be confusing or unnecessarily distracting.
				 */
				wp.a11y.speak( communityEventsData.l10n.error_occurred_please_try_again );

				elementVisibility['.community-events-errors']         = true;
				elementVisibility['.community-events-error-occurred'] = true;
			} else {
				$locationMessage.text( communityEventsData.l10n.enter_closest_city );

				elementVisibility['#community-events-location-message'] = true;
				elementVisibility['.community-events-toggle-location']  = true;
			}

			// Set the visibility of toggleable elements.
			_.each( elementVisibility, function( isVisible, element ) {
				$( element ).attr( 'aria-hidden', ! isVisible );
			});

			$toggleButton.attr( 'aria-expanded', elementVisibility['.community-events-toggle-location'] );

			if ( templateParams.location && ( templateParams.location.ip || templateParams.location.latitude ) ) {
				// Hide the form when there's a valid location.
				app.toggleLocationForm( 'hide' );

				if ( 'user' === initiatedBy ) {
					/*
					 * When the form is programmatically hidden after a user search,
					 * bring the focus back to the toggle button so users relying
					 * on screen readers don't lose their place.
					 */
					$toggleButton.focus();
				}
			} else {
				app.toggleLocationForm( 'show' );
			}
		}
	};

	if ( $( '#dashboard_primary' ).is( ':visible' ) ) {
		app.init();
	} else {
		$( document ).on( 'postbox-toggled', function( event, postbox ) {
			var $postbox = $( postbox );

			if ( 'dashboard_primary' === $postbox.attr( 'id' ) && $postbox.is( ':visible' ) ) {
				app.init();
			}
		});
	}
});
