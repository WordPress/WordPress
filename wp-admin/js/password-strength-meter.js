/**
 * @output wp-admin/js/password-strength-meter.js
 */

/* global zxcvbn */
window.wp = window.wp || {};

(function($){
	var __ = wp.i18n.__,
		sprintf = wp.i18n.sprintf;

	/**
	 * Contains functions to determine the password strength.
	 *
	 * @since 3.7.0
	 *
	 * @namespace
	 */
	wp.passwordStrength = {
		/**
		 * Determines the strength of a given password.
		 *
		 * Compares first password to the password confirmation.
		 *
		 * @since 3.7.0
		 *
		 * @param {string} password1       The subject password.
		 * @param {Array}  disallowedList An array of words that will lower the entropy of
		 *                                 the password.
		 * @param {string} password2       The password confirmation.
		 *
		 * @return {number} The password strength score.
		 */
		meter : function( password1, disallowedList, password2 ) {
			if ( ! $.isArray( disallowedList ) )
				disallowedList = [ disallowedList.toString() ];

			if (password1 != password2 && password2 && password2.length > 0)
				return 5;

			if ( 'undefined' === typeof window.zxcvbn ) {
				// Password strength unknown.
				return -1;
			}

			var result = zxcvbn( password1, disallowedList );
			return result.score;
		},

		/**
		 * Builds an array of words that should be penalized.
		 *
		 * Certain words need to be penalized because it would lower the entropy of a
		 * password if they were used. The disallowedList is based on user input fields such
		 * as username, first name, email etc.
		 *
		 * @since 3.7.0
		 * @deprecated 5.5.0 Use {@see 'userInputDisallowedList()'} instead.
		 *
		 * @return {string[]} The array of words to be disallowed.
		 */
		userInputBlacklist : function() {
			window.console.log(
				sprintf(
					/* translators: 1: Deprecated function name, 2: Version number, 3: Alternative function name. */
					__( '%1$s is deprecated since version %2$s! Use %3$s instead. Please consider writing more inclusive code.' ),
					'wp.passwordStrength.userInputBlacklist()',
					'5.5.0',
					'wp.passwordStrength.userInputDisallowedList()'
				)
			);

			return wp.passwordStrength.userInputDisallowedList();
		},

		/**
		 * Builds an array of words that should be penalized.
		 *
		 * Certain words need to be penalized because it would lower the entropy of a
		 * password if they were used. The disallowed list is based on user input fields such
		 * as username, first name, email etc.
		 *
		 * @since 5.5.0
		 *
		 * @return {string[]} The array of words to be disallowed.
		 */
		userInputDisallowedList : function() {
			var i, userInputFieldsLength, rawValuesLength, currentField,
				rawValues       = [],
				disallowedList  = [],
				userInputFields = [ 'user_login', 'first_name', 'last_name', 'nickname', 'display_name', 'email', 'url', 'description', 'weblog_title', 'admin_email' ];

			// Collect all the strings we want to disallow.
			rawValues.push( document.title );
			rawValues.push( document.URL );

			userInputFieldsLength = userInputFields.length;
			for ( i = 0; i < userInputFieldsLength; i++ ) {
				currentField = $( '#' + userInputFields[ i ] );

				if ( 0 === currentField.length ) {
					continue;
				}

				rawValues.push( currentField[0].defaultValue );
				rawValues.push( currentField.val() );
			}

			/*
			 * Strip out non-alphanumeric characters and convert each word to an
			 * individual entry.
			 */
			rawValuesLength = rawValues.length;
			for ( i = 0; i < rawValuesLength; i++ ) {
				if ( rawValues[ i ] ) {
					disallowedList = disallowedList.concat( rawValues[ i ].replace( /\W/g, ' ' ).split( ' ' ) );
				}
			}

			/*
			 * Remove empty values, short words and duplicates. Short words are likely to
			 * cause many false positives.
			 */
			disallowedList = $.grep( disallowedList, function( value, key ) {
				if ( '' === value || 4 > value.length ) {
					return false;
				}

				return $.inArray( value, disallowedList ) === key;
			});

			return disallowedList;
		}
	};

	// Backward compatibility.

	/**
	 * Password strength meter function.
	 *
	 * @since 2.5.0
	 * @deprecated 3.7.0 Use wp.passwordStrength.meter instead.
	 *
	 * @global
	 *
	 * @type {wp.passwordStrength.meter}
	 */
	window.passwordStrength = wp.passwordStrength.meter;
})(jQuery);
