window.wp = window.wp || {};

(function($) {
	wp.revisions = {

		views : {},

		Model : Backbone.Model.extend({
			defaults: {
				ID : 0,
				revision_date_author : '',
				revisiondiff : '',
				restoreaction: '',
				diff_max : 0,
				diff_count : 0,
				diff_revision_to : 0,
				revision_from_date_author : '',
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
			_showsplitview : true,
			_compareoneortwo : 1,
			left_model_loading : false,		//keep track of model loads
			right_model_loading : false,	//disallow slider interaction, also repeat loads, while loading

			//TODO add ability to arrive on specific revision
			routes : {
				"viewrevision/:revision": "viewrevision",
			},

			viewrevision : function( revision ) {
				//coming soon
			},

			start_left_model_loading : function() {
				this.left_model_loading = true;
				$('.revisiondiffcontainer').addClass('leftmodelloading');
			},

			stop_left_model_loading : function() {
				this.left_model_loading = false;
				$('.revisiondiffcontainer').removeClass('leftmodelloading');
			},

			start_right_model_loading : function() {
				this.right_model_loading = true;
				$('.revisiondiffcontainer').addClass('rightmodelloading');
			},

			stop_right_model_loading : function() {
				this.right_model_loading = false;
				$('.revisiondiffcontainer').removeClass('rightmodelloading');
			},

			reloadmodel : function() {
				if ( 2 == this._compareoneortwo ) {
					this.reloadleftright();
				} else {
					this.reloadmodelsingle();
				}
			},

			reloadmodelsingle : function() {
				var self = this;
				self._revisions.url = ajaxurl +	'?action=revisions-data&compareto=' + wpRevisionsSettings.post_id +
											'&showautosaves=' + self.self_autosaves +
											'&showsplitview=' +  REVAPP._showsplitview +
											'&nonce=' + wpRevisionsSettings.nonce;
				self.start_right_model_loading();
				this._revisions.fetch({ //reload revision data
					success : function() {
						self.stop_right_model_loading();
						var revisioncount = self._revisions.length;
						if ( self._right_diff > revisioncount ) //if right handle past rightmost, move
							self._right_diff = revisioncount;
						//TODO add a test for matchind left revision and push left, testing
						//also reset the slider values here

						self._revisionView.render();
						$( '#slider' ).slider( 'option', 'max', revisioncount-1 ); //TODO test this
					},

					error : function () {
						self.stop_right_model_loading();
						window.console && console.log( 'Error loading revision data' );
					}

				});
			},

			reloadleftright : function() {
				var self = this;
				self.start_left_model_loading();
				self.start_right_model_loading();

				self._left_handle_revisions = new wp.revisions.Collection();
				self._right_handle_revisions = new wp.revisions.Collection();

				if ( 0 == self._left_diff ) {
					self._right_handle_revisions.url =
						ajaxurl +
						'?action=revisions-data&compareto=' + wpRevisionsSettings.post_id +
						'&wpRevisionsSettings.post_id=' + wpRevisionsSettings.post_id +
						'&showautosaves=' + self._autosaves +
						'&showsplitview=' +  self._showsplitview +
						'&nonce=' + wpRevisionsSettings.nonce;
				} else {
					self._right_handle_revisions.url =
						ajaxurl +
						'?action=revisions-data&compareto=' + self._revisions.at( self._left_diff - 1 ).get( 'ID' ) +
						'&wpRevisionsSettings.post_id=' + wpRevisionsSettings.post_id +
						'&showautosaves=' + self._autosaves +
						'&showsplitview=' +  self._showsplitview +
						'&nonce=' + wpRevisionsSettings.nonce;
				}

				self._left_handle_revisions.url =
					ajaxurl +
					'?action=revisions-data&compareto=' + self._revisions.at( self._right_diff - 1 ).get( 'ID' ) +
					'&wpRevisionsSettings.post_id=' + wpRevisionsSettings.post_id +
					'&showautosaves=' + self._autosaves +
					'&showsplitview=' +  self._showsplitview +
					'&nonce=' + wpRevisionsSettings.nonce;

				self._left_handle_revisions.fetch({

					xhr: function() {
						var xhr = $.ajaxSettings.xhr();
						xhr.onprogress = self.handleProgress;
						return xhr;
					},

					handleProgress: function(evt){
						var percentComplete = 0;
						if (evt.lengthComputable) {
							percentComplete = evt.loaded / evt.total;
							window.console && console.log( Math.round( percentComplete * 100) + "%" );
						}
					},

					success : function(){
						self.stop_left_model_loading();
					},

					error : function () {
						window.console && console.log( 'Error loading revision data' );
						self.stop_left_model_loading();
					}
				});

				self._right_handle_revisions.fetch({
					
					success : function(){
						self.stop_right_model_loading();
					},

					error : function () {
						window.console && console.log( 'Error loading revision data' );
						self.stop_right_model_loading();
					}
				});
			},

			/*
			 * initialize the revision appl;ication
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
				var self = this, slider;

				this._revisionView = new wp.revisions.views.View({
					model : this._revisions
				});
				this._revisionView.render();

				this._revisionsInteractions = new wp.revisions.views.Interact({
					model : this._revisions
				});
				this._revisionsInteractions.render();

				this._revisionsOptions = new wp.revisions.views.Options({
					model : this._revisions
				});
				this._revisionsOptions.render();

			}
		})
	};

	wp.revisions.Collection = Backbone.Collection.extend({
		model : wp.revisions.Model,
		url : ajaxurl +	'?action=revisions-data&compareto=' + wpRevisionsSettings.post_id + '&showautosaves=false&showsplitview=true&nonce=' + wpRevisionsSettings.nonce
	});

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
				if ( 2 == REVAPP._compareoneortwo ) {
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
				} else { //end compare two revisions mode, eg only one slider handel
					this.comparetwochecked = '';
					if ( this.model.at( REVAPP._right_diff - 1 ) ) {
						addhtml = this.template( _.extend(
							this.model.at( REVAPP._right_diff-1 ).toJSON(),
							{ comparetwochecked : this.comparetwochecked } //keep the checkmark checked
						) );
					}
				}
				this.$el.html( addhtml );
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
				//TODO check for two handle mode
				
			},

			//
			//toggle showing the split diff view
			//
			showsplitview :  function() {
				var self = this;

				if ( $( 'input#showsplitview' ).is( ':checked' ) ) {
					REVAPP._showsplitview = 'true';
					$('.revisiondiffcontainer').addClass('diffsplit');
				} else {
					REVAPP._showsplitview = '';
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

			initialize : function() {
			},

			render : function() {
				var self = this;

				var addhtml = this.template;
				this.$el.html( addhtml );
				$( '#diff_max, #diff_maxof' ).html( this.model.length );
				$( '#diff_count' ).html( REVAPP._right_diff );
				$( '#diff_left_count_inner' ).html( 0 == REVAPP._left_diff ? '' : 'revision' + REVAPP._left_diff );

				var modelcount = REVAPP._revisions.length;

				slider = $("#slider");
				if ( 1 == REVAPP._compareoneortwo ) {
					//set up the slider with a single handle
					slider.slider({
						value : REVAPP._right_diff-1,
						min : 0,
						max : modelcount-1,
						step : 1,

						//slide interactions for one handles slider
						slide : function( event, ui ) {
							if ( REVAPP.right_model_loading ) //left model stoll loading, prevent sliding left handle
										return false;

							REVAPP._right_diff =( ui.value+1 );
							$( '#diff_count' ).html( REVAPP._right_diff );
							REVAPP._revisionView.render();
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
									if ( REVAPP.left_model_loading ) //left model stoll loading, prevent sliding left handle
										return false;

									if ( REVAPP._revisionView.model !== REVAPP._left_handle_revisions &&
											null != REVAPP._left_handle_revisions )
										REVAPP._revisionView.model = REVAPP._left_handle_revisions;

									REVAPP._revisionView.draggingleft = true;
									break;

								case 2: //right
									if ( REVAPP.right_model_loading ) //right model stoll loading, prevent sliding right handle
										return false;

									//one extra spot at left end when comparing two
									if ( REVAPP._revisionView.model !== REVAPP._right_handle_revisions &&
											null != REVAPP._right_handle_revisions )
										REVAPP._revisionView.model = REVAPP._right_handle_revisions;

									REVAPP._revisionView.draggingleft = false;
									REVAPP._right_diff = ui.values[1] - 1 ;
									break;
							}
						},

						//when sliding in two handled mode change appropriate value
						slide : function( event, ui ) {
							if ( ui.values[0] == ui.values[1] ) //prevent compare to self
								return false;

							var index = $( ui.handle ).index(); //0 (left) or 1 (right)

							switch ( index ) {
								case 1: //left
									if ( REVAPP.left_model_loading ) //left model stoll loading, prevent sliding left handle
										return false;

									REVAPP._left_diff = ui.values[0] - 1; //one extra spot at left end when comparing two
									break;

								case 2: //right
									if ( REVAPP.right_model_loading ) //right model stoll loading, prevent sliding right handle
										return false;

									REVAPP._right_diff = ui.values[1] - 1 ;
									break;
							}

							$( '#diff_count' ).html( REVAPP._right_diff );

							if ( 0 == REVAPP._left_diff ) {
								$( '.revisiondiffcontainer' ).addClass( 'currentversion' );

							} else {
								$( '.revisiondiffcontainer' ).removeClass( 'currentversion' );
								$( '#diff_left_count_inner' ).html( REVAPP._left_diff );
							}

							REVAPP._revisionView.render(); //render the diff view
						},

						//when the user stops sliding  in 2 handle mode, recalculate diffs
						stop : function( event, ui ) {
							if ( 2 == REVAPP._compareoneortwo ) {
								//calculate and generate a diff for comparing to the left handle
								//and the right handle, swap out when dragging
								if ( ! (REVAPP.left_model_loading && REVAPP.right_model.loading ) ) {
									REVAPP.reloadleftright();
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
			},

			//go the the previous revision
			previousrevision : function() {
				if ( REVAPP._right_diff > 1 ) //unless at left boundry
						REVAPP._right_diff = REVAPP._right_diff - 1 ;

				REVAPP._revisionView.render();

				$( '#diff_count' ).html( REVAPP._right_diff );
				$( '#slider' ).slider( 'value', REVAPP._right_diff - 1 ).trigger( 'slide' );
			}
		})
	});

	//instantiate Revision Application
	REVAPP = new wp.revisions.App();
	//TODO consider enable back button to step back thru states?
	Backbone.history.start();

}(jQuery));
