/* global ajaxurl, current_site_id, isRtl */
/**
 * Suggests users in a multisite environment.
 *
 * For input fields where the admin can select a user based on email or
 * username, this script shows an autocompletion menu for these inputs. Should
 * only be used in a multisite environment. Only users in the currently active
 * site are shown.
 *
 * @since 3.4.0
 */

(function( $ ) {
	var id = ( typeof current_site_id !== 'undefined' ) ? '&site_id=' + current_site_id : '';
	$(document).ready( function() {
		var position = { offset: '0, -1' };
		if ( typeof isRtl !== 'undefined' && isRtl ) {
			position.my = 'right top';
			position.at = 'right bottom';
		}

		/**
		 * Adds an autocomplete function to input fields marked with the class
		 * 'wp-suggest-user'.
		 *
		 * A minimum of two characters is required to trigger
		 * the suggestions. The autocompletion menu is shown at the left bottom of the input
		 * field. On RTL installations, it is shown at the right top.
		 * Adds the class 'open' to the input field when the autocompletion menu
		 * is shown.
		 *
		 * Does a backend call to retrieve the users.
		 *
		 * Optional data-attributes:
		 * - data-autocomplete-type (add, search)
		 *   The action that is going to be performed: search for existing users
		 *   or add a new one. Default: add
		 * - data-autocomplete-field (user_login, user_email)
		 *   The field that is returned as the value for the suggestion.
		 *   Default: user_login
		 *
		 * @see wp-admin/includes/admin-actions.php:wp_ajax_autocomplete_user()
		 */
		$( '.wp-suggest-user' ).each( function(){
			var $this = $( this ),
				autocompleteType = ( typeof $this.data( 'autocompleteType' ) !== 'undefined' ) ? $this.data( 'autocompleteType' ) : 'add',
				autocompleteField = ( typeof $this.data( 'autocompleteField' ) !== 'undefined' ) ? $this.data( 'autocompleteField' ) : 'user_login';

			$this.autocomplete({
				source:    ajaxurl + '?action=autocomplete-user&autocomplete_type=' + autocompleteType + '&autocomplete_field=' + autocompleteField + id,
				delay:     500,
				minLength: 2,
				position:  position,
				open: function() {
					$( this ).addClass( 'open' );
				},
				close: function() {
					$( this ).removeClass( 'open' );
				}
			});
		});
	});
})( jQuery );
