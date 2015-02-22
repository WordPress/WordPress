/*globals wp, _, Backbone */

/**
 * wp.media.view.Selection
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var View = require( './view.js' ),
	AttachmentsSelection = require( './attachments/selection.js' ),
	l10n = wp.media.view.l10n,
	Selection;

Selection = View.extend({
	tagName:   'div',
	className: 'media-selection',
	template:  wp.template('media-selection'),

	events: {
		'click .edit-selection':  'edit',
		'click .clear-selection': 'clear'
	},

	initialize: function() {
		_.defaults( this.options, {
			editable:  false,
			clearable: true
		});

		/**
		 * @member {wp.media.view.Attachments.Selection}
		 */
		this.attachments = new AttachmentsSelection({
			controller: this.controller,
			collection: this.collection,
			selection:  this.collection,
			model:      new Backbone.Model()
		});

		this.views.set( '.selection-view', this.attachments );
		this.collection.on( 'add remove reset', this.refresh, this );
		this.controller.on( 'content:activate', this.refresh, this );
	},

	ready: function() {
		this.refresh();
	},

	refresh: function() {
		// If the selection hasn't been rendered, bail.
		if ( ! this.$el.children().length ) {
			return;
		}

		var collection = this.collection,
			editing = 'edit-selection' === this.controller.content.mode();

		// If nothing is selected, display nothing.
		this.$el.toggleClass( 'empty', ! collection.length );
		this.$el.toggleClass( 'one', 1 === collection.length );
		this.$el.toggleClass( 'editing', editing );

		this.$('.count').text( l10n.selected.replace('%d', collection.length) );
	},

	edit: function( event ) {
		event.preventDefault();
		if ( this.options.editable ) {
			this.options.editable.call( this, this.collection );
		}
	},

	clear: function( event ) {
		event.preventDefault();
		this.collection.reset();

		// Keep focus inside media modal
		// after clear link is selected
		this.controller.modal.focusManager.focus();
	}
});

module.exports = Selection;
