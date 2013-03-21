window.wp = window.wp || {};

(function($) {
	wp.revisions = {

		views : {},

		Model : Backbone.Model.extend({
			idAttribute : 'ID',
			urlRoot : ajaxurl +	'?action=revisions-data' +
				'&show_autosaves=true&show_split_view=true&nonce=' + wpRevisionsSettings.nonce,
			defaults: {
				ID : 0,
				revision_date_author : '',
				revision_date_author_short: '',
				revisiondiff : '<div class="diff-loading"><div class="spinner"></div></div>',
				restoreaction : '',
				revision_from_date_author : '',
				revision_toload : false,
				lines_added : 0,
				lines_deleted : 0,
				scope_of_changes : 'none',
				previous_revision_id : 0
			},

			url : function() {
				if ( 1 === REVAPP._compareoneortwo ) {
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

		App : Backbone.Router.extend({
			_revisionDifflView : null,
			_revisions : null,
			_left_handle_revisions : null,
			_right_handle_revisions : null,
			_revisionsInteractions : null,
			_revisionsOptions : null,
			_left_diff : 1,
			_right_diff : 1,
			_autosaves : true,
			_show_split_view : true,
			_compareoneortwo : 1,
			_left_model_loading : false,	//keep track of model loads
			_right_model_loading : false,	//disallow slider interaction, also repeat loads, while loading
			_tickmarkView : null, //the slider tickmarks
			_has_tooltip : false,

			routes : {
			},

			reload_toload_revisions : function( model_collection, reverse_direction ) {
				var self = this;
				var revisions_to_load = model_collection.where( { revision_toload : true } );
				var delay=0;
				//match slider to passed revision_id
				_.each( revisions_to_load, function( the_model ) {
					if ( the_model.get( 'ID' )  == wpRevisionsSettings.revision_id ) {
						self._right_diff = self._revisions.indexOf( the_model ) + 1;
					}

				});
				_.each( revisions_to_load, function( the_model ) {
						the_model.urlRoot = model_collection.url;
						_.delay( function() {
							the_model.fetch( {
								update : true,
								add : false,
								remove : false,
								success : function( model ) {
									model.set( 'revision_toload', 'false' );

									//stop spinner when all models are loaded
									if ( 0 === model_collection.where( { revision_toload : true } ).length )
										self.stop_model_loading_spinner();

									self._tickmarkView.render();

									var total_changes = model.get( 'lines_added' ) + model.get( 'lines_deleted');
									var scope_of_changes = 'vsmall';

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
									if ( 0 !== self._right_diff &&
										model.get( 'ID' ) === self._revisions.at( self._right_diff - 1 ).get( 'ID' ) ) {
										//reload if current model refreshed
										self._revisionView.render();
									}

								}
						} );
						}, delay ) ;
						delay = delay + 150; //stagger model loads to avoid hammering server with requests
					}
				);
			},

			start_left_model_loading : function() {
				this._left_model_loading = true;
				$('.revisiondiffcontainer').addClass('leftmodelloading');
			},

			stop_left_model_loading : function() {
				this._left_model_loading = false;
			},

			start_right_model_loading : function() {
				this._right_model_loading = true;
				$('.revisiondiffcontainer').addClass('rightmodelloading');
			},

			stop_right_model_loading : function() {
				this._right_model_loading = false;
			},

			stop_model_loading_spinner : function() {
				$('.revisiondiffcontainer').removeClass('rightmodelloading');
				$('.revisiondiffcontainer').removeClass('leftmodelloading');
			},

			reloadmodel : function() {
				if ( 2 === this._compareoneortwo ) {
					this.reloadleftright();
				} else {
					this.reloadmodelsingle();
				}
			},

			//load the models for the single handle mode
			reloadmodelsingle : function() {
				var self = this;
				self._revisions.url = ajaxurl +	'?action=revisions-data&compare_to=' + wpRevisionsSettings.post_id +
											'&show_autosaves=' + self._autosaves +
											'&show_split_view=' +  self._show_split_view +
											'&nonce=' + wpRevisionsSettings.nonce;
				self.start_right_model_loading();
				self._revisions.fetch({ //reload revision data
					success : function() {
						console.log('loaded');
						//self.stop_right_model_loading();
						//REVAPP._right_diff -= 1;
						var revisioncount = self._revisions.length;
						self._revisionView.model = self._revisions;
						self._revisionView.render();
						self.reload_toload_revisions( self._revisions );
						self._tickmarkView.model = self._revisions;
						self._tickmarkView.render();
						$( '#slider' ).slider( 'option', 'max', revisioncount-1 ); //TODO test this, if autsave option changed
						$( '#slider' ).slider( 'value', self._right_diff - 1 ).trigger( 'slide' );

					},

					error : function () {
						self.stop_right_model_loading();
					}

				});
			},

			//load the models for the left handle
			reloadleft : function() {
				var self = this;
				self.start_left_model_loading();
				self._left_handle_revisions = new wp.revisions.Collection();

				self._left_handle_revisions.url =
					ajaxurl +
					'?action=revisions-data&compare_to=' + self._revisions.at( self._right_diff - 1 ).get( 'ID' ) +
					'&post_id=' + wpRevisionsSettings.post_id +
					'&show_autosaves=' + REVAPP._autosaves +
					'&show_split_view=' +  REVAPP._show_split_view +
					'&nonce=' + wpRevisionsSettings.nonce +
					'&right_handle_at='  + ( self._right_diff );

				self._left_handle_revisions.fetch({

					success : function(){
						self.stop_left_model_loading();
						self.reload_toload_revisions( self._left_handle_revisions );
						self._tickmarkView.model = self._left_handle_revisions;
						$( '#slider' ).slider( 'option', 'max', self._revisions.length );
						// ensure right handle not beyond length, in particular if viewing autosaves is switched from on to off
						// the number of models in the collection might get shorter, this ensures right handle is not beyond last model
						if ( self._right_diff > self._revisions.length )
							self._right_diff = self._revisions.length;
						},

					error : function () {
						self.stop_left_model_loading();
					}
				});
			},

			//load the models for the right handle
			reloadright : function() {
				var self = this;
				self.start_right_model_loading();
				self._right_handle_revisions = new wp.revisions.Collection();

					self._right_handle_revisions.url =
						ajaxurl +
						'?action=revisions-data&compare_to=' + ( self._revisions.at( self._left_diff ).get( 'ID' ) -1)+
						'&post_id=' + wpRevisionsSettings.post_id +
						'&show_autosaves=' + REVAPP._autosaves +
						'&show_split_view=' +  REVAPP._show_split_view +
						'&nonce=' + wpRevisionsSettings.nonce;

				self._right_handle_revisions.fetch({

					success : function(){
						self.stop_right_model_loading();
						self.reload_toload_revisions( self._right_handle_revisions );
						self._tickmarkView.model = self._right_handle_revisions;
						$( '#slider' ).slider( 'option', 'max', self._revisions.length );
						$( '#slider' ).slider( 'values', [ REVAPP._left_diff, REVAPP._right_diff] ).trigger( 'slide' );

						//REVAPP._revisionView.render();

					},

					error : function ( response ) {
						self.stop_right_model_loading();
					}
				});

			},

			reloadleftright : function() {
				this.start_right_model_loading();
				this.start_left_model_loading();
				this.reloadleft();
				this.reloadright();
			},

			/*
			 * initialize the revision application
			 */
			initialize : function( options ) {
				var self = this; //store the application instance
				if (this._revisions === null) {
					self._revisions = new wp.revisions.Collection(); //set up collection
					self.start_right_model_loading();
					self._revisions.fetch({ //load revision data

						success : function() {
							self.stop_right_model_loading();
							//self._right_handle_revisions = self._revisions;
							self.completeApplicationSetup();
						}
					});
				}
				return this;
			},

			addTooltip : function( handle, message ) {

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

			completeApplicationSetup : function() {
				this._revisionView = new wp.revisions.views.View({
					model : this._revisions
				});
				this._revisionView.render();
				$( '#slider' ).slider( 'option', 'max', this._revisions.length - 1 );

				this.reload_toload_revisions( this._revisions );

				this._revisionsInteractions = new wp.revisions.views.Interact({
					model : this._revisions
				});
				this._revisionsInteractions.render();

				this._tickmarkView = new wp.revisions.views.Tickmarks({
					model : this._revisions
				});
				this._tickmarkView.render();
				this._tickmarkView.resetticks();


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
				//Options hidden for now, moving to screen options
				this._revisionsOptions = new wp.revisions.views.Options({
					model : this._revisions
				});
				this._revisionsOptions.render();
				*/

			}
		})
	};

	wp.revisions.Collection = Backbone.Collection.extend({
		model : wp.revisions.Model,
		url : ajaxurl +	'?action=revisions-data&compare_to=' + wpRevisionsSettings.post_id +
			'&show_autosaves=true&show_split_view=true&nonce=' + wpRevisionsSettings.nonce,

		initialize : function() {
			}
	} );

	_.extend(wp.revisions.views, {

		//Ticks inside slider view
		//
		Tickmarks : Backbone.View.extend({
			el : $('#diff-slider-ticks')[0],
			tagName : 'diff-slider-ticks-view',
			className : 'diff-slider-ticks-container',
			template : wp.template('revision-ticks'),
			model : wp.revisions.Model,

			resetticks : function() {
				var slider_max = $( '#slider' ).slider( 'option', 'max');
				var slider_width = $( '#slider' ).width();
				var adjust_max = ( 2 === REVAPP._compareoneortwo ) ? 1 : 0;
				var tick_width = Math.floor( slider_width / ( slider_max - adjust_max ) );

				//TODO: adjust right margins for wider ticks so they stay centered on handle stop point

				//set minimum and maximum widths for tick marks
				tick_width = (tick_width > 50 ) ? 50 : tick_width;
				tick_width = (tick_width < 10 ) ? 10 : tick_width;

				slider_width = tick_width * (slider_max - adjust_max ) +1;

				$( '#slider' ).width( slider_width );
				$( '.diff-slider-ticks-wrapper' ).width( slider_width );
				$( '#diffslider' ).width( slider_width );
				$( '#diff-slider-ticks' ).width( slider_width );

				var a_tick_width = $( '.revision-tick' ).width();

				if ( tick_width !==  a_tick_width ) { // is the width already set correctly?
					$( '.revision-tick' ).each( function( ) {
						$(this).css( 'margin-right', tick_width - 1 + 'px'); //space the ticks out using right margin
					});

					if( 2 === REVAPP._compareoneortwo ) {
						$( '.revision-tick' ).first().remove(); //TODO - remove the check
					}
					$( '.revision-tick' ).last().css( 'margin-right', '0' ); // last tick gets no right margin
				}

			},

			//render the tickmark view
			render : function() {
				var self = this;

				if ( null !== self.model ) {
					var addhtml = "";
					_.each ( self.model.models, function ( the_model ) {
						addhtml = addhtml + self.template ( the_model.toJSON() );
					});
					self.$el.html( addhtml );

				}
				self.resetticks();
				return self;
			}
		}),

		//
		//primary revision diff view
		//
		View : Backbone.View.extend({
			el : $('#backbonerevisionsdiff')[0],
			tagName : 'revisionvview',
			className : 'revisionview-container',
			template : wp.template('revision'),
			comparetwochecked : '',
			draggingleft : false,

			//
			//render the revisions
			//
			render : function() {
				var addhtml = '';
				//compare two revisions mode?

				if ( 2 === REVAPP._compareoneortwo ) {
					this.comparetwochecked = 'checked';
					if ( this.draggingleft ) {
							if ( this.model.at( REVAPP._left_diff ) ) {
							addhtml = this.template( _.extend(
								this.model.at( REVAPP._left_diff ).toJSON(),
								{ comparetwochecked : this.comparetwochecked } //keep the checkmark checked
							) );
						}
					} else { //dragging right handle
						var thediff = REVAPP._right_diff;
						if ( this.model.at( thediff ) ) {
							addhtml = this.template( _.extend(
								this.model.at( thediff ).toJSON(),
								{ comparetwochecked : this.comparetwochecked } //keep the checkmark checked
							) );
						}
					}
				} else { //end compare two revisions mode, eg only one slider handle
					this.comparetwochecked = '';
					if ( this.model.at( REVAPP._right_diff - 1 ) ) {
						addhtml = this.template( _.extend(
							this.model.at( REVAPP._right_diff - 1 ).toJSON(),
							{ comparetwochecked : this.comparetwochecked } //keep the checkmark unchecked
						) );
					}
				}
				this.$el.html( addhtml );
				if ( this.model.length < 3 ) {
					$( 'div#comparetworevisions' ).hide(); //don't allow compare two if fewer than three revisions
				}
				if ( this.model.length < 2 ) {
					$( 'div#diffslider' ).hide(); //don't allow compare two if fewer than three revisions
					$( 'div.diff-slider-ticks-wrapper' ).hide();
				}

				//
				// add tooltips to the handles
				//
				if ( 2 === REVAPP._compareoneortwo ) {
					REVAPP.addTooltip ( $( 'a.ui-slider-handle.left-handle' ),
						( REVAPP._right_diff >= REVAPP._revisions.length ) ? '' : REVAPP._revisions.at( REVAPP._left_diff ).get( 'revision_date_author_short' ) );
					REVAPP.addTooltip ( $( 'a.ui-slider-handle.right-handle' ),
						( REVAPP._right_diff >= REVAPP._revisions.length ) ? '' : REVAPP._revisions.at( REVAPP._right_diff ).get( 'revision_date_author_short' ) );
				} else {
					REVAPP.addTooltip ( $( 'a.ui-slider-handle' ),
						( REVAPP._right_diff >= REVAPP._revisions.length ) ? '' : REVAPP._revisions.at( REVAPP._right_diff ).get( 'revision_date_author_short' ) );
				}

				//
				// hide the restore button when on the last sport/current post data
				//
				if (  REVAPP._right_diff === REVAPP._revisions.length ){
					$( '.restore-button' ).hide();
				} else {
					$( '.restore-button' ).show();
				}

				return this;
			},

			//the compare two button is in this view, add the interaction here
			events : {
				'click #comparetwo' : 'clickcomparetwo'
			},

			//
			//turn on/off the compare two mmode
			//
			clickcomparetwo : function(){
				self = this;

				if ( $( 'input#comparetwo' ).is( ':checked' ) ) { //compare 2 mode
					REVAPP._compareoneortwo = 2 ;

					if ( 1 === REVAPP._right_diff )
						REVAPP._right_diff = 2;
						REVAPP._revisionView.draggingleft = false;

						wpRevisionsSettings.revision_id = ''; // reset passed revision id so switching back to one handle mode doesn't re-select revision
						REVAPP.reloadleftright();
						REVAPP._revisionView.model = REVAPP._right_handle_revisions;

					} else { //compare one mode
						REVAPP._compareoneortwo = 1 ;
						REVAPP._revisionView.draggingleft = false;
						//REVAPP._left_diff = 0;
						//REVAPP._right_diff = (REVAPP._revisions.length <= REVAPP._right_diff ) ? REVAPP._right_diff + 1 : REVAPP._right_diff + 1;
						REVAPP.reloadmodelsingle();
					}
					//REVAPP._revisionView.render();
					REVAPP._revisionsInteractions.render();
					REVAPP._tickmarkView.render();

			}
		}),

		//
		//options view for show autosaves and show split view options
		//
		/* DISABLED for now
		Options : Backbone.View.extend({
			el : $('#backbonerevisionsoptions')[0],
			tagName : 'revisionoptionsview',
			className : 'revisionoptions-container',
			template : wp.template('revisionoptions'),

			//render the options view
			render : function() {
				var addhtml = this.template;
				this.$el.html( addhtml );
				return this;
			},

			//add options interactions
			events : {
				'click #toggleshowautosaves' : 'toggleshowautosaves',
				'click #showsplitview' : 'showsplitview'
			},

			//
			//toggle include autosaves
			//
			toggleshowautosaves : function() {
				var self = this;
				if ( $( '#toggleshowautosaves' ).is( ':checked' ) ) {
					REVAPP._autosaves = true ;
				} else {
					REVAPP._autosaves = false ;
				}

				//refresh the model data
				REVAPP.reloadmodel();
			},

			//
			//toggle showing the split diff view
			//
			showsplitview :  function() {
				var self = this;

				if ( $( 'input#showsplitview' ).is( ':checked' ) ) {
					REVAPP._show_split_view = 'true';
					$('.revisiondiffcontainer').addClass('diffsplit');
				} else {
					REVAPP._show_split_view = '';
					$('.revisiondiffcontainer').removeClass('diffsplit');
				}

				REVAPP.reloadmodel();
			}
		}),
		*/
		//
		//main interactions view
		//
		Interact : Backbone.View.extend({
			el : $('#backbonerevisionsinteract')[0],
			tagName : 'revisionvinteract',
			className : 'revisionvinteract-container',
			template : wp.template('revisionvinteract'),

			initialize : function() {
			},

			render : function() {
				var self = this;

				var addhtml = this.template;
				this.$el.html( addhtml );

				var modelcount = REVAPP._revisions.length;

				slider = $( "#slider" );
				if ( 1 === REVAPP._compareoneortwo ) {
					//set up the slider with a single handle
					slider.slider({
						value: REVAPP._right_diff-1,
						min: 0,
						max: modelcount-1,
						step: 1,


						//slide interactions for one handles slider
						slide : function( event, ui ) {

							REVAPP._right_diff = ( ui.value + 1 );
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
							});//.trigger( 'close' ).trigger( 'open' );
*/
							}
					});
					$( '.revisiondiffcontainer' ).removeClass( 'comparetwo' );

				} else { //comparing more than one, eg 2
					//set up the slider with two handles
					slider.slider({
						values : [ REVAPP._left_diff, REVAPP._right_diff + 1 ],
						min : 1,
						max : modelcount+1,
						step : 1,
						range: true,

						//in two handled mode when user starts dragging, swap in precalculated diff for handle
						start : function (event, ui ) {
							var index = $( ui.handle ).index(); //0 (left) or 1 (right)
							switch ( index ) {
								case 1: //left handle drag
									if ( REVAPP._left_model_loading ) //left model still loading, prevent sliding left handle
										return false;

									REVAPP._revisionView.draggingleft = true;

									if ( REVAPP._revisionView.model !== REVAPP._left_handle_revisions &&
											null !== REVAPP._left_handle_revisions ) {
										REVAPP._revisionView.model = REVAPP._left_handle_revisions;
										REVAPP._tickmarkView.model = REVAPP._left_handle_revisions;
										REVAPP._tickmarkView.render();
									}

									REVAPP._left_diff_start = ui.values[ 0 ];
									break;

								case 2: //right
									if ( REVAPP._right_model_loading || 0 === REVAPP._right_handle_revisions.length) //right model still loading, prevent sliding right handle
										return false;

									if ( REVAPP._revisionView.model !== REVAPP._right_handle_revisions &&
											null !== REVAPP._right_handle_revisions ) {
										REVAPP._revisionView.model = REVAPP._right_handle_revisions;
										REVAPP._tickmarkView.model = REVAPP._right_handle_revisions;
										REVAPP._tickmarkView.render();
									}

									REVAPP._revisionView.draggingleft = false;
									REVAPP._right_diff_start = ui.values[ 1 ];
									break;
							}
						},

						//when sliding in two handled mode change appropriate value
						slide : function( event, ui ) {
							if ( ui.values[ 0 ] === ui.values[ 1 ] ) //prevent compare to self
								return false;

							var index = $( ui.handle ).index(); //0 (left) or 1 (right)

							switch ( index ) {
								case 1: //left
									if ( REVAPP._left_model_loading ) //left model still loading, prevent sliding left handle
										return false;

									REVAPP._left_diff = ui.values[ 0 ];
									break;

								case 2: //right
									if ( REVAPP._right_model_loading ) //right model still loading, prevent sliding right handle
										return false;

									REVAPP._right_diff = ui.values[ 1 ];
									break;
							}

							if ( 0 === REVAPP._left_diff ) {
								$( '.revisiondiffcontainer' ).addClass( 'currentversion' );

							} else {
								$( '.revisiondiffcontainer' ).removeClass( 'currentversion' );
							}

							REVAPP._revisionView.render();

						},

						//when the user stops sliding  in 2 handle mode, recalculate diffs
						stop : function( event, ui ) {
							if ( 2 === REVAPP._compareoneortwo ) {
								//calculate and generate a diff for comparing to the left handle
								//and the right handle, swap out when dragging

								var index = $( ui.handle ).index(); //0 (left) or 1 (right)

								switch ( index ) {
									case 1: //left

										//left handle dragged & changed, reload right handle model
										if ( REVAPP._left_diff_start !== ui.values[ 0 ] )
											REVAPP.reloadright();

										break;

									case 2: //right
										//REVAPP._right_diff =  ( 1 >= REVAPP._right_diff ) ? 1  : REVAPP._right_diff-1;
										//right handle dragged & changed, reload left handle model if changed
										if ( REVAPP._right_diff_start !== ui.values[ 1 ] )
											REVAPP.reloadleft();

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

			//next and previous buttons, only available in compare one mode
			events : {
				'click #next' : 'nextrevision',
				'click #previous' : 'previousrevision'
			},

			//go to the next revision
			nextrevision : function() {
				if ( REVAPP._right_diff < this.model.length ) //unless at right boundry
					REVAPP._right_diff = REVAPP._right_diff + 1 ;

				REVAPP._revisionView.render();

				$( '#slider' ).slider( 'value', REVAPP._right_diff - 1 ).trigger( 'slide' );
			},

			//go the the previous revision
			previousrevision : function() {
				if ( REVAPP._right_diff > 1 ) //unless at left boundry
						REVAPP._right_diff = REVAPP._right_diff - 1 ;

				REVAPP._revisionView.render();

				$( '#slider' ).slider( 'value', REVAPP._right_diff - 1 ).trigger( 'slide' );
			}
		})
	});

	//instantiate Revision Application
	REVAPP = new wp.revisions.App();

}(jQuery));
