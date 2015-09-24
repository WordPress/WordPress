/*globals jQuery */

(function ($) {
	'use strict';

	var listTable,
		actions,
		doActions;

	function getChecked() {
		return listTable.find( 'table .check-column input[type="checkbox"]:checked' );
	}

	/**
	 * Enable and Disable Apply button in wp-list
	 *
	 * @param {jQuery.Event} e
	 */
	function setApplyButton( e ) {
		var checked = getChecked().length;

		if ( 'SELECT' === e.target.tagName ) {
			actions.val( e.target.value );
		}

		actions.prop( 'disabled', ! checked );
		doActions.prop( 'disabled', ! checked || -1 === parseInt( actions.val(), 10 ) );
	}

    $(document).ready(function () {
		listTable = $( '.wp-list-table' ).closest( 'form' );
		if ( ! listTable.length ) {
			return;
		}

		actions = listTable.find( 'select[name="action"], select[name="action2"]' )
			.on( 'change', setApplyButton )
			.prop( 'disabled', true );

		doActions = listTable.find( '#doaction, #doaction2' )
			.prop( 'disabled', true );

		listTable.find( 'table' ).on( 'click', '.check-column :checkbox', setApplyButton );
    });

}(jQuery));