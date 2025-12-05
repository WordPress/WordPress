jQuery( function ( $ ) {
	var mshotRemovalTimer = null;
	var mshotRetryTimer = null;
	var mshotTries = 0;
	var mshotRetryInterval = 1000;
	var mshotEnabledLinkSelector = 'a[id^="author_comment_url"], tr.pingback td.column-author a:first-of-type, td.comment p a';

	var preloadedMshotURLs = [];

	$('.akismet-status').each(function () {
		var thisId = $(this).attr('commentid');
		$(this).prependTo('#comment-' + thisId + ' .column-comment');
	});
	$('.akismet-user-comment-count').each(function () {
		var thisId = $(this).attr('commentid');
		$(this).insertAfter('#comment-' + thisId + ' .author strong:first').show();
	});

	akismet_enable_comment_author_url_removal();
	
	$( '#the-comment-list' ).on( 'click', '.akismet_remove_url', function () {
		var thisId = $(this).attr('commentid');
		var data = {
			action: 'comment_author_deurl',
			_wpnonce: WPAkismet.comment_author_url_nonce,
			id: thisId
		};
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: data,
			beforeSend: function () {
				// Removes "x" link
				$("a[commentid='"+ thisId +"']").hide();
				// Show temp status
				$("#author_comment_url_"+ thisId).html( $( '<span/>' ).text( WPAkismet.strings['Removing...'] ) );
			},
			success: function (response) {
				if (response) {
					// Show status/undo link
					$("#author_comment_url_"+ thisId)
						.attr('cid', thisId)
						.addClass('akismet_undo_link_removal')
						.html(
							$( '<span/>' ).text( WPAkismet.strings['URL removed'] )
						)
						.append( ' ' )
						.append(
							$( '<span/>' )
								.text( WPAkismet.strings['(undo)'] )
								.addClass( 'akismet-span-link' )
						);
				}
			}
		});

		return false;
	}).on( 'click', '.akismet_undo_link_removal', function () {
		var thisId = $(this).attr('cid');
		var thisUrl = $(this).attr('href');
		var data = {
			action: 'comment_author_reurl',
			_wpnonce: WPAkismet.comment_author_url_nonce,
			id: thisId,
			url: thisUrl
		};
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: data,
			beforeSend: function () {
				// Show temp status
				$("#author_comment_url_"+ thisId).html( $( '<span/>' ).text( WPAkismet.strings['Re-adding...'] ) );
			},
			success: function (response) {
				if (response) {
					// Add "x" link
					$("a[commentid='"+ thisId +"']").show();
					// Show link. Core strips leading http://, so let's do that too.
					$("#author_comment_url_"+ thisId).removeClass('akismet_undo_link_removal').text( thisUrl.replace( /^http:\/\/(www\.)?/ig, '' ) );
				}
			}
		});

		return false;
	});

	// Show a preview image of the hovered URL. Applies to author URLs and URLs inside the comments.
	if ( "enable_mshots" in WPAkismet && WPAkismet.enable_mshots ) {
		$( '#the-comment-list' ).on( 'mouseover', mshotEnabledLinkSelector, function () {
			clearTimeout( mshotRemovalTimer );

			if ( $( '.akismet-mshot' ).length > 0 ) {
				if ( $( '.akismet-mshot:first' ).data( 'link' ) == this ) {
					// The preview is already showing for this link.
					return;
				}
				else {
					// A new link is being hovered, so remove the old preview.
					$( '.akismet-mshot' ).remove();
				}
			}

			clearTimeout( mshotRetryTimer );

			var linkUrl = $( this ).attr( 'href' );

			if ( preloadedMshotURLs.indexOf( linkUrl ) !== -1 ) {
				// This preview image was already preloaded, so begin with a retry URL so the user doesn't see the placeholder image for the first second.
				mshotTries = 2;
			}
			else {
				mshotTries = 1;
			}

			var mShot = $( '<div class="akismet-mshot mshot-container"><div class="mshot-arrow"></div><img src="' + akismet_mshot_url( linkUrl, mshotTries ) + '" width="450" height="338" class="mshot-image" /></div>' );
			mShot.data( 'link', this );
			mShot.data( 'url', linkUrl );

			mShot.find( 'img' ).on( 'load', function () {
				$( '.akismet-mshot' ).data( 'pending-request', false );
			} );

			var offset = $( this ).offset();

			mShot.offset( {
				left : Math.min( $( window ).width() - 475, offset.left + $( this ).width() + 10 ), // Keep it on the screen if the link is near the edge of the window.
				top: offset.top + ( $( this ).height() / 2 ) - 101 // 101 = top offset of the arrow plus the top border thickness
			} );

			$( 'body' ).append( mShot );

			mshotRetryTimer = setTimeout( retryMshotUntilLoaded, mshotRetryInterval );
		} ).on( 'mouseout', 'a[id^="author_comment_url"], tr.pingback td.column-author a:first-of-type, td.comment p a', function () {
			mshotRemovalTimer = setTimeout( function () {
				clearTimeout( mshotRetryTimer );

				$( '.akismet-mshot' ).remove();
			}, 200 );
		} );

		var preloadDelayTimer = null;

		$( window ).on( 'scroll resize', function () {
			clearTimeout( preloadDelayTimer );

			preloadDelayTimer = setTimeout( preloadMshotsInViewport, 500 );
		} );

		preloadMshotsInViewport();
	}

	/**
	 * The way mShots works is if there was no screenshot already recently generated for the URL,
	 * it returns a "loading..." image for the first request. Then, some subsequent request will
	 * receive the actual screenshot, but it's unknown how long it will take. So, what we do here
	 * is continually re-request the mShot, waiting a second after every response until we get the
	 * actual screenshot.
	 */
	function retryMshotUntilLoaded() {
		clearTimeout( mshotRetryTimer );

		var imageWidth = $( '.akismet-mshot img' ).get(0).naturalWidth;

		if ( imageWidth == 0 ) {
			// It hasn't finished loading yet the first time. Check again shortly.
			setTimeout( retryMshotUntilLoaded, mshotRetryInterval );
		}
		else if ( imageWidth == 400 ) {
			// It loaded the preview image.

			if ( mshotTries == 20 ) {
				// Give up if we've requested the mShot 20 times already.
				return;
			}

			if ( ! $( '.akismet-mshot' ).data( 'pending-request' ) ) {
				$( '.akismet-mshot' ).data( 'pending-request', true );

				mshotTries++;

				$( '.akismet-mshot .mshot-image' ).attr( 'src', akismet_mshot_url( $( '.akismet-mshot' ).data( 'url' ), mshotTries ) );
			}

			mshotRetryTimer = setTimeout( retryMshotUntilLoaded, mshotRetryInterval );
		}
		else {
			// All done.
		}
	}
	
	function preloadMshotsInViewport() {
		var windowWidth = $( window ).width();
		var windowHeight = $( window ).height();

		$( '#the-comment-list' ).find( mshotEnabledLinkSelector ).each( function ( index, element ) {
			var linkUrl = $( this ).attr( 'href' );

			// Don't attempt to preload an mshot for a single link twice.
			if ( preloadedMshotURLs.indexOf( linkUrl ) !== -1 ) {
				// The URL is already preloaded.
				return true;
			}

			if ( typeof element.getBoundingClientRect !== 'function' ) {
				// The browser is too old. Return false to stop this preloading entirely.
				return false;
			}

			var rect = element.getBoundingClientRect();

			if ( rect.top >= 0 && rect.left >= 0 && rect.bottom <= windowHeight && rect.right <= windowWidth ) {
				akismet_preload_mshot( linkUrl );
				$( this ).data( 'akismet-mshot-preloaded', true );
			}
		} );
	}

	$( '.checkforspam.enable-on-load' ).on( 'click', function( e ) {
		if ( $( this ).hasClass( 'ajax-disabled' ) ) {
			// Akismet hasn't been configured yet. Allow the user to proceed to the button's link.
			return;
		}

		e.preventDefault();

		if ( $( this ).hasClass( 'button-disabled' ) ) {
			window.location.href = $( this ).data( 'success-url' ).replace( '__recheck_count__', 0 ).replace( '__spam_count__', 0 );
			return;
		}

		$('.checkforspam').addClass('button-disabled').addClass( 'checking' );
		$('.checkforspam-spinner').addClass( 'spinner' ).addClass( 'is-active' );

		akismet_check_for_spam(0, 100);
	}).removeClass( 'button-disabled' );

	var spam_count = 0;
	var recheck_count = 0;

	function akismet_check_for_spam(offset, limit) {
		var check_for_spam_buttons = $( '.checkforspam' );
		
		var nonce = check_for_spam_buttons.data( 'nonce' );
		
		// We show the percentage complete down to one decimal point so even queues with 100k
		// pending comments will show some progress pretty quickly.
		var percentage_complete = Math.round( ( recheck_count / check_for_spam_buttons.data( 'pending-comment-count' ) ) * 1000 ) / 10;
		
		// Update the progress counter on the "Check for Spam" button.
		$( '.checkforspam' ).text( check_for_spam_buttons.data( 'progress-label' ).replace( '%1$s', percentage_complete ) );

		$.post(
			ajaxurl,
			{
				'action': 'akismet_recheck_queue',
				'offset': offset,
				'limit': limit,
				'nonce': nonce
			},
			function(result) {
				if ( 'error' in result ) {
					// An error is only returned in the case of a missing nonce, so we don't need the actual error message.
					window.location.href = check_for_spam_buttons.data( 'failure-url' );
					return;
				}
				
				recheck_count += result.counts.processed;
				spam_count += result.counts.spam;
				
				if (result.counts.processed < limit) {
					window.location.href = check_for_spam_buttons.data( 'success-url' ).replace( '__recheck_count__', recheck_count ).replace( '__spam_count__', spam_count );
				}
				else {
					// Account for comments that were caught as spam and moved out of the queue.
					akismet_check_for_spam(offset + limit - result.counts.spam, limit);
				}
			}
		);
	}
	
	if ( "start_recheck" in WPAkismet && WPAkismet.start_recheck ) {
		$( '.checkforspam:first' ).click();
	}
	
	if ( typeof MutationObserver !== 'undefined' ) {
		// Dynamically add the "X" next the the author URL links when a comment is quick-edited.
		var comment_list_container = document.getElementById( 'the-comment-list' );

		if ( comment_list_container ) {
			var observer = new MutationObserver( function ( mutations ) {
				for ( var i = 0, _len = mutations.length; i < _len; i++ ) {
					if ( mutations[i].addedNodes.length > 0 ) {
						akismet_enable_comment_author_url_removal();
						
						// Once we know that we'll have to check for new author links, skip the rest of the mutations.
						break;
					}
				}
			} );
			
			observer.observe( comment_list_container, { attributes: true, childList: true, characterData: true } );
		}
	}

	function akismet_enable_comment_author_url_removal() {
		$( '#the-comment-list' )
			.find( 'tr.comment, tr[id ^= "comment-"]' )
			.find( '.column-author a[href^="http"]:first' ) // Ignore mailto: links, which would be the comment author's email.
			.each(function () {
				if ( $( this ).parent().find( '.akismet_remove_url' ).length > 0 ) {
					return;
				}
			
			var linkHref = $(this).attr( 'href' );
		
			// Ignore any links to the current domain, which are diagnostic tools, like the IP address link
			// or any other links another plugin might add.
			var currentHostParts = document.location.href.split( '/' );
			var currentHost = currentHostParts[0] + '//' + currentHostParts[2] + '/';
		
			if ( linkHref.indexOf( currentHost ) != 0 ) {
				var thisCommentId = $(this).parents('tr:first').attr('id').split("-");

				$(this)
					.attr("id", "author_comment_url_"+ thisCommentId[1])
					.after(
						$( '<a href="#" class="akismet_remove_url">x</a>' )
							.attr( 'commentid', thisCommentId[1] )
							.attr( 'title', WPAkismet.strings['Remove this URL'] )
					);
			}
		});
	}
	
	/**
	 * Generate an mShot URL if given a link URL.
	 *
	 * @param string linkUrl
	 * @param int retry If retrying a request, the number of the retry.
	 * @return string The mShot URL;
	 */
	function akismet_mshot_url( linkUrl, retry ) {
		var mshotUrl = '//s0.wp.com/mshots/v1/' + encodeURIComponent( linkUrl ) + '?w=900';

		if ( retry > 1 ) {
			mshotUrl += '&r=' + encodeURIComponent( retry );
		}

		mshotUrl += '&source=akismet';

		return mshotUrl;
	}
	
	/**
	 * Begin loading an mShot preview of a link.
	 *
	 * @param string linkUrl
	 */
	function akismet_preload_mshot( linkUrl ) {
		var img = new Image();
		img.src = akismet_mshot_url( linkUrl );
		
		preloadedMshotURLs.push( linkUrl );
	}

	$( '.akismet-could-be-primary' ).each( function () {
		var form = $( this ).closest( 'form' );

		form.data( 'initial-state', form.serialize() );

		form.on( 'change keyup', function () {
			var self = $( this );
			var submit_button = self.find( '.akismet-could-be-primary' );

			if ( self.serialize() != self.data( 'initial-state' ) ) {
				submit_button.addClass( 'akismet-is-primary' );
			}
			else {
				submit_button.removeClass( 'akismet-is-primary' );
			}
		} );
	} );

	/**
	 * Shows the Enter API key form
	 */
	$( '.akismet-enter-api-key-box__reveal' ).on( 'click', function ( e ) {
		e.preventDefault();

		var div = $( '.akismet-enter-api-key-box__form-wrapper' );
		div.show( 500 );
		div.find( 'input[name=key]' ).focus();

		$( this ).hide();
	} );

	/**
	 * Hides the Connect with Jetpack form | Shows the Activate Akismet Account form
	 */
	$( 'a.toggle-ak-connect' ).on( 'click', function ( e ) {
		e.preventDefault();

		$( '.akismet-ak-connect' ).slideToggle('slow');
		$( 'a.toggle-ak-connect' ).hide();
		$( '.akismet-jp-connect' ).hide();
		$( 'a.toggle-jp-connect' ).show();
	} );

	/**
	 * Shows the Connect with Jetpack form | Hides the Activate Akismet Account form
	 */
	$( 'a.toggle-jp-connect' ).on( 'click', function ( e ) {
		e.preventDefault();

		$( '.akismet-jp-connect' ).slideToggle('slow');
		$( 'a.toggle-jp-connect' ).hide();
		$( '.akismet-ak-connect' ).hide();
		$( 'a.toggle-ak-connect' ).show();
	} );
});
