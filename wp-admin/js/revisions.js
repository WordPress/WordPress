window.wp = window.wp || {};

(function($) {
	var Revision, Revisions, Diff, revisions;

	revisions = wp.revisions = function() {
		Diff = revisions.Diff = new Diff();
	};

	_.extend( revisions, { model: {}, view: {}, controller: {} } );

	// Link settings.
	revisions.model.settings = typeof wpRevisionsSettings === 'undefined' ? {} : wpRevisionsSettings;


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
			var self    = this;
			this.slider = new revisions.view.Slider();

			if ( null === this.revisions ) {
				this.revisions = new Revisions(); // set up collection
				this.startRightModelLoading();

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
				delay = 0,
				totalChanges;

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

								totalChanges = model.get( 'linesAdded' ) + model.get( 'linesDeleted' ),
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
								self.tickmarkView.render();
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
						'max': revisionCount - 1, // slider starts at 0 in single handle mode
						'value': self.rightDiff - 1 // slider starts at 0 in single handle mode
					}, true);
				},

				error: function() {
					self.stopRightModelLoading();
				}
			});
		},

		// load the models for the left handle (the right handler has moved)
		reloadLeft: function() {
			var self = this;
			self.startLeftModelLoading();
			self.leftHandleRevisions = new Revisions( {}, {
				'compareTo': self.revisions.at( self.rightDiff - 1 ).get( 'ID' ), // diff and model count off by 1
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
					// ensure right handle not beyond length
					if ( self.rightDiff > self.revisions.length )
						self.rightDiff = self.revisions.length;
					},

				error: function() {
					self.stopLeftModelLoading();
				}
			});
		},

		// load the models for the right handle (the left handle has moved)
		reloadRight: function() {
			var self = this;
			self.startRightModelLoading();
			self.rightHandleRevisions = new Revisions( {}, {
				'compareTo': self.revisions.at( self.leftDiff - 1 ).get( 'ID' ), // diff and model count off by 1
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
						'max': self.revisions.length
					}, true);
				},

				error: function( response ) {
					self.stopRightModelLoading();
				}
			});

		},

		/**
		 * reloadLeftRight reload models for both the left and right handles
		 */
		reloadLeftRight: function() {
			this.startRightModelLoading();
			this.startLeftModelLoading();
			this.reloadLeft();
			this.reloadRight();
		},

		disabledButtonCheck: function( val ) {
			var maxVal = this.revisions.length - 1,
				next = ! isRtl ? $( '#next' ) : $( '#previous' ),
				prev = ! isRtl ? $( '#previous' ) : $( '#next' );

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

		/**
		 * completeApplicationSetup finishes loading all views once the initial model load is complete
		 */
		completeApplicationSetup: function() {
			this.revisionView = new revisions.view.Diff({
				model: this.revisions
			});
			this.revisionView.render(); // render the revision view

			this.loadDiffs( this.revisions ); // get the actual revisions data

			this.revisionsInteractions = new revisions.view.Interact({
				model: this.revisions
			});
			this.revisionsInteractions.render(); // render the interaction view

			this.tickmarkView = new revisions.view.Tickmarks({
				model: this.revisions
			});
			this.tickmarkView.render(); // render the tickmark view
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

		/**
		 * respond to slider slide events
		 * Note: in one handle mode, jQuery UI reports leftmost position as 0
		 * in two handle mode, jQuery UI Slider reports leftmost position as 1
		 */
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

					Diff.leftDiff = isRtl ? ui.values[1] : ui.values[0]; // handles are reversed in RTL mode
				} else {
					// Right handler
					if ( Diff.rightModelLoading ) // right model still loading, prevent sliding right handle
						return false;

					Diff.rightDiff = isRtl ? ui.values[0] : ui.values[1]; // handles are reversed in RTL mode
				}

				Diff.revisionView.render();
			}
		},

		/**
		 * responds to slider start sliding events
		 * in two handle mode stores start position, so if unchanged at stop event no need to reload diffs
		 * also swaps in the appropriate models - left handled or right handled
		 */
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
					Diff.revisionView.model = Diff.leftHandleRevisions; // use the left handle models
					Diff.tickmarkView.model = Diff.leftHandleRevisions;
					Diff.tickmarkView.render();
				}

				Diff.leftDiffStart = isRtl ? ui.values[1] : ui.values[0]; // in RTL mode the 'left handle' is the second in the slider, 'right' is first

			} else {
				// Right handler
				if ( Diff.rightModelLoading || 0 === Diff.rightHandleRevisions.length) // right model still loading, prevent sliding right handle
					return false;

				if ( Diff.revisionView.model !== Diff.rightHandleRevisions &&
						null !== Diff.rightHandleRevisions ) {
					Diff.revisionView.model = Diff.rightHandleRevisions; // use the right handle models
					Diff.tickmarkView.model = Diff.rightHandleRevisions;
					Diff.tickmarkView.render();
				}

				Diff.revisionView.draggingLeft = false;
				Diff.rightDiffStart = isRtl ? ui.values[0] : ui.values[1]; // in RTL mode the 'left handle' is the second in the slider, 'right' is first
			}
		},

		/**
		 * responds to slider stop events
		 * in two handled mode, if the handle that stopped has moved, reload the diffs for the other handle
		 * the other handle compares to this handle's position, so if it changes they need to be recalculated
		 */
		stop: function( event, ui ) {
			// Not needed in one mode
			if ( this.singleRevision )
				return;

			// calculate and generate a diff for comparing to the left handle
			// and the right handle, swap out when dragging
			if ( $( ui.handle ).hasClass( 'left-handle' ) ) {
				// Left handler
				if ( Diff.leftDiffStart !== isRtl ? ui.values[1] : ui.values[0] ) // in RTL mode the 'left handle' is the second in the slider, 'right' is first
					Diff.reloadRight();
			} else {
				// Right handler
				if ( Diff.rightDiffStart !== isRtl ? ui.values[0] : ui.values[1] ) // in RTL mode the 'left handle' is the second in the slider, 'right' is first
					Diff.reloadLeft();
			}
		},

		addTooltip: function( handle, message ) {
			handle.find( '.ui-slider-tooltip' ).html( message );
		},

		width: function() {
			return $( '#diff-slider' ).width();
		},

		setWidth: function( width ) {
			$( '#diff-slider' ).width( width );
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
				stop:  $.proxy( self.stop, self )
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
			var sliderMax, sliderWidth, adjustMax, tickWidth, tickCount = 0, aTickWidth, tickMargin, self = this, firstTick, lastTick;
			sliderMax   = Diff.slider.option( 'max' );
			sliderWidth = Diff.slider.width();
			adjustMax   = Diff.singleRevision ? 0 : 1;
			tickWidth   = Math.floor( sliderWidth / ( sliderMax - adjustMax ) );
			tickWidth   = ( tickWidth > 50 ) ? 50 : tickWidth; // set minimum and maximum widths for tick marks
			tickWidth   = ( tickWidth < 6 ) ? 6 : tickWidth;
			sliderWidth = tickWidth * ( sliderMax - adjustMax ); // calculate the slider width
			aTickWidth  = $( '.revision-tick' ).width();

			if ( tickWidth !== aTickWidth ) { // is the width already set correctly?
				$( '.revision-tick' ).each( function() {
					tickMargin = Math.floor( ( tickWidth - $( this ).width() ) / 2 ) + 1;
					$( this ).css( 'border-left', tickMargin + 'px solid #f7f7f7'); // space the ticks out using margins
					$( this ).css( 'border-right', ( tickWidth - tickMargin - $( this ).width() ) + 'px solid #f7f7f7'); // space the ticks out using margins
				});
				firstTick = $( '.revision-tick' ).first(); //cache selectors for optimization
				lastTick = $( '.revision-tick' ).last();

				sliderWidth = sliderWidth + Math.ceil( ( tickWidth - ( lastTick.outerWidth() - lastTick.innerWidth() ) ) / 2 ); // room for the last tick
				sliderWidth = sliderWidth + Math.ceil( ( tickWidth - ( firstTick.outerWidth() - firstTick.innerWidth() ) ) / 2 ); // room for the first tick
				firstTick.css( 'border-left', 'none' ); // first tick gets no left border
				lastTick.css( 'border-right', 'none' ); // last tick gets no right border
			}

			/**
			 * reset the slider width
			 */
			Diff.slider.setWidth( sliderWidth );
			$( '.diff-slider-ticks-wrapper' ).width( sliderWidth );
			$( '#diff-slider-ticks' ).width( sliderWidth );

			/**
			 * go through all ticks, add hover and click interactions
			 */
			$( '.revision-tick' ).each( function() {
				Diff.slider.addTooltip ( $( this ), Diff.revisions.at( tickCount++ ).get( 'titleTooltip' ) );
				$( this ).hover(
					function() {
						$( this ).find( '.ui-slider-tooltip' ).show().append('<div class="arrow"></div>');
					},
					function() {
						$( this ).find( '.ui-slider-tooltip' ).hide().find( '.arrow' ).remove();
					}
				);

				/**
				 * move the slider handle when the tick marks are clicked
				 */
				$( this ).on( 'click',
					{ tickCount: tickCount }, // pass the tick through so we know where to move the handle
					function( event ) {
						if ( Diff.slider.singleRevision ) { // single handle mode
							Diff.rightDiff = event.data.tickCount; // reposition the right handle
							Diff.slider.refresh({
								value: Diff.rightDiff - 1
							} );
						} else { //compare two mode
							if ( isRtl ) {
								if ( event.data.tickCount < Diff.leftDiff ) { // click was on the 'left' side
										Diff.rightDiff = event.data.tickCount; // set the 'right' handle location
										Diff.reloadLeft(); // reload the left handle comparison models
								} else { // middle or 'right' clicks
									Diff.leftDiff = event.data.tickCount; // set the 'left' handle location
									Diff.reloadRight(); // reload right handle models
								}
							} else {
								if ( event.data.tickCount < Diff.leftDiff ) { // click was on the 'left' side
										Diff.leftDiff = event.data.tickCount; // set the left handle location
										Diff.reloadRight(); // reload the right handle comparison models
								} else { // middle or 'right' clicks
									Diff.rightDiff = event.data.tickCount; // set the right handle location
									Diff.reloadLeft(); // reload left handle models
								}
							}
							Diff.slider.refresh( { // set the slider handle positions
								values: [ isRtl ? Diff.rightDiff : Diff.leftDiff, isRtl ? Diff.leftDiff : Diff.rightDiff ]
							} );
						}
						Diff.revisionView.render(); // render the main view
					} );
			} );
		},

		// render the tick mark view
		render: function() {
			var self = this, addHtml;

			if ( null !== self.model ) {
				addHtml = "";
				_.each ( self.model.models, function( theModel ) {
					addHtml = addHtml + self.template ( theModel.toJSON() );
				});
				self.$el.html( addHtml );

			}
			self.resetTicks();
			return self;
		}
	} );

	/**
	 * wp.revisions.view.Interact
	 *
	 * Next/Prev buttons and the slider
	 */
	revisions.view.Interact = Backbone.View.extend({
		el: $( '#revision-interact' ),
		template: wp.template( 'revision-interact' ),

		// next and previous buttons, only available in compare one mode
		events: {
			'click #next':     ! isRtl ? 'nextRevision' : 'previousRevision',
			'click #previous': ! isRtl ? 'previousRevision' : 'nextRevision'
		},

		render: function() {
			var modelcount;
			this.$el.html( this.template );

			modelcount = Diff.revisions.length;

			Diff.slider.singleRevision = Diff.singleRevision;
			Diff.slider.render();

			if ( Diff.singleRevision ) {
				Diff.slider.refresh({
					value: Diff.rightDiff - 1, // rightDiff value is off model index by 1
					min: 0,
					max: modelcount - 1
				});

				$( '#revision-diff-container' ).removeClass( 'comparing-two-revisions' );

			} else {
				Diff.slider.refresh({
					// in RTL mode the 'left handle' is the second in the slider, 'right' is first
					values: [ isRtl ? Diff.rightDiff : Diff.leftDiff, isRtl ? Diff.leftDiff : Diff.rightDiff ],
					min: 1,
					max: modelcount + 1,
					range: true
				});

				$( '#revision-diff-container' ).addClass( 'comparing-two-revisions' );
				// in RTL mode the 'left handle' is the second in the slider, 'right' is first
				$( '#diff-slider a.ui-slider-handle' ).first().addClass( isRtl ? 'right-handle' : 'left-handle' );
				$( '#diff-slider a.ui-slider-handle' ).last().addClass( isRtl ? 'left-handle' : 'right-handle' );

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

		// go to the previous revision
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
	 * Diff, compare two checkbox and restore button
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
			var addHtml = '', thediff;

			// compare two revisions mode?
			if ( ! Diff.singleRevision ) {
				if ( this.draggingLeft ) {
					thediff = Diff.leftDiff - 1; //leftDiff value is off model index by 1
					if ( this.model.at( thediff ) ) {
						addHtml = this.template( this.model.at( thediff ).toJSON() );
					}
				} else { // dragging right handle
					thediff = Diff.rightDiff - 1; // rightDiff value is off model index by 1
					if ( this.model.at( thediff ) ) {
						addHtml = this.template( this.model.at( thediff ).toJSON() );
					}
				}
			} else { // end compare two revisions mode, eg only one slider handle
				if ( this.model.at( Diff.rightDiff - 1 ) ) { // rightDiff value is off model index by 1
					addHtml = this.template( this.model.at( Diff.rightDiff - 1 ).toJSON() );
				}
			}
			this.$el.html( addHtml );

			if ( this.model.length < 2 ) {
				$( '#diff-slider' ).hide(); // don't allow compare two if fewer than three revisions
				$( '.diff-slider-ticks-wrapper' ).hide();
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

				// in RTL mode handles are swapped, so boundary checks are different;
				if ( isRtl ){
					Diff.leftDiff = Diff.revisions.length; // put the left handle at the rightmost position, representing current revision

					if ( Diff.revisions.length === Diff.rightDiff ) // make sure 'left' handle not in rightmost slot
						Diff.rightDiff = Diff.rightDiff - 1;
				} else {
					if ( 1 === Diff.rightDiff ) // make sure right handle not in leftmost slot
						Diff.rightDiff = 2;
				}

				Diff.revisionView.draggingLeft = false;

				revisions.model.settings.revision_id = ''; // reset passed revision id so switching back to one handle mode doesn't re-select revision
				Diff.reloadLeftRight(); // load diffs for left and right handles
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
				'&compare_to=' + this.options.compareTo + // revision are we comparing to
				'&post_id=' + this.options.post_id + // the post id
				'&show_autosaves=' + this.options.showAutosaves + // show or hide autosaves
				'&show_split_view=' + this.options.showSplitView + // show in split view or single column view
				'&right_handle_at=' + this.options.rightHandleAt + // mark point for comparison list
				'&left_handle_at=' + this.options.leftHandleAt + // mark point for comparison list
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
