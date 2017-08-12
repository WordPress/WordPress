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

var FacebookRenderer = {
	name: 'facebook',
	options: {
		prefix: 'facebook',
		facebook: {
			appId: '',
			xfbml: true,
			version: 'v2.10'
		}
	},

	canPlayType: function canPlayType(type) {
		return ~['video/facebook', 'video/x-facebook'].indexOf(type.toLowerCase());
	},

	create: function create(mediaElement, options, mediaFiles) {

		var fbWrapper = {},
		    apiStack = [],
		    eventHandler = {},
		    readyState = 4,
		    autoplay = mediaElement.originalNode.autoplay;

		var poster = '',
		    src = '',
		    paused = true,
		    ended = false,
		    hasStartedPlaying = false,
		    fbApi = null,
		    fbDiv = null;

		if (mejs.Features.isiPhone && mediaElement.originalNode.getAttribute('poster')) {
			poster = mediaElement.originalNode.getAttribute('poster');
			mediaElement.originalNode.removeAttribute('poster');
		}

		options = Object.assign(options, mediaElement.options);
		fbWrapper.options = options;
		fbWrapper.id = mediaElement.id + '_' + options.prefix;
		fbWrapper.mediaElement = mediaElement;

		var props = mejs.html5media.properties,
		    assignGettersSetters = function assignGettersSetters(propName) {

			var capName = '' + propName.substring(0, 1).toUpperCase() + propName.substring(1);

			fbWrapper['get' + capName] = function () {

				if (fbApi !== null) {
					var value = null;

					switch (propName) {
						case 'currentTime':
							return fbApi.getCurrentPosition();
						case 'duration':
							return fbApi.getDuration();
						case 'volume':
							return fbApi.getVolume();
						case 'paused':
							return paused;
						case 'ended':
							return ended;
						case 'muted':
							return fbApi.isMuted();
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

			fbWrapper['set' + capName] = function (value) {

				if (fbApi !== null) {

					switch (propName) {
						case 'src':
							var url = typeof value === 'string' ? value : value[0].src;

							fbDiv.remove();
							createFacebookEmbed(url, options.facebook);

							FB.XFBML.parse();

							if (autoplay) {
								fbApi.play();
							}

							break;
						case 'currentTime':
							fbApi.seek(value);
							break;
						case 'muted':
							if (value) {
								fbApi.mute();
							} else {
								fbApi.unmute();
							}
							setTimeout(function () {
								var event = mejs.Utils.createEvent('volumechange', fbWrapper);
								mediaElement.dispatchEvent(event);
							}, 50);
							break;
						case 'volume':
							fbApi.setVolume(value);
							setTimeout(function () {
								var event = mejs.Utils.createEvent('volumechange', fbWrapper);
								mediaElement.dispatchEvent(event);
							}, 50);
							break;
						case 'readyState':
							var event = mejs.Utils.createEvent('canplay', fbWrapper);
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
			fbWrapper[methodName] = function () {
				if (fbApi !== null) {
					switch (methodName) {
						case 'play':
							return fbApi.play();
						case 'pause':
							return fbApi.pause();
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

		function sendEvents(events) {
			for (var _i2 = 0, _total2 = events.length; _i2 < _total2; _i2++) {
				var event = mejs.Utils.createEvent(events[_i2], fbWrapper);
				mediaElement.dispatchEvent(event);
			}
		}

		function createFacebookEmbed(url, config) {
			src = url;

			fbDiv = document.createElement('div');
			fbDiv.id = fbWrapper.id;
			fbDiv.className = "fb-video";
			fbDiv.setAttribute("data-href", url);
			fbDiv.setAttribute("data-allowfullscreen", "true");
			fbDiv.setAttribute("data-controls", "false");

			mediaElement.originalNode.parentNode.insertBefore(fbDiv, mediaElement.originalNode);
			mediaElement.originalNode.style.display = 'none';

			window.fbAsyncInit = function () {

				FB.init(config);

				FB.Event.subscribe('xfbml.ready', function (msg) {

					if (msg.type === 'video') {

						fbApi = msg.instance;

						var fbIframe = fbDiv.getElementsByTagName('iframe')[0],
						    width = fbIframe.offsetWidth,
						    height = fbIframe.offsetHeight,
						    events = ['mouseover', 'mouseout'],
						    assignEvents = function assignEvents(e) {
							var event = mejs.Utils.createEvent(e.type, fbWrapper);
							mediaElement.dispatchEvent(event);
						};

						fbWrapper.setSize(width, height);

						if (autoplay) {
							fbApi.play();
						}

						for (var _i3 = 0, _total3 = events.length; _i3 < _total3; _i3++) {
							fbIframe.addEventListener(events[_i3], assignEvents, false);
						}

						var fbEvents = ['startedPlaying', 'paused', 'finishedPlaying', 'startedBuffering', 'finishedBuffering'];
						for (var _i4 = 0, _total4 = fbEvents.length; _i4 < _total4; _i4++) {
							var event = fbEvents[_i4],
							    handler = eventHandler[event];
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

									fbWrapper['set' + capName](stackItem.value);
								} else if (stackItem.type === 'call') {
									fbWrapper[stackItem.methodName]();
								}
							}
						}

						sendEvents(['rendererready', 'loadeddata', 'canplay', 'progress', 'loadedmetadata', 'timeupdate']);

						var timer = void 0;

						eventHandler.startedPlaying = fbApi.subscribe('startedPlaying', function () {
							if (!hasStartedPlaying) {
								hasStartedPlaying = true;
							}
							paused = false;
							ended = false;
							sendEvents(['play', 'playing', 'timeupdate']);

							timer = setInterval(function () {
								fbApi.getCurrentPosition();
								sendEvents(['timeupdate']);
							}, 250);
						});
						eventHandler.paused = fbApi.subscribe('paused', function () {
							paused = true;
							ended = false;
							sendEvents(['pause']);
						});
						eventHandler.finishedPlaying = fbApi.subscribe('finishedPlaying', function () {
							paused = true;
							ended = true;

							sendEvents(['ended']);
							clearInterval(timer);
							timer = null;
						});
						eventHandler.startedBuffering = fbApi.subscribe('startedBuffering', function () {
							sendEvents(['progress', 'timeupdate']);
						});
						eventHandler.finishedBuffering = fbApi.subscribe('finishedBuffering', function () {
							sendEvents(['progress', 'timeupdate']);
						});
					}
				});
			};

			mejs.Utils.loadScript('https://connect.facebook.net/en_US/sdk.js');
		}

		if (mediaFiles.length > 0) {
			createFacebookEmbed(mediaFiles[0].src, fbWrapper.options.facebook);
		}

		fbWrapper.hide = function () {
			fbWrapper.stopInterval();
			fbWrapper.pause();
			if (fbDiv) {
				fbDiv.style.display = 'none';
			}
		};
		fbWrapper.show = function () {
			if (fbDiv) {
				fbDiv.style.display = '';
			}
		};
		fbWrapper.setSize = function (width) {
			if (fbApi !== null && !isNaN(width)) {
				fbDiv.style.width = width;
			}
		};
		fbWrapper.destroy = function () {
			if (poster) {
				mediaElement.originalNode.setAttribute('poster', poster);
			}
		};

		fbWrapper.interval = null;

		fbWrapper.startInterval = function () {
			fbWrapper.interval = setInterval(function () {
				var event = mejs.Utils.createEvent('timeupdate', fbWrapper);
				mediaElement.dispatchEvent(event);
			}, 250);
		};
		fbWrapper.stopInterval = function () {
			if (fbWrapper.interval) {
				clearInterval(fbWrapper.interval);
			}
		};

		return fbWrapper;
	}
};

mejs.Utils.typeChecks.push(function (url) {
	return ~url.toLowerCase().indexOf('//www.facebook') ? 'video/x-facebook' : null;
});

mejs.Renderers.add(FacebookRenderer);

},{}]},{},[1]);
