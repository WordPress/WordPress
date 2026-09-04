/**
 * @output wp-includes/js/customize-views.js
 */

/**
 * @param {JQueryStatic}       $  The jQuery object.
 * @param {Object}             wp The WordPress global object.
 * @param {_.UnderscoreStatic} _  The Underscore.js object.
 */
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
	 * @memberOf wp.customize.HeaderTool
	 * @alias wp.customize.HeaderTool.CurrentView
	 *
	 * @class
	 * @augments wp.Backbone.View
	 */
	api.HeaderTool.CurrentView = wp.Backbone.View.extend(/** @lends wp.customize.HeaderTool.CurrentView.prototype */{
		template: wp.template('header-current'),

		/**
		 * Initializes the view, rendering it whenever the model changes.
		 */
		initialize: function() {
			this.listenTo(this.model, 'change', this.render);
			this.render();
		},

		/**
		 * Renders the currently selected header image.
		 *
		 * @return {wp.customize.HeaderTool.CurrentView} Current view.
		 */
		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			this.setButtons();
			return this;
		},

		/**
		 * Shows or hides the header's action buttons.
		 */
		setButtons: function() {
			var elements = $('#customize-control-header_image .actions .remove');
			var addButton = $('#customize-control-header_image .actions .new');

			if (this.model.get('choice')) {
				elements.show();
				addButton.removeClass('upload-button');
			} else {
				elements.hide();
				addButton.addClass('upload-button');
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
	 * @memberOf wp.customize.HeaderTool
	 * @alias wp.customize.HeaderTool.ChoiceView
	 *
	 * @class
	 * @augments wp.Backbone.View
	 */
	api.HeaderTool.ChoiceView = wp.Backbone.View.extend(/** @lends wp.customize.HeaderTool.ChoiceView.prototype */{
		template: wp.template('header-choice'),

		className: 'header-view',

		events: {
			'click .choice,.random': 'select',
			'click .close': 'removeImage'
		},

		/**
		 * Initializes the view, marking it current when it matches the header image.
		 */
		initialize: function() {
			var properties = [
				this.model.get('header').url,
				this.model.get('choice')
			];

			this.listenTo(this.model, 'change:selected', this.toggleSelected);

			if (_.contains(properties, api.get().header_image)) {
				api.HeaderTool.currentHeader.set(this.extendedModel());
			}
		},

		/**
		 * Renders the choice.
		 *
		 * @return {wp.customize.HeaderTool.ChoiceView} Choice view.
		 */
		render: function() {
			this.$el.html(this.template(this.extendedModel()));

			this.toggleSelected();
			return this;
		},

		/**
		 * Toggles the selected class on the choice.
		 */
		toggleSelected: function() {
			this.$el.toggleClass('selected', this.model.get('selected'));
		},

		/**
		 * Returns the model's attributes together with its collection type.
		 *
		 * @return {Object} Extended model.
		 */
		extendedModel: function() {
			var c = this.model.get('collection');
			return _.extend(this.model.toJSON(), {
				type: c.type
			});
		},

		/**
		 * Selects the choice as the header image.
		 */
		select: function() {
			this.preventJump();
			this.model.save();
			api.HeaderTool.currentHeader.set(this.extendedModel());
		},

		/**
		 * Keeps the sidebar at its current scroll position.
		 */
		preventJump: function() {
			var container = $('.wp-full-overlay-sidebar-content'),
				scroll = container.scrollTop();

			_.defer(function() {
				container.scrollTop(scroll);
			});
		},

		/**
		 * Removes the image from the collection.
		 *
		 * @param {JQuery.Event} e Event.
		 */
		removeImage: function(e) {
			e.stopPropagation();
			this.model.destroy();
			this.remove();
		}
	});


	/**
	 * wp.customize.HeaderTool.ChoiceListView
	 *
	 * A container for ChoiceViews. These choices should be of one same type:
	 * user-uploaded headers or theme-defined ones.
	 *
	 * Takes a wp.customize.HeaderTool.ChoiceList.
	 *
	 * @memberOf wp.customize.HeaderTool
	 * @alias wp.customize.HeaderTool.ChoiceListView
	 *
	 * @class
	 * @augments wp.Backbone.View
	 */
	api.HeaderTool.ChoiceListView = wp.Backbone.View.extend(/** @lends wp.customize.HeaderTool.ChoiceListView.prototype */{
		/**
		 * Initializes the view, rendering it as the collection changes.
		 */
		initialize: function() {
			this.listenTo(this.collection, 'add', this.addOne);
			this.listenTo(this.collection, 'remove', this.render);
			this.listenTo(this.collection, 'sort', this.render);
			this.listenTo(this.collection, 'change', this.toggleList);
			this.render();
		},

		/**
		 * Renders each choice in the collection.
		 *
		 * @return {wp.customize.HeaderTool.ChoiceListView} Choice list view.
		 */
		render: function() {
			this.$el.empty();
			this.collection.each(this.addOne, this);
			this.toggleList();
			return this;
		},

		/**
		 * Renders a single choice into the list.
		 *
		 * @param {Backbone.Model} choice Choice.
		 */
		addOne: function(choice) {
			var view;
			choice.set({ collection: this.collection });
			view = new api.HeaderTool.ChoiceView({ model: choice });
			this.$el.append(view.render().el);
		},

		/**
		 * Shows or hides the list title and the random button.
		 */
		toggleList: function() {
			var title = this.$el.parents().prev('.customize-control-title'),
				randomButton = this.$el.find('.random').parent();
			if (this.collection.shouldHideTitle()) {
				title.add(randomButton).hide();
			} else {
				title.add(randomButton).show();
			}
		}
	});


	/**
	 * wp.customize.HeaderTool.CombinedList
	 *
	 * Aggregates wp.customize.HeaderTool.ChoiceList collections (or any
	 * Backbone object, really) and acts as a bus to feed them events.
	 *
	 * @memberOf wp.customize.HeaderTool
	 * @alias wp.customize.HeaderTool.CombinedList
	 *
	 * @class
	 * @augments wp.Backbone.View
	 */
	api.HeaderTool.CombinedList = wp.Backbone.View.extend(/** @lends wp.customize.HeaderTool.CombinedList.prototype */{
		/**
		 * Initializes the view with the collections to combine.
		 *
		 * @param {Backbone.Collection[]} collections Collections.
		 */
		initialize: function(collections) {
			this.collections = collections;
			this.on('all', this.propagate, this);
		},

		/**
		 * Passes an event on to each of the combined collections.
		 *
		 * @param {string} event Event.
		 * @param {*}      arg   Argument.
		 */
		propagate: function(event, arg) {
			_.each(this.collections, function(collection) {
				collection.trigger(event, arg);
			});
		}
	});

})( jQuery, window.wp, _ );
