/**
 * wp.media.view.MediaFrame.MediaDetails
 *
 * @constructor
 * @augments wp.media.view.MediaFrame.Select
 * @augments wp.media.view.MediaFrame
 * @augments wp.media.view.Frame
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 * @mixes wp.media.controller.StateMachine
 */
var View = require( '../view.js' ),
	Toolbar = require( '../toolbar.js' ),
	Select = require( './select.js' ),
	l10n = wp.media.view.l10n,
	MediaDetails;

MediaDetails = Select.extend({
	defaults: {
		id:      'media',
		url:     '',
		menu:    'media-details',
		content: 'media-details',
		toolbar: 'media-details',
		type:    'link',
		priority: 120
	},

	initialize: function( options ) {
		this.DetailsView = options.DetailsView;
		this.cancelText = options.cancelText;
		this.addText = options.addText;

		this.media = new wp.media.model.PostMedia( options.metadata );
		this.options.selection = new wp.media.model.Selection( this.media.attachment, { multiple: false } );
		Select.prototype.initialize.apply( this, arguments );
	},

	bindHandlers: function() {
		var menu = this.defaults.menu;

		Select.prototype.bindHandlers.apply( this, arguments );

		this.on( 'menu:create:' + menu, this.createMenu, this );
		this.on( 'content:render:' + menu, this.renderDetailsContent, this );
		this.on( 'menu:render:' + menu, this.renderMenu, this );
		this.on( 'toolbar:render:' + menu, this.renderDetailsToolbar, this );
	},

	renderDetailsContent: function() {
		var view = new this.DetailsView({
			controller: this,
			model: this.state().media,
			attachment: this.state().media.attachment
		}).render();

		this.content.set( view );
	},

	renderMenu: function( view ) {
		var lastState = this.lastState(),
			previous = lastState && lastState.id,
			frame = this;

		view.set({
			cancel: {
				text:     this.cancelText,
				priority: 20,
				click:    function() {
					if ( previous ) {
						frame.setState( previous );
					} else {
						frame.close();
					}
				}
			},
			separateCancel: new View({
				className: 'separator',
				priority: 40
			})
		});

	},

	setPrimaryButton: function(text, handler) {
		this.toolbar.set( new Toolbar({
			controller: this,
			items: {
				button: {
					style:    'primary',
					text:     text,
					priority: 80,
					click:    function() {
						var controller = this.controller;
						handler.call( this, controller, controller.state() );
						// Restore and reset the default state.
						controller.setState( controller.options.state );
						controller.reset();
					}
				}
			}
		}) );
	},

	renderDetailsToolbar: function() {
		this.setPrimaryButton( l10n.update, function( controller, state ) {
			controller.close();
			state.trigger( 'update', controller.media.toJSON() );
		} );
	},

	renderReplaceToolbar: function() {
		this.setPrimaryButton( l10n.replace, function( controller, state ) {
			var attachment = state.get( 'selection' ).single();
			controller.media.changeAttachment( attachment );
			state.trigger( 'replace', controller.media.toJSON() );
		} );
	},

	renderAddSourceToolbar: function() {
		this.setPrimaryButton( this.addText, function( controller, state ) {
			var attachment = state.get( 'selection' ).single();
			controller.media.setSource( attachment );
			state.trigger( 'add-source', controller.media.toJSON() );
		} );
	}
});

module.exports = MediaDetails;
