/**
 * wp.media.view.ImageDetails
 *
 * @class
 * @augments wp.media.view.Settings.AttachmentDisplay
 * @augments wp.media.view.Settings
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var AttachmentDisplay = require( './settings/attachment-display.js' ),
	$ = jQuery,
	ImageDetails;

ImageDetails = AttachmentDisplay.extend({
	className: 'image-details',
	template:  wp.template('image-details'),
	events: _.defaults( AttachmentDisplay.prototype.events, {
		'click .edit-attachment': 'editAttachment',
		'click .replace-attachment': 'replaceAttachment',
		'click .advanced-toggle': 'onToggleAdvanced',
		'change [data-setting="customWidth"]': 'onCustomSize',
		'change [data-setting="customHeight"]': 'onCustomSize',
		'keyup [data-setting="customWidth"]': 'onCustomSize',
		'keyup [data-setting="customHeight"]': 'onCustomSize'
	} ),
	initialize: function() {
		// used in AttachmentDisplay.prototype.updateLinkTo
		this.options.attachment = this.model.attachment;
		this.listenTo( this.model, 'change:url', this.updateUrl );
		this.listenTo( this.model, 'change:link', this.toggleLinkSettings );
		this.listenTo( this.model, 'change:size', this.toggleCustomSize );

		AttachmentDisplay.prototype.initialize.apply( this, arguments );
	},

	prepare: function() {
		var attachment = false;

		if ( this.model.attachment ) {
			attachment = this.model.attachment.toJSON();
		}
		return _.defaults({
			model: this.model.toJSON(),
			attachment: attachment
		}, this.options );
	},

	render: function() {
		var args = arguments;

		if ( this.model.attachment && 'pending' === this.model.dfd.state() ) {
			this.model.dfd
				.done( _.bind( function() {
					AttachmentDisplay.prototype.render.apply( this, args );
					this.postRender();
				}, this ) )
				.fail( _.bind( function() {
					this.model.attachment = false;
					AttachmentDisplay.prototype.render.apply( this, args );
					this.postRender();
				}, this ) );
		} else {
			AttachmentDisplay.prototype.render.apply( this, arguments );
			this.postRender();
		}

		return this;
	},

	postRender: function() {
		setTimeout( _.bind( this.resetFocus, this ), 10 );
		this.toggleLinkSettings();
		if ( window.getUserSetting( 'advImgDetails' ) === 'show' ) {
			this.toggleAdvanced( true );
		}
		this.trigger( 'post-render' );
	},

	resetFocus: function() {
		this.$( '.link-to-custom' ).blur();
		this.$( '.embed-media-settings' ).scrollTop( 0 );
	},

	updateUrl: function() {
		this.$( '.image img' ).attr( 'src', this.model.get( 'url' ) );
		this.$( '.url' ).val( this.model.get( 'url' ) );
	},

	toggleLinkSettings: function() {
		if ( this.model.get( 'link' ) === 'none' ) {
			this.$( '.link-settings' ).addClass('hidden');
		} else {
			this.$( '.link-settings' ).removeClass('hidden');
		}
	},

	toggleCustomSize: function() {
		if ( this.model.get( 'size' ) !== 'custom' ) {
			this.$( '.custom-size' ).addClass('hidden');
		} else {
			this.$( '.custom-size' ).removeClass('hidden');
		}
	},

	onCustomSize: function( event ) {
		var dimension = $( event.target ).data('setting'),
			num = $( event.target ).val(),
			value;

		// Ignore bogus input
		if ( ! /^\d+/.test( num ) || parseInt( num, 10 ) < 1 ) {
			event.preventDefault();
			return;
		}

		if ( dimension === 'customWidth' ) {
			value = Math.round( 1 / this.model.get( 'aspectRatio' ) * num );
			this.model.set( 'customHeight', value, { silent: true } );
			this.$( '[data-setting="customHeight"]' ).val( value );
		} else {
			value = Math.round( this.model.get( 'aspectRatio' ) * num );
			this.model.set( 'customWidth', value, { silent: true  } );
			this.$( '[data-setting="customWidth"]' ).val( value );
		}
	},

	onToggleAdvanced: function( event ) {
		event.preventDefault();
		this.toggleAdvanced();
	},

	toggleAdvanced: function( show ) {
		var $advanced = this.$el.find( '.advanced-section' ),
			mode;

		if ( $advanced.hasClass('advanced-visible') || show === false ) {
			$advanced.removeClass('advanced-visible');
			$advanced.find('.advanced-settings').addClass('hidden');
			mode = 'hide';
		} else {
			$advanced.addClass('advanced-visible');
			$advanced.find('.advanced-settings').removeClass('hidden');
			mode = 'show';
		}

		window.setUserSetting( 'advImgDetails', mode );
	},

	editAttachment: function( event ) {
		var editState = this.controller.states.get( 'edit-image' );

		if ( window.imageEdit && editState ) {
			event.preventDefault();
			editState.set( 'image', this.model.attachment );
			this.controller.setState( 'edit-image' );
		}
	},

	replaceAttachment: function( event ) {
		event.preventDefault();
		this.controller.setState( 'replace-image' );
	}
});

module.exports = ImageDetails;
