/*globals wp, _ */

/**
 * wp.media.view.Settings.AttachmentDisplay
 *
 * @class
 * @augments wp.media.view.Settings
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var Settings = require( '../settings.js' ),
	AttachmentDisplay;

AttachmentDisplay = Settings.extend({
	className: 'attachment-display-settings',
	template:  wp.template('attachment-display-settings'),

	initialize: function() {
		var attachment = this.options.attachment;

		_.defaults( this.options, {
			userSettings: false
		});
		// Call 'initialize' directly on the parent class.
		Settings.prototype.initialize.apply( this, arguments );
		this.listenTo( this.model, 'change:link', this.updateLinkTo );

		if ( attachment ) {
			attachment.on( 'change:uploading', this.render, this );
		}
	},

	dispose: function() {
		var attachment = this.options.attachment;
		if ( attachment ) {
			attachment.off( null, null, this );
		}
		/**
		 * call 'dispose' directly on the parent class
		 */
		Settings.prototype.dispose.apply( this, arguments );
	},
	/**
	 * @returns {wp.media.view.AttachmentDisplay} Returns itself to allow chaining
	 */
	render: function() {
		var attachment = this.options.attachment;
		if ( attachment ) {
			_.extend( this.options, {
				sizes: attachment.get('sizes'),
				type:  attachment.get('type')
			});
		}
		/**
		 * call 'render' directly on the parent class
		 */
		Settings.prototype.render.call( this );
		this.updateLinkTo();
		return this;
	},

	updateLinkTo: function() {
		var linkTo = this.model.get('link'),
			$input = this.$('.link-to-custom'),
			attachment = this.options.attachment;

		if ( 'none' === linkTo || 'embed' === linkTo || ( ! attachment && 'custom' !== linkTo ) ) {
			$input.addClass( 'hidden' );
			return;
		}

		if ( attachment ) {
			if ( 'post' === linkTo ) {
				$input.val( attachment.get('link') );
			} else if ( 'file' === linkTo ) {
				$input.val( attachment.get('url') );
			} else if ( ! this.model.get('linkUrl') ) {
				$input.val('http://');
			}

			$input.prop( 'readonly', 'custom' !== linkTo );
		}

		$input.removeClass( 'hidden' );

		// If the input is visible, focus and select its contents.
		if ( ! wp.media.isTouchDevice && $input.is(':visible') ) {
			$input.focus()[0].select();
		}
	}
});

module.exports = AttachmentDisplay;
