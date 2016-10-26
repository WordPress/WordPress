/* global twentyseventeenScreenReaderText */
( function( $ ) {

	// Variables and DOM Caching
	var $body = $( 'body' ),
			$customHeader = $body.find( '.custom-header' ),
			$customHeaderImage = $customHeader.find( '.custom-header-image' ),
			$branding = $customHeader.find( '.site-branding' ),
			$navigation = $body.find( '.navigation-top' ),
			$navWrap = $navigation.find( '.wrap' ),
			$navMenuItem = $navigation.find( '.menu-item' ),
			$menuToggle = $navigation.find( '.menu-toggle' ),
			$menuScrollDown = $body.find( '.menu-scroll-down' ),
			$sidebar = $body.find( '#secondary' ),
			$entryContent = $body.find( '.entry-content' ),
			$formatQuote = $body.find( '.format-quote blockquote' ),
			isFrontPage = $body.hasClass( 'twentyseventeen-front-page' ) || $body.hasClass( 'home blog' ),
			navigationFixedClass = 'site-navigation-fixed',
			navigationHeight,
			navigationOuterHeight,
			navPadding,
			navMenuItemHeight,
			idealNavHeight,
			navIsNotTooTall,
			headerOffset,
			menuTop = 0,
			resizeTimer;

	/**
	 * Sets properties of navigation
	 */
	function setNavProps() {
		navigationHeight      = $navigation.height();
		navigationOuterHeight = $navigation.outerHeight();
		navPadding            = parseFloat( $navWrap.css( 'padding-top' ) ) * 2;
		navMenuItemHeight     = $navMenuItem.outerHeight() * 2;
		idealNavHeight        = navPadding + navMenuItemHeight;
		navIsNotTooTall       = navigationHeight <= idealNavHeight;
	}

	/**
	 * Makes navigation 'stick'
	 */
	function adjustScrollClass() {

		// Make sure we're not on a mobile screen
		if ( 'none' === $menuToggle.css( 'display' ) ) {

			// Make sure the nav isn't taller than two rows
			if ( navIsNotTooTall ) {

				// When there's a custom header image, the header offset includes the height of the navigation
				if ( isFrontPage && $customHeaderImage.length ) {
					headerOffset = $customHeader.innerHeight() - navigationOuterHeight;
				} else {
					headerOffset = $customHeader.innerHeight();
				}

				// If the scroll is more than the custom header, set the fixed class
				if ( $( window ).scrollTop() >= headerOffset ) {
					$navigation.addClass( navigationFixedClass );
				} else {
					$navigation.removeClass( navigationFixedClass );
				}

			} else {

				// Remove 'fixed' class if nav is taller than two rows
				$navigation.removeClass( navigationFixedClass );
			}
		}
	}

	/**
	 * Sets margins of branding in header
	 */
	function adjustHeaderHeight() {
		if ( 'none' === $menuToggle.css( 'display' ) ) {

			// The margin should be applied to different elements on front-page or home vs interior pages.
			if ( isFrontPage ) {
				$branding.css( 'margin-bottom', navigationOuterHeight );
			} else {
				$customHeader.css( 'margin-bottom', navigationOuterHeight );
			}

		} else {
			$customHeader.css( 'margin-bottom', '0' );
			$branding.css( 'margin-bottom', '0' );
		}
	}

	/**
	 * Sets icon for quotes
	 */
	function setQuotesIcon() {
		$( twentyseventeenScreenReaderText.quote ).prependTo( $formatQuote );
	}

	/**
	 * Add 'below-entry-meta' class to elements.
	 */
	function belowEntryMetaClass( param ) {
		var sidebarPos, sidebarPosBottom;

		if ( ! $body.hasClass( 'has-sidebar' ) || (
			$body.hasClass( 'search' ) ||
			$body.hasClass( 'single-attachment' ) ||
			$body.hasClass( 'error404' ) ||
			$body.hasClass( 'twentyseventeen-front-page' )
		) ) {
			return;
		}

		sidebarPos       = $sidebar.offset();
		sidebarPosBottom = sidebarPos.top + ( $sidebar.height() + 28 );

		$entryContent.find( param ).each( function() {
			var $element = $( this ),
					elementPos = $element.offset(),
					elementPosTop = elementPos.top;

			// Add 'below-entry-meta' to elements below the entry meta.
			if ( elementPosTop > sidebarPosBottom ) {
				$element.addClass( 'below-entry-meta' );
			} else {
				$element.removeClass( 'below-entry-meta' );
			}
		});
	}

	/**
     * Test if inline SVGs are supported.
     * @link https://github.com/Modernizr/Modernizr/
     */
	function supportsInlineSVG() {
		var div = document.createElement( 'div' );
		div.innerHTML = '<svg/>';
		return 'http://www.w3.org/2000/svg' === ( 'undefined' !== typeof SVGRect && div.firstChild && div.firstChild.namespaceURI );
	}

	// Fires on document ready
	$( document ).ready( function() {

		// Let's fire some JavaScript!
		setNavProps();

		if ( $menuScrollDown.length ) {

			/**
			 * 'Scroll Down' arrow in menu area
			 */
			if ( $( 'body' ).hasClass( 'admin-bar' ) ) {
				menuTop -= 32;
			}
			if ( $( 'body' ).hasClass( 'blog' ) ) {
				menuTop -= 30; // The div for latest posts has no space above content, add some to account for this
			}
			$menuScrollDown.click( function( e ) {
				e.preventDefault();
				$( window ).scrollTo( '#primary', {
					duration: 600,
					offset: { 'top': menuTop - navigationOuterHeight }
				} );
			} );

			adjustScrollClass();
		}

		adjustHeaderHeight();
		setQuotesIcon();
		supportsInlineSVG();
		if ( true === supportsInlineSVG() ) {
			document.documentElement.className = document.documentElement.className.replace( /(\s*)no-svg(\s*)/, '$1svg$2' );
		}
	} );

	if ( 'true' === twentyseventeenScreenReaderText.has_navigation ) {

		// On scroll, we want to stick/unstick the navigation
		$( window ).on( 'scroll', function() {
			adjustScrollClass();
			adjustHeaderHeight();
		} );

		// Also want to make sure the navigation is where it should be on resize
		$( window ).resize( function() {
			setNavProps();
			setTimeout( adjustScrollClass, 500 );
			setTimeout( adjustHeaderHeight, 1000 );
		} );
	}

	$( window ).resize( function() {
		clearTimeout( resizeTimer );
		resizeTimer = setTimeout( function() {
			belowEntryMetaClass( 'blockquote.alignleft, blockquote.alignright' );
		}, 300 );
	} );

}( jQuery ) );
