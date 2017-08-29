/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {
	var $style = $( '#noteblog-color-scheme-css' ),
		api = wp.customize;

	if ( ! $style.length ) {
		$style = $( 'head' ).append( '<style type="text/css" id="noteblog-color-scheme-css" />' )
		                    .find( '#noteblog-color-scheme-css' );
	}

		
	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.navbar-brand' ).text( to );
		} );
	} );
	// Header text color.
	wp.customize( 'header_textcolor', function( value ) {
		value.bind( function( to ) {
			if ( 'blank' === to ) {
				$( '.frontpage-site-title,' ).css( {
					'clip': 'rect(1px, 1px, 1px, 1px)',
					'position': 'absolute'
				} );
			} else {
				$( '.frontpage-site-title,' ).css( {
					'clip': 'auto',
					'color': to,
					'position': 'relative'
				} );
			}
		} );
	} );

	wp.customize( 'header_bg_color', function( value ) {
		value.bind( function( to ) {
			$( '.site-header' ).css( {
				'background':to
			});
		} );
	} );

	wp.customize( 'footer_colors', function( value ) {
		value.bind( function( to ) {
			$( '.footer-widget-wrapper' ).css( {
				'background':to
			});
		} );
	} );



	wp.customize( 'footer_widget_link_colors', function( value ) {
		value.bind( function( to ) {
			$( '.footer-widgets a, .footer-widgets li a' ).css( {
				'color':to
			});
		} );
	} );



	wp.customize( 'footer_widget_text_colors', function( value ) {
		value.bind( function( to ) {
			$( '.footer-widgets, .footer-widgets p' ).css( {
				'color':to
			});
		} );
	} );



		wp.customize( 'footer_widget_title_colors', function( value ) {
		value.bind( function( to ) {
			$( '.footer-widgets h3' ).css( {
				'color':to
			});
		} );
	} );

		wp.customize( 'footer_widget_title_colors', function( value ) {
		value.bind( function( to ) {
			$( '.footer-widgets h3' ).css( {
				'color':to
			});
		} );
	} );

		wp.customize( 'header_image_text_color', function( value ) {
		value.bind( function( to ) {
			$( '.frontpage-site-title' ).css( {
				'color':to
			});
		} );
	} );

		wp.customize( 'background_elements_color', function( value ) {
		value.bind( function( to ) {
			$( 'body, #secondary h4.widget-title ' ).css( {
				'background-color':to
			});
		} );
	} );


		wp.customize( 'navigation_frontpage_logo_color', function( value ) {
		value.bind( function( to ) {
			$( '.home .lh-nav-bg-transform.navbar-default .navbar-brand, .home .lh-nav-bg-transform.navbar-default .navbar-brand:hover, .home .lh-nav-bg-transform.navbar-default .navbar-brand:active, .home .lh-nav-bg-transform.navbar-default .navbar-brand:focus, .home .lh-nav-bg-transform.navbar-default .navbar-brand:hover' ).css( {
				'color':to
			});
		} );
	} );


				wp.customize( 'navigation_frontpage_menu_color', function( value ) {
		value.bind( function( to ) {
			$( '.home .lh-nav-bg-transform .navbar-nav>li>a, .home .lh-nav-bg-transform .navbar-nav>li>a:hover, .home .lh-nav-bg-transform .navbar-nav>li>a:active, .home .lh-nav-bg-transform .navbar-nav>li>a:focus, .home .lh-nav-bg-transform .navbar-nav>li>a:visited' ).css( {
				'color':to
			});
		} );
	} );


		wp.customize( 'footer_copyright_background_color', function( value ) {
		value.bind( function( to ) {
			$( '.site-footer' ).css( {
				'background':to
			});
		} );
	} );


		wp.customize( 'footer_copyright_border_color', function( value ) {
		value.bind( function( to ) {
			$( '.copy-right-section' ).css( {
				'border-top-color':to
			});
		} );
	} );


		wp.customize( 'footer_copyright_text_color', function( value ) {
		value.bind( function( to ) {
			$( '.copy-right-section' ).css( {
				'color':to
			});
		} );
	} );
		wp.customize( 'sidebar_headline_colors', function( value ) {
		value.bind( function( to ) {
			$( '#secondary h3.widget-title, #secondary h4.widget-title' ).css( {
				'color':to
			});
		} );
	} );
		wp.customize( 'sidebar_background_color', function( value ) {
		value.bind( function( to ) {
			$( '.secondary-inner' ).css( {
				'background':to
			});
		} );
	} );
		wp.customize( 'sidebar_link_color', function( value ) {
		value.bind( function( to ) {
			$( '#secondary .widget a, #secondary .widget #recentcomments a, #secondary .widget .rsswidget' ).css( {
				'color':to
			});
		} );
	} );

		wp.customize( 'header_image_tagline_color', function( value ) {
		value.bind( function( to ) {
			$( '.frontpage-site-description' ).css( {
				'color':to
			});
		} );
	} );

		wp.customize( 'sidebar_link_border_color', function( value ) {
		value.bind( function( to ) {
			$( '#secondary .widget li' ).css( {
				'border-color':to
			});
		} );
	} );
		wp.customize( 'navigation_background_color', function( value ) {
		value.bind( function( to ) {
			$( '.navbar-default,.navbar-default li>.dropdown-menu, .navbar-default .navbar-nav .open .dropdown-menu > .active > a, .navbar-default .navbar-nav .open .dr' ).css( {
				'background-color':to
			});
		} );
	} );

		wp.customize( 'navigation_text_color', function( value ) {
		value.bind( function( to ) {
			$( '.navbar-default .navbar-nav>li>a, .navbar-default li>.dropdown-menu>li>a, .navbar-default .navbar-nav>li>a:hover, .navbar-default .navbar-nav>li>a:focus, .navbar-default .navbar-nav>li>a:active, .navbar-default .navbar-nav>li>a:visited, .navbar-default .navbar-nav > .open > a, .navbar-default .navbar-nav > .open > a:hover, .navbar-default .navbar-nav > .open > a:focus' ).css( {
				'color':to
			});
		} );
	} );

		wp.customize( 'navigation_text_color', function( value ) {
		value.bind( function( to ) {
			$( '.navbar-default .navbar-nav > .active > a, .navbar-default .navbar-nav > .active > a:hover, .navbar-default .navbar-nav > .active > a:focus' ).css( {
				'color':to
			});
		} );
	} );

		wp.customize( 'navigation_text_color', function( value ) {
		value.bind( function( to ) {
			$( '.navbar-default .navbar-nav .open .dropdown-menu>li>a, .navbar-default .navbar-nav .open .dropdown-menu>li>a,:hover, .navbar-default .navbar-nav .open .dropdown-menu>li>a,:active, .navbar-default .navbar-nav .open .dropdown-menu>li>a,:focus, .navbar-default .navbar-nav .open .dropdown-menu>li>a,:visited, .home .lh-nav-bg-transform .navbar-nav>li>a, .home .lh-nav-bg-transform .navbar-nav>li>a:hover, .home .lh-nav-bg-transform .navbar-nav>li>a:visited, .home .lh-nav-bg-transform .navbar-nav>li>a:focus, .home .lh-nav-bg-transform .navbar-nav>li>a:active, .navbar-default .navbar-nav .open .dropdown-menu>li>a:active, .navbar-default .navbar-nav .open .dropdown-menu>li>a:focus, .navbar-default .navbar-nav .open .dropdown-menu>li>a:hover, .navbar-default .navbar-nav .open .dropdown-menu>li>a:visited, .navbar-default .navbar-nav .open .dropdown-menu > .active > a, .navbar-default .navbar-nav .open .dropdown-menu > .active > a:hover' ).css( {
				'color':to
			});
		} );
	} );
		wp.customize( 'navigation_text_color', function( value ) {
		value.bind( function( to ) {
			$( '.navbar-default .navbar-toggle .icon-bar, .navbar-default .navbar-toggle:focus .icon-bar, .navbar-default .navbar-toggle:hover .icon-bar' ).css( {
				'background':to
			});
		} );
	} );		
		wp.customize( 'navigation_text_color', function( value ) {
		value.bind( function( to ) {
			$( '.navbar-default .navbar-nav .open .dropdown-menu > li > a' ).css( {
				'border-left-color':to
			});
		} );
	} );		






		wp.customize( 'navigation_logo_color', function( value ) {
		value.bind( function( to ) {
			$( '.navbar-default .navbar-brand, .navbar-default .navbar-brand:hover, .navbar-default .navbar-brand:focus' ).css( {
				'color':to
			});
		} );
	} );



		wp.customize( 'navigation_background_color', function( value ) {
		value.bind( function( to ) {
			$( '.home .lh-nav-bg-transform' ).css( {
				'background-color':to
			});
		} );
	} );
		wp.customize( 'navigation_text_color', function( value ) {
		value.bind( function( to ) {
			$( '.navbar-default .navbar-nav .open .dropdown-menu>li>a, .home .lh-nav-bg-transform .navbar-nav>li>a' ).css( {
				'color':to
			});
		} );
	} );


		wp.customize( 'navigation_logo_color', function( value ) {
		value.bind( function( to ) {
			$( '.home .lh-nav-bg-transform.navbar-default .navbar-brand, .navbar-default .navbar-brand, .navbar-default .navbar-brand:hover, .navbar-default .navbar-brand:focus' ).css( {
				'color':to
			});
		} );
	} );

		wp.customize( 'headline_color', function( value ) {
		value.bind( function( to ) {
			$( 'h1.entry-title, .entry-header .entry-title a, .page .container article h2, .page .container article h3, .page .container article h4, .page .container article h5, .page .container article h6, .single article h1, .single article h2, .single article h3, .single article h4, .single article h5, .single article h6, .page .container article h1, .single article h1, .single h2.comments-title, .single .comment-respond h3#reply-title, .page h2.comments-title, .page .comment-respond h3#reply-title' ).css( {
				'color':to
			});
		} );
	} );
		wp.customize( 'post_content_color', function( value ) {
		value.bind( function( to ) {
			$( '.entry-content, .entry-summary, .single .entry-content, .page .entry-content, .single .entry-summary, .page .entry-summary, .page .post-feed-wrapper p, .single .post-feed-wrapper p, .single .post-comments, .page .post-comments, .single .post-comments p, .page .post-comments p, .single .next-article a p, .single .prev-article a p, .page .next-article a p, .page .prev-article a p, .single thead, .page thead' ).css( {
				'color':to
			});
		} );
	} );
		wp.customize( 'post_content_color', function( value ) {
		value.bind( function( to ) {
			$( '.post-feed-wrapper p, .single .entry-content, .page .entry-content, .single .entry-summary, .page .entry-summary, .page .post-feed-wrapper p, .single .post-feed-wrapper p, .single .post-comments, .page .post-comments, .single .post-comments p, .page .post-comments p, .single .next-article a p, .single .prev-article a p, .page .next-article a p, .page .prev-article a p, .single thead, .page thead' ).css( {
				'color':to
			});
		} );
	} );


		wp.customize( 'author_line_color', function( value ) {
		value.bind( function( to ) {
			$( '.page .container .entry-date, .single-post .container .entry-date, .single .comment-metadata time, .page .comment-metadata time' ).css( {
				'color':to
			});
		} );
	} );

			wp.customize( 'top_widget_background_color', function( value ) {
		value.bind( function( to ) {
			$( '.top-widgets' ).css( {
				'background':to
			});
		} );
	} );

			wp.customize( 'top_widget_title_color', function( value ) {
		value.bind( function( to ) {
			$( '.top-widgets h3' ).css( {
				'color':to
			});
		} );
	} );
			wp.customize( 'top_widget_text_color', function( value ) {
		value.bind( function( to ) {
			$( '.top-widgets, .top-widgets p' ).css( {
				'color':to
			});
		} );
	} );


			wp.customize( 'bottom_widget_background_color', function( value ) {
		value.bind( function( to ) {
			$( '.bottom-widgets' ).css( {
				'background':to
			});
		} );
	} );

			wp.customize( 'bottom_widget_title_color', function( value ) {
		value.bind( function( to ) {
			$( '.bottom-widgets h3' ).css( {
				'color':to
			});
		} );
	} );
			wp.customize( 'bottom_widget_text_color', function( value ) {
		value.bind( function( to ) {
			$( '.bottom-widgets, .bottom-widgets p' ).css( {
				'color':to
			});
		} );
	} );

			wp.customize( 'footer_widget_text_color', function( value ) {
		value.bind( function( to ) {
			$( '.footer-widgets, .footer-widgets p' ).css( {
				'color':to
			});
		} );
	} );


			wp.customize( 'post_feed_post_background', function( value ) {
		value.bind( function( to ) {
			$( '.article-grid-container article' ).css( {
				'background':to
			});
		} );
	} );
			wp.customize( 'post_feed_post_text', function( value ) {
		value.bind( function( to ) {
			$( '.article-grid-container .post-feed-wrapper p' ).css( {
				'color':to
			});
		} );
	} );
			wp.customize( 'post_feed_post_headline', function( value ) {
		value.bind( function( to ) {
			$( '.post-feed-wrapper .entry-header .entry-title a' ).css( {
				'color':to
			});
		} );
	} );
			wp.customize( 'post_feed_post_date_noimage', function( value ) {
		value.bind( function( to ) {
			$( '.article-grid-container h5.entry-date, .article-grid-container h5.entry-date a' ).css( {
				'color':to
			});
		} );
	} );
			wp.customize( 'post_feed_post_date_withimage', function( value ) {
		value.bind( function( to ) {
			$( '.article-grid-container .post-thumbnail-wrap .entry-date' ).css( {
				'color':to
			});
		} );
	} );

			wp.customize( 'post_feed_post_button', function( value ) {
		value.bind( function( to ) {
			$( '.blog .next-post a, .blog .prev-post a' ).css( {
				'background':to
			});
		} );
	} );

			wp.customize( 'post_feed_post_button_text', function( value ) {
		value.bind( function( to ) {
			$( '.blog .next-post a, .blog .prev-post a, .blog .next-post a i.fa, .blog .prev-post a i.fa, .blog .posts-navigation .next-post a:hover .fa, .blog .posts-navigation .prev-post a:hover .fa' ).css( {
				'color':to
			});
		} );
	} );


			wp.customize( 'post_link_color', function( value ) {
		value.bind( function( to ) {
			$( '.single .entry-content a, .page .entry-content a, .single .post-comments a, .page .post-comments a, .single .next-article a, .single .prev-article a, .page .next-article a, .page .prev-article a' ).css( {
				'color':to
			});
		} );
	} );

			wp.customize( 'post_background_color', function( value ) {
		value.bind( function( to ) {
			$( '.single .post-content, .page .post-content, .single .comments-area, .page .comments-area, .single .post-comments, .page .single-post-content, .single .post-comments .comments-area, .page .post-comments .comments-area, .single .next-article a, .single .prev-article a, .page .next-article a, .page .prev-article a, .page .post-comments' ).css( {
				'background':to
			});
		} );
	} );


			wp.customize( 'sidebar_text_color', function( value ) {
		value.bind( function( to ) {
			$( '#secondary, #secondary .widget, #secondary p' ).css( {
				'color':to
			});
		} );
	} );


	// Color Scheme CSS.
	api.bind( 'preview-ready', function() {
		api.preview.bind( 'update-color-scheme-css', function( css ) {
			$style.html( css );
		} );
	} );


} )( jQuery );

