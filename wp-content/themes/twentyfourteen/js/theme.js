( function( $ ) {

	$( document ).ready( function() {

		var $primaryNaviClone,
			$secondaryNaviClone,
			$masthead = $( '#masthead' ),
			$secondaryTop = $( '#secondary-top' ),
			$mobileNavigations = $( '#mobile-navigations'),
			$socialLinksWrapper = $( 'div.social-links-wrapper' ),
			$searchBoxWrapper = $( 'div.search-box-wrapper' ),
			$searchToggle = $( 'div.search-toggle' ),
			$socialLinksToggle = $( 'div.social-links-toggle' ),
			timeout = false;

		// Toggle function.
		function menuToggle() {
			$( 'span#nav-toggle' ).toggleClass( 'active' );
			$masthead.find( '#mobile-navigations' ).toggleClass( 'hide' );
		}

		// Click event for toggle the social links
		$socialLinksToggle.click( function() {
			$( this ).toggleClass( 'active' );
			$socialLinksWrapper.toggleClass( 'hide' );
			// if .search-box-wrapper is visible hide it
			if ( ! $searchBoxWrapper.hasClass( 'hide' ) ) {
				$searchBoxWrapper.addClass( 'hide' );
			}
			if ( $searchToggle.hasClass( 'active' ) ) {
				$searchToggle.removeClass( 'active' );
			}
		} );

		// Click event for toggle the search
		$searchToggle.click( function() {
			$( this ).toggleClass( 'active' );
			$searchBoxWrapper.toggleClass( 'hide' );
			// if .social-links-wrapper is visible hide it
			if ( ! $socialLinksWrapper.hasClass( 'hide' ) ) {
				$socialLinksWrapper.addClass( 'hide' );
			}
			if ( $socialLinksToggle.hasClass( 'active' ) ) {
				$socialLinksToggle.removeClass( 'active' );
			}
		} );

		// DOM manupilations for mobile header
		function mobileHeader()	{
			// Check if the toggler exists. If not add it.
			if ( ! $( '#nav-toggle' ).length )
			$( '<span id="nav-toggle" class="genericon" />' ).appendTo( $masthead );

			// Clone and detach the primary navigation for use later
			$primaryNaviClone = $masthead.find( 'nav.primary-navigation' ).detach();

			// Clone and detach the secondary navigation for use later
			$secondaryNaviClone = $secondaryTop.find( 'nav.secondary-navigation' ).detach();

			// Prepend the primary navigation clone to #mobile-navigations and remove the class and add an id
			$primaryNaviClone.prependTo( $mobileNavigations ).removeClass( 'primary-navigation' ).addClass( 'mobile-navigation' ).attr( 'id', 'primary-mobile-navigation' );

			// Append the secondary navigation clone to #mobile-navigations and remove the class and add an id
			$secondaryNaviClone.appendTo( $mobileNavigations ).removeClass( 'secondary-navigation' ).addClass( 'mobile-navigation' ).attr( 'id', 'secondary-mobile-navigation' );

			// Remove the click event first and bind it after to make sure it's invoked once.
			$( 'span#nav-toggle' ).off( 'click', menuToggle ).click( menuToggle );
		};

		// DOM manupilations for desktop header
		function normalHeader()	{
			// Check if the toggler exists. If it does remove it.
			if ( $( 'span#nav-toggle').length )
			$( 'span#nav-toggle' ).remove();

			// Clone and detach the primary mobile navigation for use later
			$primaryNaviClone = $mobileNavigations.find( '#primary-mobile-navigation' ).detach();

			// Clone and detach the secondary mobile navigation for use later
			$secondaryNaviClone = $mobileNavigations.find( '#secondary-mobile-navigation' ).detach();

			// Append the secondary navigation clone to #mobile-navigations and remove the class and add an id
			$primaryNaviClone.appendTo( '.header-main' ).removeClass( 'mobile-navigation' ).removeAttr( 'id' ).addClass( 'primary-navigation' );

			// Append the secondary navigation clone to #mobile-navigations and remove the class and add an id
			$secondaryNaviClone.appendTo( $secondaryTop ).removeClass( 'mobile-navigation' ).removeAttr( 'id' ).addClass( 'secondary-navigation' );
		};

		// Check viewport width when user resizes the browser window.
		$( window ).resize( function() {
			if ( false !== timeout )
				clearTimeout( timeout );

			timeout = setTimeout( function() {
				if ( $( window ).width() < 770 ) {
					mobileHeader();
				} else {
					normalHeader();
				}
			}, 100 );

		} ).resize();

		// Sticky header.
		var $mastheadOffset  = -1,
			$toolbarOffset = $( 'body' ).is( '.admin-bar' ) ? 32 : 0,
			$maindiv = $( '#main' );

		$( window ).on( 'scroll', false, function() {
			if ( $mastheadOffset < 0 )
				$mastheadOffset = $masthead.offset().top - $toolbarOffset;

			if ( ( window.scrollY > $mastheadOffset ) && ( $( window ).width() > 769 ) ) {
				$masthead.addClass( 'masthead-fixed' );
				$maindiv.css( {
					marginTop: $masthead.height()
				} );
			} else {
				$masthead.removeClass( 'masthead-fixed' );
				$maindiv.css( {
					marginTop: 0
				} );
			}
		} );

	} );

} )( jQuery );