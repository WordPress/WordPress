/**
 * wp.media.view.EmbedUrl
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var View = require( '../view.js' ),
	$ = jQuery,
	EmbedUrl;

EmbedUrl = View.extend({
	tagName:   'label',
	className: 'embed-url',

	events: {
		'input':  'url',
		'keyup':  'url',
		'change': 'url'
	},

	initialize: function() {
		this.$input = $('<input id="embed-url-field" type="url" />').val( this.model.get('url') );
		this.input = this.$input[0];

		this.spinner = $('<span class="spinner" />')[0];
		this.$el.append([ this.input, this.spinner ]);

		this.listenTo( this.model, 'change:url', this.render );

		if ( this.model.get( 'url' ) ) {
			_.delay( _.bind( function () {
				this.model.trigger( 'change:url' );
			}, this ), 500 );
		}
	},
	/**
	 * @returns {wp.media.view.EmbedUrl} Returns itself to allow chaining
	 */
	render: function() {
		var $input = this.$input;

		if ( $input.is(':focus') ) {
			return;
		}

		this.input.value = this.model.get('url') || 'http://';
		/**
		 * Call `render` directly on parent class with passed arguments
		 */
		View.prototype.render.apply( this, arguments );
		return this;
	},

	ready: function() {
		if ( ! wp.media.isTouchDevice ) {
			this.focus();
		}
	},

	url: function( event ) {
		this.model.set( 'url', event.target.value );
	},

	/**
	 * If the input is visible, focus and select its contents.
	 */
	focus: function() {
		var $input = this.$input;
		if ( $input.is(':visible') ) {
			$input.focus()[0].select();
		}
	}
});

module.exports = EmbedUrl;
