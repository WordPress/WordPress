/**
 * wp.media.view.Modal
 *
 * A modal view, which the media modal uses as its default container.
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var View = require( './view.js' ),
	FocusManager = require( './focus-manager.js' ),
	$ = jQuery,
	Modal;

Modal = View.extend({
	tagName:  'div',
	template: wp.template('media-modal'),

	attributes: {
		tabindex: 0
	},

	events: {
		'click .media-modal-backdrop, .media-modal-close': 'escapeHandler',
		'keydown': 'keydown'
	},

	initialize: function() {
		_.defaults( this.options, {
			container: document.body,
			title:     '',
			propagate: true,
			freeze:    true
		});

		this.focusManager = new FocusManager({
			el: this.el
		});
	},
	/**
	 * @returns {Object}
	 */
	prepare: function() {
		return {
			title: this.options.title
		};
	},

	/**
	 * @returns {wp.media.view.Modal} Returns itself to allow chaining
	 */
	attach: function() {
		if ( this.views.attached ) {
			return this;
		}

		if ( ! this.views.rendered ) {
			this.render();
		}

		this.$el.appendTo( this.options.container );

		// Manually mark the view as attached and trigger ready.
		this.views.attached = true;
		this.views.ready();

		return this.propagate('attach');
	},

	/**
	 * @returns {wp.media.view.Modal} Returns itself to allow chaining
	 */
	detach: function() {
		if ( this.$el.is(':visible') ) {
			this.close();
		}

		this.$el.detach();
		this.views.attached = false;
		return this.propagate('detach');
	},

	/**
	 * @returns {wp.media.view.Modal} Returns itself to allow chaining
	 */
	open: function() {
		var $el = this.$el,
			options = this.options,
			mceEditor;

		if ( $el.is(':visible') ) {
			return this;
		}

		if ( ! this.views.attached ) {
			this.attach();
		}

		// If the `freeze` option is set, record the window's scroll position.
		if ( options.freeze ) {
			this._freeze = {
				scrollTop: $( window ).scrollTop()
			};
		}

		// Disable page scrolling.
		$( 'body' ).addClass( 'modal-open' );

		$el.show();

		// Try to close the onscreen keyboard
		if ( 'ontouchend' in document ) {
			if ( ( mceEditor = window.tinymce && window.tinymce.activeEditor )  && ! mceEditor.isHidden() && mceEditor.iframeElement ) {
				mceEditor.iframeElement.focus();
				mceEditor.iframeElement.blur();

				setTimeout( function() {
					mceEditor.iframeElement.blur();
				}, 100 );
			}
		}

		this.$el.focus();

		return this.propagate('open');
	},

	/**
	 * @param {Object} options
	 * @returns {wp.media.view.Modal} Returns itself to allow chaining
	 */
	close: function( options ) {
		var freeze = this._freeze;

		if ( ! this.views.attached || ! this.$el.is(':visible') ) {
			return this;
		}

		// Enable page scrolling.
		$( 'body' ).removeClass( 'modal-open' );

		// Hide modal and remove restricted media modal tab focus once it's closed
		this.$el.hide().undelegate( 'keydown' );

		// Put focus back in useful location once modal is closed
		$('#wpbody-content').focus();

		this.propagate('close');

		// If the `freeze` option is set, restore the container's scroll position.
		if ( freeze ) {
			$( window ).scrollTop( freeze.scrollTop );
		}

		if ( options && options.escape ) {
			this.propagate('escape');
		}

		return this;
	},
	/**
	 * @returns {wp.media.view.Modal} Returns itself to allow chaining
	 */
	escape: function() {
		return this.close({ escape: true });
	},
	/**
	 * @param {Object} event
	 */
	escapeHandler: function( event ) {
		event.preventDefault();
		this.escape();
	},

	/**
	 * @param {Array|Object} content Views to register to '.media-modal-content'
	 * @returns {wp.media.view.Modal} Returns itself to allow chaining
	 */
	content: function( content ) {
		this.views.set( '.media-modal-content', content );
		return this;
	},

	/**
	 * Triggers a modal event and if the `propagate` option is set,
	 * forwards events to the modal's controller.
	 *
	 * @param {string} id
	 * @returns {wp.media.view.Modal} Returns itself to allow chaining
	 */
	propagate: function( id ) {
		this.trigger( id );

		if ( this.options.propagate ) {
			this.controller.trigger( id );
		}

		return this;
	},
	/**
	 * @param {Object} event
	 */
	keydown: function( event ) {
		// Close the modal when escape is pressed.
		if ( 27 === event.which && this.$el.is(':visible') ) {
			this.escape();
			event.stopImmediatePropagation();
		}
	}
});

module.exports = Modal;
