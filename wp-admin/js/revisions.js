window.wp = window.wp || {};

(function($) {
	var Revision, Revisions, Diff, l10n, revisions;

	revisions = wp.revisions = function() {
		Diff = revisions.Diff = new Diff();
	};

	_.extend( revisions, { model: {}, view: {}, controller: {} } );

	// Link any localized strings.
	l10n = revisions.model.l10n = typeof wpRevisionsL10n === 'undefined' ? {} : wpRevisionsL10n;

	// Link any settings.
	revisions.model.settings = l10n.settings || {};
	delete l10n.settings;


	/**
	 * ========================================================================
	 * CONTROLLERS
	 * ========================================================================
	 */

	/**
	 * wp.revisions.controller.Diff
	 *
	 * Controlls the diff
	 */
	Diff = revisions.controller.Diff = Backbone.Model.extend( {
		rightDiff: 1,
		leftDiff: 1,
		revisions: null,
		leftHandleRevisions: null,
		rightHandleRevisions: null,
		revisionsInteractions: null,
		autosaves: true,
		showSplitView: true,
		singleRevision: true,
		leftModelLoading: false,	// keep track of model loads
		rightModelLoading: false,	// disallow slider interaction, also repeat loads, while loading
		tickmarkView: null, // the slider tickmarks
		slider: null, // the slider instance

		constructor: function() {
			this.slider = new revisions.view.Slider();
			if ( null === this.revisions ) {
				this.revisions = new Revisions(); // set up collection
				this.startRightModelLoading();

				var self = this;
				this.revisions.fetch({ // load revision data
					success: function() {
						self.stopRightModelLoading();
						self.completeApplicationSetup();
					}
				});
			}
		},

		loadDiffs: function( models ) {
			var self = this,
				revisionsToLoad = models.where( { completed: false } ),
				delay = 0;

			// match slider to passed revision_id
			_.each( revisionsToLoad, function( revision ) {
				if ( revision.get( 'ID' ) == revisions.model.settings.revision_id )
					self.rightDiff = self.revisions.indexOf( revision ) + 1;
			});

			_.each( revisionsToLoad, function( revision ) {
					_.delay( function() {
						revision.fetch( {
							update: true,
							add: false,
							remove: false,
							success: function( model ) {
								model.set( 'completed', true );

								// stop spinner when all models are loaded
								if ( 0 === models.where( { completed: false } ).length )
									self.stopModelLoadingSpinner();

								self.tickmarkView.render();

								var totalChanges = model.get( 'linesAdded' ) + model.get( 'linesDeleted' ),
									scopeOfChanges = 'vsmall';

								// Note: hard coded scope of changes
								// TODO change to dynamic based on range of values
								if ( totalChanges > 1 && totalChanges <= 3 ) {
									scopeOfChanges = 'small';
								} else if ( totalChanges > 3 && totalChanges <= 5 ) {
									scopeOfChanges = 'med';
								} else if ( totalChanges > 5 && totalChanges <= 10 ) {
									scopeOfChanges = 'large';
								} else if ( totalChanges > 10 ) {
									scopeOfChanges = 'vlarge';
								}
								model.set( 'scopeOfChanges', scopeOfChanges );
								if ( 0 !== self.rightDiff &&
									model.get( 'ID' ) === self.revisions.at( self.rightDiff - 1 ).get( 'ID' ) ) {
									// reload if current model refreshed
									self.revisionView.render();
								}

							}
					} );
					}, delay ) ;
					delay = delay + 150; // stagger model loads to avoid hammering server with requests
				}
			);
		},

		startLeftModelLoading: function() {
			this.leftModelLoading = true;
			$('#revision-diff-container').addClass('left-model-loading');
		},

		stopLeftModelLoading: function() {
			this.leftModelLoading = false;
		},

		startRightModelLoading: function() {
			this.rightModelLoading = true;
			$('#revision-diff-container').addClass('right-model-loading');
		},

		stopRightModelLoading: function() {
			this.rightModelLoading = false;
		},

		stopModelLoadingSpinner: function() {
			$('#revision-diff-container').removeClass('right-model-loading');
			$('#revision-diff-container').removeClass('left-model-loading');
		},

		reloadModel: function() {
			if ( this.singleRevision ) {
				this.reloadModelSingle();
			} else {
				this.reloadLeftRight();
			}
		},

		// load the models for the single handle mode
		reloadModelSingle: function() {
			var self = this;

			self.startRightModelLoading();

			self.revisions.reload({
				options: {
				'showAutosaves': self.autosaves,
				'showSplitView': self.showSplitView
				},

				success: function() {
					var revisionCount = self.revisions.length;
					self.revisionView.model = self.revisions;
					self.revisionView.render();
					self.loadDiffs( self.revisions );
					self.tickmarkView.model = self.revisions;
					self.tickmarkView.render();
					self.slider.refresh({
						'max': revisionCount - 1,
						'value': self.rightDiff - 1
					}, true);
				},

				error: function() {
					self.stopRightModelLoading();
				}
			});
		},

		// load the models for the left handle
		reloadLeft: function() {
			var self = this;
			self.startLeftModelLoading();
			self.leftHandleRevisions = new Revisions( {}, {
				'compareTo': self.revisions.at( self.rightDiff - 1 ).get( 'ID' ),
				'showAutosaves': self.autosaves,
				'showSplitView': self.showSplitView,
				'rightHandleAt': self.rightDiff
			});

			self.leftHandleRevisions.fetch({
				success: function(){
					self.stopLeftModelLoading();
					self.loadDiffs( self.leftHandleRevisions );
					self.tickmarkView.model = self.leftHandleRevisions;
					self.slider.refresh({
						'max': self.revisions.length
					});
					// ensure right handle not beyond length, in particular if viewing autosaves is switched from on to off
					// the number of models in the collection might get shorter, this ensures right handle is not beyond last model
					if ( self.rightDiff > self.revisions.length )
						self.rightDiff = self.revisions.length;
					},

				error: function() {
					self.stopLeftModelLoading();
				}
			});
		},

		// load the models for the right handle
		reloadRight: function() {
			var self = this;
			self.startRightModelLoading();
			self.rightHandleRevisions = new Revisions( {}, {
				'compareTo': self.revisions.at( self.leftDiff - 1 ).get( 'ID' ),
				'showAutosaves': self.autosaves,
				'showSplitView': self.showSplitView,
				'leftHandleAt': self.leftDiff
			});

			self.rightHandleRevisions.fetch({
				success: function(){
					self.stopRightModelLoading();
					self.loadDiffs( self.rightHandleRevisions );
					self.tickmarkView.model = self.rightHandleRevisions;
					self.slider.refresh({
						'max': self.revisions.length,
						'values': [ self.leftDiff, self.rightDiff]
					}, true);
				},

				error: function( response ) {
					self.stopRightModelLoading();
				}
			});

		},

		reloadLeftRight: function() {
			this.startRightModelLoading();
			this.startLeftModelLoading();
			this.reloadLeft();
			this.reloadRight();
		},

		disabledButtonCheck: function( val ) {
			var maxVal = this.revisions.length - 1,
				next = $( '#next' ),
				prev = $( '#previous' );

			// Disable "Next" button if you're on the last node
			if ( maxVal === val )
				next.prop( 'disabled', true );
			else
				next.prop( 'disabled', false );

			// Disable "Previous" button if you're on the 0 node
			if ( 0 === val )
				prev.prop( 'disabled', true );
			else
				prev.prop( 'disabled', false );
		},

		completeApplicationSetup: function() {
			this.revisionView = new revisions.view.Diff({
				model: this.revisions
			});
			this.revisionView.render();

			this.loadDiffs( this.revisions );

			this.revisionsInteractions = new revisions.view.Interact({
				model: this.revisions
			});
			this.revisionsInteractions.render();

			this.tickmarkView = new revisions.view.Tickmarks({
				model: this.revisions
			});
			this.tickmarkView.render();
			this.tickmarkView.resetTicks();
		}
	});


	/**
	 * ========================================================================
	 * VIEWS
	 * ========================================================================
	 */

	/**
	 * wp.revisions.view.Slider
	 *
	 * The slider
	 */
	revisions.view.Slider = Backbone.View.extend({
		el: $( '#diff-slider' ),
		singleRevision: true,

		initialize: function( options ) {
			this.options = _.defaults( options || {}, {
				value: 0,
				min: 0,
				max: 1,
				step: 1
			});
		},

		slide: function( event, ui ) {
			if ( this.singleRevision ) {
				Diff.rightDiff = ( ui.value + 1 );
				Diff.revisionView.render();
				Diff.disabledButtonCheck( ui.value );
			} else {
				if ( ui.values[0] === ui.values[1] ) // prevent compare to self
					return false;

				if ( $( ui.handle ).hasClass( 'left-handle' ) ) {
					// Left handler
					if ( Diff.leftModelLoading ) // left model still loading, prevent sliding left handle
						return false;

					Diff.leftDiff = ui.values[0];
				} else {
					// Right handler
					if ( Diff.rightModelLoading ) // right model still loading, prevent sliding right handle
						return false;

					Diff.rightDiff = ui.values[1];
				}

				if ( 0 === Diff.leftDiff ) {
					$( '#revision-diff-container' ).addClass( 'current-version' );
				} else {
					$( '#revision-diff-container' ).removeClass( 'current-version' );
				}

				Diff.revisionView.render();
			}
		},

		start: function( event, ui ) {
			// Not needed in one mode
			if ( this.singleRevision )
				return;

			if ( $( ui.handle ).hasClass( 'left-handle' ) ) {
				// Left handler
				if ( Diff.leftModelLoading ) // left model still loading, prevent sliding left handle
					return false;

				Diff.revisionView.draggingLeft = true;

				if ( Diff.revisionView.model !== Diff.leftHandleRevisions &&
						null !== Diff.leftHandleRevisions ) {
					Diff.revisionView.model = Diff.leftHandleRevisions;
					Diff.tickmarkView.model = Diff.leftHandleRevisions;
					Diff.tickmarkView.render();
				}

				Diff.leftDiffStart = ui.values[ 0 ];

			} else {
				// Right handler
				if ( Diff.rightModelLoading || 0 === Diff.rightHandleRevisions.length) // right model still loading, prevent sliding right handle
					return false;

				if ( Diff.revisionView.model !== Diff.rightHandleRevisions &&
						null !== Diff.rightHandleRevisions ) {
					Diff.revisionView.model = Diff.rightHandleRevisions;
					Diff.tickmarkView.model = Diff.rightHandleRevisions;
					Diff.tickmarkView.render();
				}

				Diff.revisionView.draggingLeft = false;
				Diff.rightDiffStart = ui.values[1];
			}
		},

		stop: function( event, ui ) {
			// Not needed in one mode
			if ( this.singleRevision )
				return;

			// calculate and generate a diff for comparing to the left handle
			// and the right handle, swap out when dragging
			if ( $( ui.handle ).hasClass( 'left-handle' ) ) {
				// Left hadnler
				if ( Diff.leftDiffStart !== ui.values[0] )
					Diff.reloadRight();
			} else {
				// Right handler
				if ( Diff.rightDiffStart !== ui.values[1] )
					Diff.reloadLeft();
			}
		},

		addTooltip: function( handle, message ) {
			handle.attr( 'title', '' ).tooltip({
				track: false,

				position: {
					my: "left-30 top-66",
					at: "top left",
					using: function( position, feedback ) {
						$( this ).css( position );
						$( "<div>" )
						.addClass( "arrow" )
						.addClass( feedback.vertical )
						.addClass( feedback.horizontal )
						.appendTo( $( this ) );
					}
				},
				show: false,
				hide: false,
				content:  function() {
					return message;
				}

			} );
		},

		width: function() {
			return $( '#diff-slider' ).width();
		},

		setWidth: function( width ) {
			return $( '#diff-slider' ).width( width );
		},

		refresh: function( options, slide ) {
			$( '#diff-slider' ).slider( 'option', options );

			// Triggers the slide event
			if ( slide )
				$( '#diff-slider' ).trigger( 'slide' );

			Diff.disabledButtonCheck( options.value );
		},

		option: function( key ) {
			return $( '#diff-slider' ).slider( 'option', key );
		},

		render: function() {
			var self = this;
			// this.$el doesn't work, why?
			$( '#diff-slider' ).slider( {
				slide: $.proxy( self.slide, self ),
				start: $.proxy( self.start, self ),
				stop: $.proxy( self.stop, self )
			} );

			// Set options
			this.refresh( this.options );
		}
	});

	/**
	 * wp.revisions.view.Tickmarks
	 *
	 * The slider tickmarks.
	 */
	revisions.view.Tickmarks = Backbone.View.extend({
		el: $('#diff-slider-ticks'),
		template: wp.template('revision-ticks'),
		model: Revision,

		resetTicks: function() {
			var sliderMax   = Diff.slider.option( 'max' );
			var sliderWidth = Diff.slider.width();
			var adjustMax   = Diff.singleRevision ? 0 : 1;
			var tickWidth   = Math.floor( sliderWidth / ( sliderMax - adjustMax ) );

			// TODO: adjust right margins for wider ticks so they stay centered on handle stop point

			// set minimum and maximum widths for tick marks
			tickWidth = (tickWidth > 50 ) ? 50 : tickWidth;
			tickWidth = (tickWidth < 10 ) ? 10 : tickWidth;

			sliderWidth = tickWidth * (sliderMax - adjustMax ) + 1;

			Diff.slider.setWidth( sliderWidth );
			$( '.diff-slider-ticks-wrapper' ).width( sliderWidth );
			$( '#diff-slider-ticks' ).width( sliderWidth );

			var aTickWidth = $( '.revision-tick' ).width();

			if ( tickWidth !== aTickWidth ) { // is the width already set correctly?
				$( '.revision-tick' ).each( function( ) {
					$(this).css( 'margin-right', tickWidth - 1 + 'px'); // space the ticks out using right margin
				});

				$( '.revision-tick' ).last().css( 'margin-right', '0' ); // last tick gets no right margin
			}

		},

		// render the tickmark view
		render: function() {
			var self = this;

			if ( null !== self.model ) {
				var addHtml = "";
				_.each ( self.model.models, function( theModel ) {
					addHtml = addHtml + self.template ( theModel.toJSON() );
				});
				self.$el.html( addHtml );

			}
			self.resetTicks();
			return self;
		}
	});

	/**
	 * wp.revisions.view.Interact
	 *
	 * Next/Prev buttons and the slider
	 */
	// TODO: Change Interact to something else.
	revisions.view.Interact = Backbone.View.extend({
		el: $( '#revision-interact' ),
		template: wp.template( 'revision-interact' ),

		// next and previous buttons, only available in compare one mode
		events: {
			'click #next':     'nextRevision',
			'click #previous': 'previousRevision'
		},

		render: function() {
			this.$el.html( this.template );

			var modelcount = Diff.revisions.length;

			Diff.slider.singleRevision = Diff.singleRevision;
			Diff.slider.render();

			if ( Diff.singleRevision ) {
				Diff.slider.refresh({
					value: Diff.rightDiff - 1,
					min: 0,
					max: modelcount - 1
				});

				$( '#revision-diff-container' ).removeClass( 'comparing-two-revisions' );

			} else {
				Diff.slider.refresh({
					values: [ Diff.leftDiff, Diff.rightDiff + 1 ],
					min: 1,
					max: modelcount + 1,
					range: true
				});

				$( '#revision-diff-container' ).addClass( 'comparing-two-revisions' );
				$( '#diff-slider a.ui-slider-handle' ).first().addClass( 'left-handle' );
				$( '#diff-slider a.ui-slider-handle' ).last().addClass( 'right-handle' );

			}

			return this;
		},

		// go to the next revision
		nextRevision: function() {
			if ( Diff.rightDiff < this.model.length ) // unless at right boundry
				Diff.rightDiff = Diff.rightDiff + 1 ;

			Diff.revisionView.render();

			Diff.slider.refresh({
				value: Diff.rightDiff - 1
			}, true );
		},

		// go the the previous revision
		previousRevision: function() {
			if ( Diff.rightDiff > 1 ) // unless at left boundry
				Diff.rightDiff = Diff.rightDiff - 1 ;

			Diff.revisionView.render();

			Diff.slider.refresh({
				value: Diff.rightDiff - 1
			}, true );
		}
	});

	/**
	 * wp.revisions.view.Diff
	 *
	 * Next/Prev buttons and the slider
	 */
	revisions.view.Diff = Backbone.View.extend({
		el: $( '#revisions-diff' ),
		template: wp.template( 'revisions-diff' ),
		draggingLeft: false,

		// the compare two button is in this view, add the interaction here
		events: {
			'click #compare-two-revisions': 'compareTwo',
			'click #restore-revision':      'restore'
		},

		// render the revisions
		render: function() {
			var addHtml = '';
			var thediff;

			// compare two revisions mode?
			if ( ! Diff.singleRevision ) {
				if ( this.draggingLeft ) {
					thediff = Diff.leftDiff - 1;
					if ( this.model.at( thediff ) ) {
						addHtml = this.template( this.model.at( thediff ).toJSON() );
					}
				} else { // dragging right handle
					thediff = Diff.rightDiff -1;
					if ( this.model.at( thediff ) ) {
						addHtml = this.template( this.model.at( thediff ).toJSON() );
					}
				}
			} else { // end compare two revisions mode, eg only one slider handle
				if ( this.model.at( Diff.rightDiff - 1 ) ) {
					addHtml = this.template( this.model.at( Diff.rightDiff - 1 ).toJSON() );
				}
			}
			this.$el.html( addHtml );

			if ( this.model.length < 2 ) {
				$( '#diff-slider' ).hide(); // don't allow compare two if fewer than three revisions
				$( '.diff-slider-ticks-wrapper' ).hide();
			}

			// add tooltips to the handles
			if ( ! Diff.singleRevision ) {
				Diff.slider.addTooltip ( $( 'a.ui-slider-handle.left-handle' ),
					( Diff.leftDiff < 0 ) ? '' : Diff.revisions.at( Diff.leftDiff - 1 ).get( 'titleTooltip' ) );
				Diff.slider.addTooltip ( $( 'a.ui-slider-handle.right-handle' ),
					( Diff.rightDiff > Diff.revisions.length ) ? '' : Diff.revisions.at( Diff.rightDiff - 1 ).get( 'titleTooltip' ) );
			} else {
				Diff.slider.addTooltip ( $( 'a.ui-slider-handle' ),
					( Diff.rightDiff > Diff.revisions.length ) ? '' : Diff.revisions.at( Diff.rightDiff - 1 ).get( 'titleTooltip' ) );
			}

			this.toggleCompareTwoCheckbox();

			// hide the restore button when on the last sport/current post data
			$( '#restore-revision' ).toggle( ! Diff.revisions.at( Diff.rightDiff - 1 ).get( 'isCurrent' ) );

			return this;
		},

		toggleCompareTwoCheckbox: function() {
			// don't allow compare two if fewer than three revisions
			if ( this.model.length < 3 )
				$( '#toggle-revision-compare-mode' ).hide();

			$( '#compare-two-revisions' ).prop( 'checked', ! Diff.singleRevision );
		},

		// turn on/off the compare two mode
		compareTwo: function() {
			if ( $( '#compare-two-revisions' ).is( ':checked' ) ) { // compare 2 mode
				Diff.singleRevision = false ;

				if ( 1 === Diff.rightDiff )
					Diff.rightDiff = 2;

				Diff.revisionView.draggingLeft = false;

				revisions.model.settings.revision_id = ''; // reset passed revision id so switching back to one handle mode doesn't re-select revision
				Diff.reloadLeftRight();
				Diff.revisionView.model = Diff.rightHandleRevisions;

			} else { // compare one mode
				Diff.singleRevision = true;
				Diff.revisionView.draggingLeft = false;
				Diff.reloadModelSingle();
			}
			Diff.revisionsInteractions.render();
			Diff.tickmarkView.render();
		},

		restore: function() {
			document.location = $( '#restore-revision' ).data( 'restoreLink' );
		}
	});


	/**
	 * ========================================================================
	 * MODELS
	 * ========================================================================
	 */

	/**
	 * wp.revisions.Revision
	 */
	Revision = revisions.model.Revision = Backbone.Model.extend({
		idAttribute: 'ID',

		defaults: {
			ID: 0,
			titleTo: '',
			titleTooltip: '',
			titleFrom: '',
			diff: '<div class="diff-loading"><div class="spinner"></div></div>',
			restoreLink: '',
			completed: false,
			linesAdded: 0,
			linesDeleted: 0,
			scopeOfChanges: 'none',
			previousID: 0,
			isCurrent: false
		},

		url: function() {
			if ( Diff.singleRevision ) {
				return ajaxurl +
					'?action=revisions-data' +
					'&show_autosaves=true' +
					'&show_split_view=true' +
					'&nonce=' + revisions.model.settings.nonce +
					'&single_revision_id=' + this.id +
					'&compare_to=' + this.get( 'previousID' ) +
					'&post_id=' + revisions.model.settings.post_id;
			} else {
				return this.collection.url() + '&single_revision_id=' + this.id;
			}

		}
	});

	/**
	 * wp.revisions.Revisions
	 */
	Revisions = revisions.Revisions = Backbone.Collection.extend({
		model: Revision,

		initialize: function( models, options ) {
			this.options = _.defaults( options || {}, {
				'compareTo': revisions.model.settings.post_id,
				'post_id': revisions.model.settings.post_id,
				'showAutosaves': true,
				'showSplitView': true,
				'rightHandleAt': 0,
				'leftHandleAt': 0,
				'nonce': revisions.model.settings.nonce
			});
		},

		url: function() {
			return ajaxurl +
				'?action=revisions-data' +
				'&compare_to=' + this.options.compareTo +
				'&post_id=' + this.options.post_id +
				'&show_autosaves=' + this.options.showAutosaves +
				'&show_split_view=' + this.options.showSplitView +
				'&right_handle_at=' + this.options.rightHandleAt +
				'&left_handle_at=' + this.options.leftHandleAt +
				'&nonce=' + this.options.nonce;
		},

		reload: function( options ) {
			this.options = _.defaults( options.options || {}, this.options );

			this.fetch({
				success: options.success || null,
				error: options.error || null
			});
		}

	} );

	$( wp.revisions );

}(jQuery));
