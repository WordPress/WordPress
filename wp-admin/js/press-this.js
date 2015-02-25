/**
 * PressThis App
 *
 */
( function( $, window ) {
	var PressThis = function() {
		var editor,
			saveAlert             = false,
			$div                  = $( '<div>' ),
			siteConfig            = window.wpPressThisConfig || {},
			data                  = window.wpPressThisData || {},
			smallestWidth         = 128,
			interestingImages	  = getInterestingImages( data ) || [],
			interestingEmbeds	  = getInterestingEmbeds( data ) || [],
			hasEmptyTitleStr      = false,
			suggestedTitleStr     = getSuggestedTitle( data ),
			suggestedContentStr   = getSuggestedContent( data ),
			hasSetFocus           = false,
			catsCache             = [],
			transitionEndEvent    = ( function() {
				var style = document.documentElement.style;

				if ( typeof style.transition !== 'undefined' ) {
					return 'transitionend';
				}

				if ( typeof style.WebkitTransition !== 'undefined' ) {
					return 'webkitTransitionEnd';
				}

				return false;
			}() );

		/* ***************************************************************
		 * HELPER FUNCTIONS
		 *************************************************************** */

		/**
		 * Emulates our PHP __() gettext function, powered by the strings exported in pressThisL10n.
		 *
		 * @param key string Key of the string to be translated, as found in pressThisL10n.
		 * @returns string Original or translated string, or empty string if no key.
		 */
		function __( key ) {
			if ( key && window.pressThisL10n ) {
				return window.pressThisL10n[key] || key;
			}

			return key || '';
		}

		/**
		 * Strips HTML tags
		 *
		 * @param string string Text to have the HTML tags striped out of.
		 * @returns string Stripped text.
		 */
		function stripTags( string ) {
			string = string || '';

			return string
				.replace( /<!--[\s\S]*?(-->|$)/g, '' )
				.replace( /<(script|style)[^>]*>[\s\S]*?(<\/\1>|$)/ig, '' )
				.replace( /<\/?[a-z][^>]*>/ig, '' );
		}

		// TODO: needed?
		function entityEncode( text ) {
			return $div.text( text ).html();
		}

		/**
		 * Strip HTML tags and entity encode some of the HTML special chars.
		 *
		 * @param text string Text.
		 * @returns string Sanitized text.
		 */
		function sanitizeText( text ) {
			text = stripTags( text );

			return text
				.replace( /\\/, '' )
				.replace( /</g, '&lt;' )
				.replace( />/g, '&gt;' )
				.replace( /"/g, '&quot;' )
				.replace( /'/g, '&#039;' );
		}

		/**
		 * Allow only HTTP or protocol relative URLs.
		 *
		 * @param url string The URL.
		 * @returns string Processed URL.
		 */
		function checkUrl( url ) {
			url = $.trim( url || '' );

			if ( /^(?:https?:)?\/\//.test( url ) ) {
				url = stripTags( url );
				return url.replace( /["\\]+/g, '' );
			}

			return '';
		}

		/**
		 * Gets the source page's canonical link, based on passed location and meta data.
		 *
		 * @param data object Usually WpPressThis_App.data
		 * @returns string Discovered canonical URL, or empty
		 */
		function getCanonicalLink( data ) {
			if ( ! data || data.length ) {
				return '';
			}

			var link = '';

			if ( data._links ) {
				if ( data._links.canonical && data._links.canonical.length ) {
					link = data._links.canonical;
				}
			}

			if ( ! link.length && data.u ) {
				link = data.u;
			}

			if ( ! link.length && data._meta ) {
				if ( data._meta['twitter:url'] && data._meta['twitter:url'].length ) {
					link = data._meta['twitter:url'];
				} else if ( data._meta['og:url'] && data._meta['og:url'].length ) {
					link = data._meta['og:url'];
				}
			}

			return decodeURI( link );
		}

		/**
		 * Gets the source page's site name, based on passed meta data.
		 *
		 * @param data object Usually WpPressThis_App.data
		 * @returns string Discovered site name, or empty
		 */
		function getSourceSiteName( data ) {
			if ( ! data || data.length ) {
				return '';
			}

			var name='';

			if ( data._meta ) {
				if ( data._meta['og:site_name'] && data._meta['og:site_name'].length ) {
					name = data._meta['og:site_name'];
				} else if ( data._meta['application-name'] && data._meta['application-name'].length ) {
					name = data._meta['application-name'];
				}
			}

			return name.replace( /\\/g, '' );
		}

		/**
		 * Gets the source page's title, based on passed title and meta data.
		 *
		 * @param data object Usually WpPressThis_App.data
		 * @returns string Discovered page title, or empty
		 */
		function getSuggestedTitle( data ) {
			if ( ! data || data.length ) {
				return __( 'newPost' );
			}

			var title = '';

			if ( data.t ) {
				title = data.t;
			}

			if ( ! title.length && data._meta ) {
				if ( data._meta['twitter:title'] && data._meta['twitter:title'].length ) {
					title = data._meta['twitter:title'];
				} else if ( data._meta['og:title'] && data._meta['og:title'].length ) {
					title = data._meta['og:title'];
				} else if ( data._meta.title && data._meta.title.length ) {
					title = data._meta.title;
				}
			}

			if ( ! title.length ) {
				title = __( 'newPost' );
				hasEmptyTitleStr = true;
			}

			return title.replace( /\\/g, '' );
		}

		/**
		 * Gets the source page's suggested content, based on passed data (description, selection, etc).
		 * Features a blockquoted excerpt, as well as content attribution, if any.
		 *
		 * @param data object Usually WpPressThis_App.data
		 * @returns string Discovered content, or empty
		 */
		function getSuggestedContent( data ) {
			if ( ! data || data.length ) {
				return '';
			}

			var content   = '',
				title     = getSuggestedTitle( data ),
				url       = getCanonicalLink( data ),
				siteName  = getSourceSiteName( data );

			if ( data.s && data.s.length ) {
				content = data.s;
			} else if ( data._meta ) {
				if ( data._meta['twitter:description'] && data._meta['twitter:description'].length ) {
					content = data._meta['twitter:description'];
				} else if ( data._meta['og:description'] && data._meta['og:description'].length ) {
					content = data._meta['og:description'];
				} else if ( data._meta.description && data._meta.description.length ) {
					content = data._meta.description;
				}
			}

			// Wrap suggested content in blockquote tag, if we have any.
			content = ( content.length ? '<blockquote class="press-this-suggested-content">' + sanitizeText( content ) + '</blockquote>' : '' );

			// Add a source attribution if there is one available.
			if ( ( ( title.length && __( 'newPost' ) !== title ) || siteName.length ) && url.length ) {
				content += '<p class="press-this-suggested-source">';
				content += __( 'source' );
				content += ' <cite>';
				content += __( 'sourceLink').replace( '%1$s', encodeURI( url ) ).replace( '%2$s', sanitizeText( title || siteName ) );
				content += '</cite></p>';
			}

			if ( ! content ) {
				content = '';
			}

			return content.replace( /\\/g, '' );
		}

		/**
		 * Tests if what was passed as an embed URL is deemed to be embeddable in the editor.
		 *
		 * @param url string Passed URl, usually from WpPressThis_App.data._embed
		 * @returns boolean
		 */
		function isEmbeddable( url ) {
			if ( ! url ) {
				return false;
			} else if ( url.match( /\/\/(m\.|www\.)?youtube\.com\/watch\?/ ) || url.match( /\/youtu\.be\/.+$/ ) ) {
				return true;
			} else if ( url.match( /\/\/vimeo\.com\/(.+\/)?[\d]+$/ ) ) {
				return true;
			} else if ( url.match( /\/\/(www\.)?dailymotion\.com\/video\/.+$/ ) ) {
				return true;
			} else if ( url.match( /\/\/soundcloud\.com\/.+$/ ) ) {
				return true;
			} else if ( url.match( /\/\/twitter\.com\/[^\/]+\/status\/[\d]+$/ ) ) {
				return true;
			} else if ( url.match( /\/\/vine\.co\/v\/[^\/]+/ ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Tests if what was passed as an image URL is deemed to be interesting enough to offer to the user for selection.
		 *
		 * @param src string Passed URl, usually from WpPressThis_App.data._ing
		 * @returns boolean Test for false
		 */
		function isSrcUninterestingPath( src ) {
			if ( src.match( /\/ad[sx]{1}?\// ) ) {
				// Ads
				return true;
			} else if ( src.match( /(\/share-?this[^\.]+?\.[a-z0-9]{3,4})(\?.*)?$/ ) ) {
				// Share-this type button
				return true;
			} else if ( src.match( /\/(spinner|loading|spacer|blank|rss)\.(gif|jpg|png)/ ) ) {
				// Loaders, spinners, spacers
				return true;
			} else if ( src.match( /\/([^\.\/]+[-_]{1})?(spinner|loading|spacer|blank)s?([-_]{1}[^\.\/]+)?\.[a-z0-9]{3,4}/ ) ) {
				// Fancy loaders, spinners, spacers
				return true;
			} else if ( src.match( /([^\.\/]+[-_]{1})?thumb[^.]*\.(gif|jpg|png)$/ ) ) {
				// Thumbnails, too small, usually irrelevant to context
				return true;
			} else if ( src.match( /\/wp-includes\// ) ) {
				// Classic WP interface images
				return true;
			} else if ( src.match( /[^\d]{1}\d{1,2}x\d+\.(gif|jpg|png)$/ ) ) {
				// Most often tiny buttons/thumbs (< 100px wide)
				return true;
			} else if ( src.indexOf( '/g.gif' ) > -1 ) {
				// Classic WP stats gif
				return true;
			} else if ( src.indexOf( '/pixel.mathtag.com' ) > -1 ) {
				// See mathtag.com
				return true;
			}
			return false;
		}

		/**
		 * Get a list of valid embeds from what was passed via WpPressThis_App.data._embed on page load.
		 *
		 * @returns array
		 */
		function getInterestingEmbeds() {
			var embeds             = data._embed || [],
				interestingEmbeds  = [],
				alreadySelected    = [];

			if ( embeds.length ) {
				$.each( embeds, function ( i, src ) {
					if ( !src || !src.length ) {
						// Skip: no src value
						return;
					} else if ( !isEmbeddable( src ) ) {
						// Skip: not deemed embeddable
						return;
					}

					var schemelessSrc = src.replace( /^https?:/, '' );

					if ( $.inArray( schemelessSrc, alreadySelected ) > -1 ) {
						// Skip: already shown
						return;
					}

					interestingEmbeds.push( src );
					alreadySelected.push( schemelessSrc );
				} );
			}

			return interestingEmbeds;
		}

		/**
		 * Get what is likely the most valuable image from what was passed via WpPressThis_App.data._img and WpPressThis_App.data._meta on page load.
		 *
		 * @returns array
		 */
		function getFeaturedImage( data ) {
			var featured = '';

			if ( ! data || ! data._meta ) {
				return '';
			}

			if ( data._meta['twitter:image0:src'] && data._meta['twitter:image0:src'].length ) {
				featured = data._meta['twitter:image0:src'];
			} else if ( data._meta['twitter:image0'] && data._meta['twitter:image0'].length ) {
				featured = data._meta['twitter:image0'];
			} else if ( data._meta['twitter:image:src'] && data._meta['twitter:image:src'].length ) {
				featured = data._meta['twitter:image:src'];
			} else if ( data._meta['twitter:image'] && data._meta['twitter:image'].length ) {
				featured = data._meta['twitter:image'];
			} else if ( data._meta['og:image'] && data._meta['og:image'].length ) {
				featured = data._meta['og:image'];
			} else if ( data._meta['og:image:secure_url'] && data._meta['og:image:secure_url'].length ) {
				featured = data._meta['og:image:secure_url'];
			}

			featured = checkUrl( featured );

			return ( isSrcUninterestingPath( featured ) ) ? '' : featured;
		}

		/**
		 * Get a list of valid images from what was passed via WpPressThis_App.data._img and WpPressThis_App.data._meta on page load.
		 *
		 * @returns array
		 */
		function getInterestingImages( data ) {
			var imgs             = data._img || [],
				featuredPict     = getFeaturedImage( data ) || '',
				interestingImgs  = [],
				alreadySelected  = [];

			if ( featuredPict.length ) {
				interestingImgs.push( featuredPict );
				alreadySelected.push( featuredPict.replace(/^https?:/, '') );
			}

			if ( imgs.length ) {
				$.each( imgs, function ( i, src ) {
					src = src.replace( /http:\/\/[\d]+\.gravatar\.com\//, 'https://secure.gravatar.com/' );
					src = checkUrl( src );

					if ( ! src || ! src.length ) {
						// Skip: no src value
						return;
					}

					var schemelessSrc = src.replace( /^https?:/, '' );

					if ( Array.prototype.indexOf && alreadySelected.indexOf( schemelessSrc ) > -1 ) {
						// Skip: already shown
						return;
					} else if ( isSrcUninterestingPath( src ) ) {
						// Skip: spinner, stat, ad, or spacer pict
						return;
					} else if ( src.indexOf( 'avatar' ) > -1 && interestingImgs.length >= 15 ) {
						// Skip:  some type of avatar and we've already gathered more than 23 diff images to show
						return;
					}

					interestingImgs.push( src );
					alreadySelected.push( schemelessSrc );
				} );
			}

			return interestingImgs;
		}

		/**
		 * Show UX spinner
		 */
		function showSpinner() {
			$( '#spinner' ).addClass( 'show' );
			$( '.post-actions button' ).each( function() {
				$( this ).attr( 'disabled', 'disabled' );
			} );
		}

		/**
		 * Hide UX spinner
		 */
		function hideSpinner() {
			$( '#spinner' ).removeClass( 'show' );
			$( '.post-actions button' ).each( function() {
				$( this ).removeAttr( 'disabled' );
			} );
		}

		/**
		 * Submit the post form via AJAX, and redirect to the proper screen if published vs saved as a draft.
		 *
		 * @param action string publish|draft
		 */
		function submitPost( action ) {
			saveAlert = false;
			showSpinner();

			var $form = $( '#pressthis-form' );

			if ( 'publish' === action ) {
				$( '#post_status' ).val( 'publish' );
			}

			editor && editor.save();

			$( '#title-field' ).val( sanitizeText( $( '#title-container' ).text() ) );

			// Make sure to flush out the tags with tagBox before saving
			if ( window.tagBox ) {
				$( 'div.tagsdiv' ).each( function() {
					window.tagBox.flushTags( this, false, 1 );
				} );
			}

			var data = $form.serialize();

			$.ajax( {
				type: 'post',
				url: window.ajaxurl,
				data: data,
				success: function( response ) {
					if ( ! response.success ) {
						renderError( response.data.errorMessage );
						hideSpinner();
					} else if ( response.data.redirect ) {
						if ( window.opener && siteConfig.redir_in_parent ) {
							try {
								window.opener.location.href = response.data.redirect;
							} catch( er ) {}

							window.self.close();
						} else {
							window.location.href = response.data.redirect;
						}
					}
				}
			} );
		}

		/**
		 * Inserts the media a user has selected from the presented list inside the editor, as an image or embed, based on type
		 *
		 * @param type string img|embed
		 * @param src string Source URL
		 * @param link string Optional destination link, for images (defaults to src)
		 */
		function insertSelectedMedia( type, src, link ) {
			var newContent = '';

			if ( ! editor ) {
				return;
			}

			src = checkUrl( src );
			link = checkUrl( link );

			if ( 'img' === type ) {
				if ( ! link || ! link.length ) {
					link = src;
				}

				newContent = '<a href="' + link + '"><img class="alignnone size-full" src="' + src + '" /></a>\n';
			} else {
				newContent = '[embed]' + src + '[/embed]\n';
			}

			if ( ! hasSetFocus ) {
				// Append to top of content on 1st media insert
				editor.setContent( newContent + editor.getContent() );
			} else {
				// Or add where the cursor was last positioned in TinyMCE
				editor.execCommand( 'mceInsertContent', false, newContent );
			}

			hasSetFocus = true;
		}

		/**
		 * Adds the currently selected post format next to the option, in the options panel.
		 *
		 * @param format string Post format to be displayed
		 */
		function setPostFormatString( format ) {
			if ( ! format || ! siteConfig || ! siteConfig.post_formats || ! siteConfig.post_formats[ format ] ) {
				return;
			}
			$( '#post-option-post-format' ).text( siteConfig.post_formats[ format ] );
		}

		/**
		 * Save a new user-generated category via AJAX
		 */
		function saveNewCategory() {
			var data = {
				action: 'press-this-add-category',
				post_id: $( '#post_ID' ).val() || 0,
				name: $( '#new-category' ).val() || '',
				new_cat_nonce: $( '#_ajax_nonce-add-category' ).val() || '',
				parent: $( '#new-category-parent' ).val() || 0
			};

			$.post( window.ajaxurl, data, function( response ) {
				if ( ! response.success ) {
					renderError( response.data.errorMessage );
				} else {
					// TODO: change if/when the html changes.
					var $parent, $ul,
						$wrap = $( 'ul.categories-select' );

					$.each( response.data, function( i, newCat ) {
						var $node = $( '<li>' ).attr( 'id', 'category-' + newCat.term_id )
							.append( $( '<label class="selectit">' ).text( newCat.name )
								.append( $( '<input type="checkbox" name="post_category[]" checked>' ).attr( 'value', newCat.term_id ) ) );

						if ( newCat.parent ) {
							if ( ! $ul || ! $ul.length ) {
								$parent = $wrap.find( '#category-' + newCat.parent );
								$ul = $parent.find( 'ul.children:first' );

								if ( ! $ul.length ) {
									$ul = $( '<ul class="children">' ).appendTo( $parent );
								}
							}

							$ul.append( $node );
							// TODO: set focus on
						} else {
							$wrap.prepend( $node );
						}
					} );

					refreshCatsCache();
				}
			} );
		}

		/* ***************************************************************
		 * RENDERING FUNCTIONS
		 *************************************************************** */

		/**
		 * Hide the form letting users enter a URL to be scanned, if a URL was already passed.
		 */
		function renderToolsVisibility() {
			if ( data.u && data.u.match( /^https?:/ ) ) {
				$( '#scanbar' ).hide();
			}
		}

		/**
		 * Render error notice
		 *
		 * @param msg string Notice/error message
		 * @param error string error|notice CSS class for display
		 */
		function renderNotice( msg, error ) {
			var $alerts = $( '.editor-wrapper div.alerts' ),
				className = error ? 'is-error' : 'is-notice';

			$alerts.append( $( '<p class="' + className + '">' ).text( msg ) );
		}

		/**
		 * Render error notice
		 *
		 * @param msg string Error message
		 */
		function renderError( msg ) {
			renderNotice( msg, true );
		}

		/**
		 * Render notices on page load, if any already
		 */
		function renderStartupNotices() {
			// Render errors sent in the data, if any
			if ( data.errors && data.errors.length ) {
				$.each( data.errors, function( i, msg ) {
					renderError( msg );
				} );
			}

			// Prompt user to upgrade their bookmarklet if there is a version mismatch.
			if ( data.v && data._version && ( data.v + '' ) !== ( data._version + '' ) ) {
				$( '.should-upgrade-bookmarklet' ).removeClass( 'is-hidden' );
			}
		}

		/**
		 * Render the suggested title, if any
		 */
		function renderSuggestedTitle() {
			var suggestedTitle = suggestedTitleStr || '',
				$title = $( '#title-container' );

			if ( ! hasEmptyTitleStr ) {
				$( '#title-field' ).val( suggestedTitle );
				$title.text( suggestedTitle );
				$( '.post-title-placeholder' ).addClass( 'is-hidden' );
			}

			$title.on( 'keyup', function() {
				saveAlert = true;
			}).on( 'paste', function() {
				saveAlert = true;

				setTimeout( function() {
					$title.text( $title.text() );
				}, 100 );
			} );

		}

		/**
		 * Render the suggested content, if any
		 */
		function renderSuggestedContent() {
			if ( ! suggestedContentStr || ! suggestedContentStr.length ) {
				return;
			}

			if ( ! editor ) {
				editor = window.tinymce.get( 'pressthis' );
			}

			if ( editor ) {
				editor.setContent( suggestedContentStr );
				editor.on( 'focus', function() {
					hasSetFocus = true;
				} );
			}

		}

		/**
		 * Render the detected images and embed for selection, if any
		 */
		function renderDetectedMedia() {
			var mediaContainer = $( '#featured-media-container'),
				listContainer  = $( '#all-media-container' ),
				found          = 0;

			listContainer.empty();

			if ( ( interestingEmbeds && interestingEmbeds.length ) || ( interestingImages && interestingImages.length ) ) {
				listContainer.append( '<h2 class="screen-reader-text">' + __( 'allMediaHeading' ) + '</h2><ul class="wppt-all-media-list"/>' );
			}

			if ( interestingEmbeds && interestingEmbeds.length ) {
				$.each( interestingEmbeds, function ( i, src ) {
					src = checkUrl( src );

					if ( ! isEmbeddable( src ) ) {
						return;
					}

					var displaySrc = '',
						cssClass   = 'suggested-media-thumbnail suggested-media-embed';

					if ( src.indexOf( 'youtube.com/' ) > -1 ) {
						displaySrc = 'https://i.ytimg.com/vi/' + src.replace( /.+v=([^&]+).*/, '$1' ) + '/hqdefault.jpg';
						cssClass += ' is-video';
					} else if ( src.indexOf( 'youtu.be/' ) > -1 ) {
						displaySrc = 'https://i.ytimg.com/vi/' + src.replace( /\/([^\/])$/, '$1' ) + '/hqdefault.jpg';
						cssClass += ' is-video';
					} else if ( src.indexOf( 'dailymotion.com' ) > -1 ) {
						displaySrc = src.replace( '/video/', '/thumbnail/video/' );
						cssClass += ' is-video';
					} else if ( src.indexOf( 'soundcloud.com' ) > -1 ) {
						cssClass += ' is-audio';
					} else if ( src.indexOf( 'twitter.com' ) > -1 ) {
						cssClass += ' is-tweet';
					} else {
						cssClass += ' is-video';
					}

					$( '<li></li>', {
						'id': 'embed-' + i + '-container',
						'class': cssClass,
						'tabindex': '0'
					} ).css( {
						'background-image': ( displaySrc.length ) ? 'url(' + displaySrc + ')' : null
					} ).html(
						'<span class="screen-reader-text">' + __( 'suggestedEmbedAlt' ).replace( '%d', i + 1 ) + '</span>'
					).on( 'click keypress', function ( e ) {
						if ( e.type === 'click' || e.which === 13 ) {
							insertSelectedMedia( 'embed',src );
						}
					} ).appendTo( '.wppt-all-media-list', listContainer );

					found++;
				} );
			}

			if ( interestingImages && interestingImages.length ) {
				$.each( interestingImages, function ( i, src ) {
					src = checkUrl( src );

					var displaySrc = src.replace(/^(http[^\?]+)(\?.*)?$/, '$1');
					if ( src.indexOf( 'files.wordpress.com/' ) > -1 ) {
						displaySrc = displaySrc.replace(/\?.*$/, '') + '?w=' + smallestWidth;
					} else if ( src.indexOf( 'gravatar.com/' ) > -1 ) {
						displaySrc = displaySrc.replace( /\?.*$/, '' ) + '?s=' + smallestWidth;
					} else {
						displaySrc = src;
					}

					$( '<li></li>', {
						'id': 'img-' + i + '-container',
						'class': 'suggested-media-thumbnail is-image',
						'tabindex': '0'
					} ).css( {
						'background-image': 'url(' + displaySrc + ')'
					} ).html(
						'<span class="screen-reader-text">' +__( 'suggestedImgAlt' ).replace( '%d', i + 1 ) + '</span>'
					).on( 'click keypress', function ( e ) {
						if ( e.type === 'click' || e.which === 13 ) {
							insertSelectedMedia( 'img', src, data.u );
						}
					} ).appendTo( '.wppt-all-media-list', listContainer );

					found++;
				} );
			}

			if ( ! found ) {
				mediaContainer.removeClass( 'all-media-visible' ).addClass( 'no-media');
				return;
			}

			mediaContainer.removeClass( 'no-media' ).addClass( 'all-media-visible' );
		}

		/* ***************************************************************
		 * MONITORING FUNCTIONS
		 *************************************************************** */

		/**
		 * Interactive navigation behavior for the options modal (post format, tags, categories)
		 */
		function monitorOptionsModal() {
			var isOffScreen   = 'is-off-screen',
				isHidden      = 'is-hidden',
				$postOptions  = $( '.post-options' ),
				$postOption   = $( '.post-option' ),
				$settingModal = $( '.setting-modal' ),
				$modalClose   = $( '.modal-close' );

			$postOption.on( 'click', function( event ) {
				var index = $( this ).index(),
					$targetSettingModal = $settingModal.eq( index );

				event.preventDefault();

				$postOptions
					.addClass( isOffScreen )
					.one( transitionEndEvent, function() {
						$( this ).addClass( isHidden );
					} );

				$targetSettingModal
					.removeClass( isOffScreen + ' ' + isHidden )
					.one( transitionEndEvent, function() {
						$( this ).find( $modalClose ).focus();
					} );
			} );

			$modalClose.on( 'click', function( event ) {
				var $targetSettingModal = $( this ).parent(),
					index = $targetSettingModal.index();

				event.preventDefault();

				$postOptions
					.removeClass( isOffScreen + ' ' + isHidden );

				$targetSettingModal
					.addClass( isOffScreen )
					.one( transitionEndEvent, function() {
						$( this ).addClass( isHidden );
					} );

				// For browser that don't support transitionend.
				if ( ! transitionEndEvent ) {
					setTimeout( function() {
						$targetSettingModal.addClass( isHidden );
					}, 350 );
				}

				$postOption.eq( index - 1 ).focus();
			} );
		}

		/**
		 * Interactive behavior for the sidebar toggle, to show the options modals
		 */
		function monitorSidebarToggle() {
			var $optOpen  = $( '.options-open' ),
				$optClose = $( '.options-close' ),
				$postOption = $( '.post-option' ),
				$sidebar = $( '.options-panel' ),
				$postActions = $( '.press-this-actions' ),
				$scanbar = $( '#scanbar' ),
				isOffScreen = 'is-off-screen',
				isHidden = 'is-hidden',
				ifOffHidden = isOffScreen + ' ' + isHidden;

			$optOpen.on( 'click', function(){
				$optOpen.addClass( isHidden );
				$optClose.removeClass( isHidden );
				$postActions.addClass( isHidden );
				$scanbar.addClass( isHidden );

				$sidebar
					.removeClass( ifOffHidden )
					.one( 'transitionend', function() {
						$postOption.eq( 0 ).focus();
					} );
			} );

			$optClose.on( 'click', function(){
				$optClose.addClass( isHidden );
				$optOpen.removeClass( isHidden );
				$postActions.removeClass( isHidden );
				$scanbar.removeClass( isHidden );

				$sidebar
					.addClass( isOffScreen )
					.one( 'transitionend', function() {
						$( this ).addClass( isHidden );
						// Reset to options list
						$( '.post-options' ).removeClass( ifOffHidden );
						$( '.setting-modal').addClass( ifOffHidden );
					} );
			} );
		}

		/**
		 * Interactive behavior for the post title's field placeholder
		 */
		function monitorPlaceholder() {
			var $selector = $( '#title-container'),
				$placeholder = $('.post-title-placeholder');

			$selector.on( 'focus', function() {
				$placeholder.addClass('is-hidden');
			} );

			$selector.on( 'blur', function() {
				var textLength = $( this ).text().length;

				if ( ! textLength ) {
					$placeholder.removeClass('is-hidden');
				}
			} );
		}

		/* ***************************************************************
		 * PROCESSING FUNCTIONS
		 *************************************************************** */

		/**
		 * Calls all the rendring related functions to happen on page load
		 */
		function render(){
			// We're on!
			renderToolsVisibility();
			renderSuggestedTitle();
			renderDetectedMedia();
			$( document ).on( 'tinymce-editor-init', renderSuggestedContent );
			renderStartupNotices();
		}

		/**
		 * Set app events and other state monitoring related code.
		 */
		function monitor(){
			$( '#current-site a').click( function( e ) {
				e.preventDefault();
			} );

			// Publish and Draft buttons and submit

			$( '#draft-field' ).on( 'click', function() {
				submitPost( 'draft' );
			} );

			$( '#publish-field' ).on( 'click', function() {
				submitPost( 'publish' );
			} );

			monitorOptionsModal();
			monitorSidebarToggle();
			monitorPlaceholder();

			$( '#post-formats-select input' ).on( 'change', function() {
				var $this = $( this );

				if ( $this.is( ':checked' ) ) {
					setPostFormatString( $this.attr( 'id' ).replace( /^post-format-(.+)$/, '$1' ) );
				}
			} );

			// Needs more work, doesn't detect when the other JS changes the value of #tax-input-post_tag
			$( '#tax-input-post_tag' ).on( 'change', function() {
				var val =  $( this ).val();
				$( '#post-option-tags' ).text( ( val.length ) ? val.replace( /,([^\s])/g, ', $1' ) : '' );
			} );

			$( window ).on( 'beforeunload.press-this', function() {
				if ( saveAlert || ( editor && editor.isDirty() ) ) {
					return __( 'saveAlert' );
				}
			} );

			$( 'button.add-cat-toggle' ).on( 'click.press-this', function() {
				$( this ).toggleClass( 'is-toggled' );
				$( '.setting-modal .add-category' ).toggleClass( 'is-hidden' );
				$( '.categories-search-wrapper' ).toggleClass( 'is-hidden' );
			} );

			$( 'button.add-cat-submit' ).on( 'click.press-this', saveNewCategory );

			$( '.categories-search' ).on( 'keyup', function() {
				var search = $( this ).val().toLowerCase() || '';

				// Don't search when less thasn 3 extended ASCII chars
				if ( /[\x20-\xFF]+/.test( search ) && search.length < 2 ) {
					return;
				}

				$.each( catsCache, function( i, cat ) {
					cat.node.removeClass( 'is-hidden searched-parent' );
				} );

				if ( search ) {
					$.each( catsCache, function( i, cat ) {
						if ( cat.text.indexOf( search ) === -1 ) {
							cat.node.addClass( 'is-hidden' );
						} else {
							cat.parents.addClass( 'searched-parent' );
						}
					} );
				}
			} );

			return true;
		}

		function refreshCatsCache() {
			$( '.categories-select' ).find( 'li' ).each( function() {
				var $this = $( this );

				catsCache.push( {
					node: $this,
					parents: $this.parents( 'li' ),
					text: $this.children( 'label' ).text().toLowerCase()
				} );
			} );
		}

		// Let's go!
		$( document ).ready( function() {
			render();
			monitor();
			refreshCatsCache();
		});

		// Expose public methods
		// TODO: which are needed?
		return {
			renderNotice: renderNotice,
			renderError: renderError
		};
	}

	window.wp = window.wp || {};
	window.wp.pressThis = new PressThis();

}( jQuery, window ));
