( function( $ ) {

	"use strict";

	// Extend etCore since it is declared by localization.
	$.extend( etCore, {

		init: function() {
			this.tabs();
			this.listen();
		},

		applyMaxHeight: function() {
			var $et_core_modal_overlay = $( '.et-core-modal-overlay' ),
				$et_core_modal = $et_core_modal_overlay.find( '.et-core-modal' ),
				overlay_height = $et_core_modal_overlay.innerHeight(),
				disabled_scrollbar_class = 'et-core-modal-disabled-scrollbar',
				et_core_modal_height;

			if ( ! $et_core_modal_overlay.length || ! $et_core_modal_overlay.hasClass('et-core-active') ) {
				return;
			}

			$et_core_modal_overlay.addClass( disabled_scrollbar_class );

			et_core_modal_height = $et_core_modal.innerHeight();

			if ( et_core_modal_height > ( overlay_height * 0.6 ) ) {
				$et_core_modal_overlay.removeClass( disabled_scrollbar_class );

				$et_core_modal.css( 'marginTop', '0' );

				return;
			}

			$et_core_modal.css( 'marginTop', '-' + ( et_core_modal_height / 2 ) + 'px' );
		},

		listen: function() {
			var $this = this;

			$( document ).on( 'click', '[data-et-core-modal]', function( e ) {
				e.preventDefault();

				var $button = $(this),
					$overlay = $( $button.data( 'et-core-modal' ) );

				if ( $button.hasClass( 'et-core-disabled' ) ) {
					return;
				}

				$overlay.addClass( 'et-core-active' );
				$( 'body' ).addClass( 'et-core-nbfc');
				$( window ).trigger( 'et-core-modal-active' );
			} );

			$( document ).on( 'click', '[data-et-core-modal="close"], .et-core-modal-overlay', function( e ) {
				$this.modalClose( e, this );
			} );

			// Distroy listener to make sure it is only called once.
			$this.listen = function() {};
		},

		modalClose: function( e, self ) {
			// Prevent default and propagation.
			if ( e && self ) {
				var $element = $( self );

				if ( self !== e.target ) {
					return;
				} else {
					e.preventDefault();
				}
			}

			$( '.et-core-modal-overlay.et-core-active' ).addClass( 'et-core-closing' ).delay( 600 ).queue( function() {
				var $overlay = $( this );

				$overlay.removeClass( 'et-core-active et-core-closing' ).dequeue();
				$( 'body' ).removeClass( 'et-core-nbfc');
				$overlay.find( '.et-core-modal' ).removeAttr( 'style' );
			} );
		},

		modalTitle: function( text ) {
			$( '.et-core-modal-overlay.et-core-active .et-core-modal-title' ).html( text );
		},

		modalContent: function( text, replace, remove, parent ) {
			var parent = parent ? parent + ' ' : '',
				$modal = $( '.et-core-modal-overlay.et-core-active' ),
				$content = $modal.find( parent + '.et-core-modal-content' ),
				tempContent = parent + '.et-core-modal-temp-content',
				contentHeight = $content.height();

			if ( replace ) {
				$content.html( text );
			} else {
				var displayTempContent = function() {
					var removeContent = function( delay ) {
						$content.delay( delay ).queue( function() {
							$modal.find( tempContent ).fadeOut( 200, function() {
								$content.fadeIn( 200 );
								$( this ).remove();
							} );
							$( this ).dequeue();
						} );
					}

					if ( true === remove ) {
						text = text + '<p><a class="et-core-modal-remove-temp-content" href="#">' + etCore.text.modalTempContentCheck + '</a></p>';
					}

					$content.stop().fadeOut( 200, function() {
						$( this ).before( '<div class="et-core-modal-temp-content"><div>' + text + '</div></div>' );
						$modal.find( tempContent ).height( contentHeight ).hide().fadeIn( 200 );
						$modal.find( '.et-core-modal-remove-temp-content' ).click( function( e ) {
							removeContent( 0 );
						} );
					} );

					if ( $.isNumeric( remove ) ) {
						removeContent( remove );
					}
				}

				if ( $modal.find( tempContent ).length > 0 ) {
					$modal.find( tempContent ).fadeOut( 200, function() {
						$( this ).remove();
						displayTempContent();
					} );
				} else {
					displayTempContent();
				}
			}
		},

		tabs: function() {
			$( '[data-et-core-tabs]' ).tabs( {
				fx: {
					opacity: 'toggle',
					duration:'fast'
				},
				selected: 0,
				beforeActivate: function( event, ui ) {
					ui.newPanel.addClass( 'et-core-tabs-transition' );
				}
			} );
		},

	} );

	$( window ).on( 'et-core-modal-active', function() {
		etCore.applyMaxHeight();
	} );

	$( document ).ready( function() {
		etCore.init();
	});

	$( window ).resize( function() {
		etCore.applyMaxHeight();
	} );

} )( jQuery );