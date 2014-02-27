/*globals window, document, jQuery, _, Backbone, _wpmejsSettings */

(function ($, _, Backbone) {
	"use strict";

	var WPPlaylistView = Backbone.View.extend({
		index : 0,

		itemTemplate : wp.template('wp-playlist-item'),

		initialize : function () {
			var settings = {};

			this.data = $.parseJSON( this.$('script').html() );
			this.playerNode = this.$( this.data.type );

			this.tracks = new Backbone.Collection( this.data.tracks );
			this.current = this.tracks.first();

			if ( 'audio' === this.data.type ) {
				this.currentTemplate = wp.template('wp-playlist-current-item');
				this.currentNode = this.$( '.wp-playlist-current-item' );
			}

			this.renderCurrent();

			if ( this.data.tracklist ) {
				this.renderTracks();
			}

			this.playerNode.attr( 'src', this.current.get('src') );

			_.bindAll( this, 'bindPlayer', 'ended', 'clickTrack' );

			if ( ! _.isUndefined( window._wpmejsSettings ) ) {
				settings.pluginPath = _wpmejsSettings.pluginPath;
			}
			settings.success = this.bindPlayer;

			new MediaElementPlayer( this.playerNode.get(0), settings );
		},

		renderCurrent : function () {
			var dimensions;
			if ( 'video' === this.data.type ) {
				if ( this.data.images && this.current.get( 'image' ) ) {
					this.playerNode.attr( 'poster', this.current.get( 'image' ).src );
				}
				dimensions = this.current.get( 'dimensions' ).resized;
				this.playerNode.attr( dimensions );
			} else {
				if ( ! this.data.images ) {
					this.current.set( 'image', false );
				}
				this.currentNode.html( this.currentTemplate( this.current.toJSON() ) );
			}
		},

		renderTracks : function () {
			var that = this, i = 1, tracklist = $( '<div class="wp-playlist-tracks"></div>' );
			this.tracks.each(function (model) {
				if ( ! that.data.images ) {
					model.set( 'image', false );
				}
				model.set( 'artists', that.data.artists );
				model.set( 'index', that.data.tracknumbers ? i : false );
				tracklist.append( that.itemTemplate( model.toJSON() ) );
				i += 1;
			});
			this.$el.append( tracklist );
		},

		events : {
			'click .wp-playlist-item' : 'clickTrack',
			'click .wp-playlist-next' : 'next',
			'click .wp-playlist-prev' : 'prev'
		},

		bindPlayer : function (mejs) {
			this.player = mejs;
			this.player.addEventListener( 'ended', this.ended );
		},

		clickTrack : function (e) {
			this.index = this.$( '.wp-playlist-item' ).index( e.currentTarget );
			this.setCurrent();
		},

		ended : function () {
			if ( this.index + 1 < this.tracks.length ) {
				this.next();
			} else {
				this.index = 0;
				this.current = this.tracks.at( this.index );
				this.loadCurrent();
			}
		},

		next : function () {
			this.index = this.index + 1 >= this.tracks.length ? 0 : this.index + 1;
			this.setCurrent();
		},

		prev : function () {
			this.index = this.index - 1 < 0 ? this.tracks.length - 1 : this.index - 1;
			this.setCurrent();
		},

		loadCurrent : function () {
			this.player.pause();
			this.playerNode.attr( 'src', this.current.get( 'src' ) );
			this.renderCurrent();
			this.player.load();
		},

		setCurrent : function () {
			this.current = this.tracks.at( this.index );
			this.loadCurrent();
			this.player.play();
		}
	});

    $(document).ready(function () {
		$('.wp-playlist').each(function () {
			return new WPPlaylistView({ el: this });
		});
    });

}(jQuery, _, Backbone));