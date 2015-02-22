/*globals wp, _, Backbone */

/**
 * wp.media.controller.Cropper
 *
 * A state for cropping an image.
 *
 * @class
 * @augments wp.media.controller.State
 * @augments Backbone.Model
 */
var State = require( './state.js' ),
	ToolbarView = require( '../views/toolbar.js' ),
	CropperView = require( '../views/cropper.js' ),
	l10n = wp.media.view.l10n,
	Cropper;

Cropper = State.extend({
	defaults: {
		id:          'cropper',
		title:       l10n.cropImage,
		// Region mode defaults.
		toolbar:     'crop',
		content:     'crop',
		router:      false,

		canSkipCrop: false
	},

	activate: function() {
		this.frame.on( 'content:create:crop', this.createCropContent, this );
		this.frame.on( 'close', this.removeCropper, this );
		this.set('selection', new Backbone.Collection(this.frame._selection.single));
	},

	deactivate: function() {
		this.frame.toolbar.mode('browse');
	},

	createCropContent: function() {
		this.cropperView = new CropperView({
			controller: this,
			attachment: this.get('selection').first()
		});
		this.cropperView.on('image-loaded', this.createCropToolbar, this);
		this.frame.content.set(this.cropperView);

	},
	removeCropper: function() {
		this.imgSelect.cancelSelection();
		this.imgSelect.setOptions({remove: true});
		this.imgSelect.update();
		this.cropperView.remove();
	},
	createCropToolbar: function() {
		var canSkipCrop, toolbarOptions;

		canSkipCrop = this.get('canSkipCrop') || false;

		toolbarOptions = {
			controller: this.frame,
			items: {
				insert: {
					style:    'primary',
					text:     l10n.cropImage,
					priority: 80,
					requires: { library: false, selection: false },

					click: function() {
						var controller = this.controller,
							selection;

						selection = controller.state().get('selection').first();
						selection.set({cropDetails: controller.state().imgSelect.getSelection()});

						this.$el.text(l10n.cropping);
						this.$el.attr('disabled', true);

						controller.state().doCrop( selection ).done( function( croppedImage ) {
							controller.trigger('cropped', croppedImage );
							controller.close();
						}).fail( function() {
							controller.trigger('content:error:crop');
						});
					}
				}
			}
		};

		if ( canSkipCrop ) {
			_.extend( toolbarOptions.items, {
				skip: {
					style:      'secondary',
					text:       l10n.skipCropping,
					priority:   70,
					requires:   { library: false, selection: false },
					click:      function() {
						var selection = this.controller.state().get('selection').first();
						this.controller.state().cropperView.remove();
						this.controller.trigger('skippedcrop', selection);
						this.controller.close();
					}
				}
			});
		}

		this.frame.toolbar.set( new ToolbarView(toolbarOptions) );
	},

	doCrop: function( attachment ) {
		return wp.ajax.post( 'custom-header-crop', {
			nonce: attachment.get('nonces').edit,
			id: attachment.get('id'),
			cropDetails: attachment.get('cropDetails')
		} );
	}
});

module.exports = Cropper;
