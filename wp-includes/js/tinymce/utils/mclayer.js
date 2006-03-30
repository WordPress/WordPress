/**
 * $RCSfile: mclayer.js,v $
 * $Revision: 1.2 $
 * $Date: 2006/02/06 20:11:09 $
 *
 * Moxiecode floating layer script.
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2006, Moxiecode Systems AB, All rights reserved.
 */

function MCLayer(id) {
	this.id = id;
	this.settings = new Array();
	this.blockerElement = null;
	this.isMSIE = navigator.appName == "Microsoft Internet Explorer";
	this.events = false;
	this.autoHideCallback = null;
}

MCLayer.prototype = {
	moveRelativeTo : function(re, p, a) {
		var rep = this.getAbsPosition(re);
		var w = parseInt(re.offsetWidth);
		var h = parseInt(re.offsetHeight);
		var x, y;

		switch (p) {
			case "tl":
				break;

			case "tr":
				x = rep.absLeft + w;
				y = rep.absTop;
				break;

			case "bl":
				break;

			case "br":
				break;
		}

		this.moveTo(x, y);
	},

	moveBy : function(dx, dy) {
		var e = this.getElement();
		var x = parseInt(e.style.left);
		var y = parseInt(e.style.top);

		e.style.left = (x + dx) + "px";
		e.style.top = (y + dy) + "px";

		this.updateBlocker();
	},

	moveTo : function(x, y) {
		var e = this.getElement();

		e.style.left = x + "px";
		e.style.top = y + "px";

		this.updateBlocker();
	},

	show : function() {
		MCLayer.visibleLayer = this;

		this.getElement().style.display = 'block';
		this.updateBlocker();
	},

	hide : function() {
		this.getElement().style.display = 'none';
		this.updateBlocker();
	},

	setAutoHide : function(s, cb) {
		this.autoHideCallback = cb;
		this.registerEventHandlers();
	},

	getElement : function() {
		return document.getElementById(this.id);
	},

	updateBlocker : function() {
		if (!this.isMSIE)
			return;

		var e = this.getElement();
		var b = this.getBlocker();
		var x = this.parseInt(e.style.left);
		var y = this.parseInt(e.style.top);
		var w = this.parseInt(e.offsetWidth);
		var h = this.parseInt(e.offsetHeight);

		b.style.left = x + 'px';
		b.style.top = y + 'px';
		b.style.width = w + 'px';
		b.style.height = h + 'px';
		b.style.display = e.style.display;
	},

	getBlocker : function() {
		if (!this.blockerElement) {
			var d = document, b = d.createElement("iframe");

			b.style.cssText = 'display: none; left: 0px; position: absolute; top: 0';
			b.src = 'javascript:false;';
			b.frameBorder = '0';
			b.scrolling = 'no';

			d.body.appendChild(b);
			this.blockerElement = b;
		}

		return this.blockerElement;
	},

	getAbsPosition : function(n) {
		var p = {absLeft : 0, absTop : 0};

		while (n) {
			p.absLeft += n.offsetLeft;
			p.absTop += n.offsetTop;
			n = n.offsetParent;
		}

		return p;
	},

	registerEventHandlers : function() {
		if (!this.events) {
			var d = document;

			this.addEvent(d, 'mousedown', MCLayer.prototype.onMouseDown);

			this.events = true;
		}
	},

	addEvent : function(o, n, h) {
		if (o.attachEvent)
			o.attachEvent("on" + n, h);
		else
			o.addEventListener(n, h, false);
	},

	onMouseDown : function(e) {
		e = typeof(e) == "undefined" ? window.event : e;
		var b = document.body;
		var l = MCLayer.visibleLayer;

		if (l) {
			var mx = l.isMSIE ? e.clientX + b.scrollLeft : e.pageX;
			var my = l.isMSIE ? e.clientY + b.scrollTop : e.pageY;
			var el = l.getElement();
			var x = parseInt(el.style.left);
			var y = parseInt(el.style.top);
			var w = parseInt(el.offsetWidth);
			var h = parseInt(el.offsetHeight);

			if (!(mx > x && mx < x + w && my > y && my < y + h)) {
				MCLayer.visibleLayer = null;

				if (l.autoHideCallback && l.autoHideCallback(l, e, mx, my))
					return true;

				l.hide();
			}
		}
	},

	addCSSClass : function(e, c) {
		this.removeCSSClass(e, c);
		var a = this.explode(' ', e.className);
		a[a.length] = c;
		e.className = a.join(' ');
	},

	removeCSSClass : function(e, c) {
		var a = this.explode(' ', e.className), i;

		for (i=0; i<a.length; i++) {
			if (a[i] == c)
				a[i] = '';
		}

		e.className = a.join(' ');
	},

	explode : function(d, s) {
		var ar = s.split(d);
		var oar = new Array();

		for (var i = 0; i<ar.length; i++) {
			if (ar[i] != "")
				oar[oar.length] = ar[i];
		}

		return oar;
	},

	parseInt : function(s) {
		if (s == null || s == '')
			return 0;

		return parseInt(s);
	}
}