/**
 * @summary Handles the addition of the comment form.
 *
 * @since 2.7.0
 *
 * @namespace addComment
 *
 * @type {Object}
 */
var addComment;
addComment = ( function( window ) {
	// Avoid scope lookups on commonly used variables.
	var document = window.document;

	// Settings.
	var config = {
		commentReplyClass : 'comment-reply-link',
		cancelReplyId     : 'cancel-comment-reply-link',
		commentFormId     : 'commentform',
		temporaryFormId   : 'wp-temp-form-div',
		parentIdFieldId   : 'comment_parent',
		postIdFieldId     : 'comment_post_ID'
	};

	// Check browser cuts the mustard.
	var cutsTheMustard = 'querySelector' in document && 'addEventListener' in window;

	/*
	 * Check browser supports dataset.
	 * !! sets the variable to true if the property exists.
	 */
	var supportsDataset = !! document.body.dataset;

	// For holding the cancel element.
	var cancelElement;

	// For holding the comment form element.
	var commentFormElement;

	// The respond element.
	var respondElement;

	// Initialise the events.
	init();

	/**
	 * Add events to links classed .comment-reply-link.
	 *
	 * Searches the context for reply links and adds the JavaScript events
	 * required to move the comment form. To allow for lazy loading of
	 * comments this method is exposed as window.commentReply.init().
	 *
	 * @since 5.0.0
	 *
	 * @memberOf addComment
	 *
	 * @param {HTMLElement} context The parent DOM element to search for links.
	 */
	function init( context ) {
		if ( true !== cutsTheMustard ) {
			return;
		}

		// Get required elements.
		cancelElement = getElementById( config.cancelReplyId );
		commentFormElement = getElementById( config.commentFormId );

		// No cancel element, no replies.
		if ( ! cancelElement ) {
			return;
		}

		cancelElement.addEventListener( 'touchstart', cancelEvent );
		cancelElement.addEventListener( 'click',      cancelEvent );

		var links = replyLinks( context );
		var element;

		for ( var i = 0, l = links.length; i < l; i++ ) {
			element = links[i];

			element.addEventListener( 'touchstart', clickEvent );
			element.addEventListener( 'click',      clickEvent );
		}
	}

	/**
	 * Return all links classed .comment-reply-link.
	 *
	 * @since 5.0.0
	 *
	 * @param {HTMLElement} context The parent DOM element to search for links.
	 *
	 * @return {HTMLCollection|NodeList|Array}
	 */
	function replyLinks( context ) {
		var selectorClass = config.commentReplyClass;
		var allReplyLinks;

		// childNodes is a handy check to ensure the context is a HTMLElement.
		if ( ! context || ! context.childNodes ) {
			context = document;
		}

		if ( document.getElementsByClassName ) {
			// Fastest.
			allReplyLinks = context.getElementsByClassName( selectorClass );
		}
		else {
			// Fast.
			allReplyLinks = context.querySelectorAll( '.' + selectorClass );
		}

		return allReplyLinks;
	}

	/**
	 * Cancel event handler.
	 *
	 * @since 5.0.0
	 *
	 * @param {Event} event The calling event.
	 */
	function cancelEvent( event ) {
		var cancelLink = this;
		var temporaryFormId  = config.temporaryFormId;
		var temporaryElement = getElementById( temporaryFormId );

		if ( ! temporaryElement || ! respondElement ) {
			// Conditions for cancel link fail.
			return;
		}

		getElementById( config.parentIdFieldId ).value = '0';

		// Move the respond form back in place of the temporary element.
		temporaryElement.parentNode.replaceChild( respondElement ,temporaryElement );
		cancelLink.style.display = 'none';
		event.preventDefault();
	}

	/**
	 * Click event handler.
	 *
	 * @since 5.0.0
	 *
	 * @param {Event} event The calling event.
	 */
	function clickEvent( event ) {
		var replyLink = this,
			commId    = getDataAttribute( replyLink, 'belowelement'),
			parentId  = getDataAttribute( replyLink, 'commentid' ),
			respondId = getDataAttribute( replyLink, 'respondelement'),
			postId    = getDataAttribute( replyLink, 'postid'),
			follow;

		/*
		 * Third party comments systems can hook into this function via the global scope,
		 * therefore the click event needs to reference the global scope.
		 */
		follow = window.addComment.moveForm(commId, parentId, respondId, postId);
		if ( false === follow ) {
			event.preventDefault();
		}
	}

	/**
	 * Backward compatible getter of data-* attribute.
	 *
	 * Uses element.dataset if it exists, otherwise uses getAttribute.
	 *
	 * @since 5.0.0
	 *
	 * @param {HTMLElement} Element DOM element with the attribute.
	 * @param {String}      Attribute the attribute to get.
	 *
	 * @return {String}
	 */
	function getDataAttribute( element, attribute ) {
		if ( supportsDataset ) {
			return element.dataset[attribute];
		}
		else {
			return element.getAttribute( 'data-' + attribute );
		}
	}

	/**
	 * Get element by ID.
	 *
	 * Local alias for document.getElementById.
	 *
	 * @since 5.0.0
	 *
	 * @param {HTMLElement} The requested element.
	 */
	function getElementById( elementId ) {
		return document.getElementById( elementId );
	}

	/**
	 * Moves the reply form from its current position to the reply location.
	 *
	 * @since 2.7.0
	 *
	 * @memberOf addComment
	 *
	 * @param {String} addBelowId HTML ID of element the form follows.
	 * @param {String} commentId  Database ID of comment being replied to.
	 * @param {String} respondId  HTML ID of 'respond' element.
	 * @param {String} postId     Database ID of the post.
	 */
	function moveForm( addBelowId, commentId, respondId, postId ) {
		// Get elements based on their IDs.
		var addBelowElement = getElementById( addBelowId );
		respondElement  = getElementById( respondId );

		// Get the hidden fields.
		var parentIdField   = getElementById( config.parentIdFieldId );
		var postIdField     = getElementById( config.postIdFieldId );
		var element, cssHidden, style;

		if ( ! addBelowElement || ! respondElement || ! parentIdField ) {
			// Missing key elements, fail.
			return;
		}

		addPlaceHolder( respondElement );

		// Set the value of the post.
		if ( postId && postIdField ) {
			postIdField.value = postId;
		}

		parentIdField.value = commentId;

		cancelElement.style.display = '';
		addBelowElement.parentNode.insertBefore( respondElement, addBelowElement.nextSibling );

		/*
		 * This is for backward compatibility with third party commenting systems
		 * hooking into the event using older techniques.
		 */
		cancelElement.onclick = function(){
			return false;
		};

		// Focus on the first field in the comment form.
		try {
			for ( var i = 0; i < commentFormElement.elements.length; i++ ) {
				element = commentFormElement.elements[i];
				cssHidden = false;

				// Get elements computed style.
				if ( 'getComputedStyle' in window ) {
					// Modern browsers.
					style = window.getComputedStyle( element );
				} else if ( document.documentElement.currentStyle ) {
					// IE 8.
					style = element.currentStyle;
				}

				/*
				 * For display none, do the same thing jQuery does. For visibility,
				 * check the element computed style since browsers are already doing
				 * the job for us. In fact, the visibility computed style is the actual
				 * computed value and already takes into account the element ancestors.
				 */
				if ( ( element.offsetWidth <= 0 && element.offsetHeight <= 0 ) || style.visibility === 'hidden' ) {
					cssHidden = true;
				}

				// Skip form elements that are hidden or disabled.
				if ( 'hidden' === element.type || element.disabled || cssHidden ) {
					continue;
				}

				element.focus();
				// Stop after the first focusable element.
				break;
			}
		}
		catch(e) {

		}

		/*
		 * false is returned for backward compatibility with third party commenting systems
		 * hooking into this function.
		 */
		return false;
	}

	/**
	 * Add placeholder element.
	 *
	 * Places a place holder element above the #respond element for
	 * the form to be returned to if needs be.
	 *
	 * @since 2.7.0
	 *
	 * @param {HTMLelement} respondElement the #respond element holding comment form.
	 */
	function addPlaceHolder( respondElement ) {
		var temporaryFormId  = config.temporaryFormId;
		var temporaryElement = getElementById( temporaryFormId );

		if ( temporaryElement ) {
			// The element already exists, no need to recreate.
			return;
		}

		temporaryElement = document.createElement( 'div' );
		temporaryElement.id = temporaryFormId;
		temporaryElement.style.display = 'none';
		respondElement.parentNode.insertBefore( temporaryElement, respondElement );
	}

	return {
		init: init,
		moveForm: moveForm
	};
})( window );
