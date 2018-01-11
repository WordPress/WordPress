jQuery( document ).ready(function( $ ) {
	$( '#update-nav-menu' ).bind( 'click', function( e ) {
		if ( e.target && e.target.className && -1 != e.target.className.indexOf( 'item-edit' ) ) {
			$( "input[value='#pll_switcher'][type=text]" ).parent().parent().parent().each(function(){
				var item = $( this ).attr( 'id' ).substring( 19 );
				$( this ).children( 'p:not( .field-move )' ).remove(); // remove default fields we don't need

				h = $( '<input>' ).attr({
					type:  'hidden',
					id:    'edit-menu-item-title-' + item,
					name:  'menu-item-title[' + item + ']',
					value: pll_data.title
				});
				$( this ).append( h );

				h = $( '<input>' ).attr({
					type:  'hidden',
					id:    'edit-menu-item-url-' + item,
					name:  'menu-item-url[' + item + ']',
					value: '#pll_switcher'
				});
				$( this ).append( h );

				// a hidden field which exits only if our jQuery code has been executed
				h = $( '<input>' ).attr({
					type:  'hidden',
					id:    'edit-menu-item-pll-detect-' + item,
					name:  'menu-item-pll-detect[' + item + ']',
					value: 1
				});
				$( this ).append( h );

				ids = Array( 'hide_if_no_translation', 'hide_current', 'force_home', 'show_flags', 'show_names', 'dropdown' ); // reverse order

				// add the fields
				for ( var i = 0; i < ids.length; i++ ) {
					p = $( '<p>' ).attr( 'class', 'description' );
					$( this ).prepend( p );
					label = $( '<label>' ).attr( 'for', 'edit-menu-item-' + ids[ i ] + '-' + item ).text( ' ' + pll_data.strings[ ids[ i ] ] );
					p.append( label );
					cb = $( '<input>' ).attr({
						type:  'checkbox',
						id:    'edit-menu-item-' + ids[ i ] + '-' + item,
						name:  'menu-item-' + ids[ i ] + '[' + item + ']',
						value: 1
					});
					if ( ( typeof( pll_data.val[ item ] ) != 'undefined' && pll_data.val[ item ][ ids[ i ] ] == 1 ) || ( typeof( pll_data.val[ item ] ) == 'undefined' && ids[ i ] == 'show_names' ) ) { // show_names as default value
						cb.prop( 'checked', true );
					}
					label.prepend( cb );
				}
			});

			// disallow unchecking both show names and show flags
			$( '.menu-item-data-object-id' ).each(function() {
				var id = $( this ).val();
				var options = ['names-', 'flags-'];
				$.each( options, function( i, v ) {
					$( '#edit-menu-item-show_' + v + id ).change(function() {
						if ( 'checked' != $( this ).attr( 'checked' ) ) {
							$( '#edit-menu-item-show_' + options[ 1 - i ] + id ).prop( 'checked', true );
						}
					});
				});
			});
		}
	});
});
