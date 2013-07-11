window.wp = window.wp || {};

(function($) {
	var revisions;

	revisions = wp.revisions = { model: {}, view: {}, controller: {} };

	// Link settings.
	revisions.settings = _.isUndefined( _wpRevisionsSettings ) ? {} : _wpRevisionsSettings;

	// For debugging
	revisions.debug = true;

	revisions.log = function() {
		if ( revisions.debug )
			console.log.apply( console, arguments );
	};

	// wp_localize_script transforms top-level numbers into strings. Undo that.
	if ( revisions.settings.selectedRevision )
		revisions.settings.selectedRevision = parseInt( revisions.settings.selectedRevision, 10 );


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
			step: 1,
			compareTwoMode: false
		},

		initialize: function( options ) {
			this.frame = options.frame;
			this.revisions = options.revisions;
			this.set({
				max:   this.revisions.length - 1,
				value: this.revisions.indexOf( this.revisions.get( revisions.settings.selectedRevision ) ),
				compareTwoMode: this.frame.get('compareTwoMode')
			});

			// Listen for changes to the revisions or mode from outside
			this.listenTo( this.frame, 'update:revisions', this.receiveRevisions );
			this.listenTo( this.frame, 'change:compareTwoMode', this.updateMode );

			// Listen for internal changes
			this.listenTo( this, 'change:from', this.handleLocalChanges );
			this.listenTo( this, 'change:to', this.handleLocalChanges );

			// Listen for changes to the hovered revision
			this.listenTo( this, 'change:hoveredRevision', this.hoverRevision );
		},

		// Called when a revision is hovered
		hoverRevision: function( model, value ) {
			this.trigger( 'hovered:revision', value );
		},

		// Called when `compareTwoMode` changes
		updateMode: function( model, value ) {
			this.set({ compareTwoMode: value });
		},

		// Called when `from` or `to` changes in the local model
		handleLocalChanges: function() {
			this.frame.set({
				from: this.get('from'),
				to: this.get('to')
			});
		},

		// Receives revisions changes from outside the model
		receiveRevisions: function( from, to ) {
			// Bail if nothing changed
			if ( this.get('from') === from && this.get('to') === to )
				return;

			this.set({ from: from, to: to }, { silent: true });
			this.trigger( 'update:revisions', from, to );
		}

	});

	revisions.model.Tooltip = Backbone.Model.extend({
		defaults: {
			revision: null,
			hovering: false, // Whether the mouse is hovering
			scrubbing: false // Whether the mouse is scrubbing
		},

		initialize: function( options ) {
			this.revisions = options.revisions;
			this.slider = options.slider;

			this.listenTo( this.slider, 'hovered:revision', this.updateRevision );
			this.listenTo( this.slider, 'change:hovering', this.setHovering );
			this.listenTo( this.slider, 'change:scrubbing', this.setScrubbing );
		},

		updateRevision: function( revision ) {
			this.set({ revision: revision });
		},

		setHovering: function( model, value ) {
			this.set({ hovering: value });
		},

		setScrubbing: function( model, value ) {
			this.set({ scrubbing: value });
		}
	});

	revisions.model.Revision = Backbone.Model.extend({});

	revisions.model.Revisions = Backbone.Collection.extend({
		model: revisions.model.Revision,

		initialize: function() {
			_.bindAll( this, 'next', 'prev' );
		},

		comparator: function( a, b ) {
			var a_ = a.get('dateUnix');
			var b_ = b.get('dateUnix');
			var cmp = (a_ > b_) - (a_ < b_);
			if (cmp === 0 && a.id != b.id)
				cmp = a.id < b.id ? -1 : 1;
			return cmp;
		},

		next: function( revision ) {
			var index = this.indexOf( revision );

			if ( index !== -1 && index !== this.length - 1 )
				return this.at( index + 1 );
		},

		prev: function( revision ) {
			var index = this.indexOf( revision );

			if ( index !== -1 && index !== 0 )
				return this.at( index - 1 );
		}
	});

	revisions.model.Field = Backbone.Model.extend({});

	revisions.model.Fields = Backbone.Collection.extend({
		model: revisions.model.Field
	});

	revisions.model.Diff = Backbone.Model.extend({
		initialize: function( attributes, options ) {
			var fields = this.get('fields');
			this.unset('fields');

			this.fields = new revisions.model.Fields( fields );
		}
	});

	revisions.model.Diffs = Backbone.Collection.extend({
		initialize: function( models, options ) {
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
			var self = this;
			_.each( comparisons, function( id, index ) {
				// Already exists in collection. Don't request it again.
				if ( self.get( id ) )
					delete comparisons[ index ];
			});
			wp.revisions.log( 'loadNew', comparisons );

			if ( comparisons.length )
				return this.load( comparisons );
			else
				return $.Deferred().resolve().promise();
		},

		load: function( comparisons ) {
			wp.revisions.log( 'load', comparisons );
			// Our collection should only ever grow, never shrink, so remove: false
			return this.fetch({ data: { compare: comparisons }, remove: false });
		},

		loadLast: function( num ) {
			var ids;

			num = num || 1;
			ids = _.last( this.getProximalDiffIds(), num );

			if ( ids.length )
				return this.loadNew( ids );
			else
				return $.Deferred().resolve().promise();
		},

		loadLastUnloaded: function( num ) {
			var ids;

			num = num || 1;
			ids = _.last( this.getUnloadedProximalDiffIds(), num );

			if ( ids.length )
				return this.loadNew( ids );
			else
				return $.Deferred().resolve().promise();
		},

		getProximalDiffIds: function() {
			var previous = 0, ids = [];
			this.revisions.each( _.bind( function( revision ) {
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
				options = options || {};
				options.context = this;
				options.data = _.extend( options.data || {}, {
					action: 'get-revision-diffs',
					post_id: revisions.settings.postId
				});

				var deferred = wp.ajax.send( options );
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
			var properties = {};

			this._debouncedEnsureDiff = _.debounce( this._ensureDiff, 200 );

			this.revisions = options.revisions;
			this.diffs = new revisions.model.Diffs( [], { revisions: this.revisions });

			// Set the initial revision provided through the settings.
			properties.to = this.revisions.get( revisions.settings.selectedRevision );
			properties.from = this.revisions.prev( properties.to );
			properties.compareTwoMode = false;
			this.set( properties );

			// Start the router. This will trigger a navigate event and ensure that
			// the `from` and `to` revisions accurately reflect the hash.
			this.router = new revisions.Router({ model: this });
			Backbone.history.start();

			this.listenTo( this, 'change:from', this.changeRevisionHandler );
			this.listenTo( this, 'change:to', this.changeRevisionHandler );
			this.listenTo( this, 'update:revisions', this.loadSurrounding );
			this.listenTo( this, 'change:compareTwoMode', this.changedMode );
		},

		changedMode: function() {
			// This isn't passed from/to so we grab them from the model
			this.loadSurrounding( this.get( 'from' ), this.get( 'to' ) );
		},

		loadSurrounding: function( from, to ) {
			// Different strategies for single and compare-two models
			if ( this.get( 'compareTwoMode' ) ) {
				// TODO: compare-two loading strategy
			} else {
				// TODO: clean this up to hook in to the ensure process
				if ( this.revisions.length ) {
					// Load the rest: first 10, then the rest by 50
					this.diffs.loadLastUnloaded( 10 ).always( _.bind( function() {
						this.diffs.loadAllBy( 50 );
					}, this ) );
				}
			}
		},

		// Fetch the currently loaded diff.
		diff: function() {
			return this.diffs.get( this._diffId );
		},

		// So long as `from` and `to` are changed at the same time, the diff
		// will only be updated once. This is because Backbone updates all of
		// the changed attributes in `set`, and then fires the `change` events.
		updateDiff: function( options ) {
			var from, to, diffId, diff;

			options = options || {};
			from = this.get('from');
			to = this.get('to');
			diffId = ( from ? from.id : 0 ) + ':' + to.id;

			// Check if we're actually changing the diff id.
			if ( this._diffId === diffId )
				return $.Deferred().fail().promise();

			this._diffId = diffId;
			this.trigger( 'update:revisions', from, to );

			// If we already have the diff, then immediately trigger the update.
			diff = this.diffs.get( diffId );
			if ( diff ) {
				this.trigger( 'update:diff', diff );
				return $.Deferred().resolve().promise();
			// Otherwise, fetch the diff.
			} else {
				if ( options.immediate ) {
					return this._ensureDiff();
				} else {
					this._debouncedEnsureDiff();
					return $.Deferred().fail().promise();
				}
			}
		},

		// A simple wrapper around `updateDiff` to prevent the change event's
		// parameters from being passed through.
		changeRevisionHandler: function( model, value, options ) {
			this.updateDiff();
		},

		_ensureDiff: function() {
			return this.diffs.ensure( this._diffId, this ).done( function( diff ) {
				// Make sure the current diff didn't change while the request was in flight.
				if ( this._diffId === diff.id )
					this.trigger( 'update:diff', diff );
			});
		}
	});


	/**
	 * ========================================================================
	 * VIEWS
	 * ========================================================================
	 */

	// The frame view. This contains the entire page.
	revisions.view.Frame = wp.Backbone.View.extend({
		className: 'revisions',
		template: wp.template('revisions-frame'),

		initialize: function() {
			// Generate the frame model.
			this.model = new revisions.model.FrameState({}, {
				revisions: this.collection
			});

			this.listenTo( this.model, 'update:diff', this.renderDiff );
			this.listenTo( this.model, 'change:compareTwoMode', this.updateCompareTwoMode );

			this.views.set( '.revisions-control-frame', new revisions.view.Controls({
				model: this.model
			}) );
		},

		render: function() {
			this.model.updateDiff({ immediate: true }).done( _.bind( function() {
				wp.Backbone.View.prototype.render.apply( this, arguments );

				$('#wpbody-content .wrap').append( this.el );
				this.updateCompareTwoMode();
				this.views.ready();
			}, this ) );

			return this;
		},

		renderDiff: function( diff ) {
			this.views.set( '.revisions-diff-frame', new revisions.view.Diff({
				model: diff
			}) );
		},

		updateCompareTwoMode: function() {
			this.$el.toggleClass( 'comparing-two-revisions', this.model.get('compareTwoMode') );
		}
	});

	// The control view.
	// This contains the revision slider, previous/next buttons, the meta info and the compare checkbox.
	revisions.view.Controls = wp.Backbone.View.extend({
		className: 'revisions-controls',

		initialize: function() {
			// Add the button view
			this.views.add( new revisions.view.Buttons({
				model: this.model
			}) );

			// Add the checkbox view
			this.views.add( new revisions.view.Checkbox({
				model: this.model
			}) );

			// Prep the slider model
			var slider = new revisions.model.Slider({
				frame: this.model,
				revisions: this.model.revisions
			});

			// Add the tooltip view
			this.views.add( new revisions.view.Tooltip({
				model: new revisions.model.Tooltip({
					revisions: this.model.revisions,
					slider: slider
				})
			}) );

			// Add the tickmarks view
			this.views.add( new revisions.view.Tickmarks({
				model: this.model
			}) );

			// Add the slider view
			this.views.add( new revisions.view.Slider({
				model: slider
			}) );

			// Add the Meta view
			this.views.add( new revisions.view.Meta({
				model: this.model
			}) );

		}
	});

	// The tickmarks view
	// This contains the slider tickmarks.
	revisions.view.Tickmarks = wp.Backbone.View.extend({
		className: 'revisions-tickmarks',

		render: function() {
			var tickCount, tickWidth;

			tickCount = this.model.revisions.length - 1;
			tickWidth = 1 / tickCount;

			this.$el.html('');
			_(tickCount).times( function(){ this.$el.append( '<div></div>' ); }, this );

			this.$('div').css( 'width', ( 100 * tickWidth ) + '%' );
		},

		ready: function() {
			this.render();
		}
	});

	// The meta view.
	// This contains the revision meta, and the restore button.
	revisions.view.Meta = wp.Backbone.View.extend({
		className: 'revisions-meta',
		template: wp.template('revisions-meta'),

		events: {
			'click .restore-revision': 'restoreRevision'
		},

		initialize: function() {
			this.listenTo( this.model, 'update:revisions', this.updateMeta );
		},

		restoreRevision: function() {
			var restoreUrl    = this.model.get('to').attributes.restoreUrl.replace(/&amp;/g, '&');
			document.location = restoreUrl;
		},

		updateMeta: function( from, to ) {
			this.$el.html( this.template( this.model.toJSON() ) );
			this.$('.restore-revision').prop( 'disabled', to.attributes.current );
		}
	});

	// The checkbox view.
	// Encapsulates all of the configuration for the compare checkbox.
	revisions.view.Checkbox = wp.Backbone.View.extend({
		className: 'revisions-checkbox',
		template: wp.template('revisions-checkbox'),

		events: {
			'click .compare-two-revisions': 'compareTwoToggle'
		},

		initialize: function() {
			this.$el.html( this.template() );
		},

		updateCompareTwoMode: function() {
			this.$('.compare-two-revisions').prop( 'checked', this.model.get('compareTwoMode') );
		},

		// Toggle the compare two mode feature when the compare two checkbox is checked.
		compareTwoToggle: function( event ) {
			// Activate compare two mode?
			this.model.set({ compareTwoMode: $('.compare-two-revisions').prop('checked') });

			// Update route
			this.model.router.updateUrl();
		},

		ready: function() {
			// Hide compare two mode toggle when fewer than three revisions.
			if ( this.model.revisions.length < 3 )
				$('.revision-toggle-compare-mode').hide();

			this.listenTo( this.model, 'change:compareTwoMode', this.updateCompareTwoMode );

			// Update the mode in case route has set it
			this.updateCompareTwoMode();
		}

	});

	// The tooltip view.
	// Encapsulates the tooltip.
	revisions.view.Tooltip = wp.Backbone.View.extend({
		className: 'revisions-tooltip',
		template: wp.template('revisions-tooltip'),

		initialize: function( options ) {
			this.listenTo( this.model, 'change:revision', this.render );
			this.listenTo( this.model, 'change:hovering', this.toggleVisibility );
			this.listenTo( this.model, 'change:scrubbing', this.toggleVisibility );
		},

		ready: function() {
			this.toggleVisibility({ immediate: true });
		},

		visible: function() {
			return this.model.get( 'scrubbing' ) || this.model.get( 'hovering' );
		},

		toggleVisibility: function( options ) {
			options = options || {};
			var visible = this.visible()
			if ( visible ) { // Immediate show
				// this.$el.removeClass('fade');
				this.$el.css( 'opacity', 1 );
			} else if ( options.immediate ) { // Immediate fade out
				this.$el.addClass('fade');
				this.$el.css( 'opacity', 0 );
			} else { // Wait a bit, make sure we're really done, then fade it out
				_.delay( function( view ) {
					if ( ! view.visible() )
						view.toggleVisibility({ immediate: true });
				}, 500, this );
			}
		},

		render: function() {
			var offset;
			// Check if a revision exists.
			if ( _.isNull( this.model.get('revision') ) )
				return;

			// Insert revision data.
			this.$el.html( this.template( this.model.get('revision').toJSON() ) );

			// Set the position.
			offset = this.model.revisions.indexOf( this.model.get('revision') ) / ( this.model.revisions.length - 1 );
			// 15% to get us to the start of the slider
			// 0.7 to convert the slider-relative percentage to a page-relative percentage
			// 100 to convert to a percentage
			offset = 15 + (0.7 * offset * 100 ); // Now in a percentage
			this.$el.css( 'left', offset + '%' );
		}
	});

	// The buttons view.
	// Encapsulates all of the configuration for the previous/next buttons.
	revisions.view.Buttons = wp.Backbone.View.extend({
		className: 'revisions-buttons',
		template: wp.template('revisions-buttons'),

		events: {
			'click #next': 'nextRevision',
			'click #previous': 'previousRevision'
		},

		initialize: function() {
			this.$el.html( this.template() );
		},

		ready: function() {
			this.listenTo( this.model, 'update:revisions', this.disabledButtonCheck );
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
			this.model.router.updateUrl();
		},

		// Go to the 'next' revision, direction takes into account RTL mode.
		nextRevision: function() {
			var toIndex = isRtl ? this.model.revisions.length - this.model.revisions.indexOf( this.model.get('to') ) - 1 : this.model.revisions.indexOf( this.model.get('to') );
			toIndex     = isRtl ? toIndex - 1 : toIndex + 1;
			this.gotoModel( toIndex );
		},

		// Go to the 'previous' revision, direction takes into account RTL mode.
		previousRevision: function() {
			var toIndex = isRtl ? this.model.revisions.length - this.model.revisions.indexOf( this.model.get('to') ) - 1 : this.model.revisions.indexOf( this.model.get('to') );
			toIndex     = isRtl ? toIndex + 1 : toIndex - 1;
			this.gotoModel( toIndex );
		},

		// Check to see if the Previous or Next buttons need to be disabled or enabled.
		disabledButtonCheck: function() {
			var maxVal = this.model.revisions.length - 1,
				minVal = 0,
				next = $('.revisions-next .button'),
				previous = $('.revisions-previous .button'),
				val = this.model.revisions.indexOf( this.model.get('to') );

			// Disable "Next" button if you're on the last node.
			next.prop( 'disabled', ( maxVal === val ) );

			// Disable "Previous" button if you're on the first node.
			previous.prop( 'disabled', ( minVal === val ) );
		}
	});


	// The slider view.
	revisions.view.Slider = wp.Backbone.View.extend({
		className: 'wp-slider',

		events: {
			'mousemove'  : 'mouseMove',
			'mouseleave' : 'mouseLeave',
			'mouseenter' : 'mouseEnter'
		},

		initialize: function() {
			_.bindAll( this, 'start', 'slide', 'stop', 'mouseMove' );
			this.listenTo( this.model, 'change:compareTwoMode', this.updateSliderSettings );
			this.listenTo( this.model, 'update:revisions', this.updateSliderSettings );
		},

		ready: function() {
			this.$el.slider( _.extend( this.model.toJSON(), {
				start: this.start,
				slide: this.slide,
				stop:  this.stop
			}) );

			this.listenTo( this, 'slide:stop', this.updateSliderSettings );
		},

		mouseMove: function( e ) {
			var zoneCount = this.model.revisions.length - 1, // One fewer zone than models
				sliderLeft = this.$el.offset().left, // Left edge of slider
				sliderWidth = this.$el.width(), // Width of slider
				tickWidth = sliderWidth / zoneCount, // Calculated width of zone
				actualX = e.clientX - sliderLeft, // Offset of mouse position in slider
				currentModelIndex = Math.floor( ( actualX + ( tickWidth / 2 )  ) / tickWidth ); // Calculate the model index

			// Reverse direction in RTL mode.
			if ( isRtl )
				currentModelIndex = this.model.revisions.length - currentModelIndex - 1;

			// Ensure sane value for currentModelIndex.
			if ( currentModelIndex < 0 )
				currentModelIndex = 0;
			else if ( currentModelIndex >= this.model.revisions.length )
				currentModelIndex = this.model.revisions.length - 1;

			// Update the tooltip model
			this.model.set({
				'hoveredRevision': this.model.revisions.at( currentModelIndex )
			});
		},

		mouseLeave: function() {
			this.model.set({ hovering: false });
		},

		mouseEnter: function() {
			this.model.set({ hovering: true });
		},

		updateSliderSettings: function() {
			var handles, leftValue, rightValue;

			if ( this.model.get('compareTwoMode') ) {
				leftValue = isRtl ?	this.model.revisions.length - this.model.revisions.indexOf( this.model.get('to') ) - 1 :
											this.model.revisions.indexOf( this.model.get('from') ),
				rightValue = isRtl ?	this.model.revisions.length - this.model.revisions.indexOf( this.model.get('from') ) - 1 :
											this.model.revisions.indexOf( this.model.get('to') );

				// Set handles to current from / to models.
				// Reverse order for RTL
				this.$el.slider( {
					values: [
						leftValue,
						rightValue
					],
					value: null,
					range: true // Range mode ensures handles can't cross
				} );

				handles = this.$('a.ui-slider-handle');
				// in RTL mode the 'left handle' is the second in the slider, 'right' is first
				handles.first()
					.toggleClass( 'right-handle', !! isRtl )
					.toggleClass( 'left-handle', ! isRtl );
				handles.last()
					.toggleClass( 'left-handle', !! isRtl )
					.toggleClass( 'right-handle', ! isRtl );

			} else {
				this.$el.slider( { // Set handle to current to model
					// Reverse order for RTL.
					value: isRtl ? this.model.revisions.length - this.model.revisions.indexOf( this.model.get('to') ) - 1 :
									this.model.revisions.indexOf( this.model.get('to') ),
					values: null, // Clear existing two handled values
					range: false
				} );
				this.$('a.ui-slider-handle').removeClass('left-handle right-handle');
			}
		},

		getSliderPosition: function( ui ){
			return isRtl ? this.model.revisions.length - ui.value - 1 : ui.value;
		},

		start: function( event, ui ) {
			this.model.set({ scrubbing: true });

			// Track the mouse position to enable smooth dragging,
			// overrides default jQuery UI step behavior.
			$( window ).on( 'mousemove.wp.revisions', { view: this }, function( e ) {
				var view            = e.data.view,
					leftDragBoundary  = view.$el.offset().left, // Initial left boundary
					sliderOffset      = leftDragBoundary,
					sliderRightEdge   = leftDragBoundary + view.$el.width(),
					rightDragBoundary = sliderRightEdge, // Initial right boundary
					leftDragReset     = 0, // Initial left drag reset
					rightDragReset    = sliderRightEdge - sliderOffset; // Initial right drag reset

				// In two handle mode, ensure handles can't be dragged past each other.
				// Adjust left/right boundaries and reset points.
				if ( view.model.frame.get('compareTwoMode') ) {
					var rightHandle = $( ui.handle ).parent().find('.right-handle'),
						leftHandle  = $( ui.handle ).parent().find('.left-handle');

					if ( $( ui.handle ).hasClass('left-handle') ) {
						// Dragging the left handle, boundary is right handle.
						// RTL mode calculations reverse directions.
						if ( isRtl ) {
							leftDragBoundary = rightHandle.offset().left + rightHandle.width();
							leftDragReset    = leftDragBoundary - sliderOffset;
						} else {
							rightDragBoundary = rightHandle.offset().left;
							rightDragReset    = rightDragBoundary - sliderOffset;
						}
					} else {
						// Dragging the right handle, boundary is the left handle.
						// RTL mode calculations reverse directions.
						if ( isRtl ) {
							rightDragBoundary = leftHandle.offset().left;
							rightDragReset    = rightDragBoundary - sliderOffset;
						} else {
							leftDragBoundary = leftHandle.offset().left + leftHandle.width() ;
							leftDragReset    = leftDragBoundary - sliderOffset;
						}
					}
				}

				// Follow mouse movements, as long as handle remains inside slider.
				if ( e.clientX < leftDragBoundary ) {
					$( ui.handle ).css( 'left', leftDragReset ); // Mouse to left of slider.
				} else if ( e.clientX > rightDragBoundary ) {
					$( ui.handle ).css( 'left', rightDragReset ); // Mouse to right of slider.
				} else {
					$( ui.handle ).css( 'left', e.clientX - sliderOffset ); // Mouse in slider.
				}
			} );
		},

		// Responds to slide events
		slide: function( event, ui ) {
			var attributes;
			// Compare two revisions mode
			if ( ! _.isUndefined( ui.values ) && this.model.frame.get('compareTwoMode') ) {
				// Prevent sliders from occupying same spot
				if ( ui.values[1] === ui.values[0] )
					return false;

				attributes = {
					to: this.model.revisions.at( isRtl ? this.model.revisions.length - ui.values[0] - 1 : ui.values[1] ), // Reverse directions for RTL.
					from: this.model.revisions.at( isRtl ? this.model.revisions.length - ui.values[1] - 1 : ui.values[0] ) // Reverse directions for RTL.
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
					attributes.from = undefined;
			}
			this.model.set( attributes );

			// If we are scrubbing, a scrub to a revision is considered a hover
			if ( this.model.get( 'scrubbing' ) ) {
				this.model.set({
					'hoveredRevision': attributes.to
				});
			}
		},

		stop: function( event, ui ) {
			$( window ).off('mousemove.wp.revisions');
			this.updateSliderSettings();
			this.model.set({ scrubbing: false });
		}
	});

	// The diff view.
	// This is the view for the current active diff.
	revisions.view.Diff = wp.Backbone.View.extend({
		className: 'revisions-diff',
		template: wp.template('revisions-diff'),

		// Generate the options to be passed to the template.
		prepare: function() {
			return _.extend({ fields: this.model.fields.toJSON() }, this.options );
		}
	});

	// The revisions router
	// takes URLs with #hash fragments and routes them
	revisions.Router = Backbone.Router.extend({
		initialize: function( options ) {
			this.model = options.model;

			// Maintain state history when dragging
			this.listenTo( this.model, 'update:diff', _.debounce( this.updateUrl, 250 ) );
		},

		routes: {
			'from/:from/to/:to': 'handleRoute',
			'at/:to': 'handleRoute'
		},

		updateUrl: function() {
			var from = this.model.has('from') ? this.model.get('from').id : 0;
			var to = this.model.get('to').id;
			if ( this.model.get('compareTwoMode' ) )
				this.navigate( 'from/' + from + '/to/' + to );
			else
				this.navigate( 'at/' + to );
		},

		handleRoute: function( a, b ) {
			var from, to, compareTwo;

			// If `b` is undefined, this is an 'at/:to' route, for a single revision
			if ( _.isUndefined( b ) ) {
				b = this.model.revisions.get( a );
				a = this.model.revisions.prev( b );
				b = b ? b.id : 0;
				a = a ? a.id : 0
				compareTwo = false;
			} else {
				compareTwo = true;
			}

			from = parseInt( a, 10 );
			to = parseInt( b, 10 );

			this.model.set({ compareTwoMode: compareTwo });

			if ( ! _.isUndefined( this.model ) ) {
				var selectedToRevision = this.model.revisions.get( to ),
					selectedFromRevision = this.model.revisions.get( from );

				this.model.set({
					to: selectedToRevision,
					from: selectedFromRevision
				});
			}
			revisions.settings.selectedRevision = to;
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
