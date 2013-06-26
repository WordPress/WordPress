window.wp = window.wp || {};

(function($) {
	var revisions;

	revisions = wp.revisions = { model: {}, view: {}, controller: {} };

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
		},
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

/**/
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

			this.listenTo( this, 'change:from change:to', this.updateDiffId );
		},

		updateDiffId: function() {
			var from = this.get( 'from' );
			var to   = this.get( 'to' );
			this.set( 'diffId', (from ? from.id : '0') + ':' + to.id );
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
			});
		}
	});

	// The control view.
	// This contains the revision slider, previous/next buttons, and the compare checkbox.
	revisions.view.Controls = wp.Backbone.View.extend({
		tagName: 'div',
		className: 'revisions-controls',

		initialize: function() {
			// Add the button view
			this.views.add( new revisions.view.Buttons({ 
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

		initialize: function() {
			this.listenTo( this.model, 'change:diffId', this.updateMeta );
		},

		events: {
			'click #restore-revision': 'restoreRevision'
		},

		restoreRevision: function() {
			var restoreUrl    = this.model.get('to').attributes.restoreUrl.replace(/&amp;/g, '&');
			document.location = restoreUrl;
		},

		updateMeta: function() {
			this.$el.html( this.template( this.model.toJSON() ) );
			if( this.model.get( 'to' ).attributes.current ) {
				$( '#restore-revision' ).prop( 'disabled', true);
			} else {
				$( '#restore-revision' ).prop( 'disabled', false)
			}
		}
	});


	// The buttons view.
	// Encapsulates all of the configuration for the previous/next buttons, and the compare checkbox.
	revisions.view.Buttons = wp.Backbone.View.extend({
		tagName: 'div',
		className: 'revisions-buttons',
		template: wp.template('revisions-controls'),

		initialize: function() {
			this.$el.html( this.template() )
		},

		events: {
			'click #next': 'nextRevision',
			'click #previous': 'previousRevision'
		},
		
		gotoModel: function( toIndex ) {
			var attributes = {
				to: this.model.revisions.at( isRtl ? this.model.revisions.length - toIndex - 1 : toIndex ) // Reverse directions for Rtl
			};
			// If we're at the first revision, unset 'from'.
			if ( isRtl ? this.model.revisions.length - toIndex - 1 : toIndex ) // Reverse directions for Rtl
				attributes.from = this.model.revisions.at( isRtl ? this.model.revisions.length - toIndex - 2 : toIndex - 1 );
			else
				this.model.unset('from', { silent: true });

			this.model.set( attributes );
		},

		nextRevision: function() {
			var toIndex = this.model.revisions.indexOf( this.model.get( 'to' ) );
			toIndex     = isRtl ? toIndex - 1 : toIndex + 1;
			this.gotoModel( toIndex );
		},
		
		previousRevision: function() {
			var toIndex = this.model.revisions.indexOf( this.model.get('to') );
			toIndex     = isRtl ? toIndex + 1 : toIndex - 1;
			this.gotoModel( toIndex );
		},

		ready: function() {
			this.listenTo( this.model, 'change:diffId', this.disabledButtonCheck );
		},

		// Check to see if the Previous or Next buttons need to be disabled or enabled
		disabledButtonCheck: function() {
			var maxVal   = isRtl ? 0 : this.model.revisions.length - 1,
				minVal   = isRtl ? this.model.revisions.length - 1 : 0,
				next     = $( '.revisions-next .button' ),
				previous = $( '.revisions-previous .button' ),
				val      = this.model.revisions.indexOf( this.model.get( 'to' ) );

			// Disable "Next" button if you're on the last node
			if ( maxVal === val )
				next.prop( 'disabled', true );
			else
				next.prop( 'disabled', false );

			// Disable "Previous" button if you're on the first node
			if ( minVal === val )
				previous.prop( 'disabled', true );
			else
				previous.prop( 'disabled', false );
		},


	});

	// The slider view.
	// Encapsulates all of the configuration for the jQuery UI slider into a view.
	revisions.view.Slider = wp.Backbone.View.extend({
		tagName: 'div',
		className: 'wp-slider',

		initialize: function() {
			_.bindAll( this, 'start', 'slide', 'stop' );

			// Create the slider model from the provided collection data.
			// TODO: This should actually pull from the model's `to` key.
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
			this.settings.on( 'change', function( model, options ) {
				// Apply changes to slider settings here.
 				this.$el.slider( { value: this.model.revisions.indexOf( this.model.get( 'to' ) ) } ); // Set handle to current to model
			}, this );
			// Reset to the initially selected revision
			this.slide( '', this.settings.attributes );

			// Listen for changes in the diffId
			this.listenTo( this.model, 'change:diffId', this.diffIdChanged );

		},

		diffIdChanged: function() {
			// Reset the view settings when diffId is changed
			this.settings.set( { 'value': this.model.revisions.indexOf( this.model.get( 'to' ) ) } );
		},

		start: function( event, ui ) {
			// Track the mouse position to enable smooth dragging, overrides default jquery ui step behaviour 
			$( window ).mousemove( function( e ) { 
				var sliderLeft  = $( '.wp-slider' ).offset().left,
					sliderRight = sliderLeft + $( '.wp-slider' ).width();

				// Follow mouse movements, as long as handle remains inside slider
				if ( e.clientX < sliderLeft ) {
					$( ui.handle ).css( 'left', 0 ); // Mouse to left of slider
				} else if ( e.clientX > sliderRight ) {
					$( ui.handle ).css( 'left', sliderRight - sliderLeft); // Mouse to right of slider
				} else {
					$( ui.handle ).css( 'left', e.clientX - sliderLeft ); // Mouse in slider
				}
			} ); // End mousemove 
		},

		slide: function( event, ui ) {
			var attributes = {
				to: this.model.revisions.at( isRtl ? this.model.revisions.length - ui.value - 1 : ui.value ) // Reverse directions for Rtl
			};

			// If we're at the first revision, unset 'from'.
			if ( isRtl ? this.model.revisions.length - ui.value - 1 : ui.value ) // Reverse directions for Rtl
				attributes.from = this.model.revisions.at( isRtl ? this.model.revisions.length - ui.value - 2 : ui.value - 1 );
			else
				this.model.unset('from', { silent: true });

			this.model.set( attributes );
		},

		stop: function( event, ui ) {
			$( window ).unbind( 'mousemove' ); // Stop tracking the mouse
			// Reset settings pops handle back to the step position
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

	// Initialize the revisions UI.
	revisions.init = function() {
		revisions.view.frame = new revisions.view.Frame({
			collection: new revisions.model.Revisions( revisions.settings.revisionData )
		}).render();
	};

	$( revisions.init );
}(jQuery));
