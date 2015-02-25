( function( window, document, href, pt_url ) {
	var encodeURI = window.encodeURIComponent,
		form = document.createElement( 'form' ),
		head = document.getElementsByTagName( 'head' )[0],
		img = new Image(),
		target = '_press_this_app',
		windowWidth, windowHeight,
		metas, links, content, imgs, ifrs,
		vid, selection;

	if ( ! pt_url ) {
		return;
	}

	if ( window.getSelection ) {
		selection = window.getSelection() + '';
	} else if ( document.getSelection ) {
		selection = document.getSelection() + '';
	} else if ( document.selection ) {
		selection = document.selection.createRange().text;
	}

	pt_url += ( pt_url.indexOf( '?' ) > -1 ? '&' : '?' ) + 'buster=' + ( new Date().getTime() );

	if ( document.title.length && document.title.length <= 512 ) {
		pt_url += '&t=' + encodeURI( document.title );
	}

	if ( selection && selection.length <= 512 ) {
		pt_url += '&s=' + encodeURI( selection );
	}

	if ( href.match( /^https?:/ ) ) {
		pt_url += '&u=' + encodeURI( href );
	} else {
		top.location.href = pt_url;
		return;
	}

	function add( name, value ) {
		if ( typeof value === 'undefined' ) {
			return;
		}

		var input = document.createElement( 'input' );

		input.name = name;
		input.value = value;
		input.type = 'hidden';

		form.appendChild( input );
	}

	if ( href.match( /\/\/www\.youtube\.com\/watch/ ) ) {
		add( '_embed[]', href );
	} else if ( href.match( /\/\/vimeo\.com\/(.+\/)?([\d]+)$/ ) ) {
		add( '_embed[]', href );
	} else if ( href.match( /\/\/(www\.)?dailymotion\.com\/video\/.+$/ ) ) {
		add( '_embed[]', href );
	} else if ( href.match( /\/\/soundcloud\.com\/.+$/ ) ) {
		add( '_embed[]', href );
	} else if ( href.match( /\/\/twitter\.com\/[^\/]+\/status\/[\d]+$/ ) ) {
		add( '_embed[]', href );
	} else if ( href.match( /\/\/vine\.co\/v\/[^\/]+/ ) ) {
		add( '_embed[]', href );
	}

	metas = head.getElementsByTagName( 'meta' ) || [];

	for ( var m = 0; m < metas.length; m++ ) {
		if ( m >= 50 ) {
			break;
		}

		var q = metas[ m ],
			q_name = q.getAttribute( 'name' ),
			q_prop = q.getAttribute( 'property' ),
			q_cont = q.getAttribute( 'content' );

		if ( q_name ) {
			add( '_meta[' + q_name + ']', q_cont );
		} else if ( q_prop ) {
			add( '_meta[' + q_prop + ']', q_cont );
		}
	}

	links = head.getElementsByTagName( 'link' ) || [];

	for ( var y = 0; y < links.length; y++ ) {
		if ( y >= 50 ) {
			break;
		}

		var g = links[ y ],
			g_rel = g.getAttribute( 'rel' );

		if ( g_rel ) {
			switch ( g_rel ) {
				case 'canonical':
				case 'icon':
				case 'shortlink':
					add( '_links[' + g_rel + ']', g.getAttribute( 'href' ) );
					break;
				case 'alternate':
					if ( 'application/json+oembed' === g.getAttribute( 'type' ) ) {
						add( '_links[' + g_rel + ']', g.getAttribute( 'href' ) );
					} else if ( 'handheld' === g.getAttribute( 'media' ) ) {
						add( '_links[' + g_rel + ']', g.getAttribute( 'href' ) );
					}
			}
		}
	}

	if ( document.body.getElementsByClassName ) {
		content = document.body.getElementsByClassName( 'hfeed' )[0];
	}

	content = document.getElementById( 'content' ) || content || document.body;
	imgs = content.getElementsByTagName( 'img' ) || [];

	for ( var n = 0; n < imgs.length; n++ ) {
		if ( n >= 100 ) {
			break;
		}

		if ( imgs[ n ].src.indexOf( 'avatar' ) > -1 || imgs[ n ].className.indexOf( 'avatar' ) > -1 ) {
			continue;
		}

		img.src = imgs[ n ].src;

		if ( img.width >= 256 && img.height >= 128 ) {
			add( '_img[]', img.src );
		}
	}

	ifrs = document.body.getElementsByTagName( 'iframe' ) || [];

	for ( var p = 0; p < ifrs.length; p++ ) {
		if ( p >= 100 ) {
			break;
		}

		vid = ifrs[ p ].src.match(/\/\/www\.youtube\.com\/embed\/([^\?]+)\?.+$/);

		if ( vid && 2 === vid.length ) {
			add( '_embed[]', 'https://www.youtube.com/watch?v=' + vid[1] );
		}

		vid = ifrs[ p ].src.match( /\/\/player\.vimeo\.com\/video\/([\d]+)$/ );

		if ( vid && 2 === vid.length ) {
			add( '_embed[]', 'https://vimeo.com/' + vid[1] );
		}

		vid = ifrs[ p ].src.match( /\/\/vine\.co\/v\/([^\/]+)\/embed/ );

		if ( vid && 2 === vid.length ) {
			add( '_embed[]', 'https://vine.co/v/' + vid[1] );
		}
	}

	if ( document.title && document.title > 512 ) {
		add( 't', document.title );
	}

	if ( selection && selection.length > 512 ) {
		add( 's', selection );
	}

	form.setAttribute( 'method', 'POST' );
	form.setAttribute( 'action', pt_url );
	form.setAttribute( 'target', target );
	form.setAttribute( 'style', 'display: none;' );

	windowWidth  = window.outerWidth || document.documentElement.clientWidth || 600;
	windowHeight = window.outerHeight || document.documentElement.clientHeight || 700;

	windowWidth = ( windowWidth < 800 || windowWidth > 5000 ) ? 600 : ( windowWidth * 0.7 );
	windowHeight = ( windowHeight < 800 || windowHeight > 3000 ) ? 700 : ( windowHeight * 0.9 );

	window.open( 'about:blank', target, 'width=' + windowWidth + ',height=' + windowHeight );

	document.body.appendChild( form );

	form.submit();
} )( window, document, top.location.href, window.pt_url );
