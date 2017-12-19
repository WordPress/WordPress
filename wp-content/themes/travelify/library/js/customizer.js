/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {
	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title a' ).text( to );
		} );
	} );
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		} );
	} );
	// Header text color.
	wp.customize( 'header_textcolor', function( value ) {
		value.bind( function( to ) {
			if ( 'blank' == to ) {
				$( '.site-title, .site-description' ).css( {
					'clip': 'rect(1px, 1px, 1px, 1px)',
					'position': 'absolute'
				} );
			} else {
				$( '.site-title, .site-description' ).css( {
					'clip': 'auto',
					'color': to,
					'position': 'relative'
				} );
			}
		} );
	} );

	//  Menu color text color.
  wp.customize( 'travelify_menu_color', function( value ) {
      value.bind( function( to ) {
          $( '#main-nav' ).css( {
          	'background-color': to,
          	'border-color': to
          } );
      } );
  });

  // Menu hover color
  wp.customize( 'travelify_menu_hover_color', function( value ) {
      value.bind( function( to ) {
          $( '#main-nav a:hover,#main-nav ul li.current-menu-item a,#main-nav ul li.current_page_ancestor a,#main-nav ul li.current-menu-ancestor a,#main-nav ul li.current_page_item a,#main-nav ul li:hover > a, #main-nav li:hover > a,#main-nav ul ul :hover > a,#main-nav a:focus,#main-nav ul li ul li a:hover,#main-nav ul li ul li:hover > a,#main-nav ul li.current-menu-item ul li a:hover' ).css( {
          	'background-color': to
          } );
      } );
  });

  // Entry content text color
  wp.customize( 'travelify_entry_color', function( value ) {
      value.bind( function( to ) {
          $( '.entry-content' ).css( {
          	'color': to
          } );
      } );
  });

  // Element content text color
  wp.customize( 'travelify_element_color', function( value ) {
      value.bind( function( to ) {
          $( 'input[type="reset"], input[type="button"], input[type="submit"], a.readmore' ).css( {
          	'background-color': to,
          	'border-color': to
          } );
      } );
  });

  // Website title color
  wp.customize( 'travelify_logo_color', function( value ) {
      value.bind( function( to ) {
          $( '#site-title a' ).css( {
          	'color': to
          } );
      } );
  });

  // Header and Title color
  wp.customize( 'travelify_header_color', function( value ) {
      value.bind( function( to ) {
          $( '.entry-title, .entry-title a, .entry-title a:focus, h1, h2, h3, h4, h5, h6, .widget-title' ).css( {
          	'color': to
          } );
      } );
  });

  // Wrapper color
  wp.customize( 'travelify_wrapper_color', function( value ) {
      value.bind( function( to ) {
          $( '.wrapper' ).css( {
          	'background-color': to
          } );
      } );
  });

  // Content and widget background color
  wp.customize( 'travelify_content_bg_color', function( value ) {
      value.bind( function( to ) {
          $( '.widget, article' ).css( {
          	'background-color': to
          } );
      } );
  });

  // Menu item text color
  wp.customize( 'travelify_menu_item_color', function( value ) {
      value.bind( function( to ) {
          $( '#main-nav a, #main-nav a:hover, #main-nav a:focus, #main-nav ul li.current-menu-item a,#main-nav ul li.current_page_ancestor a,#main-nav ul li.current-menu-ancestor a,#main-nav ul li.current_page_item a,#main-nav ul li:hover > a' ).css( {
          	'color': to
          } );
      } );
  });
  
  // Header Logo Preview via 3 show options
  wp.customize( 'travelify_theme_options[header_show]', function( value ) {
      value.bind( function( to ) {
          var divLogo = jQuery("#site-logo");              
          siteTitle = ( wp.customize.instance( 'blogname').get() != '') ? wp.customize.instance( 'blogname').get() : '';
          siteDesc = ( wp.customize.instance( 'blogdescription').get() != '') ? wp.customize.instance( 'blogdescription').get() : '';
          var logo = wp.customize.instance( 'travelify_theme_options[header_logo]').get();
              
          if( to == 'header-logo' && logo != '' ){
              var html = '<h1 id="site-title"><a href="#" title="'+siteTitle+'" rel="home"><img src="'+logo+'" alt="'+siteTitle+'"></a></h1>';
              divLogo.empty().append(html);
          }
          else if( to == 'header-text' ){
              var html = '<h1 id="site-title"><a href="#" title="" rel="home">'+siteTitle+'</a></h1><h2 id="site-description">'+siteDesc+'</h2>';
              divLogo.empty().append(html);
          }
          else{
              divLogo.empty();                
          }
      } );
  });
  
  // Header Logo Preview
  wp.customize( 'travelify_theme_options[header_logo]', function( value ) {
      value.bind( function( to ) {
          showOptions = (wp.customize.instance( 'travelify_theme_options[header_show]').get() != '') ? wp.customize.instance( 'travelify_theme_options[header_show]').get() : '';
          siteTitle = ( wp.customize.instance( 'blogname').get() != '') ? wp.customize.instance( 'blogname').get() : '';
          siteDesc = ( wp.customize.instance( 'blogdescription').get() != '') ? wp.customize.instance( 'blogdescription').get() : '';
          var divLogo = jQuery("#site-logo");

          if( showOptions == 'header-logo' ){
            var html = '<h1 id="site-title"><a href="#" title="'+siteTitle+'" rel="home"><img src="'+to+'" alt="Logo"></a></h1>';
            divLogo.empty().append(html);
          }
      } );
  });
  
  wp.customize( 'travelify_theme_options[default_layout]', function( value ) {
      value.bind( function( to ) {
        var sidebar = jQuery( "#secondary" );
        var body = jQuery("body");
        var content = jQuery("#container > #content");
        var primary = jQuery("#primary");
        switch(to) {
            case 'no-sidebar':
                body.removeClass('right-sidebar-template left-sidebar-template one-column-template').addClass('no-sidebar-template');
                
                if( primary.length != 0 ){
                  primary.attr("id","content").removeClass("no-margin-left").css({"width" : "668px","margin" : "0 auto"});
                }
                else{
                    jQuery(".no-sidebar-template #content").removeClass("no-margin-left").css({"width" : "668px","margin" :"0 auto"});
                }
                sidebar.hide();
                break;
            case 'no-sidebar-full-width':
                body.removeClass('right-sidebar-template left-sidebar-template one-column-template no-sidebar-template')
                if( primary.length != 0 ){
                  primary.attr("id","content").removeAttr("style");
                }
                else{
                    jQuery("#content").removeAttr("style");
                }
                sidebar.hide();
                break;
            case 'no-sidebar-one-column':
                body.removeClass('right-sidebar-template left-sidebar-template no-sidebar-template').addClass("one-column-template");
                if( primary.length != 0 ){
                  primary.attr("id","content");
                }
                sidebar.hide();
                break;
            case 'left-sidebar':
                if( sidebar.length == 0){
                    jQuery("#container").append('<div class="no-margin-left" id="secondary">');
                }
                else{
                    sidebar.show();
                }
                body.removeClass('right-sidebar-template one-column-template no-sidebar-template').addClass("left-sidebar-template");
                if( content.length != 0 && primary.length == 0 ){
                  content.attr("id","primary").removeAttr("style");;
                }
                break;
            default:
                if( sidebar.length == 0){
                    jQuery("#container").append('<div id="secondary">');
                }
                else{
                    sidebar.removeClass('no-margin-left').show();
                }
                if( content.length != 0 ){
                    content.attr("id","primary").removeAttr("style").addClass('no-margin-left');
                }
                primary.addClass('no-margin-left');
                body.removeClass('left-sidebar-template one-column-template no-sidebar-template').addClass("right-sidebar-template");                
        } 
      });
  });
  
  
} )( jQuery );