(function( $, wp, _ ) {

	if ( ! wp || ! wp.customize ) { return; }
	var api = wp.customize;


	/**
	 * wp.customize.HeaderTool.CurrentView
	 *
	 * Displays the currently selected header image, or a placeholder in lack
	 * thereof.
	 *
	 * Instantiate with model wp.customize.HeaderTool.currentHeader.
	 *
	 * @constructor
	 * @augments wp.Backbone.View
	 */
	api.HeaderTool.CurrentView = wp.Backbone.View.extend({
		template: wp.template('header-current'),

		initialize: function() {
			this.listenTo(this.model, 'change', this.render);
			this.render();
		},

		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			this.setPlaceholder();
			this.setButtons();
			return this;
		},

		getHeight: function() {
			var image = this.$el.find('img'),
				saved = this.model.get('savedHeight'),
				height = image.height() || saved,
				headerImageData;

			if (image.length) {
				this.$el.find('.inner').hide();
			} else {
				this.$el.find('.inner').show();
			}

			// happens at ready
			if (!height) {
				headerImageData = api.get().header_image_data;

				if (headerImageData && headerImageData.width && headerImageData.height) {
					// hardcoded container width
					height = 260 / headerImageData.width * headerImageData.height;
				}
				else {
					// fallback for when no image is set
					height = 40;
				}
			}

			return height;
		},

		setPlaceholder: function(_height) {
			var height = _height || this.getHeight();
			this.model.set('savedHeight', height);
			this.$el
				.add(this.$el.find('.placeholder'))
				.height(height);
		},

		setButtons: function() {
			var elements = $('.actions .remove');
			if (this.model.get('choice')) {
				elements.show();
			} else {
				elements.hide();
			}
		}
	});


	/**
	 * wp.customize.HeaderTool.ChoiceView
	 *
	 * Represents a choosable header image, be it user-uploaded,
	 * theme-suggested or a special Randomize choice.
	 *
	 * Takes a wp.customize.HeaderTool.ImageModel.
	 *
	 * Manually changes model wp.customize.HeaderTool.currentHeader via the
	 * `select` method.
	 *
	 * @constructor
	 * @augments wp.Backbone.View
	 */
	(function () { // closures FTW
	var lastHeight = 0;
	api.HeaderTool.ChoiceView = wp.Backbone.View.extend({
		template: wp.template('header-choice'),

		className: 'header-view',

		events: {
			'click .choice,.random': 'select',
			'click .close': 'removeImage'
		},

		initialize: function() {
			var properties = [
				this.model.get('header').url,
				this.model.get('choice')
			];

			this.listenTo(this.model, 'change', this.render);

			if (_.contains(properties, api.get().header_image)) {
				api.HeaderTool.currentHeader.set(this.extendedModel());
			}
		},

		render: function() {
			var model = this.model;

			this.$el.html(this.template(this.extendedModel()));

			if (model.get('random')) {
				this.setPlaceholder(40);
			}
			else {
				lastHeight = this.getHeight();
			}

			this.$el.toggleClass('hidden', model.get('hidden'));
			return this;
		},

		extendedModel: function() {
			var c = this.model.get('collection');
			return _.extend(this.model.toJSON(), {
				// -1 to exclude the randomize button
				nImages: c.size() - 1
			});
		},

		getHeight: api.HeaderTool.CurrentView.prototype.getHeight,

		setPlaceholder: api.HeaderTool.CurrentView.prototype.setPlaceholder,

		select: function() {
			this.model.save();
			api.HeaderTool.currentHeader.set(this.extendedModel());
		},

		removeImage: function(e) {
			e.stopPropagation();
			this.model.destroy();
			this.remove();
		}
	});
	})();


	/**
	 * wp.customize.HeaderTool.ChoiceListView
	 *
	 * A container for ChoiceViews. These choices should be of one same type:
	 * user-uploaded headers or theme-defined ones.
	 *
	 * Takes a wp.customize.HeaderTool.ChoiceList.
	 *
	 * @constructor
	 * @augments wp.Backbone.View
	 */
	api.HeaderTool.ChoiceListView = wp.Backbone.View.extend({
		initialize: function() {
			this.listenTo(this.collection, 'add', this.addOne);
			this.listenTo(this.collection, 'remove', this.render);
			this.listenTo(this.collection, 'sort', this.render);
			this.listenTo(this.collection, 'change:hidden', this.toggleTitle);
			this.listenTo(this.collection, 'change:hidden', this.setMaxListHeight);
			this.render();
		},

		render: function() {
			this.$el.empty();
			this.collection.each(this.addOne, this);
			this.toggleTitle();
		},

		addOne: function(choice) {
			var view;
			choice.set({ collection: this.collection });
			view = new api.HeaderTool.ChoiceView({ model: choice });
			this.$el.append(view.render().el);
		},

		toggleTitle: function() {
			var title = this.$el.parents().prev('.customize-control-title');
			if (this.collection.shouldHideTitle()) {
				title.hide();
			} else {
				title.show();
			}
		}
	});


	/**
	 * wp.customize.HeaderTool.CombinedList
	 *
	 * Aggregates wp.customize.HeaderTool.ChoiceList collections (or any
	 * Backbone object, really) and acts as a bus to feed them events.
	 *
	 * @constructor
	 * @augments wp.Backbone.View
	 */
	api.HeaderTool.CombinedList = wp.Backbone.View.extend({
		initialize: function(collections) {
			this.collections = collections;
			this.on('all', this.propagate, this);
		},
		propagate: function(event, arg) {
			_.each(this.collections, function(collection) {
				collection.trigger(event, arg);
			});
		}
	});

})( jQuery, window.wp, _ );
