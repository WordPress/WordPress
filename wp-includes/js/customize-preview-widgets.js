/*global jQuery, WidgetCustomizerPreview_exports */
/*exported WidgetCustomizerPreview */
var WidgetCustomizerPreview = (function ($) {
	'use strict';

	var OldPreview, self = {
		rendered_sidebars: {}, // @todo Make rendered a property of the Backbone model
		rendered_widgets: {}, // @todo Make rendered a property of the Backbone model
		registered_sidebars: [], // @todo Make a Backbone collection
		registered_widgets: {}, // @todo Make array, Backbone collection
		widget_selectors: [],
		render_widget_ajax_action: null,
		render_widget_nonce_value: null,
		render_widget_nonce_post_key: null,
		preview: null,
		i18n: {},

		init: function () {
			this.buildWidgetSelectors();
			this.highlightControls();

			self.preview.bind( 'active', function() {
				self.preview.send( 'rendered-sidebars', self.rendered_sidebars ); // @todo Only send array of IDs
				self.preview.send( 'rendered-widgets', self.rendered_widgets ); // @todo Only send array of IDs
			} );
		},

		/**
		 * Calculate the selector for the sidebar's widgets based on the registered sidebar's info
		 */
		buildWidgetSelectors: function () {
			$.each( self.registered_sidebars, function ( i, sidebar ) {
				var widget_tpl = [
						sidebar.before_widget.replace('%1$s', '').replace('%2$s', ''),
						sidebar.before_title,
						sidebar.after_title,
						sidebar.after_widget
					].join(''),
					empty_widget,
					widget_selector,
					widget_classes;

				empty_widget = $(widget_tpl);
				widget_selector = empty_widget.prop('tagName');
				widget_classes = empty_widget.prop('className').replace(/^\s+|\s+$/g, '');

				if ( widget_classes ) {
					widget_selector += '.' + widget_classes.split(/\s+/).join('.');
				}
				self.widget_selectors.push(widget_selector);
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

			var selector = this.widget_selectors.join(',');

			$(selector).attr( 'title', self.i18n.widget_tooltip );

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

	$.extend(self, WidgetCustomizerPreview_exports);

	/**
	 * Capture the instance of the Preview since it is private
	 */
	OldPreview = wp.customize.Preview;
	wp.customize.Preview = OldPreview.extend( {
		initialize: function( params, options ) {
			self.preview = this;
			OldPreview.prototype.initialize.call( this, params, options );
		}
	} );

	// @todo on customize ready?
	$(function () {
		self.init();
	});

	return self;
}( jQuery ));
