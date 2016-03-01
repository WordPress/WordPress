/**
 * plugin.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2015 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/*jshint maxlen:255 */
/*eslint max-len:0 */
/*global tinymce:true */

tinymce.PluginManager.add('media', function(editor, url) {
	var urlPatterns = [
		{regex: /youtu\.be\/([\w\-.]+)/, type: 'iframe', w: 560, h: 314, url: '//www.youtube.com/embed/$1', allowFullscreen: true},
		{regex: /youtube\.com(.+)v=([^&]+)/, type: 'iframe', w: 560, h: 314, url: '//www.youtube.com/embed/$2', allowFullscreen: true},
		{regex: /youtube.com\/embed\/([a-z0-9\-_]+)/i, type: 'iframe', w: 560, h: 314, url: '//www.youtube.com/embed/$1', allowFullscreen: true},
		{regex: /vimeo\.com\/([0-9]+)/, type: 'iframe', w: 425, h: 350, url: '//player.vimeo.com/video/$1?title=0&byline=0&portrait=0&color=8dc7dc', allowfullscreen: true},
		{regex: /vimeo\.com\/(.*)\/([0-9]+)/, type: "iframe", w: 425, h: 350, url: "//player.vimeo.com/video/$2?title=0&amp;byline=0", allowfullscreen: true},
		{regex: /maps\.google\.([a-z]{2,3})\/maps\/(.+)msid=(.+)/, type: 'iframe', w: 425, h: 350, url: '//maps.google.com/maps/ms?msid=$2&output=embed"', allowFullscreen: false}
	];

	var embedChange = (tinymce.Env.ie && tinymce.Env.ie <= 8) ? 'onChange' : 'onInput';

	function guessMime(url) {
		url = url.toLowerCase();

		if (url.indexOf('.mp3') != -1) {
			return 'audio/mpeg';
		}

		if (url.indexOf('.wav') != -1) {
			return 'audio/wav';
		}

		if (url.indexOf('.mp4') != -1) {
			return 'video/mp4';
		}

		if (url.indexOf('.webm') != -1) {
			return 'video/webm';
		}

		if (url.indexOf('.ogg') != -1) {
			return 'video/ogg';
		}

		if (url.indexOf('.swf') != -1) {
			return 'application/x-shockwave-flash';
		}

		return '';
	}

	function getVideoScriptMatch(src) {
		var prefixes = editor.settings.media_scripts;

		if (prefixes) {
			for (var i = 0; i < prefixes.length; i++) {
				if (src.indexOf(prefixes[i].filter) !== -1) {
					return prefixes[i];
				}
			}
		}
	}

	function showDialog() {
		var win, width, height, data;

		var generalFormItems = [
			{
				name: 'source1',
				type: 'filepicker',
				filetype: 'media',
				size: 40,
				autofocus: true,
				label: 'Source',
				onchange: function(e) {
					tinymce.each(e.meta, function(value, key) {
						win.find('#' + key).value(value);
					});
				}
			}
		];

		function recalcSize(e) {
			var widthCtrl, heightCtrl, newWidth, newHeight;

			widthCtrl = win.find('#width')[0];
			heightCtrl = win.find('#height')[0];

			newWidth = widthCtrl.value();
			newHeight = heightCtrl.value();

			if (win.find('#constrain')[0].checked() && width && height && newWidth && newHeight) {
				if (e.control == widthCtrl) {
					newHeight = Math.round((newWidth / width) * newHeight);

					if (!isNaN(newHeight)) {
						heightCtrl.value(newHeight);
					}
				} else {
					newWidth = Math.round((newHeight / height) * newWidth);

					if (!isNaN(newWidth)) {
						widthCtrl.value(newWidth);
					}
				}
			}

			width = newWidth;
			height = newHeight;
		}

		if (editor.settings.media_alt_source !== false) {
			generalFormItems.push({name: 'source2', type: 'filepicker', filetype: 'media', size: 40, label: 'Alternative source'});
		}

		if (editor.settings.media_poster !== false) {
			generalFormItems.push({name: 'poster', type: 'filepicker', filetype: 'image', size: 40, label: 'Poster'});
		}

		if (editor.settings.media_dimensions !== false) {
			generalFormItems.push({
				type: 'container',
				label: 'Dimensions',
				layout: 'flex',
				align: 'center',
				spacing: 5,
				items: [
					{name: 'width', type: 'textbox', maxLength: 5, size: 3, onchange: recalcSize, ariaLabel: 'Width'},
					{type: 'label', text: 'x'},
					{name: 'height', type: 'textbox', maxLength: 5, size: 3, onchange: recalcSize, ariaLabel: 'Height'},
					{name: 'constrain', type: 'checkbox', checked: true, text: 'Constrain proportions'}
				]
			});
		}

		data = getData(editor.selection.getNode());
		width = data.width;
		height = data.height;

		var embedTextBox = {
			id: 'mcemediasource',
			type: 'textbox',
			flex: 1,
			name: 'embed',
			value: getSource(),
			multiline: true,
			label: 'Source'
		};

		function updateValueOnChange() {
			data = htmlToData(this.value());
			this.parent().parent().fromJSON(data);
		}

		embedTextBox[embedChange] = updateValueOnChange;

		win = editor.windowManager.open({
			title: 'Insert/edit video',
			data: data,
			bodyType: 'tabpanel',
			body: [
				{
					title: 'General',
					type: "form",
					onShowTab: function() {
						data = htmlToData(this.next().find('#embed').value());
						this.fromJSON(data);
					},
					items: generalFormItems
				},

				{
					title: 'Embed',
					type: "container",
					layout: 'flex',
					direction: 'column',
					align: 'stretch',
					padding: 10,
					spacing: 10,
					onShowTab: function() {
						this.find('#embed').value(dataToHtml(this.parent().toJSON()));
					},
					items: [
						{
							type: 'label',
							text: 'Paste your embed code below:',
							forId: 'mcemediasource'
						},
						embedTextBox
					]
				}
			],
			onSubmit: function() {
				var beforeObjects, afterObjects, i, y;

				beforeObjects = editor.dom.select('img[data-mce-object]');
				editor.insertContent(dataToHtml(this.toJSON()));
				afterObjects = editor.dom.select('img[data-mce-object]');

				// Find new image placeholder so we can select it
				for (i = 0; i < beforeObjects.length; i++) {
					for (y = afterObjects.length - 1; y >= 0; y--) {
						if (beforeObjects[i] == afterObjects[y]) {
							afterObjects.splice(y, 1);
						}
					}
				}

				editor.selection.select(afterObjects[0]);
				editor.nodeChanged();
			}
		});
	}

	function getSource() {
		var elm = editor.selection.getNode();

		if (elm.getAttribute('data-mce-object')) {
			return editor.selection.getContent();
		}
	}

	function dataToHtml(data) {
		var html = '';

		if (!data.source1) {
			tinymce.extend(data, htmlToData(data.embed));
			if (!data.source1) {
				return '';
			}
		}

		if (!data.source2) {
			data.source2 = '';
		}

		if (!data.poster) {
			data.poster = '';
		}

		data.source1 = editor.convertURL(data.source1, "source");
		data.source2 = editor.convertURL(data.source2, "source");
		data.source1mime = guessMime(data.source1);
		data.source2mime = guessMime(data.source2);
		data.poster = editor.convertURL(data.poster, "poster");
		data.flashPlayerUrl = editor.convertURL(url + '/moxieplayer.swf', "movie");

		tinymce.each(urlPatterns, function(pattern) {
			var match, i, url;

			if ((match = pattern.regex.exec(data.source1))) {
				url = pattern.url;

				for (i = 0; match[i]; i++) {
					/*jshint loopfunc:true*/
					/*eslint no-loop-func:0 */
					url = url.replace('$' + i, function() {
						return match[i];
					});
				}

				data.source1 = url;
				data.type = pattern.type;
				data.allowFullscreen = pattern.allowFullscreen;
				data.width = data.width || pattern.w;
				data.height = data.height || pattern.h;
			}
		});

		if (data.embed) {
			html = updateHtml(data.embed, data, true);
		} else {
			var videoScript = getVideoScriptMatch(data.source1);
			if (videoScript) {
				data.type = 'script';
				data.width = videoScript.width;
				data.height = videoScript.height;
			}

			data.width = data.width || 300;
			data.height = data.height || 150;

			tinymce.each(data, function(value, key) {
				data[key] = editor.dom.encode(value);
			});

			if (data.type == "iframe") {
				var allowFullscreen = data.allowFullscreen ? ' allowFullscreen="1"' : '';
				html += '<iframe src="' + data.source1 + '" width="' + data.width + '" height="' + data.height + '"' + allowFullscreen + '></iframe>';
			} else if (data.source1mime == "application/x-shockwave-flash") {
				html += '<object data="' + data.source1 + '" width="' + data.width + '" height="' + data.height + '" type="application/x-shockwave-flash">';

				if (data.poster) {
					html += '<img src="' + data.poster + '" width="' + data.width + '" height="' + data.height + '" />';
				}

				html += '</object>';
			} else if (data.source1mime.indexOf('audio') != -1) {
				if (editor.settings.audio_template_callback) {
					html = editor.settings.audio_template_callback(data);
				} else {
					html += (
						'<audio controls="controls" src="' + data.source1 + '">' +
							(data.source2 ? '\n<source src="' + data.source2 + '"' + (data.source2mime ? ' type="' + data.source2mime + '"' : '') + ' />\n' : '') +
						'</audio>'
					);
				}
			} else if (data.type == "script") {
				html += '<script src="' + data.source1 + '"></script>';
			} else {
				if (editor.settings.video_template_callback) {
					html = editor.settings.video_template_callback(data);
				} else {
					html = (
						'<video width="' + data.width + '" height="' + data.height + '"' + (data.poster ? ' poster="' + data.poster + '"' : '') + ' controls="controls">\n' +
							'<source src="' + data.source1 + '"' + (data.source1mime ? ' type="' + data.source1mime + '"' : '') + ' />\n' +
							(data.source2 ? '<source src="' + data.source2 + '"' + (data.source2mime ? ' type="' + data.source2mime + '"' : '') + ' />\n' : '') +
						'</video>'
					);
				}
			}
		}

		return html;
	}

	function htmlToData(html) {
		var data = {};

		new tinymce.html.SaxParser({
			validate: false,
			allow_conditional_comments: true,
			special: 'script,noscript',
			start: function(name, attrs) {
				if (!data.source1 && name == "param") {
					data.source1 = attrs.map.movie;
				}

				if (name == "iframe" || name == "object" || name == "embed" || name == "video" || name == "audio") {
					if (!data.type) {
						data.type = name;
					}

					data = tinymce.extend(attrs.map, data);
				}

				if (name == "script") {
					var videoScript = getVideoScriptMatch(attrs.map.src);
					if (!videoScript) {
						return;
					}

					data = {
						type: "script",
						source1: attrs.map.src,
						width: videoScript.width,
						height: videoScript.height
					};
				}

				if (name == "source") {
					if (!data.source1) {
						data.source1 = attrs.map.src;
					} else if (!data.source2) {
						data.source2 = attrs.map.src;
					}
				}

				if (name == "img" && !data.poster) {
					data.poster = attrs.map.src;
				}
			}
		}).parse(html);

		data.source1 = data.source1 || data.src || data.data;
		data.source2 = data.source2 || '';
		data.poster = data.poster || '';

		return data;
	}

	function getData(element) {
		if (element.getAttribute('data-mce-object')) {
			return htmlToData(editor.serializer.serialize(element, {selection: true}));
		}

		return {};
	}

	function sanitize(html) {
		if (editor.settings.media_filter_html === false) {
			return html;
		}

		var writer = new tinymce.html.Writer(), blocked;

		new tinymce.html.SaxParser({
			validate: false,
			allow_conditional_comments: false,
			special: 'script,noscript',

			comment: function(text) {
				writer.comment(text);
			},

			cdata: function(text) {
				writer.cdata(text);
			},

			text: function(text, raw) {
				writer.text(text, raw);
			},

			start: function(name, attrs, empty) {
				blocked = true;

				if (name == 'script' || name == 'noscript') {
					return;
				}

				for (var i = 0; i < attrs.length; i++) {
					if (attrs[i].name.indexOf('on') === 0) {
						return;
					}

					if (attrs[i].name == 'style') {
						attrs[i].value = editor.dom.serializeStyle(editor.dom.parseStyle(attrs[i].value), name);
					}
				}

				writer.start(name, attrs, empty);
				blocked = false;
			},

			end: function(name) {
				if (blocked) {
					return;
				}

				writer.end(name);
			}
		}, new tinymce.html.Schema({})).parse(html);

		return writer.getContent();
	}

	function updateHtml(html, data, updateAll) {
		var writer = new tinymce.html.Writer();
		var sourceCount = 0, hasImage;

		function setAttributes(attrs, updatedAttrs) {
			var name, i, value, attr;

			for (name in updatedAttrs) {
				value = "" + updatedAttrs[name];

				if (attrs.map[name]) {
					i = attrs.length;
					while (i--) {
						attr = attrs[i];

						if (attr.name == name) {
							if (value) {
								attrs.map[name] = value;
								attr.value = value;
							} else {
								delete attrs.map[name];
								attrs.splice(i, 1);
							}
						}
					}
				} else if (value) {
					attrs.push({
						name: name,
						value: value
					});

					attrs.map[name] = value;
				}
			}
		}

		new tinymce.html.SaxParser({
			validate: false,
			allow_conditional_comments: true,
			special: 'script,noscript',

			comment: function(text) {
				writer.comment(text);
			},

			cdata: function(text) {
				writer.cdata(text);
			},

			text: function(text, raw) {
				writer.text(text, raw);
			},

			start: function(name, attrs, empty) {
				switch (name) {
					case "video":
					case "object":
					case "embed":
					case "img":
					case "iframe":
						setAttributes(attrs, {
							width: data.width,
							height: data.height
						});
						break;
				}

				if (updateAll) {
					switch (name) {
						case "video":
							setAttributes(attrs, {
								poster: data.poster,
								src: ""
							});

							if (data.source2) {
								setAttributes(attrs, {
									src: ""
								});
							}
							break;

						case "iframe":
							setAttributes(attrs, {
								src: data.source1
							});
							break;

						case "source":
							sourceCount++;

							if (sourceCount <= 2) {
								setAttributes(attrs, {
									src: data["source" + sourceCount],
									type: data["source" + sourceCount + "mime"]
								});

								if (!data["source" + sourceCount]) {
									return;
								}
							}
							break;

						case "img":
							if (!data.poster) {
								return;
							}

							hasImage = true;
							break;
					}
				}

				writer.start(name, attrs, empty);
			},

			end: function(name) {
				if (name == "video" && updateAll) {
					for (var index = 1; index <= 2; index++) {
						if (data["source" + index]) {
							var attrs = [];
							attrs.map = {};

							if (sourceCount < index) {
								setAttributes(attrs, {
									src: data["source" + index],
									type: data["source" + index + "mime"]
								});

								writer.start("source", attrs, true);
							}
						}
					}
				}

				if (data.poster && name == "object" && updateAll && !hasImage) {
					var imgAttrs = [];
					imgAttrs.map = {};

					setAttributes(imgAttrs, {
						src: data.poster,
						width: data.width,
						height: data.height
					});

					writer.start("img", imgAttrs, true);
				}

				writer.end(name);
			}
		}, new tinymce.html.Schema({})).parse(html);

		return writer.getContent();
	}

	editor.on('ResolveName', function(e) {
		var name;

		if (e.target.nodeType == 1 && (name = e.target.getAttribute("data-mce-object"))) {
			e.name = name;
		}
	});

	function retainAttributesAndInnerHtml(sourceNode, targetNode) {
		var attrName, attrValue, attribs, ai, innerHtml;

		// Prefix all attributes except width, height and style since we
		// will add these to the placeholder
		attribs = sourceNode.attributes;
		ai = attribs.length;
		while (ai--) {
			attrName = attribs[ai].name;
			attrValue = attribs[ai].value;

			if (attrName !== "width" && attrName !== "height" && attrName !== "style") {
				if (attrName == "data" || attrName == "src") {
					attrValue = editor.convertURL(attrValue, attrName);
				}

				targetNode.attr('data-mce-p-' + attrName, attrValue);
			}
		}

		// Place the inner HTML contents inside an escaped attribute
		// This enables us to copy/paste the fake object
		innerHtml = sourceNode.firstChild && sourceNode.firstChild.value;
		if (innerHtml) {
			targetNode.attr("data-mce-html", escape(innerHtml));
			targetNode.firstChild = null;
		}
	}

	function createPlaceholderNode(node) {
		var placeHolder, name = node.name;

		placeHolder = new tinymce.html.Node('img', 1);
		placeHolder.shortEnded = true;

		retainAttributesAndInnerHtml(node, placeHolder);

		placeHolder.attr({
			width: node.attr('width') || "300",
			height: node.attr('height') || (name == "audio" ? "30" : "150"),
			style: node.attr('style'),
			src: tinymce.Env.transparentSrc,
			"data-mce-object": name,
			"class": "mce-object mce-object-" + name
		});

		return placeHolder;
	}

	function createPreviewNode(node) {
		var previewWrapper, previewNode, shimNode, name = node.name;

		previewWrapper = new tinymce.html.Node('span', 1);
		previewWrapper.attr({
			contentEditable: 'false',
			style: node.attr('style'),
			"data-mce-object": name,
			"class": "mce-preview-object mce-object-" + name
		});

		retainAttributesAndInnerHtml(node, previewWrapper);

		previewNode = new tinymce.html.Node(name, 1);
		previewNode.attr({
			src: node.attr('src'),
			allowfullscreen: node.attr('allowfullscreen'),
			width: node.attr('width') || "300",
			height: node.attr('height') || (name == "audio" ? "30" : "150"),
			frameborder: '0'
		});

		shimNode = new tinymce.html.Node('span', 1);
		shimNode.attr('class', 'mce-shim');

		previewWrapper.append(previewNode);
		previewWrapper.append(shimNode);

		return previewWrapper;
	}

	editor.on('preInit', function() {
		// Make sure that any messy HTML is retained inside these
		var specialElements = editor.schema.getSpecialElements();
		tinymce.each('video audio iframe object'.split(' '), function(name) {
			specialElements[name] = new RegExp('<\/' + name + '[^>]*>', 'gi');
		});

		// Allow elements
		//editor.schema.addValidElements('object[id|style|width|height|classid|codebase|*],embed[id|style|width|height|type|src|*],video[*],audio[*]');

		// Set allowFullscreen attribs as boolean
		var boolAttrs = editor.schema.getBoolAttrs();
		tinymce.each('webkitallowfullscreen mozallowfullscreen allowfullscreen'.split(' '), function(name) {
			boolAttrs[name] = {};
		});

		// Converts iframe, video etc into placeholder images
		editor.parser.addNodeFilter('iframe,video,audio,object,embed,script', function(nodes) {
			var i = nodes.length, node, placeHolder, videoScript;

			while (i--) {
				node = nodes[i];
				if (!node.parent) {
					continue;
				}

				if (node.parent.attr('data-mce-object')) {
					continue;
				}

				if (node.name == 'script') {
					videoScript = getVideoScriptMatch(node.attr('src'));
					if (!videoScript) {
						continue;
					}
				}

				if (videoScript) {
					if (videoScript.width) {
						node.attr('width', videoScript.width.toString());
					}

					if (videoScript.height) {
						node.attr('height', videoScript.height.toString());
					}
				}

				if (node.name == 'iframe' && editor.settings.media_live_embeds !== false && tinymce.Env.ceFalse) {
					placeHolder = createPreviewNode(node);
				} else {
					placeHolder = createPlaceholderNode(node);
				}

				node.replace(placeHolder);
			}
		});

		// Replaces placeholder images with real elements for video, object, iframe etc
		editor.serializer.addAttributeFilter('data-mce-object', function(nodes, name) {
			var i = nodes.length, node, realElm, ai, attribs, innerHtml, innerNode, realElmName, className;

			while (i--) {
				node = nodes[i];
				if (!node.parent) {
					continue;
				}

				realElmName = node.attr(name);
				realElm = new tinymce.html.Node(realElmName, 1);

				// Add width/height to everything but audio
				if (realElmName != "audio" && realElmName != "script") {
					className = node.attr('class');
					if (className && className.indexOf('mce-preview-object') !== -1) {
						realElm.attr({
							width: node.firstChild.attr('width'),
							height: node.firstChild.attr('height')
						});
					} else {
						realElm.attr({
							width: node.attr('width'),
							height: node.attr('height')
						});
					}
				}

				realElm.attr({
					style: node.attr('style')
				});

				// Unprefix all placeholder attributes
				attribs = node.attributes;
				ai = attribs.length;
				while (ai--) {
					var attrName = attribs[ai].name;

					if (attrName.indexOf('data-mce-p-') === 0) {
						realElm.attr(attrName.substr(11), attribs[ai].value);
					}
				}

				if (realElmName == "script") {
					realElm.attr('type', 'text/javascript');
				}

				// Inject innerhtml
				innerHtml = node.attr('data-mce-html');
				if (innerHtml) {
					innerNode = new tinymce.html.Node('#text', 3);
					innerNode.raw = true;
					innerNode.value = sanitize(unescape(innerHtml));
					realElm.append(innerNode);
				}

				node.replace(realElm);
			}
		});
	});

	editor.on('click keyup', function() {
		var selectedNode = editor.selection.getNode();

		if (selectedNode && editor.dom.hasClass(selectedNode, 'mce-preview-object')) {
			if (editor.dom.getAttrib(selectedNode, 'data-mce-selected')) {
				selectedNode.setAttribute('data-mce-selected', '2');
			}
		}
	});

	editor.on('ObjectSelected', function(e) {
		var objectType = e.target.getAttribute('data-mce-object');

		if (objectType == "audio" || objectType == "script") {
			e.preventDefault();
		}
	});

	editor.on('objectResized', function(e) {
		var target = e.target, html;

		if (target.getAttribute('data-mce-object')) {
			html = target.getAttribute('data-mce-html');
			if (html) {
				html = unescape(html);
				target.setAttribute('data-mce-html', escape(
					updateHtml(html, {
						width: e.width,
						height: e.height
					})
				));
			}
		}
	});

	editor.addButton('media', {
		tooltip: 'Insert/edit video',
		onclick: showDialog,
		stateSelector: ['img[data-mce-object]', 'span[data-mce-object]']
	});

	editor.addMenuItem('media', {
		icon: 'media',
		text: 'Insert/edit video',
		onclick: showDialog,
		context: 'insert',
		prependToContext: true
	});

	editor.on('setContent', function() {
		// TODO: This shouldn't be needed there should be a way to mark bogus
		// elements so they are never removed except external save
		editor.$('span.mce-preview-object').each(function(index, elm) {
			var $elm = editor.$(elm);

			if ($elm.find('span.mce-shim', elm).length === 0) {
				$elm.append('<span class="mce-shim"></span>');
			}
		});
	});

	editor.addCommand('mceMedia', showDialog);

	this.showDialog = showDialog;
});
