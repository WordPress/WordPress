( function( $ ) {

	"use strict";

	// Extend etCore since it is declared by localization.
	$.extend( etCore, {

		init: function() {
			this.tabs();
			this.listen();
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

				// Wait until it has been displayed but still transitioned
				setTimeout( function() {
					var $modal = $overlay.find('.et-core-modal'),
						modal_height = $modal.outerHeight(),
						modal_height_adjustment = 0 - ( modal_height / 2 );

					$modal.css({
						top : '50%',
						bottom : 'auto',
						marginTop : modal_height_adjustment
					});
				}, 100 );
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

	$( document ).ready( function() {
		etCore.init();
	});

} )( jQuery );