/**
 * Javascript to make Customizer Control add and remove inputs.
 * @package Twenty8teen
 */

( function( wp, $ ) {

 	wp.customize.controlConstructor['repeat-one-many'] = wp.customize.Control.extend( {
 	  ready: function() {
 			var control = this;
			control.container.on('click', '.add-content', function() {
				control.container.find('.customize-repeat-one-many')
					.append(control.params.newInputContent);
			});

			control.container.on('blur', '.one-key', function() {
				if ( $(this).val() && $(this).parent().find('.one-value').val() ) {
					control.updateSetting();
				}
			 });

			control.container.on('blur', '.one-value', function() {
				if ( $(this).val() && $(this).parent().find('.one-key').val() ) {
					control.updateSetting();
				}
			 });

			control.container.on('click','.submitdelete', function() {
			 	$(this).parent('.repeat-one-one').remove();
				control.updateSetting();
			});

 		},

		/**
		 * Update setting to match user input.
		 */
 		updateSetting: function() {
 			var control = this;
			var values = control.getValues();
			control.setting.set( values );
 		},

		/**
		 * Get all the repeater values into an array.
		 */
 		getValues: function() {
 			var values = [];
			this.container.find('.repeat-one-one').each(function() {
			values.push( {
					'one_key': $(this).find('.one-key').val(),
					'one_value': $(this).find('.one-value').val()
				} );
			});
 			return values;
 		}

 	} );
} )( wp, jQuery );
