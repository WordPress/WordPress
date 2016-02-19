/* global _wpCustomizeWidgetsSettings */
(function( wp, $ ){

	if ( ! wp || ! wp.customize ) { return; }

	// Set up our namespace...
	var api = wp.customize,
		l10n;

	api.Widgets = api.Widgets || {};
	api.Widgets.savedWidgetIds = {};

	// Link settings
	api.Widgets.data = _wpCustomizeWidgetsSettings || {};
	l10n = api.Widgets.data.l10n;
	delete api.Widgets.data.l10n;

	/**
	 * wp.customize.Widgets.WidgetModel
	 *
	 * A single widget model.
	 *
	 * @constructor
	 * @augments Backbone.Model
	 */
	api.Widgets.WidgetModel = Backbone.Model.extend({
		id: null,
		temp_id: null,
		classname: null,
		control_tpl: null,
		description: null,
		is_disabled: null,
		is_multi: null,
		multi_number: null,
		name: null,
		id_base: null,
		transport: api.Widgets.data.selectiveRefresh ? 'postMessage' : 'refresh',
		params: [],
		width: null,
		height: null,
		search_matched: true
	});

	/**
	 * wp.customize.Widgets.WidgetCollection
	 *
	 * Collection for widget models.
	 *
	 * @constructor
	 * @augments Backbone.Model
	 */
	api.Widgets.WidgetCollection = Backbone.Collection.extend({
		model: api.Widgets.WidgetModel,

		// Controls searching on the current widget collection
		// and triggers an update event
		doSearch: function( value ) {

			// Don't do anything if we've already done this search
			// Useful because the search handler fires multiple times per keystroke
			if ( this.terms === value ) {
				return;
			}

			// Updates terms with the value passed
			this.terms = value;

			// If we have terms, run a search...
			if ( this.terms.length > 0 ) {
				this.search( this.terms );
			}

			// If search is blank, show all themes
			// Useful for resetting the views when you clean the input
			if ( this.terms === '' ) {
				this.each( function ( widget ) {
					widget.set( 'search_matched', true );
				} );
			}
		},

		// Performs a search within the collection
		// @uses RegExp
		search: function( term ) {
			var match, haystack;

			// Escape the term string for RegExp meta characters
			term = term.replace( /[-\/\\^$*+?.()|[\]{}]/g, '\\$&' );

			// Consider spaces as word delimiters and match the whole string
			// so matching terms can be combined
			term = term.replace( / /g, ')(?=.*' );
			match = new RegExp( '^(?=.*' + term + ').+', 'i' );

			this.each( function ( data ) {
				haystack = [ data.get( 'name' ), data.get( 'id' ), data.get( 'description' ) ].join( ' ' );
				data.set( 'search_matched', match.test( haystack ) );
			} );
		}
	});
	api.Widgets.availableWidgets = new api.Widgets.WidgetCollection( api.Widgets.data.availableWidgets );

	/**
	 * wp.customize.Widgets.SidebarModel
	 *
	 * A single sidebar model.
	 *
	 * @constructor
	 * @augments Backbone.Model
	 */
	api.Widgets.SidebarModel = Backbone.Model.extend({
		after_title: null,
		after_widget: null,
		before_title: null,
		before_widget: null,
		'class': null,
		description: null,
		id: null,
		name: null,
		is_rendered: false
	});

	/**
	 * wp.customize.Widgets.SidebarCollection
	 *
	 * Collection for sidebar models.
	 *
	 * @constructor
	 * @augments Backbone.Collection
	 */
	api.Widgets.SidebarCollection = Backbone.Collection.extend({
		model: api.Widgets.SidebarModel
	});
	api.Widgets.registeredSidebars = new api.Widgets.SidebarCollection( api.Widgets.data.registeredSidebars );

	/**
	 * wp.customize.Widgets.AvailableWidgetsPanelView
	 *
	 * View class for the available widgets panel.
	 *
	 * @constructor
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	api.Widgets.AvailableWidgetsPanelView = wp.Backbone.View.extend({

		el: '#available-widgets',

		events: {
			'input #widgets-search': 'search',
			'keyup #widgets-search': 'search',
			'change #widgets-search': 'search',
			'search #widgets-search': 'search',
			'focus .widget-tpl' : 'focus',
			'click .widget-tpl' : '_submit',
			'keypress .widget-tpl' : '_submit',
			'keydown' : 'keyboardAccessible'
		},

		// Cache current selected widget
		selected: null,

		// Cache sidebar control which has opened panel
		currentSidebarControl: null,
		$search: null,

		initialize: function() {
			var self = this;

			this.$search = $( '#widgets-search' );

			_.bindAll( this, 'close' );

			this.listenTo( this.collection, 'change', this.updateList );

			this.updateList();

			// If the available widgets panel is open and the customize controls are
			// interacted with (i.e. available widgets panel is blurred) then close the
			// available widgets panel. Also close on back button click.
			$( '#customize-controls, #available-widgets .customize-section-title' ).on( 'click keydown', function( e ) {
				var isAddNewBtn = $( e.target ).is( '.add-new-widget, .add-new-widget *' );
				if ( $( 'body' ).hasClass( 'adding-widget' ) && ! isAddNewBtn ) {
					self.close();
				}
			} );

			// Close the panel if the URL in the preview changes
			api.previewer.bind( 'url', this.close );
		},

		// Performs a search and handles selected widget
		search: function( event ) {
			var firstVisible;

			this.collection.doSearch( event.target.value );

			// Remove a widget from being selected if it is no longer visible
			if ( this.selected && ! this.selected.is( ':visible' ) ) {
				this.selected.removeClass( 'selected' );
				this.selected = null;
			}

			// If a widget was selected but the filter value has been cleared out, clear selection
			if ( this.selected && ! event.target.value ) {
				this.selected.removeClass( 'selected' );
				this.selected = null;
			}

			// If a filter has been entered and a widget hasn't been selected, select the first one shown
			if ( ! this.selected && event.target.value ) {
				firstVisible = this.$el.find( '> .widget-tpl:visible:first' );
				if ( firstVisible.length ) {
					this.select( firstVisible );
				}
			}
		},

		// Changes visibility of available widgets
		updateList: function() {
			this.collection.each( function( widget ) {
				var widgetTpl = $( '#widget-tpl-' + widget.id );
				widgetTpl.toggle( widget.get( 'search_matched' ) && ! widget.get( 'is_disabled' ) );
				if ( widget.get( 'is_disabled' ) && widgetTpl.is( this.selected ) ) {
					this.selected = null;
				}
			} );
		},

		// Highlights a widget
		select: function( widgetTpl ) {
			this.selected = $( widgetTpl );
			this.selected.siblings( '.widget-tpl' ).removeClass( 'selected' );
			this.selected.addClass( 'selected' );
		},

		// Highlights a widget on focus
		focus: function( event ) {
			this.select( $( event.currentTarget ) );
		},

		// Submit handler for keypress and click on widget
		_submit: function( event ) {
			// Only proceed with keypress if it is Enter or Spacebar
			if ( event.type === 'keypress' && ( event.which !== 13 && event.which !== 32 ) ) {
				return;
			}

			this.submit( $( event.currentTarget ) );
		},

		// Adds a selected widget to the sidebar
		submit: function( widgetTpl ) {
			var widgetId, widget, widgetFormControl;

			if ( ! widgetTpl ) {
				widgetTpl = this.selected;
			}

			if ( ! widgetTpl || ! this.currentSidebarControl ) {
				return;
			}

			this.select( widgetTpl );

			widgetId = $( this.selected ).data( 'widget-id' );
			widget = this.collection.findWhere( { id: widgetId } );
			if ( ! widget ) {
				return;
			}

			widgetFormControl = this.currentSidebarControl.addWidget( widget.get( 'id_base' ) );
			if ( widgetFormControl ) {
				widgetFormControl.focus();
			}

			this.close();
		},

		// Opens the panel
		open: function( sidebarControl ) {
			this.currentSidebarControl = sidebarControl;

			// Wide widget controls appear over the preview, and so they need to be collapsed when the panel opens
			_( this.currentSidebarControl.getWidgetFormControls() ).each( function( control ) {
				if ( control.params.is_wide ) {
					control.collapseForm();
				}
			} );

			$( 'body' ).addClass( 'adding-widget' );

			this.$el.find( '.selected' ).removeClass( 'selected' );

			// Reset search
			this.collection.doSearch( '' );

			if ( ! api.settings.browser.mobile ) {
				this.$search.focus();
			}
		},

		// Closes the panel
		close: function( options ) {
			options = options || {};

			if ( options.returnFocus && this.currentSidebarControl ) {
				this.currentSidebarControl.container.find( '.add-new-widget' ).focus();
			}

			this.currentSidebarControl = null;
			this.selected = null;

			$( 'body' ).removeClass( 'adding-widget' );

			this.$search.val( '' );
		},

		// Add keyboard accessiblity to the panel
		keyboardAccessible: function( event ) {
			var isEnter = ( event.which === 13 ),
				isEsc = ( event.which === 27 ),
				isDown = ( event.which === 40 ),
				isUp = ( event.which === 38 ),
				isTab = ( event.which === 9 ),
				isShift = ( event.shiftKey ),
				selected = null,
				firstVisible = this.$el.find( '> .widget-tpl:visible:first' ),
				lastVisible = this.$el.find( '> .widget-tpl:visible:last' ),
				isSearchFocused = $( event.target ).is( this.$search ),
				isLastWidgetFocused = $( event.target ).is( '.widget-tpl:visible:last' );

			if ( isDown || isUp ) {
				if ( isDown ) {
					if ( isSearchFocused ) {
						selected = firstVisible;
					} else if ( this.selected && this.selected.nextAll( '.widget-tpl:visible' ).length !== 0 ) {
						selected = this.selected.nextAll( '.widget-tpl:visible:first' );
					}
				} else if ( isUp ) {
					if ( isSearchFocused ) {
						selected = lastVisible;
					} else if ( this.selected && this.selected.prevAll( '.widget-tpl:visible' ).length !== 0 ) {
						selected = this.selected.prevAll( '.widget-tpl:visible:first' );
					}
				}

				this.select( selected );

				if ( selected ) {
					selected.focus();
				} else {
					this.$search.focus();
				}

				return;
			}

			// If enter pressed but nothing entered, don't do anything
			if ( isEnter && ! this.$search.val() ) {
				return;
			}

			if ( isEnter ) {
				this.submit();
			} else if ( isEsc ) {
				this.close( { returnFocus: true } );
			}

			if ( this.currentSidebarControl && isTab && ( isShift && isSearchFocused || ! isShift && isLastWidgetFocused ) ) {
				this.currentSidebarControl.container.find( '.add-new-widget' ).focus();
				event.preventDefault();
			}
		}
	});

	/**
	 * Handlers for the widget-synced event, organized by widget ID base.
	 * Other widgets may provide their own update handlers by adding
	 * listeners for the widget-synced event.
	 */
	api.Widgets.formSyncHandlers = {

		/**
		 * @param {jQuery.Event} e
		 * @param {jQuery} widget
		 * @param {String} newForm
		 */
		rss: function( e, widget, newForm ) {
			var oldWidgetError = widget.find( '.widget-error:first' ),
				newWidgetError = $( '<div>' + newForm + '</div>' ).find( '.widget-error:first' );

			if ( oldWidgetError.length && newWidgetError.length ) {
				oldWidgetError.replaceWith( newWidgetError );
			} else if ( oldWidgetError.length ) {
				oldWidgetError.remove();
			} else if ( newWidgetError.length ) {
				widget.find( '.widget-content:first' ).prepend( newWidgetError );
			}
		}
	};

	/**
	 * wp.customize.Widgets.WidgetControl
	 *
	 * Customizer control for widgets.
	 * Note that 'widget_form' must match the WP_Widget_Form_Customize_Control::$type
	 *
	 * @constructor
	 * @augments wp.customize.Control
	 */
	api.Widgets.WidgetControl = api.Control.extend({
		defaultExpandedArguments: {
			duration: 'fast',
			completeCallback: $.noop
		},

		/**
		 * @since 4.1.0
		 */
		initialize: function( id, options ) {
			var control = this;

			control.widgetControlEmbedded = false;
			control.widgetContentEmbedded = false;
			control.expanded = new api.Value( false );
			control.expandedArgumentsQueue = [];
			control.expanded.bind( function( expanded ) {
				var args = control.expandedArgumentsQueue.shift();
				args = $.extend( {}, control.defaultExpandedArguments, args );
				control.onChangeExpanded( expanded, args );
			});

			api.Control.prototype.initialize.call( control, id, options );
		},

		/**
		 * Set up the control.
		 *
		 * @since 3.9.0
		 */
		ready: function() {
			var control = this;

			/*
			 * Embed a placeholder once the section is expanded. The full widget
			 * form content will be embedded once the control itself is expanded,
			 * and at this point the widget-added event will be triggered.
			 */
			if ( ! control.section() ) {
				control.embedWidgetControl();
			} else {
				api.section( control.section(), function( section ) {
					var onExpanded = function( isExpanded ) {
						if ( isExpanded ) {
							control.embedWidgetControl();
							section.expanded.unbind( onExpanded );
						}
					};
					if ( section.expanded() ) {
						onExpanded( true );
					} else {
						section.expanded.bind( onExpanded );
					}
				} );
			}
		},

		/**
		 * Embed the .widget element inside the li container.
		 *
		 * @since 4.4.0
		 */
		embedWidgetControl: function() {
			var control = this, widgetControl;

			if ( control.widgetControlEmbedded ) {
				return;
			}
			control.widgetControlEmbedded = true;

			widgetControl = $( control.params.widget_control );
			control.container.append( widgetControl );

			control._setupModel();
			control._setupWideWidget();
			control._setupControlToggle();

			control._setupWidgetTitle();
			control._setupReorderUI();
			control._setupHighlightEffects();
			control._setupUpdateUI();
			control._setupRemoveUI();
		},

		/**
		 * Embed the actual widget form inside of .widget-content and finally trigger the widget-added event.
		 *
		 * @since 4.4.0
		 */
		embedWidgetContent: function() {
			var control = this, widgetContent;

			control.embedWidgetControl();
			if ( control.widgetContentEmbedded ) {
				return;
			}
			control.widgetContentEmbedded = true;

			widgetContent = $( control.params.widget_content );
			control.container.find( '.widget-content:first' ).append( widgetContent );

			/*
			 * Trigger widget-added event so that plugins can attach any event
			 * listeners and dynamic UI elements.
			 */
			$( document ).trigger( 'widget-added', [ control.container.find( '.widget:first' ) ] );

		},

		/**
		 * Handle changes to the setting
		 */
		_setupModel: function() {
			var self = this, rememberSavedWidgetId;

			// Remember saved widgets so we know which to trash (move to inactive widgets sidebar)
			rememberSavedWidgetId = function() {
				api.Widgets.savedWidgetIds[self.params.widget_id] = true;
			};
			api.bind( 'ready', rememberSavedWidgetId );
			api.bind( 'saved', rememberSavedWidgetId );

			this._updateCount = 0;
			this.isWidgetUpdating = false;
			this.liveUpdateMode = true;

			// Update widget whenever model changes
			this.setting.bind( function( to, from ) {
				if ( ! _( from ).isEqual( to ) && ! self.isWidgetUpdating ) {
					self.updateWidget( { instance: to } );
				}
			} );
		},

		/**
		 * Add special behaviors for wide widget controls
		 */
		_setupWideWidget: function() {
			var self = this, $widgetInside, $widgetForm, $customizeSidebar,
				$themeControlsContainer, positionWidget;

			if ( ! this.params.is_wide ) {
				return;
			}

			$widgetInside = this.container.find( '.widget-inside' );
			$widgetForm = $widgetInside.find( '> .form' );
			$customizeSidebar = $( '.wp-full-overlay-sidebar-content:first' );
			this.container.addClass( 'wide-widget-control' );

			this.container.find( '.widget-content:first' ).css( {
				'max-width': this.params.width,
				'min-height': this.params.height
			} );

			/**
			 * Keep the widget-inside positioned so the top of fixed-positioned
			 * element is at the same top position as the widget-top. When the
			 * widget-top is scrolled out of view, keep the widget-top in view;
			 * likewise, don't allow the widget to drop off the bottom of the window.
			 * If a widget is too tall to fit in the window, don't let the height
			 * exceed the window height so that the contents of the widget control
			 * will become scrollable (overflow:auto).
			 */
			positionWidget = function() {
				var offsetTop = self.container.offset().top,
					windowHeight = $( window ).height(),
					formHeight = $widgetForm.outerHeight(),
					top;
				$widgetInside.css( 'max-height', windowHeight );
				top = Math.max(
					0, // prevent top from going off screen
					Math.min(
						Math.max( offsetTop, 0 ), // distance widget in panel is from top of screen
						windowHeight - formHeight // flush up against bottom of screen
					)
				);
				$widgetInside.css( 'top', top );
			};

			$themeControlsContainer = $( '#customize-theme-controls' );
			this.container.on( 'expand', function() {
				positionWidget();
				$customizeSidebar.on( 'scroll', positionWidget );
				$( window ).on( 'resize', positionWidget );
				$themeControlsContainer.on( 'expanded collapsed', positionWidget );
			} );
			this.container.on( 'collapsed', function() {
				$customizeSidebar.off( 'scroll', positionWidget );
				$( window ).off( 'resize', positionWidget );
				$themeControlsContainer.off( 'expanded collapsed', positionWidget );
			} );

			// Reposition whenever a sidebar's widgets are changed
			api.each( function( setting ) {
				if ( 0 === setting.id.indexOf( 'sidebars_widgets[' ) ) {
					setting.bind( function() {
						if ( self.container.hasClass( 'expanded' ) ) {
							positionWidget();
						}
					} );
				}
			} );
		},

		/**
		 * Show/hide the control when clicking on the form title, when clicking
		 * the close button
		 */
		_setupControlToggle: function() {
			var self = this, $closeBtn;

			this.container.find( '.widget-top' ).on( 'click', function( e ) {
				e.preventDefault();
				var sidebarWidgetsControl = self.getSidebarWidgetsControl();
				if ( sidebarWidgetsControl.isReordering ) {
					return;
				}
				self.expanded( ! self.expanded() );
			} );

			$closeBtn = this.container.find( '.widget-control-close' );
			$closeBtn.on( 'click', function( e ) {
				e.preventDefault();
				self.collapse();
				self.container.find( '.widget-top .widget-action:first' ).focus(); // keyboard accessibility
			} );
		},

		/**
		 * Update the title of the form if a title field is entered
		 */
		_setupWidgetTitle: function() {
			var self = this, updateTitle;

			updateTitle = function() {
				var title = self.setting().title,
					inWidgetTitle = self.container.find( '.in-widget-title' );

				if ( title ) {
					inWidgetTitle.text( ': ' + title );
				} else {
					inWidgetTitle.text( '' );
				}
			};
			this.setting.bind( updateTitle );
			updateTitle();
		},

		/**
		 * Set up the widget-reorder-nav
		 */
		_setupReorderUI: function() {
			var self = this, selectSidebarItem, $moveWidgetArea,
				$reorderNav, updateAvailableSidebars, template;

			/**
			 * select the provided sidebar list item in the move widget area
			 *
			 * @param {jQuery} li
			 */
			selectSidebarItem = function( li ) {
				li.siblings( '.selected' ).removeClass( 'selected' );
				li.addClass( 'selected' );
				var isSelfSidebar = ( li.data( 'id' ) === self.params.sidebar_id );
				self.container.find( '.move-widget-btn' ).prop( 'disabled', isSelfSidebar );
			};

			/**
			 * Add the widget reordering elements to the widget control
			 */
			this.container.find( '.widget-title-action' ).after( $( api.Widgets.data.tpl.widgetReorderNav ) );


			template = _.template( api.Widgets.data.tpl.moveWidgetArea );
			$moveWidgetArea = $( template( {
					sidebars: _( api.Widgets.registeredSidebars.toArray() ).pluck( 'attributes' )
				} )
			);
			this.container.find( '.widget-top' ).after( $moveWidgetArea );

			/**
			 * Update available sidebars when their rendered state changes
			 */
			updateAvailableSidebars = function() {
				var $sidebarItems = $moveWidgetArea.find( 'li' ), selfSidebarItem,
					renderedSidebarCount = 0;

				selfSidebarItem = $sidebarItems.filter( function(){
					return $( this ).data( 'id' ) === self.params.sidebar_id;
				} );

				$sidebarItems.each( function() {
					var li = $( this ),
						sidebarId, sidebar, sidebarIsRendered;

					sidebarId = li.data( 'id' );
					sidebar = api.Widgets.registeredSidebars.get( sidebarId );
					sidebarIsRendered = sidebar.get( 'is_rendered' );

					li.toggle( sidebarIsRendered );

					if ( sidebarIsRendered ) {
						renderedSidebarCount += 1;
					}

					if ( li.hasClass( 'selected' ) && ! sidebarIsRendered ) {
						selectSidebarItem( selfSidebarItem );
					}
				} );

				if ( renderedSidebarCount > 1 ) {
					self.container.find( '.move-widget' ).show();
				} else {
					self.container.find( '.move-widget' ).hide();
				}
			};

			updateAvailableSidebars();
			api.Widgets.registeredSidebars.on( 'change:is_rendered', updateAvailableSidebars );

			/**
			 * Handle clicks for up/down/move on the reorder nav
			 */
			$reorderNav = this.container.find( '.widget-reorder-nav' );
			$reorderNav.find( '.move-widget, .move-widget-down, .move-widget-up' ).each( function() {
				$( this ).prepend( self.container.find( '.widget-title' ).text() + ': ' );
			} ).on( 'click keypress', function( event ) {
				if ( event.type === 'keypress' && ( event.which !== 13 && event.which !== 32 ) ) {
					return;
				}
				$( this ).focus();

				if ( $( this ).is( '.move-widget' ) ) {
					self.toggleWidgetMoveArea();
				} else {
					var isMoveDown = $( this ).is( '.move-widget-down' ),
						isMoveUp = $( this ).is( '.move-widget-up' ),
						i = self.getWidgetSidebarPosition();

					if ( ( isMoveUp && i === 0 ) || ( isMoveDown && i === self.getSidebarWidgetsControl().setting().length - 1 ) ) {
						return;
					}

					if ( isMoveUp ) {
						self.moveUp();
						wp.a11y.speak( l10n.widgetMovedUp );
					} else {
						self.moveDown();
						wp.a11y.speak( l10n.widgetMovedDown );
					}

					$( this ).focus(); // re-focus after the container was moved
				}
			} );

			/**
			 * Handle selecting a sidebar to move to
			 */
			this.container.find( '.widget-area-select' ).on( 'click keypress', 'li', function( event ) {
				if ( event.type === 'keypress' && ( event.which !== 13 && event.which !== 32 ) ) {
					return;
				}
				event.preventDefault();
				selectSidebarItem( $( this ) );
			} );

			/**
			 * Move widget to another sidebar
			 */
			this.container.find( '.move-widget-btn' ).click( function() {
				self.getSidebarWidgetsControl().toggleReordering( false );

				var oldSidebarId = self.params.sidebar_id,
					newSidebarId = self.container.find( '.widget-area-select li.selected' ).data( 'id' ),
					oldSidebarWidgetsSetting, newSidebarWidgetsSetting,
					oldSidebarWidgetIds, newSidebarWidgetIds, i;

				oldSidebarWidgetsSetting = api( 'sidebars_widgets[' + oldSidebarId + ']' );
				newSidebarWidgetsSetting = api( 'sidebars_widgets[' + newSidebarId + ']' );
				oldSidebarWidgetIds = Array.prototype.slice.call( oldSidebarWidgetsSetting() );
				newSidebarWidgetIds = Array.prototype.slice.call( newSidebarWidgetsSetting() );

				i = self.getWidgetSidebarPosition();
				oldSidebarWidgetIds.splice( i, 1 );
				newSidebarWidgetIds.push( self.params.widget_id );

				oldSidebarWidgetsSetting( oldSidebarWidgetIds );
				newSidebarWidgetsSetting( newSidebarWidgetIds );

				self.focus();
			} );
		},

		/**
		 * Highlight widgets in preview when interacted with in the Customizer
		 */
		_setupHighlightEffects: function() {
			var self = this;

			// Highlight whenever hovering or clicking over the form
			this.container.on( 'mouseenter click', function() {
				self.setting.previewer.send( 'highlight-widget', self.params.widget_id );
			} );

			// Highlight when the setting is updated
			this.setting.bind( function() {
				self.setting.previewer.send( 'highlight-widget', self.params.widget_id );
			} );
		},

		/**
		 * Set up event handlers for widget updating
		 */
		_setupUpdateUI: function() {
			var self = this, $widgetRoot, $widgetContent,
				$saveBtn, updateWidgetDebounced, formSyncHandler;

			$widgetRoot = this.container.find( '.widget:first' );
			$widgetContent = $widgetRoot.find( '.widget-content:first' );

			// Configure update button
			$saveBtn = this.container.find( '.widget-control-save' );
			$saveBtn.val( l10n.saveBtnLabel );
			$saveBtn.attr( 'title', l10n.saveBtnTooltip );
			$saveBtn.removeClass( 'button-primary' ).addClass( 'button-secondary' );
			$saveBtn.on( 'click', function( e ) {
				e.preventDefault();
				self.updateWidget( { disable_form: true } ); // @todo disable_form is unused?
			} );

			updateWidgetDebounced = _.debounce( function() {
				self.updateWidget();
			}, 250 );

			// Trigger widget form update when hitting Enter within an input
			$widgetContent.on( 'keydown', 'input', function( e ) {
				if ( 13 === e.which ) { // Enter
					e.preventDefault();
					self.updateWidget( { ignoreActiveElement: true } );
				}
			} );

			// Handle widgets that support live previews
			$widgetContent.on( 'change input propertychange', ':input', function( e ) {
				if ( ! self.liveUpdateMode ) {
					return;
				}
				if ( e.type === 'change' || ( this.checkValidity && this.checkValidity() ) ) {
					updateWidgetDebounced();
				}
			} );

			// Remove loading indicators when the setting is saved and the preview updates
			this.setting.previewer.channel.bind( 'synced', function() {
				self.container.removeClass( 'previewer-loading' );
			} );

			api.previewer.bind( 'widget-updated', function( updatedWidgetId ) {
				if ( updatedWidgetId === self.params.widget_id ) {
					self.container.removeClass( 'previewer-loading' );
				}
			} );

			formSyncHandler = api.Widgets.formSyncHandlers[ this.params.widget_id_base ];
			if ( formSyncHandler ) {
				$( document ).on( 'widget-synced', function( e, widget ) {
					if ( $widgetRoot.is( widget ) ) {
						formSyncHandler.apply( document, arguments );
					}
				} );
			}
		},

		/**
		 * Update widget control to indicate whether it is currently rendered.
		 *
		 * Overrides api.Control.toggle()
		 *
		 * @since 4.1.0
		 *
		 * @param {Boolean}   active
		 * @param {Object}    args
		 * @param {Callback}  args.completeCallback
		 */
		onChangeActive: function ( active, args ) {
			// Note: there is a second 'args' parameter being passed, merged on top of this.defaultActiveArguments
			this.container.toggleClass( 'widget-rendered', active );
			if ( args.completeCallback ) {
				args.completeCallback();
			}
		},

		/**
		 * Set up event handlers for widget removal
		 */
		_setupRemoveUI: function() {
			var self = this, $removeBtn, replaceDeleteWithRemove;

			// Configure remove button
			$removeBtn = this.container.find( 'a.widget-control-remove' );
			$removeBtn.on( 'click', function( e ) {
				e.preventDefault();

				// Find an adjacent element to add focus to when this widget goes away
				var $adjacentFocusTarget;
				if ( self.container.next().is( '.customize-control-widget_form' ) ) {
					$adjacentFocusTarget = self.container.next().find( '.widget-action:first' );
				} else if ( self.container.prev().is( '.customize-control-widget_form' ) ) {
					$adjacentFocusTarget = self.container.prev().find( '.widget-action:first' );
				} else {
					$adjacentFocusTarget = self.container.next( '.customize-control-sidebar_widgets' ).find( '.add-new-widget:first' );
				}

				self.container.slideUp( function() {
					var sidebarsWidgetsControl = api.Widgets.getSidebarWidgetControlContainingWidget( self.params.widget_id ),
						sidebarWidgetIds, i;

					if ( ! sidebarsWidgetsControl ) {
						return;
					}

					sidebarWidgetIds = sidebarsWidgetsControl.setting().slice();
					i = _.indexOf( sidebarWidgetIds, self.params.widget_id );
					if ( -1 === i ) {
						return;
					}

					sidebarWidgetIds.splice( i, 1 );
					sidebarsWidgetsControl.setting( sidebarWidgetIds );

					$adjacentFocusTarget.focus(); // keyboard accessibility
				} );
			} );

			replaceDeleteWithRemove = function() {
				$removeBtn.text( l10n.removeBtnLabel ); // wp_widget_control() outputs the link as "Delete"
				$removeBtn.attr( 'title', l10n.removeBtnTooltip );
			};

			if ( this.params.is_new ) {
				api.bind( 'saved', replaceDeleteWithRemove );
			} else {
				replaceDeleteWithRemove();
			}
		},

		/**
		 * Find all inputs in a widget container that should be considered when
		 * comparing the loaded form with the sanitized form, whose fields will
		 * be aligned to copy the sanitized over. The elements returned by this
		 * are passed into this._getInputsSignature(), and they are iterated
		 * over when copying sanitized values over to the form loaded.
		 *
		 * @param {jQuery} container element in which to look for inputs
		 * @returns {jQuery} inputs
		 * @private
		 */
		_getInputs: function( container ) {
			return $( container ).find( ':input[name]' );
		},

		/**
		 * Iterate over supplied inputs and create a signature string for all of them together.
		 * This string can be used to compare whether or not the form has all of the same fields.
		 *
		 * @param {jQuery} inputs
		 * @returns {string}
		 * @private
		 */
		_getInputsSignature: function( inputs ) {
			var inputsSignatures = _( inputs ).map( function( input ) {
				var $input = $( input ), signatureParts;

				if ( $input.is( ':checkbox, :radio' ) ) {
					signatureParts = [ $input.attr( 'id' ), $input.attr( 'name' ), $input.prop( 'value' ) ];
				} else {
					signatureParts = [ $input.attr( 'id' ), $input.attr( 'name' ) ];
				}

				return signatureParts.join( ',' );
			} );

			return inputsSignatures.join( ';' );
		},

		/**
		 * Get the state for an input depending on its type.
		 *
		 * @param {jQuery|Element} input
		 * @returns {string|boolean|array|*}
		 * @private
		 */
		_getInputState: function( input ) {
			input = $( input );
			if ( input.is( ':radio, :checkbox' ) ) {
				return input.prop( 'checked' );
			} else if ( input.is( 'select[multiple]' ) ) {
				return input.find( 'option:selected' ).map( function () {
					return $( this ).val();
				} ).get();
			} else {
				return input.val();
			}
		},

		/**
		 * Update an input's state based on its type.
		 *
		 * @param {jQuery|Element} input
		 * @param {string|boolean|array|*} state
		 * @private
		 */
		_setInputState: function ( input, state ) {
			input = $( input );
			if ( input.is( ':radio, :checkbox' ) ) {
				input.prop( 'checked', state );
			} else if ( input.is( 'select[multiple]' ) ) {
				if ( ! $.isArray( state ) ) {
					state = [];
				} else {
					// Make sure all state items are strings since the DOM value is a string
					state = _.map( state, function ( value ) {
						return String( value );
					} );
				}
				input.find( 'option' ).each( function () {
					$( this ).prop( 'selected', -1 !== _.indexOf( state, String( this.value ) ) );
				} );
			} else {
				input.val( state );
			}
		},

		/***********************************************************************
		 * Begin public API methods
		 **********************************************************************/

		/**
		 * @return {wp.customize.controlConstructor.sidebar_widgets[]}
		 */
		getSidebarWidgetsControl: function() {
			var settingId, sidebarWidgetsControl;

			settingId = 'sidebars_widgets[' + this.params.sidebar_id + ']';
			sidebarWidgetsControl = api.control( settingId );

			if ( ! sidebarWidgetsControl ) {
				return;
			}

			return sidebarWidgetsControl;
		},

		/**
		 * Submit the widget form via Ajax and get back the updated instance,
		 * along with the new widget control form to render.
		 *
		 * @param {object} [args]
		 * @param {Object|null} [args.instance=null]  When the model changes, the instance is sent here; otherwise, the inputs from the form are used
		 * @param {Function|null} [args.complete=null]  Function which is called when the request finishes. Context is bound to the control. First argument is any error. Following arguments are for success.
		 * @param {Boolean} [args.ignoreActiveElement=false] Whether or not updating a field will be deferred if focus is still on the element.
		 */
		updateWidget: function( args ) {
			var self = this, instanceOverride, completeCallback, $widgetRoot, $widgetContent,
				updateNumber, params, data, $inputs, processing, jqxhr, isChanged;

			// The updateWidget logic requires that the form fields to be fully present.
			self.embedWidgetContent();

			args = $.extend( {
				instance: null,
				complete: null,
				ignoreActiveElement: false
			}, args );

			instanceOverride = args.instance;
			completeCallback = args.complete;

			this._updateCount += 1;
			updateNumber = this._updateCount;

			$widgetRoot = this.container.find( '.widget:first' );
			$widgetContent = $widgetRoot.find( '.widget-content:first' );

			// Remove a previous error message
			$widgetContent.find( '.widget-error' ).remove();

			this.container.addClass( 'widget-form-loading' );
			this.container.addClass( 'previewer-loading' );
			processing = api.state( 'processing' );
			processing( processing() + 1 );

			if ( ! this.liveUpdateMode ) {
				this.container.addClass( 'widget-form-disabled' );
			}

			params = {};
			params.action = 'update-widget';
			params.wp_customize = 'on';
			params.nonce = api.settings.nonce['update-widget'];
			params.theme = api.settings.theme.stylesheet;
			params.customized = wp.customize.previewer.query().customized;

			data = $.param( params );
			$inputs = this._getInputs( $widgetContent );

			// Store the value we're submitting in data so that when the response comes back,
			// we know if it got sanitized; if there is no difference in the sanitized value,
			// then we do not need to touch the UI and mess up the user's ongoing editing.
			$inputs.each( function() {
				$( this ).data( 'state' + updateNumber, self._getInputState( this ) );
			} );

			if ( instanceOverride ) {
				data += '&' + $.param( { 'sanitized_widget_setting': JSON.stringify( instanceOverride ) } );
			} else {
				data += '&' + $inputs.serialize();
			}
			data += '&' + $widgetContent.find( '~ :input' ).serialize();

			if ( this._previousUpdateRequest ) {
				this._previousUpdateRequest.abort();
			}
			jqxhr = $.post( wp.ajax.settings.url, data );
			this._previousUpdateRequest = jqxhr;

			jqxhr.done( function( r ) {
				var message, sanitizedForm,	$sanitizedInputs, hasSameInputsInResponse,
					isLiveUpdateAborted = false;

				// Check if the user is logged out.
				if ( '0' === r ) {
					api.previewer.preview.iframe.hide();
					api.previewer.login().done( function() {
						self.updateWidget( args );
						api.previewer.preview.iframe.show();
					} );
					return;
				}

				// Check for cheaters.
				if ( '-1' === r ) {
					api.previewer.cheatin();
					return;
				}

				if ( r.success ) {
					sanitizedForm = $( '<div>' + r.data.form + '</div>' );
					$sanitizedInputs = self._getInputs( sanitizedForm );
					hasSameInputsInResponse = self._getInputsSignature( $inputs ) === self._getInputsSignature( $sanitizedInputs );

					// Restore live update mode if sanitized fields are now aligned with the existing fields
					if ( hasSameInputsInResponse && ! self.liveUpdateMode ) {
						self.liveUpdateMode = true;
						self.container.removeClass( 'widget-form-disabled' );
						self.container.find( 'input[name="savewidget"]' ).hide();
					}

					// Sync sanitized field states to existing fields if they are aligned
					if ( hasSameInputsInResponse && self.liveUpdateMode ) {
						$inputs.each( function( i ) {
							var $input = $( this ),
								$sanitizedInput = $( $sanitizedInputs[i] ),
								submittedState, sanitizedState,	canUpdateState;

							submittedState = $input.data( 'state' + updateNumber );
							sanitizedState = self._getInputState( $sanitizedInput );
							$input.data( 'sanitized', sanitizedState );

							canUpdateState = ( ! _.isEqual( submittedState, sanitizedState ) && ( args.ignoreActiveElement || ! $input.is( document.activeElement ) ) );
							if ( canUpdateState ) {
								self._setInputState( $input, sanitizedState );
							}
						} );

						$( document ).trigger( 'widget-synced', [ $widgetRoot, r.data.form ] );

					// Otherwise, if sanitized fields are not aligned with existing fields, disable live update mode if enabled
					} else if ( self.liveUpdateMode ) {
						self.liveUpdateMode = false;
						self.container.find( 'input[name="savewidget"]' ).show();
						isLiveUpdateAborted = true;

					// Otherwise, replace existing form with the sanitized form
					} else {
						$widgetContent.html( r.data.form );

						self.container.removeClass( 'widget-form-disabled' );

						$( document ).trigger( 'widget-updated', [ $widgetRoot ] );
					}

					/**
					 * If the old instance is identical to the new one, there is nothing new
					 * needing to be rendered, and so we can preempt the event for the
					 * preview finishing loading.
					 */
					isChanged = ! isLiveUpdateAborted && ! _( self.setting() ).isEqual( r.data.instance );
					if ( isChanged ) {
						self.isWidgetUpdating = true; // suppress triggering another updateWidget
						self.setting( r.data.instance );
						self.isWidgetUpdating = false;
					} else {
						// no change was made, so stop the spinner now instead of when the preview would updates
						self.container.removeClass( 'previewer-loading' );
					}

					if ( completeCallback ) {
						completeCallback.call( self, null, { noChange: ! isChanged, ajaxFinished: true } );
					}
				} else {
					// General error message
					message = l10n.error;

					if ( r.data && r.data.message ) {
						message = r.data.message;
					}

					if ( completeCallback ) {
						completeCallback.call( self, message );
					} else {
						$widgetContent.prepend( '<p class="widget-error"><strong>' + message + '</strong></p>' );
					}
				}
			} );

			jqxhr.fail( function( jqXHR, textStatus ) {
				if ( completeCallback ) {
					completeCallback.call( self, textStatus );
				}
			} );

			jqxhr.always( function() {
				self.container.removeClass( 'widget-form-loading' );

				$inputs.each( function() {
					$( this ).removeData( 'state' + updateNumber );
				} );

				processing( processing() - 1 );
			} );
		},

		/**
		 * Expand the accordion section containing a control
		 */
		expandControlSection: function() {
			api.Control.prototype.expand.call( this );
		},

		/**
		 * @since 4.1.0
		 *
		 * @param {Boolean} expanded
		 * @param {Object} [params]
		 * @returns {Boolean} false if state already applied
		 */
		_toggleExpanded: api.Section.prototype._toggleExpanded,

		/**
		 * @since 4.1.0
		 *
		 * @param {Object} [params]
		 * @returns {Boolean} false if already expanded
		 */
		expand: api.Section.prototype.expand,

		/**
		 * Expand the widget form control
		 *
		 * @deprecated 4.1.0 Use this.expand() instead.
		 */
		expandForm: function() {
			this.expand();
		},

		/**
		 * @since 4.1.0
		 *
		 * @param {Object} [params]
		 * @returns {Boolean} false if already collapsed
		 */
		collapse: api.Section.prototype.collapse,

		/**
		 * Collapse the widget form control
		 *
		 * @deprecated 4.1.0 Use this.collapse() instead.
		 */
		collapseForm: function() {
			this.collapse();
		},

		/**
		 * Expand or collapse the widget control
		 *
		 * @deprecated this is poor naming, and it is better to directly set control.expanded( showOrHide )
		 *
		 * @param {boolean|undefined} [showOrHide] If not supplied, will be inverse of current visibility
		 */
		toggleForm: function( showOrHide ) {
			if ( typeof showOrHide === 'undefined' ) {
				showOrHide = ! this.expanded();
			}
			this.expanded( showOrHide );
		},

		/**
		 * Respond to change in the expanded state.
		 *
		 * @param {Boolean} expanded
		 * @param {Object} args  merged on top of this.defaultActiveArguments
		 */
		onChangeExpanded: function ( expanded, args ) {
			var self = this, $widget, $inside, complete, prevComplete;

			self.embedWidgetControl(); // Make sure the outer form is embedded so that the expanded state can be set in the UI.
			if ( expanded ) {
				self.embedWidgetContent();
			}

			// If the expanded state is unchanged only manipulate container expanded states
			if ( args.unchanged ) {
				if ( expanded ) {
					api.Control.prototype.expand.call( self, {
						completeCallback:  args.completeCallback
					});
				}
				return;
			}

			$widget = this.container.find( 'div.widget:first' );
			$inside = $widget.find( '.widget-inside:first' );

			if ( expanded ) {

				if ( self.section() && api.section( self.section() ) ) {
					self.expandControlSection();
				}

				// Close all other widget controls before expanding this one
				api.control.each( function( otherControl ) {
					if ( self.params.type === otherControl.params.type && self !== otherControl ) {
						otherControl.collapse();
					}
				} );

				complete = function() {
					self.container.removeClass( 'expanding' );
					self.container.addClass( 'expanded' );
					self.container.trigger( 'expanded' );
				};
				if ( args.completeCallback ) {
					prevComplete = complete;
					complete = function () {
						prevComplete();
						args.completeCallback();
					};
				}

				if ( self.params.is_wide ) {
					$inside.fadeIn( args.duration, complete );
				} else {
					$inside.slideDown( args.duration, complete );
				}

				self.container.trigger( 'expand' );
				self.container.addClass( 'expanding' );
			} else {

				complete = function() {
					self.container.removeClass( 'collapsing' );
					self.container.removeClass( 'expanded' );
					self.container.trigger( 'collapsed' );
				};
				if ( args.completeCallback ) {
					prevComplete = complete;
					complete = function () {
						prevComplete();
						args.completeCallback();
					};
				}

				self.container.trigger( 'collapse' );
				self.container.addClass( 'collapsing' );

				if ( self.params.is_wide ) {
					$inside.fadeOut( args.duration, complete );
				} else {
					$inside.slideUp( args.duration, function() {
						$widget.css( { width:'', margin:'' } );
						complete();
					} );
				}
			}
		},

		/**
		 * Get the position (index) of the widget in the containing sidebar
		 *
		 * @returns {Number}
		 */
		getWidgetSidebarPosition: function() {
			var sidebarWidgetIds, position;

			sidebarWidgetIds = this.getSidebarWidgetsControl().setting();
			position = _.indexOf( sidebarWidgetIds, this.params.widget_id );

			if ( position === -1 ) {
				return;
			}

			return position;
		},

		/**
		 * Move widget up one in the sidebar
		 */
		moveUp: function() {
			this._moveWidgetByOne( -1 );
		},

		/**
		 * Move widget up one in the sidebar
		 */
		moveDown: function() {
			this._moveWidgetByOne( 1 );
		},

		/**
		 * @private
		 *
		 * @param {Number} offset 1|-1
		 */
		_moveWidgetByOne: function( offset ) {
			var i, sidebarWidgetsSetting, sidebarWidgetIds,	adjacentWidgetId;

			i = this.getWidgetSidebarPosition();

			sidebarWidgetsSetting = this.getSidebarWidgetsControl().setting;
			sidebarWidgetIds = Array.prototype.slice.call( sidebarWidgetsSetting() ); // clone
			adjacentWidgetId = sidebarWidgetIds[i + offset];
			sidebarWidgetIds[i + offset] = this.params.widget_id;
			sidebarWidgetIds[i] = adjacentWidgetId;

			sidebarWidgetsSetting( sidebarWidgetIds );
		},

		/**
		 * Toggle visibility of the widget move area
		 *
		 * @param {Boolean} [showOrHide]
		 */
		toggleWidgetMoveArea: function( showOrHide ) {
			var self = this, $moveWidgetArea;

			$moveWidgetArea = this.container.find( '.move-widget-area' );

			if ( typeof showOrHide === 'undefined' ) {
				showOrHide = ! $moveWidgetArea.hasClass( 'active' );
			}

			if ( showOrHide ) {
				// reset the selected sidebar
				$moveWidgetArea.find( '.selected' ).removeClass( 'selected' );

				$moveWidgetArea.find( 'li' ).filter( function() {
					return $( this ).data( 'id' ) === self.params.sidebar_id;
				} ).addClass( 'selected' );

				this.container.find( '.move-widget-btn' ).prop( 'disabled', true );
			}

			$moveWidgetArea.toggleClass( 'active', showOrHide );
		},

		/**
		 * Highlight the widget control and section
		 */
		highlightSectionAndControl: function() {
			var $target;

			if ( this.container.is( ':hidden' ) ) {
				$target = this.container.closest( '.control-section' );
			} else {
				$target = this.container;
			}

			$( '.highlighted' ).removeClass( 'highlighted' );
			$target.addClass( 'highlighted' );

			setTimeout( function() {
				$target.removeClass( 'highlighted' );
			}, 500 );
		}
	} );

	/**
	 * wp.customize.Widgets.WidgetsPanel
	 *
	 * Customizer panel containing the widget area sections.
	 *
	 * @since 4.4.0
	 */
	api.Widgets.WidgetsPanel = api.Panel.extend({

		/**
		 * Add and manage the display of the no-rendered-areas notice.
		 *
		 * @since 4.4.0
		 */
		ready: function () {
			var panel = this;

			api.Panel.prototype.ready.call( panel );

			panel.deferred.embedded.done(function() {
				var panelMetaContainer, noRenderedAreasNotice, shouldShowNotice;
				panelMetaContainer = panel.container.find( '.panel-meta' );
				noRenderedAreasNotice = $( '<div></div>', {
					'class': 'no-widget-areas-rendered-notice'
				});
				noRenderedAreasNotice.append( $( '<em></em>', {
					text: l10n.noAreasRendered
				} ) );
				panelMetaContainer.append( noRenderedAreasNotice );

				shouldShowNotice = function() {
					return ( 0 === _.filter( panel.sections(), function( section ) {
						return section.active();
					} ).length );
				};

				/*
				 * Set the initial visibility state for rendered notice.
				 * Update the visibility of the notice whenever a reflow happens.
				 */
				noRenderedAreasNotice.toggle( shouldShowNotice() );
				api.previewer.deferred.active.done( function () {
					noRenderedAreasNotice.toggle( shouldShowNotice() );
				});
				api.bind( 'pane-contents-reflowed', function() {
					var duration = ( 'resolved' === api.previewer.deferred.active.state() ) ? 'fast' : 0;
					if ( shouldShowNotice() ) {
						noRenderedAreasNotice.slideDown( duration );
					} else {
						noRenderedAreasNotice.slideUp( duration );
					}
				});
			});
		},

		/**
		 * Allow an active widgets panel to be contextually active even when it has no active sections (widget areas).
		 *
		 * This ensures that the widgets panel appears even when there are no
		 * sidebars displayed on the URL currently being previewed.
		 *
		 * @since 4.4.0
		 *
		 * @returns {boolean}
		 */
		isContextuallyActive: function() {
			var panel = this;
			return panel.active();
		}
	});

	/**
	 * wp.customize.Widgets.SidebarSection
	 *
	 * Customizer section representing a widget area widget
	 *
	 * @since 4.1.0
	 */
	api.Widgets.SidebarSection = api.Section.extend({

		/**
		 * Sync the section's active state back to the Backbone model's is_rendered attribute
		 *
		 * @since 4.1.0
		 */
		ready: function () {
			var section = this, registeredSidebar;
			api.Section.prototype.ready.call( this );
			registeredSidebar = api.Widgets.registeredSidebars.get( section.params.sidebarId );
			section.active.bind( function ( active ) {
				registeredSidebar.set( 'is_rendered', active );
			});
			registeredSidebar.set( 'is_rendered', section.active() );
		}
	});

	/**
	 * wp.customize.Widgets.SidebarControl
	 *
	 * Customizer control for widgets.
	 * Note that 'sidebar_widgets' must match the WP_Widget_Area_Customize_Control::$type
	 *
	 * @since 3.9.0
	 *
	 * @constructor
	 * @augments wp.customize.Control
	 */
	api.Widgets.SidebarControl = api.Control.extend({

		/**
		 * Set up the control
		 */
		ready: function() {
			this.$controlSection = this.container.closest( '.control-section' );
			this.$sectionContent = this.container.closest( '.accordion-section-content' );

			this._setupModel();
			this._setupSortable();
			this._setupAddition();
			this._applyCardinalOrderClassNames();
		},

		/**
		 * Update ordering of widget control forms when the setting is updated
		 */
		_setupModel: function() {
			var self = this;

			this.setting.bind( function( newWidgetIds, oldWidgetIds ) {
				var widgetFormControls, removedWidgetIds, priority;

				removedWidgetIds = _( oldWidgetIds ).difference( newWidgetIds );

				// Filter out any persistent widget IDs for widgets which have been deactivated
				newWidgetIds = _( newWidgetIds ).filter( function( newWidgetId ) {
					var parsedWidgetId = parseWidgetId( newWidgetId );

					return !! api.Widgets.availableWidgets.findWhere( { id_base: parsedWidgetId.id_base } );
				} );

				widgetFormControls = _( newWidgetIds ).map( function( widgetId ) {
					var widgetFormControl = api.Widgets.getWidgetFormControlForWidget( widgetId );

					if ( ! widgetFormControl ) {
						widgetFormControl = self.addWidget( widgetId );
					}

					return widgetFormControl;
				} );

				// Sort widget controls to their new positions
				widgetFormControls.sort( function( a, b ) {
					var aIndex = _.indexOf( newWidgetIds, a.params.widget_id ),
						bIndex = _.indexOf( newWidgetIds, b.params.widget_id );
					return aIndex - bIndex;
				});

				priority = 0;
				_( widgetFormControls ).each( function ( control ) {
					control.priority( priority );
					control.section( self.section() );
					priority += 1;
				});
				self.priority( priority ); // Make sure sidebar control remains at end

				// Re-sort widget form controls (including widgets form other sidebars newly moved here)
				self._applyCardinalOrderClassNames();

				// If the widget was dragged into the sidebar, make sure the sidebar_id param is updated
				_( widgetFormControls ).each( function( widgetFormControl ) {
					widgetFormControl.params.sidebar_id = self.params.sidebar_id;
				} );

				// Cleanup after widget removal
				_( removedWidgetIds ).each( function( removedWidgetId ) {

					// Using setTimeout so that when moving a widget to another sidebar, the other sidebars_widgets settings get a chance to update
					setTimeout( function() {
						var removedControl, wasDraggedToAnotherSidebar, inactiveWidgets, removedIdBase,
							widget, isPresentInAnotherSidebar = false;

						// Check if the widget is in another sidebar
						api.each( function( otherSetting ) {
							if ( otherSetting.id === self.setting.id || 0 !== otherSetting.id.indexOf( 'sidebars_widgets[' ) || otherSetting.id === 'sidebars_widgets[wp_inactive_widgets]' ) {
								return;
							}

							var otherSidebarWidgets = otherSetting(), i;

							i = _.indexOf( otherSidebarWidgets, removedWidgetId );
							if ( -1 !== i ) {
								isPresentInAnotherSidebar = true;
							}
						} );

						// If the widget is present in another sidebar, abort!
						if ( isPresentInAnotherSidebar ) {
							return;
						}

						removedControl = api.Widgets.getWidgetFormControlForWidget( removedWidgetId );

						// Detect if widget control was dragged to another sidebar
						wasDraggedToAnotherSidebar = removedControl && $.contains( document, removedControl.container[0] ) && ! $.contains( self.$sectionContent[0], removedControl.container[0] );

						// Delete any widget form controls for removed widgets
						if ( removedControl && ! wasDraggedToAnotherSidebar ) {
							api.control.remove( removedControl.id );
							removedControl.container.remove();
						}

						// Move widget to inactive widgets sidebar (move it to trash) if has been previously saved
						// This prevents the inactive widgets sidebar from overflowing with throwaway widgets
						if ( api.Widgets.savedWidgetIds[removedWidgetId] ) {
							inactiveWidgets = api.value( 'sidebars_widgets[wp_inactive_widgets]' )().slice();
							inactiveWidgets.push( removedWidgetId );
							api.value( 'sidebars_widgets[wp_inactive_widgets]' )( _( inactiveWidgets ).unique() );
						}

						// Make old single widget available for adding again
						removedIdBase = parseWidgetId( removedWidgetId ).id_base;
						widget = api.Widgets.availableWidgets.findWhere( { id_base: removedIdBase } );
						if ( widget && ! widget.get( 'is_multi' ) ) {
							widget.set( 'is_disabled', false );
						}
					} );

				} );
			} );
		},

		/**
		 * Allow widgets in sidebar to be re-ordered, and for the order to be previewed
		 */
		_setupSortable: function() {
			var self = this;

			this.isReordering = false;

			/**
			 * Update widget order setting when controls are re-ordered
			 */
			this.$sectionContent.sortable( {
				items: '> .customize-control-widget_form',
				handle: '.widget-top',
				axis: 'y',
				tolerance: 'pointer',
				connectWith: '.accordion-section-content:has(.customize-control-sidebar_widgets)',
				update: function() {
					var widgetContainerIds = self.$sectionContent.sortable( 'toArray' ), widgetIds;

					widgetIds = $.map( widgetContainerIds, function( widgetContainerId ) {
						return $( '#' + widgetContainerId ).find( ':input[name=widget-id]' ).val();
					} );

					self.setting( widgetIds );
				}
			} );

			/**
			 * Expand other Customizer sidebar section when dragging a control widget over it,
			 * allowing the control to be dropped into another section
			 */
			this.$controlSection.find( '.accordion-section-title' ).droppable({
				accept: '.customize-control-widget_form',
				over: function() {
					var section = api.section( self.section.get() );
					section.expand({
						allowMultiple: true, // Prevent the section being dragged from to be collapsed
						completeCallback: function () {
							// @todo It is not clear when refreshPositions should be called on which sections, or if it is even needed
							api.section.each( function ( otherSection ) {
								if ( otherSection.container.find( '.customize-control-sidebar_widgets' ).length ) {
									otherSection.container.find( '.accordion-section-content:first' ).sortable( 'refreshPositions' );
								}
							} );
						}
					});
				}
			});

			/**
			 * Keyboard-accessible reordering
			 */
			this.container.find( '.reorder-toggle' ).on( 'click', function() {
				self.toggleReordering( ! self.isReordering );
			} );
		},

		/**
		 * Set up UI for adding a new widget
		 */
		_setupAddition: function() {
			var self = this;

			this.container.find( '.add-new-widget' ).on( 'click', function() {
				var addNewWidgetBtn = $( this );

				if ( self.$sectionContent.hasClass( 'reordering' ) ) {
					return;
				}

				if ( ! $( 'body' ).hasClass( 'adding-widget' ) ) {
					addNewWidgetBtn.attr( 'aria-expanded', 'true' );
					api.Widgets.availableWidgetsPanel.open( self );
				} else {
					addNewWidgetBtn.attr( 'aria-expanded', 'false' );
					api.Widgets.availableWidgetsPanel.close();
				}
			} );
		},

		/**
		 * Add classes to the widget_form controls to assist with styling
		 */
		_applyCardinalOrderClassNames: function() {
			var widgetControls = [];
			_.each( this.setting(), function ( widgetId ) {
				var widgetControl = api.Widgets.getWidgetFormControlForWidget( widgetId );
				if ( widgetControl ) {
					widgetControls.push( widgetControl );
				}
			});

			if ( 0 === widgetControls.length || ( 1 === api.Widgets.registeredSidebars.length && widgetControls.length <= 1 ) ) {
				this.container.find( '.reorder-toggle' ).hide();
				return;
			} else {
				this.container.find( '.reorder-toggle' ).show();
			}

			$( widgetControls ).each( function () {
				$( this.container )
					.removeClass( 'first-widget' )
					.removeClass( 'last-widget' )
					.find( '.move-widget-down, .move-widget-up' ).prop( 'tabIndex', 0 );
			});

			_.first( widgetControls ).container
				.addClass( 'first-widget' )
				.find( '.move-widget-up' ).prop( 'tabIndex', -1 );

			_.last( widgetControls ).container
				.addClass( 'last-widget' )
				.find( '.move-widget-down' ).prop( 'tabIndex', -1 );
		},


		/***********************************************************************
		 * Begin public API methods
		 **********************************************************************/

		/**
		 * Enable/disable the reordering UI
		 *
		 * @param {Boolean} showOrHide to enable/disable reordering
		 *
		 * @todo We should have a reordering state instead and rename this to onChangeReordering
		 */
		toggleReordering: function( showOrHide ) {
			var addNewWidgetBtn = this.$sectionContent.find( '.add-new-widget' ),
				reorderBtn = this.container.find( '.reorder-toggle' ),
				widgetsTitle = this.$sectionContent.find( '.widget-title' );

			showOrHide = Boolean( showOrHide );

			if ( showOrHide === this.$sectionContent.hasClass( 'reordering' ) ) {
				return;
			}

			this.isReordering = showOrHide;
			this.$sectionContent.toggleClass( 'reordering', showOrHide );

			if ( showOrHide ) {
				_( this.getWidgetFormControls() ).each( function( formControl ) {
					formControl.collapse();
				} );

				addNewWidgetBtn.attr({ 'tabindex': '-1', 'aria-hidden': 'true' });
				reorderBtn.attr( 'aria-label', l10n.reorderLabelOff );
				wp.a11y.speak( l10n.reorderModeOn );
				// Hide widget titles while reordering: title is already in the reorder controls.
				widgetsTitle.attr( 'aria-hidden', 'true' );
			} else {
				addNewWidgetBtn.removeAttr( 'tabindex aria-hidden' );
				reorderBtn.attr( 'aria-label', l10n.reorderLabelOn );
				wp.a11y.speak( l10n.reorderModeOff );
				widgetsTitle.attr( 'aria-hidden', 'false' );
			}
		},

		/**
		 * Get the widget_form Customize controls associated with the current sidebar.
		 *
		 * @since 3.9
		 * @return {wp.customize.controlConstructor.widget_form[]}
		 */
		getWidgetFormControls: function() {
			var formControls = [];

			_( this.setting() ).each( function( widgetId ) {
				var settingId = widgetIdToSettingId( widgetId ),
					formControl = api.control( settingId );
				if ( formControl ) {
					formControls.push( formControl );
				}
			} );

			return formControls;
		},

		/**
		 * @param {string} widgetId or an id_base for adding a previously non-existing widget
		 * @returns {object|false} widget_form control instance, or false on error
		 */
		addWidget: function( widgetId ) {
			var self = this, controlHtml, $widget, controlType = 'widget_form', controlContainer, controlConstructor,
				parsedWidgetId = parseWidgetId( widgetId ),
				widgetNumber = parsedWidgetId.number,
				widgetIdBase = parsedWidgetId.id_base,
				widget = api.Widgets.availableWidgets.findWhere( {id_base: widgetIdBase} ),
				settingId, isExistingWidget, widgetFormControl, sidebarWidgets, settingArgs, setting;

			if ( ! widget ) {
				return false;
			}

			if ( widgetNumber && ! widget.get( 'is_multi' ) ) {
				return false;
			}

			// Set up new multi widget
			if ( widget.get( 'is_multi' ) && ! widgetNumber ) {
				widget.set( 'multi_number', widget.get( 'multi_number' ) + 1 );
				widgetNumber = widget.get( 'multi_number' );
			}

			controlHtml = $.trim( $( '#widget-tpl-' + widget.get( 'id' ) ).html() );
			if ( widget.get( 'is_multi' ) ) {
				controlHtml = controlHtml.replace( /<[^<>]+>/g, function( m ) {
					return m.replace( /__i__|%i%/g, widgetNumber );
				} );
			} else {
				widget.set( 'is_disabled', true ); // Prevent single widget from being added again now
			}

			$widget = $( controlHtml );

			controlContainer = $( '<li/>' )
				.addClass( 'customize-control' )
				.addClass( 'customize-control-' + controlType )
				.append( $widget );

			// Remove icon which is visible inside the panel
			controlContainer.find( '> .widget-icon' ).remove();

			if ( widget.get( 'is_multi' ) ) {
				controlContainer.find( 'input[name="widget_number"]' ).val( widgetNumber );
				controlContainer.find( 'input[name="multi_number"]' ).val( widgetNumber );
			}

			widgetId = controlContainer.find( '[name="widget-id"]' ).val();

			controlContainer.hide(); // to be slid-down below

			settingId = 'widget_' + widget.get( 'id_base' );
			if ( widget.get( 'is_multi' ) ) {
				settingId += '[' + widgetNumber + ']';
			}
			controlContainer.attr( 'id', 'customize-control-' + settingId.replace( /\]/g, '' ).replace( /\[/g, '-' ) );

			// Only create setting if it doesn't already exist (if we're adding a pre-existing inactive widget)
			isExistingWidget = api.has( settingId );
			if ( ! isExistingWidget ) {
				settingArgs = {
					transport: api.Widgets.data.selectiveRefresh ? 'postMessage' : 'refresh',
					previewer: this.setting.previewer
				};
				setting = api.create( settingId, settingId, '', settingArgs );
				setting.set( {} ); // mark dirty, changing from '' to {}
			}

			controlConstructor = api.controlConstructor[controlType];
			widgetFormControl = new controlConstructor( settingId, {
				params: {
					settings: {
						'default': settingId
					},
					content: controlContainer,
					sidebar_id: self.params.sidebar_id,
					widget_id: widgetId,
					widget_id_base: widget.get( 'id_base' ),
					type: controlType,
					is_new: ! isExistingWidget,
					width: widget.get( 'width' ),
					height: widget.get( 'height' ),
					is_wide: widget.get( 'is_wide' ),
					active: true
				},
				previewer: self.setting.previewer
			} );
			api.control.add( settingId, widgetFormControl );

			// Make sure widget is removed from the other sidebars
			api.each( function( otherSetting ) {
				if ( otherSetting.id === self.setting.id ) {
					return;
				}

				if ( 0 !== otherSetting.id.indexOf( 'sidebars_widgets[' ) ) {
					return;
				}

				var otherSidebarWidgets = otherSetting().slice(),
					i = _.indexOf( otherSidebarWidgets, widgetId );

				if ( -1 !== i ) {
					otherSidebarWidgets.splice( i );
					otherSetting( otherSidebarWidgets );
				}
			} );

			// Add widget to this sidebar
			sidebarWidgets = this.setting().slice();
			if ( -1 === _.indexOf( sidebarWidgets, widgetId ) ) {
				sidebarWidgets.push( widgetId );
				this.setting( sidebarWidgets );
			}

			controlContainer.slideDown( function() {
				if ( isExistingWidget ) {
					widgetFormControl.updateWidget( {
						instance: widgetFormControl.setting()
					} );
				}
			} );

			return widgetFormControl;
		}
	} );

	// Register models for custom panel, section, and control types
	$.extend( api.panelConstructor, {
		widgets: api.Widgets.WidgetsPanel
	});
	$.extend( api.sectionConstructor, {
		sidebar: api.Widgets.SidebarSection
	});
	$.extend( api.controlConstructor, {
		widget_form: api.Widgets.WidgetControl,
		sidebar_widgets: api.Widgets.SidebarControl
	});

	/**
	 * Init Customizer for widgets.
	 */
	api.bind( 'ready', function() {
		// Set up the widgets panel
		api.Widgets.availableWidgetsPanel = new api.Widgets.AvailableWidgetsPanelView({
			collection: api.Widgets.availableWidgets
		});

		// Highlight widget control
		api.previewer.bind( 'highlight-widget-control', api.Widgets.highlightWidgetFormControl );

		// Open and focus widget control
		api.previewer.bind( 'focus-widget-control', api.Widgets.focusWidgetFormControl );
	} );

	/**
	 * Highlight a widget control.
	 *
	 * @param {string} widgetId
	 */
	api.Widgets.highlightWidgetFormControl = function( widgetId ) {
		var control = api.Widgets.getWidgetFormControlForWidget( widgetId );

		if ( control ) {
			control.highlightSectionAndControl();
		}
	},

	/**
	 * Focus a widget control.
	 *
	 * @param {string} widgetId
	 */
	api.Widgets.focusWidgetFormControl = function( widgetId ) {
		var control = api.Widgets.getWidgetFormControlForWidget( widgetId );

		if ( control ) {
			control.focus();
		}
	},

	/**
	 * Given a widget control, find the sidebar widgets control that contains it.
	 * @param {string} widgetId
	 * @return {object|null}
	 */
	api.Widgets.getSidebarWidgetControlContainingWidget = function( widgetId ) {
		var foundControl = null;

		// @todo this can use widgetIdToSettingId(), then pass into wp.customize.control( x ).getSidebarWidgetsControl()
		api.control.each( function( control ) {
			if ( control.params.type === 'sidebar_widgets' && -1 !== _.indexOf( control.setting(), widgetId ) ) {
				foundControl = control;
			}
		} );

		return foundControl;
	};

	/**
	 * Given a widget ID for a widget appearing in the preview, get the widget form control associated with it.
	 *
	 * @param {string} widgetId
	 * @return {object|null}
	 */
	api.Widgets.getWidgetFormControlForWidget = function( widgetId ) {
		var foundControl = null;

		// @todo We can just use widgetIdToSettingId() here
		api.control.each( function( control ) {
			if ( control.params.type === 'widget_form' && control.params.widget_id === widgetId ) {
				foundControl = control;
			}
		} );

		return foundControl;
	};

	/**
	 * @param {String} widgetId
	 * @returns {Object}
	 */
	function parseWidgetId( widgetId ) {
		var matches, parsed = {
			number: null,
			id_base: null
		};

		matches = widgetId.match( /^(.+)-(\d+)$/ );
		if ( matches ) {
			parsed.id_base = matches[1];
			parsed.number = parseInt( matches[2], 10 );
		} else {
			// likely an old single widget
			parsed.id_base = widgetId;
		}

		return parsed;
	}

	/**
	 * @param {String} widgetId
	 * @returns {String} settingId
	 */
	function widgetIdToSettingId( widgetId ) {
		var parsed = parseWidgetId( widgetId ), settingId;

		settingId = 'widget_' + parsed.id_base;
		if ( parsed.number ) {
			settingId += '[' + parsed.number + ']';
		}

		return settingId;
	}

})( window.wp, jQuery );
