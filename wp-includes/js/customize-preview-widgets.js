(function( wp, $ ){

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

			this.preview.bind( 'highlight-widget', self.highlightWidget );
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
				widgetClasses = emptyWidget.prop('className');

				// Prevent a rare case when before_widget, before_title, after_title and after_widget is empty.
				if ( ! widgetClasses ) {
					return;
				}

				widgetClasses = widgetClasses.replace(/^\s+|\s+$/g, '');

				if ( widgetClasses ) {
					widgetSelector += '.' + widgetClasses.split(/\s+/).join('.');
				}
				self.widgetSelectors.push(widgetSelector);
			});
		},

		/**
		 * Highlight the widget on widget updates or widget control mouse overs.
		 *
		 * @param  {string} widgetId ID of the widget.
		 */
		highlightWidget: function( widgetId ) {
			var $body = $( document.body ),
				$widget = $( '#' + widgetId );

			$body.find( '.widget-customizer-highlighted-widget' ).removeClass( 'widget-customizer-highlighted-widget' );

			$widget.addClass( 'widget-customizer-highlighted-widget' );
			setTimeout( function () {
				$widget.removeClass( 'widget-customizer-highlighted-widget' );
			}, 500 );
		},

		/**
		 * Show a title and highlight widgets on hover. On shift+clicking
		 * focus the widget control.
		 */
		highlightControls: function() {
			var self = this,
				selector = this.widgetSelectors.join(',');

			$(selector).attr( 'title', this.l10n.widgetTooltip );

			$(document).on( 'mouseenter', selector, function () {
				self.preview.send( 'highlight-widget-control', $( this ).prop( 'id' ) );
			});

			// Open expand the widget control when shift+clicking the widget element
			$(document).on( 'click', selector, function ( e ) {
				if ( ! e.shiftKey ) {
					return;
				}
				e.preventDefault();

				self.preview.send( 'focus-widget-control', $( this ).prop( 'id' ) );
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

})( window.wp, jQuery );
