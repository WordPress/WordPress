(function( $, wp ){

	if ( ! wp || ! wp.customize ) { return; }

	var api = wp.customize,
		OldPreview;

	/**
	 * wp.customize.WidgetCustomizerPreview
	 *
	 */
	api.WidgetCustomizerPreview = {
		renderedSidebars: {}, // @todo Make rendered a property of the Backbone model
		renderedWidgets: {}, // @todo Make rendered a property of the Backbone model
		registeredSidebars: [], // @todo Make a Backbone collection
		registeredWidgets: {}, // @todo Make array, Backbone collection
		widgetSelectors: [],
		preview: null,
		l10n: {},

		init: function () {
			var self = this;
			this.buildWidgetSelectors();
			this.highlightControls();

			this.preview.bind( 'active', function() {
				self.preview.send( 'rendered-sidebars', self.renderedSidebars ); // @todo Only send array of IDs
				self.preview.send( 'rendered-widgets', self.renderedWidgets ); // @todo Only send array of IDs
			} );
		},

		/**
		 * Calculate the selector for the sidebar's widgets based on the registered sidebar's info
		 */
		buildWidgetSelectors: function () {
			var self = this;

			$.each( this.registeredSidebars, function ( i, sidebar ) {
				var widgetTpl = [
						sidebar.before_widget.replace('%1$s', '').replace('%2$s', ''),
						sidebar.before_title,
						sidebar.after_title,
						sidebar.after_widget
					].join(''),
					emptyWidget,
					widgetSelector,
					widgetClasses;

				emptyWidget = $(widgetTpl);
				widgetSelector = emptyWidget.prop('tagName');
				widgetClasses = emptyWidget.prop('className').replace(/^\s+|\s+$/g, '');

				if ( widgetClasses ) {
					widgetSelector += '.' + widgetClasses.split(/\s+/).join('.');
				}
				self.widgetSelectors.push(widgetSelector);
			});
		},

		/**
		 * Obtain a rendered widget element. Assumes standard practice of using
		 * the widget ID as the ID for the DOM element. To eliminate this
		 * assumption, additional data-* attributes would need to be injected
		 * onto the rendered widget root element.
		 *
		 * @param {String} widget_id
		 * @return {jQuery}
		 */
		getWidgetElement: function ( widget_id ) {
			return $( '#' + widget_id );
		},

		/**
		 *
		 */
		highlightControls: function() {
			var selector = this.widgetSelectors.join(',');

			$(selector).attr( 'title', this.l10n.widgetTooltip );

			$(document).on( 'mouseenter', selector, function () {
				var control = parent.WidgetCustomizer.getWidgetFormControlForWidget( $(this).prop('id') );
				if ( control ) {
					control.highlightSectionAndControl();
				}
			});

			// Open expand the widget control when shift+clicking the widget element
			$(document).on( 'click', selector, function ( e ) {
				if ( ! e.shiftKey ) {
					return;
				}
				e.preventDefault();
				var control = parent.WidgetCustomizer.getWidgetFormControlForWidget( $(this).prop('id') );
				if ( control ) {
					control.focus();
				}
			});
		}
	};

	/**
	 * Capture the instance of the Preview since it is private
	 */
	OldPreview = api.Preview;
	api.Preview = OldPreview.extend( {
		initialize: function( params, options ) {
			api.WidgetCustomizerPreview.preview = this;
			OldPreview.prototype.initialize.call( this, params, options );
		}
	} );

	$(function () {
		var settings = window._wpWidgetCustomizerPreviewSettings;
		if ( ! settings ) {
			return;
		}

		$.extend( api.WidgetCustomizerPreview, settings );

		api.WidgetCustomizerPreview.init();
	});

})( jQuery, window.wp );
