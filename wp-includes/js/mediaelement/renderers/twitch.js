/*!
 * MediaElement.js
 * http://www.mediaelementjs.com/
 *
 * Wrapper that mimics native HTML5 MediaElement (audio and video)
 * using a variety of technologies (pure JavaScript, Flash, iframe)
 *
 * Copyright 2010-2017, John Dyer (http://j.hn/)
 * Maintained by, Rafael Miranda (rafa8626@gmail.com)
 * License: MIT
 *
 */(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(_dereq_,module,exports){
'use strict';

var TwitchApi = {

	promise: null,

	load: function load(settings) {
		if (typeof Twitch !== 'undefined') {
			TwitchApi.promise = new Promise(function (resolve) {
				resolve();
			}).then(function () {
				TwitchApi._createPlayer(settings);
			});
		} else {
			TwitchApi.promise = TwitchApi.promise || mejs.Utils.loadScript('https://player.twitch.tv/js/embed/v1.js');
			TwitchApi.promise.then(function () {
				TwitchApi._createPlayer(settings);
			});
		}
	},

	_createPlayer: function _createPlayer(settings) {
		var player = new Twitch.Player(settings.id, settings);
		window['__ready__' + settings.id](player);
	},

	getTwitchId: function getTwitchId(url) {
		var twitchId = '';

		if (url.indexOf('?') > 0) {
			twitchId = TwitchApi.getTwitchIdFromParam(url);
			if (twitchId === '') {
				twitchId = TwitchApi.getTwitchIdFromUrl(url);
			}
		} else {
			twitchId = TwitchApi.getTwitchIdFromUrl(url);
		}

		return twitchId;
	},

	getTwitchIdFromParam: function getTwitchIdFromParam(url) {
		if (url === undefined || url === null || !url.trim().length) {
			return null;
		}

		var parts = url.split('?'),
		    parameters = parts[1].split('&');

		var twitchId = '';

		for (var i = 0, total = parameters.length; i < total; i++) {
			var paramParts = parameters[i].split('=');
			if (~paramParts[0].indexOf('channel=')) {
				twitchId = paramParts[1];
				break;
			} else if (~paramParts[0].indexOf('video=')) {
				twitchId = 'v' + paramParts[1];
				break;
			}
		}

		return twitchId;
	},

	getTwitchIdFromUrl: function getTwitchIdFromUrl(url) {
		if (url === undefined || url === null || !url.trim().length) {
			return null;
		}

		var parts = url.split('?');
		url = parts[0];
		var id = url.substring(url.lastIndexOf('/') + 1);
		return (/^\d+$/i.test(id) !== null ? 'v' + id : id
		);
	},

	getTwitchType: function getTwitchType(id) {
		return (/^v\d+/i.test(id) !== null ? 'video' : 'channel'
		);
	}
};

var TwitchIframeRenderer = {
	name: 'twitch_iframe',
	options: {
		prefix: 'twitch_iframe'
	},

	canPlayType: function canPlayType(type) {
		return ~['video/twitch', 'video/x-twitch'].indexOf(type.toLowerCase());
	},

	create: function create(mediaElement, options, mediaFiles) {
		var twitch = {},
		    apiStack = [],
		    readyState = 4,
		    twitchId = TwitchApi.getTwitchId(mediaFiles[0].src);

		var twitchPlayer = null,
		    paused = true,
		    ended = false,
		    hasStartedPlaying = false,
		    volume = 1,
		    duration = Infinity,
		    time = 0;

		twitch.options = options;
		twitch.id = mediaElement.id + '_' + options.prefix;
		twitch.mediaElement = mediaElement;

		var props = mejs.html5media.properties,
		    assignGettersSetters = function assignGettersSetters(propName) {
			var capName = '' + propName.substring(0, 1).toUpperCase() + propName.substring(1);

			twitch['get' + capName] = function () {
				if (twitchPlayer !== null) {
					var value = null;

					switch (propName) {
						case 'currentTime':
							time = twitchPlayer.getCurrentTime();
							return time;
						case 'duration':
							duration = twitchPlayer.getDuration();
							return duration;
						case 'volume':
							volume = twitchPlayer.getVolume();
							return volume;
						case 'paused':
							paused = twitchPlayer.isPaused();
							return paused;
						case 'ended':
							ended = twitchPlayer.getEnded();
							return ended;
						case 'muted':
							return twitchPlayer.getMuted();
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
							return TwitchApi.getTwitchType(twitchId) === 'channel' ? twitchPlayer.getChannel() : twitchPlayer.getVideo();
						case 'readyState':
							return readyState;
					}

					return value;
				} else {
					return null;
				}
			};

			twitch['set' + capName] = function (value) {
				if (twitchPlayer !== null) {
					switch (propName) {
						case 'src':
							var url = typeof value === 'string' ? value : value[0].src,
							    videoId = TwitchApi.getTwitchId(url);

							if (TwitchApi.getTwitchType(twitchId) === 'channel') {
								twitchPlayer.setChannel(videoId);
							} else {
								twitchPlayer.setVideo(videoId);
							}
							break;
						case 'currentTime':
							twitchPlayer.seek(value);
							setTimeout(function () {
								var event = mejs.Utils.createEvent('timeupdate', twitch);
								mediaElement.dispatchEvent(event);
							}, 50);
							break;
						case 'muted':
							twitchPlayer.setMuted(value);
							setTimeout(function () {
								var event = mejs.Utils.createEvent('volumechange', twitch);
								mediaElement.dispatchEvent(event);
							}, 50);
							break;
						case 'volume':
							volume = value;
							twitchPlayer.setVolume(value);
							setTimeout(function () {
								var event = mejs.Utils.createEvent('volumechange', twitch);
								mediaElement.dispatchEvent(event);
							}, 50);
							break;
						case 'readyState':
							var event = mejs.Utils.createEvent('canplay', twitch);
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
			twitch[methodName] = function () {
				if (twitchPlayer !== null) {
					switch (methodName) {
						case 'play':
							paused = false;
							return twitchPlayer.play();
						case 'pause':
							paused = true;
							return twitchPlayer.pause();
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
				var event = mejs.Utils.createEvent(events[_i2], twitch);
				mediaElement.dispatchEvent(event);
			}
		}

		window['__ready__' + twitch.id] = function (_twitchPlayer) {
			mediaElement.twitchPlayer = twitchPlayer = _twitchPlayer;

			if (apiStack.length) {
				for (var _i3 = 0, _total3 = apiStack.length; _i3 < _total3; _i3++) {
					var stackItem = apiStack[_i3];

					if (stackItem.type === 'set') {
						var propName = stackItem.propName,
						    capName = '' + propName.substring(0, 1).toUpperCase() + propName.substring(1);

						twitch['set' + capName](stackItem.value);
					} else if (stackItem.type === 'call') {
						twitch[stackItem.methodName]();
					}
				}
			}

			var twitchIframe = document.getElementById(twitch.id).firstChild;
			twitchIframe.style.width = '100%';
			twitchIframe.style.height = '100%';

			var events = ['mouseover', 'mouseout'],
			    assignEvents = function assignEvents(e) {
				var event = createEvent(e.type, twitch);
				mediaElement.dispatchEvent(event);
			};

			for (var _i4 = 0, _total4 = events.length; _i4 < _total4; _i4++) {
				twitchIframe.addEventListener(events[_i4], assignEvents, false);
			}

			var timer = void 0;

			twitchPlayer.addEventListener('ready', function () {
				paused = false;
				ended = false;
				sendEvents(['rendererready', 'loadedmetadata', 'loadeddata', 'canplay']);
			});
			twitchPlayer.addEventListener('play', function () {
				if (!hasStartedPlaying) {
					hasStartedPlaying = true;
				}
				paused = false;
				ended = false;
				sendEvents(['play', 'playing', 'progress']);

				timer = setInterval(function () {
					twitchPlayer.getCurrentTime();
					sendEvents(['timeupdate']);
				}, 250);
			});
			twitchPlayer.addEventListener('pause', function () {
				paused = true;
				ended = false;
				if (!twitchPlayer.getEnded()) {
					sendEvents(['pause']);
				}
			});
			twitchPlayer.addEventListener('ended', function () {
				paused = true;
				ended = true;
				sendEvents(['ended']);
				clearInterval(timer);
				hasStartedPlaying = false;
				timer = null;
			});
		};

		var height = mediaElement.originalNode.height,
		    width = mediaElement.originalNode.width,
		    twitchContainer = document.createElement('div'),
		    type = TwitchApi.getTwitchType(twitchId),
		    twitchSettings = {
			id: twitch.id,
			width: width,
			height: height,
			playsinline: false,
			autoplay: mediaElement.originalNode.autoplay,
			muted: mediaElement.originalNode.muted
		};

		twitchSettings[type] = twitchId;
		twitchContainer.id = twitch.id;
		twitchContainer.style.width = '100%';
		twitchContainer.style.height = '100%';

		mediaElement.originalNode.parentNode.insertBefore(twitchContainer, mediaElement.originalNode);
		mediaElement.originalNode.style.display = 'none';
		mediaElement.originalNode.autoplay = false;

		twitch.setSize = function (width, height) {
			if (TwitchApi !== null && !isNaN(width) && !isNaN(height)) {
				twitchContainer.setAttribute('width', width);
				twitchContainer.setAttribute('height', height);
			}
		};
		twitch.hide = function () {
			twitch.pause();
			twitchContainer.style.display = 'none';
		};
		twitch.show = function () {
			twitchContainer.style.display = '';
		};
		twitch.destroy = function () {};

		TwitchApi.load(twitchSettings);

		return twitch;
	}
};

mejs.Utils.typeChecks.push(function (url) {
	return (/\/\/(www|player).twitch.tv/i.test(url) ? 'video/x-twitch' : null
	);
});

mejs.Renderers.add(TwitchIframeRenderer);

},{}]},{},[1]);
