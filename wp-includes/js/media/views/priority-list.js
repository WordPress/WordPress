/**
 * wp.media.view.PriorityList
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var View = require( './view.js' ),
	PriorityList;

PriorityList = View.extend({
	tagName:   'div',

	initialize: function() {
		this._views = {};

		this.set( _.extend( {}, this._views, this.options.views ), { silent: true });
		delete this.options.views;

		if ( ! this.options.silent ) {
			this.render();
		}
	},
	/**
	 * @param {string} id
	 * @param {wp.media.View|Object} view
	 * @param {Object} options
	 * @returns {wp.media.view.PriorityList} Returns itself to allow chaining
	 */
	set: function( id, view, options ) {
		var priority, views, index;

		options = options || {};

		// Accept an object with an `id` : `view` mapping.
		if ( _.isObject( id ) ) {
			_.each( id, function( view, id ) {
				this.set( id, view );
			}, this );
			return this;
		}

		if ( ! (view instanceof Backbone.View) ) {
			view = this.toView( view, id, options );
		}
		view.controller = view.controller || this.controller;

		this.unset( id );

		priority = view.options.priority || 10;
		views = this.views.get() || [];

		_.find( views, function( existing, i ) {
			if ( existing.options.priority > priority ) {
				index = i;
				return true;
			}
		});

		this._views[ id ] = view;
		this.views.add( view, {
			at: _.isNumber( index ) ? index : views.length || 0
		});

		return this;
	},
	/**
	 * @param {string} id
	 * @returns {wp.media.View}
	 */
	get: function( id ) {
		return this._views[ id ];
	},
	/**
	 * @param {string} id
	 * @returns {wp.media.view.PriorityList}
	 */
	unset: function( id ) {
		var view = this.get( id );

		if ( view ) {
			view.remove();
		}

		delete this._views[ id ];
		return this;
	},
	/**
	 * @param {Object} options
	 * @returns {wp.media.View}
	 */
	toView: function( options ) {
		return new View( options );
	}
});

module.exports = PriorityList;
