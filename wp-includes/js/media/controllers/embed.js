/**
 * wp.media.controller.Embed
 *
 * A state for embedding media from a URL.
 *
 * @class
 * @augments wp.media.controller.State
 * @augments Backbone.Model
 *
 * @param {object} attributes                         The attributes hash passed to the state.
 * @param {string} [attributes.id=embed]              Unique identifier.
 * @param {string} [attributes.title=Insert From URL] Title for the state. Displays in the media menu and the frame's title region.
 * @param {string} [attributes.content=embed]         Initial mode for the content region.
 * @param {string} [attributes.menu=default]          Initial mode for the menu region.
 * @param {string} [attributes.toolbar=main-embed]    Initial mode for the toolbar region.
 * @param {string} [attributes.menu=false]            Initial mode for the menu region.
 * @param {int}    [attributes.priority=120]          The priority for the state link in the media menu.
 * @param {string} [attributes.type=link]             The type of embed. Currently only link is supported.
 * @param {string} [attributes.url]                   The embed URL.
 * @param {object} [attributes.metadata={}]           Properties of the embed, which will override attributes.url if set.
 */
var State = require( './state.js' ),
	l10n = wp.media.view.l10n,
	$ = jQuery,
	Embed;

Embed = State.extend({
	defaults: {
		id:       'embed',
		title:    l10n.insertFromUrlTitle,
		content:  'embed',
		menu:     'default',
		toolbar:  'main-embed',
		priority: 120,
		type:     'link',
		url:      '',
		metadata: {}
	},

	// The amount of time used when debouncing the scan.
	sensitivity: 200,

	initialize: function(options) {
		this.metadata = options.metadata;
		this.debouncedScan = _.debounce( _.bind( this.scan, this ), this.sensitivity );
		this.props = new Backbone.Model( this.metadata || { url: '' });
		this.props.on( 'change:url', this.debouncedScan, this );
		this.props.on( 'change:url', this.refresh, this );
		this.on( 'scan', this.scanImage, this );
	},

	/**
	 * Trigger a scan of the embedded URL's content for metadata required to embed.
	 *
	 * @fires wp.media.controller.Embed#scan
	 */
	scan: function() {
		var scanners,
			embed = this,
			attributes = {
				type: 'link',
				scanners: []
			};

		// Scan is triggered with the list of `attributes` to set on the
		// state, useful for the 'type' attribute and 'scanners' attribute,
		// an array of promise objects for asynchronous scan operations.
		if ( this.props.get('url') ) {
			this.trigger( 'scan', attributes );
		}

		if ( attributes.scanners.length ) {
			scanners = attributes.scanners = $.when.apply( $, attributes.scanners );
			scanners.always( function() {
				if ( embed.get('scanners') === scanners ) {
					embed.set( 'loading', false );
				}
			});
		} else {
			attributes.scanners = null;
		}

		attributes.loading = !! attributes.scanners;
		this.set( attributes );
	},
	/**
	 * Try scanning the embed as an image to discover its dimensions.
	 *
	 * @param {Object} attributes
	 */
	scanImage: function( attributes ) {
		var frame = this.frame,
			state = this,
			url = this.props.get('url'),
			image = new Image(),
			deferred = $.Deferred();

		attributes.scanners.push( deferred.promise() );

		// Try to load the image and find its width/height.
		image.onload = function() {
			deferred.resolve();

			if ( state !== frame.state() || url !== state.props.get('url') ) {
				return;
			}

			state.set({
				type: 'image'
			});

			state.props.set({
				width:  image.width,
				height: image.height
			});
		};

		image.onerror = deferred.reject;
		image.src = url;
	},

	refresh: function() {
		this.frame.toolbar.get().refresh();
	},

	reset: function() {
		this.props.clear().set({ url: '' });

		if ( this.active ) {
			this.refresh();
		}
	}
});

module.exports = Embed;
