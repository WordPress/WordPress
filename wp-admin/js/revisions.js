window.wp = window.wp || {};

(function($) {
	wp.revisions = {

		views : {},

		Model : Backbone.Model.extend({
			idAttribute : 'ID',
			urlRoot : ajaxurl +	'?action=revisions-data&compare_to=' + wpRevisionsSettings.post_id +
				'&show_autosaves=false&show_split_view=true&nonce=' + wpRevisionsSettings.nonce,
			defaults: {
				ID : 0,
				revision_date_author : '',
				revisiondiff : '<div class="diff-loading"><div class="spinner"></div></div>',
				restoreaction : '',
				revision_from_date_author : '',
				revision_toload : false
			},

			url : function() {
				return this.urlRoot + '&single_revision_id=' + this.id;
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
			_left_diff : 0,
			_right_diff : 1,
			_autosaves : false,
			_show_split_view : true,
			_compareoneortwo : 1,
			_left_model_loading : false,	//keep track of model loads
			_right_model_loading : false,	//disallow slider interaction, also repeat loads, while loading

			//TODO add ability to arrive on specific revision
			routes : {
			},

			viewrevision : function( revision ) {
				//coming soon
			},

			reload_toload_revisions : function( model_collection, reverse_direction ) {
				var self = this;
				var revisions_to_load = model_collection.where( { revision_toload : true } );
				//console.log(revisions_to_load);
				var delay=0;
				_.each(revisions_to_load, function( the_model ) {
						the_model.urlRoot = model_collection.url;
						_.delay( function() {
							the_model.fetch( {
								update : true,
								add : false,
								remove : false,
								//async : false,
								success : function( model ) {
									//console.log(model.get( 'ID' ) +'-'+self._revisions.at( self._right_diff ).get( 'ID' ));
									if ( model.get( 'ID' ) === self._revisions.at( self._right_diff - 1 ).get( 'ID' ) ) { //reload if current model refreshed
										//console.log('render');
										self._revisionView.render();
									}
								}
						} );
						}, delay ) ;
						delay = delay + 200; //stagger model loads by 200 ms to avoid hammering server with requests
					}
				);
			},

			start_left_model_loading : function() {
				this._left_model_loading = true;
				$('.revisiondiffcontainer').addClass('leftmodelloading');
			},

			stop_left_model_loading : function() {
				this._left_model_loading = false;
				$('.revisiondiffcontainer').removeClass('leftmodelloading');
			},

			start_right_model_loading : function() {
				this._right_model_loading = true;
				$('.revisiondiffcontainer').addClass('rightmodelloading');
			},

			stop_right_model_loading : function() {
				this._right_model_loading = false;
				$('.revisiondiffcontainer').removeClass('rightmodelloading');
			},

			reloadmodel : function() {
				if ( 2 === this._compareoneortwo ) {
					this.reloadleftright();
				} else {
					this.reloadmodelsingle();
				}
			},

			reloadmodelsingle : function() {
				var self = this;
				self._revisions.url = ajaxurl +	'?action=revisions-data&compare_to=' + wpRevisionsSettings.post_id +
											'&show_autosaves=' + self._autosaves +
											'&show_split_view=' +  REVAPP._show_split_view +
											'&nonce=' + wpRevisionsSettings.nonce;
				self.start_right_model_loading();
				this._revisions.fetch({ //reload revision data
					success : function() {
						self.stop_right_model_loading();
						var revisioncount = self._revisions.length;
						if ( self._right_diff > revisioncount ) //if right handle past rightmost, move
							self._right_diff = revisioncount;

						self._revisionView.render();
						self.reload_toload_revisions( self._revisions );

						$( '#slider' ).slider( 'option', 'max', revisioncount-1 ); //TODO test this, autsaves changed
					},

					error : function () {
						self.stop_right_model_loading();
						//console.log( 'Error loading revision data' );
					}

				});
			},

			reloadleft : function() {
				var self = this;
				self.start_left_model_loading();
				self._left_handle_revisions = new wp.revisions.Collection();
				self._left_handle_revisions.url =
					ajaxurl +
					'?action=revisions-data&compare_to=' + self._revisions.at( self._right_diff - 1 ).get( 'ID' ) +
					'&post_id=' + wpRevisionsSettings.post_id +
					'&show_autosaves=' + self._autosaves +
					'&show_split_view=' +  self._show_split_view +
					'&nonce=' + wpRevisionsSettings.nonce +
					'&right_handle_at='  + ( self._right_diff );

				self._left_handle_revisions.fetch({

					success : function(){
						self.stop_left_model_loading();
						self.reload_toload_revisions( self._left_handle_revisions );
					},

					error : function () {
						//console.log( 'Error loading revision data' );
						self.stop_left_model_loading();
					}
				});
			},

			reloadright : function() {
				var self = this;
				self.start_right_model_loading();
				self._right_handle_revisions = new wp.revisions.Collection();
				if ( 0 === self._left_diff ) {
					self._right_handle_revisions.url =
						ajaxurl +
						'?action=revisions-data&compare_to=' + wpRevisionsSettings.post_id +
						'&post_id=' + wpRevisionsSettings.post_id +
						'&show_autosaves=' + self._autosaves +
						'&show_split_view=' +  self._show_split_view +
						'&nonce=' + wpRevisionsSettings.nonce;
				} else {
					self._right_handle_revisions.url =
						ajaxurl +
						'?action=revisions-data&compare_to=' + self._revisions.at( self._left_diff - 1 ).get( 'ID' ) +
						'&post_id=' + wpRevisionsSettings.post_id +
						'&show_autosaves=' + self._autosaves +
						'&show_split_view=' +  self._show_split_view +
						'&nonce=' + wpRevisionsSettings.nonce +
						'&left_handle_at=' + (self._left_diff ) ;
				}

				self._right_handle_revisions.fetch({

					success : function(){
						self.stop_right_model_loading();
						self.reload_toload_revisions( self._right_handle_revisions );
					},

					error : function ( response ) {
						//console.log( 'Error loading revision data - ' + response.toSource() );
						self.stop_right_model_loading();
					}
				});

			},

			reloadleftright : function() {
				this.reloadleft();
				this.reloadright();
			},

			/*
			 * initialize the revision application
			 */
			initialize : function( options ) {
				var self = this; //store the application instance
				if (this._revisions === null) {
					self._autosaves = '';
					self._revisions = new wp.revisions.Collection(); //set up collection
					self.start_right_model_loading();
					self._revisions.fetch({ //load revision data

						success : function() {
							self.stop_right_model_loading();
							self.revisionDiffSetup();
						}
					});
				}
				return this;
			},

			revisionDiffSetup : function() {
				this._revisionView = new wp.revisions.views.View({
					model : this._revisions
				});
				this._revisionView.render();
				$( '#diff_max, #diff_maxof' ).html( this._revisions.length );
				$( '#diff_count' ).html( REVAPP._right_diff );
				$( '#slider' ).slider( 'option', 'max', this._revisions.length - 1 );

				this.reload_toload_revisions( this._revisions );
				this._revisionsInteractions = new wp.revisions.views.Interact({
					model : this._revisions
				});
				this._revisionsInteractions.render();

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
			'&show_autosaves=false&show_split_view=true&nonce=' + wpRevisionsSettings.nonce,

		initialize : function() {
			}
	} );

	_.extend(wp.revisions.views, {
		//
		//primary revision diff view
		//
		View : Backbone.View.extend({
			el : $('#backbonerevisionsdiff')[0],
			tagName : 'revisionvview',
			className : 'revisionview-container',
			template : wp.template('revision'),
			revvapp : null,
			comparetwochecked : '',
			draggingleft : false,

			initialize : function(){
			},

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
				//console.log ( (this.model.at( REVAPP._right_diff - 1 )).url());
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
				if ( $( 'input#comparetwo' ).is( ':checked' ) ) {
					REVAPP._compareoneortwo = 2 ;
					REVAPP.reloadleftright();
				} else {
					REVAPP._compareoneortwo = 1 ;
					REVAPP._revisionView.draggingleft = false;
					REVAPP._left_diff = 0;
					REVAPP.reloadmodelsingle();
				}
				REVAPP._revisionsInteractions.render();
			}
		}),

		//
		//options view for show autosaves and show split view options
		//
		Options : Backbone.View.extend({
			el : $('#backbonerevisionsoptions')[0],
			tagName : 'revisionoptionsview',
			className : 'revisionoptions-container',
			template : wp.template('revisionoptions'),

			initialize : function() {
			},

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

		//
		//main interactions view
		//
		Interact : Backbone.View.extend({
			el : $('#backbonerevisionsinteract')[0],
			tagName : 'revisionvinteract',
			className : 'revisionvinteract-container',
			template : wp.template('revisionvinteract'),
			_restoreword : '',

			initialize : function() {
				this._restoreword = $( 'input#restore' ).attr( 'value' );
			},

			reset_restore_button : function() {
				$( 'input#restore' ).attr( 'value', this._restoreword + ' ' + REVAPP._revisions.at( REVAPP._right_diff - 1 ).get( 'ID' ) );
			},

			render : function() {
				var self = this;

				var addhtml = this.template;
				this.$el.html( addhtml );
				$( '#diff_max, #diff_maxof' ).html( this.model.length );
				$( '#diff_count' ).html( REVAPP._right_diff );
				$( '#diff_left_count_inner' ).html( 0 === REVAPP._left_diff ? '' : 'revision' + REVAPP._left_diff );
				self.reset_restore_button();

				var modelcount = REVAPP._revisions.length;

				slider = $( "#slider" );
				if ( 1 === REVAPP._compareoneortwo ) {
					//set up the slider with a single handle
					slider.slider({
						value : REVAPP._right_diff-1,
						min : 0,
						max : modelcount-1,
						step : 1,

						//slide interactions for one handles slider
						slide : function( event, ui ) {
							if ( REVAPP._right_model_loading ) //left model stoll loading, prevent sliding left handle
										return false;

							REVAPP._right_diff =( ui.value+1 );
							$( '#diff_count' ).html( REVAPP._right_diff );
							REVAPP._revisionView.render();
							self.reset_restore_button();
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
									if ( REVAPP._left_model_loading ) //left model stoll loading, prevent sliding left handle
										return false;

									if ( REVAPP._revisionView.model !== REVAPP._left_handle_revisions &&
											null !== REVAPP._left_handle_revisions )
										REVAPP._revisionView.model = REVAPP._left_handle_revisions;

									REVAPP._revisionView.draggingleft = true;
									REVAPP._left_diff_start = ui.values[ 0 ];
									break;

								case 2: //right
									if ( REVAPP._right_model_loading ) //right model stoll loading, prevent sliding right handle
										return false;

									//one extra spot at left end when comparing two
									if ( REVAPP._revisionView.model !== REVAPP._right_handle_revisions &&
											null !== REVAPP._right_handle_revisions )
										REVAPP._revisionView.model = REVAPP._right_handle_revisions;

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

									REVAPP._left_diff = ui.values[ 0 ] - 1; //one extra spot at left end when comparing two
									break;

								case 2: //right
									if ( REVAPP._right_model_loading ) //right model still loading, prevent sliding right handle
										return false;

									REVAPP._right_diff = ui.values[ 1 ] - 1 ;
									break;
							}

							$( '#diff_count' ).html( REVAPP._right_diff );

							if ( 0 === REVAPP._left_diff ) {
								$( '.revisiondiffcontainer' ).addClass( 'currentversion' );

							} else {
								$( '.revisiondiffcontainer' ).removeClass( 'currentversion' );
								$( '#diff_left_count_inner' ).html( REVAPP._left_diff );
							}

							REVAPP._revisionView.render(); //render the diff view
							self.reset_restore_button();
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
										if ( ! ( REVAPP._left_diff_start === ui.values[ 0 ] || REVAPP._left_model_loading ) )
											REVAPP.reloadright();

										break;

									case 2: //right
										//right handle dragged & changed, reload left handle model if changed
										if ( ! ( REVAPP._right_diff_start === ui.values[ 1 ] || REVAPP._right_model_loading ) ) {
											REVAPP.reloadleft();
										}
										break;
								}
							}
						}
					});
					$( '.revisiondiffcontainer' ).addClass( 'comparetwo' );
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

				$( '#diff_count' ).html( REVAPP._right_diff );
				$( '#slider' ).slider( 'value', REVAPP._right_diff - 1 ).trigger( 'slide' );
				this.reset_restore_button();
			},

			//go the the previous revision
			previousrevision : function() {
				if ( REVAPP._right_diff > 1 ) //unless at left boundry
						REVAPP._right_diff = REVAPP._right_diff - 1 ;

				REVAPP._revisionView.render();

				$( '#diff_count' ).html( REVAPP._right_diff );
				$( '#slider' ).slider( 'value', REVAPP._right_diff - 1 ).trigger( 'slide' );
				this.reset_restore_button();
			}
		})
	});

	//instantiate Revision Application
	REVAPP = new wp.revisions.App();
	//TODO consider enable back button to step back thru states?
	//Backbone.history.start({pushState: true});

}(jQuery));
