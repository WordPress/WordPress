window.wp = window.wp || {};

(function($) {
	var revisions;

	revisions = wp.revisions = { model: {}, view: {}, controller: {} };

	// Link settings.
	revisions.settings = _.isUndefined( _wpRevisionsSettings ) ? {} : _wpRevisionsSettings;

	// For debugging
	revisions.debug = false;

	revisions.log = function() {
		if ( window.console && revisions.debug )
			console.log.apply( console, arguments );
	};

	// Handy functions to help with positioning
	$.fn.allOffsets = function() {
		var offset = this.offset() || {top: 0, left: 0}, win = $(window);
		return _.extend( offset, {
			right:  win.width()  - offset.left - this.outerWidth(),
			bottom: win.height() - offset.top  - this.outerHeight()
		});
	};

	$.fn.allPositions = function() {
		var position = this.position() || {top: 0, left: 0}, parent = this.parent();
		return _.extend( position, {
			right:  parent.outerWidth()  - position.left - this.outerWidth(),
			bottom: parent.outerHeight() - position.top  - this.outerHeight()
		});
	};

	// wp_localize_script transforms top-level numbers into strings. Undo that.
	if ( revisions.settings.to )
		revisions.settings.to = parseInt( revisions.settings.to, 10 );
	if ( revisions.settings.from )
		revisions.settings.from = parseInt( revisions.settings.from, 10 );

	// wp_localize_script does not allow for top-level booleans. Fix that.
	if ( revisions.settings.compareTwoMode )
		revisions.settings.compareTwoMode = revisions.settings.compareTwoMode === '1';

	/**
	 * ========================================================================
	 * MODELS
	 * ========================================================================
	 */
	revisions.model.Slider = Backbone.Model.extend({
		defaults: {
			value: null,
			values: null,
			min: 0,
			max: 1,
			step: 1,
			range: false,
			compareTwoMode: false
		},

		initialize: function( options ) {
			this.frame = options.frame;
			this.revisions = options.revisions;

			// Listen for changes to the revisions or mode from outside
			this.listenTo( this.frame, 'update:revisions', this.receiveRevisions );
			this.listenTo( this.frame, 'change:compareTwoMode', this.updateMode );

			// Listen for internal changes
			this.listenTo( this, 'change:from', this.handleLocalChanges );
			this.listenTo( this, 'change:to', this.handleLocalChanges );
			this.listenTo( this, 'change:compareTwoMode', this.updateSliderSettings );
			this.listenTo( this, 'update:revisions', this.updateSliderSettings );

			// Listen for changes to the hovered revision
			this.listenTo( this, 'change:hoveredRevision', this.hoverRevision );

			this.set({
				max:   this.revisions.length - 1,
				compareTwoMode: this.frame.get('compareTwoMode'),
				from: this.frame.get('from'),
				to: this.frame.get('to')
			});
			this.updateSliderSettings();
		},

		getSliderValue: function( a, b ) {
			return isRtl ? this.revisions.length - this.revisions.indexOf( this.get(a) ) - 1 : this.revisions.indexOf( this.get(b) );
		},

		updateSliderSettings: function() {
			if ( this.get('compareTwoMode') ) {
				this.set({
					values: [
						this.getSliderValue( 'to', 'from' ),
						this.getSliderValue( 'from', 'to' )
					],
					value: null,
					range: true // ensures handles cannot cross
				});
			} else {
				this.set({
					value: this.getSliderValue( 'to', 'to' ),
					values: null,
					range: false
				});
			}
			this.trigger( 'update:slider' );
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
			offset: {},
			hovering: false, // Whether the mouse is hovering
			scrubbing: false // Whether the mouse is scrubbing
		},

		initialize: function( options ) {
			this.frame = options.frame;
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
			_.bindAll( this, 'getClosestUnloaded' );
			this.loadAll = _.once( this._loadAll );
			this.revisions = options.revisions;
			this.requests  = {};
		},

		model: revisions.model.Diff,

		ensure: function( id, context ) {
			var diff     = this.get( id );
			var request  = this.requests[ id ];
			var deferred = $.Deferred();
			var ids      = {};
			var from     = id.split(':')[0];
			var to       = id.split(':')[1];
			ids[id] = true;

			wp.revisions.log( 'ensure', id );

			this.trigger( 'ensure', ids, from, to, deferred.promise() );

			if ( diff ) {
				deferred.resolveWith( context, [ diff ] );
			} else {
				this.trigger( 'ensure:load', ids, from, to, deferred.promise() );
				_.each( ids, _.bind( function( id ) {
					// Remove anything that has an ongoing request
					if ( this.requests[ id ] )
						delete ids[ id ];
					// Remove anything we already have
					if ( this.get( id ) )
						delete ids[ id ];
				}, this ) );
				if ( ! request ) {
					// Always include the ID that started this ensure
					ids[ id ] = true;
					request   = this.load( _.keys( ids ) );
				}

				request.done( _.bind( function() {
					deferred.resolveWith( context, [ this.get( id ) ] );
				}, this ) ).fail( _.bind( function() {
					deferred.reject();
				}) );
			}

			return deferred.promise();
		},

		// Returns an array of proximal diffs
		getClosestUnloaded: function( ids, centerId ) {
			var self = this;
			return _.chain([0].concat( ids )).initial().zip( ids ).sortBy( function( pair ) {
				return Math.abs( centerId - pair[1] );
			}).map( function( pair ) {
				return pair.join(':');
			}).filter( function( diffId ) {
				return _.isUndefined( self.get( diffId ) ) && ! self.requests[ diffId ];
			}).value();
		},

		_loadAll: function( allRevisionIds, centerId, num ) {
			var self = this, deferred = $.Deferred();
			diffs = _.first( this.getClosestUnloaded( allRevisionIds, centerId ), num );
			if ( _.size( diffs ) > 0 ) {
				this.load( diffs ).done( function() {
					self._loadAll( allRevisionIds, centerId, num ).done( function() {
						deferred.resolve();
					});
				}).fail( function() {
					if ( 1 === num ) { // Already tried 1. This just isn't working. Give up.
						deferred.reject();
					} else { // Request fewer diffs this time
						self._loadAll( allRevisionIds, centerId, Math.ceil( num / 2 ) ).done( function() {
							deferred.resolve();
						});
					}
				});
			} else {
				deferred.resolve();
			}
			return deferred;
		},

		load: function( comparisons ) {
			wp.revisions.log( 'load', comparisons );
			// Our collection should only ever grow, never shrink, so remove: false
			return this.fetch({ data: { compare: comparisons }, remove: false }).done( function(){
				wp.revisions.log( 'load:complete', comparisons );
			});
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
		defaults: {
			loading: false,
			error: false,
			compareTwoMode: false
		},

		initialize: function( attributes, options ) {
			var properties = {};

			_.bindAll( this, 'receiveDiff' );
			this._debouncedEnsureDiff = _.debounce( this._ensureDiff, 200 );

			this.revisions = options.revisions;
			this.diffs = new revisions.model.Diffs( [], { revisions: this.revisions });

			// Set the initial diffs collection provided through the settings
			this.diffs.set( revisions.settings.diffData );

			// Set up internal listeners
			this.listenTo( this, 'change:from', this.changeRevisionHandler );
			this.listenTo( this, 'change:to', this.changeRevisionHandler );
			this.listenTo( this, 'change:compareTwoMode', this.changeMode );
			this.listenTo( this, 'update:revisions', this.updatedRevisions );
			this.listenTo( this.diffs, 'ensure:load', this.updateLoadingStatus );
			this.listenTo( this, 'update:diff', this.updateLoadingStatus );

			// Set the initial revisions, baseUrl, and mode as provided through settings
			properties.to = this.revisions.get( revisions.settings.to );
			properties.from = this.revisions.get( revisions.settings.from );
			properties.compareTwoMode = revisions.settings.compareTwoMode;
			properties.baseUrl = revisions.settings.baseUrl;
			this.set( properties );

			// Start the router if browser supports History API
			if ( window.history && window.history.pushState ) {
				this.router = new revisions.Router({ model: this });
				Backbone.history.start({ pushState: true });
			}
		},

		updateLoadingStatus: function() {
			this.set( 'error', false );
			this.set( 'loading', ! this.diff() );
		},

		changeMode: function( model, value ) {
			// If we were on the first revision before switching, we have to bump them over one
			if ( value && 0 === this.revisions.indexOf( this.get('to') ) ) {
				this.set({
					from: this.revisions.at(0),
					to: this.revisions.at(1)
				});
			}
		},

		updatedRevisions: function( from, to ) {
			if ( this.get( 'compareTwoMode' ) ) {
				// TODO: compare-two loading strategy
			} else {
				this.diffs.loadAll( this.revisions.pluck('id'), to.id, 40 );
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
				return $.Deferred().reject().promise();

			this._diffId = diffId;
			this.trigger( 'update:revisions', from, to );

			diff = this.diffs.get( diffId );

			// If we already have the diff, then immediately trigger the update.
			if ( diff ) {
				this.receiveDiff( diff );
				return $.Deferred().resolve().promise();
			// Otherwise, fetch the diff.
			} else {
				if ( options.immediate ) {
					return this._ensureDiff();
				} else {
					this._debouncedEnsureDiff();
					return $.Deferred().reject().promise();
				}
			}
		},

		// A simple wrapper around `updateDiff` to prevent the change event's
		// parameters from being passed through.
		changeRevisionHandler: function( model, value, options ) {
			this.updateDiff();
		},

		receiveDiff: function( diff ) {
			// Did we actually get a diff?
			if ( _.isUndefined( diff ) || _.isUndefined( diff.id ) ) {
				this.set({
					loading: false,
					error: true
				});
			} else if ( this._diffId === diff.id ) { // Make sure the current diff didn't change
				this.trigger( 'update:diff', diff );
			}
		},

		_ensureDiff: function() {
			return this.diffs.ensure( this._diffId, this ).always( this.receiveDiff );
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
			this.listenTo( this.model, 'update:diff', this.renderDiff );
			this.listenTo( this.model, 'change:compareTwoMode', this.updateCompareTwoMode );
			this.listenTo( this.model, 'change:loading', this.updateLoadingStatus );
			this.listenTo( this.model, 'change:error', this.updateErrorStatus );

			this.views.set( '.revisions-control-frame', new revisions.view.Controls({
				model: this.model
			}) );
		},

		render: function() {
			wp.Backbone.View.prototype.render.apply( this, arguments );

			$('html').css( 'overflow-y', 'scroll' );
			$('#wpbody-content .wrap').append( this.el );
			this.updateCompareTwoMode();
			this.renderDiff( this.model.diff() );
			this.views.ready();

			return this;
		},

		renderDiff: function( diff ) {
			this.views.set( '.revisions-diff-frame', new revisions.view.Diff({
				model: diff
			}) );
		},

		updateLoadingStatus: function() {
			this.$el.toggleClass( 'loading', this.model.get('loading') );
		},

		updateErrorStatus: function() {
			this.$el.toggleClass( 'diff-error', this.model.get('error') );
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
			_.bindAll( this, 'setWidth' );

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

			// Prep the tooltip model
			var tooltip = new revisions.model.Tooltip({
				frame: this.model,
				revisions: this.model.revisions,
				slider: slider
			});

			// Add the tooltip view
			this.views.add( new revisions.view.Tooltip({
				model: tooltip
			}) );

			// Add the tickmarks view
			this.views.add( new revisions.view.Tickmarks({
				model: tooltip
			}) );

			// Add the slider view
			this.views.add( new revisions.view.Slider({
				model: slider
			}) );

			// Add the Metabox view
			this.views.add( new revisions.view.Metabox({
				model: this.model
			}) );
		},

		ready: function() {
			this.top = this.$el.offset().top;
			this.window = $(window);
			this.window.on( 'scroll.wp.revisions', {controls: this}, function(e) {
				var controls = e.data.controls;
				var container = controls.$el.parent();
				var scrolled = controls.window.scrollTop();
				var frame = controls.views.parent;

				if ( scrolled >= controls.top ) {
					if ( ! frame.$el.hasClass('pinned') ) {
						controls.setWidth();
						container.css('height', container.height() + 'px' );
						controls.window.on('resize.wp.revisions.pinning click.wp.revisions.pinning', {controls: controls}, function(e) {
							e.data.controls.setWidth();
						});
					}
					frame.$el.addClass('pinned');
				} else if ( frame.$el.hasClass('pinned') ) {
					controls.window.off('.wp.revisions.pinning');
					controls.$el.css('width', 'auto');
					frame.$el.removeClass('pinned');
					container.css('height', 'auto');
					controls.top = controls.$el.offset().top;
				} else {
					controls.top = controls.$el.offset().top;
				}
			});
		},

		setWidth: function() {
			this.$el.css('width', this.$el.parent().width() + 'px');
		}
	});

	// The tickmarks view
	revisions.view.Tickmarks = wp.Backbone.View.extend({
		className: 'revisions-tickmarks',
		direction: isRtl ? 'right' : 'left',

		initialize: function() {
			this.listenTo( this.model, 'change:revision', this.reportTickPosition );
		},

		reportTickPosition: function( model, revision ) {
			var offset, thisOffset, parentOffset, tick, index = this.model.revisions.indexOf( revision );
			thisOffset = this.$el.allOffsets();
			parentOffset = this.$el.parent().allOffsets();
			if ( index === this.model.revisions.length - 1 ) {
				// Last one
				offset = {
					rightPlusWidth: thisOffset.left - parentOffset.left + 1,
					leftPlusWidth: thisOffset.right - parentOffset.right + 1
				};
			} else {
				// Normal tick
				tick = this.$('div:nth-of-type(' + (index + 1) + ')');
				offset = tick.allPositions();
				_.extend( offset, {
					left: offset.left + thisOffset.left - parentOffset.left,
					right: offset.right + thisOffset.right - parentOffset.right
				});
				_.extend( offset, {
					leftPlusWidth: offset.left + tick.outerWidth(),
					rightPlusWidth: offset.right + tick.outerWidth()
				});
			}
			this.model.set({ offset: offset });
		},

		ready: function() {
			var tickCount, tickWidth;
			tickCount = this.model.revisions.length - 1;
			tickWidth = 1 / tickCount;
			this.$el.css('width', ( this.model.revisions.length * 50 ) + 'px');

			_(tickCount).times( function( index ){
				this.$el.append( '<div style="' + this.direction + ': ' + ( 100 * tickWidth * index ) + '%"></div>' );
			}, this );
		}
	});

	// The metabox view
	revisions.view.Metabox = wp.Backbone.View.extend({
		className: 'revisions-meta',

		initialize: function() {
			// Add the 'from' view
			this.views.add( new revisions.view.MetaFrom({
				model: this.model,
				className: 'diff-meta diff-meta-from'
			}) );

			// Add the 'to' view
			this.views.add( new revisions.view.MetaTo({
				model: this.model
			}) );
		}
	});

	// The revision meta view (to be extended)
	revisions.view.Meta = wp.Backbone.View.extend({
		template: wp.template('revisions-meta'),

		events: {
			'click .restore-revision': 'restoreRevision'
		},

		initialize: function() {
			this.listenTo( this.model, 'update:revisions', this.render );
		},

		prepare: function() {
			return _.extend( this.model.toJSON()[this.type] || {}, {
				type: this.type
			});
		},

		restoreRevision: function() {
			document.location = this.model.get('to').attributes.restoreUrl;
		}
	});

	// The revision meta 'from' view
	revisions.view.MetaFrom = revisions.view.Meta.extend({
		className: 'diff-meta diff-meta-from',
		type: 'from'
	});

	// The revision meta 'to' view
	revisions.view.MetaTo = revisions.view.Meta.extend({
		className: 'diff-meta diff-meta-to',
		type: 'to'
	});

	// The checkbox view.
	revisions.view.Checkbox = wp.Backbone.View.extend({
		className: 'revisions-checkbox',
		template: wp.template('revisions-checkbox'),

		events: {
			'click .compare-two-revisions': 'compareTwoToggle'
		},

		initialize: function() {
			this.listenTo( this.model, 'change:compareTwoMode', this.updateCompareTwoMode );
		},

		ready: function() {
			if ( this.model.revisions.length < 3 )
				$('.revision-toggle-compare-mode').hide();
		},

		updateCompareTwoMode: function() {
			this.$('.compare-two-revisions').prop( 'checked', this.model.get('compareTwoMode') );
		},

		// Toggle the compare two mode feature when the compare two checkbox is checked.
		compareTwoToggle: function( event ) {
			// Activate compare two mode?
			this.model.set({ compareTwoMode: $('.compare-two-revisions').prop('checked') });
		}
	});

	// The tooltip view.
	// Encapsulates the tooltip.
	revisions.view.Tooltip = wp.Backbone.View.extend({
		className: 'revisions-tooltip',
		template: wp.template('revisions-meta'),

		initialize: function( options ) {
			this.listenTo( this.model, 'change:offset', this.render );
			this.listenTo( this.model, 'change:hovering', this.toggleVisibility );
			this.listenTo( this.model, 'change:scrubbing', this.toggleVisibility );
		},

		prepare: function() {
			if ( _.isNull( this.model.get('revision') ) )
				return;
			else
				return _.extend( { type: 'tooltip' }, {
					attributes: this.model.get('revision').toJSON()
				});
		},

		render: function() {
			var direction, directionVal, flipped, css = {}, position = this.model.revisions.indexOf( this.model.get('revision') ) + 1;
			flipped = ( position / this.model.revisions.length ) > 0.5;
			if ( isRtl ) {
				direction = flipped ? 'left' : 'right';
				directionVal = flipped ? 'leftPlusWidth' : direction;
			} else {
				direction = flipped ? 'right' : 'left';
				directionVal = flipped ? 'rightPlusWidth' : direction;
			}
			otherDirection = 'right' === direction ? 'left': 'right';
			wp.Backbone.View.prototype.render.apply( this, arguments );
			css[direction] = this.model.get('offset')[directionVal] + 'px';
			css[otherDirection] = '';
			this.$el.toggleClass( 'flipped', flipped ).css( css );
		},

		visible: function() {
			return this.model.get( 'scrubbing' ) || this.model.get( 'hovering' );
		},

		toggleVisibility: function( options ) {
			if ( this.visible() )
				this.$el.stop().show().fadeTo( 100 - this.el.style.opacity * 100, 1 );
			else
				this.$el.stop().fadeTo( this.el.style.opacity * 300, 0, function(){ $(this).hide(); } );
			return;
		}
	});

	// The buttons view.
	// Encapsulates all of the configuration for the previous/next buttons.
	revisions.view.Buttons = wp.Backbone.View.extend({
		className: 'revisions-buttons',
		template: wp.template('revisions-buttons'),

		events: {
			'click .revisions-next .button': 'nextRevision',
			'click .revisions-previous .button': 'previousRevision'
		},

		initialize: function() {
			this.listenTo( this.model, 'update:revisions', this.disabledButtonCheck );
		},

		ready: function() {
			this.disabledButtonCheck();
		},

		// Go to a specific model index
		gotoModel: function( toIndex ) {
			var attributes = {
				to: this.model.revisions.at( toIndex )
			};
			// If we're at the first revision, unset 'from'.
			if ( toIndex )
				attributes.from = this.model.revisions.at( toIndex - 1 );
			else
				this.model.unset('from', { silent: true });

			this.model.set( attributes );
		},

		// Go to the 'next' revision
		nextRevision: function() {
			var toIndex = this.model.revisions.indexOf( this.model.get('to') ) + 1;
			this.gotoModel( toIndex );
		},

		// Go to the 'previous' revision
		previousRevision: function() {
			var toIndex = this.model.revisions.indexOf( this.model.get('to') ) - 1;
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
		direction: isRtl ? 'right' : 'left',

		events: {
			'mousemove' : 'mouseMove'
		},

		initialize: function() {
			_.bindAll( this, 'start', 'slide', 'stop', 'mouseMove', 'mouseEnter', 'mouseLeave' );
			this.listenTo( this.model, 'update:slider', this.applySliderSettings );
		},

		ready: function() {
			this.$el.css('width', ( this.model.revisions.length * 50 ) + 'px');
			this.$el.slider( _.extend( this.model.toJSON(), {
				start: this.start,
				slide: this.slide,
				stop:  this.stop
			}) );

			this.$el.hoverIntent({
				over: this.mouseEnter,
				out: this.mouseLeave,
				timeout: 800
			});

			this.applySliderSettings();
		},

		mouseMove: function( e ) {
			var zoneCount = this.model.revisions.length - 1, // One fewer zone than models
				sliderFrom = this.$el.allOffsets()[this.direction], // "From" edge of slider
				sliderWidth = this.$el.width(), // Width of slider
				tickWidth = sliderWidth / zoneCount, // Calculated width of zone
				actualX = isRtl? $(window).width() - e.pageX : e.pageX; // Flipped for RTL - sliderFrom;
			actualX = actualX - sliderFrom; // Offset of mouse position in slider
			var currentModelIndex = Math.floor( ( actualX + ( tickWidth / 2 )  ) / tickWidth ); // Calculate the model index

			// Ensure sane value for currentModelIndex.
			if ( currentModelIndex < 0 )
				currentModelIndex = 0;
			else if ( currentModelIndex >= this.model.revisions.length )
				currentModelIndex = this.model.revisions.length - 1;

			// Update the tooltip mode
			this.model.set({ hoveredRevision: this.model.revisions.at( currentModelIndex ) });
		},

		mouseLeave: function() {
			this.model.set({ hovering: false });
		},

		mouseEnter: function() {
			this.model.set({ hovering: true });
		},

		applySliderSettings: function() {
			this.$el.slider( _.pick( this.model.toJSON(), 'value', 'values', 'range' ) );
			var handles = this.$('a.ui-slider-handle');

			if ( this.model.get('compareTwoMode') ) {
				// in RTL mode the 'left handle' is the second in the slider, 'right' is first
				handles.first()
					.toggleClass( 'to-handle', !! isRtl )
					.toggleClass( 'from-handle', ! isRtl );
				handles.last()
					.toggleClass( 'from-handle', !! isRtl )
					.toggleClass( 'to-handle', ! isRtl );
			} else {
				handles.removeClass('from-handle to-handle');
			}
		},

		start: function( event, ui ) {
			this.model.set({ scrubbing: true });

			// Track the mouse position to enable smooth dragging,
			// overrides default jQuery UI step behavior.
			$( window ).on( 'mousemove.wp.revisions', { view: this }, function( e ) {
				var view              = e.data.view,
				    leftDragBoundary  = view.$el.offset().left,
				    sliderOffset      = leftDragBoundary,
				    sliderRightEdge   = leftDragBoundary + view.$el.width(),
				    rightDragBoundary = sliderRightEdge,
				    leftDragReset     = '0',
				    rightDragReset    = '100%',
				    handle            = $( ui.handle );

				// In two handle mode, ensure handles can't be dragged past each other.
				// Adjust left/right boundaries and reset points.
				if ( view.model.get('compareTwoMode') ) {
					var handles = handle.parent().find('.ui-slider-handle');
					if ( handle.is( handles.first() ) ) { // We're the left handle
						rightDragBoundary = handles.last().offset().left;
						rightDragReset    = rightDragBoundary - sliderOffset;
					} else { // We're the right handle
						leftDragBoundary = handles.first().offset().left + handles.first().width();
						leftDragReset    = leftDragBoundary - sliderOffset;
					}
				}

				// Follow mouse movements, as long as handle remains inside slider.
				if ( e.pageX < leftDragBoundary ) {
					handle.css( 'left', leftDragReset ); // Mouse to left of slider.
				} else if ( e.pageX > rightDragBoundary ) {
					handle.css( 'left', rightDragReset ); // Mouse to right of slider.
				} else {
					handle.css( 'left', e.pageX - sliderOffset ); // Mouse in slider.
				}
			} );
		},

		getPosition: function( position ) {
			return isRtl ? this.model.revisions.length - position - 1: position;
		},

		// Responds to slide events
		slide: function( event, ui ) {
			var attributes, movedRevision;
			// Compare two revisions mode
			if ( this.model.get('compareTwoMode') ) {
				// Prevent sliders from occupying same spot
				if ( ui.values[1] === ui.values[0] )
					return false;
				if ( isRtl )
					ui.values.reverse();
				attributes = {
					from: this.model.revisions.at( this.getPosition( ui.values[0] ) ),
					to: this.model.revisions.at( this.getPosition( ui.values[1] ) )
				};
			} else {
				attributes = {
					to: this.model.revisions.at( this.getPosition( ui.value ) )
				};
				// If we're at the first revision, unset 'from'.
				if ( this.getPosition( ui.value ) > 0 )
					attributes.from = this.model.revisions.at( this.getPosition( ui.value ) - 1 );
				else
					attributes.from = undefined;
			}
			movedRevision = this.model.revisions.at( this.getPosition( ui.value ) );

			// If we are scrubbing, a scrub to a revision is considered a hover
			if ( this.model.get('scrubbing') )
				attributes.hoveredRevision = movedRevision;

			this.model.set( attributes );
		},

		stop: function( event, ui ) {
			$( window ).off('mousemove.wp.revisions');
			this.model.updateSliderSettings(); // To snap us back to a tick mark
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
			this.routes = _.object([
				[ this.baseUrl( '?from=:from&to=:to' ), 'handleRoute' ],
				[ this.baseUrl( '?from=:from&to=:to' ), 'handleRoute' ]
			]);
			// Maintain state and history when navigating
			this.listenTo( this.model, 'update:diff', _.debounce( this.updateUrl, 250 ) );
			this.listenTo( this.model, 'change:compareTwoMode', this.updateUrl );
		},

		baseUrl: function( url ) {
			return this.model.get('baseUrl') + url;
		},

		updateUrl: function() {
			var from = this.model.has('from') ? this.model.get('from').id : 0;
			var to = this.model.get('to').id;
			if ( this.model.get('compareTwoMode' ) )
				this.navigate( this.baseUrl( '?from=' + from + '&to=' + to ) );
			else
				this.navigate( this.baseUrl( '?revision=' + to ) );
		},

		handleRoute: function( a, b ) {
			var from, to, compareTwo = _.isUndefined( b );

			if ( ! compareTwo ) {
				b = this.model.revisions.get( a );
				a = this.model.revisions.prev( b );
				b = b ? b.id : 0;
				a = a ? a.id : 0;
			}

			this.model.set({
				from: this.model.revisions.get( parseInt( a, 10 ) ),
				to: this.model.revisions.get( parseInt( a, 10 ) ),
				compareTwoMode: compareTwo
			});
		}
	});

	// Initialize the revisions UI.
	revisions.init = function() {
		revisions.view.frame = new revisions.view.Frame({
			model: new revisions.model.FrameState({}, {
				revisions: new revisions.model.Revisions( revisions.settings.revisionData )
			})
		}).render();
	};

	$( revisions.init );
}(jQuery));
