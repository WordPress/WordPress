/**
 * By Elementor Team
 */
( function( $ ) {
	window.ShareLink = function( element, userSettings ) {
		var $element,
			settings = {};

		var getNetworkNameFromClass = function( className ) {
			var classNamePrefix = className.substr( 0, settings.classPrefixLength );

			return classNamePrefix === settings.classPrefix ? className.substr( settings.classPrefixLength ) : null;
		};

		var bindShareClick = function( networkName ) {
			$element.on( 'click', function() {
				openShareLink( networkName );
			} );

			// Add "Enter" and "Space" event only if the element has role=button attribute.
			if ( 'button' === $element.attr( 'role' ) ) {
				$element.on( 'keyup', ( event ) => {
					if ( 13 === event.keyCode || 32 === event.keyCode ) {
						event.preventDefault();

						openShareLink( networkName );
					}
				} );
			}
		};

		var openShareLink = function( networkName ) {
			var shareWindowParams = '';

			if ( settings.width && settings.height ) {
				var shareWindowLeft = ( screen.width / 2 ) - ( settings.width / 2 ),
					shareWindowTop = ( screen.height / 2 ) - ( settings.height / 2 );

				shareWindowParams = 'toolbar=0,status=0,width=' + settings.width + ',height=' + settings.height + ',top=' + shareWindowTop + ',left=' + shareWindowLeft;
			}

			var link = ShareLink.getNetworkLink( networkName, settings ),
				isPlainLink = /^https?:\/\//.test( link ),
				windowName = isPlainLink ? '' : '_self';

			open( link, windowName, shareWindowParams );
		};

		var run = function() {
			$.each( element.classList, function() {
				var networkName = getNetworkNameFromClass( this );

				if ( networkName ) {
					bindShareClick( networkName );

					return false;
				}
			} );
		};

		var initSettings = function() {
			$.extend( settings, ShareLink.defaultSettings, userSettings );

			[ 'title', 'text' ].forEach( function( propertyName ) {
				settings[ propertyName ] = settings[ propertyName ].replace( '#', '' );
			} );

			settings.classPrefixLength = settings.classPrefix.length;
		};

		var initElements = function() {
			$element = $( element );
		};

		var init = function() {
			initSettings();

			initElements();

			run();
		};

		init();
	};

	ShareLink.networkTemplates = {
		twitter: 'https://twitter.com/intent/tweet?text={text}\x20{url}',
		'x-twitter': 'https://x.com/intent/tweet?text={text}\x20{url}',
		pinterest: 'https://www.pinterest.com/pin/create/button/?url={url}&media={image}',
		facebook: 'https://www.facebook.com/sharer.php?u={url}',
		threads: 'https://threads.net/intent/post?text={text}\x20{url}',
		vk: 'https://vkontakte.ru/share.php?url={url}&title={title}&description={text}&image={image}',
		linkedin: 'https://www.linkedin.com/shareArticle?mini=true&url={url}&title={title}&summary={text}&source={url}',
		odnoklassniki: 'https://connect.ok.ru/offer?url={url}&title={title}&imageUrl={image}',
		tumblr: 'https://tumblr.com/share/link?url={url}',
		google: 'https://plus.google.com/share?url={url}',
		digg: 'https://digg.com/submit?url={url}',
		reddit: 'https://reddit.com/submit?url={url}&title={title}',
		stumbleupon: 'https://www.stumbleupon.com/submit?url={url}',
		pocket: 'https://getpocket.com/edit?url={url}',
		whatsapp: 'https://api.whatsapp.com/send?text=*{title}*%0A{text}%0A{url}',
		xing: 'https://www.xing.com/spi/shares/new?url={url}',
		print: 'javascript:print()',
		email: 'mailto:?subject={title}&body={text}%0A{url}',
		telegram: 'https://telegram.me/share/url?url={url}&text={text}',
		skype: 'https://web.skype.com/share?url={url}',
	};

	ShareLink.defaultSettings = {
		title: '',
		text: '',
		image: '',
		url: location.href,
		classPrefix: 's_',
		width: 640,
		height: 480,
	};

	ShareLink.getNetworkLink = function( networkName, settings ) {
		var link = ShareLink.networkTemplates[ networkName ].replace( /{([^}]+)}/g, function( fullMatch, pureMatch ) {
			return settings[ pureMatch ] || '';
		} );

		if ( 'email' === networkName ) {
			if ( -1 < settings['title'].indexOf( '&' ) ||  -1 < settings['text'].indexOf( '&' ) ) {
				var emailSafeSettings = {
					text: settings['text'].replace( new RegExp('&', 'g'), '%26' ),
					title: settings['title'].replace( new RegExp('&', 'g'), '%26' ),
					url: settings['url'],
				};

				link = ShareLink.networkTemplates[ networkName ].replace( /{([^}]+)}/g, function( fullMatch, pureMatch ) {
					return emailSafeSettings[ pureMatch ];
				} );
			}

			if ( link.indexOf( '?subject=&body') ) {
				link = link.replace( 'subject=&', '' );
			}

			return link;
		}

		return link;
	};

	$.fn.shareLink = function( settings ) {
		return this.each( function() {
			$( this ).data( 'shareLink', new ShareLink( this, settings ) );
		} );
	};
} )( jQuery );
