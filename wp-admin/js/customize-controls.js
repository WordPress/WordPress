/* globals _wpCustomizeHeader, _wpCustomizeBackground, _wpMediaViewsL10n */
(function( exports, $ ){
	var Container, focus, api = wp.customize;

	/**
	 * @class
	 * @augments wp.customize.Value
	 * @augments wp.customize.Class
	 *
	 * @param options
	 * - previewer - The Previewer instance to sync with.
	 * - transport - The transport to use for previewing. Supports 'refresh' and 'postMessage'.
	 */
	api.Setting = api.Value.extend({
		initialize: function( id, value, options ) {
			api.Value.prototype.initialize.call( this, value, options );

			this.id = id;
			this.transport = this.transport || 'refresh';

			this.bind( this.preview );
		},
		preview: function() {
			switch ( this.transport ) {
				case 'refresh':
					return this.previewer.refresh();
				case 'postMessage':
					return this.previewer.send( 'setting', [ this.id, this() ] );
			}
		}
	});

	/**
	 * Utility function namespace
	 */
	api.utils = {};

	/**
	 * Watch all changes to Value properties, and bubble changes to parent Values instance
	 *
	 * @since 4.1.0
	 *
	 * @param {wp.customize.Class} instance
	 * @param {Array}              properties  The names of the Value instances to watch.
	 */
	api.utils.bubbleChildValueChanges = function ( instance, properties ) {
		$.each( properties, function ( i, key ) {
			instance[ key ].bind( function ( to, from ) {
				if ( instance.parent && to !== from ) {
					instance.parent.trigger( 'change', instance );
				}
			} );
		} );
	};

	/**
	 * Expand a panel, section, or control and focus on the first focusable element.
	 *
	 * @since 4.1.0
	 *
	 * @param {Object}   [params]
	 * @param {Callback} [params.completeCallback]
	 */
	focus = function ( params ) {
		var construct, completeCallback, focus;
		construct = this;
		params = params || {};
		focus = function () {
			construct.container.find( ':focusable:first' ).focus();
			construct.container[0].scrollIntoView( true );
		};
		if ( params.completeCallback ) {
			completeCallback = params.completeCallback;
			params.completeCallback = function () {
				focus();
				completeCallback();
			};
		} else {
			params.completeCallback = focus;
		}
		if ( construct.expand ) {
			construct.expand( params );
		} else {
			params.completeCallback();
		}
	};

	/**
	 * Stable sort for Panels, Sections, and Controls.
	 *
	 * If a.priority() === b.priority(), then sort by their respective params.instanceNumber.
	 *
	 * @since 4.1.0
	 *
	 * @param {(wp.customize.Panel|wp.customize.Section|wp.customize.Control)} a
	 * @param {(wp.customize.Panel|wp.customize.Section|wp.customize.Control)} b
	 * @returns {Number}
	 */
	api.utils.prioritySort = function ( a, b ) {
		if ( a.priority() === b.priority() && typeof a.params.instanceNumber === 'number' && typeof b.params.instanceNumber === 'number' ) {
			return a.params.instanceNumber - b.params.instanceNumber;
		} else {
			return a.priority() - b.priority();
		}
	};

	/**
	 * Return whether the supplied Event object is for a keydown event but not the Enter key.
	 *
	 * @since 4.1.0
	 *
	 * @param {jQuery.Event} event
	 * @returns {boolean}
	 */
	api.utils.isKeydownButNotEnterEvent = function ( event ) {
		return ( 'keydown' === event.type && 13 !== event.which );
	};

	/**
	 * Return whether the two lists of elements are the same and are in the same order.
	 *
	 * @since 4.1.0
	 *
	 * @param {Array|jQuery} listA
	 * @param {Array|jQuery} listB
	 * @returns {boolean}
	 */
	api.utils.areElementListsEqual = function ( listA, listB ) {
		var equal = (
			listA.length === listB.length && // if lists are different lengths, then naturally they are not equal
			-1 === _.indexOf( _.map( // are there any false values in the list returned by map?
				_.zip( listA, listB ), // pair up each element between the two lists
				function ( pair ) {
					return $( pair[0] ).is( pair[1] ); // compare to see if each pair are equal
				}
			), false ) // check for presence of false in map's return value
		);
		return equal;
	};

	/**
	 * Base class for Panel and Section.
	 *
	 * @since 4.1.0
	 *
	 * @class
	 * @augments wp.customize.Class
	 */
	Container = api.Class.extend({
		defaultActiveArguments: { duration: 'fast', completeCallback: $.noop },
		defaultExpandedArguments: { duration: 'fast', completeCallback: $.noop },

		/**
		 * @since 4.1.0
		 *
		 * @param {String} id
		 * @param {Object} options
		 */
		initialize: function ( id, options ) {
			var container = this;
			container.id = id;
			container.params = {};
			$.extend( container, options || {} );
			container.container = $( container.params.content );

			container.deferred = {
				embedded: new $.Deferred()
			};
			container.priority = new api.Value();
			container.active = new api.Value();
			container.activeArgumentsQueue = [];
			container.expanded = new api.Value();
			container.expandedArgumentsQueue = [];

			container.active.bind( function ( active ) {
				var args = container.activeArgumentsQueue.shift();
				args = $.extend( {}, container.defaultActiveArguments, args );
				active = ( active && container.isContextuallyActive() );
				container.onChangeActive( active, args );
			});
			container.expanded.bind( function ( expanded ) {
				var args = container.expandedArgumentsQueue.shift();
				args = $.extend( {}, container.defaultExpandedArguments, args );
				container.onChangeExpanded( expanded, args );
			});

			container.attachEvents();

			api.utils.bubbleChildValueChanges( container, [ 'priority', 'active' ] );

			container.priority.set( isNaN( container.params.priority ) ? 100 : container.params.priority );
			container.active.set( container.params.active );
			container.expanded.set( false );
		},

		/**
		 * @since 4.1.0
		 *
		 * @abstract
		 */
		ready: function() {},

		/**
		 * Get the child models associated with this parent, sorting them by their priority Value.
		 *
		 * @since 4.1.0
		 *
		 * @param {String} parentType
		 * @param {String} childType
		 * @returns {Array}
		 */
		_children: function ( parentType, childType ) {
			var parent = this,
				children = [];
			api[ childType ].each( function ( child ) {
				if ( child[ parentType ].get() === parent.id ) {
					children.push( child );
				}
			} );
			children.sort( api.utils.prioritySort );
			return children;
		},

		/**
		 * To override by subclass, to return whether the container has active children.
		 *
		 * @since 4.1.0
		 *
		 * @abstract
		 */
		isContextuallyActive: function () {
			throw new Error( 'Container.isContextuallyActive() must be overridden in a subclass.' );
		},

		/**
		 * Handle changes to the active state.
		 *
		 * This does not change the active state, it merely handles the behavior
		 * for when it does change.
		 *
		 * To override by subclass, update the container's UI to reflect the provided active state.
		 *
		 * @since 4.1.0
		 *
		 * @param {Boolean} active
		 * @param {Object}  args
		 * @param {Object}  args.duration
		 * @param {Object}  args.completeCallback
		 */
		onChangeActive: function ( active, args ) {
			var duration = ( 'resolved' === api.previewer.deferred.active.state() ? args.duration : 0 );
			if ( ! $.contains( document, this.container ) ) {
				// jQuery.fn.slideUp is not hiding an element if it is not in the DOM
				this.container.toggle( active );
				if ( args.completeCallback ) {
					args.completeCallback();
				}
			} else if ( active ) {
				this.container.stop( true, true ).slideDown( duration, args.completeCallback );
			} else {
				this.container.stop( true, true ).slideUp( duration, args.completeCallback );
			}
		},

		/**
		 * @since 4.1.0
		 *
		 * @params {Boolean} active
		 * @param {Object}   [params]
		 * @returns {Boolean} false if state already applied
		 */
		_toggleActive: function ( active, params ) {
			var self = this;
			params = params || {};
			if ( ( active && this.active.get() ) || ( ! active && ! this.active.get() ) ) {
				params.unchanged = true;
				self.onChangeActive( self.active.get(), params );
				return false;
			} else {
				params.unchanged = false;
				this.activeArgumentsQueue.push( params );
				this.active.set( active );
				return true;
			}
		},

		/**
		 * @param {Object} [params]
		 * @returns {Boolean} false if already active
		 */
		activate: function ( params ) {
			return this._toggleActive( true, params );
		},

		/**
		 * @param {Object} [params]
		 * @returns {Boolean} false if already inactive
		 */
		deactivate: function ( params ) {
			return this._toggleActive( false, params );
		},

		/**
		 * To override by subclass, update the container's UI to reflect the provided active state.
		 * @abstract
		 */
		onChangeExpanded: function () {
			throw new Error( 'Must override with subclass.' );
		},

		/**
		 * @param {Boolean} expanded
		 * @param {Object} [params]
		 * @returns {Boolean} false if state already applied
		 */
		_toggleExpanded: function ( expanded, params ) {
			var self = this;
			params = params || {};
			if ( ( expanded && this.expanded.get() ) || ( ! expanded && ! this.expanded.get() ) ) {
				params.unchanged = true;
				self.onChangeExpanded( self.expanded.get(), params );
				return false;
			} else {
				params.unchanged = false;
				this.expandedArgumentsQueue.push( params );
				this.expanded.set( expanded );
				return true;
			}
		},

		/**
		 * @param {Object} [params]
		 * @returns {Boolean} false if already expanded
		 */
		expand: function ( params ) {
			return this._toggleExpanded( true, params );
		},

		/**
		 * @param {Object} [params]
		 * @returns {Boolean} false if already collapsed
		 */
		collapse: function ( params ) {
			return this._toggleExpanded( false, params );
		},

		/**
		 * Bring the container into view and then expand this and bring it into view
		 * @param {Object} [params]
		 */
		focus: focus
	});

	/**
	 * @since 4.1.0
	 *
	 * @class
	 * @augments wp.customize.Class
	 */
	api.Section = Container.extend({

		/**
		 * @since 4.1.0
		 *
		 * @param {String} id
		 * @param {Array}  options
		 */
		initialize: function ( id, options ) {
			var section = this;
			Container.prototype.initialize.call( section, id, options );

			section.id = id;
			section.panel = new api.Value();
			section.panel.bind( function ( id ) {
				$( section.container ).toggleClass( 'control-subsection', !! id );
			});
			section.panel.set( section.params.panel || '' );
			api.utils.bubbleChildValueChanges( section, [ 'panel' ] );

			section.embed();
			section.deferred.embedded.done( function () {
				section.ready();
			});
		},

		/**
		 * Embed the container in the DOM when any parent panel is ready.
		 *
		 * @since 4.1.0
		 */
		embed: function () {
			var section = this, inject;

			// Watch for changes to the panel state
			inject = function ( panelId ) {
				var parentContainer;
				if ( panelId ) {
					// The panel has been supplied, so wait until the panel object is registered
					api.panel( panelId, function ( panel ) {
						// The panel has been registered, wait for it to become ready/initialized
						panel.deferred.embedded.done( function () {
							parentContainer = panel.container.find( 'ul:first' );
							if ( ! section.container.parent().is( parentContainer ) ) {
								parentContainer.append( section.container );
							}
							section.deferred.embedded.resolve();
						});
					} );
				} else {
					// There is no panel, so embed the section in the root of the customizer
					parentContainer = $( '#customize-theme-controls' ).children( 'ul' ); // @todo This should be defined elsewhere, and to be configurable
					if ( ! section.container.parent().is( parentContainer ) ) {
						parentContainer.append( section.container );
					}
					section.deferred.embedded.resolve();
				}
			};
			section.panel.bind( inject );
			inject( section.panel.get() ); // Since a section may never get a panel, assume that it won't ever get one
		},

		/**
		 * Add behaviors for the accordion section.
		 *
		 * @since 4.1.0
		 */
		attachEvents: function () {
			var section = this;

			// Expand/Collapse accordion sections on click.
			section.container.find( '.accordion-section-title' ).on( 'click keydown', function( event ) {
				if ( api.utils.isKeydownButNotEnterEvent( event ) ) {
					return;
				}
				event.preventDefault(); // Keep this AFTER the key filter above

				if ( section.expanded() ) {
					section.collapse();
				} else {
					section.expand();
				}
			});
		},

		/**
		 * Return whether this section has any active controls.
		 *
		 * @since 4.1.0
		 *
		 * @returns {Boolean}
		 */
		isContextuallyActive: function () {
			var section = this,
				controls = section.controls(),
				activeCount = 0;
			_( controls ).each( function ( control ) {
				if ( control.active() ) {
					activeCount += 1;
				}
			} );
			return ( activeCount !== 0 );
		},

		/**
		 * Get the controls that are associated with this section, sorted by their priority Value.
		 *
		 * @since 4.1.0
		 *
		 * @returns {Array}
		 */
		controls: function () {
			return this._children( 'section', 'control' );
		},

		/**
		 * Update UI to reflect expanded state.
		 *
		 * @since 4.1.0
		 *
		 * @param {Boolean} expanded
		 * @param {Object}  args
		 */
		onChangeExpanded: function ( expanded, args ) {
			var section = this,
				content = section.container.find( '.accordion-section-content' ),
				expand;

			if ( expanded ) {

				if ( args.unchanged ) {
					expand = args.completeCallback;
				} else {
					expand = function () {
						content.stop().slideDown( args.duration, args.completeCallback );
						section.container.addClass( 'open' );
					};
				}

				if ( ! args.allowMultiple ) {
					api.section.each( function ( otherSection ) {
						if ( otherSection !== section ) {
							otherSection.collapse( { duration: args.duration } );
						}
					});
				}

				if ( section.panel() ) {
					api.panel( section.panel() ).expand({
						duration: args.duration,
						completeCallback: expand
					});
				} else {
					expand();
				}

			} else {
				section.container.removeClass( 'open' );
				content.slideUp( args.duration, args.completeCallback );
			}
		}
	});

	/**
	 * @since 4.1.0
	 *
	 * @class
	 * @augments wp.customize.Class
	 */
	api.Panel = Container.extend({
		/**
		 * @since 4.1.0
		 *
		 * @param  {String} id
		 * @param  {Object} options
		 */
		initialize: function ( id, options ) {
			var panel = this;
			Container.prototype.initialize.call( panel, id, options );
			panel.embed();
			panel.deferred.embedded.done( function () {
				panel.ready();
			});
		},

		/**
		 * Embed the container in the DOM when any parent panel is ready.
		 *
		 * @since 4.1.0
		 */
		embed: function () {
			var panel = this,
				parentContainer = $( '#customize-theme-controls > ul' ); // @todo This should be defined elsewhere, and to be configurable

			if ( ! panel.container.parent().is( parentContainer ) ) {
				parentContainer.append( panel.container );
			}
			panel.deferred.embedded.resolve();
		},

		/**
		 * @since 4.1.0
		 */
		attachEvents: function () {
			var meta, panel = this;

			// Expand/Collapse accordion sections on click.
			panel.container.find( '.accordion-section-title' ).on( 'click keydown', function( event ) {
				if ( api.utils.isKeydownButNotEnterEvent( event ) ) {
					return;
				}
				event.preventDefault(); // Keep this AFTER the key filter above

				if ( ! panel.expanded() ) {
					panel.expand();
				}
			});

			meta = panel.container.find( '.panel-meta:first' );

			meta.find( '> .accordion-section-title' ).on( 'click keydown', function( event ) {
				if ( api.utils.isKeydownButNotEnterEvent( event ) ) {
					return;
				}
				event.preventDefault(); // Keep this AFTER the key filter above

				if ( meta.hasClass( 'cannot-expand' ) ) {
					return;
				}

				var content = meta.find( '.accordion-section-content:first' );
				if ( meta.hasClass( 'open' ) ) {
					meta.toggleClass( 'open' );
					content.slideUp( panel.defaultExpandedArguments.duration );
				} else {
					content.slideDown( panel.defaultExpandedArguments.duration );
					meta.toggleClass( 'open' );
				}
			});

		},

		/**
		 * Get the sections that are associated with this panel, sorted by their priority Value.
		 *
		 * @since 4.1.0
		 *
		 * @returns {Array}
		 */
		sections: function () {
			return this._children( 'panel', 'section' );
		},

		/**
		 * Return whether this panel has any active sections.
		 *
		 * @since 4.1.0
		 *
		 * @returns {boolean}
		 */
		isContextuallyActive: function () {
			var panel = this,
				sections = panel.sections(),
				activeCount = 0;
			_( sections ).each( function ( section ) {
				if ( section.active() && section.isContextuallyActive() ) {
					activeCount += 1;
				}
			} );
			return ( activeCount !== 0 );
		},

		/**
		 * Update UI to reflect expanded state
		 *
		 * @since 4.1.0
		 *
		 * @param {Boolean}  expanded
		 * @param {Object}   args
		 * @param {Boolean}  args.unchanged
		 * @param {Callback} args.completeCallback
		 */
		onChangeExpanded: function ( expanded, args ) {

			// Immediately call the complete callback if there were no changes
			if ( args.unchanged ) {
				if ( args.completeCallback ) {
					args.completeCallback();
				}
				return;
			}

			// Note: there is a second argument 'args' passed
			var position, scroll,
				panel = this,
				section = panel.container.closest( '.accordion-section' ),
				overlay = section.closest( '.wp-full-overlay' ),
				container = section.closest( '.accordion-container' ),
				siblings = container.find( '.open' ),
				topPanel = overlay.find( '#customize-theme-controls > ul > .accordion-section > .accordion-section-title' ).add( '#customize-info > .accordion-section-title' ),
				backBtn = overlay.find( '.control-panel-back' ),
				panelTitle = section.find( '.accordion-section-title' ).first(),
				content = section.find( '.control-panel-content' );

			if ( expanded ) {

				// Collapse any sibling sections/panels
				api.section.each( function ( section ) {
					if ( ! section.panel() ) {
						section.collapse( { duration: 0 } );
					}
				});
				api.panel.each( function ( otherPanel ) {
					if ( panel !== otherPanel ) {
						otherPanel.collapse( { duration: 0 } );
					}
				});

				content.show( 0, function() {
					position = content.offset().top;
					scroll = container.scrollTop();
					content.css( 'margin-top', ( 45 - position - scroll ) );
					section.addClass( 'current-panel' );
					overlay.addClass( 'in-sub-panel' );
					container.scrollTop( 0 );
					if ( args.completeCallback ) {
						args.completeCallback();
					}
				} );
				topPanel.attr( 'tabindex', '-1' );
				backBtn.attr( 'tabindex', '0' );
				backBtn.focus();
			} else {
				siblings.removeClass( 'open' );
				section.removeClass( 'current-panel' );
				overlay.removeClass( 'in-sub-panel' );
				content.delay( 180 ).hide( 0, function() {
					content.css( 'margin-top', 'inherit' ); // Reset
					if ( args.completeCallback ) {
						args.completeCallback();
					}
				} );
				topPanel.attr( 'tabindex', '0' );
				backBtn.attr( 'tabindex', '-1' );
				panelTitle.focus();
				container.scrollTop( 0 );
			}
		}
	});

	/**
	 * A Customizer Control.
	 *
	 * A control provides a UI element that allows a user to modify a Customizer Setting.
	 *
	 * @see PHP class WP_Customize_Control.
	 *
	 * @class
	 * @augments wp.customize.Class
	 *
	 * @param {string} id                            Unique identifier for the control instance.
	 * @param {object} options                       Options hash for the control instance.
	 * @param {object} options.params
	 * @param {object} options.params.type           Type of control (e.g. text, radio, dropdown-pages, etc.)
	 * @param {string} options.params.content        The HTML content for the control.
	 * @param {string} options.params.priority       Order of priority to show the control within the section.
	 * @param {string} options.params.active
	 * @param {string} options.params.section
	 * @param {string} options.params.label
	 * @param {string} options.params.description
	 * @param {string} options.params.instanceNumber Order in which this instance was created in relation to other instances.
	 */
	api.Control = api.Class.extend({
		defaultActiveArguments: { duration: 'fast', completeCallback: $.noop },

		initialize: function( id, options ) {
			var control = this,
				nodes, radios, settings;

			control.params = {};
			$.extend( control, options || {} );
			control.id = id;
			control.selector = '#customize-control-' + id.replace( /\]/g, '' ).replace( /\[/g, '-' );
			control.templateSelector = 'customize-control-' + control.params.type + '-content';
			control.container = control.params.content ? $( control.params.content ) : $( control.selector );

			control.deferred = {
				embedded: new $.Deferred()
			};
			control.section = new api.Value();
			control.priority = new api.Value();
			control.active = new api.Value();
			control.activeArgumentsQueue = [];

			control.elements = [];

			nodes  = control.container.find('[data-customize-setting-link]');
			radios = {};

			nodes.each( function() {
				var node = $( this ),
					name;

				if ( node.is( ':radio' ) ) {
					name = node.prop( 'name' );
					if ( radios[ name ] ) {
						return;
					}

					radios[ name ] = true;
					node = nodes.filter( '[name="' + name + '"]' );
				}

				api( node.data( 'customizeSettingLink' ), function( setting ) {
					var element = new api.Element( node );
					control.elements.push( element );
					element.sync( setting );
					element.set( setting() );
				});
			});

			control.active.bind( function ( active ) {
				var args = control.activeArgumentsQueue.shift();
				args = $.extend( {}, control.defaultActiveArguments, args );
				control.onChangeActive( active, args );
			} );

			control.section.set( control.params.section );
			control.priority.set( isNaN( control.params.priority ) ? 10 : control.params.priority );
			control.active.set( control.params.active );

			api.utils.bubbleChildValueChanges( control, [ 'section', 'priority', 'active' ] );

			// Associate this control with its settings when they are created
			settings = $.map( control.params.settings, function( value ) {
				return value;
			});
			api.apply( api, settings.concat( function () {
				var key;

				control.settings = {};
				for ( key in control.params.settings ) {
					control.settings[ key ] = api( control.params.settings[ key ] );
				}

				control.setting = control.settings['default'] || null;

				control.embed();
			}) );

			control.deferred.embedded.done( function () {
				control.ready();
			});
		},

		/**
		 * Embed the control into the page.
		 */
		embed: function () {
			var control = this,
				inject;

			// Watch for changes to the section state
			inject = function ( sectionId ) {
				var parentContainer;
				if ( ! sectionId ) { // @todo allow a control to be embedded without a section, for instance a control embedded in the frontend
					return;
				}
				// Wait for the section to be registered
				api.section( sectionId, function ( section ) {
					// Wait for the section to be ready/initialized
					section.deferred.embedded.done( function () {
						parentContainer = section.container.find( 'ul:first' );
						if ( ! control.container.parent().is( parentContainer ) ) {
							parentContainer.append( control.container );
							control.renderContent();
						}
						control.deferred.embedded.resolve();
					});
				});
			};
			control.section.bind( inject );
			inject( control.section.get() );
		},

		/**
		 * Triggered when the control's markup has been injected into the DOM.
		 *
		 * @abstract
		 */
		ready: function() {},

		/**
		 * Normal controls do not expand, so just expand its parent
		 *
		 * @param {Object} [params]
		 */
		expand: function ( params ) {
			api.section( this.section() ).expand( params );
		},

		/**
		 * Bring the containing section and panel into view and then
		 * this control into view, focusing on the first input.
		 */
		focus: focus,

		/**
		 * Update UI in response to a change in the control's active state.
		 * This does not change the active state, it merely handles the behavior
		 * for when it does change.
		 *
		 * @since 4.1.0
		 *
		 * @param {Boolean}  active
		 * @param {Object}   args
		 * @param {Number}   args.duration
		 * @param {Callback} args.completeCallback
		 */
		onChangeActive: function ( active, args ) {
			if ( ! $.contains( document, this.container ) ) {
				// jQuery.fn.slideUp is not hiding an element if it is not in the DOM
				this.container.toggle( active );
				if ( args.completeCallback ) {
					args.completeCallback();
				}
			} else if ( active ) {
				this.container.slideDown( args.duration, args.completeCallback );
			} else {
				this.container.slideUp( args.duration, args.completeCallback );
			}
		},

		/**
		 * @deprecated 4.1.0 Use this.onChangeActive() instead.
		 */
		toggle: function ( active ) {
			return this.onChangeActive( active, this.defaultActiveArguments );
		},

		/**
		 * Shorthand way to enable the active state.
		 *
		 * @since 4.1.0
		 *
		 * @param {Object} [params]
		 * @returns {Boolean} false if already active
		 */
		activate: Container.prototype.activate,

		/**
		 * Shorthand way to disable the active state.
		 *
		 * @since 4.1.0
		 *
		 * @param {Object} [params]
		 * @returns {Boolean} false if already inactive
		 */
		deactivate: Container.prototype.deactivate,

		/**
		 * Re-use _toggleActive from Container class.
		 *
		 * @access private
		 */
		_toggleActive: Container.prototype._toggleActive,

		dropdownInit: function() {
			var control      = this,
				statuses     = this.container.find('.dropdown-status'),
				params       = this.params,
				toggleFreeze = false,
				update       = function( to ) {
					if ( typeof to === 'string' && params.statuses && params.statuses[ to ] )
						statuses.html( params.statuses[ to ] ).show();
					else
						statuses.hide();
				};

			// Support the .dropdown class to open/close complex elements
			this.container.on( 'click keydown', '.dropdown', function( event ) {
				if ( api.utils.isKeydownButNotEnterEvent( event ) ) {
					return;
				}

				event.preventDefault();

				if (!toggleFreeze)
					control.container.toggleClass('open');

				if ( control.container.hasClass('open') )
					control.container.parent().parent().find('li.library-selected').focus();

				// Don't want to fire focus and click at same time
				toggleFreeze = true;
				setTimeout(function () {
					toggleFreeze = false;
				}, 400);
			});

			this.setting.bind( update );
			update( this.setting() );
		},

		/**
		 * Render the control from its JS template, if it exists.
		 *
		 * The control's container must already exist in the DOM.
		 *
		 * @since 4.1.0
		 */
		renderContent: function () {
			var template,
				control = this;

			// Replace the container element's content with the control.
			if ( 0 !== $( '#tmpl-' + control.templateSelector ).length ) {
				template = wp.template( control.templateSelector );
				if ( template && control.container ) {
					control.container.html( template( control.params ) );
				}
			}
		}
	});

	/**
	 * A colorpicker control.
	 *
	 * @class
	 * @augments wp.customize.Control
	 * @augments wp.customize.Class
	 */
	api.ColorControl = api.Control.extend({
		ready: function() {
			var control = this,
				picker = this.container.find('.color-picker-hex');

			picker.val( control.setting() ).wpColorPicker({
				change: function() {
					control.setting.set( picker.wpColorPicker('color') );
				},
				clear: function() {
					control.setting.set( false );
				}
			});

			this.setting.bind( function ( value ) {
				picker.val( value );
				picker.wpColorPicker( 'color', value );
			});
		}
	});

	/**
	 * An upload control, which utilizes the media modal.
	 *
	 * @class
	 * @augments wp.customize.Control
	 * @augments wp.customize.Class
	 */
	api.UploadControl = api.Control.extend({

		/**
		 * When the control's DOM structure is ready,
		 * set up internal event bindings.
		 */
		ready: function() {
			var control = this;
			// Shortcut so that we don't have to use _.bind every time we add a callback.
			_.bindAll( control, 'restoreDefault', 'removeFile', 'openFrame', 'select' );

			// Bind events, with delegation to facilitate re-rendering.
			control.container.on( 'click keydown', '.upload-button', control.openFrame );
			control.container.on( 'click keydown', '.thumbnail-image img', control.openFrame );
			control.container.on( 'click keydown', '.default-button', control.restoreDefault );
			control.container.on( 'click keydown', '.remove-button', control.removeFile );

			// Re-render whenever the control's setting changes.
			control.setting.bind( function () { control.renderContent(); } );
		},

		/**
		 * Open the media modal.
		 */
		openFrame: function( event ) {
			if ( api.utils.isKeydownButNotEnterEvent( event ) ) {
				return;
			}

			event.preventDefault();

			if ( ! this.frame ) {
				this.initFrame();
			}

			this.frame.open();
		},

		/**
		 * Create a media modal select frame, and store it so the instance can be reused when needed.
		 */
		initFrame: function() {
			this.frame = wp.media({
				button: {
					text: this.params.button_labels.frame_button
				},
				states: [
					new wp.media.controller.Library({
						title:     this.params.button_labels.frame_title,
						library:   wp.media.query({ type: this.params.mime_type }),
						multiple:  false,
						date:      false
					})
				]
			});

			// When a file is selected, run a callback.
			this.frame.on( 'select', this.select );
		},

		/**
		 * Callback handler for when an attachment is selected in the media modal.
		 * Gets the selected image information, and sets it within the control.
		 */
		select: function() {
			// Get the attachment from the modal frame.
			var attachment = this.frame.state().get( 'selection' ).first().toJSON();

			this.params.attachment = attachment;

			// Set the Customizer setting; the callback takes care of rendering.
			this.setting( attachment.url );
		},

		/**
		 * Reset the setting to the default value.
		 */
		restoreDefault: function( event ) {
			if ( api.utils.isKeydownButNotEnterEvent( event ) ) {
				return;
			}
			event.preventDefault();

			this.params.attachment = this.params.defaultAttachment;
			this.setting( this.params.defaultAttachment.url );
 		},

		/**
		 * Called when the "Remove" link is clicked. Empties the setting.
		 *
		 * @param {object} event jQuery Event object
		 */
		removeFile: function( event ) {
			if ( api.utils.isKeydownButNotEnterEvent( event ) ) {
				return;
			}
			event.preventDefault();

			this.params.attachment = {};
			this.setting( '' );
			this.renderContent(); // Not bound to setting change when emptying.
		},

		// @deprecated
		success: function() {},

		// @deprecated
		removerVisibility: function() {}
	});

	/**
	 * A control for uploading images.
	 *
	 * This control no longer needs to do anything more
	 * than what the upload control does in JS.
	 *
	 * @class
	 * @augments wp.customize.UploadControl
	 * @augments wp.customize.Control
	 * @augments wp.customize.Class
	 */
	api.ImageControl = api.UploadControl.extend({
		// @deprecated
		thumbnailSrc: function() {}
	});

	/**
	 * A control for uploading background images.
	 *
	 * @class
	 * @augments wp.customize.UploadControl
	 * @augments wp.customize.Control
	 * @augments wp.customize.Class
	 */
	api.BackgroundControl = api.UploadControl.extend({

		/**
		 * When the control's DOM structure is ready,
		 * set up internal event bindings.
		 */
		ready: function() {
			api.UploadControl.prototype.ready.apply( this, arguments );
		},

		/**
		 * Callback handler for when an attachment is selected in the media modal.
		 * Does an additional AJAX request for setting the background context.
		 */
		select: function() {
			api.UploadControl.prototype.select.apply( this, arguments );

			wp.ajax.post( 'custom-background-add', {
				nonce: _wpCustomizeBackground.nonces.add,
				wp_customize: 'on',
				theme: api.settings.theme.stylesheet,
				attachment_id: this.params.attachment.id
			} );
		}
	});

	/**
	 * @class
	 * @augments wp.customize.Control
	 * @augments wp.customize.Class
	 */
	api.HeaderControl = api.Control.extend({
		ready: function() {
			this.btnRemove        = $('#customize-control-header_image .actions .remove');
			this.btnNew           = $('#customize-control-header_image .actions .new');

			_.bindAll(this, 'openMedia', 'removeImage');

			this.btnNew.on( 'click', this.openMedia );
			this.btnRemove.on( 'click', this.removeImage );

			api.HeaderTool.currentHeader = new api.HeaderTool.ImageModel();

			new api.HeaderTool.CurrentView({
				model: api.HeaderTool.currentHeader,
				el: '#customize-control-header_image .current .container'
			});

			new api.HeaderTool.ChoiceListView({
				collection: api.HeaderTool.UploadsList = new api.HeaderTool.ChoiceList(),
				el: '#customize-control-header_image .choices .uploaded .list'
			});

			new api.HeaderTool.ChoiceListView({
				collection: api.HeaderTool.DefaultsList = new api.HeaderTool.DefaultsList(),
				el: '#customize-control-header_image .choices .default .list'
			});

			api.HeaderTool.combinedList = api.HeaderTool.CombinedList = new api.HeaderTool.CombinedList([
				api.HeaderTool.UploadsList,
				api.HeaderTool.DefaultsList
			]);
		},

		/**
		 * Returns a set of options, computed from the attached image data and
		 * theme-specific data, to be fed to the imgAreaSelect plugin in
		 * wp.media.view.Cropper.
		 *
		 * @param {wp.media.model.Attachment} attachment
		 * @param {wp.media.controller.Cropper} controller
		 * @returns {Object} Options
		 */
		calculateImageSelectOptions: function(attachment, controller) {
			var xInit = parseInt(_wpCustomizeHeader.data.width, 10),
				yInit = parseInt(_wpCustomizeHeader.data.height, 10),
				flexWidth = !! parseInt(_wpCustomizeHeader.data['flex-width'], 10),
				flexHeight = !! parseInt(_wpCustomizeHeader.data['flex-height'], 10),
				ratio, xImg, yImg, realHeight, realWidth,
				imgSelectOptions;

			realWidth = attachment.get('width');
			realHeight = attachment.get('height');

			this.headerImage = new api.HeaderTool.ImageModel();
			this.headerImage.set({
				themeWidth: xInit,
				themeHeight: yInit,
				themeFlexWidth: flexWidth,
				themeFlexHeight: flexHeight,
				imageWidth: realWidth,
				imageHeight: realHeight
			});

			controller.set( 'canSkipCrop', ! this.headerImage.shouldBeCropped() );

			ratio = xInit / yInit;
			xImg = realWidth;
			yImg = realHeight;

			if ( xImg / yImg > ratio ) {
				yInit = yImg;
				xInit = yInit * ratio;
			} else {
				xInit = xImg;
				yInit = xInit / ratio;
			}

			imgSelectOptions = {
				handles: true,
				keys: true,
				instance: true,
				persistent: true,
				imageWidth: realWidth,
				imageHeight: realHeight,
				x1: 0,
				y1: 0,
				x2: xInit,
				y2: yInit
			};

			if (flexHeight === false && flexWidth === false) {
				imgSelectOptions.aspectRatio = xInit + ':' + yInit;
			}
			if (flexHeight === false ) {
				imgSelectOptions.maxHeight = yInit;
			}
			if (flexWidth === false ) {
				imgSelectOptions.maxWidth = xInit;
			}

			return imgSelectOptions;
		},

		/**
		 * Sets up and opens the Media Manager in order to select an image.
		 * Depending on both the size of the image and the properties of the
		 * current theme, a cropping step after selection may be required or
		 * skippable.
		 *
		 * @param {event} event
		 */
		openMedia: function(event) {
			var l10n = _wpMediaViewsL10n;

			event.preventDefault();

			this.frame = wp.media({
				button: {
					text: l10n.selectAndCrop,
					close: false
				},
				states: [
					new wp.media.controller.Library({
						title:     l10n.chooseImage,
						library:   wp.media.query({ type: 'image' }),
						multiple:  false,
						date:      false,
						priority:  20,
						suggestedWidth: _wpCustomizeHeader.data.width,
						suggestedHeight: _wpCustomizeHeader.data.height
					}),
					new wp.media.controller.Cropper({
						imgSelectOptions: this.calculateImageSelectOptions
					})
				]
			});

			this.frame.on('select', this.onSelect, this);
			this.frame.on('cropped', this.onCropped, this);
			this.frame.on('skippedcrop', this.onSkippedCrop, this);

			this.frame.open();
		},

		/**
		 * After an image is selected in the media modal,
		 * switch to the cropper state.
		 */
		onSelect: function() {
			this.frame.setState('cropper');
		},

		/**
		 * After the image has been cropped, apply the cropped image data to the setting.
		 *
		 * @param {object} croppedImage Cropped attachment data.
		 */
		onCropped: function(croppedImage) {
			var url = croppedImage.post_content,
				attachmentId = croppedImage.attachment_id,
				w = croppedImage.width,
				h = croppedImage.height;
			this.setImageFromURL(url, attachmentId, w, h);
		},

		/**
		 * If cropping was skipped, apply the image data directly to the setting.
		 *
		 * @param {object} selection
		 */
		onSkippedCrop: function(selection) {
			var url = selection.get('url'),
				w = selection.get('width'),
				h = selection.get('height');
			this.setImageFromURL(url, selection.id, w, h);
		},

		/**
		 * Creates a new wp.customize.HeaderTool.ImageModel from provided
		 * header image data and inserts it into the user-uploaded headers
		 * collection.
		 *
		 * @param {String} url
		 * @param {Number} attachmentId
		 * @param {Number} width
		 * @param {Number} height
		 */
		setImageFromURL: function(url, attachmentId, width, height) {
			var choice, data = {};

			data.url = url;
			data.thumbnail_url = url;
			data.timestamp = _.now();

			if (attachmentId) {
				data.attachment_id = attachmentId;
			}

			if (width) {
				data.width = width;
			}

			if (height) {
				data.height = height;
			}

			choice = new api.HeaderTool.ImageModel({
				header: data,
				choice: url.split('/').pop()
			});
			api.HeaderTool.UploadsList.add(choice);
			api.HeaderTool.currentHeader.set(choice.toJSON());
			choice.save();
			choice.importImage();
		},

		/**
		 * Triggers the necessary events to deselect an image which was set as
		 * the currently selected one.
		 */
		removeImage: function() {
			api.HeaderTool.currentHeader.trigger('hide');
			api.HeaderTool.CombinedList.trigger('control:removeImage');
		}

	});

	// Change objects contained within the main customize object to Settings.
	api.defaultConstructor = api.Setting;

	// Create the collections for Controls, Sections and Panels.
	api.control = new api.Values({ defaultConstructor: api.Control });
	api.section = new api.Values({ defaultConstructor: api.Section });
	api.panel = new api.Values({ defaultConstructor: api.Panel });

	/**
	 * @class
	 * @augments wp.customize.Messenger
	 * @augments wp.customize.Class
	 * @mixes wp.customize.Events
	 */
	api.PreviewFrame = api.Messenger.extend({
		sensitivity: 2000,

		initialize: function( params, options ) {
			var deferred = $.Deferred();

			// This is the promise object.
			deferred.promise( this );

			this.container = params.container;
			this.signature = params.signature;

			$.extend( params, { channel: api.PreviewFrame.uuid() });

			api.Messenger.prototype.initialize.call( this, params, options );

			this.add( 'previewUrl', params.previewUrl );

			this.query = $.extend( params.query || {}, { customize_messenger_channel: this.channel() });

			this.run( deferred );
		},

		run: function( deferred ) {
			var self   = this,
				loaded = false,
				ready  = false;

			if ( this._ready ) {
				this.unbind( 'ready', this._ready );
			}

			this._ready = function() {
				ready = true;

				if ( loaded ) {
					deferred.resolveWith( self );
				}
			};

			this.bind( 'ready', this._ready );

			this.bind( 'ready', function ( data ) {
				if ( ! data ) {
					return;
				}

				/*
				 * Walk over all panels, sections, and controls and set their
				 * respective active states to true if the preview explicitly
				 * indicates as such.
				 */
				var constructs = {
					panel: data.activePanels,
					section: data.activeSections,
					control: data.activeControls
				};
				_( constructs ).each( function ( activeConstructs, type ) {
					api[ type ].each( function ( construct, id ) {
						var active = !! ( activeConstructs && activeConstructs[ id ] );
						construct.active( active );
					} );
				} );
			} );

			this.request = $.ajax( this.previewUrl(), {
				type: 'POST',
				data: this.query,
				xhrFields: {
					withCredentials: true
				}
			} );

			this.request.fail( function() {
				deferred.rejectWith( self, [ 'request failure' ] );
			});

			this.request.done( function( response ) {
				var location = self.request.getResponseHeader('Location'),
					signature = self.signature,
					index;

				// Check if the location response header differs from the current URL.
				// If so, the request was redirected; try loading the requested page.
				if ( location && location !== self.previewUrl() ) {
					deferred.rejectWith( self, [ 'redirect', location ] );
					return;
				}

				// Check if the user is not logged in.
				if ( '0' === response ) {
					self.login( deferred );
					return;
				}

				// Check for cheaters.
				if ( '-1' === response ) {
					deferred.rejectWith( self, [ 'cheatin' ] );
					return;
				}

				// Check for a signature in the request.
				index = response.lastIndexOf( signature );
				if ( -1 === index || index < response.lastIndexOf('</html>') ) {
					deferred.rejectWith( self, [ 'unsigned' ] );
					return;
				}

				// Strip the signature from the request.
				response = response.slice( 0, index ) + response.slice( index + signature.length );

				// Create the iframe and inject the html content.
				self.iframe = $('<iframe />').appendTo( self.container );

				// Bind load event after the iframe has been added to the page;
				// otherwise it will fire when injected into the DOM.
				self.iframe.one( 'load', function() {
					loaded = true;

					if ( ready ) {
						deferred.resolveWith( self );
					} else {
						setTimeout( function() {
							deferred.rejectWith( self, [ 'ready timeout' ] );
						}, self.sensitivity );
					}
				});

				self.targetWindow( self.iframe[0].contentWindow );

				self.targetWindow().document.open();
				self.targetWindow().document.write( response );
				self.targetWindow().document.close();
			});
		},

		login: function( deferred ) {
			var self = this,
				reject;

			reject = function() {
				deferred.rejectWith( self, [ 'logged out' ] );
			};

			if ( this.triedLogin )
				return reject();

			// Check if we have an admin cookie.
			$.get( api.settings.url.ajax, {
				action: 'logged-in'
			}).fail( reject ).done( function( response ) {
				var iframe;

				if ( '1' !== response )
					reject();

				iframe = $('<iframe src="' + self.previewUrl() + '" />').hide();
				iframe.appendTo( self.container );
				iframe.load( function() {
					self.triedLogin = true;

					iframe.remove();
					self.run( deferred );
				});
			});
		},

		destroy: function() {
			api.Messenger.prototype.destroy.call( this );
			this.request.abort();

			if ( this.iframe )
				this.iframe.remove();

			delete this.request;
			delete this.iframe;
			delete this.targetWindow;
		}
	});

	(function(){
		var uuid = 0;
		/**
		 * Create a universally unique identifier.
		 *
		 * @return {int}
		 */
		api.PreviewFrame.uuid = function() {
			return 'preview-' + uuid++;
		};
	}());

	/**
	 * Set the document title of the customizer.
	 *
	 * @since 4.1.0
	 *
	 * @param {string} documentTitle
	 */
	api.setDocumentTitle = function ( documentTitle ) {
		var tmpl, title;
		tmpl = api.settings.documentTitleTmpl;
		title = tmpl.replace( '%s', documentTitle );
		document.title = title;
		if ( window !== window.parent ) {
			window.parent.document.title = document.title;
		}
	};

	/**
	 * @class
	 * @augments wp.customize.Messenger
	 * @augments wp.customize.Class
	 * @mixes wp.customize.Events
	 */
	api.Previewer = api.Messenger.extend({
		refreshBuffer: 250,

		/**
		 * Requires params:
		 *  - container  - a selector or jQuery element
		 *  - previewUrl - the URL of preview frame
		 */
		initialize: function( params, options ) {
			var self = this,
				rscheme = /^https?/;

			$.extend( this, options || {} );
			this.deferred = {
				active: $.Deferred()
			};

			/*
			 * Wrap this.refresh to prevent it from hammering the servers:
			 *
			 * If refresh is called once and no other refresh requests are
			 * loading, trigger the request immediately.
			 *
			 * If refresh is called while another refresh request is loading,
			 * debounce the refresh requests:
			 * 1. Stop the loading request (as it is instantly outdated).
			 * 2. Trigger the new request once refresh hasn't been called for
			 *    self.refreshBuffer milliseconds.
			 */
			this.refresh = (function( self ) {
				var refresh  = self.refresh,
					callback = function() {
						timeout = null;
						refresh.call( self );
					},
					timeout;

				return function() {
					if ( typeof timeout !== 'number' ) {
						if ( self.loading ) {
							self.abort();
						} else {
							return callback();
						}
					}

					clearTimeout( timeout );
					timeout = setTimeout( callback, self.refreshBuffer );
				};
			})( this );

			this.container   = api.ensure( params.container );
			this.allowedUrls = params.allowedUrls;
			this.signature   = params.signature;

			params.url = window.location.href;

			api.Messenger.prototype.initialize.call( this, params );

			this.add( 'scheme', this.origin() ).link( this.origin ).setter( function( to ) {
				var match = to.match( rscheme );
				return match ? match[0] : '';
			});

			// Limit the URL to internal, front-end links.
			//
			// If the frontend and the admin are served from the same domain, load the
			// preview over ssl if the Customizer is being loaded over ssl. This avoids
			// insecure content warnings. This is not attempted if the admin and frontend
			// are on different domains to avoid the case where the frontend doesn't have
			// ssl certs.

			this.add( 'previewUrl', params.previewUrl ).setter( function( to ) {
				var result;

				// Check for URLs that include "/wp-admin/" or end in "/wp-admin".
				// Strip hashes and query strings before testing.
				if ( /\/wp-admin(\/|$)/.test( to.replace( /[#?].*$/, '' ) ) )
					return null;

				// Attempt to match the URL to the control frame's scheme
				// and check if it's allowed. If not, try the original URL.
				$.each([ to.replace( rscheme, self.scheme() ), to ], function( i, url ) {
					$.each( self.allowedUrls, function( i, allowed ) {
						var path;

						allowed = allowed.replace( /\/+$/, '' );
						path = url.replace( allowed, '' );

						if ( 0 === url.indexOf( allowed ) && /^([/#?]|$)/.test( path ) ) {
							result = url;
							return false;
						}
					});
					if ( result )
						return false;
				});

				// If we found a matching result, return it. If not, bail.
				return result ? result : null;
			});

			// Refresh the preview when the URL is changed (but not yet).
			this.previewUrl.bind( this.refresh );

			this.scroll = 0;
			this.bind( 'scroll', function( distance ) {
				this.scroll = distance;
			});

			// Update the URL when the iframe sends a URL message.
			this.bind( 'url', this.previewUrl );

			// Update the document title when the preview changes.
			this.bind( 'documentTitle', function ( title ) {
				api.setDocumentTitle( title );
			} );
		},

		query: function() {},

		abort: function() {
			if ( this.loading ) {
				this.loading.destroy();
				delete this.loading;
			}
		},

		refresh: function() {
			var self = this;

			this.abort();

			this.loading = new api.PreviewFrame({
				url:        this.url(),
				previewUrl: this.previewUrl(),
				query:      this.query() || {},
				container:  this.container,
				signature:  this.signature
			});

			this.loading.done( function() {
				// 'this' is the loading frame
				this.bind( 'synced', function() {
					if ( self.preview )
						self.preview.destroy();
					self.preview = this;
					delete self.loading;

					self.targetWindow( this.targetWindow() );
					self.channel( this.channel() );

					self.deferred.active.resolve();
					self.send( 'active' );
				});

				this.send( 'sync', {
					scroll:   self.scroll,
					settings: api.get()
				});
			});

			this.loading.fail( function( reason, location ) {
				if ( 'redirect' === reason && location )
					self.previewUrl( location );

				if ( 'logged out' === reason ) {
					if ( self.preview ) {
						self.preview.destroy();
						delete self.preview;
					}

					self.login().done( self.refresh );
				}

				if ( 'cheatin' === reason )
					self.cheatin();
			});
		},

		login: function() {
			var previewer = this,
				deferred, messenger, iframe;

			if ( this._login )
				return this._login;

			deferred = $.Deferred();
			this._login = deferred.promise();

			messenger = new api.Messenger({
				channel: 'login',
				url:     api.settings.url.login
			});

			iframe = $('<iframe src="' + api.settings.url.login + '" />').appendTo( this.container );

			messenger.targetWindow( iframe[0].contentWindow );

			messenger.bind( 'login', function() {
				iframe.remove();
				messenger.destroy();
				delete previewer._login;
				deferred.resolve();
			});

			return this._login;
		},

		cheatin: function() {
			$( document.body ).empty().addClass('cheatin').append( '<p>' + api.l10n.cheatin + '</p>' );
		}
	});

	api.controlConstructor = {
		color:  api.ColorControl,
		upload: api.UploadControl,
		image:  api.ImageControl,
		header: api.HeaderControl,
		background: api.BackgroundControl
	};
	api.panelConstructor = {};
	api.sectionConstructor = {};

	$( function() {
		api.settings = window._wpCustomizeSettings;
		api.l10n = window._wpCustomizeControlsL10n;

		// Check if we can run the Customizer.
		if ( ! api.settings ) {
			return;
		}

		// Redirect to the fallback preview if any incompatibilities are found.
		if ( ! $.support.postMessage || ( ! $.support.cors && api.settings.isCrossDomain ) )
			return window.location = api.settings.url.fallback;

		var parent, topFocus,
			body = $( document.body ),
			overlay = body.children( '.wp-full-overlay' ),
			title = $( '#customize-info .theme-name.site-title' ),
			closeBtn = $( '.customize-controls-close' ),
			saveBtn = $( '#save' );

		// Prevent the form from saving when enter is pressed on an input or select element.
		$('#customize-controls').on( 'keydown', function( e ) {
			var isEnter = ( 13 === e.which ),
				$el = $( e.target );

			if ( isEnter && ( $el.is( 'input:not([type=button])' ) || $el.is( 'select' ) ) ) {
				e.preventDefault();
			}
		});

		// Expand/Collapse the main customizer customize info.
		$( '#customize-info' ).find( '> .accordion-section-title' ).on( 'click keydown', function( event ) {
			if ( api.utils.isKeydownButNotEnterEvent( event ) ) {
				return;
			}
			event.preventDefault(); // Keep this AFTER the key filter above

			var section = $( this ).parent(),
				content = section.find( '.accordion-section-content:first' );

			if ( section.hasClass( 'cannot-expand' ) ) {
				return;
			}

			if ( section.hasClass( 'open' ) ) {
				section.toggleClass( 'open' );
				content.slideUp( api.Panel.prototype.defaultExpandedArguments.duration );
			} else {
				content.slideDown( api.Panel.prototype.defaultExpandedArguments.duration );
				section.toggleClass( 'open' );
			}
		});

		// Initialize Previewer
		api.previewer = new api.Previewer({
			container:   '#customize-preview',
			form:        '#customize-controls',
			previewUrl:  api.settings.url.preview,
			allowedUrls: api.settings.url.allowed,
			signature:   'WP_CUSTOMIZER_SIGNATURE'
		}, {

			nonce: api.settings.nonce,

			query: function() {
				var dirtyCustomized = {};
				api.each( function ( value, key ) {
					if ( value._dirty ) {
						dirtyCustomized[ key ] = value();
					}
				} );

				return {
					wp_customize: 'on',
					theme:      api.settings.theme.stylesheet,
					customized: JSON.stringify( dirtyCustomized ),
					nonce:      this.nonce.preview
				};
			},

			save: function() {
				var self  = this,
					query = $.extend( this.query(), {
						nonce:  this.nonce.save
					} ),
					processing = api.state( 'processing' ),
					submitWhenDoneProcessing,
					submit;

				body.addClass( 'saving' );

				submit = function () {
					var request = wp.ajax.post( 'customize_save', query );

					api.trigger( 'save', request );

					request.always( function () {
						body.removeClass( 'saving' );
					} );

					request.fail( function ( response ) {
						if ( '0' === response ) {
							response = 'not_logged_in';
						} else if ( '-1' === response ) {
							// Back-compat in case any other check_ajax_referer() call is dying
							response = 'invalid_nonce';
						}

						if ( 'invalid_nonce' === response ) {
							self.cheatin();
						} else if ( 'not_logged_in' === response ) {
							self.preview.iframe.hide();
							self.login().done( function() {
								self.save();
								self.preview.iframe.show();
							} );
						}
						api.trigger( 'error', response );
					} );

					request.done( function( response ) {
						// Clear setting dirty states
						api.each( function ( value ) {
							value._dirty = false;
						} );

						api.trigger( 'saved', response );
					} );
				};

				if ( 0 === processing() ) {
					submit();
				} else {
					submitWhenDoneProcessing = function () {
						if ( 0 === processing() ) {
							api.state.unbind( 'change', submitWhenDoneProcessing );
							submit();
						}
					};
					api.state.bind( 'change', submitWhenDoneProcessing );
				}

			}
		});

		// Refresh the nonces if the preview sends updated nonces over.
		api.previewer.bind( 'nonce', function( nonce ) {
			$.extend( this.nonce, nonce );
		});

		// Create Settings
		$.each( api.settings.settings, function( id, data ) {
			api.create( id, id, data.value, {
				transport: data.transport,
				previewer: api.previewer
			} );
		});

		// Create Panels
		$.each( api.settings.panels, function ( id, data ) {
			var constructor = api.panelConstructor[ data.type ] || api.Panel,
				panel;

			panel = new constructor( id, {
				params: data
			} );
			api.panel.add( id, panel );
		});

		// Create Sections
		$.each( api.settings.sections, function ( id, data ) {
			var constructor = api.sectionConstructor[ data.type ] || api.Section,
				section;

			section = new constructor( id, {
				params: data
			} );
			api.section.add( id, section );
		});

		// Create Controls
		$.each( api.settings.controls, function( id, data ) {
			var constructor = api.controlConstructor[ data.type ] || api.Control,
				control;

			control = new constructor( id, {
				params: data,
				previewer: api.previewer
			} );
			api.control.add( id, control );
		});

		// Focus the autofocused element
		_.each( [ 'panel', 'section', 'control' ], function ( type ) {
			var instance, id = api.settings.autofocus[ type ];
			if ( id && api[ type ]( id ) ) {
				instance = api[ type ]( id );
				// Wait until the element is embedded in the DOM
				instance.deferred.embedded.done( function () {
					// Wait until the preview has activated and so active panels, sections, controls have been set
					api.previewer.deferred.active.done( function () {
						instance.focus();
					});
				});
			}
		});

		/**
		 * Sort panels, sections, controls by priorities. Hide empty sections and panels.
		 *
		 * @since 4.1.0
		 */
		api.reflowPaneContents = _.bind( function () {

			var appendContainer, activeElement, rootContainers, rootNodes = [], wasReflowed = false;

			if ( document.activeElement ) {
				activeElement = $( document.activeElement );
			}

			// Sort the sections within each panel
			api.panel.each( function ( panel ) {
				var sections = panel.sections(),
					sectionContainers = _.pluck( sections, 'container' );
				rootNodes.push( panel );
				appendContainer = panel.container.find( 'ul:first' );
				if ( ! api.utils.areElementListsEqual( sectionContainers, appendContainer.children( '[id]' ) ) ) {
					_( sections ).each( function ( section ) {
						appendContainer.append( section.container );
					} );
					wasReflowed = true;
				}
			} );

			// Sort the controls within each section
			api.section.each( function ( section ) {
				var controls = section.controls(),
					controlContainers = _.pluck( controls, 'container' );
				if ( ! section.panel() ) {
					rootNodes.push( section );
				}
				appendContainer = section.container.find( 'ul:first' );
				if ( ! api.utils.areElementListsEqual( controlContainers, appendContainer.children( '[id]' ) ) ) {
					_( controls ).each( function ( control ) {
						appendContainer.append( control.container );
					} );
					wasReflowed = true;
				}
			} );

			// Sort the root panels and sections
			rootNodes.sort( api.utils.prioritySort );
			rootContainers = _.pluck( rootNodes, 'container' );
			appendContainer = $( '#customize-theme-controls' ).children( 'ul' ); // @todo This should be defined elsewhere, and to be configurable
			if ( ! api.utils.areElementListsEqual( rootContainers, appendContainer.children() ) ) {
				_( rootNodes ).each( function ( rootNode ) {
					appendContainer.append( rootNode.container );
				} );
				wasReflowed = true;
			}

			// Now re-trigger the active Value callbacks to that the panels and sections can decide whether they can be rendered
			api.panel.each( function ( panel ) {
				var value = panel.active();
				panel.active.callbacks.fireWith( panel.active, [ value, value ] );
			} );
			api.section.each( function ( section ) {
				var value = section.active();
				section.active.callbacks.fireWith( section.active, [ value, value ] );
			} );

			// Restore focus if there was a reflow and there was an active (focused) element
			if ( wasReflowed && activeElement ) {
				activeElement.focus();
			}
		}, api );
		api.bind( 'ready', api.reflowPaneContents );
		api.reflowPaneContents = _.debounce( api.reflowPaneContents, 100 );
		$( [ api.panel, api.section, api.control ] ).each( function ( i, values ) {
			values.bind( 'add', api.reflowPaneContents );
			values.bind( 'change', api.reflowPaneContents );
			values.bind( 'remove', api.reflowPaneContents );
		} );

		// Check if preview url is valid and load the preview frame.
		if ( api.previewer.previewUrl() ) {
			api.previewer.refresh();
		} else {
			api.previewer.previewUrl( api.settings.url.home );
		}

		// Save and activated states
		(function() {
			var state = new api.Values(),
				saved = state.create( 'saved' ),
				activated = state.create( 'activated' ),
				processing = state.create( 'processing' );

			state.bind( 'change', function() {
				if ( ! activated() ) {
					saveBtn.val( api.l10n.activate ).prop( 'disabled', false );
					closeBtn.find( '.screen-reader-text' ).text( api.l10n.cancel );

				} else if ( saved() ) {
					saveBtn.val( api.l10n.saved ).prop( 'disabled', true );
					closeBtn.find( '.screen-reader-text' ).text( api.l10n.close );

				} else {
					saveBtn.val( api.l10n.save ).prop( 'disabled', false );
					closeBtn.find( '.screen-reader-text' ).text( api.l10n.cancel );
				}
			});

			// Set default states.
			saved( true );
			activated( api.settings.theme.active );
			processing( 0 );

			api.bind( 'change', function() {
				state('saved').set( false );
			});

			api.bind( 'saved', function() {
				state('saved').set( true );
				state('activated').set( true );
			});

			activated.bind( function( to ) {
				if ( to )
					api.trigger( 'activated' );
			});

			// Expose states to the API.
			api.state = state;
		}());

		// Button bindings.
		saveBtn.click( function( event ) {
			api.previewer.save();
			event.preventDefault();
		}).keydown( function( event ) {
			if ( 9 === event.which ) // tab
				return;
			if ( 13 === event.which ) // enter
				api.previewer.save();
			event.preventDefault();
		});

		// Go back to the top-level Customizer accordion.
		$( '#customize-header-actions' ).on( 'click keydown', '.control-panel-back', function( event ) {
			if ( api.utils.isKeydownButNotEnterEvent( event ) ) {
				return;
			}

			event.preventDefault(); // Keep this AFTER the key filter above
			api.panel.each( function ( panel ) {
				panel.collapse();
			});
		});

		closeBtn.keydown( function( event ) {
			if ( 9 === event.which ) // tab
				return;
			if ( 13 === event.which ) // enter
				this.click();
			event.preventDefault();
		});

		$('.collapse-sidebar').on( 'click keydown', function( event ) {
			if ( api.utils.isKeydownButNotEnterEvent( event ) ) {
				return;
			}

			overlay.toggleClass( 'collapsed' ).toggleClass( 'expanded' );
			event.preventDefault();
		});

		// Bind site title display to the corresponding field.
		if ( title.length ) {
			$( '#customize-control-blogname input' ).on( 'input', function() {
				title.text( this.value );
			} );
		}

		// Create a potential postMessage connection with the parent frame.
		parent = new api.Messenger({
			url: api.settings.url.parent,
			channel: 'loader'
		});

		// If we receive a 'back' event, we're inside an iframe.
		// Send any clicks to the 'Return' link to the parent page.
		parent.bind( 'back', function() {
			closeBtn.on( 'click.customize-controls-close', function( event ) {
				event.preventDefault();
				parent.send( 'close' );
			});
		});

		// Prompt user with AYS dialog if leaving the Customizer with unsaved changes
		$( window ).on( 'beforeunload', function () {
			if ( ! api.state( 'saved' )() ) {
				return api.l10n.saveAlert;
			}
		} );

		// Pass events through to the parent.
		$.each( [ 'saved', 'change' ], function ( i, event ) {
			api.bind( event, function() {
				parent.send( event );
			});
		} );

		// When activated, let the loader handle redirecting the page.
		// If no loader exists, redirect the page ourselves (if a url exists).
		api.bind( 'activated', function() {
			if ( parent.targetWindow() )
				parent.send( 'activated', api.settings.url.activated );
			else if ( api.settings.url.activated )
				window.location = api.settings.url.activated;
		});

		// Initialize the connection with the parent frame.
		parent.send( 'ready' );

		// Control visibility for default controls
		$.each({
			'background_image': {
				controls: [ 'background_repeat', 'background_position_x', 'background_attachment' ],
				callback: function( to ) { return !! to; }
			},
			'show_on_front': {
				controls: [ 'page_on_front', 'page_for_posts' ],
				callback: function( to ) { return 'page' === to; }
			},
			'header_textcolor': {
				controls: [ 'header_textcolor' ],
				callback: function( to ) { return 'blank' !== to; }
			}
		}, function( settingId, o ) {
			api( settingId, function( setting ) {
				$.each( o.controls, function( i, controlId ) {
					api.control( controlId, function( control ) {
						var visibility = function( to ) {
							control.container.toggle( o.callback( to ) );
						};

						visibility( setting.get() );
						setting.bind( visibility );
					});
				});
			});
		});

		// Juggle the two controls that use header_textcolor
		api.control( 'display_header_text', function( control ) {
			var last = '';

			control.elements[0].unsync( api( 'header_textcolor' ) );

			control.element = new api.Element( control.container.find('input') );
			control.element.set( 'blank' !== control.setting() );

			control.element.bind( function( to ) {
				if ( ! to )
					last = api( 'header_textcolor' ).get();

				control.setting.set( to ? last : 'blank' );
			});

			control.setting.bind( function( to ) {
				control.element.set( 'blank' !== to );
			});
		});

		api.trigger( 'ready' );

		// Make sure left column gets focus
		topFocus = closeBtn;
		topFocus.focus();
		setTimeout(function () {
			topFocus.focus();
		}, 200);

	});

})( wp, jQuery );
