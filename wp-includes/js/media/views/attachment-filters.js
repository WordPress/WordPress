/**
 * wp.media.view.AttachmentFilters
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var View = require( './view.js' ),
	$ = jQuery,
	AttachmentFilters;

AttachmentFilters = View.extend({
	tagName:   'select',
	className: 'attachment-filters',
	id:        'media-attachment-filters',

	events: {
		change: 'change'
	},

	keys: [],

	initialize: function() {
		this.createFilters();
		_.extend( this.filters, this.options.filters );

		// Build `<option>` elements.
		this.$el.html( _.chain( this.filters ).map( function( filter, value ) {
			return {
				el: $( '<option></option>' ).val( value ).html( filter.text )[0],
				priority: filter.priority || 50
			};
		}, this ).sortBy('priority').pluck('el').value() );

		this.listenTo( this.model, 'change', this.select );
		this.select();
	},

	/**
	 * @abstract
	 */
	createFilters: function() {
		this.filters = {};
	},

	/**
	 * When the selected filter changes, update the Attachment Query properties to match.
	 */
	change: function() {
		var filter = this.filters[ this.el.value ];
		if ( filter ) {
			this.model.set( filter.props );
		}
	},

	select: function() {
		var model = this.model,
			value = 'all',
			props = model.toJSON();

		_.find( this.filters, function( filter, id ) {
			var equal = _.all( filter.props, function( prop, key ) {
				return prop === ( _.isUndefined( props[ key ] ) ? null : props[ key ] );
			});

			if ( equal ) {
				return value = id;
			}
		});

		this.$el.val( value );
	}
});

module.exports = AttachmentFilters;
