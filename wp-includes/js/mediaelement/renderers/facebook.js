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

var FacebookApi = {

	promise: null,

	load: function load(settings) {

		if (typeof FB !== 'undefined') {
			FacebookApi._createPlayer(settings);
		} else {
			FacebookApi.promise = FacebookApi.promise || mejs.Utils.loadScript('https://connect.facebook.net/' + settings.options.lang + '/sdk.js');
			FacebookApi.promise.then(function () {
				FB.init(settings.options);

				setTimeout(function () {
					FacebookApi._createPlayer(settings);
				}, 50);
			});
		}
	},

	_createPlayer: function _createPlayer(settings) {
		window['__ready__' + settings.id]();
	}
};
var FacebookRenderer = {
	name: 'facebook',
	options: {
		prefix: 'facebook',
		facebook: {
			appId: '',
			xfbml: true,
			version: 'v2.10',
			lang: 'en_US'
		}
	},

	canPlayType: function canPlayType(type) {
		return ~['video/facebook', 'video/x-facebook'].indexOf(type.toLowerCase());
	},

	create: function create(mediaElement, options, mediaFiles) {
		var apiStack = [],
		    fb = {},
		    readyState = 4;

		var hasStartedPlaying = false,
		    paused = true,
		    ended = false,
		    fbPlayer = null,
		    src = '',
		    poster = '',
		    autoplay = mediaElement.originalNode.autoplay;

		fb.options = options;
		fb.id = mediaElement.id + '_' + options.prefix;
		fb.mediaElement = mediaElement;

		if (mejs.Features.isiPhone && mediaElement.originalNode.getAttribute('poster')) {
			poster = mediaElement.originalNode.getAttribute('poster');
			mediaElement.originalNode.removeAttribute('poster');
		}

		var props = mejs.html5media.properties,
		    assignGettersSetters = function assignGettersSetters(propName) {

			var capName = '' + propName.substring(0, 1).toUpperCase() + propName.substring(1);

			fb['get' + capName] = function () {

				if (fbPlayer !== null) {
					var value = null;

					switch (propName) {
						case 'currentTime':
							return fbPlayer.getCurrentPosition();
						case 'duration':
							return fbPlayer.getDuration();
						case 'volume':
							return fbPlayer.getVolume();
						case 'paused':
							return paused;
						case 'ended':
							return ended;
						case 'muted':
							return fbPlayer.isMuted();
						case 'buffered':
							return {
								start: function start() {
									return 0;
								},
								end: function end() {
									return 0;
								},
								length: 1
							};
						case 'src':
							return src;
						case 'readyState':
							return readyState;
					}

					return value;
				} else {
					return null;
				}
			};

			fb['set' + capName] = function (value) {

				if (fbPlayer !== null) {

					switch (propName) {
						case 'src':
							var url = typeof value === 'string' ? value : value[0].src;
							src = url;

							fbContainer.remove();
							fbContainer = document.createElement('div');
							fbContainer.id = fb.id;
							fbContainer.className = 'fb-video';
							fbContainer.setAttribute('data-href', url);
							fbContainer.setAttribute('data-allowfullscreen', 'true');
							fbContainer.setAttribute('data-controls', 'false');

							mediaElement.originalNode.parentNode.insertBefore(fbContainer, mediaElement.originalNode);
							mediaElement.originalNode.style.display = 'none';

							FacebookApi.load({
								lang: fb.options.lang,
								id: fb.id
							});

							FB.XFBML.parse();

							if (autoplay) {
								fbPlayer.play();
							}
							break;
						case 'currentTime':
							fbPlayer.seek(value);
							break;
						case 'muted':
							if (value) {
								fbPlayer.mute();
							} else {
								fbPlayer.unmute();
							}
							setTimeout(function () {
								var event = mejs.Utils.createEvent('volumechange', fb);
								mediaElement.dispatchEvent(event);
							}, 50);
							break;
						case 'volume':
							fbPlayer.setVolume(value);
							setTimeout(function () {
								var event = mejs.Utils.createEvent('volumechange', fb);
								mediaElement.dispatchEvent(event);
							}, 50);
							break;
						case 'readyState':
							var event = mejs.Utils.createEvent('canplay', fb);
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
			fb[methodName] = function () {
				if (fbPlayer !== null) {
					switch (methodName) {
						case 'play':
							return fbPlayer.play();
						case 'pause':
							return fbPlayer.pause();
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

		function assignEvents(events) {
			for (var _i2 = 0, _total2 = events.length; _i2 < _total2; _i2++) {
				var event = mejs.Utils.createEvent(events[_i2], fb);
				mediaElement.dispatchEvent(event);
			}
		}

		window['__ready__' + fb.id] = function () {
			FB.Event.subscribe('xfbml.ready', function (msg) {
				if (msg.type === 'video' && fb.id === msg.id) {
					mediaElement.fbPlayer = fbPlayer = msg.instance;

					var fbIframe = document.getElementById(fb.id),
					    width = fbIframe.offsetWidth,
					    height = fbIframe.offsetHeight,
					    events = ['mouseover', 'mouseout'],
					    assignIframeEvents = function assignIframeEvents(e) {
						var event = mejs.Utils.createEvent(e.type, fb);
						mediaElement.dispatchEvent(event);
					};

					fb.setSize(width, height);
					if (!mediaElement.originalNode.muted) {
						fbPlayer.unmute();
					}

					if (autoplay) {
						fbPlayer.play();
					}

					for (var _i3 = 0, _total3 = events.length; _i3 < _total3; _i3++) {
						fbIframe.addEventListener(events[_i3], assignIframeEvents);
					}

					fb.eventHandler = {};

					var fbEvents = ['startedPlaying', 'paused', 'finishedPlaying', 'startedBuffering', 'finishedBuffering'];
					for (var _i4 = 0, _total4 = fbEvents.length; _i4 < _total4; _i4++) {
						var event = fbEvents[_i4],
						    handler = fb.eventHandler[event];
						if (handler !== undefined && handler !== null && !mejs.Utils.isObjectEmpty(handler) && typeof handler.removeListener === 'function') {
							handler.removeListener(event);
						}
					}

					if (apiStack.length) {
						for (var _i5 = 0, _total5 = apiStack.length; _i5 < _total5; _i5++) {
							var stackItem = apiStack[_i5];

							if (stackItem.type === 'set') {
								var propName = stackItem.propName,
								    capName = '' + propName.substring(0, 1).toUpperCase() + propName.substring(1);

								fb['set' + capName](stackItem.value);
							} else if (stackItem.type === 'call') {
								fb[stackItem.methodName]();
							}
						}
					}

					assignEvents(['rendererready', 'loadeddata', 'canplay', 'progress', 'loadedmetadata', 'timeupdate']);

					var timer = void 0;

					fb.eventHandler.startedPlaying = fbPlayer.subscribe('startedPlaying', function () {
						if (!hasStartedPlaying) {
							hasStartedPlaying = true;
						}
						paused = false;
						ended = false;
						assignEvents(['play', 'playing', 'timeupdate']);

						timer = setInterval(function () {
							fbPlayer.getCurrentPosition();
							assignEvents(['timeupdate']);
						}, 250);
					});
					fb.eventHandler.paused = fbPlayer.subscribe('paused', function () {
						paused = true;
						ended = false;
						assignEvents(['pause']);
					});
					fb.eventHandler.finishedPlaying = fbPlayer.subscribe('finishedPlaying', function () {
						paused = true;
						ended = true;

						assignEvents(['ended']);
						clearInterval(timer);
						timer = null;
					});
					fb.eventHandler.startedBuffering = fbPlayer.subscribe('startedBuffering', function () {
						assignEvents(['progress', 'timeupdate']);
					});
					fb.eventHandler.finishedBuffering = fbPlayer.subscribe('finishedBuffering', function () {
						assignEvents(['progress', 'timeupdate']);
					});
				}
			});
		};

		src = mediaFiles[0].src;
		var fbContainer = document.createElement('div');
		fbContainer.id = fb.id;
		fbContainer.className = 'fb-video';
		fbContainer.setAttribute('data-href', src);
		fbContainer.setAttribute('data-allowfullscreen', true);
		fbContainer.setAttribute('data-controls', !!mediaElement.originalNode.controls);
		mediaElement.originalNode.parentNode.insertBefore(fbContainer, mediaElement.originalNode);
		mediaElement.originalNode.style.display = 'none';

		FacebookApi.load({
			options: fb.options.facebook,
			id: fb.id
		});

		fb.hide = function () {
			fb.pause();
			if (fbPlayer) {
				fbContainer.style.display = 'none';
			}
		};
		fb.setSize = function (width) {
			if (fbPlayer !== null && !isNaN(width)) {
				fbContainer.style.width = width;
			}
		};
		fb.show = function () {
			if (fbPlayer) {
				fbContainer.style.display = '';
			}
		};

		fb.destroy = function () {
			if (poster) {
				mediaElement.originalNode.setAttribute('poster', poster);
			}
		};

		return fb;
	}
};

mejs.Utils.typeChecks.push(function (url) {
	return ~url.toLowerCase().indexOf('//www.facebook') ? 'video/x-facebook' : null;
});

mejs.Renderers.add(FacebookRenderer);

},{}]},{},[1]);
