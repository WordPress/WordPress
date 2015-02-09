/**
 * wp.media.view.Iframe
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var View = require( './view.js' ),
	Iframe;

Iframe = View.extend({
	className: 'media-iframe',
	/**
	 * @returns {wp.media.view.Iframe} Returns itself to allow chaining
	 */
	render: function() {
		this.views.detach();
		this.$el.html( '<iframe src="' + this.controller.state().get('src') + '" />' );
		this.views.render();
		return this;
	}
});

module.exports = Iframe;
