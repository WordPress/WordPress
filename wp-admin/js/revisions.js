window.wp = window.wp || {};

(function($) {
	wp.revisions = {

		views: {},

		Model: Backbone.Model.extend({
			idAttribute: 'ID',
			urlRoot: ajaxurl +	'?action=revisions-data' +
				'&show_autosaves=true&show_split_view=true&nonce=' + wpRevisionsSettings.nonce,
			defaults: {
				ID: 0,
				revision_date_author: '',
				revision_date_author_short: '',
				revisiondiff: '<div class="diff-loading"><div class="spinner"></div></div>',
				restoreaction: '',
				revision_from_date_author: '',
				revision_toload: false,
				lines_added: 0,
				lines_deleted: 0,
				scope_of_changes: 'none',
				previous_revision_id: 0
			},

			url: function() {
				if ( 1 === REVAPP._compareOneOrTwo ) {
					return this.urlRoot +
						'&single_revision_id=' + this.id +
						'&compare_to=' + this.get( 'previous_revision_id' ) +
						'&post_id=' + wpRevisionsSettings.post_id;
				} else {
					return this.urlRoot +
				'&single_revision_id=' + this.id;
				}

			}

		}),

		app: _.extend({}, Backbone.Events),

		App: Backbone.Router.extend({
			_revisions: null,
			_leftHandleRevisions: null,
			_rightHandleRevisions: null,
			_revisionsInteractions: null,
			_revisionsOptions: null,
			_leftDiff: 1,
			_rightDiff: 1,
			_autosaves: true,
			_showSplitView: true,
			_compareOneOrTwo: 1,
			_leftModelLoading: false,	// keep track of model loads
			_rightModelLoading: false,	// disallow slider interaction, also repeat loads, while loading
			_tickmarkView: null, // the slider tickmarks

			routes: {
			},

			reloadToLoadRevisions: function( model_collection, reverse_direction ) {
				var self = this,
				    revisionsToLoad = model_collection.where( { revision_toload: true } ),
				    delay = 0;
				// match slider to passed revision_id
				_.each( revisionsToLoad, function( theModel ) {
					if ( theModel.get( 'ID' )  == wpRevisionsSettings.revision_id ) {
						self._rightDiff = self._revisions.indexOf( theModel ) + 1;
					}

				});
				_.each( revisionsToLoad, function( theModel ) {
						theModel.urlRoot = model_collection.url;
						_.delay( function() {
							theModel.fetch( {
								update: true,
								add: false,
								remove: false,
								success: function( model ) {
									model.set( 'revision_toload', 'false' );

									// stop spinner when all models are loaded
									if ( 0 === model_collection.where( { revision_toload: true } ).length )
										self.stopModelLoadingSpinner();

									self._tickmarkView.render();

									var total_changes = model.get( 'lines_added' ) + model.get( 'lines_deleted'),
									    scope_of_changes = 'vsmall';

									// Note: hard coded scope of changes
									// TODO change to dynamic based on range of values
									if  ( total_changes > 1 && total_changes <= 3 ) {
										scope_of_changes = 'small';
									} else if(total_changes > 3 && total_changes <= 5 ) {
										scope_of_changes = 'med';
									} else if(total_changes > 5 && total_changes <= 10 ) {
										scope_of_changes = 'large';
									} else if(total_changes > 10 ) {
										scope_of_changes = 'vlarge';
									}
									model.set( 'scope_of_changes', scope_of_changes );
									if ( 0 !== self._rightDiff &&
										model.get( 'ID' ) === self._revisions.at( self._rightDiff - 1 ).get( 'ID' ) ) {
										// reload if current model refreshed
										self._revisionView.render();
									}

								}
						} );
						}, delay ) ;
						delay = delay + 150; // stagger model loads to avoid hammering server with requests
					}
				);
			},

			startLeftModelLoading: function() {
				this._leftModelLoading = true;
				$('.revisiondiffcontainer').addClass('leftmodelloading');
			},

			stopLeftModelLoading: function() {
				this._leftModelLoading = false;
			},

			startRightModelLoading: function() {
				this._rightModelLoading = true;
				$('.revisiondiffcontainer').addClass('rightmodelloading');
			},

			stopRightModelLoading: function() {
				this._rightModelLoading = false;
			},

			stopModelLoadingSpinner: function() {
				$('.revisiondiffcontainer').removeClass('rightmodelloading');
				$('.revisiondiffcontainer').removeClass('leftmodelloading');
			},

			reloadModel: function() {
				if ( 2 === this._compareOneOrTwo ) {
					this.reloadLeftRight();
				} else {
					this.reloadModelSingle();
				}
			},

			// load the models for the single handle mode
			reloadModelSingle: function() {
				var self = this;
				self._revisions.url = ajaxurl +	'?action=revisions-data&compare_to=' + wpRevisionsSettings.post_id +
											'&show_autosaves=' + self._autosaves +
											'&show_split_view=' +  self._showSplitView +
											'&nonce=' + wpRevisionsSettings.nonce;
				self.startRightModelLoading();
				self._revisions.fetch({ // reload revision data
					success: function() {
						console.log('loaded');
						// self.stopRightModelLoading();
						// REVAPP._rightDiff -= 1;
						var revisionCount = self._revisions.length;
						self._revisionView.model = self._revisions;
						self._revisionView.render();
						self.reloadToLoadRevisions( self._revisions );
						self._tickmarkView.model = self._revisions;
						self._tickmarkView.render();
						$( '#slider' ).slider( 'option', 'max', revisionCount - 1 ); // TODO: test this, if autosave option changed
						$( '#slider' ).slider( 'value', self._rightDiff - 1 ).trigger( 'slide' );

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
				self._leftHandleRevisions = new wp.revisions.Collection();

				self._leftHandleRevisions.url =
					ajaxurl +
					'?action=revisions-data&compare_to=' + self._revisions.at( self._rightDiff - 1 ).get( 'ID' ) +
					'&post_id=' + wpRevisionsSettings.post_id +
					'&show_autosaves=' + REVAPP._autosaves +
					'&show_split_view=' +  REVAPP._showSplitView +
					'&nonce=' + wpRevisionsSettings.nonce +
					'&right_handle_at='  + ( self._rightDiff );

				self._leftHandleRevisions.fetch({

					success: function(){
						self.stopLeftModelLoading();
						self.reloadToLoadRevisions( self._leftHandleRevisions );
						self._tickmarkView.model = self._leftHandleRevisions;
						$( '#slider' ).slider( 'option', 'max', self._revisions.length );
						// ensure right handle not beyond length, in particular if viewing autosaves is switched from on to off
						// the number of models in the collection might get shorter, this ensures right handle is not beyond last model
						if ( self._rightDiff > self._revisions.length )
							self._rightDiff = self._revisions.length;
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
				self._rightHandleRevisions = new wp.revisions.Collection();

					self._rightHandleRevisions.url =
						ajaxurl +
						'?action=revisions-data&compare_to=' + ( self._revisions.at( self._leftDiff ).get( 'ID' ) - 1 )+
						'&post_id=' + wpRevisionsSettings.post_id +
						'&show_autosaves=' + REVAPP._autosaves +
						'&show_split_view=' +  REVAPP._showSplitView +
						'&nonce=' + wpRevisionsSettings.nonce;

				self._rightHandleRevisions.fetch({

					success: function(){
						self.stopRightModelLoading();
						self.reloadToLoadRevisions( self._rightHandleRevisions );
						self._tickmarkView.model = self._rightHandleRevisions;
						$( '#slider' ).slider( 'option', 'max', self._revisions.length );
						$( '#slider' ).slider( 'values', [ REVAPP._leftDiff, REVAPP._rightDiff] ).trigger( 'slide' );

						// REVAPP._revisionView.render();

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

			/*
			 * initialize the revision application
			 */
			initialize: function( options ) {
				var self = this; // store the application instance
				if (this._revisions === null) {
					self._revisions = new wp.revisions.Collection(); // set up collection
					self.startRightModelLoading();
					self._revisions.fetch({ // load revision data

						success: function() {
							self.stopRightModelLoading();
							// self._rightHandleRevisions = self._revisions;
							self.completeApplicationSetup();
						}
					});
				}
				return this;
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
/**/

			completeApplicationSetup: function() {
				this._revisionView = new wp.revisions.views.View({
					model: this._revisions
				});
				this._revisionView.render();
				$( '#slider' ).slider( 'option', 'max', this._revisions.length - 1 );

				this.reloadToLoadRevisions( this._revisions );

				this._revisionsInteractions = new wp.revisions.views.Interact({
					model: this._revisions
				});
				this._revisionsInteractions.render();

				this._tickmarkView = new wp.revisions.views.Tickmarks({
					model: this._revisions
				});
				this._tickmarkView.render();
				this._tickmarkView.resetTicks();


				/*
				.on( 'mouseup', function( event ) {
					REVAPP._keep_tooltip_open = false;
					$( this ).find('.ui-slider-tooltip').hide();
				} ).on( 'mousedown', function( event ) {
					REVAPP._keep_tooltip_open = true;
				} ).on( 'mouseout', function( event ) {
					if ( REVAPP._keep_tooltip_open)
						event.stopImmediatePropagation();
					});
				*/
				/*
				// Options hidden for now, moving to screen options
				this._revisionsOptions = new wp.revisions.views.Options({
					model: this._revisions
				});
				this._revisionsOptions.render();
				*/

			}
		})
	};

	wp.revisions.Collection = Backbone.Collection.extend({
		model: wp.revisions.Model,
		url: ajaxurl +	'?action=revisions-data&compare_to=' + wpRevisionsSettings.post_id +
			'&show_autosaves=true&show_split_view=true&nonce=' + wpRevisionsSettings.nonce,

		initialize: function() {
			}
	} );

	_.extend(wp.revisions.views, {

		// Ticks inside slider view
		Tickmarks: Backbone.View.extend({
			el: $('#diff-slider-ticks')[0],
			tagName: 'diff-slider-ticks-view',
			className: 'diff-slider-ticks-container',
			template: wp.template('revision-ticks'),
			model: wp.revisions.Model,

			resetTicks: function() {
				var sliderMax = $( '#slider' ).slider( 'option', 'max');
				var sliderWidth = $( '#slider' ).width();
				var adjustMax = ( 2 === REVAPP._compareOneOrTwo ) ? 1 : 0;
				var tickWidth = Math.floor( sliderWidth / ( sliderMax - adjustMax ) );

				// TODO: adjust right margins for wider ticks so they stay centered on handle stop point

				// set minimum and maximum widths for tick marks
				tickWidth = (tickWidth > 50 ) ? 50 : tickWidth;
				tickWidth = (tickWidth < 10 ) ? 10 : tickWidth;

				sliderWidth = tickWidth * (sliderMax - adjustMax ) + 1;

				$( '#slider' ).width( sliderWidth );
				$( '.diff-slider-ticks-wrapper' ).width( sliderWidth );
				$( '#diffslider' ).width( sliderWidth );
				$( '#diff-slider-ticks' ).width( sliderWidth );

				var aTickWidth = $( '.revision-tick' ).width();

				if ( tickWidth !==  aTickWidth ) { // is the width already set correctly?
					$( '.revision-tick' ).each( function( ) {
						$(this).css( 'margin-right', tickWidth - 1 + 'px'); // space the ticks out using right margin
					});

					if( 2 === REVAPP._compareOneOrTwo ) {
						$( '.revision-tick' ).first().remove(); // TODO - remove the check
					}
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
		}),

		// primary revision diff view
		View: Backbone.View.extend({
			el: $('#backbonerevisionsdiff')[0],
			tagName: 'revisionvview',
			className: 'revisionview-container',
			template: wp.template('revision'),
			comparetwochecked: '',
			draggingLeft: false,

			// render the revisions
			render: function() {
				var addHtml = '';
				// compare two revisions mode?

				if ( 2 === REVAPP._compareOneOrTwo ) {
					this.comparetwochecked = 'checked';
					if ( this.draggingLeft ) {
							if ( this.model.at( REVAPP._leftDiff ) ) {
							addHtml = this.template( _.extend(
								this.model.at( REVAPP._leftDiff ).toJSON(),
								{ comparetwochecked: this.comparetwochecked } // keep the checkmark checked
							) );
						}
					} else { // dragging right handle
						var thediff = REVAPP._rightDiff;
						if ( this.model.at( thediff ) ) {
							addHtml = this.template( _.extend(
								this.model.at( thediff ).toJSON(),
								{ comparetwochecked: this.comparetwochecked } // keep the checkmark checked
							) );
						}
					}
				} else { // end compare two revisions mode, eg only one slider handle
					this.comparetwochecked = '';
					if ( this.model.at( REVAPP._rightDiff - 1 ) ) {
						addHtml = this.template( _.extend(
							this.model.at( REVAPP._rightDiff - 1 ).toJSON(),
							{ comparetwochecked: this.comparetwochecked } // keep the checkmark unchecked
						) );
					}
				}
				this.$el.html( addHtml );
				if ( this.model.length < 3 ) {
					$( 'div#comparetworevisions' ).hide(); // don't allow compare two if fewer than three revisions
				}
				if ( this.model.length < 2 ) {
					$( 'div#diffslider' ).hide(); // don't allow compare two if fewer than three revisions
					$( 'div.diff-slider-ticks-wrapper' ).hide();
				}

				// add tooltips to the handles
				if ( 2 === REVAPP._compareOneOrTwo ) {
					REVAPP.addTooltip ( $( 'a.ui-slider-handle.left-handle' ),
						( REVAPP._leftDiff < 0 ) ? '' : REVAPP._revisions.at( REVAPP._leftDiff - 1 ).get( 'revision_date_author_short' ) );
					REVAPP.addTooltip ( $( 'a.ui-slider-handle.right-handle' ),
						( REVAPP._rightDiff > REVAPP._revisions.length ) ? '' : REVAPP._revisions.at( REVAPP._rightDiff - 1 ).get( 'revision_date_author_short' ) );
				} else {
					REVAPP.addTooltip ( $( 'a.ui-slider-handle' ),
						( REVAPP._rightDiff > REVAPP._revisions.length ) ? '' : REVAPP._revisions.at( REVAPP._rightDiff - 1 ).get( 'revision_date_author_short' ) );
				}

				// hide the restore button when on the last sport/current post data
				if (  REVAPP._rightDiff === REVAPP._revisions.length ){
					$( '.restore-button' ).hide();
				} else {
					$( '.restore-button' ).show();
				}

				return this;
			},

			// the compare two button is in this view, add the interaction here
			events: {
				'click #comparetwo': 'clickcomparetwo'
			},

			// turn on/off the compare two mmode
			clickcomparetwo: function(){
				self = this;

				if ( $( 'input#comparetwo' ).is( ':checked' ) ) { // compare 2 mode
					REVAPP._compareOneOrTwo = 2 ;

					if ( 1 === REVAPP._rightDiff )
						REVAPP._rightDiff = 2;
						REVAPP._revisionView.draggingLeft = false;

						wpRevisionsSettings.revision_id = ''; // reset passed revision id so switching back to one handle mode doesn't re-select revision
						REVAPP.reloadLeftRight();
						REVAPP._revisionView.model = REVAPP._rightHandleRevisions;

					} else { // compare one mode
						REVAPP._compareOneOrTwo = 1 ;
						REVAPP._revisionView.draggingLeft = false;
						// REVAPP._leftDiff = 0;
						// REVAPP._rightDiff = (REVAPP._revisions.length <= REVAPP._rightDiff ) ? REVAPP._rightDiff + 1 : REVAPP._rightDiff + 1;
						REVAPP.reloadModelSingle();
					}
					// REVAPP._revisionView.render();
					REVAPP._revisionsInteractions.render();
					REVAPP._tickmarkView.render();

			}
		}),

		// options view for show autosaves and show split view options
		/* DISABLED for now
		Options: Backbone.View.extend({
			el: $('#backbonerevisionsoptions')[0],
			tagName: 'revisionoptionsview',
			className: 'revisionoptions-container',
			template: wp.template('revisionoptions'),

			// render the options view
			render: function() {
				var addHtml = this.template;
				this.$el.html( addHtml );
				return this;
			},

			// add options interactions
			events: {
				'click #toggleshowautosaves': 'toggleshowautosaves',
				'click #showsplitview': 'showsplitview'
			},

			// toggle include autosaves
			toggleshowautosaves: function() {
				var self = this;
				if ( $( '#toggleshowautosaves' ).is( ':checked' ) ) {
					REVAPP._autosaves = true ;
				} else {
					REVAPP._autosaves = false ;
				}

				// refresh the model data
				REVAPP.reloadModel();
			},

			// toggle showing the split diff view
			showsplitview:  function() {
				var self = this;

				if ( $( 'input#showsplitview' ).is( ':checked' ) ) {
					REVAPP._showSplitView = 'true';
					$('.revisiondiffcontainer').addClass('diffsplit');
				} else {
					REVAPP._showSplitView = '';
					$('.revisiondiffcontainer').removeClass('diffsplit');
				}

				REVAPP.reloadModel();
			}
		}),
		*/
		// main interactions view
		Interact: Backbone.View.extend({
			el: $('#backbonerevisionsinteract')[0],
			tagName: 'revisionvinteract',
			className: 'revisionvinteract-container',
			template: wp.template('revisionvinteract'),

			initialize: function() {
			},

			render: function() {
				var self = this;

				var addHtml = this.template;
				this.$el.html( addHtml );

				var modelcount = REVAPP._revisions.length;

				slider = $( "#slider" );
				if ( 1 === REVAPP._compareOneOrTwo ) {
					// set up the slider with a single handle
					slider.slider({
						value: REVAPP._rightDiff - 1,
						min: 0,
						max: modelcount - 1,
						step: 1,


						// slide interactions for one handles slider
						slide: function( event, ui ) {

							REVAPP._rightDiff = ( ui.value + 1 );
							REVAPP._revisionView.render();
							/*
							$( 'a.ui-slider-handle' ).tooltip( {
								content: REVAPP._revisions.at( ui.value ).get( 'revision_date_author_short' ),
								position: {
								my: "top-65",
								using: function( position, feedback ) {
									$( this ).css( position );
									$( "<div>" )
									.addClass( "arrow" )
									.addClass( feedback.vertical )
									.addClass( feedback.horizontal )
									.appendTo( this );
									}
								}
							});// .trigger( 'close' ).trigger( 'open' );
*/
							}
					});
					$( '.revisiondiffcontainer' ).removeClass( 'comparetwo' );

				} else { // comparing more than one, eg 2
					// set up the slider with two handles
					slider.slider({
						values: [ REVAPP._leftDiff, REVAPP._rightDiff + 1 ],
						min: 1,
						max: modelcount + 1,
						step: 1,
						range: true,

						// in two handled mode when user starts dragging, swap in precalculated diff for handle
						start: function(event, ui ) {
							var index = $( ui.handle ).index(); // 0 (left) or 1 (right)
							switch ( index ) {
								case 1: // left handle drag
									if ( REVAPP._leftModelLoading ) // left model still loading, prevent sliding left handle
										return false;

									REVAPP._revisionView.draggingLeft = true;

									if ( REVAPP._revisionView.model !== REVAPP._leftHandleRevisions &&
											null !== REVAPP._leftHandleRevisions ) {
										REVAPP._revisionView.model = REVAPP._leftHandleRevisions;
										REVAPP._tickmarkView.model = REVAPP._leftHandleRevisions;
										REVAPP._tickmarkView.render();
									}

									REVAPP._leftDiffStart = ui.values[ 0 ];
									break;

								case 2: // right
									if ( REVAPP._rightModelLoading || 0 === REVAPP._rightHandleRevisions.length) // right model still loading, prevent sliding right handle
										return false;

									if ( REVAPP._revisionView.model !== REVAPP._rightHandleRevisions &&
											null !== REVAPP._rightHandleRevisions ) {
										REVAPP._revisionView.model = REVAPP._rightHandleRevisions;
										REVAPP._tickmarkView.model = REVAPP._rightHandleRevisions;
										REVAPP._tickmarkView.render();
									}

									REVAPP._revisionView.draggingLeft = false;
									REVAPP._rightDiffStart = ui.values[1];
									break;
							}
						},

						// when sliding in two handled mode change appropriate value
						slide: function( event, ui ) {
							if ( ui.values[0] === ui.values[1] ) // prevent compare to self
								return false;

							var index = $( ui.handle ).index(); // 0 (left) or 1 (right)

							switch ( index ) {
								case 1: // left
									if ( REVAPP._leftModelLoading ) // left model still loading, prevent sliding left handle
										return false;

									REVAPP._leftDiff = ui.values[0];
									break;

								case 2: // right
									if ( REVAPP._rightModelLoading ) // right model still loading, prevent sliding right handle
										return false;

									REVAPP._rightDiff = ui.values[1];
									break;
							}

							if ( 0 === REVAPP._leftDiff ) {
								$( '.revisiondiffcontainer' ).addClass( 'currentversion' );

							} else {
								$( '.revisiondiffcontainer' ).removeClass( 'currentversion' );
							}

							REVAPP._revisionView.render();

						},

						// when the user stops sliding  in 2 handle mode, recalculate diffs
						stop: function( event, ui ) {
							if ( 2 === REVAPP._compareOneOrTwo ) {
								// calculate and generate a diff for comparing to the left handle
								// and the right handle, swap out when dragging

								var index = $( ui.handle ).index(); // 0 (left) or 1 (right)

								switch ( index ) {
									case 1: // left

										// left handle dragged & changed, reload right handle model
										if ( REVAPP._leftDiffStart !== ui.values[0] )
											REVAPP.reloadRight();

										break;

									case 2: // right
										// REVAPP._rightDiff =  ( 1 >= REVAPP._rightDiff ) ? 1 : REVAPP._rightDiff - 1;
										// right handle dragged & changed, reload left handle model if changed
										if ( REVAPP._rightDiffStart !== ui.values[1] )
											REVAPP.reloadLeft();

										break;
								}
							}
						}
					});
					$( '.revisiondiffcontainer' ).addClass( 'comparetwo' );
					$( '#diffslider a.ui-slider-handle' ).first().addClass( 'left-handle' ).next().addClass( 'right-handle' );
				}

				return this;
			},

			// next and previous buttons, only available in compare one mode
			events: {
				'click #next': 'nextRevision',
				'click #previous': 'previousRevision'
			},

			// go to the next revision
			nextRevision: function() {
				if ( REVAPP._rightDiff < this.model.length ) // unless at right boundry
					REVAPP._rightDiff = REVAPP._rightDiff + 1 ;

				REVAPP._revisionView.render();

				$( '#slider' ).slider( 'value', REVAPP._rightDiff - 1 ).trigger( 'slide' );
			},

			// go the the previous revision
			previousRevision: function() {
				if ( REVAPP._rightDiff > 1 ) // unless at left boundry
					REVAPP._rightDiff = REVAPP._rightDiff - 1 ;

				REVAPP._revisionView.render();

				$( '#slider' ).slider( 'value', REVAPP._rightDiff - 1 ).trigger( 'slide' );
			}
		})
	});

	// instantiate Revision Application
	REVAPP = new wp.revisions.App();

}(jQuery));
