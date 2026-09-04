/**
 * @output wp-includes/js/customize-models.js
 */

/* global _wpCustomizeHeader */

/**
 * @param {JQueryStatic} $  The jQuery object.
 * @param {Object}       wp The WordPress global object.
 */
(function( $, wp ) {
	var api = wp.customize;
	/** @namespace wp.customize.HeaderTool */
	api.HeaderTool = {};


	/**
	 * wp.customize.HeaderTool.ImageModel
	 *
	 * A header image. This is where saves via the Customizer API are
	 * abstracted away, plus our own Ajax calls to add images to and remove
	 * images from the user's recently uploaded images setting on the server.
	 * These calls are made regardless of whether the user actually saves new
	 * Customizer settings.
	 *
	 * @memberOf wp.customize.HeaderTool
	 * @alias wp.customize.HeaderTool.ImageModel
	 *
	 * @class
	 * @augments Backbone.Model
	 */
	api.HeaderTool.ImageModel = Backbone.Model.extend(/** @lends wp.customize.HeaderTool.ImageModel.prototype */{
		/**
		 * Returns the default attributes for a header image.
		 *
		 * @return {Object} Default attributes.
		 */
		defaults: function() {
			return {
				header: {
					attachment_id: 0,
					url: '',
					timestamp: _.now(),
					thumbnail_url: ''
				},
				choice: '',
				selected: false,
				random: false
			};
		},

		/**
		 * Initializes the model, hiding the header when asked to.
		 */
		initialize: function() {
			this.on('hide', this.hide, this);
		},

		/**
		 * Hides the header image, removing it from the site.
		 */
		hide: function() {
			this.set('choice', '');
			api('header_image').set('remove-header');
			api('header_image_data').set('remove-header');
		},

		/**
		 * Removes the image, hiding the header first if it is the current one.
		 */
		destroy: function() {
			var data = this.get('header'),
				curr = api.HeaderTool.currentHeader.get('header').attachment_id;

			// If the image we're removing is also the current header,
			// unset the latter.
			if (curr && data.attachment_id === curr) {
				api.HeaderTool.currentHeader.trigger('hide');
			}

			wp.ajax.post( 'custom-header-remove', {
				nonce: _wpCustomizeHeader.nonces.remove,
				wp_customize: 'on',
				theme: api.settings.theme.stylesheet,
				attachment_id: data.attachment_id
			});

			this.trigger('destroy', this, this.collection);
		},

		/**
		 * Makes this image the current header image.
		 */
		save: function() {
			if (this.get('random')) {
				api('header_image').set(this.get('header').random);
				api('header_image_data').set(this.get('header').random);
			} else {
				if (this.get('header').defaultName) {
					api('header_image').set(this.get('header').url);
					api('header_image_data').set(this.get('header').defaultName);
				} else {
					api('header_image').set(this.get('header').url);
					api('header_image_data').set(this.get('header'));
				}
			}

			api.HeaderTool.combinedList.trigger('control:setImage', this);
		},

		/**
		 * Adds the image to the theme's uploaded headers.
		 */
		importImage: function() {
			var data = this.get('header');
			if (data.attachment_id === undefined) {
				return;
			}

			wp.ajax.post( 'custom-header-add', {
				nonce: _wpCustomizeHeader.nonces.add,
				wp_customize: 'on',
				theme: api.settings.theme.stylesheet,
				attachment_id: data.attachment_id
			} );
		},

		/**
		 * Returns whether the image needs cropping for this theme.
		 *
		 * @return {boolean} Whether the image should be cropped.
		 */
		shouldBeCropped: function() {
			if (this.get('themeFlexWidth') === true &&
						this.get('themeFlexHeight') === true) {
				return false;
			}

			if (this.get('themeFlexWidth') === true &&
				this.get('themeHeight') === this.get('imageHeight')) {
				return false;
			}

			if (this.get('themeFlexHeight') === true &&
				this.get('themeWidth') === this.get('imageWidth')) {
				return false;
			}

			if (this.get('themeWidth') === this.get('imageWidth') &&
				this.get('themeHeight') === this.get('imageHeight')) {
				return false;
			}

			if (this.get('imageWidth') <= this.get('themeWidth')) {
				return false;
			}

			return true;
		}
	});


	/**
	 * wp.customize.HeaderTool.ChoiceList
	 *
	 * @memberOf wp.customize.HeaderTool
	 * @alias wp.customize.HeaderTool.ChoiceList
	 *
	 * @class
	 * @augments Backbone.Collection
	 */
	api.HeaderTool.ChoiceList = Backbone.Collection.extend({
		model: api.HeaderTool.ImageModel,

		/**
		 * Orders the collection from most recently used to least.
		 *
		 * @param {Backbone.Model} model The model to sort.
		 * @return {number} The sort order.
		 */
		comparator: function(model) {
			return -model.get('header').timestamp;
		},

		/**
		 * Initializes the collection from the uploaded header images.
		 */
		initialize: function() {
			var current = api.HeaderTool.currentHeader.get('choice').replace(/^https?:\/\//, ''),
				isRandom = this.isRandomChoice(api.get().header_image);

			// Overridable by an extending class.
			if (!this.type) {
				this.type = 'uploaded';
			}

			// Overridable by an extending class.
			if (typeof this.data === 'undefined') {
				this.data = _wpCustomizeHeader.uploads;
			}

			if (isRandom) {
				// So that when adding data we don't hide regular images.
				current = api.get().header_image;
			}

			this.on('control:setImage', this.setImage, this);
			this.on('control:removeImage', this.removeImage, this);
			this.on('add', this.maybeRemoveOldCrop, this);
			this.on('add', this.maybeAddRandomChoice, this);

			_.each(this.data, function(elt, index) {
				if (!elt.attachment_id) {
					elt.defaultName = index;
				}

				if (typeof elt.timestamp === 'undefined') {
					elt.timestamp = 0;
				}

				this.add({
					header: elt,
					choice: elt.url.split('/').pop(),
					selected: current === elt.url.replace(/^https?:\/\//, '')
				}, { silent: true });
			}, this);

			if (this.size() > 0) {
				this.addRandomChoice(current);
			}
		},

		/**
		 * Removes an image's previous crop once it has been replaced.
		 *
		 * @param {Backbone.Model} model Model.
		 */
		maybeRemoveOldCrop: function( model ) {
			var newID = model.get( 'header' ).attachment_id || false,
			 	oldCrop;

			// Bail early if we don't have a new attachment ID.
			if ( ! newID ) {
				return;
			}

			oldCrop = this.find( function( item ) {
				return ( item.cid !== model.cid && item.get( 'header' ).attachment_id === newID );
			} );

			// If we found an old crop, remove it from the collection.
			if ( oldCrop ) {
				this.remove( oldCrop );
			}
		},

		/**
		 * Adds the random choice once a single image is left.
		 */
		maybeAddRandomChoice: function() {
			if (this.size() === 1) {
				this.addRandomChoice();
			}
		},

		/**
		 * Adds the random image choice to the collection.
		 *
		 * @param {string} initialChoice Initial choice.
		 */
		addRandomChoice: function(initialChoice) {
			var isRandomSameType = RegExp(this.type).test(initialChoice),
				randomChoice = 'random-' + this.type + '-image';

			this.add({
				header: {
					timestamp: 0,
					random: randomChoice,
					width: 245,
					height: 41
				},
				choice: randomChoice,
				random: true,
				selected: isRandomSameType
			});
		},

		/**
		 * Returns whether a choice is one of the random image choices.
		 *
		 * @param {string} choice Choice.
		 * @return {boolean} Whether the choice is random.
		 */
		isRandomChoice: function(choice) {
			return (/^random-(uploaded|default)-image$/).test(choice);
		},

		/**
		 * Returns whether the list title should be hidden.
		 *
		 * @return {boolean} Whether the title should be hidden.
		 */
		shouldHideTitle: function() {
			return this.size() < 2;
		},

		/**
		 * Marks a single image as the selected one.
		 *
		 * @param {Backbone.Model} model Model.
		 */
		setImage: function(model) {
			this.each(function(m) {
				m.set('selected', false);
			});

			if (model) {
				model.set('selected', true);
			}
		},

		/**
		 * Clears the selection.
		 */
		removeImage: function() {
			this.each(function(m) {
				m.set('selected', false);
			});
		}
	});


	/**
	 * wp.customize.HeaderTool.DefaultsList
	 *
	 * @memberOf wp.customize.HeaderTool
	 * @alias wp.customize.HeaderTool.DefaultsList
	 *
	 * @class
	 * @augments wp.customize.HeaderTool.ChoiceList
	 * @augments Backbone.Collection
	 */
	api.HeaderTool.DefaultsList = api.HeaderTool.ChoiceList.extend({
		/**
		 * Initializes the collection from the theme's default header images.
		 */
		initialize: function() {
			this.type = 'default';
			this.data = _wpCustomizeHeader.defaults;
			api.HeaderTool.ChoiceList.prototype.initialize.apply(this);
		}
	});

})( jQuery, window.wp );
