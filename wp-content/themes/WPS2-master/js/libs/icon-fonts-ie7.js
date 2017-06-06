/* To avoid CSS expressions while still supporting IE 7 and IE 6, use this script */
/* The script tag referencing this file must be placed before the ending body tag. */

/* Use conditional comments in order to target IE 7 and older:
	<!--[if lt IE 8]><!-->
	<script src="ie7/ie7.js"></script>
	<!--<![endif]-->
*/

(function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'CaGov\'">' + entity + '</span>' + html;
	}
	var icons = {
		'ca-gov-icon-logo': '&#xe600;',
		'ca-gov-icon-home': '&#xe601;',
		'ca-gov-icon-menu': '&#xe602;',
		'ca-gov-icon-apps': '&#xe603;',
		'ca-gov-icon-search': '&#xe604;',
		'ca-gov-icon-chat': '&#xe605;',
		'ca-gov-icon-capitol': '&#xe606;',
		'ca-gov-icon-state': '&#xe607;',
		'ca-gov-icon-phone': '&#xe608;',
		'ca-gov-icon-email': '&#xe609;',
		'ca-gov-icon-contact-us': '&#xe66e;',
		'ca-gov-icon-calendar': '&#xe60a;',
		'ca-gov-icon-bear': '&#xe60b;',
		'ca-gov-icon-chat-bubble': '&#xe66f;',
		'ca-gov-icon-info-bubble': '&#xe670;',
		'ca-gov-icon-share-button': '&#xe671;',
		'ca-gov-icon-share-facebook': '&#xe672;',
		'ca-gov-icon-share-email': '&#xe673;',
		'ca-gov-icon-share-flickr': '&#xe674;',
		'ca-gov-icon-share-twitter': '&#xe675;',
		'ca-gov-icon-share-linkedin': '&#xe676;',
		'ca-gov-icon-share-googleplus': '&#xe677;',
		'ca-gov-icon-share-instagram': '&#xe678;',
		'ca-gov-icon-share-pinterest': '&#xe679;',
		'ca-gov-icon-share-vimeo': '&#xe67a;',
		'ca-gov-icon-share-youtube': '&#xe67b;',
		'ca-gov-icon-law-enforcement': '&#xe60c;',
		'ca-gov-icon-justice-legal': '&#xe60d;',
		'ca-gov-icon-at-sign': '&#xe60e;',
		'ca-gov-icon-attachment': '&#xe60f;',
		'ca-gov-icon-zipped-file': '&#xe610;',
		'ca-gov-icon-powerpoint': '&#xe611;',
		'ca-gov-icon-excel': '&#xe612;',
		'ca-gov-icon-word': '&#xe613;',
		'ca-gov-icon-pdf': '&#xe614;',
		'ca-gov-icon-share': '&#xe615;',
		'ca-gov-icon-facebook': '&#xe616;',
		'ca-gov-icon-linkedin': '&#xe617;',
		'ca-gov-icon-youtube': '&#xe618;',
		'ca-gov-icon-twitter': '&#xe619;',
		'ca-gov-icon-pinterest': '&#xe61a;',
		'ca-gov-icon-vimeo': '&#xe61b;',
		'ca-gov-icon-instagram': '&#xe61c;',
		'ca-gov-icon-flickr': '&#xe61d;',
		'ca-gov-icon-google-plus': '&#xe66d;',
		'ca-gov-icon-microsoft': '&#xe61e;',
		'ca-gov-icon-apple': '&#xe61f;',
		'ca-gov-icon-android': '&#xe620;',
		'ca-gov-icon-computer': '&#xe621;',
		'ca-gov-icon-tablet': '&#xe622;',
		'ca-gov-icon-smartphone': '&#xe623;',
		'ca-gov-icon-roadways': '&#xe624;',
		'ca-gov-icon-travel-car': '&#xe625;',
		'ca-gov-icon-travel-air': '&#xe626;',
		'ca-gov-icon-truck-delivery': '&#xe627;',
		'ca-gov-icon-construction': '&#xe628;',
		'ca-gov-icon-bar-chart': '&#xe629;',
		'ca-gov-icon-pie-chart': '&#xe62a;',
		'ca-gov-icon-graph': '&#xe62b;',
		'ca-gov-icon-server': '&#xe62c;',
		'ca-gov-icon-download': '&#xe62d;',
		'ca-gov-icon-cloud-download': '&#xe62e;',
		'ca-gov-icon-cloud-upload': '&#xe62f;',
		'ca-gov-icon-shield': '&#xe630;',
		'ca-gov-icon-fire': '&#xe631;',
		'ca-gov-icon-binoculars': '&#xe632;',
		'ca-gov-icon-compass': '&#xe633;',
		'ca-gov-icon-sos': '&#xe634;',
		'ca-gov-icon-shopping-cart': '&#xe635;',
		'ca-gov-icon-video-camera': '&#xe636;',
		'ca-gov-icon-camera': '&#xe637;',
		'ca-gov-icon-green': '&#xe638;',
		'ca-gov-icon-loud-speaker': '&#xe639;',
		'ca-gov-icon-audio': '&#xe63a;',
		'ca-gov-icon-print': '&#xe63b;',
		'ca-gov-icon-medical': '&#xe63c;',
		'ca-gov-icon-zoom-out': '&#xe63d;',
		'ca-gov-icon-zoom-in': '&#xe63e;',
		'ca-gov-icon-important': '&#xe63f;',
		'ca-gov-icon-chat-bubbles': '&#xe640;',
		'ca-gov-icon-call': '&#xe641;',
		'ca-gov-icon-people': '&#xe642;',
		'ca-gov-icon-person': '&#xe643;',
		'ca-gov-icon-user-id': '&#xe644;',
		'ca-gov-icon-payment-card': '&#xe645;',
		'ca-gov-icon-skip-backwards': '&#xe646;',
		'ca-gov-icon-play': '&#xe647;',
		'ca-gov-icon-pause': '&#xe648;',
		'ca-gov-icon-skip-forward': '&#xe649;',
		'ca-gov-icon-mail': '&#xe64a;',
		'ca-gov-icon-image': '&#xe64b;',
		'ca-gov-icon-house': '&#xe64c;',
		'ca-gov-icon-gear': '&#xe64d;',
		'ca-gov-icon-tool': '&#xe64e;',
		'ca-gov-icon-time': '&#xe64f;',
		'ca-gov-icon-cal': '&#xe650;',
		'ca-gov-icon-check-list': '&#xe651;',
		'ca-gov-icon-document': '&#xe652;',
		'ca-gov-icon-clipboard': '&#xe653;',
		'ca-gov-icon-page': '&#xe654;',
		'ca-gov-icon-read-book': '&#xe655;',
		'ca-gov-icon-cc-copyright': '&#xe656;',
		'ca-gov-icon-ca-capitol': '&#xe657;',
		'ca-gov-icon-ca-state': '&#xe658;',
		'ca-gov-icon-favorite': '&#xe659;',
		'ca-gov-icon-rss': '&#xe65a;',
		'ca-gov-icon-road-pin': '&#xe65b;',
		'ca-gov-icon-online-services': '&#xe65c;',
		'ca-gov-icon-link': '&#xe65d;',
		'ca-gov-icon-magnify-glass': '&#xe65e;',
		'ca-gov-icon-key': '&#xe65f;',
		'ca-gov-icon-lock': '&#xe660;',
		'ca-gov-icon-info': '&#xe661;',
		'ca-gov-icon-arrow-up': '&#xe04b;',
		'ca-gov-icon-arrow-down': '&#xe04c;',
		'ca-gov-icon-arrow-left': '&#xe04d;',
		'ca-gov-icon-arrow-right': '&#xe04e;',
		'ca-gov-icon-carousel-prev': '&#xe666;',
		'ca-gov-icon-carousel-next': '&#xe667;',
		'ca-gov-icon-arrow-prev': '&#xe668;',
		'ca-gov-icon-arrow-next': '&#xe669;',
		'ca-gov-icon-menu-toggle-closed': '&#xe66a;',
		'ca-gov-icon-menu-toggle-open': '&#xe66b;',
		'ca-gov-icon-carousel-play': '&#xe907;',
		'ca-gov-icon-carousel-pause': '&#xe66c;',
		'ca-gov-icon-search-right': '&#x55;',
		'ca-gov-icon-graduate': '&#xe903;',
		'ca-gov-icon-briefcase': '&#xe901;',
		'ca-gov-icon-images': '&#xe904;',
		'ca-gov-icon-gears': '&#xe900;',
		'ca-gov-icon-tools': '&#xe035;',
		'ca-gov-icon-pencil': '&#x6a;',
		'ca-gov-icon-pencil-edit': '&#x6c;',
		'ca-gov-icon-science': '&#xe00a;',
		'ca-gov-icon-film': '&#xe024;',
		'ca-gov-icon-table': '&#xe025;',
		'ca-gov-icon-flowchart': '&#xe0df;',
		'ca-gov-icon-building': '&#xe0fd;',
		'ca-gov-icon-searching': '&#xe0f7;',
		'ca-gov-icon-wallet': '&#xe0d8;',
		'ca-gov-icon-tags': '&#xe07c;',
		'ca-gov-icon-currency': '&#xe0f3;',
		'ca-gov-icon-idea': '&#xe902;',
		'ca-gov-icon-lightbulb': '&#xe072;',
		'ca-gov-icon-calculator': '&#xe0e7;',
		'ca-gov-icon-drive': '&#xe0e5;',
		'ca-gov-icon-globe': '&#xe0e3;',
		'ca-gov-icon-hourglass': '&#xe0e1;',
		'ca-gov-icon-mic': '&#xe07f;',
		'ca-gov-icon-volume': '&#xe069;',
		'ca-gov-icon-music': '&#xe08e;',
		'ca-gov-icon-folder': '&#xe05c;',
		'ca-gov-icon-grid': '&#xe08c;',
		'ca-gov-icon-archive': '&#xe088;',
		'ca-gov-icon-contacts': '&#xe087;',
		'ca-gov-icon-book': '&#xe086;',
		'ca-gov-icon-drawer': '&#xe084;',
		'ca-gov-icon-map': '&#xe083;',
		'ca-gov-icon-pushpin': '&#xe082;',
		'ca-gov-icon-location': '&#xe081;',
		'ca-gov-icon-quote-fill': '&#xe06a;',
		'ca-gov-icon-question-fill': '&#xe064;',
		'ca-gov-icon-warning-triangle': '&#xe063;',
		'ca-gov-icon-warning-fill': '&#xe062;',
		'ca-gov-icon-check-fill': '&#xe052;',
		'ca-gov-icon-close-fill': '&#xe051;',
		'ca-gov-icon-plus-fill': '&#xe050;',
		'ca-gov-icon-minus-fill': '&#xe04f;',
		'ca-gov-icon-caret-fill-right': '&#xe046;',
		'ca-gov-icon-caret-fill-left': '&#xe045;',
		'ca-gov-icon-caret-fill-down': '&#xe044;',
		'ca-gov-icon-caret-fill-up': '&#xe043;',
		'ca-gov-icon-caret-fill-two-right': '&#xe04a;',
		'ca-gov-icon-caret-fill-two-left': '&#xe049;',
		'ca-gov-icon-caret-fill-two-down': '&#xe048;',
		'ca-gov-icon-caret-fill-two-up': '&#xe047;',
		'ca-gov-icon-arrow-fill-right': '&#xe03c;',
		'ca-gov-icon-arrow-fill-left': '&#xe03b;',
		'ca-gov-icon-arrow-fill-down': '&#xe03a;',
		'ca-gov-icon-arrow-fill-up': '&#xe039;',
		'ca-gov-icon-arrow-fill-left-down': '&#xe040;',
		'ca-gov-icon-arrow-fill-right-down': '&#xe03f;',
		'ca-gov-icon-arrow-fill-right-up': '&#xe03e;',
		'ca-gov-icon-arrow-fill-left-up': '&#xe03d;',
		'ca-gov-icon-triangle-line-right': '&#x49;',
		'ca-gov-icon-triangle-line-left': '&#x48;',
		'ca-gov-icon-triangle-line-up': '&#x46;',
		'ca-gov-icon-triangle-line-down': '&#x47;',
		'ca-gov-icon-caret-line-two-right': '&#x41;',
		'ca-gov-icon-caret-line-two-left': '&#x40;',
		'ca-gov-icon-caret-line-two-down': '&#x3f;',
		'ca-gov-icon-caret-line-two-up': '&#x3e;',
		'ca-gov-icon-caret-line-right': '&#x3d;',
		'ca-gov-icon-caret-line-left': '&#x3c;',
		'ca-gov-icon-caret-line-up': '&#x3a;',
		'ca-gov-icon-caret-line-down': '&#x3b;',
		'ca-gov-icon-important-line': '&#xe906;',
		'ca-gov-icon-info-line': '&#xe905;',
		'ca-gov-icon-check-line': '&#x52;',
		'ca-gov-icon-question-line': '&#xe908;',
		'ca-gov-icon-close-line': '&#x51;',
		'ca-gov-icon-plus-line': '&#x50;',
		'ca-gov-icon-minus-line': '&#x4f;',
		'ca-gov-icon-question': '&#xe909;',
		'ca-gov-icon-minus-mark': '&#x4b;',
		'ca-gov-icon-plus-mark': '&#x4c;',
		'ca-gov-icon-collapse': '&#x58;',
		'ca-gov-icon-expand': '&#x59;',
		'ca-gov-icon-check-mark': '&#x4e;',
		'ca-gov-icon-close-mark': '&#x4d;',
		'ca-gov-icon-triangle-right': '&#x45;',
		'ca-gov-icon-triangle-left': '&#x44;',
		'ca-gov-icon-triangle-down': '&#x43;',
		'ca-gov-icon-triangle-up': '&#x42;',
		'ca-gov-icon-caret-two-right': '&#x39;',
		'ca-gov-icon-caret-two-left': '&#x38;',
		'ca-gov-icon-caret-two-down': '&#x37;',
		'ca-gov-icon-caret-two-up': '&#x36;',
		'ca-gov-icon-caret-right': '&#x35;',
		'ca-gov-icon-caret-left': '&#x34;',
		'ca-gov-icon-caret-up': '&#x32;',
		'ca-gov-icon-caret-down': '&#x33;',
		'ca-gov-icon-filter': '&#xe90a;',
		'0': 0
		},
		els = document.getElementsByTagName('*'),
		i, c, el;
	for (i = 0; ; i += 1) {
		el = els[i];
		if(!el) {
			break;
		}
		c = el.className;
		c = c.match(/ca-gov-icon-[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
}());
