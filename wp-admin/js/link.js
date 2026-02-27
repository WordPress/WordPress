/**
 * @output wp-admin/js/link.js
 */

/* global postboxes, deleteUserSetting, setUserSetting, getUserSetting */

jQuery( function($) {

	var newCat, noSyncChecks = false, syncChecks, catAddAfter;

	$('#link_name').trigger( 'focus' );
	// Postboxes.
	postboxes.add_postbox_toggles('link');

	/**
	 * Adds event that opens a particular category tab.
	 *
	 * @ignore
	 *
	 * @return {boolean} Always returns false to prevent the default behavior.
	 */
	$('#category-tabs a').on( 'click keyup keydown', function( event ){
		var t = $(this).attr('href');
		if ( event.type === 'keydown' && event.key === ' ' ) {
			event.preventDefault();
		}
		if ( ( event.type === 'keyup' && event.key === ' ' ) || ( event.type === 'keydown' && event.key === 'Enter' ) || event.type === 'click' ) {
			event.preventDefault();
			$('#category-tabs a').removeAttr( 'aria-selected' ).attr( 'tabindex', '-1' );
			$(this).attr( 'aria-selected', 'true' ).removeAttr( 'tabindex' );
			$(this).parent().addClass('tabs').siblings('li').removeClass('tabs');
			$('.tabs-panel').hide();
			$(t).show();
			if ( '#categories-all' == t ) {
				deleteUserSetting('cats');
			} else {
				setUserSetting('cats','pop');
			}
			return false;
		}
		if ( event.type === 'keyup' && ( event.key === 'ArrowRight' || event.key === 'ArrowLeft' ) ) {
			$(this).attr( 'tabindex', '-1' );
			let next = $(this).parent('li').next();
			let prev = $(this).parent('li').prev();
			if ( next.length > 0 ) {
				next.find('a').removeAttr( 'tabindex');
				next.find('a').trigger( 'focus' );
			} else {
				prev.find('a').removeAttr( 'tabindex');
				prev.find('a').trigger( 'focus' );
			}
		}
	});
	if ( getUserSetting('cats') )
		$('#category-tabs a[href="#categories-pop"]').trigger( 'click' );

	// Ajax Cat.
	newCat = $('#newcat').one( 'focus', function() { $(this).val( '' ).removeClass( 'form-input-tip' ); } );

	/**
	 * After adding a new category, focus on the category add input field.
	 *
	 * @return {void}
	 */
	$('#link-category-add-submit').on( 'click', function() { newCat.focus(); } );

	/**
	 * Synchronize category checkboxes.
	 *
	 * This function makes sure that the checkboxes are synced between the all
	 * categories tab and the most used categories tab.
	 *
	 * @since 2.5.0
	 *
	 * @return {void}
	 */
	syncChecks = function() {
		if ( noSyncChecks )
			return;
		noSyncChecks = true;
		var th = $(this), c = th.is(':checked'), id = th.val().toString();
		$('#in-link-category-' + id + ', #in-popular-link_category-' + id).prop( 'checked', c );
		noSyncChecks = false;
	};

	/**
	 * Adds event listeners to an added category.
	 *
	 * This is run on the addAfter event to make sure the correct event listeners
	 * are bound to the DOM elements.
	 *
	 * @since 2.5.0
	 *
	 * @param {string} r Raw XML response returned from the server after adding a
	 *                   category.
	 * @param {Object} s List manager configuration object; settings for the Ajax
	 *                   request.
	 *
	 * @return {void}
	 */
	catAddAfter = function( r, s ) {
		$(s.what + ' response_data', r).each( function() {
			var t = $($(this).text());
			t.find( 'label' ).each( function() {
				var th = $(this),
					val = th.find('input').val(),
					id = th.find('input')[0].id,
					name = th.text().trim(),
					o;
				$('#' + id).on( 'change', syncChecks );
				o = $( '<option value="' +  parseInt( val, 10 ) + '"></option>' ).text( name );
			} );
		} );
	};

	/*
	 * Instantiates the list manager.
	 *
	 * @see js/_enqueues/lib/lists.js
	 */
	$('#categorychecklist').wpList( {
		// CSS class name for alternate styling.
		alt: '',

		// The type of list.
		what: 'link-category',

		// ID of the element the parsed Ajax response will be stored in.
		response: 'category-ajax-response',

		// Callback that's run after an item got added to the list.
		addAfter: catAddAfter
	} );

	// All categories is the default tab, so we delete the user setting.
	$('a[href="#categories-all"]').on( 'click', function(){deleteUserSetting('cats');});

	// Set a preference for the popular categories to cookies.
	$('a[href="#categories-pop"]').on( 'click', function(){setUserSetting('cats','pop');});

	if ( 'pop' == getUserSetting('cats') )
		$('a[href="#categories-pop"]').trigger( 'click' );

	/**
	 * Adds event handler that shows the interface controls to add a new category.
	 *
	 * @ignore
	 *
	 * @param {Event} event The event object.
	 * @return {boolean} Always returns false to prevent regular link
	 *                   functionality.
	 */
	$('#category-add-toggle').on( 'click', function() {
		$(this).parents('div:first').toggleClass( 'wp-hidden-children' );
		$('#category-tabs a[href="#categories-all"]').trigger( 'click' );
		$('#newcategory').trigger( 'focus' );
		return false;
	} );

	$('.categorychecklist :checkbox').on( 'change', syncChecks ).filter( ':checked' ).trigger( 'change' );
});
