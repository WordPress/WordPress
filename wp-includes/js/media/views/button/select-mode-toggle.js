/*globals wp */

/**
 * wp.media.view.SelectModeToggleButton
 *
 * @class
 * @augments wp.media.view.Button
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var Button = wp.media.view.Button,
	l10n = wp.media.view.l10n,
	SelectModeToggle;

SelectModeToggle = Button.extend({
	initialize: function() {
		Button.prototype.initialize.apply( this, arguments );
		this.listenTo( this.controller, 'select:activate select:deactivate', this.toggleBulkEditHandler );
		this.listenTo( this.controller, 'selection:action:done', this.back );
	},

	back: function () {
		this.controller.deactivateMode( 'select' ).activateMode( 'edit' );
	},

	click: function() {
		Button.prototype.click.apply( this, arguments );
		if ( this.controller.isModeActive( 'select' ) ) {
			this.back();
		} else {
			this.controller.deactivateMode( 'edit' ).activateMode( 'select' );
		}
	},

	render: function() {
		Button.prototype.render.apply( this, arguments );
		this.$el.addClass( 'select-mode-toggle-button' );
		return this;
	},

	toggleBulkEditHandler: function() {
		var toolbar = this.controller.content.get().toolbar, children;

		children = toolbar.$( '.media-toolbar-secondary > *, .media-toolbar-primary > *' );

		// TODO: the Frame should be doing all of this.
		if ( this.controller.isModeActive( 'select' ) ) {
			this.model.set( 'text', l10n.cancelSelection );
			children.not( '.media-button' ).hide();
			this.$el.show();
			toolbar.$( '.delete-selected-button' ).removeClass( 'hidden' );
		} else {
			this.model.set( 'text', l10n.bulkSelect );
			this.controller.content.get().$el.removeClass( 'fixed' );
			toolbar.$el.css( 'width', '' );
			toolbar.$( '.delete-selected-button' ).addClass( 'hidden' );
			children.not( '.spinner, .media-button' ).show();
			this.controller.state().get( 'selection' ).reset();
		}
	}
});

module.exports = SelectModeToggle;
