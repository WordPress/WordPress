var addComment = {
	moveForm: function( commId, parentId, respondId, postId ) {
		var div, element, node, style, cssHidden,
			t           = this,
			comm        = t.I( commId ),
			respond     = t.I( respondId ),
			cancel      = t.I( 'cancel-comment-reply-link' ),
			parent      = t.I( 'comment_parent' ),
			post        = t.I( 'comment_post_ID' ),
			commentForm = respond.getElementsByTagName( 'form' )[0];

		if ( ! comm || ! respond || ! cancel || ! parent || ! commentForm ) {
			return;
		}

		t.respondId = respondId;
		postId = postId || false;

		if ( ! t.I( 'wp-temp-form-div' ) ) {
			div = document.createElement( 'div' );
			div.id = 'wp-temp-form-div';
			div.style.display = 'none';
			respond.parentNode.insertBefore( div, respond );
		}

		comm.parentNode.insertBefore( respond, comm.nextSibling );
		if ( post && postId ) {
			post.value = postId;
		}
		parent.value = parentId;
		cancel.style.display = '';

		cancel.onclick = function() {
			var t       = addComment,
				temp    = t.I( 'wp-temp-form-div' ),
				respond = t.I( t.respondId );

			if ( ! temp || ! respond ) {
				return;
			}

			t.I( 'comment_parent' ).value = '0';
			temp.parentNode.insertBefore( respond, temp );
			temp.parentNode.removeChild( temp );
			this.style.display = 'none';
			this.onclick = null;
			return false;
		};

		// Set initial focus to the first form focusable element.
		try {
			for ( var i = 0; i < commentForm.elements.length; i++ ) {
				element = commentForm.elements[i];

				// Skip form elements that are hidden or disabled.
				if ( 'hidden' === element.type || element.hasAttribute( 'disabled' ) ) {
					continue;
				}

				if ( 'getComputedStyle' in window ) {
					node = element;
					cssHidden = false;

					while( node.parentNode ) {
						style = window.getComputedStyle( node );

						if ( style.display === 'none' || style.visibility === 'hidden' ) {
							cssHidden = true;
							break;
						}

						node = node.parentNode;
					}

					if ( cssHidden ) {
						continue;
					}
				}

				element.focus();
				// Stop after the first focusable element.
				break;
			}
		} catch( er ) {}

		return false;
	},

	I: function( id ) {
		return document.getElementById( id );
	}
};
