/*globals wp, _, jQuery */

/**
 * wp.media.view.EmbedLink
 *
 * @class
 * @augments wp.media.view.Settings
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var Settings = require( '../settings.js' ),
	$ = jQuery,
	EmbedLink;

EmbedLink = Settings.extend({
	className: 'embed-link-settings',
	template:  wp.template('embed-link-settings'),

	initialize: function() {
		this.spinner = $('<span class="spinner" />');
		this.$el.append( this.spinner[0] );
		this.listenTo( this.model, 'change:url change:width change:height', this.updateoEmbed );
	},

	updateoEmbed: _.debounce( function() {
		var url = this.model.get( 'url' );

		// clear out previous results
		this.$('.embed-container').hide().find('.embed-preview').empty();
		this.$( '.setting' ).hide();

		// only proceed with embed if the field contains more than 6 characters
		if ( url && url.length < 6 ) {
			return;
		}

		this.spinner.show();

		this.fetch();
	}, 600 ),

	fetch: function() {
		var embed;

		// check if they haven't typed in 500 ms
		if ( $('#embed-url-field').val() !== this.model.get('url') ) {
			return;
		}

		embed = new wp.shortcode({
			tag: 'embed',
			attrs: _.pick( this.model.attributes, [ 'width', 'height', 'src' ] ),
			content: this.model.get('url')
		});

		wp.ajax.send( 'parse-embed', {
			data : {
				post_ID: wp.media.view.settings.post.id,
				shortcode: embed.string()
			}
		} )
			.done( _.bind( this.renderoEmbed, this ) )
			.fail( _.bind( this.renderFail, this ) );
	},

	renderFail: function () {
		this.$( '.setting' ).hide().filter( '.link-text' ).show();
	},

	renderoEmbed: function( response ) {
		var html = ( response && response.body ) || '',
			attr = {},
			opts = { silent: true };

		this.$( '.setting' ).hide()
			.filter( '.link-text' )[ html ? 'hide' : 'show' ]();

		if ( response && response.attr ) {
			attr = response.attr;

			_.each( [ 'width', 'height' ], function ( key ) {
				var $el = this.$( '.setting.' + key ),
					value = attr[ key ];

				if ( value ) {
					this.model.set( key, value, opts );
					$el.show().find( 'input' ).val( value );
				} else {
					this.model.unset( key, opts );
					$el.hide().find( 'input' ).val( '' );
				}
			}, this );
		} else {
			this.model.unset( 'height', opts );
			this.model.unset( 'width', opts );
		}

		this.spinner.hide();

		this.$('.embed-container').show().find('.embed-preview').html( html );
	}
});

module.exports = EmbedLink;
