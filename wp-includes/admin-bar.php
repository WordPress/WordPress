<?php
/**
 * Admin Bar
 *
 * This code handles the building and rendering of the press bar.
 */
 
/**
 * Instantiate the admin bar class and set it up as a global for access elsewhere.
 */
function wp_admin_bar_init() {
	global $current_user, $pagenow, $wp_admin_bar;

	/* Set the protocol constant used throughout this code */
	if ( !defined( 'PROTO' ) )
		if ( is_ssl() ) define( 'PROTO', 'https://' ); else define( 'PROTO', 'http://' );

	/* Don't load the admin bar if the user is not logged in, or we are using press this */
	if ( !is_user_logged_in() || 'press-this.php' == $pagenow || 'update.php' == $pagenow )
		return false;

	/* Set up the settings we need to render menu items */
	if ( !is_object( $current_user ) )
		$current_user = wp_get_current_user();

	/* Enqueue the JS files for the admin bar. */
	if ( is_user_logged_in() )
		wp_enqueue_script( 'jquery', false, false, false, true );
	
	/* Load the admin bar class code ready for instantiation */
	require( ABSPATH . WPINC . '/admin-bar/admin-bar-class.php' );

	/* Only load super admin menu code if the logged in user is a super admin */
	if ( is_super_admin() ) {
		require( ABSPATH . WPINC . '/admin-bar/admin-bar-debug.php' );
		require( ABSPATH . WPINC . '/admin-bar/admin-bar-superadmin.php' );
	}

	/* Initialize the admin bar */
	$wp_admin_bar = new wp_admin_bar();
}
add_action( 'init', 'wp_admin_bar_init' );

/**
 * Render the admin bar to the page based on the $wp_admin_bar->menu member var.
 * This is called very late on the footer actions so that it will render after anything else being
 * added to the footer.
 *
 * It includes the action "wp_before_admin_bar_render" which should be used to hook in and
 * add new menus to the admin bar. That way you can be sure that you are adding at most optimal point,
 * right before the admin bar is rendered. This also gives you access to the $post global, among others.
 */
function wp_admin_bar_render() {
	global $wp_admin_bar;

	if ( !is_object( $wp_admin_bar ) )
		return false;
		
	$wp_admin_bar->load_user_locale_translations();

	do_action( 'wp_before_admin_bar_render' );

	$wp_admin_bar->render();

	do_action( 'wp_after_admin_bar_render' );
	
	$wp_admin_bar->unload_user_locale_translations();
}
add_action( 'wp_footer', 'wp_admin_bar_render', 1000 );
add_action( 'admin_footer', 'wp_admin_bar_render', 1000 );

/**
 * Show the logged in user's gravatar as a separator.
 */
function wp_admin_bar_me_separator() {
	global $wp_admin_bar, $current_user;

	if ( !is_object( $wp_admin_bar ) )
		return false;

	$wp_admin_bar->add_menu( array( 'id' => 'me', 'title' => get_avatar( $current_user->ID, 16 ), 'href' => $wp_admin_bar->user->account_domain . 'wp-admin/profile.php' ) );
}
add_action( 'wp_before_admin_bar_render', 'wp_admin_bar_me_separator', 10 );

/**
 * Use the $wp_admin_bar global to add the "My Account" menu and all submenus.
 */
function wp_admin_bar_my_account_menu() {
	global $wp_admin_bar, $current_user;

	if ( !is_object( $wp_admin_bar ) )
		return false;

	/* Add the 'My Account' menu */
	$wp_admin_bar->add_menu( array( 'id' => 'my-account', 'title' => __( 'My Account' ), 'href' => admin_url('profile.php') ) );

	/* Add the "My Account" sub menus */
	$wp_admin_bar->add_menu( array( 'parent' => 'my-account', 'title' => __( 'Edit My Profile' ), 'href' => admin_url('profile.php') ) );
	$wp_admin_bar->add_menu( array( 'parent' => 'my-account', 'title' => __( 'Global Dashboard' ), 'href' => admin_url() ) );
	$wp_admin_bar->add_menu( array( 'parent' => 'my-account', 'title' => __( 'Log Out' ), 'href' => wp_logout_url() ) );
}
add_action( 'wp_before_admin_bar_render', 'wp_admin_bar_my_account_menu', 20 );

/**
 * Use the $wp_admin_bar global to add the "My Blogs/[Blog Name]" menu and all submenus.
 */
function wp_admin_bar_my_blogs_menu() {
	global $wpdb, $wp_admin_bar;

	if ( !is_object( $wp_admin_bar ) )
		return false;

	/* Add the 'My Dashboards' menu if the user has more than one blog. */
	if ( count( $wp_admin_bar->user->blogs ) > 1 ) {
		$wp_admin_bar->add_menu( array( 'id' => 'my-blogs', 'title' => __( 'My Blogs' ), 'href' => $wp_admin_bar->user->account_domain ) );

		$default = includes_url('images/wpmini-blue.png');

		$counter = 2;
		foreach ( (array) $wp_admin_bar->user->blogs as $blog ) {
			$blogdomain = preg_replace( '!^https?://!', '', $blog->siteurl );
			// @todo Replace with some favicon lookup.
			//$blavatar = '<img src="' . esc_url( blavatar_url( blavatar_domain( $blog->siteurl ), 'img', 16, $default ) ) . '" alt="Blavatar" width="16" height="16" />';
			$blavatar = '<img src="' . esc_url($default) . '" alt="Blavatar" width="16" height="16" />';;

			$marker = '';
			if ( strlen($blog->blogname) > 35 )
				$marker = '...';

			if ( empty( $blog->blogname ) )
				$blogname = $blog->domain;
			else
				$blogname = substr( $blog->blogname, 0, 35 ) . $marker;

			if ( !isset( $blog->visible ) || $blog->visible === true ) {
				$wp_admin_bar->add_menu( array( 'parent' => 'my-blogs', 'id' => 'blog-' . $blog->userblog_id, 'title' => $blavatar . $blogname, 'href' => constant( 'PROTO' ) . $blogdomain . '/wp-admin/' ) );
				$wp_admin_bar->add_menu( array( 'parent' => 'blog-' . $blog->userblog_id, 'id' => 'blog-' . $blog->userblog_id . '-d', 'title' => __( 'Dashboard' ), 'href' => constant( 'PROTO' ) . $blogdomain . '/wp-admin/' ) );
				$wp_admin_bar->add_menu( array( 'parent' => 'blog-' . $blog->userblog_id, 'id' => 'blog-' . $blog->userblog_id . '-n', 'title' => __( 'New Post' ), 'href' => constant( 'PROTO' ) . $blogdomain . '/wp-admin/post-new.php' ) );
				// @todo, stats plugins should add this:
				//$wp_admin_bar->add_menu( array( 'parent' => 'blog-' . $blog->userblog_id, 'id' => 'blog-' . $blog->userblog_id . '-s', 'title' => __( 'Blog Stats' ), 'href' => constant( 'PROTO' ) . $blogdomain . '/wp-admin/index.php?page=stats' ) );
				$wp_admin_bar->add_menu( array( 'parent' => 'blog-' . $blog->userblog_id, 'id' => 'blog-' . $blog->userblog_id . '-c', 'title' => __( 'Manage Comments' ), 'href' => constant( 'PROTO' ) . $blogdomain . '/wp-admin/edit-comments.php' ) );
				$wp_admin_bar->add_menu( array( 'parent' => 'blog-' . $blog->userblog_id, 'id' => 'blog-' . $blog->userblog_id . '-v', 'title' => __( 'Read Blog' ), 'href' => constant( 'PROTO' ) . $blogdomain ) );
			}
			$counter++;
		}

		/* Add the "Manage Blogs" menu item */
		// @todo, use dashboard blog.
		$wp_admin_bar->add_menu( array( 'parent' => 'my-blogs', 'id' => 'manage-blogs', 'title' => __( 'Manage Blogs' ), admin_url('my-sites.php') ) );

	/* Add the 'My Dashboard' menu if the user only has one blog. */
	} else {
		$wp_admin_bar->add_menu( array( 'id' => 'my-blogs', 'title' => __( 'My Blog' ), 'href' => $wp_admin_bar->user->account_domain ) );

		$wp_admin_bar->add_menu( array( 'parent' => 'my-blogs', 'id' => 'blog-1-d', 'title' => __( 'Dashboard' ), 'href' => admin_url() ) );
		$wp_admin_bar->add_menu( array( 'parent' => 'my-blogs', 'id' => 'blog-1-n', 'title' => __( 'New Post' ), 'href' => admin_url('post-new.php') ) );
		// @todo Stats plugins should add this.
		//$wp_admin_bar->add_menu( array( 'parent' => 'my-blogs', 'id' => 'blog-1-s', 'title' => __( 'Blog Stats' ), 'href' => admin_ur;('index.php?page=stats') ) );
		$wp_admin_bar->add_menu( array( 'parent' => 'my-blogs', 'id' => 'blog-1-c', 'title' => __( 'Manage Comments' ), 'href' => admin_url('edit-comments.php') ) );
		$wp_admin_bar->add_menu( array( 'parent' => 'my-blogs', 'id' => 'blog-1-v', 'title' => __( 'Read Blog' ), 'href' => home_url() ) );
	}
}
add_action( 'wp_before_admin_bar_render', 'wp_admin_bar_my_blogs_menu', 30 );

/**
 * Show the blavatar of the current blog as a separator.
 */
function wp_admin_bar_blog_separator() {
	global $wp_admin_bar, $current_user, $current_blog;

	if ( !is_object( $wp_admin_bar ) )
		return false;

	$default = includes_url('images/wpmini-blue.png');

	$wp_admin_bar->add_menu( array( 'id' => 'blog', 'title' => '<img class="avatar" src="' . $default . '" alt="' . __( 'Current blog avatar' ) . '" width="16" height="16" />', 'href' => home_url() ) );
}
add_action( 'wp_before_admin_bar_render', 'wp_admin_bar_blog_separator', 40 );

/**
 * Use the $wp_admin_bar global to add a menu for blog info, accessable to all users.
 */
function wp_admin_bar_bloginfo_menu() {
	global $wp_admin_bar;

	if ( !is_object( $wp_admin_bar ) )
		return false;

	/* Add the Blog Info menu */
	$wp_admin_bar->add_menu( array( 'id' => 'bloginfo', 'title' => __( 'Blog Info' ), 'href' => '' ) );

	$wp_admin_bar->add_menu( array( 'parent' => 'bloginfo', 'title' => __( 'Get Shortlink' ), 'href' => '', 'meta' => array( 'onclick' => 'javascript:function wpcomshort() { var url=document.location;var links=document.getElementsByTagName(&#39;link&#39;);var found=0;for(var i = 0, l; l = links[i]; i++){if(l.getAttribute(&#39;rel&#39;)==&#39;shortlink&#39;) {found=l.getAttribute(&#39;href&#39;);break;}}if (!found) {for (var i = 0; l = document.links[i]; i++) {if (l.getAttribute(&#39;rel&#39;) == &#39;shortlink&#39;) {found = l.getAttribute(&#39;href&#39;);break;}}}if (found) {prompt(&#39;URL:&#39;, found);} else {alert(&#39;No shortlink available for this page&#39;); } } wpcomshort(); return false;' ) ) );
}
add_action( 'wp_before_admin_bar_render', 'wp_admin_bar_bloginfo_menu', 50 );

/**
 * Use the $wp_admin_bar global to add the "Edit Post" menu when viewing a single post.
 */
function wp_admin_bar_edit_menu() {
	global $post, $wp_admin_bar;

	if ( !is_object( $wp_admin_bar ) )
		return false;

	if ( !is_single() && !is_page() )
		return false;

	if ( !$post_type_object = get_post_type_object( $post->post_type ) )
		return false;

	if ( !current_user_can( $post_type_object->cap->edit_post, $post->ID ) )
		return false;

	$wp_admin_bar->add_menu( array( 'id' => 'edit', 'title' => __( 'Edit' ), 'href' => get_edit_post_link( $post->ID ) ) );
}
add_action( 'wp_before_admin_bar_render', 'wp_admin_bar_edit_menu', 100 );

/**
 * Load up the CSS needed to render the admin bar nice and pretty.
 */
function wp_admin_bar_css() {
	global $pagenow, $wp_locale;

	if ( !is_user_logged_in() || 'press-this.php' == $pagenow || 'update.php' == $pagenow || 'media-upload.php' == $pagenow )
		return;

	$nobump = false;

	/* Wish we could use wp_enqueue_style() here, but it will not let us pass GET params to the stylesheet correctly. */
	?>
	<link rel="stylesheet" href="<?php echo includes_url('admin-bar/admin-bar-css.php') . '?t=' . get_current_theme() . '&amp;a=' . is_admin() . '&amp;p=' . is_ssl() . '&amp;sa=' . is_super_admin() . '&amp;td=' . $wp_locale->text_direction . '&amp;inc=' . includes_url() . '&amp;nobump=' . $nobump; ?>" type="text/css" />
	<!--[if IE 6]><style type="text/css">#wpadminbar, #wpadminbar .menupop a span, #wpadminbar .menupop ul li a:hover, #wpadminbar .myaccount a, .quicklinks a:hover,#wpadminbar .menupop:hover { background-image: none !important; } #wpadminbar .myaccount a { margin-left:0 !important; padding-left:12px !important;}</style><![endif]-->
	<style type="text/css" media="print">#wpadminbar { display:none; }</style><?php
}
add_action( 'wp_head', 'wp_admin_bar_css' );
add_action( 'admin_head', 'wp_admin_bar_css' );

/**
 * Load up the JS needed to allow the admin bar to function correctly.
 */
function wp_admin_bar_js() {
	global $wp_admin_bar;

	if ( !is_object( $wp_admin_bar ) )
		return false;

	?>
	<script type="text/javascript">
/*	<![CDATA[ */
		function pressthis(step) {if (step == 1) {if(navigator.userAgent.indexOf('Safari') >= 0) {Q=getSelection();}else {if(window.getSelection)Q=window.getSelection().toString();else if(document.selection)Q=document.selection.createRange().text;else Q=document.getSelection().toString();}} else {location.href='<?php echo $wp_admin_bar->user->account_domain; ?>wp-admin/post-new.php?text='+encodeURIComponent(Q.toString())+'&amp;popupurl='+encodeURIComponent(location.href)+'&amp;popuptitle='+encodeURIComponent(document.title);}}
		function toggle_query_list() { var querylist = document.getElementById( 'querylist' );if( querylist.style.display == 'block' ) {querylist.style.display='none';} else {querylist.style.display='block';}}

		jQuery( function() {
			(function(jq){jq.fn.hoverIntent=function(f,g){var cfg={sensitivity:7,interval:100,timeout:0};cfg=jq.extend(cfg,g?{over:f,out:g}:f);var cX,cY,pX,pY;var track=function(ev){cX=ev.pageX;cY=ev.pageY;};var compare=function(ev,ob){ob.hoverIntent_t=clearTimeout(ob.hoverIntent_t);if((Math.abs(pX-cX)+Math.abs(pY-cY))<cfg.sensitivity){jq(ob).unbind("mousemove",track);ob.hoverIntent_s=1;return cfg.over.apply(ob,[ev]);}else{pX=cX;pY=cY;ob.hoverIntent_t=setTimeout(function(){compare(ev,ob);},cfg.interval);}};var delay=function(ev,ob){ob.hoverIntent_t=clearTimeout(ob.hoverIntent_t);ob.hoverIntent_s=0;return cfg.out.apply(ob,[ev]);};var handleHover=function(e){var p=(e.type=="mouseover"?e.fromElement:e.toElement)||e.relatedTarget;while(p&&p!=this){try{p=p.parentNode;}catch(e){p=this;}}if(p==this){return false;}var ev=jQuery.extend({},e);var ob=this;if(ob.hoverIntent_t){ob.hoverIntent_t=clearTimeout(ob.hoverIntent_t);}if(e.type=="mouseover"){pX=ev.pageX;pY=ev.pageY;jq(ob).bind("mousemove",track);if(ob.hoverIntent_s!=1){ob.hoverIntent_t=setTimeout(function(){compare(ev,ob);},cfg.interval);}}else{jq(ob).unbind("mousemove",track);if(ob.hoverIntent_s==1){ob.hoverIntent_t=setTimeout(function(){delay(ev,ob);},cfg.timeout);}}};return this.mouseover(handleHover).mouseout(handleHover);};})(jQuery);
			;(function(jq){jq.fn.superfish=function(op){var sf=jq.fn.superfish,c=sf.c,jqarrow=jq([''].join('')),over=function(){var jqjq=jq(this),menu=getMenu(jqjq);clearTimeout(menu.sfTimer);jqjq.showSuperfishUl().siblings().hideSuperfishUl();},out=function(){var jqjq=jq(this),menu=getMenu(jqjq),o=sf.op;clearTimeout(menu.sfTimer);menu.sfTimer=setTimeout(function(){o.retainPath=(jq.inArray(jqjq[0],o.jqpath)>-1);jqjq.hideSuperfishUl();if(o.jqpath.length&&jqjq.parents(['li.',o.hoverClass].join('')).length<1){over.call(o.jqpath);}},o.delay);},getMenu=function(jqmenu){var menu=jqmenu.parents(['ul.',c.menuClass,':first'].join(''))[0];sf.op=sf.o[menu.serial];return menu;},addArrow=function(jqa){jqa.addClass(c.anchorClass).append(jqarrow.clone());};return this.each(function(){var s=this.serial=sf.o.length;var o=jq.extend({},sf.defaults,op);o.jqpath=jq('li.'+o.pathClass,this).slice(0,o.pathLevels).each(function(){jq(this).addClass([o.hoverClass,c.bcClass].join(' ')).filter('li:has(ul)').removeClass(o.pathClass);});sf.o[s]=sf.op=o;jq('li:has(ul)',this)[(jq.fn.hoverIntent&&!o.disableHI)?'hoverIntent':'hover'](over,out).each(function(){if(o.autoArrows)addArrow(jq('>a:first-child',this));}).not('.'+c.bcClass).hideSuperfishUl();var jqa=jq('a',this);jqa.each(function(i){var jqli=jqa.eq(i).parents('li');jqa.eq(i).focus(function(){over.call(jqli);}).blur(function(){out.call(jqli);});});o.onInit.call(this);}).each(function(){var menuClasses=[c.menuClass];if(sf.op.dropShadows&&!(jq.browser.msie&&jq.browser.version<7))menuClasses.push(c.shadowClass);jq(this).addClass(menuClasses.join(' '));});};var sf=jq.fn.superfish;sf.o=[];sf.op={};sf.IE7fix=function(){var o=sf.op;if(jq.browser.msie&&jq.browser.version>6&&o.dropShadows&&o.animation.opacity!=undefined) this.toggleClass(sf.c.shadowClass+'-off');};sf.c={bcClass:'sf-breadcrumb',menuClass:'sf-js-enabled',anchorClass:'sf-with-ul',arrowClass:'sf-sub-indicator',shadowClass:'sf-shadow'};sf.defaults={hoverClass:'sfHover',pathClass:'overideThisToUse',pathLevels:1,delay:600,animation:{opacity:'show'},speed:100,autoArrows:false,dropShadows:false,disableHI:false,onInit:function(){},onBeforeShow:function(){},onShow:function(){},onHide:function(){}};jq.fn.extend({hideSuperfishUl:function(){var o=sf.op,not=(o.retainPath===true)?o.jqpath:'';o.retainPath=false;var jqul=jq(['li.',o.hoverClass].join(''),this).add(this).not(not).removeClass(o.hoverClass).find('>ul').hide().css('visibility','hidden');o.onHide.call(jqul);return this;},showSuperfishUl:function(){var o=sf.op,sh=sf.c.shadowClass+'-off',jqul=this.addClass(o.hoverClass).find('>ul:hidden').css('visibility','visible');sf.IE7fix.call(jqul);o.onBeforeShow.call(jqul);jqul.animate(o.animation,o.speed,function(){sf.IE7fix.call(jqul);o.onShow.call(jqul);});return this;}});})(jQuery);

			<?php if ( is_single() ) : ?>
			if ( jQuery(this).width() < 1100 ) jQuery("#adminbarsearch").hide();
			<?php endif; ?>
				
			jQuery( '#wpadminbar li.ab-my-account, #wpadminbar li.ab-bloginfo' ).mouseover( function() {
				if ( jQuery(this).hasClass( 'ab-my-account' ) ) jQuery('#wpadminbar li.ab-me > a').addClass('hover');
				if ( jQuery(this).hasClass( 'ab-bloginfo' ) ) jQuery('#wpadminbar li.ab-blog > a').addClass('hover');
			});
			
			jQuery( '#wpadminbar li.ab-my-account, #wpadminbar li.ab-bloginfo' ).mouseout( function() {
				if ( jQuery(this).hasClass( 'ab-my-account' ) ) jQuery('#wpadminbar li.ab-me > a').removeClass('hover');
				if ( jQuery(this).hasClass( 'ab-bloginfo' ) ) jQuery('#wpadminbar li.ab-blog > a').removeClass('hover');
			});

			<?php if ( is_single() ) : ?>
			jQuery(window).resize( function() {
				if ( jQuery(this).width() < 1100 )
					jQuery("#adminbarsearch").hide();
				
				if ( jQuery(this).width() > 1100 )
					jQuery("#adminbarsearch").show();
			});
			<?php endif; ?>
			
			jQuery( '#wpadminbar ul ul li a' ).mouseover( function() {
				var root = jQuery(this).parents('div.quicklinks ul > li');
				var par = jQuery(this).parent();
				var children = par.children('ul');
				if ( root.hasClass('ab-sadmin') )
					jQuery(children[0]).css('<?php echo( is_rtl() ? 'left' : 'right' ); ?>',par.parents('ul').width() - 1 +'px' );
				else
					jQuery(children[0]).css('<?php echo( is_rtl() ? 'right' : 'left' ); ?>',par.parents('ul').width() +'px' );
				
				jQuery(children[0]).css('top', '0' );
			});
			
			<?php if ( is_user_logged_in() ) : // Hash links scroll 32px back so admin bar doesn't cover. ?>
				if ( window.location.hash ) window.scrollBy(0,-32);
			<?php endif; ?>
		
		});

		jQuery( function() { 
			jQuery('#wpadminbar').appendTo('body'); 
			jQuery("#wpadminbar ul").superfish();
		});

		/*	]]> */
	</script><?php
}
add_action( 'wp_footer', 'wp_admin_bar_js' );
add_action( 'admin_footer', 'wp_admin_bar_js' );

/**
 * Return a rendered admin bar via AJAX for use on pages that do not run inside the
 * WP environment. Used on bbPress forum pages to show the admin bar.
 */
function wp_admin_bar_ajax_render() {
	global $wp_admin_bar;

	wp_admin_bar_js();
	wp_admin_bar_css();
	wp_admin_bar_render();
	die;
}
add_action( 'wp_ajax_adminbar_render', 'wp_admin_bar_ajax_render' );

function is_admin_bar() {
	return ( 0 === strpos($_SERVER['REQUEST_URI'], '/js/admin-bar') );
}

function wp_admin_bar_lang($locale) {
	if ( is_admin_bar() )
		$locale = get_locale();
	return $locale;
}
add_filter('locale', 'wp_admin_bar_lang');

?>
