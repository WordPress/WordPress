/* global window, document, jQuery, inlineEditL10n, ajaxurl */
var inlineEditKey;

( function( $ ) {
	inlineEditKey = {

		init: function() {
			var t = this,
				row = $( '#security-keys-section #inline-edit' );

			t.what = '#key-';

			$( '#security-keys-section #the-list' ).on( 'click', 'a.editinline', function() {
				inlineEditKey.edit( this );
				return false;
			} );

			// Prepare the edit row.
			row.keyup( function( event ) {
				if ( 27 === event.which ) {
					return inlineEditKey.revert();
				}
			} );

			$( 'a.cancel', row ).click( function() {
				return inlineEditKey.revert();
			} );

			$( 'a.save', row ).click( function() {
				return inlineEditKey.save( this );
			} );

			$( 'input, select', row ).keydown( function( event ) {
				if ( 13 === event.which ) {
					return inlineEditKey.save( this );
				}
			} );
		},

		toggle: function( el ) {
			var t = this;

			if ( 'none' === $( t.what + t.getId( el ) ).css( 'display' ) ) {
				t.revert();
			} else {
				t.edit( el );
			}
		},

		edit: function( id ) {
			var editRow, rowData, val,
				t = this;
			t.revert();

			if ( 'object' === typeof id ) {
				id = t.getId( id );
			}

			editRow = $( '#inline-edit' ).clone( true );
			rowData = $( '#inline_' + id );

			$( 'td', editRow ).attr( 'colspan', $( 'th:visible, td:visible', '#security-keys-section .widefat thead' ).length );

			$( t.what + id ).hide().after( editRow ).after( '<tr class="hidden"></tr>' );

			val = $( '.name', rowData );
			val.find( 'img' ).replaceWith( function() {
				return this.alt;
			} );
			val = val.text();
			$( ':input[name="name"]', editRow ).val( val );

			$( editRow ).attr( 'id', 'edit-' + id ).addClass( 'inline-editor' ).show();
			$( '.ptitle', editRow ).eq( 0 ).focus();

			return false;
		},

		save: function( id ) {
			var params, fields;

			if ( 'object' === typeof id ) {
				id = this.getId( id );
			}

			$( '#security-keys-section table.widefat .spinner' ).addClass( 'is-active' );

			params = {
				action: 'inline-save-key',
				keyHandle: id,
				user_id: window.u2fL10n.user_id
			};

			fields = $( '#edit-' + id ).find( ':input' ).serialize();
			params = fields + '&' + $.param( params );

			// Make ajax request.
			$.post( ajaxurl, params,
				function( r ) {
					var row, newID;
					$( '#security-keys-section table.widefat .spinner' ).removeClass( 'is-active' );

					if ( r ) {
						if ( -1 !== r.indexOf( '<tr' ) ) {
							$( inlineEditKey.what + id ).siblings( 'tr.hidden' ).addBack().remove();
							newID = $( r ).attr( 'id' );

							$( '#edit-' + id ).before( r ).remove();

							if ( newID ) {
								row = $( '#' + newID );
							} else {
								row = $( inlineEditKey.what + id );
							}

							row.hide().fadeIn();
						} else {
							$( '#edit-' + id + ' .inline-edit-save .error' ).html( r ).show();
						}
					} else {
						$( '#edit-' + id + ' .inline-edit-save .error' ).html( inlineEditL10n.error ).show();
					}
				}
			);
			return false;
		},

		revert: function() {
			var id = $( '#security-keys-section table.widefat tr.inline-editor' ).attr( 'id' );

			if ( id ) {
				$( '#security-keys-section table.widefat .spinner' ).removeClass( 'is-active' );
				$( '#' + id ).siblings( 'tr.hidden' ).addBack().remove();
				id = id.replace( /\w+\-/, '' );
				$( this.what + id ).show();
			}

			return false;
		},

		getId: function( o ) {
			var id = 'TR' === o.tagName ? o.id : $( o ).parents( 'tr' ).attr( 'id' );
			return id.replace( /\w+\-/, '' );
		}
	};

	$( document ).ready( function() {
		inlineEditKey.init();
	} );
}( jQuery ) );
