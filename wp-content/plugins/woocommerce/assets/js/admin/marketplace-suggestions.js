/* global marketplace_suggestions, ajaxurl, Cookies */
( function( $, marketplace_suggestions, ajaxurl ) {
	$( function() {
		if ( 'undefined' === typeof marketplace_suggestions ) {
			return;
		}

		// Stand-in wcTracks.recordEvent in case tracks is not available (for any reason).
		window.wcTracks = window.wcTracks || {};
		window.wcTracks.recordEvent = window.wcTracks.recordEvent  || function() { };

		// Tracks events sent in this file:
		// - marketplace_suggestion_displayed
		// - marketplace_suggestion_clicked
		// - marketplace_suggestion_dismissed
		// All are prefixed by {WC_Tracks::PREFIX}.
		// All have one property for `suggestionSlug`, to identify the specific suggestion message.

		// Dismiss the specified suggestion from the UI, and save the dismissal in settings.
		function dismissSuggestion( context, product, promoted, url, suggestionSlug ) {
			// hide the suggestion in the UI
			var selector = '[data-suggestion-slug=' + suggestionSlug + ']';
			$( selector ).fadeOut( function() {
				$( this ).remove();
				tidyProductEditMetabox();
			} );

			// save dismissal in user settings
			jQuery.post(
				ajaxurl,
				{
					'action': 'woocommerce_add_dismissed_marketplace_suggestion',
					'_wpnonce': marketplace_suggestions.dismiss_suggestion_nonce,
					'slug': suggestionSlug
				}
			);

			// if this is a high-use area, delay new suggestion that area for a short while
			var highUseSuggestionContexts = [ 'products-list-inline' ];
			if ( _.contains( highUseSuggestionContexts, context ) ) {
				// snooze suggestions in that area for 2 days
				var contextSnoozeCookie = 'woocommerce_snooze_suggestions__' + context;
				Cookies.set( contextSnoozeCookie, 'true', { expires: 2 } );

				// keep track of how often this area gets dismissed in a cookie
				var contextDismissalCountCookie = 'woocommerce_dismissed_suggestions__' + context;
				var previousDismissalsInThisContext = parseInt( Cookies.get( contextDismissalCountCookie ), 10 ) || 0;
				Cookies.set( contextDismissalCountCookie, previousDismissalsInThisContext + 1, { expires: 31 } );
			}

			window.wcTracks.recordEvent( 'marketplace_suggestion_dismissed', {
				suggestion_slug: suggestionSlug,
				context: context,
				product: product || '',
				promoted: promoted || '',
				target: url || ''
			} );
		}

		// Render DOM element for suggestion dismiss button.
		function renderDismissButton( context, product, promoted, url, suggestionSlug ) {
			var dismissButton = document.createElement( 'a' );

			dismissButton.classList.add( 'suggestion-dismiss' );
			dismissButton.setAttribute( 'title', marketplace_suggestions.i18n_marketplace_suggestions_dismiss_tooltip );
			dismissButton.setAttribute( 'href', '#' );
			dismissButton.onclick = function( event ) {
				event.preventDefault();
				dismissSuggestion( context, product, promoted, url, suggestionSlug );
			};

			return dismissButton;
		}

		function addURLParameters( context, url ) {
			var urlParams = marketplace_suggestions.in_app_purchase_params;
			urlParams.utm_source = 'unknown';
			urlParams.utm_campaign = 'marketplacesuggestions';
			urlParams.utm_medium = 'product';

			var sourceContextMap = {
				'productstable': [
					'products-list-inline'
				],
				'productsempty': [
					'products-list-empty-header',
					'products-list-empty-footer',
					'products-list-empty-body'
				],
				'ordersempty': [
					'orders-list-empty-header',
					'orders-list-empty-footer',
					'orders-list-empty-body'
				],
				'editproduct': [
					'product-edit-meta-tab-header',
					'product-edit-meta-tab-footer',
					'product-edit-meta-tab-body'
				]
			};
			var utmSource = _.findKey( sourceContextMap, function( sourceInfo ) {
				return _.contains( sourceInfo, context );
			} );
			if ( utmSource ) {
				urlParams.utm_source = utmSource;
			}

			return url + '?' + jQuery.param( urlParams );
		}

		// Render DOM element for suggestion linkout, optionally with button style.
		function renderLinkout( context, product, promoted, slug, url, text, isButton ) {
			var linkoutButton = document.createElement( 'a' );

			var utmUrl = addURLParameters( context, url );
			linkoutButton.setAttribute( 'href', utmUrl );

			// By default, CTA links should open in same tab (and feel integrated with Woo).
			// Exception: when editing products, use new tab. User may have product edits
			// that need to be saved.
			var newTabContexts = [
				'product-edit-meta-tab-header',
				'product-edit-meta-tab-footer',
				'product-edit-meta-tab-body',
				'products-list-empty-footer'
			];
			if ( _.includes( newTabContexts, context ) ) {
				linkoutButton.setAttribute( 'target', 'blank' );
			}

			linkoutButton.textContent = text;

			linkoutButton.onclick = function() {
				window.wcTracks.recordEvent( 'marketplace_suggestion_clicked', {
					suggestion_slug: slug,
					context: context,
					product: product || '',
					promoted: promoted || '',
					target: url || ''
				} );
			};

			if ( isButton ) {
				linkoutButton.classList.add( 'button' );
			} else {
				linkoutButton.classList.add( 'linkout' );
				var linkoutIcon = document.createElement( 'span' );
				linkoutIcon.classList.add( 'dashicons', 'dashicons-external' );
				linkoutButton.appendChild( linkoutIcon );
			}

			return linkoutButton;
		}

		// Render DOM element for suggestion icon image.
		function renderSuggestionIcon( iconUrl ) {
			if ( ! iconUrl ) {
				return null;
			}

			var image = document.createElement( 'img' );
			image.src = iconUrl;
			image.classList.add( 'marketplace-suggestion-icon' );

			return image;
		}

		// Render DOM elements for suggestion content.
		function renderSuggestionContent( slug, title, copy ) {
			var container = document.createElement( 'div' );

			container.classList.add( 'marketplace-suggestion-container-content' );

			if ( title ) {
				var titleHeading = document.createElement( 'h4' );
				titleHeading.textContent = title;
				container.appendChild( titleHeading );
			}

			if ( copy ) {
				var body = document.createElement( 'p' );
				body.textContent = copy;
				container.appendChild( body );
			}

			// Conditionally add in a Manage suggestions link to product edit
			// metabox footer (based on suggestion slug).
			var slugsWithManage = [
				'product-edit-empty-footer-browse-all',
				'product-edit-meta-tab-footer-browse-all'
			];
			if ( -1 !== slugsWithManage.indexOf( slug ) ) {
				container.classList.add( 'has-manage-link' );

				var manageSuggestionsLink = document.createElement( 'a' );
				manageSuggestionsLink.classList.add( 'marketplace-suggestion-manage-link', 'linkout' );
				manageSuggestionsLink.setAttribute(
					'href',
					marketplace_suggestions.manage_suggestions_url
				);
				manageSuggestionsLink.textContent =  marketplace_suggestions.i18n_marketplace_suggestions_manage_suggestions;

				container.appendChild( manageSuggestionsLink );
			}

			return container;
		}

		// Render DOM elements for suggestion call-to-action – button or link with dismiss 'x'.
		function renderSuggestionCTA( context, product, promoted, slug, url, linkText, linkIsButton, allowDismiss ) {
			var container = document.createElement( 'div' );

			if ( ! linkText ) {
				linkText = marketplace_suggestions.i18n_marketplace_suggestions_default_cta;
			}

			container.classList.add( 'marketplace-suggestion-container-cta' );
			if ( url && linkText ) {
				var linkoutElement = renderLinkout( context, product, promoted, slug, url, linkText, linkIsButton );
				container.appendChild( linkoutElement );
			}

			if ( allowDismiss ) {
				container.appendChild( renderDismissButton( context, product, promoted, url, slug ) );
			}

			return container;
		}

		// Render a "list item" style suggestion.
		// These are used in onboarding style contexts, e.g. products list empty state.
		function renderListItem( context, product, promoted, slug, iconUrl, title, copy, url, linkText, linkIsButton, allowDismiss ) {
			var container = document.createElement( 'div' );
			container.classList.add( 'marketplace-suggestion-container' );
			container.dataset.suggestionSlug = slug;

			var icon = renderSuggestionIcon( iconUrl );
			if ( icon ) {
				container.appendChild( icon );
			}
			container.appendChild(
				renderSuggestionContent( slug, title, copy )
			);
			container.appendChild(
				renderSuggestionCTA( context, product, promoted, slug, url, linkText, linkIsButton, allowDismiss )
			);

			return container;
		}

		// Filter suggestion data to remove less-relevant suggestions.
		function getRelevantPromotions( marketplaceSuggestionsApiData, displayContext ) {
			// select based on display context
			var promos = _.filter( marketplaceSuggestionsApiData, function( promo ) {
				if ( _.isArray( promo.context ) ) {
					return _.contains( promo.context, displayContext );
				}
				return ( displayContext === promo.context );
			} );

			// hide promos the user has dismissed
			promos = _.filter( promos, function( promo ) {
				return ! _.contains( marketplace_suggestions.dismissed_suggestions, promo.slug );
			} );

			// hide promos for things the user already has installed
			promos = _.filter( promos, function( promo ) {
				return ! _.contains( marketplace_suggestions.active_plugins, promo.product );
			} );

			// hide promos that are not applicable based on user's installed extensions
			promos = _.filter( promos, function( promo ) {
				if ( ! promo['show-if-active'] ) {
					// this promotion is relevant to all
					return true;
				}

				// if the user has any of the prerequisites, show the promo
				return ( _.intersection( marketplace_suggestions.active_plugins, promo['show-if-active'] ).length > 0 );
			} );

			return promos;
		}

		// Show and hide page elements dependent on suggestion state.
		function hidePageElementsForSuggestionState( usedSuggestionsContexts ) {
			var showingEmptyStateSuggestions = _.intersection(
				usedSuggestionsContexts,
				[ 'products-list-empty-body', 'orders-list-empty-body' ]
			).length > 0;

			// Streamline onboarding UI if we're in 'empty state' welcome mode.
			if ( showingEmptyStateSuggestions ) {
				$( '#screen-meta-links' ).hide();
				$( '#wpfooter' ).hide();
			}

			// Hide the header & footer, they don't make sense without specific promotion content
			if ( ! showingEmptyStateSuggestions ) {
				$( '.marketplace-suggestions-container[data-marketplace-suggestions-context="products-list-empty-header"]' ).hide();
				$( '.marketplace-suggestions-container[data-marketplace-suggestions-context="products-list-empty-footer"]' ).hide();
				$( '.marketplace-suggestions-container[data-marketplace-suggestions-context="orders-list-empty-header"]' ).hide();
				$( '.marketplace-suggestions-container[data-marketplace-suggestions-context="orders-list-empty-footer"]' ).hide();
			}
		}

		// Streamline the product edit suggestions tab dependent on what's visible.
		function tidyProductEditMetabox() {
			var productMetaboxSuggestions = $(
				'.marketplace-suggestions-container[data-marketplace-suggestions-context="product-edit-meta-tab-body"]'
			).children();
			if ( 0 >= productMetaboxSuggestions.length ) {
				var metaboxSuggestionsUISelector =
					'.marketplace-suggestions-container[data-marketplace-suggestions-context="product-edit-meta-tab-body"]';
				metaboxSuggestionsUISelector +=
					', .marketplace-suggestions-container[data-marketplace-suggestions-context="product-edit-meta-tab-header"]';
				metaboxSuggestionsUISelector +=
					', .marketplace-suggestions-container[data-marketplace-suggestions-context="product-edit-meta-tab-footer"]';
				$( metaboxSuggestionsUISelector ).fadeOut( {
					complete: function() {
						$( '.marketplace-suggestions-metabox-nosuggestions-placeholder' ).fadeIn();
					}
				} );

			}
		}

		function addManageSuggestionsTracksHandler() {
			$( 'a.marketplace-suggestion-manage-link' ).on( 'click', function() {
				window.wcTracks.recordEvent( 'marketplace_suggestions_manage_clicked' );
			} );
		}

		function isContextHiddenOnPageLoad( context ) {
			// Some suggestions are not visible on page load;
			// e.g. the user reveals them by selecting a tab.
			var revealableSuggestionsContexts = [
				'product-edit-meta-tab-header',
				'product-edit-meta-tab-body',
				'product-edit-meta-tab-footer'
			];
			return _.includes( revealableSuggestionsContexts, context );
		}

		// track the current product data tab to avoid over-tracking suggestions
		var currentTab = false;

		// Render suggestion data in appropriate places in UI.
		function displaySuggestions( marketplaceSuggestionsApiData ) {
			var usedSuggestionsContexts = [];

			// iterate over all suggestions containers, rendering promos
			$( '.marketplace-suggestions-container' ).each( function() {
				// determine the context / placement we're populating
				var context = this.dataset.marketplaceSuggestionsContext;

				// find promotions that target this context
				var promos = getRelevantPromotions( marketplaceSuggestionsApiData, context );

				// shuffle/randomly select five suggestions to display
				var suggestionsToDisplay = _.sample( promos, 5 );

				// render the promo content
				for ( var i in suggestionsToDisplay ) {

					var linkText = suggestionsToDisplay[ i ]['link-text'];
					var linkoutIsButton = true;
					if ( suggestionsToDisplay[ i ]['link-text'] ) {
						linkText = suggestionsToDisplay[ i ]['link-text'];
						linkoutIsButton = false;
					}

					// dismiss is allowed by default
					var allowDismiss = true;
					if ( suggestionsToDisplay[ i ]['allow-dismiss'] === false ) {
						allowDismiss = false;
					}

					var content = renderListItem(
						context,
						suggestionsToDisplay[ i ].product,
						suggestionsToDisplay[ i ].promoted,
						suggestionsToDisplay[ i ].slug,
						suggestionsToDisplay[ i ].icon,
						suggestionsToDisplay[ i ].title,
						suggestionsToDisplay[ i ].copy,
						suggestionsToDisplay[ i ].url,
						linkText,
						linkoutIsButton,
						allowDismiss
					);
					$( this ).append( content );
					$( this ).addClass( 'showing-suggestion' );
					usedSuggestionsContexts.push( context );

					if ( ! isContextHiddenOnPageLoad( context ) ) {
						// Fire 'displayed' tracks events for immediately visible suggestions.
						window.wcTracks.recordEvent( 'marketplace_suggestion_displayed', {
							suggestion_slug: suggestionsToDisplay[ i ].slug,
							context: context,
							product: suggestionsToDisplay[ i ].product || '',
							promoted: suggestionsToDisplay[ i ].promoted || '',
							target: suggestionsToDisplay[ i ].url || ''
						} );
					}
				}

				// Track when suggestions are displayed (and not already visible).
				$( 'ul.product_data_tabs li.marketplace-suggestions_options a' ).on( 'click', function( e ) {
					e.preventDefault();

					if ( '#marketplace_suggestions' === currentTab ) {
						return;
					}

					if ( ! isContextHiddenOnPageLoad( context ) ) {
						// We've already fired 'displayed' event above.
						return;
					}

					for ( var i in suggestionsToDisplay ) {
						window.wcTracks.recordEvent( 'marketplace_suggestion_displayed', {
							suggestion_slug: suggestionsToDisplay[ i ].slug,
							context: context,
							product: suggestionsToDisplay[ i ].product || '',
							promoted: suggestionsToDisplay[ i ].promoted || '',
							target: suggestionsToDisplay[ i ].url || ''
						} );
					}
				} );
			} );

			hidePageElementsForSuggestionState( usedSuggestionsContexts );
			tidyProductEditMetabox();
		}

		if ( marketplace_suggestions.suggestions_data ) {
			displaySuggestions( marketplace_suggestions.suggestions_data );

			// track the current product data tab to avoid over-reporting suggestion views
			$( 'ul.product_data_tabs' ).on( 'click', 'li a', function( e ) {
				e.preventDefault();
				currentTab = $( this ).attr( 'href' );
			} );
		}

		addManageSuggestionsTracksHandler();
	});

})( jQuery, marketplace_suggestions, ajaxurl );
