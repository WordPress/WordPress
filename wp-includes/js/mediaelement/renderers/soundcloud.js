/*!
 * MediaElement.js
 * http://www.mediaelementjs.com/
 *
 * Wrapper that mimics native HTML5 MediaElement (audio and video)
 * using a variety of technologies (pure JavaScript, Flash, iframe)
 *
 * Copyright 2010-2017, John Dyer (http://j.hn/)
 * License: MIT
 *
 */(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(_dereq_,module,exports){
'use strict';

var SoundCloudApi = {

	promise: null,

	load: function load(settings) {

		if (typeof SC !== 'undefined') {
			SoundCloudApi._createPlayer(settings);
		} else {
			SoundCloudApi.promise = SoundCloudApi.promise || mejs.Utils.loadScript('https://w.soundcloud.com/player/api.js');
			SoundCloudApi.promise.then(function () {
				SoundCloudApi._createPlayer(settings);
			});
		}
	},

	_createPlayer: function _createPlayer(settings) {
		var player = SC.Widget(settings.iframe);
		window['__ready__' + settings.id](player);
	}
};

var SoundCloudIframeRenderer = {
	name: 'soundcloud_iframe',
	options: {
		prefix: 'soundcloud_iframe'
	},

	canPlayType: function canPlayType(type) {
		return ~['video/soundcloud', 'video/x-soundcloud'].indexOf(type.toLowerCase());
	},

	create: function create(mediaElement, options, mediaFiles) {
		var sc = {},
		    apiStack = [],
		    readyState = 4,
		    autoplay = mediaElement.originalNode.autoplay;

		var duration = 0,
		    currentTime = 0,
		    bufferedTime = 0,
		    volume = 1,
		    muted = false,
		    paused = true,
		    ended = false,
		    scPlayer = null,
		    scIframe = null;

		sc.options = options;
		sc.id = mediaElement.id + '_' + options.prefix;
		sc.mediaElement = mediaElement;

		var props = mejs.html5media.properties,
		    assignGettersSetters = function assignGettersSetters(propName) {
			var capName = '' + propName.substring(0, 1).toUpperCase() + propName.substring(1);

			sc['get' + capName] = function () {
				if (scPlayer !== null) {
					var value = null;

					switch (propName) {
						case 'currentTime':
							return currentTime;
						case 'duration':
							return duration;
						case 'volume':
							return volume;
						case 'paused':
							return paused;
						case 'ended':
							return ended;
						case 'muted':
							return muted;
						case 'buffered':
							return {
								start: function start() {
									return 0;
								},
								end: function end() {
									return bufferedTime * duration;
								},
								length: 1
							};
						case 'src':
							return scIframe ? scIframe.src : '';
						case 'readyState':
							return readyState;
					}
					return value;
				} else {
					return null;
				}
			};

			sc['set' + capName] = function (value) {
				if (scPlayer !== null) {
					switch (propName) {
						case 'src':
							var url = typeof value === 'string' ? value : value[0].src;
							scPlayer.load(url);
							if (autoplay) {
								scPlayer.play();
							}
							break;
						case 'currentTime':
							scPlayer.seekTo(value * 1000);
							break;
						case 'muted':
							if (value) {
								scPlayer.setVolume(0);
							} else {
								scPlayer.setVolume(1);
							}
							setTimeout(function () {
								var event = mejs.Utils.createEvent('volumechange', sc);
								mediaElement.dispatchEvent(event);
							}, 50);
							break;
						case 'volume':
							scPlayer.setVolume(value);
							setTimeout(function () {
								var event = mejs.Utils.createEvent('volumechange', sc);
								mediaElement.dispatchEvent(event);
							}, 50);
							break;
						case 'readyState':
							var event = mejs.Utils.createEvent('canplay', sc);
							mediaElement.dispatchEvent(event);
							break;

						default:
							
							break;
					}
				} else {
					apiStack.push({ type: 'set', propName: propName, value: value });
				}
			};
		};

		for (var i = 0, total = props.length; i < total; i++) {
			assignGettersSetters(props[i]);
		}

		var methods = mejs.html5media.methods,
		    assignMethods = function assignMethods(methodName) {
			sc[methodName] = function () {
				if (scPlayer !== null) {
					switch (methodName) {
						case 'play':
							return scPlayer.play();
						case 'pause':
							return scPlayer.pause();
						case 'load':
							return null;
					}
				} else {
					apiStack.push({ type: 'call', methodName: methodName });
				}
			};
		};

		for (var _i = 0, _total = methods.length; _i < _total; _i++) {
			assignMethods(methods[_i]);
		}

		window['__ready__' + sc.id] = function (_scPlayer) {

			mediaElement.scPlayer = scPlayer = _scPlayer;

			if (autoplay) {
				scPlayer.play();
			}

			if (apiStack.length) {
				for (var _i2 = 0, _total2 = apiStack.length; _i2 < _total2; _i2++) {

					var stackItem = apiStack[_i2];

					if (stackItem.type === 'set') {
						var propName = stackItem.propName,
						    capName = '' + propName.substring(0, 1).toUpperCase() + propName.substring(1);

						sc['set' + capName](stackItem.value);
					} else if (stackItem.type === 'call') {
						sc[stackItem.methodName]();
					}
				}
			}

			scPlayer.bind(SC.Widget.Events.PLAY_PROGRESS, function () {
				paused = false;
				ended = false;
				scPlayer.getPosition(function (_currentTime) {
					currentTime = _currentTime / 1000;
					var event = mejs.Utils.createEvent('timeupdate', sc);
					mediaElement.dispatchEvent(event);
				});
			});
			scPlayer.bind(SC.Widget.Events.PAUSE, function () {
				paused = true;
				var event = mejs.Utils.createEvent('pause', sc);
				mediaElement.dispatchEvent(event);
			});
			scPlayer.bind(SC.Widget.Events.PLAY, function () {
				paused = false;
				ended = false;
				var event = mejs.Utils.createEvent('play', sc);
				mediaElement.dispatchEvent(event);
			});
			scPlayer.bind(SC.Widget.Events.FINISHED, function () {
				paused = false;
				ended = true;
				var event = mejs.Utils.createEvent('ended', sc);
				mediaElement.dispatchEvent(event);
			});
			scPlayer.bind(SC.Widget.Events.READY, function () {
				scPlayer.getDuration(function (_duration) {
					duration = _duration / 1000;
					var event = mejs.Utils.createEvent('loadedmetadata', sc);
					mediaElement.dispatchEvent(event);
				});
			});
			scPlayer.bind(SC.Widget.Events.LOAD_PROGRESS, function () {
				scPlayer.getDuration(function (loadProgress) {
					if (duration > 0) {
						bufferedTime = duration * loadProgress;
						var event = mejs.Utils.createEvent('progress', sc);
						mediaElement.dispatchEvent(event);
					}
				});
				scPlayer.getDuration(function (_duration) {
					duration = _duration;

					var event = mejs.Utils.createEvent('loadedmetadata', sc);
					mediaElement.dispatchEvent(event);
				});
			});

			var initEvents = ['rendererready', 'loadeddata', 'loadedmetadata', 'canplay'];
			for (var _i3 = 0, _total3 = initEvents.length; _i3 < _total3; _i3++) {
				var event = mejs.Utils.createEvent(initEvents[_i3], sc);
				mediaElement.dispatchEvent(event);
			}
		};

		scIframe = document.createElement('iframe');
		scIframe.id = sc.id;
		scIframe.width = 10;
		scIframe.height = 10;
		scIframe.frameBorder = 0;
		scIframe.style.visibility = 'hidden';
		scIframe.src = mediaFiles[0].src;
		scIframe.scrolling = 'no';

		mediaElement.appendChild(scIframe);
		mediaElement.originalNode.style.display = 'none';

		var scSettings = {
			iframe: scIframe,
			id: sc.id
		};

		SoundCloudApi.load(scSettings);

		sc.setSize = function () {};
		sc.hide = function () {
			sc.pause();
			if (scIframe) {
				scIframe.style.display = 'none';
			}
		};
		sc.show = function () {
			if (scIframe) {
				scIframe.style.display = '';
			}
		};
		sc.destroy = function () {
			scPlayer.destroy();
		};

		return sc;
	}
};

mejs.Utils.typeChecks.push(function (url) {
	return (/\/\/(w\.)?soundcloud.com/i.test(url) ? 'video/x-soundcloud' : null
	);
});

mejs.Renderers.add(SoundCloudIframeRenderer);

},{}]},{},[1]);
