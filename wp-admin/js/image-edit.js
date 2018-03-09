/* global imageEditL10n, ajaxurl, confirm */
/**
 * The functions necessary for editing images.
 *
 * @since 2.9.0
 */

(function($) {

	/**
	 * Contains all the methods to initialise and control the image editor.
	 *
	 * @namespace imageEdit
	 */
	var imageEdit = window.imageEdit = {
	iasapi : {},
	hold : {},
	postid : '',
	_view : false,

	/**
	 * Handle crop tool clicks.
	 */
	handleCropToolClick: function( postid, nonce, cropButton ) {
		var img = $( '#image-preview-' + postid ),
			selection = this.iasapi.getSelection();

		// Ensure selection is available, otherwise reset to full image.
		if ( isNaN( selection.x1 ) ) {
			this.setCropSelection( postid, { 'x1': 0, 'y1': 0, 'x2': img.innerWidth(), 'y2': img.innerHeight(), 'width': img.innerWidth(), 'height': img.innerHeight() } );
			selection = this.iasapi.getSelection();
		}

		// If we don't already have a selection, select the entire image.
		if ( 0 === selection.x1 && 0 === selection.y1 && 0 === selection.x2 && 0 === selection.y2 ) {
			this.iasapi.setSelection( 0, 0, img.innerWidth(), img.innerHeight(), true );
			this.iasapi.setOptions( { show: true } );
			this.iasapi.update();
		} else {

			// Otherwise, perform the crop.
			imageEdit.crop( postid, nonce , cropButton );
		}
	},

	/**
	 * Converts a value to an integer.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {number} f The float value that should be converted.
	 *
	 * @return {number} The integer representation from the float value.
	 */
	intval : function(f) {
		/*
		 * Bitwise OR operator: one of the obscure ways to truncate floating point figures,
		 * worth reminding JavaScript doesn't have a distinct "integer" type.
		 */
		return f | 0;
	},

	/**
	 * Adds the disabled attribute and class to a single form element or a field set.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {jQuery}         el The element that should be modified.
	 * @param {bool|number}    s  The state for the element. If set to true
	 *                            the element is disabled,
	 *                            otherwise the element is enabled.
	 *                            The function is sometimes called with a 0 or 1
	 *                            instead of true or false.
	 *
	 * @returns {void}
	 */
	setDisabled : function( el, s ) {
		/*
		 * `el` can be a single form element or a fieldset. Before #28864, the disabled state on
		 * some text fields  was handled targeting $('input', el). Now we need to handle the
		 * disabled state on buttons too so we can just target `el` regardless if it's a single
		 * element or a fieldset because when a fieldset is disabled, its descendants are disabled too.
		 */
		if ( s ) {
			el.removeClass( 'disabled' ).prop( 'disabled', false );
		} else {
			el.addClass( 'disabled' ).prop( 'disabled', true );
		}
	},

	/**
	 * Initializes the image editor.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {number} postid The post id.
	 *
	 * @returns {void}
	 */
	init : function(postid) {
		var t = this, old = $('#image-editor-' + t.postid),
			x = t.intval( $('#imgedit-x-' + postid).val() ),
			y = t.intval( $('#imgedit-y-' + postid).val() );

		if ( t.postid !== postid && old.length ) {
			t.close(t.postid);
		}

		t.hold.w = t.hold.ow = x;
		t.hold.h = t.hold.oh = y;
		t.hold.xy_ratio = x / y;
		t.hold.sizer = parseFloat( $('#imgedit-sizer-' + postid).val() );
		t.postid = postid;
		$('#imgedit-response-' + postid).empty();

		$('input[type="text"]', '#imgedit-panel-' + postid).keypress(function(e) {
			var k = e.keyCode;

			// Key codes 37 thru 40 are the arrow keys.
			if ( 36 < k && k < 41 ) {
				$(this).blur();
			}

			// The key code 13 is the enter key.
			if ( 13 === k ) {
				e.preventDefault();
				e.stopPropagation();
				return false;
			}
		});
	},

	/**
	 * Toggles the wait/load icon in the editor.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {number} postid The post id.
	 * @param {number} toggle Is 0 or 1, fades the icon in then 1 and out when 0.
	 *
	 * @returns {void}
	 */
	toggleEditor : function(postid, toggle) {
		var wait = $('#imgedit-wait-' + postid);

		if ( toggle ) {
			wait.fadeIn( 'fast' );
		} else {
			wait.fadeOut('fast');
		}
	},

	/**
	 * Shows or hides the image edit help box.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {HTMLElement} el The element to create the help window in.
	 *
	 * @returns {boolean} Always returns false.
	 */
	toggleHelp : function(el) {
		var $el = $( el );
		$el
			.attr( 'aria-expanded', 'false' === $el.attr( 'aria-expanded' ) ? 'true' : 'false' )
			.parents( '.imgedit-group-top' ).toggleClass( 'imgedit-help-toggled' ).find( '.imgedit-help' ).slideToggle( 'fast' );

		return false;
	},

	/**
	 * Gets the value from the image edit target.
	 *
	 * The image edit target contains the image sizes where the (possible) changes
	 * have to be applied to.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {number} postid The post id.
	 *
	 * @returns {string} The value from the imagedit-save-target input field when available,
	 *                   or 'full' when not available.
	 */
	getTarget : function(postid) {
		return $('input[name="imgedit-target-' + postid + '"]:checked', '#imgedit-save-target-' + postid).val() || 'full';
	},

	/**
	 * Recalculates the height or width and keeps the original aspect ratio.
	 *
	 * If the original image size is exceeded a red exclamation mark is shown.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {number}         postid The current post id.
	 * @param {number}         x      Is 0 when it applies the y-axis
	 *                                and 1 when applicable for the x-axis.
	 * @param {jQuery}         el     Element.
	 *
	 * @returns {void}
	 */
	scaleChanged : function( postid, x, el ) {
		var w = $('#imgedit-scale-width-' + postid), h = $('#imgedit-scale-height-' + postid),
		warn = $('#imgedit-scale-warn-' + postid), w1 = '', h1 = '';

		if ( false === this.validateNumeric( el ) ) {
			return;
		}

		if ( x ) {
			h1 = ( w.val() !== '' ) ? Math.round( w.val() / this.hold.xy_ratio ) : '';
			h.val( h1 );
		} else {
			w1 = ( h.val() !== '' ) ? Math.round( h.val() * this.hold.xy_ratio ) : '';
			w.val( w1 );
		}

		if ( ( h1 && h1 > this.hold.oh ) || ( w1 && w1 > this.hold.ow ) ) {
			warn.css('visibility', 'visible');
		} else {
			warn.css('visibility', 'hidden');
		}
	},

	/**
	 * Gets the selected aspect ratio.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {number} postid The post id.
	 *
	 * @returns {string} The aspect ratio.
	 */
	getSelRatio : function(postid) {
		var x = this.hold.w, y = this.hold.h,
			X = this.intval( $('#imgedit-crop-width-' + postid).val() ),
			Y = this.intval( $('#imgedit-crop-height-' + postid).val() );

		if ( X && Y ) {
			return X + ':' + Y;
		}

		if ( x && y ) {
			return x + ':' + y;
		}

		return '1:1';
	},

	/**
	 * Removes the last action from the image edit history.
	 * The history consist of (edit) actions performed on the image.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {number} postid  The post id.
	 * @param {number} setSize 0 or 1, when 1 the image resets to its original size.
	 *
	 * @returns {string} JSON string containing the history or an empty string if no history exists.
	 */
	filterHistory : function(postid, setSize) {
		// Apply undo state to history.
		var history = $('#imgedit-history-' + postid).val(), pop, n, o, i, op = [];

		if ( history !== '' ) {
			// Read the JSON string with the image edit history.
			history = JSON.parse(history);
			pop = this.intval( $('#imgedit-undone-' + postid).val() );
			if ( pop > 0 ) {
				while ( pop > 0 ) {
					history.pop();
					pop--;
				}
			}

			// Reset size to it's original state.
			if ( setSize ) {
				if ( !history.length ) {
					this.hold.w = this.hold.ow;
					this.hold.h = this.hold.oh;
					return '';
				}

				// Restore original 'o'.
				o = history[history.length - 1];

				// c = 'crop', r = 'rotate', f = 'flip'
				o = o.c || o.r || o.f || false;

				if ( o ) {
					// fw = Full image width
					this.hold.w = o.fw;
					// fh = Full image height
					this.hold.h = o.fh;
				}
			}

			// Filter the last step/action from the history.
			for ( n in history ) {
				i = history[n];
				if ( i.hasOwnProperty('c') ) {
					op[n] = { 'c': { 'x': i.c.x, 'y': i.c.y, 'w': i.c.w, 'h': i.c.h } };
				} else if ( i.hasOwnProperty('r') ) {
					op[n] = { 'r': i.r.r };
				} else if ( i.hasOwnProperty('f') ) {
					op[n] = { 'f': i.f.f };
				}
			}
			return JSON.stringify(op);
		}
		return '';
	},
	/**
	 * Binds the necessary events to the image.
	 *
	 * When the image source is reloaded the image will be reloaded.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {number}   postid   The post id.
	 * @param {string}   nonce    The nonce to verify the request.
	 * @param {function} callback Function to execute when the image is loaded.
	 *
	 * @returns {void}
	 */
	refreshEditor : function(postid, nonce, callback) {
		var t = this, data, img;

		t.toggleEditor(postid, 1);
		data = {
			'action': 'imgedit-preview',
			'_ajax_nonce': nonce,
			'postid': postid,
			'history': t.filterHistory(postid, 1),
			'rand': t.intval(Math.random() * 1000000)
		};

		img = $( '<img id="image-preview-' + postid + '" alt="" />' )
			.on( 'load', { history: data.history }, function( event ) {
				var max1, max2,
					parent = $( '#imgedit-crop-' + postid ),
					t = imageEdit,
					historyObj;

				// Checks if there already is some image-edit history.
				if ( '' !== event.data.history ) {
					historyObj = JSON.parse( event.data.history );
					// If last executed action in history is a crop action.
					if ( historyObj[historyObj.length - 1].hasOwnProperty( 'c' ) ) {
						/*
						 * A crop action has completed and the crop button gets disabled
						 * ensure the undo button is enabled.
						 */
						t.setDisabled( $( '#image-undo-' + postid) , true );
						// Move focus to the undo button to avoid a focus loss.
						$( '#image-undo-' + postid ).focus();
					}
				}

				parent.empty().append(img);

				// w, h are the new full size dims
				max1 = Math.max( t.hold.w, t.hold.h );
				max2 = Math.max( $(img).width(), $(img).height() );
				t.hold.sizer = max1 > max2 ? max2 / max1 : 1;

				t.initCrop(postid, img, parent);

				if ( (typeof callback !== 'undefined') && callback !== null ) {
					callback();
				}

				if ( $('#imgedit-history-' + postid).val() && $('#imgedit-undone-' + postid).val() === '0' ) {
					$('input.imgedit-submit-btn', '#imgedit-panel-' + postid).removeAttr('disabled');
				} else {
					$('input.imgedit-submit-btn', '#imgedit-panel-' + postid).prop('disabled', true);
				}

				t.toggleEditor(postid, 0);
			})
			.on('error', function() {
				$('#imgedit-crop-' + postid).empty().append('<div class="error"><p>' + imageEditL10n.error + '</p></div>');
				t.toggleEditor(postid, 0);
			})
			.attr('src', ajaxurl + '?' + $.param(data));
	},
	/**
	 * Performs an image edit action.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param  {number}  postid The post id.
	 * @param  {string}  nonce  The nonce to verify the request.
	 * @param  {string}  action The action to perform on the image.
	 *                          The possible actions are: "scale" and "restore".
	 *
	 * @returns {boolean|void} Executes a post request that refreshes the page
	 *                         when the action is performed.
	 *                         Returns false if a invalid action is given,
	 *                         or when the action cannot be performed.
	 */
	action : function(postid, nonce, action) {
		var t = this, data, w, h, fw, fh;

		if ( t.notsaved(postid) ) {
			return false;
		}

		data = {
			'action': 'image-editor',
			'_ajax_nonce': nonce,
			'postid': postid
		};

		if ( 'scale' === action ) {
			w = $('#imgedit-scale-width-' + postid),
			h = $('#imgedit-scale-height-' + postid),
			fw = t.intval(w.val()),
			fh = t.intval(h.val());

			if ( fw < 1 ) {
				w.focus();
				return false;
			} else if ( fh < 1 ) {
				h.focus();
				return false;
			}

			if ( fw === t.hold.ow || fh === t.hold.oh ) {
				return false;
			}

			data['do'] = 'scale';
			data.fwidth = fw;
			data.fheight = fh;
		} else if ( 'restore' === action ) {
			data['do'] = 'restore';
		} else {
			return false;
		}

		t.toggleEditor(postid, 1);
		$.post(ajaxurl, data, function(r) {
			$('#image-editor-' + postid).empty().append(r);
			t.toggleEditor(postid, 0);
			// refresh the attachment model so that changes propagate
			if ( t._view ) {
				t._view.refresh();
			}
		});
	},

	/**
	 * Stores the changes that are made to the image.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {number}  postid   The post id to get the image from the database.
	 * @param {string}  nonce    The nonce to verify the request.
	 *
	 * @returns {boolean|void}  If the actions are successfully saved a response message is shown.
	 *                          Returns false if there is no image editing history,
	 *                          thus there are not edit-actions performed on the image.
	 */
	save : function(postid, nonce) {
		var data,
			target = this.getTarget(postid),
			history = this.filterHistory(postid, 0),
			self = this;

		if ( '' === history ) {
			return false;
		}

		this.toggleEditor(postid, 1);
		data = {
			'action': 'image-editor',
			'_ajax_nonce': nonce,
			'postid': postid,
			'history': history,
			'target': target,
			'context': $('#image-edit-context').length ? $('#image-edit-context').val() : null,
			'do': 'save'
		};
		// Post the image edit data to the backend.
		$.post(ajaxurl, data, function(r) {
			// Read the response.
			var ret = JSON.parse(r);

			// If a response is returned, close the editor and show an error.
			if ( ret.error ) {
				$('#imgedit-response-' + postid).html('<div class="error"><p>' + ret.error + '</p></div>');
				imageEdit.close(postid);
				return;
			}

			if ( ret.fw && ret.fh ) {
				$('#media-dims-' + postid).html( ret.fw + ' &times; ' + ret.fh );
			}

			if ( ret.thumbnail ) {
				$('.thumbnail', '#thumbnail-head-' + postid).attr('src', ''+ret.thumbnail);
			}

			if ( ret.msg ) {
				$('#imgedit-response-' + postid).html('<div class="updated"><p>' + ret.msg + '</p></div>');
			}

			if ( self._view ) {
				self._view.save();
			} else {
				imageEdit.close(postid);
			}
		});
	},

	/**
	 * Creates the image edit window.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {number} postid   The post id for the image.
	 * @param {string} nonce    The nonce to verify the request.
	 * @param {object} view     The image editor view to be used for the editing.
	 *
	 * @returns {void|promise} Either returns void if the button was already activated
	 *                         or returns an instance of the image editor, wrapped in a promise.
	 */
	open : function( postid, nonce, view ) {
		this._view = view;

		var dfd, data, elem = $('#image-editor-' + postid), head = $('#media-head-' + postid),
			btn = $('#imgedit-open-btn-' + postid), spin = btn.siblings('.spinner');

		/*
		 * Instead of disabling the button, which causes a focus loss and makes screen
		 * readers announce "unavailable", return if the button was already clicked.
		 */
		if ( btn.hasClass( 'button-activated' ) ) {
			return;
		}

		spin.addClass( 'is-active' );

		data = {
			'action': 'image-editor',
			'_ajax_nonce': nonce,
			'postid': postid,
			'do': 'open'
		};

		dfd = $.ajax({
			url:  ajaxurl,
			type: 'post',
			data: data,
			beforeSend: function() {
				btn.addClass( 'button-activated' );
			}
		}).done(function( html ) {
			elem.html( html );
			head.fadeOut('fast', function(){
				elem.fadeIn('fast');
				btn.removeClass( 'button-activated' );
				spin.removeClass( 'is-active' );
			});
			// Initialise the Image Editor now that everything is ready.
			imageEdit.init( postid );
		});

		return dfd;
	},

	/**
	 * Initializes the cropping tool and sets a default cropping selection.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {number} postid The post id.
	 *
	 * @returns {void}
	 */
	imgLoaded : function(postid) {
		var img = $('#image-preview-' + postid), parent = $('#imgedit-crop-' + postid);

		// Ensure init has run even when directly loaded.
		if ( 'undefined' === typeof this.hold.sizer ) {
			this.init( postid );
		}

		this.initCrop(postid, img, parent);
		this.setCropSelection( postid, { 'x1': 0, 'y1': 0, 'x2': 0, 'y2': 0, 'width': img.innerWidth(), 'height': img.innerHeight() } );

		this.toggleEditor(postid, 0);
		// Editor is ready, move focus to the first focusable element.
		$( '.imgedit-wrap .imgedit-help-toggle' ).eq( 0 ).focus();
	},

	/**
	 * Initializes the cropping tool.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {number}      postid The post id.
	 * @param {HTMLElement} image  The preview image.
	 * @param {HTMLElement} parent The preview image container.
	 *
	 * @returns {void}
	 */
	initCrop : function(postid, image, parent) {
		var t = this,
			selW = $('#imgedit-sel-width-' + postid),
			selH = $('#imgedit-sel-height-' + postid),
			$img;

		t.iasapi = $(image).imgAreaSelect({
			parent: parent,
			instance: true,
			handles: true,
			keys: true,
			minWidth: 3,
			minHeight: 3,

			/**
			 * Sets the CSS styles and binds events for locking the aspect ratio.
			 *
			 * @ignore
			 *
			 * @param {jQuery} img The preview image.
			 */
			onInit: function( img ) {
				// Ensure that the imgAreaSelect wrapper elements are position:absolute.
				// (even if we're in a position:fixed modal)
				$img = $( img );
				$img.next().css( 'position', 'absolute' )
					.nextAll( '.imgareaselect-outer' ).css( 'position', 'absolute' );
				/**
				 * Binds mouse down event to the cropping container.
				 *
				 * @returns {void}
				 */
				parent.children().on( 'mousedown, touchstart', function(e){
					var ratio = false, sel, defRatio;

					if ( e.shiftKey ) {
						sel = t.iasapi.getSelection();
						defRatio = t.getSelRatio(postid);
						ratio = ( sel && sel.width && sel.height ) ? sel.width + ':' + sel.height : defRatio;
					}

					t.iasapi.setOptions({
						aspectRatio: ratio
					});
				});
			},

			/**
			 * Event triggered when starting a selection.
			 *
			 * @ignore
			 *
			 * @returns {void}
			 */
			onSelectStart: function() {
				imageEdit.setDisabled($('#imgedit-crop-sel-' + postid), 1);
			},
			/**
			 * Event triggered when the selection is ended.
			 *
			 * @ignore
			 *
			 * @param {object} img jQuery object representing the image.
			 * @param {object} c   The selection.
			 *
			 * @returns {object}
			 */
			onSelectEnd: function(img, c) {
				imageEdit.setCropSelection(postid, c);
			},

			/**
			 * Event triggered when the selection changes.
			 *
			 * @ignore
			 *
			 * @param {object} img jQuery object representing the image.
			 * @param {object} c   The selection.
			 *
			 * @returns {void}
			 */
			onSelectChange: function(img, c) {
				var sizer = imageEdit.hold.sizer;
				selW.val( imageEdit.round(c.width / sizer) );
				selH.val( imageEdit.round(c.height / sizer) );
			}
		});
	},

	/**
	 * Stores the current crop selection.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {number} postid The post id.
	 * @param {object} c      The selection.
	 *
	 * @returns {boolean}
	 */
	setCropSelection : function(postid, c) {
		var sel;

		c = c || 0;

		if ( !c || ( c.width < 3 && c.height < 3 ) ) {
			this.setDisabled( $( '.imgedit-crop', '#imgedit-panel-' + postid ), 1 );
			this.setDisabled( $( '#imgedit-crop-sel-' + postid ), 1 );
			$('#imgedit-sel-width-' + postid).val('');
			$('#imgedit-sel-height-' + postid).val('');
			$('#imgedit-selection-' + postid).val('');
			return false;
		}

		sel = { 'x': c.x1, 'y': c.y1, 'w': c.width, 'h': c.height };
		this.setDisabled($('.imgedit-crop', '#imgedit-panel-' + postid), 1);
		$('#imgedit-selection-' + postid).val( JSON.stringify(sel) );
	},


	/**
	 * Closes the image editor.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {number}  postid The post id.
	 * @param {bool}    warn   Warning message.
	 *
	 * @returns {void|bool} Returns false if there is a warning.
	 */
	close : function(postid, warn) {
		warn = warn || false;

		if ( warn && this.notsaved(postid) ) {
			return false;
		}

		this.iasapi = {};
		this.hold = {};

		// If we've loaded the editor in the context of a Media Modal, then switch to the previous view,
		// whatever that might have been.
		if ( this._view ){
			this._view.back();
		}

		// In case we are not accessing the image editor in the context of a View, close the editor the old-skool way
		else {
			$('#image-editor-' + postid).fadeOut('fast', function() {
				$( '#media-head-' + postid ).fadeIn( 'fast', function() {
					// Move focus back to the Edit Image button. Runs also when saving.
					$( '#imgedit-open-btn-' + postid ).focus();
				});
				$(this).empty();
			});
		}


	},

	/**
	 * Checks if the image edit history is saved.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {number} postid The post id.
	 *
	 * @returns {boolean} Returns true if the history is not saved.
	 */
	notsaved : function(postid) {
		var h = $('#imgedit-history-' + postid).val(),
			history = ( h !== '' ) ? JSON.parse(h) : [],
			pop = this.intval( $('#imgedit-undone-' + postid).val() );

		if ( pop < history.length ) {
			if ( confirm( $('#imgedit-leaving-' + postid).html() ) ) {
				return false;
			}
			return true;
		}
		return false;
	},

	/**
	 * Adds an image edit action to the history.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {object} op     The original position.
	 * @param {number} postid The post id.
	 * @param {string} nonce  The nonce.
	 *
	 * @returns {void}
	 */
	addStep : function(op, postid, nonce) {
		var t = this, elem = $('#imgedit-history-' + postid),
			history = ( elem.val() !== '' ) ? JSON.parse( elem.val() ) : [],
			undone = $( '#imgedit-undone-' + postid ),
			pop = t.intval( undone.val() );

		while ( pop > 0 ) {
			history.pop();
			pop--;
		}
		undone.val(0); // reset

		history.push(op);
		elem.val( JSON.stringify(history) );

		t.refreshEditor(postid, nonce, function() {
			t.setDisabled($('#image-undo-' + postid), true);
			t.setDisabled($('#image-redo-' + postid), false);
		});
	},

	/**
	 * Rotates the image.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {string} angle  The angle the image is rotated with.
	 * @param {number} postid The post id.
	 * @param {string} nonce  The nonce.
	 * @param {object} t      The target element.
	 *
	 * @returns {boolean}
	 */
	rotate : function(angle, postid, nonce, t) {
		if ( $(t).hasClass('disabled') ) {
			return false;
		}

		this.addStep({ 'r': { 'r': angle, 'fw': this.hold.h, 'fh': this.hold.w }}, postid, nonce);
	},

	/**
	 * Flips the image.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {number} axis   The axle the image is flipped on.
	 * @param {number} postid The post id.
	 * @param {string} nonce  The nonce.
	 * @param {object} t      The target element.
	 *
	 * @returns {boolean}
	 */
	flip : function (axis, postid, nonce, t) {
		if ( $(t).hasClass('disabled') ) {
			return false;
		}

		this.addStep({ 'f': { 'f': axis, 'fw': this.hold.w, 'fh': this.hold.h }}, postid, nonce);
	},

	/**
	 * Crops the image.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {number} postid The post id.
	 * @param {string} nonce  The nonce.
	 * @param {object} t      The target object.
	 *
	 * @returns {void|boolean} Returns false if the crop button is disabled.
	 */
	crop : function (postid, nonce, t) {
		var sel = $('#imgedit-selection-' + postid).val(),
			w = this.intval( $('#imgedit-sel-width-' + postid).val() ),
			h = this.intval( $('#imgedit-sel-height-' + postid).val() );

		if ( $(t).hasClass('disabled') || sel === '' ) {
			return false;
		}

		sel = JSON.parse(sel);
		if ( sel.w > 0 && sel.h > 0 && w > 0 && h > 0 ) {
			sel.fw = w;
			sel.fh = h;
			this.addStep({ 'c': sel }, postid, nonce);
		}
	},

	/**
	 * Undoes an image edit action.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {number} postid   The post id.
	 * @param {string} nonce    The nonce.
	 *
	 * @returns {void|false} Returns false if the undo button is disabled.
	 */
	undo : function (postid, nonce) {
		var t = this, button = $('#image-undo-' + postid), elem = $('#imgedit-undone-' + postid),
			pop = t.intval( elem.val() ) + 1;

		if ( button.hasClass('disabled') ) {
			return;
		}

		elem.val(pop);
		t.refreshEditor(postid, nonce, function() {
			var elem = $('#imgedit-history-' + postid),
				history = ( elem.val() !== '' ) ? JSON.parse( elem.val() ) : [];

			t.setDisabled($('#image-redo-' + postid), true);
			t.setDisabled(button, pop < history.length);
			// When undo gets disabled, move focus to the redo button to avoid a focus loss.
			if ( history.length === pop ) {
				$( '#image-redo-' + postid ).focus();
			}
		});
	},

	/**
	 * Reverts a undo action.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {number} postid The post id.
	 * @param {string} nonce  The nonce.
	 *
	 * @returns {void}
	 */
	redo : function(postid, nonce) {
		var t = this, button = $('#image-redo-' + postid), elem = $('#imgedit-undone-' + postid),
			pop = t.intval( elem.val() ) - 1;

		if ( button.hasClass('disabled') ) {
			return;
		}

		elem.val(pop);
		t.refreshEditor(postid, nonce, function() {
			t.setDisabled($('#image-undo-' + postid), true);
			t.setDisabled(button, pop > 0);
			// When redo gets disabled, move focus to the undo button to avoid a focus loss.
			if ( 0 === pop ) {
				$( '#image-undo-' + postid ).focus();
			}
		});
	},

	/**
	 * Sets the selection for the height and width in pixels.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {number} postid The post id.
	 * @param {jQuery} el     The element containing the values.
	 *
	 * @returns {void|boolean} Returns false when the x or y value is lower than 1,
	 *                         void when the value is not numeric or when the operation
	 *                         is successful.
	 */
	setNumSelection : function( postid, el ) {
		var sel, elX = $('#imgedit-sel-width-' + postid), elY = $('#imgedit-sel-height-' + postid),
			x = this.intval( elX.val() ), y = this.intval( elY.val() ),
			img = $('#image-preview-' + postid), imgh = img.height(), imgw = img.width(),
			sizer = this.hold.sizer, x1, y1, x2, y2, ias = this.iasapi;

		if ( false === this.validateNumeric( el ) ) {
			return;
		}

		if ( x < 1 ) {
			elX.val('');
			return false;
		}

		if ( y < 1 ) {
			elY.val('');
			return false;
		}

		if ( x && y && ( sel = ias.getSelection() ) ) {
			x2 = sel.x1 + Math.round( x * sizer );
			y2 = sel.y1 + Math.round( y * sizer );
			x1 = sel.x1;
			y1 = sel.y1;

			if ( x2 > imgw ) {
				x1 = 0;
				x2 = imgw;
				elX.val( Math.round( x2 / sizer ) );
			}

			if ( y2 > imgh ) {
				y1 = 0;
				y2 = imgh;
				elY.val( Math.round( y2 / sizer ) );
			}

			ias.setSelection( x1, y1, x2, y2 );
			ias.update();
			this.setCropSelection(postid, ias.getSelection());
		}
	},

	/**
	 * Rounds a number to a whole.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {number} num The number.
	 *
	 * @returns {number} The number rounded to a whole number.
	 */
	round : function(num) {
		var s;
		num = Math.round(num);

		if ( this.hold.sizer > 0.6 ) {
			return num;
		}

		s = num.toString().slice(-1);

		if ( '1' === s ) {
			return num - 1;
		} else if ( '9' === s ) {
			return num + 1;
		}

		return num;
	},

	/**
	 * Sets a locked aspect ratio for the selection.
	 *
	 * @memberof imageEdit
	 * @since    2.9.0
	 *
	 * @param {number} postid     The post id.
	 * @param {number} n          The ratio to set.
	 * @param {jQuery} el         The element containing the values.
	 *
	 * @returns {void}
	 */
	setRatioSelection : function(postid, n, el) {
		var sel, r, x = this.intval( $('#imgedit-crop-width-' + postid).val() ),
			y = this.intval( $('#imgedit-crop-height-' + postid).val() ),
			h = $('#image-preview-' + postid).height();

		if ( false === this.validateNumeric( el ) ) {
			return;
		}

		if ( x && y ) {
			this.iasapi.setOptions({
				aspectRatio: x + ':' + y
			});

			if ( sel = this.iasapi.getSelection(true) ) {
				r = Math.ceil( sel.y1 + ( ( sel.x2 - sel.x1 ) / ( x / y ) ) );

				if ( r > h ) {
					r = h;
					if ( n ) {
						$('#imgedit-crop-height-' + postid).val('');
					} else {
						$('#imgedit-crop-width-' + postid).val('');
					}
				}

				this.iasapi.setSelection( sel.x1, sel.y1, sel.x2, r );
				this.iasapi.update();
			}
		}
	},

	/**
	 * Validates if a value in a jQuery.HTMLElement is numeric.
	 *
	 * @memberof imageEdit
	 * @since    4.6
	 *
	 * @param {jQuery} el The html element.
	 *
	 * @returns {void|boolean} Returns false if the value is not numeric,
	 *                         void when it is.
	 */
	validateNumeric: function( el ) {
		if ( ! this.intval( $( el ).val() ) ) {
			$( el ).val( '' );
			return false;
		}
	}
};
})(jQuery);
