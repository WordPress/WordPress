/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/_enqueues/wp/media/audiovideo.js":
/*!*************************************************!*\
  !*** ./src/js/_enqueues/wp/media/audiovideo.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("/**\n * @output wp-includes/js/media-audiovideo.js\n */\n\nvar media = wp.media,\n\tbaseSettings = window._wpmejsSettings || {},\n\tl10n = window._wpMediaViewsL10n || {};\n\n/**\n *\n * Defines the wp.media.mixin object.\n *\n * @mixin\n *\n * @since 4.2.0\n */\nwp.media.mixin = {\n\tmejsSettings: baseSettings,\n\n\t/**\n\t * Pauses and removes all players.\n\t *\n\t * @since 4.2.0\n\t *\n\t * @return {void}\n\t */\n\tremoveAllPlayers: function() {\n\t\tvar p;\n\n\t\tif ( window.mejs && window.mejs.players ) {\n\t\t\tfor ( p in window.mejs.players ) {\n\t\t\t\twindow.mejs.players[p].pause();\n\t\t\t\tthis.removePlayer( window.mejs.players[p] );\n\t\t\t}\n\t\t}\n\t},\n\n\t/**\n\t * Removes the player.\n\t *\n\t * Override the MediaElement method for removing a player.\n\t * MediaElement tries to pull the audio/video tag out of\n\t * its container and re-add it to the DOM.\n\t *\n\t * @since 4.2.0\n\t *\n\t * @return {void}\n\t */\n\tremovePlayer: function(t) {\n\t\tvar featureIndex, feature;\n\n\t\tif ( ! t.options ) {\n\t\t\treturn;\n\t\t}\n\n\t\t// invoke features cleanup\n\t\tfor ( featureIndex in t.options.features ) {\n\t\t\tfeature = t.options.features[featureIndex];\n\t\t\tif ( t['clean' + feature] ) {\n\t\t\t\ttry {\n\t\t\t\t\tt['clean' + feature](t);\n\t\t\t\t} catch (e) {}\n\t\t\t}\n\t\t}\n\n\t\tif ( ! t.isDynamic ) {\n\t\t\tt.node.remove();\n\t\t}\n\n\t\tif ( 'html5' !== t.media.rendererName ) {\n\t\t\tt.media.remove();\n\t\t}\n\n\t\tdelete window.mejs.players[t.id];\n\n\t\tt.container.remove();\n\t\tt.globalUnbind('resize', t.globalResizeCallback);\n\t\tt.globalUnbind('keydown', t.globalKeydownCallback);\n\t\tt.globalUnbind('click', t.globalClickCallback);\n\t\tdelete t.media.player;\n\t},\n\n\t/**\n\t *\n\t * Removes and resets all players.\n\t *\n\t * Allows any class that has set 'player' to a MediaElementPlayer\n\t * instance to remove the player when listening to events.\n\t *\n\t * Examples: modal closes, shortcode properties are removed, etc.\n\t *\n\t * @since 4.2.0\n\t */\n\tunsetPlayers : function() {\n\t\tif ( this.players && this.players.length ) {\n\t\t\t_.each( this.players, function (player) {\n\t\t\t\tplayer.pause();\n\t\t\t\twp.media.mixin.removePlayer( player );\n\t\t\t} );\n\t\t\tthis.players = [];\n\t\t}\n\t}\n};\n\n/**\n * Shortcode modeling for playlists.\n *\n * @since 4.2.0\n */\nwp.media.playlist = new wp.media.collection({\n\ttag: 'playlist',\n\teditTitle : l10n.editPlaylistTitle,\n\tdefaults : {\n\t\tid: wp.media.view.settings.post.id,\n\t\tstyle: 'light',\n\t\ttracklist: true,\n\t\ttracknumbers: true,\n\t\timages: true,\n\t\tartists: true,\n\t\ttype: 'audio'\n\t}\n});\n\n/**\n * Shortcode modeling for audio.\n *\n * `edit()` prepares the shortcode for the media modal.\n * `shortcode()` builds the new shortcode after an update.\n *\n * @namespace\n *\n * @since 4.2.0\n */\nwp.media.audio = {\n\tcoerce : wp.media.coerce,\n\n\tdefaults : {\n\t\tid : wp.media.view.settings.post.id,\n\t\tsrc : '',\n\t\tloop : false,\n\t\tautoplay : false,\n\t\tpreload : 'none',\n\t\twidth : 400\n\t},\n\n\t/**\n\t * Instantiates a new media object with the next matching shortcode.\n\t *\n\t * @since 4.2.0\n\t *\n\t * @param {string} data The text to apply the shortcode on.\n\t * @returns {wp.media} The media object.\n\t */\n\tedit : function( data ) {\n\t\tvar frame, shortcode = wp.shortcode.next( 'audio', data ).shortcode;\n\n\t\tframe = wp.media({\n\t\t\tframe: 'audio',\n\t\t\tstate: 'audio-details',\n\t\t\tmetadata: _.defaults( shortcode.attrs.named, this.defaults )\n\t\t});\n\n\t\treturn frame;\n\t},\n\n\t/**\n\t * Generates an audio shortcode.\n\t *\n\t * @since 4.2.0\n\t *\n\t * @param {Array} model Array with attributes for the shortcode.\n\t * @returns {wp.shortcode} The audio shortcode object.\n\t */\n\tshortcode : function( model ) {\n\t\tvar content;\n\n\t\t_.each( this.defaults, function( value, key ) {\n\t\t\tmodel[ key ] = this.coerce( model, key );\n\n\t\t\tif ( value === model[ key ] ) {\n\t\t\t\tdelete model[ key ];\n\t\t\t}\n\t\t}, this );\n\n\t\tcontent = model.content;\n\t\tdelete model.content;\n\n\t\treturn new wp.shortcode({\n\t\t\ttag: 'audio',\n\t\t\tattrs: model,\n\t\t\tcontent: content\n\t\t});\n\t}\n};\n\n/**\n * Shortcode modeling for video.\n *\n *  `edit()` prepares the shortcode for the media modal.\n *  `shortcode()` builds the new shortcode after update.\n *\n * @since 4.2.0\n *\n * @namespace\n */\nwp.media.video = {\n\tcoerce : wp.media.coerce,\n\n\tdefaults : {\n\t\tid : wp.media.view.settings.post.id,\n\t\tsrc : '',\n\t\tposter : '',\n\t\tloop : false,\n\t\tautoplay : false,\n\t\tpreload : 'metadata',\n\t\tcontent : '',\n\t\twidth : 640,\n\t\theight : 360\n\t},\n\n\t/**\n\t * Instantiates a new media object with the next matching shortcode.\n\t *\n\t * @since 4.2.0\n\t *\n\t * @param {string} data The text to apply the shortcode on.\n\t * @returns {wp.media} The media object.\n\t */\n\tedit : function( data ) {\n\t\tvar frame,\n\t\t\tshortcode = wp.shortcode.next( 'video', data ).shortcode,\n\t\t\tattrs;\n\n\t\tattrs = shortcode.attrs.named;\n\t\tattrs.content = shortcode.content;\n\n\t\tframe = wp.media({\n\t\t\tframe: 'video',\n\t\t\tstate: 'video-details',\n\t\t\tmetadata: _.defaults( attrs, this.defaults )\n\t\t});\n\n\t\treturn frame;\n\t},\n\n\t/**\n\t * Generates an video shortcode.\n\t *\n\t * @since 4.2.0\n\t *\n\t * @param {Array} model Array with attributes for the shortcode.\n\t * @returns {wp.shortcode} The video shortcode object.\n\t */\n\tshortcode : function( model ) {\n\t\tvar content;\n\n\t\t_.each( this.defaults, function( value, key ) {\n\t\t\tmodel[ key ] = this.coerce( model, key );\n\n\t\t\tif ( value === model[ key ] ) {\n\t\t\t\tdelete model[ key ];\n\t\t\t}\n\t\t}, this );\n\n\t\tcontent = model.content;\n\t\tdelete model.content;\n\n\t\treturn new wp.shortcode({\n\t\t\ttag: 'video',\n\t\t\tattrs: model,\n\t\t\tcontent: content\n\t\t});\n\t}\n};\n\nmedia.model.PostMedia = __webpack_require__( /*! ../../../media/models/post-media.js */ \"./src/js/media/models/post-media.js\" );\nmedia.controller.AudioDetails = __webpack_require__( /*! ../../../media/controllers/audio-details.js */ \"./src/js/media/controllers/audio-details.js\" );\nmedia.controller.VideoDetails = __webpack_require__( /*! ../../../media/controllers/video-details.js */ \"./src/js/media/controllers/video-details.js\" );\nmedia.view.MediaFrame.MediaDetails = __webpack_require__( /*! ../../../media/views/frame/media-details.js */ \"./src/js/media/views/frame/media-details.js\" );\nmedia.view.MediaFrame.AudioDetails = __webpack_require__( /*! ../../../media/views/frame/audio-details.js */ \"./src/js/media/views/frame/audio-details.js\" );\nmedia.view.MediaFrame.VideoDetails = __webpack_require__( /*! ../../../media/views/frame/video-details.js */ \"./src/js/media/views/frame/video-details.js\" );\nmedia.view.MediaDetails = __webpack_require__( /*! ../../../media/views/media-details.js */ \"./src/js/media/views/media-details.js\" );\nmedia.view.AudioDetails = __webpack_require__( /*! ../../../media/views/audio-details.js */ \"./src/js/media/views/audio-details.js\" );\nmedia.view.VideoDetails = __webpack_require__( /*! ../../../media/views/video-details.js */ \"./src/js/media/views/video-details.js\" );\n\n\n//# sourceURL=webpack:///./src/js/_enqueues/wp/media/audiovideo.js?");

/***/ }),

/***/ "./src/js/media/controllers/audio-details.js":
/*!***************************************************!*\
  !*** ./src/js/media/controllers/audio-details.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("var State = wp.media.controller.State,\n\tl10n = wp.media.view.l10n,\n\tAudioDetails;\n\n/**\n * wp.media.controller.AudioDetails\n *\n * The controller for the Audio Details state\n *\n * @memberOf wp.media.controller\n *\n * @class\n * @augments wp.media.controller.State\n * @augments Backbone.Model\n */\nAudioDetails = State.extend(/** @lends wp.media.controller.AudioDetails.prototype */{\n\tdefaults: {\n\t\tid: 'audio-details',\n\t\ttoolbar: 'audio-details',\n\t\ttitle: l10n.audioDetailsTitle,\n\t\tcontent: 'audio-details',\n\t\tmenu: 'audio-details',\n\t\trouter: false,\n\t\tpriority: 60\n\t},\n\n\tinitialize: function( options ) {\n\t\tthis.media = options.media;\n\t\tState.prototype.initialize.apply( this, arguments );\n\t}\n});\n\nmodule.exports = AudioDetails;\n\n\n//# sourceURL=webpack:///./src/js/media/controllers/audio-details.js?");

/***/ }),

/***/ "./src/js/media/controllers/video-details.js":
/*!***************************************************!*\
  !*** ./src/js/media/controllers/video-details.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("/**\n * wp.media.controller.VideoDetails\n *\n * The controller for the Video Details state\n *\n * @memberOf wp.media.controller\n *\n * @class\n * @augments wp.media.controller.State\n * @augments Backbone.Model\n */\nvar State = wp.media.controller.State,\n\tl10n = wp.media.view.l10n,\n\tVideoDetails;\n\nVideoDetails = State.extend(/** @lends wp.media.controller.VideoDetails.prototype */{\n\tdefaults: {\n\t\tid: 'video-details',\n\t\ttoolbar: 'video-details',\n\t\ttitle: l10n.videoDetailsTitle,\n\t\tcontent: 'video-details',\n\t\tmenu: 'video-details',\n\t\trouter: false,\n\t\tpriority: 60\n\t},\n\n\tinitialize: function( options ) {\n\t\tthis.media = options.media;\n\t\tState.prototype.initialize.apply( this, arguments );\n\t}\n});\n\nmodule.exports = VideoDetails;\n\n\n//# sourceURL=webpack:///./src/js/media/controllers/video-details.js?");

/***/ }),

/***/ "./src/js/media/models/post-media.js":
/*!*******************************************!*\
  !*** ./src/js/media/models/post-media.js ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("/**\n * wp.media.model.PostMedia\n *\n * Shared model class for audio and video. Updates the model after\n *   \"Add Audio|Video Source\" and \"Replace Audio|Video\" states return\n *\n * @memberOf wp.media.model\n *\n * @class\n * @augments Backbone.Model\n */\nvar PostMedia = Backbone.Model.extend(/** @lends wp.media.model.PostMedia.prototype */{\n\tinitialize: function() {\n\t\tthis.attachment = false;\n\t},\n\n\tsetSource: function( attachment ) {\n\t\tthis.attachment = attachment;\n\t\tthis.extension = attachment.get( 'filename' ).split('.').pop();\n\n\t\tif ( this.get( 'src' ) && this.extension === this.get( 'src' ).split('.').pop() ) {\n\t\t\tthis.unset( 'src' );\n\t\t}\n\n\t\tif ( _.contains( wp.media.view.settings.embedExts, this.extension ) ) {\n\t\t\tthis.set( this.extension, this.attachment.get( 'url' ) );\n\t\t} else {\n\t\t\tthis.unset( this.extension );\n\t\t}\n\t},\n\n\tchangeAttachment: function( attachment ) {\n\t\tthis.setSource( attachment );\n\n\t\tthis.unset( 'src' );\n\t\t_.each( _.without( wp.media.view.settings.embedExts, this.extension ), function( ext ) {\n\t\t\tthis.unset( ext );\n\t\t}, this );\n\t}\n});\n\nmodule.exports = PostMedia;\n\n\n//# sourceURL=webpack:///./src/js/media/models/post-media.js?");

/***/ }),

/***/ "./src/js/media/views/audio-details.js":
/*!*********************************************!*\
  !*** ./src/js/media/views/audio-details.js ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("var MediaDetails = wp.media.view.MediaDetails,\n\tAudioDetails;\n\n/**\n * wp.media.view.AudioDetails\n *\n * @memberOf wp.media.view\n *\n * @class\n * @augments wp.media.view.MediaDetails\n * @augments wp.media.view.Settings.AttachmentDisplay\n * @augments wp.media.view.Settings\n * @augments wp.media.View\n * @augments wp.Backbone.View\n * @augments Backbone.View\n */\nAudioDetails = MediaDetails.extend(/** @lends wp.media.view.AudioDetails.prototype */{\n\tclassName: 'audio-details',\n\ttemplate:  wp.template('audio-details'),\n\n\tsetMedia: function() {\n\t\tvar audio = this.$('.wp-audio-shortcode');\n\n\t\tif ( audio.find( 'source' ).length ) {\n\t\t\tif ( audio.is(':hidden') ) {\n\t\t\t\taudio.show();\n\t\t\t}\n\t\t\tthis.media = MediaDetails.prepareSrc( audio.get(0) );\n\t\t} else {\n\t\t\taudio.hide();\n\t\t\tthis.media = false;\n\t\t}\n\n\t\treturn this;\n\t}\n});\n\nmodule.exports = AudioDetails;\n\n\n//# sourceURL=webpack:///./src/js/media/views/audio-details.js?");

/***/ }),

/***/ "./src/js/media/views/frame/audio-details.js":
/*!***************************************************!*\
  !*** ./src/js/media/views/frame/audio-details.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("var MediaDetails = wp.media.view.MediaFrame.MediaDetails,\n\tMediaLibrary = wp.media.controller.MediaLibrary,\n\n\tl10n = wp.media.view.l10n,\n\tAudioDetails;\n\n/**\n * wp.media.view.MediaFrame.AudioDetails\n *\n * @memberOf wp.media.view.MediaFrame\n *\n * @class\n * @augments wp.media.view.MediaFrame.MediaDetails\n * @augments wp.media.view.MediaFrame.Select\n * @augments wp.media.view.MediaFrame\n * @augments wp.media.view.Frame\n * @augments wp.media.View\n * @augments wp.Backbone.View\n * @augments Backbone.View\n * @mixes wp.media.controller.StateMachine\n */\nAudioDetails = MediaDetails.extend(/** @lends wp.media.view.MediaFrame.AudioDetails.prototype */{\n\tdefaults: {\n\t\tid:      'audio',\n\t\turl:     '',\n\t\tmenu:    'audio-details',\n\t\tcontent: 'audio-details',\n\t\ttoolbar: 'audio-details',\n\t\ttype:    'link',\n\t\ttitle:    l10n.audioDetailsTitle,\n\t\tpriority: 120\n\t},\n\n\tinitialize: function( options ) {\n\t\toptions.DetailsView = wp.media.view.AudioDetails;\n\t\toptions.cancelText = l10n.audioDetailsCancel;\n\t\toptions.addText = l10n.audioAddSourceTitle;\n\n\t\tMediaDetails.prototype.initialize.call( this, options );\n\t},\n\n\tbindHandlers: function() {\n\t\tMediaDetails.prototype.bindHandlers.apply( this, arguments );\n\n\t\tthis.on( 'toolbar:render:replace-audio', this.renderReplaceToolbar, this );\n\t\tthis.on( 'toolbar:render:add-audio-source', this.renderAddSourceToolbar, this );\n\t},\n\n\tcreateStates: function() {\n\t\tthis.states.add([\n\t\t\tnew wp.media.controller.AudioDetails( {\n\t\t\t\tmedia: this.media\n\t\t\t} ),\n\n\t\t\tnew MediaLibrary( {\n\t\t\t\ttype: 'audio',\n\t\t\t\tid: 'replace-audio',\n\t\t\t\ttitle: l10n.audioReplaceTitle,\n\t\t\t\ttoolbar: 'replace-audio',\n\t\t\t\tmedia: this.media,\n\t\t\t\tmenu: 'audio-details'\n\t\t\t} ),\n\n\t\t\tnew MediaLibrary( {\n\t\t\t\ttype: 'audio',\n\t\t\t\tid: 'add-audio-source',\n\t\t\t\ttitle: l10n.audioAddSourceTitle,\n\t\t\t\ttoolbar: 'add-audio-source',\n\t\t\t\tmedia: this.media,\n\t\t\t\tmenu: false\n\t\t\t} )\n\t\t]);\n\t}\n});\n\nmodule.exports = AudioDetails;\n\n\n//# sourceURL=webpack:///./src/js/media/views/frame/audio-details.js?");

/***/ }),

/***/ "./src/js/media/views/frame/media-details.js":
/*!***************************************************!*\
  !*** ./src/js/media/views/frame/media-details.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("var Select = wp.media.view.MediaFrame.Select,\n\tl10n = wp.media.view.l10n,\n\tMediaDetails;\n\n/**\n * wp.media.view.MediaFrame.MediaDetails\n *\n * @memberOf wp.media.view.MediaFrame\n *\n * @class\n * @augments wp.media.view.MediaFrame.Select\n * @augments wp.media.view.MediaFrame\n * @augments wp.media.view.Frame\n * @augments wp.media.View\n * @augments wp.Backbone.View\n * @augments Backbone.View\n * @mixes wp.media.controller.StateMachine\n */\nMediaDetails = Select.extend(/** @lends wp.media.view.MediaFrame.MediaDetails.prototype */{\n\tdefaults: {\n\t\tid:      'media',\n\t\turl:     '',\n\t\tmenu:    'media-details',\n\t\tcontent: 'media-details',\n\t\ttoolbar: 'media-details',\n\t\ttype:    'link',\n\t\tpriority: 120\n\t},\n\n\tinitialize: function( options ) {\n\t\tthis.DetailsView = options.DetailsView;\n\t\tthis.cancelText = options.cancelText;\n\t\tthis.addText = options.addText;\n\n\t\tthis.media = new wp.media.model.PostMedia( options.metadata );\n\t\tthis.options.selection = new wp.media.model.Selection( this.media.attachment, { multiple: false } );\n\t\tSelect.prototype.initialize.apply( this, arguments );\n\t},\n\n\tbindHandlers: function() {\n\t\tvar menu = this.defaults.menu;\n\n\t\tSelect.prototype.bindHandlers.apply( this, arguments );\n\n\t\tthis.on( 'menu:create:' + menu, this.createMenu, this );\n\t\tthis.on( 'content:render:' + menu, this.renderDetailsContent, this );\n\t\tthis.on( 'menu:render:' + menu, this.renderMenu, this );\n\t\tthis.on( 'toolbar:render:' + menu, this.renderDetailsToolbar, this );\n\t},\n\n\trenderDetailsContent: function() {\n\t\tvar view = new this.DetailsView({\n\t\t\tcontroller: this,\n\t\t\tmodel: this.state().media,\n\t\t\tattachment: this.state().media.attachment\n\t\t}).render();\n\n\t\tthis.content.set( view );\n\t},\n\n\trenderMenu: function( view ) {\n\t\tvar lastState = this.lastState(),\n\t\t\tprevious = lastState && lastState.id,\n\t\t\tframe = this;\n\n\t\tview.set({\n\t\t\tcancel: {\n\t\t\t\ttext:     this.cancelText,\n\t\t\t\tpriority: 20,\n\t\t\t\tclick:    function() {\n\t\t\t\t\tif ( previous ) {\n\t\t\t\t\t\tframe.setState( previous );\n\t\t\t\t\t} else {\n\t\t\t\t\t\tframe.close();\n\t\t\t\t\t}\n\t\t\t\t}\n\t\t\t},\n\t\t\tseparateCancel: new wp.media.View({\n\t\t\t\tclassName: 'separator',\n\t\t\t\tpriority: 40\n\t\t\t})\n\t\t});\n\n\t},\n\n\tsetPrimaryButton: function(text, handler) {\n\t\tthis.toolbar.set( new wp.media.view.Toolbar({\n\t\t\tcontroller: this,\n\t\t\titems: {\n\t\t\t\tbutton: {\n\t\t\t\t\tstyle:    'primary',\n\t\t\t\t\ttext:     text,\n\t\t\t\t\tpriority: 80,\n\t\t\t\t\tclick:    function() {\n\t\t\t\t\t\tvar controller = this.controller;\n\t\t\t\t\t\thandler.call( this, controller, controller.state() );\n\t\t\t\t\t\t// Restore and reset the default state.\n\t\t\t\t\t\tcontroller.setState( controller.options.state );\n\t\t\t\t\t\tcontroller.reset();\n\t\t\t\t\t}\n\t\t\t\t}\n\t\t\t}\n\t\t}) );\n\t},\n\n\trenderDetailsToolbar: function() {\n\t\tthis.setPrimaryButton( l10n.update, function( controller, state ) {\n\t\t\tcontroller.close();\n\t\t\tstate.trigger( 'update', controller.media.toJSON() );\n\t\t} );\n\t},\n\n\trenderReplaceToolbar: function() {\n\t\tthis.setPrimaryButton( l10n.replace, function( controller, state ) {\n\t\t\tvar attachment = state.get( 'selection' ).single();\n\t\t\tcontroller.media.changeAttachment( attachment );\n\t\t\tstate.trigger( 'replace', controller.media.toJSON() );\n\t\t} );\n\t},\n\n\trenderAddSourceToolbar: function() {\n\t\tthis.setPrimaryButton( this.addText, function( controller, state ) {\n\t\t\tvar attachment = state.get( 'selection' ).single();\n\t\t\tcontroller.media.setSource( attachment );\n\t\t\tstate.trigger( 'add-source', controller.media.toJSON() );\n\t\t} );\n\t}\n});\n\nmodule.exports = MediaDetails;\n\n\n//# sourceURL=webpack:///./src/js/media/views/frame/media-details.js?");

/***/ }),

/***/ "./src/js/media/views/frame/video-details.js":
/*!***************************************************!*\
  !*** ./src/js/media/views/frame/video-details.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("var MediaDetails = wp.media.view.MediaFrame.MediaDetails,\n\tMediaLibrary = wp.media.controller.MediaLibrary,\n\tl10n = wp.media.view.l10n,\n\tVideoDetails;\n\n/**\n * wp.media.view.MediaFrame.VideoDetails\n *\n * @memberOf wp.media.view.MediaFrame\n *\n * @class\n * @augments wp.media.view.MediaFrame.MediaDetails\n * @augments wp.media.view.MediaFrame.Select\n * @augments wp.media.view.MediaFrame\n * @augments wp.media.view.Frame\n * @augments wp.media.View\n * @augments wp.Backbone.View\n * @augments Backbone.View\n * @mixes wp.media.controller.StateMachine\n */\nVideoDetails = MediaDetails.extend(/** @lends wp.media.view.MediaFrame.VideoDetails.prototype */{\n\tdefaults: {\n\t\tid:      'video',\n\t\turl:     '',\n\t\tmenu:    'video-details',\n\t\tcontent: 'video-details',\n\t\ttoolbar: 'video-details',\n\t\ttype:    'link',\n\t\ttitle:    l10n.videoDetailsTitle,\n\t\tpriority: 120\n\t},\n\n\tinitialize: function( options ) {\n\t\toptions.DetailsView = wp.media.view.VideoDetails;\n\t\toptions.cancelText = l10n.videoDetailsCancel;\n\t\toptions.addText = l10n.videoAddSourceTitle;\n\n\t\tMediaDetails.prototype.initialize.call( this, options );\n\t},\n\n\tbindHandlers: function() {\n\t\tMediaDetails.prototype.bindHandlers.apply( this, arguments );\n\n\t\tthis.on( 'toolbar:render:replace-video', this.renderReplaceToolbar, this );\n\t\tthis.on( 'toolbar:render:add-video-source', this.renderAddSourceToolbar, this );\n\t\tthis.on( 'toolbar:render:select-poster-image', this.renderSelectPosterImageToolbar, this );\n\t\tthis.on( 'toolbar:render:add-track', this.renderAddTrackToolbar, this );\n\t},\n\n\tcreateStates: function() {\n\t\tthis.states.add([\n\t\t\tnew wp.media.controller.VideoDetails({\n\t\t\t\tmedia: this.media\n\t\t\t}),\n\n\t\t\tnew MediaLibrary( {\n\t\t\t\ttype: 'video',\n\t\t\t\tid: 'replace-video',\n\t\t\t\ttitle: l10n.videoReplaceTitle,\n\t\t\t\ttoolbar: 'replace-video',\n\t\t\t\tmedia: this.media,\n\t\t\t\tmenu: 'video-details'\n\t\t\t} ),\n\n\t\t\tnew MediaLibrary( {\n\t\t\t\ttype: 'video',\n\t\t\t\tid: 'add-video-source',\n\t\t\t\ttitle: l10n.videoAddSourceTitle,\n\t\t\t\ttoolbar: 'add-video-source',\n\t\t\t\tmedia: this.media,\n\t\t\t\tmenu: false\n\t\t\t} ),\n\n\t\t\tnew MediaLibrary( {\n\t\t\t\ttype: 'image',\n\t\t\t\tid: 'select-poster-image',\n\t\t\t\ttitle: l10n.videoSelectPosterImageTitle,\n\t\t\t\ttoolbar: 'select-poster-image',\n\t\t\t\tmedia: this.media,\n\t\t\t\tmenu: 'video-details'\n\t\t\t} ),\n\n\t\t\tnew MediaLibrary( {\n\t\t\t\ttype: 'text',\n\t\t\t\tid: 'add-track',\n\t\t\t\ttitle: l10n.videoAddTrackTitle,\n\t\t\t\ttoolbar: 'add-track',\n\t\t\t\tmedia: this.media,\n\t\t\t\tmenu: 'video-details'\n\t\t\t} )\n\t\t]);\n\t},\n\n\trenderSelectPosterImageToolbar: function() {\n\t\tthis.setPrimaryButton( l10n.videoSelectPosterImageTitle, function( controller, state ) {\n\t\t\tvar urls = [], attachment = state.get( 'selection' ).single();\n\n\t\t\tcontroller.media.set( 'poster', attachment.get( 'url' ) );\n\t\t\tstate.trigger( 'set-poster-image', controller.media.toJSON() );\n\n\t\t\t_.each( wp.media.view.settings.embedExts, function (ext) {\n\t\t\t\tif ( controller.media.get( ext ) ) {\n\t\t\t\t\turls.push( controller.media.get( ext ) );\n\t\t\t\t}\n\t\t\t} );\n\n\t\t\twp.ajax.send( 'set-attachment-thumbnail', {\n\t\t\t\tdata : {\n\t\t\t\t\turls: urls,\n\t\t\t\t\tthumbnail_id: attachment.get( 'id' )\n\t\t\t\t}\n\t\t\t} );\n\t\t} );\n\t},\n\n\trenderAddTrackToolbar: function() {\n\t\tthis.setPrimaryButton( l10n.videoAddTrackTitle, function( controller, state ) {\n\t\t\tvar attachment = state.get( 'selection' ).single(),\n\t\t\t\tcontent = controller.media.get( 'content' );\n\n\t\t\tif ( -1 === content.indexOf( attachment.get( 'url' ) ) ) {\n\t\t\t\tcontent += [\n\t\t\t\t\t'<track srclang=\"en\" label=\"English\" kind=\"subtitles\" src=\"',\n\t\t\t\t\tattachment.get( 'url' ),\n\t\t\t\t\t'\" />'\n\t\t\t\t].join('');\n\n\t\t\t\tcontroller.media.set( 'content', content );\n\t\t\t}\n\t\t\tstate.trigger( 'add-track', controller.media.toJSON() );\n\t\t} );\n\t}\n});\n\nmodule.exports = VideoDetails;\n\n\n//# sourceURL=webpack:///./src/js/media/views/frame/video-details.js?");

/***/ }),

/***/ "./src/js/media/views/media-details.js":
/*!*********************************************!*\
  !*** ./src/js/media/views/media-details.js ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("/* global MediaElementPlayer */\nvar AttachmentDisplay = wp.media.view.Settings.AttachmentDisplay,\n\t$ = jQuery,\n\tMediaDetails;\n\n/**\n * wp.media.view.MediaDetails\n *\n * @memberOf wp.media.view\n *\n * @class\n * @augments wp.media.view.Settings.AttachmentDisplay\n * @augments wp.media.view.Settings\n * @augments wp.media.View\n * @augments wp.Backbone.View\n * @augments Backbone.View\n */\nMediaDetails = AttachmentDisplay.extend(/** @lends wp.media.view.MediaDetails.prototype */{\n\tinitialize: function() {\n\t\t_.bindAll(this, 'success');\n\t\tthis.players = [];\n\t\tthis.listenTo( this.controller, 'close', wp.media.mixin.unsetPlayers );\n\t\tthis.on( 'ready', this.setPlayer );\n\t\tthis.on( 'media:setting:remove', wp.media.mixin.unsetPlayers, this );\n\t\tthis.on( 'media:setting:remove', this.render );\n\t\tthis.on( 'media:setting:remove', this.setPlayer );\n\n\t\tAttachmentDisplay.prototype.initialize.apply( this, arguments );\n\t},\n\n\tevents: function(){\n\t\treturn _.extend( {\n\t\t\t'click .remove-setting' : 'removeSetting',\n\t\t\t'change .content-track' : 'setTracks',\n\t\t\t'click .remove-track' : 'setTracks',\n\t\t\t'click .add-media-source' : 'addSource'\n\t\t}, AttachmentDisplay.prototype.events );\n\t},\n\n\tprepare: function() {\n\t\treturn _.defaults({\n\t\t\tmodel: this.model.toJSON()\n\t\t}, this.options );\n\t},\n\n\t/**\n\t * Remove a setting's UI when the model unsets it\n\t *\n\t * @fires wp.media.view.MediaDetails#media:setting:remove\n\t *\n\t * @param {Event} e\n\t */\n\tremoveSetting : function(e) {\n\t\tvar wrap = $( e.currentTarget ).parent(), setting;\n\t\tsetting = wrap.find( 'input' ).data( 'setting' );\n\n\t\tif ( setting ) {\n\t\t\tthis.model.unset( setting );\n\t\t\tthis.trigger( 'media:setting:remove', this );\n\t\t}\n\n\t\twrap.remove();\n\t},\n\n\t/**\n\t *\n\t * @fires wp.media.view.MediaDetails#media:setting:remove\n\t */\n\tsetTracks : function() {\n\t\tvar tracks = '';\n\n\t\t_.each( this.$('.content-track'), function(track) {\n\t\t\ttracks += $( track ).val();\n\t\t} );\n\n\t\tthis.model.set( 'content', tracks );\n\t\tthis.trigger( 'media:setting:remove', this );\n\t},\n\n\taddSource : function( e ) {\n\t\tthis.controller.lastMime = $( e.currentTarget ).data( 'mime' );\n\t\tthis.controller.setState( 'add-' + this.controller.defaults.id + '-source' );\n\t},\n\n\tloadPlayer: function () {\n\t\tthis.players.push( new MediaElementPlayer( this.media, this.settings ) );\n\t\tthis.scriptXhr = false;\n\t},\n\n\tsetPlayer : function() {\n\t\tvar src;\n\n\t\tif ( this.players.length || ! this.media || this.scriptXhr ) {\n\t\t\treturn;\n\t\t}\n\n\t\tsrc = this.model.get( 'src' );\n\n\t\tif ( src && src.indexOf( 'vimeo' ) > -1 && ! ( 'Vimeo' in window ) ) {\n\t\t\tthis.scriptXhr = $.getScript( 'https://player.vimeo.com/api/player.js', _.bind( this.loadPlayer, this ) );\n\t\t} else {\n\t\t\tthis.loadPlayer();\n\t\t}\n\t},\n\n\t/**\n\t * @abstract\n\t */\n\tsetMedia : function() {\n\t\treturn this;\n\t},\n\n\tsuccess : function(mejs) {\n\t\tvar autoplay = mejs.attributes.autoplay && 'false' !== mejs.attributes.autoplay;\n\n\t\tif ( 'flash' === mejs.pluginType && autoplay ) {\n\t\t\tmejs.addEventListener( 'canplay', function() {\n\t\t\t\tmejs.play();\n\t\t\t}, false );\n\t\t}\n\n\t\tthis.mejs = mejs;\n\t},\n\n\t/**\n\t * @returns {media.view.MediaDetails} Returns itself to allow chaining\n\t */\n\trender: function() {\n\t\tAttachmentDisplay.prototype.render.apply( this, arguments );\n\n\t\tsetTimeout( _.bind( function() {\n\t\t\tthis.resetFocus();\n\t\t}, this ), 10 );\n\n\t\tthis.settings = _.defaults( {\n\t\t\tsuccess : this.success\n\t\t}, wp.media.mixin.mejsSettings );\n\n\t\treturn this.setMedia();\n\t},\n\n\tresetFocus: function() {\n\t\tthis.$( '.embed-media-settings' ).scrollTop( 0 );\n\t}\n},/** @lends wp.media.view.MediaDetails */{\n\tinstances : 0,\n\t/**\n\t * When multiple players in the DOM contain the same src, things get weird.\n\t *\n\t * @param {HTMLElement} elem\n\t * @returns {HTMLElement}\n\t */\n\tprepareSrc : function( elem ) {\n\t\tvar i = MediaDetails.instances++;\n\t\t_.each( $( elem ).find( 'source' ), function( source ) {\n\t\t\tsource.src = [\n\t\t\t\tsource.src,\n\t\t\t\tsource.src.indexOf('?') > -1 ? '&' : '?',\n\t\t\t\t'_=',\n\t\t\t\ti\n\t\t\t].join('');\n\t\t} );\n\n\t\treturn elem;\n\t}\n});\n\nmodule.exports = MediaDetails;\n\n\n//# sourceURL=webpack:///./src/js/media/views/media-details.js?");

/***/ }),

/***/ "./src/js/media/views/video-details.js":
/*!*********************************************!*\
  !*** ./src/js/media/views/video-details.js ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("var MediaDetails = wp.media.view.MediaDetails,\n\tVideoDetails;\n\n/**\n * wp.media.view.VideoDetails\n *\n * @memberOf wp.media.view\n *\n * @class\n * @augments wp.media.view.MediaDetails\n * @augments wp.media.view.Settings.AttachmentDisplay\n * @augments wp.media.view.Settings\n * @augments wp.media.View\n * @augments wp.Backbone.View\n * @augments Backbone.View\n */\nVideoDetails = MediaDetails.extend(/** @lends wp.media.view.VideoDetails.prototype */{\n\tclassName: 'video-details',\n\ttemplate:  wp.template('video-details'),\n\n\tsetMedia: function() {\n\t\tvar video = this.$('.wp-video-shortcode');\n\n\t\tif ( video.find( 'source' ).length ) {\n\t\t\tif ( video.is(':hidden') ) {\n\t\t\t\tvideo.show();\n\t\t\t}\n\n\t\t\tif ( ! video.hasClass( 'youtube-video' ) && ! video.hasClass( 'vimeo-video' ) ) {\n\t\t\t\tthis.media = MediaDetails.prepareSrc( video.get(0) );\n\t\t\t} else {\n\t\t\t\tthis.media = video.get(0);\n\t\t\t}\n\t\t} else {\n\t\t\tvideo.hide();\n\t\t\tthis.media = false;\n\t\t}\n\n\t\treturn this;\n\t}\n});\n\nmodule.exports = VideoDetails;\n\n\n//# sourceURL=webpack:///./src/js/media/views/video-details.js?");

/***/ }),

/***/ 0:
/*!*******************************************************!*\
  !*** multi ./src/js/_enqueues/wp/media/audiovideo.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__(/*! ./src/js/_enqueues/wp/media/audiovideo.js */\"./src/js/_enqueues/wp/media/audiovideo.js\");\n\n\n//# sourceURL=webpack:///multi_./src/js/_enqueues/wp/media/audiovideo.js?");

/***/ })

/******/ });