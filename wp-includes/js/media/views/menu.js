/**
 * wp.media.view.Menu
 *
 * @class
 * @augments wp.media.view.PriorityList
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var MenuItem = wp.media.view.MenuItem,
	PriorityList = wp.media.view.PriorityList,
	Menu;

Menu = PriorityList.extend({
	tagName:   'div',
	className: 'media-menu',
	property:  'state',
	ItemView:  MenuItem,
	region:    'menu',

	/* TODO: alternatively hide on any click anywhere
	events: {
		'click': 'click'
	},

	click: function() {
		this.$el.removeClass( 'visible' );
	},
	*/

	/**
	 * @param {Object} options
	 * @param {string} id
	 * @returns {wp.media.View}
	 */
	toView: function( options, id ) {
		options = options || {};
		options[ this.property ] = options[ this.property ] || id;
		return new this.ItemView( options ).render();
	},

	ready: function() {
		/**
		 * call 'ready' directly on the parent class
		 */
		PriorityList.prototype.ready.apply( this, arguments );
		this.visibility();
	},

	set: function() {
		/**
		 * call 'set' directly on the parent class
		 */
		PriorityList.prototype.set.apply( this, arguments );
		this.visibility();
	},

	unset: function() {
		/**
		 * call 'unset' directly on the parent class
		 */
		PriorityList.prototype.unset.apply( this, arguments );
		this.visibility();
	},

	visibility: function() {
		var region = this.region,
			view = this.controller[ region ].get(),
			views = this.views.get(),
			hide = ! views || views.length < 2;

		if ( this === view ) {
			this.controller.$el.toggleClass( 'hide-' + region, hide );
		}
	},
	/**
	 * @param {string} id
	 */
	select: function( id ) {
		var view = this.get( id );

		if ( ! view ) {
			return;
		}

		this.deselect();
		view.$el.addClass('active');
	},

	deselect: function() {
		this.$el.children().removeClass('active');
	},

	hide: function( id ) {
		var view = this.get( id );

		if ( ! view ) {
			return;
		}

		view.$el.addClass('hidden');
	},

	show: function( id ) {
		var view = this.get( id );

		if ( ! view ) {
			return;
		}

		view.$el.removeClass('hidden');
	}
});

module.exports = Menu;
