/**
 * wp.media.view.ButtonGroup
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var View = require( './view.js' ),
	Button = require( './button.js' ),
	$ = jQuery,
	ButtonGroup;

ButtonGroup = View.extend({
	tagName:   'div',
	className: 'button-group button-large media-button-group',

	initialize: function() {
		/**
		 * @member {wp.media.view.Button[]}
		 */
		this.buttons = _.map( this.options.buttons || [], function( button ) {
			if ( button instanceof Backbone.View ) {
				return button;
			} else {
				return new Button( button ).render();
			}
		});

		delete this.options.buttons;

		if ( this.options.classes ) {
			this.$el.addClass( this.options.classes );
		}
	},

	/**
	 * @returns {wp.media.view.ButtonGroup}
	 */
	render: function() {
		this.$el.html( $( _.pluck( this.buttons, 'el' ) ).detach() );
		return this;
	}
});

module.exports = ButtonGroup;
