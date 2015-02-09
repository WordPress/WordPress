/**
 * wp.media.view.UploaderWindow
 *
 * An uploader window that allows for dragging and dropping media.
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 *
 * @param {object} [options]                   Options hash passed to the view.
 * @param {object} [options.uploader]          Uploader properties.
 * @param {jQuery} [options.uploader.browser]
 * @param {jQuery} [options.uploader.dropzone] jQuery collection of the dropzone.
 * @param {object} [options.uploader.params]
 */
var View = require( '../view.js' ),
	$ = jQuery,
	UploaderWindow;

UploaderWindow = View.extend({
	tagName:   'div',
	className: 'uploader-window',
	template:  wp.template('uploader-window'),

	initialize: function() {
		var uploader;

		this.$browser = $('<a href="#" class="browser" />').hide().appendTo('body');

		uploader = this.options.uploader = _.defaults( this.options.uploader || {}, {
			dropzone:  this.$el,
			browser:   this.$browser,
			params:    {}
		});

		// Ensure the dropzone is a jQuery collection.
		if ( uploader.dropzone && ! (uploader.dropzone instanceof $) ) {
			uploader.dropzone = $( uploader.dropzone );
		}

		this.controller.on( 'activate', this.refresh, this );

		this.controller.on( 'detach', function() {
			this.$browser.remove();
		}, this );
	},

	refresh: function() {
		if ( this.uploader ) {
			this.uploader.refresh();
		}
	},

	ready: function() {
		var postId = wp.media.view.settings.post.id,
			dropzone;

		// If the uploader already exists, bail.
		if ( this.uploader ) {
			return;
		}

		if ( postId ) {
			this.options.uploader.params.post_id = postId;
		}
		this.uploader = new wp.Uploader( this.options.uploader );

		dropzone = this.uploader.dropzone;
		dropzone.on( 'dropzone:enter', _.bind( this.show, this ) );
		dropzone.on( 'dropzone:leave', _.bind( this.hide, this ) );

		$( this.uploader ).on( 'uploader:ready', _.bind( this._ready, this ) );
	},

	_ready: function() {
		this.controller.trigger( 'uploader:ready' );
	},

	show: function() {
		var $el = this.$el.show();

		// Ensure that the animation is triggered by waiting until
		// the transparent element is painted into the DOM.
		_.defer( function() {
			$el.css({ opacity: 1 });
		});
	},

	hide: function() {
		var $el = this.$el.css({ opacity: 0 });

		wp.media.transition( $el ).done( function() {
			// Transition end events are subject to race conditions.
			// Make sure that the value is set as intended.
			if ( '0' === $el.css('opacity') ) {
				$el.hide();
			}
		});

		// https://core.trac.wordpress.org/ticket/27341
		_.delay( function() {
			if ( '0' === $el.css('opacity') && $el.is(':visible') ) {
				$el.hide();
			}
		}, 500 );
	}
});

module.exports = UploaderWindow;
