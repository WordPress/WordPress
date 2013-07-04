window.wp = window.wp || {};

(function($) {
	var revisions;

	revisions = wp.revisions = { model: {}, view: {}, controller: {}, router: {} };

	// Link settings.
	revisions.settings = typeof _wpRevisionsSettings === 'undefined' ? {} : _wpRevisionsSettings;


	/**
	 * ========================================================================
	 * MODELS
	 * ========================================================================
	 */
	revisions.model.Slider = Backbone.Model.extend({
		defaults: {
			value: 0,
			min: 0,
			max: 1,
			step: 1
		}
	});

	revisions.model.Revision = Backbone.Model.extend({});

	revisions.model.Revisions = Backbone.Collection.extend({
		model: revisions.model.Revision,

		comparator: function( revision ) {
			return revision.id;
		}
	});

	revisions.model.Field = Backbone.Model.extend({});

	revisions.model.Fields = Backbone.Collection.extend({
		model: revisions.model.Field
	});

	revisions.model.Diff = Backbone.Model.extend({
		initialize: function(attributes, options) {
			var fields = this.get('fields');
			this.unset('fields');

			this.fields = new revisions.model.Fields( fields );
		}
	});

	revisions.model.Diffs = Backbone.Collection.extend({
		initialize: function(models, options) {
			this.revisions = options.revisions;
			this.requests  = {};
		},

		model: revisions.model.Diff,

		ensure: function( id, context ) {
			var diff     = this.get( id );
			var request  = this.requests[ id ];
			var deferred = $.Deferred();
			var ids      = {};

			if ( diff ) {
				deferred.resolveWith( context, [ diff ] );
			} else {
				this.trigger( 'ensure:load', ids );
				_.each( ids, _.bind( function(id) {
					// Remove anything that has an ongoing request
					if ( this.requests[ id ] )
						delete ids[ id ];
				}, this ) );
				if ( ! request ) {
					// Always include the ID that started this ensure
					ids[ id ] = true;
					request   = this.load( _.keys( ids ) );
				}

				request.done( _.bind( function() {
					deferred.resolveWith( context, [ this.get( id ) ] );
				}, this ) );
			}

			return deferred.promise();
		},

		loadNew: function( comparisons ) {
			comparisons = _.object( comparisons, comparisons );
			_.each( comparisons, _.bind( function( id ) {
				// Exists
				if ( this.get( id ) )
					delete comparisons[ id ];
			}, this ) );
			comparisons = _.toArray( comparisons );
			return this.load( comparisons );
		},

		load: function( comparisons ) {
			// Our collection should only ever grow, never shrink, so remove: false
			return this.fetch({ data: { compare: comparisons }, remove: false });
		},

		loadLast: function( num ) {
			num     = num || 1;
			var ids = this.getProximalDiffIds();
			ids     = _.last( ids, num );

			if ( ids.length ) {
				return this.loadNew( ids );
			}
		},

		loadLastUnloaded: function( num ) {
			num     = num || 1;
			var ids = this.getUnloadedProximalDiffIds();
			ids     = _.last( ids, num );

			if ( ids.length ) {
				return this.loadNew( ids );
			}
		},

		getProximalDiffIds: function() {
			var previous = 0, ids = [];
			this.revisions.each( _.bind( function(revision) {
				ids.push( previous + ':' + revision.id );
				previous = revision.id;
			}, this ) );
			return ids;
		},

		getUnloadedProximalDiffIds: function() {
			var comparisons = this.getProximalDiffIds();
			comparisons     = _.object( comparisons, comparisons );
			_.each( comparisons, _.bind( function( id ) {
				// Exists
				if ( this.get( id ) )
					delete comparisons[ id ];
			}, this ) );
			return _.toArray( comparisons );
		},

		loadAllBy: function( chunkSize ) {
			chunkSize    = chunkSize || 20;
			var unloaded = this.getUnloadedProximalDiffIds();
			if ( unloaded.length ) {
				return this.loadLastUnloaded( chunkSize ).always( _.bind( function() {
					this.loadAllBy( chunkSize );
				}, this ) );
			}
		},

		sync: function( method, model, options ) {
			if ( 'read' === method ) {
				options         = options || {};
				options.context = this;
				options.data    = _.extend( options.data || {}, {
					action: 'get-revision-diffs',
					post_id: revisions.settings.postId
				});

				var deferred = wp.xhr.send( options );
				var requests = this.requests;

				// Record that we're requesting each diff.
				if ( options.data.compare ) {
					_.each( options.data.compare, function( id ) {
						requests[ id ] = deferred;
					});
				}

				// When the request completes, clear the stored request.
				deferred.always( function() {
					if ( options.data.compare ) {
						_.each( options.data.compare, function( id ) {
							delete requests[ id ];
						});
					}
				});

				return deferred;

			// Otherwise, fall back to `Backbone.sync()`.
			} else {
				return Backbone.Model.prototype.sync.apply( this, arguments );
			}
		}
	});


	revisions.model.FrameState = Backbone.Model.extend({
		initialize: function( attributes, options ) {
			this.revisions = options.revisions;
			this.diffs     = new revisions.model.Diffs( [], {revisions: this.revisions} );
			this.listenTo( this, 'change:from', this.updateDiffFrom );
			this.listenTo( this, 'change:to', this.updateDiffTo );
			this.revisionsRouter = new revisions.router.Router({ model: this });
		},

		updateDiffTo: function() {
			var from = this.get( 'from' );
			this.set( 'diffId', (from ? from.id : '0' ) + ':' + this.get('to').id );
		},

		updateDiffFrom: function() {
			if ( this.get( 'compareTwoMode' ) )
				this.updateDiffTo();
		}
	});


	/**
	 * ========================================================================
	 * VIEWS
	 * ========================================================================
	 */

	// The frame view. This contains the entire page.
	revisions.view.Frame = wp.Backbone.View.extend({
		tagName: 'div',
		className: 'revisions',
		template: wp.template('revisions-frame'),

		initialize: function() {
			this.model = new revisions.model.FrameState({}, {
				revisions: this.collection
			});

			this.listenTo( this.model, 'change:diffId', this.updateDiff );

			this.views.set( '.revisions-control-frame', new revisions.view.Controls({
				model: this.model
			}) );

			if ( this.model.revisions.length ) {
				var last = this.model.revisions.last(2);
				var attributes = { to: last.pop() };

				if ( last.length )
					attributes.from = last.pop();

				this.model.set( attributes );

				// Load the rest: first 10, then the rest by 50
				this.model.diffs.loadLastUnloaded( 10 ).always( _.bind( function() {
					this.model.diffs.loadAllBy( 50 );
				}, this ) );
			}
			Backbone.history.start();
		},

		render: function() {
			wp.Backbone.View.prototype.render.apply( this, arguments );

			$('#wpbody-content .wrap').append( this.el );
			this.views.ready();

			return this;
		},

		updateDiff: function() {
			this.model.diffs.ensure( this.model.get('diffId'), this ).done( function( diff ) {
				if ( this.model.get('diffId') !== diff.id )
					return;
				this.views.set( '.revisions-diff-frame', new revisions.view.Diff({
					model: diff
				}));
				this.model.trigger( 'renderDiff' );
			});
		}
	});

	// The control view.
	// This contains the revision slider, previous/next buttons, the meta info and the compare checkbox.
	revisions.view.Controls = wp.Backbone.View.extend({
		tagName: 'div',
		className: 'revisions-controls',

		initialize: function() {
			// Add the button view
			this.views.add( new revisions.view.Buttons({
				model: this.model
			}));

			// Add the checkbox view
			this.views.add( new revisions.view.Checkbox({
				model: this.model
			}));

			// Add the tooltip view
			this.views.add( new revisions.view.Tooltip({
				model: this.model
			}));
			// Add the Slider view
			this.views.add( new revisions.view.Slider({
				model: this.model
			}) );

			// Add the Meta view
			this.views.add( new revisions.view.Meta({
				model: this.model
			}) );
		}
	});

	// The meta view.
	// This contains the revision meta, and the restore button.
	revisions.view.Meta = wp.Backbone.View.extend({
		tagName: 'div',
		className: 'revisions-meta',
		template: wp.template('revisions-meta'),

		events: {
			'click #restore-revision': 'restoreRevision'
		},

		initialize: function() {
			this.listenTo( this.model, 'change:diffId', this.updateMeta );
		},

		restoreRevision: function() {
			var restoreUrl    = this.model.get('to').attributes.restoreUrl.replace(/&amp;/g, '&');
			document.location = restoreUrl;
		},

		updateMeta: function() {
			this.$el.html( this.template( this.model.toJSON() ) );
			if( this.model.get( 'to' ).attributes.current ) {
				$( '#restore-revision' ).prop( 'disabled', true );
			} else {
				$( '#restore-revision' ).prop( 'disabled', false );
			}
		}
	});

	// The checkbox view.
	// Encapsulates all of the configuration for the compare checkbox.
	revisions.view.Checkbox = wp.Backbone.View.extend({
		tagName: 'div',
		className: 'revisions-checkbox',
		template: wp.template( 'revisions-checkbox' ),

		events: {
			'click .compare-two-revisions': 'compareTwoToggle'
		},

		initialize: function() {
			this.$el.html( this.template() );
			this.listenTo( this.model, 'change:compareTwoMode', this.updateCompareTwoMode );
		},

		updateCompareTwoMode: function() {
			if ( this.model.get( 'compareTwoMode' ) ) {
				$( '.compare-two-revisions' ).parent().css('border', '1px solid #f00;').prop( 'checked', true );
				$( '.revisions-control-frame' ).addClass( 'comparing-two-revisions' );
				// in RTL mode the 'left handle' is the second in the slider, 'right' is first
				$( '.wp-slider a.ui-slider-handle' ).first().addClass( isRtl ? 'right-handle' : 'left-handle' );
				$( '.wp-slider a.ui-slider-handle' ).last().addClass( isRtl ? 'left-handle' : 'right-handle' );
			} else {
				$( '.compare-two-revisions' ).prop( 'checked', false );
				$( '.revisions-control-frame' ).removeClass( 'comparing-two-revisions' );
				$( '.wp-slider a.ui-slider-handle' ).removeClass( 'left-handle' ).removeClass( 'right-handle' );
			}

		},

		// Toggle the compare two mode feature when the compare two checkbox is checked.
		compareTwoToggle: function( event ) {
			// Activate compare two mode?
			if ( $( '.compare-two-revisions' ).is( ':checked' ) ) {
				this.model.set( { compareTwoMode: true } );
			} else {
				this.model.set( { compareTwoMode: false } );
			}

			// Update route
			this.model.revisionsRouter.navigateRoute( this.model.get( 'to').id, this.model.get( 'from' ).id );
		},

		ready: function() {
			// Hide compare two mode toggle when fewer than three revisions.
			if ( this.model.revisions.length < 3 )
				$( '.revision-toggle-compare-mode' ).hide();
		}

	});

	// The tooltip view.
	// Encapsulates the tooltip.
	revisions.view.Tooltip = wp.Backbone.View.extend({
		tagName: 'div',
		className: 'revisions-tooltip',
		template: wp.template( 'revisions-tooltip' ),

		initialize: function() {
			this.listenTo( this.model, 'change:sliderHovering', this.sliderHoveringChanged );
			this.listenTo( this.model, 'change:tooltipPosition', this.tooltipPositionChanged );
		},

		ready: function() {
		},

		// Show or hide tooltip based on sliderHovering is true
		sliderHoveringChanged: function() {
			if ( this.model.get( 'sliderHovering' ) ) {
				this.$el.show();
			} else {
				this.$el.hide();
			}
		},

		tooltipPositionChanged: function() {
			this.$el.html( this.template( this.model.revisions.at( this.model.get( 'hoveringAt') ).toJSON() ) );

			this.setTooltip( this.model.get( 'tooltipPosition' ) );
		},

		setTooltip: function( tooltipPosition ) {
			var offset = $( '.revisions-buttons' ).offset().left,
				calculatedX = tooltipPosition - offset;


			this.$el.find( '.ui-slider-tooltip' ).css( 'left', calculatedX );
			this.$el.find( '.arrow' ).css( 'left', calculatedX );
		}
	});

	// The buttons view.
	// Encapsulates all of the configuration for the previous/next buttons.
	revisions.view.Buttons = wp.Backbone.View.extend({
		tagName: 'div',
		className: 'revisions-buttons',
		template: wp.template( 'revisions-buttons' ),

		events: {
			'click #next': 'nextRevision',
			'click #previous': 'previousRevision'
		},

		initialize: function() {
			this.$el.html( this.template() );
		},

		ready: function() {
			this.listenTo( this.model, 'change:diffId', this.disabledButtonCheck );
		},

		// Go to a specific modelindex, taking into account RTL mode.
		gotoModel: function( toIndex ) {
			var attributes = {
				to: this.model.revisions.at( isRtl ? this.model.revisions.length - toIndex - 1 : toIndex ) // Reverse directions for RTL.
			};
			// If we're at the first revision, unset 'from'.
			if ( isRtl ? this.model.revisions.length - toIndex - 1 : toIndex ) // Reverse directions for RTL
				attributes.from = this.model.revisions.at( isRtl ? this.model.revisions.length - toIndex - 2 : toIndex - 1 );
			else
				this.model.unset('from', { silent: true });

			this.model.set( attributes );

			// Update route
			this.model.revisionsRouter.navigateRoute( attributes.to.id, attributes.from ? attributes.from.id : 0 );
		},

		// Go to the 'next' revision, direction takes into account RTL mode.
		nextRevision: function() {
			var toIndex = isRtl ? this.model.revisions.length - this.model.revisions.indexOf( this.model.get( 'to' ) ) - 1 : this.model.revisions.indexOf( this.model.get( 'to' ) );
			toIndex     = isRtl ? toIndex - 1 : toIndex + 1;
			this.gotoModel( toIndex );
		},

		// Go to the 'previous' revision, direction takes into account RTL mode.
		previousRevision: function() {
			var toIndex = isRtl ? this.model.revisions.length - this.model.revisions.indexOf( this.model.get( 'to' ) ) - 1 : this.model.revisions.indexOf( this.model.get( 'to' ) );
			toIndex     = isRtl ? toIndex + 1 : toIndex - 1;
			this.gotoModel( toIndex );
		},

		// Check to see if the Previous or Next buttons need to be disabled or enabled.
		disabledButtonCheck: function() {
			var maxVal = this.model.revisions.length - 1,
				minVal = 0,
				next = $( '.revisions-next .button' ),
				previous = $( '.revisions-previous .button' ),
				val = this.model.revisions.indexOf( this.model.get( 'to' ) );

			// Disable "Next" button if you're on the last node.
			if ( maxVal === val )
				next.prop( 'disabled', true );
			else
				next.prop( 'disabled', false );

			// Disable "Previous" button if you're on the first node.
			if ( minVal === val )
				previous.prop( 'disabled', true );
			else
				previous.prop( 'disabled', false );
		}
	});


	// The slider view.
	// Encapsulates all of the configuration for the jQuery UI slider into a view.
	revisions.view.Slider = wp.Backbone.View.extend({
		tagName: 'div',
		className: 'wp-slider',

		events: {
			'mousemove'  : 'mousemove',
			'mouseenter' : 'mouseenter',
			'mouseleave' : 'mouseleave'
		},

		initialize: function() {
			_.bindAll( this, 'start', 'slide', 'stop' );

			// Create the slider model from the provided collection data.
			var latestRevisionIndex = this.model.revisions.length - 1;

			// Find the initially selected revision
			var initiallySelectedRevisionIndex =
				this.model.revisions.indexOf(
					this.model.revisions.findWhere(  { id: Number( revisions.settings.selectedRevision ) } ) );

			this.settings = new revisions.model.Slider({
				max:   latestRevisionIndex,
				value: initiallySelectedRevisionIndex,
				start: this.start,
				slide: this.slide,
				stop:  this.stop
			});
		},

		ready: function() {
			this.$el.slider( this.settings.toJSON() );

			// Listen for changes in Compare Two Mode setting
			this.listenTo( this.model, 'change:compareTwoMode', this.updateSliderSettings );

			this.settings.on( 'change', function( model, options ) {
				this.updateSliderSettings();
			}, this );

			// Listen for changes in the diffId
			this.listenTo( this.model, 'change:diffId', this.diffIdChanged );

			// Reset to the initially selected revision
			this.slide( '', this.settings.attributes );

		},

		mousemove: function( e ) {
			var sliderLeft = Math.ceil( this.$el.offset().left ),
				sliderWidth = Math.ceil( this.$el.width() ) + 2,
				tickWidth = Math.ceil( ( sliderWidth ) / this.model.revisions.length ),
				actualX = e.clientX - sliderLeft,
				hoveringAt = Math.floor( actualX / tickWidth );

				// Reverse direction in Rtl mode.
				if ( isRtl )
					hoveringAt = this.model.revisions.length - hoveringAt - 1;

			// Ensure sane value for hoveringAt.
			if ( hoveringAt < 0 )
				hoveringAt = 0;
			else if ( hoveringAt >= this.model.revisions.length )
				hoveringAt = this.model.revisions.length - 1;

			// Update the model
			this.model.set( 'hoveringAt', hoveringAt );
			this.model.set( 'tooltipPosition', e.clientX );

		},

		mouseenter: function( e ) {
			this.model.set( 'sliderHovering', true );
		},

		mouseleave: function( e ) {
			this.model.set( 'sliderHovering', false );
		},

		updateSliderSettings: function() {
			if ( isRtl ) {
				this.$el.slider( { // Order reversed in RTL mode
					value: this.model.revisions.length - this.model.revisions.indexOf( this.model.get( 'to' ) ) - 1
				} );
			} else {
				if ( this.model.get( 'compareTwoMode' ) ) {
					this.$el.slider( { // Set handles to current from/to models
						values: [
							this.model.revisions.indexOf( this.model.get( 'from' ) ),
							this.model.revisions.indexOf( this.model.get( 'to' ) )
								],
						value: null,
						range: true // Range mode ensures handles can't cross
					} );
				} else {
					this.$el.slider( { // Set handle to current to model
						value: this.model.revisions.indexOf( this.model.get( 'to' ) ),
						values: null, // Clear existing two handled values
						range: false
					} );
				}
			}
			if ( this.model.get( 'compareTwoMode' ) ){
				$( '.revisions' ).addClass( 'comparing-two-revisions' );

				// in RTL mode the 'left handle' is the second in the slider, 'right' is first
				$( 'a.ui-slider-handle', this.$el )
					.first()
					.addClass( isRtl ? 'right-handle' : 'left-handle' )
					.removeClass( isRtl ? 'left-handle' : 'right-handle' );
				$( 'a.ui-slider-handle', this.$el )
					.last()
					.addClass( isRtl ? 'left-handle' : 'right-handle' )
					.removeClass( isRtl ? 'right-handle' : 'left-handle' );
			} else {
				$( '.revisions' ).removeClass( 'comparing-two-revisions' );
			}
		},

		diffIdChanged: function() {
			// Reset the view settings when diffId is changed
			if ( this.model.get( 'compareTwoMode' ) ) {
				this.settings.set( { 'values': [
						this.model.revisions.indexOf( this.model.get( 'from' ) ),
						this.model.revisions.indexOf( this.model.get( 'to' ) )
					] } );
			} else {
				this.settings.set( { 'value': this.model.revisions.indexOf( this.model.get( 'to' ) ) } );
			}
		},

		getSliderPosition: function( ui ){
			return isRtl ? this.model.revisions.length - ui.value - 1 : ui.value;
		},

		start: function( event, ui ) {
			if ( ! this.model.get( 'compareTwoMode' ) )
				return;

			// Track the mouse position to enable smooth dragging, overrides default jquery ui step behaviour .
			$( window ).mousemove( function( e ) {
				var sliderLeft = this.$el.offset().left,
					sliderRight = sliderLeft + this.$el.width();

				// Follow mouse movements, as long as handle remains inside slider.
				if ( e.clientX < sliderLeft ) {
					$( ui.handle ).css( 'left', 0 ); // Mouse to left of slider.
				} else if ( e.clientX > sliderRight ) {
					$( ui.handle ).css( 'left', sliderRight - sliderLeft); // Mouse to right of slider.
				} else {
					$( ui.handle ).css( 'left', e.clientX - sliderLeft ); // Mouse in slider.
				}
			} ); // End mousemove.
		},

		slide: function( event, ui ) {
			var attributes;
			// Compare two revisions mode
			if ( 'undefined' !== typeof ui.values && this.model.get( 'compareTwoMode' ) ) {
				// Prevent sliders from occupying same spot
				if ( ui.values[1] === ui.values[0] )
					return false;

				attributes = {
					to: this.model.revisions.at( isRtl ? this.model.revisions.length - ui.values[1] - 1 : ui.values[1] ), // Reverse directions for RTL.
					from: this.model.revisions.at( isRtl ? this.model.revisions.length - ui.values[0] - 1 : ui.values[0] ) // Reverse directions for RTL.
				};
			} else {
				// Compare single revision mode
				var sliderPosition = this.getSliderPosition( ui );
				attributes = {
					to: this.model.revisions.at( sliderPosition )
				};

				// If we're at the first revision, unset 'from'.
				if ( sliderPosition ) // Reverse directions for RTL.
					attributes.from = this.model.revisions.at( sliderPosition - 1  );
				else
					this.model.unset('from', { silent: true });
			}
			this.model.set( attributes );
		},

		stop: function( event, ui ) {
			if ( ! this.model.get( 'compareTwoMode' ) )
				return;

			// Stop tracking the mouse.
			$( window ).unbind( 'mousemove' );

			// Reset settings pops handle back to the step position.
			this.settings.trigger( 'change' );
		}
	});

	// The diff view.
	// This is the view for the current active diff.
	revisions.view.Diff = wp.Backbone.View.extend({
		tagName: 'div',
		className: 'revisions-diff',
		template: wp.template('revisions-diff'),

		// Generate the options to be passed to the template.
		prepare: function() {
			return _.extend({ fields: this.model.fields.toJSON() }, this.options );
		}
	});

	// The revisions router
	// takes URLs with #hash fragments and routes them
	revisions.router.Router = Backbone.Router.extend({
		initialize: function( options ) {
			this.model = options.model;

			// Maintain state history when dragging
			this.listenTo( this.model, 'renderDiff', this.updateURL );
		},

		routes: {
			'revision/from/:from/to/:to/handles/:handles': 'gotoRevisionId'
		},

		navigateRoute: function( to, from ) {
			var navigateTo = '/revision/from/' + from + '/to/' + to + '/handles/';
			if ( this.model.get( 'compareTwoMode' ) ){
				navigateTo = navigateTo + '2';
			} else {
				navigateTo = navigateTo + '1';
			}
			this.navigate( navigateTo );
		},

		updateURL: _.debounce( function() {
			var from = this.model.get('from');
			this.navigateRoute( this.model.get('to').id, from ? from.id : 0 );
		}, 250 ),

		gotoRevisionId: function( from, to, handles ) {
			if ( '2' === handles ) {
				this.model.set( { compareTwoMode: true } );
			} else {
				this.model.set( { compareTwoMode: false } );
			}

			if ( 'undefined' !== typeof this.model ) {
				var selectedToRevision =
					this.model.revisions.findWhere( { 'id': Number( to ) } ),
					selectedFromRevision =
					this.model.revisions.findWhere( { 'id': Number( from ) } );

				this.model.set( {
					to:   selectedToRevision,
					from: selectedFromRevision } );
			}
		}
	});

	// Initialize the revisions UI.
	revisions.init = function() {
		revisions.view.frame = new revisions.view.Frame({
			collection: new revisions.model.Revisions( revisions.settings.revisionData )
		}).render();
	};

	$( revisions.init );
}(jQuery));
