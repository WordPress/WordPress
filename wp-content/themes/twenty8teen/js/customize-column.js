/**
 * Javascript to make Customizer Control consolidate many checkboxes into one input.
 * The idea is from http://justintadlock.com/archives/2015/05/26/multiple-checkbox-customizer-control
 * @package Twenty8teen
 */

( function( wp ) {
 	wp.customize.controlConstructor['checkbox-column'] = wp.customize.Control.extend( {
 	  ready: function() {
 			var control = this;
 			control.container.find( 'input[type="checkbox"]' ).on( 'change', function() {
 				/* Get the values of all the checked boxes. */
 				var checkedValues = control.getValueList( 'input[type="checkbox"]:checked' );
 				/* Get the values of all the unchecked boxes. */
 				var uncheckedValues = control.getValueList( 'input[type="checkbox"]:not(:checked)' );

				control.setting.set( uncheckedValues + ',' + checkedValues );
 			} );
			control.setting.allValues = control.getValueList( 'input[type="checkbox"]' );
			control.setting.validate = function ( newValue ) {
				var setting = this;
				var allValues = this.allValues.split( ' ' );
				newValue = wp.customize.Setting.prototype.validate.call( setting, newValue );
				if ( newValue.indexOf( ',' ) === -1 ) {
					var valid = newValue.split( ' ' ).filter( function( item ) {
		 				return allValues.includes( item );
		 			} );
		 			var unchosen = allValues.filter( function( item ) {
						return ! valid.includes( item );
		 			} );
					newValue = unchosen.join( ' ' ) + ',' + valid.join( ' ' );
				}
				return newValue;
			};
 			control.updateChecks();
 			control.setting.bind( _.bind( control.updateChecks, control ) );
 		},

		/**
		 * Update the checkboxes to reflect the current value.
		 */
 		updateChecks: function() {
 			var control = this;
 			var allValues = control.setting.allValues.split( ' ' );
 			var currentVal = control.setting.get() || control.setting.allValues + ',';
 			var delim = currentVal.indexOf( ',' ) === -1 ? ',' : '';
 			currentVal = delim.concat(currentVal).split( ',' );
			var valid = currentVal[1].split( ' ' );
			control.container.find( 'input[type="checkbox"]' ).each( function() {
 				jQuery( this ).prop( 'checked', valid.includes( this.value ) );
 			} );
 		},

		/**
		 * Join the checkbox values into a space separated string.
		 */
 		getValueList: function( selector ) {
 			var control = this;
 			return jQuery( selector, control.container )
 				.map( function() {
 					return this.value;
 				} )
 				.get().join( ' ' );
 		}
 	} );
} )( wp );
