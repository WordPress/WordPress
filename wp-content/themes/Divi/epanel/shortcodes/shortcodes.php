<?php

/********* Shortcodes v.3.0 ************/

if ( ! defined( 'ET_SHORTCODES_VERSION' ) ) define( 'ET_SHORTCODES_VERSION', '3.0' );
if ( ! defined( 'ET_SHORTCODES_DIR' ) ) define( 'ET_SHORTCODES_DIR', get_template_directory_uri() . '/epanel/shortcodes' );

add_action('wp_enqueue_scripts', 'et_shortcodes_css_and_js');
function et_shortcodes_css_and_js(){
	global $themename;
	$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.dev' : '';

	wp_enqueue_style( 'et-shortcodes-css', ET_SHORTCODES_DIR . '/css/shortcodes.css', false, ET_SHORTCODES_VERSION, 'all' );
	wp_register_script( 'et-shortcodes-js', ET_SHORTCODES_DIR . "/js/et_shortcodes_frontend{$suffix}.js", array('jquery'), ET_SHORTCODES_VERSION, false );
	wp_localize_script( 'et-shortcodes-js', 'et_shortcodes_strings', array( 'previous' => esc_html__( 'Previous', $themename ), 'next' => esc_html__( 'Next', $themename ) ) );
}

function et_add_simple_buttons(){
    wp_print_scripts( 'quicktags' );
	$output = "<script type='text/javascript'>\n
	/* <![CDATA[ */ \n";

	$buttons = array();

	$buttons[] = array('name' => 'raw',
					'options' => array(
						'display_name' => 'raw',
						'open_tag' => '\n[raw]',
						'close_tag' => '[/raw]\n',
						'key' => ''
					));
	$buttons[] = array('name' => 'one_half',
					'options' => array(
						'display_name' => 'one half',
						'open_tag' => '\n[one_half]',
						'close_tag' => '[/one_half]\n',
						'key' => ''
					));
	$buttons[] = array('name' => 'one_half_last',
					'options' => array(
						'display_name' => 'one half last',
						'open_tag' => '\n[one_half_last]',
						'close_tag' => '[/one_half_last]\n',
						'key' => ''
					));
	$buttons[] = array('name' => 'one_third',
					'options' => array(
						'display_name' => 'one third',
						'open_tag' => '\n[one_third]',
						'close_tag' => '[/one_third]\n',
						'key' => ''
					));
	$buttons[] = array('name' => 'one_third_last',
					'options' => array(
						'display_name' => 'one third last',
						'open_tag' => '\n[one_third_last]',
						'close_tag' => '[/one_third_last]\n',
						'key' => ''
					));
	$buttons[] = array('name' => 'one_fourth',
					'options' => array(
						'display_name' => 'one fourth',
						'open_tag' => '\n[one_fourth]',
						'close_tag' => '[/one_fourth]\n',
						'key' => ''
					));
	$buttons[] = array('name' => 'one_fourth_last',
					'options' => array(
						'display_name' => 'one fourth last',
						'open_tag' => '\n[one_fourth_last]',
						'close_tag' => '[/one_fourth_last]\n',
						'key' => ''
					));
	$buttons[] = array('name' => 'two_third',
					'options' => array(
						'display_name' => 'two third',
						'open_tag' => '\n[two_third]',
						'close_tag' => '[/two_third]\n',
						'key' => ''
					));
	$buttons[] = array('name' => 'two_third_last',
					'options' => array(
						'display_name' => 'two third last',
						'open_tag' => '\n[two_third_last]',
						'close_tag' => '[/two_third_last]\n',
						'key' => ''
					));
	$buttons[] = array('name' => 'three_fourth',
					'options' => array(
						'display_name' => 'three fourth',
						'open_tag' => '\n[three_fourth]',
						'close_tag' => '[/three_fourth]\n',
						'key' => ''
					));
	$buttons[] = array('name' => 'three_fourth_last',
					'options' => array(
						'display_name' => 'three fourth last',
						'open_tag' => '\n[three_fourth_last]',
						'close_tag' => '[/three_fourth_last]\n',
						'key' => ''
					));
	$buttons[] = array('name' => 'three_fourth_last',
					'options' => array(
						'display_name' => 'three fourth last',
						'open_tag' => '\n[three_fourth_last]',
						'close_tag' => '[/three_fourth_last]\n',
						'key' => ''
					));
	$buttons[] = array('name' => 'box',
					'options' => array(
						'display_name' => 'box',
						'open_tag' => '\n[box type="shadow"]',
						'close_tag' => '[/box]\n',
						'key' => ''
					));
	$buttons[] = array('name' => 'tooltip',
					'options' => array(
						'display_name' => 'tooltip',
						'open_tag' => '[tooltip text="Tooltip Text"]',
						'close_tag' => '[/tooltip]',
						'key' => ''
					));
	$buttons[] = array('name' => 'learn_more',
					'options' => array(
						'display_name' => 'learn_more',
						'open_tag' => '\n[learn_more caption="Click here to learn more"]',
						'close_tag' => '[/learn_more]\n',
						'key' => ''
					));
	$buttons[] = array('name' => 'slider',
					'options' => array(
						'display_name' => 'slider',
						'open_tag' => '\n[slider]',
						'close_tag' => '[/slider]\n',
						'key' => ''
					));
	$buttons[] = array('name' => 'slide',
					'options' => array(
						'display_name' => 'slide',
						'open_tag' => '\n[slide]',
						'close_tag' => '[/slide]\n',
						'key' => ''
					));
	$buttons[] = array('name' => 'button',
					'options' => array(
						'display_name' => 'button',
						'open_tag' => '\n[button link="#"]',
						'close_tag' => '[/button]\n',
						'key' => ''
					));
	$buttons[] = array('name' => 'digg',
					'options' => array(
						'display_name' => 'digg',
						'open_tag' => '\n[digg]',
						'close_tag' => '[/digg]\n',
						'key' => ''
					));
	$buttons[] = array('name' => 'stumble',
					'options' => array(
						'display_name' => 'stumble',
						'open_tag' => '\n[stumble]',
						'close_tag' => '[/stumble]\n',
						'key' => ''
					));
	$buttons[] = array('name' => 'facebook',
					'options' => array(
						'display_name' => 'facebook',
						'open_tag' => '\n[facebook]',
						'close_tag' => '[/facebook]\n',
						'key' => ''
					));

	$buttons[] = array('name' => 'twitter',
					'options' => array(
						'display_name' => 'twitter',
						'open_tag' => '\n[twitter name="name"]',
						'close_tag' => '[/twitter]\n',
						'key' => ''
					));

	$buttons[] = array('name' => 'retweet',
					'options' => array(
						'display_name' => 'retweet',
						'open_tag' => '\n[retweet]',
						'close_tag' => '[/retweet]\n',
						'key' => ''
					));

	$buttons[] = array('name' => 'feedburner',
					'options' => array(
						'display_name' => 'feedburner',
						'open_tag' => '\n[feedburner name="name"]',
						'close_tag' => '[/feedburner]\n',
						'key' => ''
					));
	$buttons[] = array('name' => 'protected',
					'options' => array(
						'display_name' => 'protected',
						'open_tag' => '\n[protected]',
						'close_tag' => '[/protected]\n',
						'key' => ''
					));


	for ($i=0; $i <= (count($buttons)-1); $i++) {
		$output .= "edButtons[edButtons.length] = new edButton('ed_{$buttons[$i]['name']}'
			,'{$buttons[$i]['options']['display_name']}'
			,'{$buttons[$i]['options']['open_tag']}'
			,'{$buttons[$i]['options']['close_tag']}'
			,'{$buttons[$i]['options']['key']}'
		); \n";
	}

	$output .= "\n /* ]]> */ \n
	</script>";
	echo $output;
}


add_shortcode('digg', 'et_digg');
function et_digg($atts, $content = null) {
	$output = "<script type='text/javascript'>
(function() {
var s = document.createElement('SCRIPT'), s1 = document.getElementsByTagName('SCRIPT')[0];
s.type = 'text/javascript';
s.async = true;
s.src = 'http://widgets.digg.com/buttons.js';
s1.parentNode.insertBefore(s, s1);
})();
</script>
<!-- Medium Button -->
<a class='DiggThisButton DiggMedium'></a>";

	return $output;
}

add_shortcode('stumble','et_stumble');
function et_stumble($atts, $content = null){
	$output = "<script src='http://www.stumbleupon.com/hostedbadge.php?s=5' type='text/javascript'></script>";
	return $output;
}

add_shortcode('facebook','et_facebook');
function et_facebook($atts, $content = null){
	$output = "<a name='fb_share' type='button_count' href='http://www.facebook.com/sharer.php'>Share</a><script src='http://static.ak.fbcdn.net/connect.php/js/FB.Share' type='text/javascript'></script>";
	return $output;
}

add_shortcode('twitter','et_twitter');
function et_twitter($atts, $content = null){
	extract(shortcode_atts(array(
		"name" => 'name'
	), $atts, 'twitter'));
	$output = "<script type='text/javascript' src='" . esc_url( "http://twittercounter.com/embed/{$name}/ffffff/111111" ) . "'></script>";
	return $output;
}

add_shortcode('feedburner','et_feedburner');
function et_feedburner($atts, $content = null){
	extract(shortcode_atts(array(
		"name" => 'name'
	), $atts, 'feedburner'));
	$output = "<a href='" . esc_url( "http://feeds.feedburner.com/{$name}" ) . "'><img src='" . esc_url( "http://feeds.feedburner.com/~fc/{$name}?bg=99CCFF&amp;fg=444444&amp;anim=0" ) . "' height='26' width='88' style='border:0' alt='' />
</a>";
	return $output;
}


add_shortcode('retweet','et_retweet');
function et_retweet($atts, $content = null){
	global $themename;

	$output = "<a href='http://twitter.com/share' class='twitter-share-button' data-count='vertical'>" . esc_html__( 'Tweet', $themename ) . "</a><script type='text/javascript' src='http://platform.twitter.com/widgets.js'></script>";
	return $output;
}


add_shortcode('protected','et_protected');
function et_protected($atts, $content = null){
	global $themename, $user_login;

	if ( is_user_logged_in() ) {
		$content = et_content_helper($content);
		$output = $content;
	} else {
		$scheme = apply_filters( 'et_forms_scheme', null );

		$output = "<div class='et-protected'>
					<div class='et-protected-title'>" . esc_html__('Member Login',$themename) . "</div>
					<div class='et-protected-form'>
						<form action='" . esc_url( site_url( 'wp-login.php', $scheme ) ) . "' method='post'>
							<p><label>" . "<span>" . esc_html__('Username: ',$themename) . "</span>" . "<input type='text' name='log' id='log' value='" . esc_attr( $user_login ) . "' size='20' /><span class='et_protected_icon'></span></label></p>
							<p><label>" . "<span>" . esc_html__('Password: ',$themename) . "</span>" . "<input type='password' name='pwd' id='pwd' size='20' /><span class='et_protected_icon et_protected_password'></span></label></p>
							<input type='submit' name='submit' value='" . esc_html__('Login',$themename) . "' class='etlogin-button' />
							<input type='hidden' name='redirect_to' value='" . esc_url( get_permalink() ) . "'>
						</form>
					</div> <!-- .et-protected-form -->
				</div> <!-- .et-protected -->";
	}

	return $output;
}


add_shortcode('box', 'et_box');
function et_box($atts, $content = null) {
	extract(shortcode_atts(array(
				"type" => 'shadow',
				"id" => '',
				"class" => ''
			), $atts, 'box'));
	$content = et_content_helper($content);

	$id = ($id <> '') ? " id='" . esc_attr( $id ) . "'" : '';
	$class = ($class <> '') ? esc_attr( ' ' . $class ) : '';

	$output = "<div{$id} class='et-box{$class} et-{$type}'>
					<div class='et-box-content'>";
	$output .= do_shortcode($content);
	$output .= "</div></div>";

	return $output;
}

add_shortcode('tooltip', 'et_tooltip');
function et_tooltip($atts, $content = null) {
	global $themename;

	extract(shortcode_atts(array(
				"text" => esc_html__( 'Add a Tooltip Text', $themename ),
				"id" => '',
				"class" => ''
			), $atts, 'tooltip'));

	wp_enqueue_script( 'et-shortcodes-js' );

	$content = et_content_helper($content);

	$id = ($id <> '') ? " id='" . esc_attr( $id ) . "'" : '';
	$class = ($class <> '') ? esc_attr( ' ' . $class ) : '';

	$output = "<span{$id} class='et-tooltip{$class}'>{$content}<span class='et-tooltip-box'>{$text}<span class='et-tooltip-arrow'></span></span></span>";

	return $output;
}

add_shortcode('learn_more', 'et_learnmore');
function et_learnmore($atts, $content = null) {
	global $themename;

	extract(shortcode_atts(array(
				"caption" => esc_html__( 'Click here to learn more', $themename ),
				"state" => 'close',
				"id" => '',
				"class" => ''
			), $atts, 'learn_more'));

	wp_enqueue_script( 'et-shortcodes-js' );

	$content = et_content_helper($content);

	$id = ($id <> '') ? " id='" . esc_attr( $id ) . "'" : '';
	$class = ($class <> '') ? esc_attr( ' ' . $class ) : '';

	$divClass = ($state == 'close') ? 'et-learn-more' : 'et-learn-more et-open';
	$hClass = ($state == 'close') ? 'heading-more' : 'heading-more open';
	$divClass .= ' clearfix';

	$output = "<div{$id} class='{$divClass}{$class}'>
					<h3 class='{$hClass}'>" . esc_html( $caption ) . "<span class='et_learnmore_arrow'><span></span></span></h3>
					<div class='learn-more-content'>{$content}</div>
				</div>";

	return $output;
}

add_shortcode('button', 'et_button');
function et_button($atts, $content = null) {
	extract(shortcode_atts(array(
				"link" => "#",
				"color" => "blue",
				"type" => "small",
				"icon" => "download",
				"newwindow" => "no",
				"id" => '',
				"class" => '',
				"br" => 'no'
			), $atts, 'button'));

	$output = '';
	$target = ($newwindow == 'yes') ? ' target="_blank"' : '';

	$content = et_content_helper($content);

	$id = ($id <> '') ? " id='" . esc_attr( $id ) . "'" : '';

	if ($type == 'small')
		$output .= "<a{$id} href='" . esc_url( $link ) . "' class='" . esc_attr( "small-button small{$color}{$class}" ) . "'{$target}>{$content}</a>";

	if ($type == 'big')
		$output .= "<a{$id} href='" . esc_url( $link ) . "' class='" . esc_attr( "big-button big{$color}{$class}" ) . "'{$target}>{$content}</a>";

	if ($type == 'icon')
		$output .= "<a{$id} href='" . esc_url( $link ) . "' class='" . esc_attr( "icon-button {$icon}-icon{$class}" ) . "'{$target}>{$content}<span class='et-icon'></span></a>";

	if ( $br == 'yes' ) $output .= '<br class="clear"/>';

	return $output;
}

add_shortcode('slide', 'et_slide');
function et_slide($atts, $content = null) {
	extract(shortcode_atts(array(
		"id" => '',
		"class" => ''
	), $atts, 'slide'));

	$content = et_content_helper($content);

	$id = ($id <> '') ? " id='" . esc_attr( $id ) . "'" : '';
	$class = ($class <> '') ? esc_attr( ' ' . $class ) : '';

	$output = "<div{$id} class='et-slide{$class}'>
					{$content}
				</div>";

	return $output;
}

add_shortcode('tabs', 'et_tabs');
function et_tabs($atts, $content = null) {
	extract(shortcode_atts(array(
				"fx" => 'fade',
				"auto" => 'no',
				"autospeed" => '5000',
				"id" => '',
				"slidertype" => 'top tabs',
				"class" => ''
			), $atts, 'tabs'));

	wp_enqueue_script( 'et-shortcodes-js' );

	$auto = ( $auto == 'no' ) ? 'false' : 'true';

	$content = et_content_helper($content);

	$id = ( '' != $id ) ? " id='" . esc_attr( $id ) . "'" : '';
	$class = ($class <> '') ? " {$class}" : '';

	$class .= " et_sliderfx_{$fx}" . " et_sliderauto_{$auto}" . " et_sliderauto_speed_{$autospeed}";

	if ($slidertype == 'top tabs') {
		$class .= ' et_slidertype_top_tabs';
		$output = "
			<div class='" . esc_attr( "et-tabs-container{$class}" ) ."'{$id}>
				{$content}
			</div> <!-- .et-tabs-container -->";
	} elseif ($slidertype == 'left tabs') {
		$class .= ' et_slidertype_left_tabs clearfix';
		$output = "
			<div class='" . esc_attr( "tabs-left{$class}" ) . "'{$id}>
				<div class='et_left_tabs_bg'></div>
				{$content}
			</div> <!-- .tabs-left -->";
	} elseif ($slidertype == 'simple') {
		$class .= ' et_slidertype_simple';
		$output = "
		<div class='" . esc_attr( "et-simple-slider{$class}" ) . "'{$id}>
			<div class='et-simple-slides'>
				<div class='et-tabs-content-wrapper'>
					{$content}
				</div>
			</div>
		</div> <!-- .et-simple-slider -->
		";
	} elseif ($slidertype == 'images') {
		$class .= ' et_slidertype_images';
		$output = "
		<div class='" . esc_attr( "et-image-slider{$class}" ) . "'{$id}>
			<div class='et-image-slides'>
				<div class='et-tabs-content-wrapper'>
					{$content}
				</div>
			</div>
		</div> <!-- .et-image-slider -->
		";
	}

	return $output;
}

add_shortcode('tabcontainer', 'et_tabcontainer');
function et_tabcontainer($atts, $content = null) {
	$content = et_content_helper($content);

	$output = "
		<ul class='et-tabs-control'>
			{$content}
		</ul> <!-- .et-tabs-control -->";

	return $output;
}

add_shortcode('imagetabcontainer', 'et_imagetabcontainer');
function et_imagetabcontainer($atts, $content = null) {
	global $themename;

	extract(shortcode_atts(array(
		"id" => '',
		"class" => ''
	), $atts, 'imagetabcontainer'));

	$content = et_content_helper($content);

	$id = ($id <> '') ? " id='" . esc_attr( $id ) . "'" : '';
	$class = ($class <> '') ? esc_attr( ' ' . $class ) : '';

	$previous = esc_html__( 'Previous', $themename );
	$next     = esc_html__( 'Next', $themename );

	$output = "
		<div{$id} class='controllers-wrapper{$class}'>
			<div class='controllers'>
				<a class='left-arrow' href='#'>{$previous}</a>
				{$content}
				<a class='right-arrow' href='#'>{$next}</a>
			</div> <!-- end #controllers -->
			<div class='controllers-right'></div>
		</div><!-- end #controllers-wrapper -->";

	return $output;
}

add_shortcode('imagetabtext', 'et_imagetabtext');
function et_imagetabtext($atts, $content = null) {
	$content = et_content_helper($content);

	$output = "
		<a href='#' class='switch'>
			{$content}
		</a>";

	return $output;
}

add_shortcode('tabtext', 'et_tabtext');
function et_tabtext($atts, $content = null) {
	extract(shortcode_atts(array(
		"id" => '',
		"class" => ''
	), $atts, 'tabtext'));

	$content = et_content_helper($content);

	$id = ($id <> '') ? " id='" . esc_attr( $id ) . "'" : '';
	$class = ($class <> '') ? esc_attr( ' ' . $class ) : '';

	$output = "
		<li{$id}{$class}><a href='#'>
			{$content}
		</a></li>";

	return $output;
}

add_shortcode('tabcontent', 'et_tabcontent');
function et_tabcontent($atts, $content = null) {
	extract(shortcode_atts(array(
		"id" => '',
		"class" => ''
	), $atts, 'tabcontent'));

	$content = et_content_helper($content);

	$id = ($id <> '') ? " id='" . esc_attr( $id ) . "'" : '';
	$class = ($class <> '') ? esc_attr( ' ' . $class ) : '';

	$output = "
		<div{$id} class='et-tabs-content{$class}'>
			<div class='et-tabs-content-main-wrap'>
				<div class='et-tabs-content-wrapper'>
					{$content}
				</div>
			</div>
		</div>";

	return $output;
}

add_shortcode('tab', 'et_tab');
function et_tab($atts, $content = null) {
	extract(shortcode_atts(array(
		"id" => '',
		"class" => ''
	), $atts, 'tab'));

	$content = et_content_helper($content);

	$id = ($id <> '') ? " id='" . esc_attr( $id ) . "'" : '';
	$class = ($class <> '') ? esc_attr( ' ' . $class ) : '';

	$output = "
		<div{$id} class='et_slidecontent{$class}'>
			{$content}
		</div>";

	return $output;
}

add_shortcode('imagetab', 'et_imagetab');
function et_imagetab($atts, $content = null) {
	extract(shortcode_atts(array(
		"width"		=> '',
		"height"	=> '',
		"id"		=> '',
		"class"		=> '',
		"link"		=> '',
		"newwindow"	=> '',
		"alt"		=> ''
	), $atts, 'imagetab'));

	$target = '' != $newwindow ? ' target="_blank"' : '';
	$content = et_content_helper($content);

	$id = ($id <> '') ? " id='" . esc_attr( $id ) . "'" : '';
	$class = ($class <> '') ? esc_attr( ' ' . $class ) : '';

	$output = "
		<div{$id} class='et_slidecontent et-image-slide{$class}'>" . ( '' != $link ? "<a href='" . esc_url( $link ) . "'{$target}>" : '' ) . et_new_thumb_resize( $content, $width, $height, $alt ) . "<span class='et-image-overlay'> </span>" . ( '' != $link ? '</a>' : '' ) . "</div>";

	return $output;
}

add_shortcode('author', 'et_author');
function et_author($atts, $content = null) {
	extract(shortcode_atts(array(
		'id' => '',
		'class' => '',
		'name' => '',
		'description' => ''
	), $atts, 'author'));

	$content = et_content_helper($content);

	$id = ($id <> '') ? " id='" . esc_attr( $id ) . "'" : '';
	$class = ($class <> '') ? esc_attr( ' ' . $class ) : '';

	$output = "
		<div{$id} class='author-shortcodes{$class}'>
			<div class='author-inner'>
				{$content}
			</div> <!-- .author-inner -->
		</div> <!-- .author-shortcodes -->";

	return $output;
}

add_shortcode('author_image', 'et_author_image');
function et_author_image($atts, $content = null) {
	extract(shortcode_atts(array(
		"timthumb" => 'on'
	), $atts, 'author_image'));

	$content = et_content_helper($content);

	$src = ($timthumb == 'on') ? ( et_new_thumb_resize( $content, 60, 60, '', $forstyle = true ) ) : $content;

	$output = "
		<div class='author-image'>
			<img src='" . esc_attr( $src ) . "' alt='' />
			<div class='author-overlay'></div>
		</div> <!-- .author-image -->";

	return $output;
}

add_shortcode('author_info', 'et_author_info');
function et_author_info($atts, $content = null) {

	$content = et_content_helper($content);

	$output = "
		<div class='author-info'>
			{$content}
		</div> <!-- .author-info -->";

	return $output;
}

add_shortcode('pricing_table', 'et_pricing_table');
function et_pricing_table($atts, $content = null) {
	wp_enqueue_script( 'et-shortcodes-js' );

	$content = et_content_helper($content);

	$output = "
		<div class='et-pricing clearfix'>
			{$content}
		</div> <!-- end .et-pricing -->";

	return $output;
}

add_shortcode('custom_list', 'et_custom_list');
function et_custom_list($atts, $content = null) {
	extract(shortcode_atts(array(
		"type" => 'checkmark'
	), $atts, 'custom_list'));

	$content = et_content_helper($content);

	$type = ( $type <> 'checkmark' ) ? ' etlist-' . $type : '';

	$output = "
		<div class='et-custom-list{$type}'>
			{$content}
		</div> <!-- .et-custom-list -->";

	return $output;
}

add_shortcode('pricing', 'et_pricing');
function et_pricing($atts, $content = null) {
	global $themename;

	extract(shortcode_atts(array(
		"price" => '19.95',
		"title" => "professional",
		"desc" => "",
		"url" => "#",
		"window" => "",
		"moretext" => esc_html__( 'Join Now', $themename ),
		"type" => "small",
		"currency" => "$"
	), $atts, 'pricing'));

	$content = et_content_helper($content);

	$separator_sign = ( strpos($price, '.') !== false ) ? '.' : ',';
	$price_array = explode($separator_sign, $price);
	$link_target = ( $window == 'new' ) ? ' target="_blank"' : '';
	$type = ( $type == 'big' ) ? ' pricing-big' : '';

	$output = "
		<div class='" . esc_attr( 'pricing-table' . $type ) . "'>
			<div class='pricing-table-wrap'>
				<div class='pricing-heading'>
					<h2 class='pricing-title'>" . esc_html( $title ) . "</h2>
					<p>" . esc_html( $desc ) . "</p>
				</div> <!-- end .pricing-heading -->

				<div class='pricing-content'>
					<ul class='pricing'>
						{$content}
					</ul>
				</div> <!-- end .pricing-content -->

				<div class='pricing-content-bottom'>
					<span class='et-price'><span class='dollar-sign'>" . esc_html( $currency ) . "</span>" . esc_html( $price_array[0] ) .
						"<sup>" . esc_html( $price_array[1] ) . "</sup></span>
				</div> <!-- end .pricing-content-bottom -->

				<a href='" . esc_url( $url ) . "' class='icon-button'{$link_target}>" . esc_html( $moretext ) . "</a>
			</div> <!-- end .pricing-table-wrap -->
		</div> <!-- end .pricing-table -->";

	return $output;
}

add_shortcode('feature', 'et_pricing_feature');
function et_pricing_feature($atts, $content = null) {
	extract(shortcode_atts(array(
		"checkmark" => 'normal'
	), $atts, 'feature'));

	$content = et_content_helper($content);
	$class = ( $checkmark == 'x' ) ? ' class="x-mark"' : '';

	$output = "<li{$class}>{$content}<span class='pricing-mark'></span></li>";

	return $output;
}

add_shortcode('dropcap', 'et_dropcap');
function et_dropcap($atts, $content = null) {
	extract(shortcode_atts(array(
		'style' => '',
		'id' => '',
		'class' => ''
	), $atts, 'dropcap'));

	$content = et_content_helper($content);
	$style = ( $style <> '' ) ? ' style="' . esc_attr( $style ) . '"' : '';
	$id = ($id <> '') ? " id='" . esc_attr( $id ) . "'" : '';
	$class = ($class <> '') ? esc_attr( ' ' . $class ) : '';

	$output = "<span{$id} class='et-dropcap{$class}'{$style}>{$content}</span>";

	return $output;
}

add_shortcode('testimonial', 'et_testimonial');
function et_testimonial($atts, $content = null) {
	extract(shortcode_atts(array(
		'style' => '',
		'id' => '',
		'class' => '',
		'author' => '',
		'company' => '',
		'image' => '',
		'timthumb' => 'on'
	), $atts, 'testimonial'));

	$content = et_content_helper($content);
	$style = ( $style <> '' ) ? ' style="' . esc_attr( $style ) . '"' : '';
	$id = ($id <> '') ? " id='" . esc_attr( $id ) . "'" : '';
	$class = ($class <> '') ? esc_attr( ' ' . $class ) : '';

	$orig_name = $author;
	$author = ( $author <> '' ) ? "<span class='t-author'>" . esc_html( $author ) . "</span>" : '';
	$company = ( $company <> '' ) ? "<span class='t-position'>" . esc_html( $company ) . "</span>" : '';

	$image_markup = '';
	if ( $image <> '' ) {
		$image = ( $timthumb == 'on' ) ? et_new_thumb_resize( $image, 60, 60, '', $forstyle = true ) : $image;
		$image_markup = "
			<div class='t-img'>
				<img src='" . esc_attr( $image ) . "' alt='" . esc_attr( $orig_name ) . "' />
				<span class='t-overlay'></span>
			</div>
		";
	}

	$output = "
		<div{$id} class='et-testimonial-box{$class}'{$style}>
			<div class='et-testimonial-author-info clearfix'>
				{$image_markup}
				{$author}
				{$company}
			</div>

			<div class='et-testimonial clearfix'>
				{$content}
			</div>

		    <div class='t-bottom-arrow'></div>
		</div>";

	return $output;
}

add_shortcode('quote','et_quote');
function et_quote($atts, $content = null) {
	extract(shortcode_atts(array(
		'style' => '',
		'id' => '',
		'class' => '',
		'type' => 'normal'
	), $atts, 'quote'));

	$content = et_content_helper($content);
	$style = ( $style <> '' ) ? ' style="' . esc_attr( $style ) . '"' : '';
	$id = ($id <> '') ? " id='" . esc_attr( $id ) . "'" : '';
	$class = ($class <> '') ? esc_attr( ' ' . $class ) : '';
	if ( $type == 'center' ) $class = ' quote-center';

	$output = "
		<div{$id} class='et_quote{$class}'{$style}>
			<div class='et_right_quote'>
				{$content}
				" . ( 'center' == $type ? "<span class='et_quote_additional_sign'></span>" : '' ) . "
			</div>
			<span class='et_quote_sign'></span>
		</div>
	";

	return $output;
}

add_shortcode('one_half', 'et_columns');
add_shortcode('one_half_last', 'et_columns');
add_shortcode('one_third', 'et_columns');
add_shortcode('one_third_last', 'et_columns');
add_shortcode('one_fourth', 'et_columns');
add_shortcode('one_fourth_last', 'et_columns');
add_shortcode('two_third', 'et_columns');
add_shortcode('two_third_last', 'et_columns');
add_shortcode('three_fourth', 'et_columns');
add_shortcode('three_fourth_last', 'et_columns');
function et_columns($atts, $content = null, $name='') {
	extract(shortcode_atts(array(
		"id" => '',
		"class" => ''
	), $atts, $name));

	$content = et_content_helper($content);

	$id = ($id <> '') ? " id='" . esc_attr( $id ) . "'" : '';

	$pos = strpos($name,'_last');

	if($pos !== false)
		$name = str_replace('_last',' et_column_last',$name);

	$output = "<div{$id} class='" . esc_attr( $name . $class ) . "'>
					{$content}
				</div>";
	if($pos !== false)
		$output .= "<div class='clear'></div>";

	return $output;
}

if ( ! function_exists( 'et_paragraph_br_fix' ) ){
	function et_paragraph_br_fix($content,$paragraph_tag=false,$br_tag=false){
		$content = preg_replace('#^<\/p>|^<br \/>|<p>$#', '', $content);

		$content = preg_replace('#<br \/>#', '', $content);

		if ( $paragraph_tag ) $content = preg_replace('#<p>|</p>#', '', $content);

		return trim($content);
	}
}

if ( ! function_exists( 'et_content_helper' ) ){
	function et_content_helper($content,$paragraph_tag=false,$br_tag=false){
		return et_paragraph_br_fix( do_shortcode(shortcode_unautop($content)), $paragraph_tag, $br_tag );
	}
}

add_action('admin_init', 'et_init_shortcodes');
function et_init_shortcodes(){
	if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
		if ( in_array(basename($_SERVER['PHP_SELF']), array('post-new.php', 'page-new.php', 'post.php', 'page.php') ) ) {
			add_filter('mce_buttons', 'et_filter_mce_button');
			add_filter('mce_external_plugins', 'et_filter_mce_plugin');
			add_action('admin_head','et_add_simple_buttons');
			add_action('edit_form_advanced', 'et_advanced_buttons');
			add_action('edit_page_form', 'et_advanced_buttons');
		}
	}
}

function et_filter_mce_button($buttons) {
	array_push( $buttons, '|', 'et_learn_more', 'et_box', 'et_button', 'et_tabs', 'et_author' );

	return $buttons;
}

function et_filter_mce_plugin($plugins) {
	$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.dev' : '';

	$plugins['et_quicktags'] = get_template_directory_uri(). "/epanel/shortcodes/js/editor_plugin{$suffix}.js";

	return $plugins;
}

function et_advanced_buttons(){
	global $themename; ?>
	<script type="text/javascript">
		var defaultSettings = {},
			outputOptions = '',
			selected ='',
			content = '',
			et_quicktags_strings = {
				learn_more : "<?php esc_html_e( 'Add ET Learn more block', $themename ); ?>",
				box        : "<?php esc_html_e( 'Add ET Box', $themename ); ?>",
				button     : "<?php esc_html_e( 'Add ET Button', $themename ); ?>",
				tabs       : "<?php esc_html_e( 'Add ET Tabs', $themename ); ?>",
				author     : "<?php esc_html_e( 'Add Author Bio', $themename ); ?>",
				shortcodes : "<?php esc_html_e( 'Shortcodes', $themename ); ?>",
			};

		defaultSettings['learn_more'] = {
			caption: {
				name: '<?php esc_html_e( 'Caption', $themename ); ?>',
				defaultvalue: '<?php esc_html_e( 'Caption goes here', $themename ); ?>',
				description: '<?php esc_html_e( 'Caption title goes here', $themename ); ?>',
				type: 'text'
			},
			state: {
				name: '<?php esc_html_e( 'State', $themename ); ?>',
				defaultvalue: 'close',
				description: '<?php esc_html_e( 'Select between expanded and closed state', $themename ); ?>',
				type: 'select',
				options: 'open|close'
			},
			content: {
				name: '<?php esc_html_e( 'Content', $themename ); ?>',
				defaultvalue: '<?php esc_html_e( 'Content goes here', $themename ); ?>',
				description: '<?php esc_html_e( 'Content text or html', $themename ); ?>',
				type: 'textarea'
			}
		};

		defaultSettings['box'] = {
			type: {
				name: '<?php esc_html_e( 'Type', $themename ); ?>',
				defaultvalue: 'shadow',
				description: '<?php esc_html_e( 'Type of the box', $themename ); ?>',
				type: 'select',
				options: 'info|warning|download|bio|shadow'
			},
			content: {
				name: '<?php esc_html_e( 'Content', $themename ); ?>',
				defaultvalue: '<?php esc_html_e( 'Content goes here', $themename ); ?>',
				description: '<?php esc_html_e( 'Content text or html', $themename ); ?>',
				type: 'textarea'
			}
		};

		defaultSettings['button'] = {
			link: {
				name: '<?php esc_html_e( 'Link', $themename ); ?>',
				defaultvalue: '#',
				description: '<?php esc_html_e( 'URL', $themename ); ?>',
				type: 'text'
			},
			type: {
				name: '<?php esc_html_e( 'Type', $themename ); ?>',
				defaultvalue: 'small',
				description: '<?php esc_html_e( 'Choose button type', $themename ); ?>',
				type: 'select',
				options: 'small|big|icon'
			},
			color: {
				name: '<?php esc_html_e( 'Color', $themename ); ?>',
				defaultvalue: 'blue',
				description: '<?php esc_html_e( 'Choose button color', $themename ); ?>',
				type: 'select',
				options: 'blue|lightblue|teal|silver|black|pink|purple|orange|green|red'
			},
			content: {
				name: '<?php esc_html_e( 'Content', $themename ); ?>',
				defaultvalue: '<?php esc_html_e( 'Link text', $themename ); ?>',
				description: '<?php esc_html_e( 'Content text or html', $themename ); ?>',
				type: 'textarea'
			},
			icon: {
				name: '<?php esc_html_e( 'Icon', $themename ); ?>',
				defaultvalue: 'download',
				description: '<?php esc_html_e( 'Used for icon button type', $themename ); ?>',
				type: 'select',
				options: 'download|search|refresh|question|people|warning|mail|heart|paper|notice|stats|rss'
			},
			newwindow: {
				name: '<?php esc_html_e( 'Open link in new window', $themename ); ?>',
				defaultvalue: 'no',
				description: '<?php esc_html_e( 'Select yes if the link should be opened in a new window', $themename ); ?>',
				type: 'select',
				options: 'yes|no'
			}
		};

		defaultSettings['tabs'] = {
			slidertype: {
				name: '<?php esc_html_e( 'Slider Type', $themename ); ?>',
				defaultvalue: 'fade',
				description: '<?php esc_html_e( 'Select Slider Type here', $themename ); ?>',
				type: 'select',
				options: 'top tabs|left tabs|simple|images'
			},
			fx: {
				name: '<?php esc_html_e( 'Effect', $themename ); ?>',
				defaultvalue: 'fade',
				description: '<?php esc_html_e( 'Select Animation Effect', $themename ); ?>',
				type: 'select',
				options: 'fade|slide'
			},
			auto: {
				name: '<?php esc_html_e( 'Auto', $themename ); ?>',
				defaultvalue: 'no',
				description: '<?php esc_html_e( 'Choose yes if you want for automatic slider animation', $themename ); ?>',
				type: 'select',
				options: 'no|yes'
			},
			autospeed: {
				name: '<?php esc_html_e( 'Auto Speed', $themename ); ?>',
				defaultvalue: '5000',
				description: '<?php esc_html_e( 'Automattic slider speed (works only if Auto is set to yes)', $themename ); ?>',
				type: 'text'
			},
			tabtext: {
				name: '<?php esc_html_e( 'Tab Text', $themename ); ?>',
				defaultvalue: '',
				description: '',
				type: 'text',
				clone: 'cloned'
			},
			tabcontent: {
				name: '<?php esc_html_e( 'Tab Content', $themename ); ?>',
				defaultvalue: '<?php esc_html_e( 'Content goes here', $themename ); ?>',
				description: '<?php esc_html_e( 'Paste image url here, if you chose "images" slider type', $themename ); ?>',
				type: 'textarea',
				clone: 'cloned'
			}
		}

		defaultSettings['author'] = {
			imageurl: {
				name: '<?php esc_html_e( 'Image Url', $themename ); ?>',
				defaultvalue: '',
				description: '<?php esc_html_e( 'Author Image URL', $themename ); ?>',
				type: 'text'
			},
			timthumb: {
				name: '<?php esc_html_e( 'Use resizing', $themename ); ?>',
				defaultvalue: 'on',
				description: '',
				type: 'select',
				options: 'on|off'
			},
			content: {
				name: '<?php esc_html_e( 'Content', $themename ); ?>',
				defaultvalue: '<?php esc_html_e( 'Content goes here', $themename ); ?>',
				description: '',
				type: 'textarea'
			}
		}

		function CustomButtonClick(tag){

			var index = tag;

				for (var index2 in defaultSettings[index]) {
					if (defaultSettings[index][index2]['clone'] === 'cloned')
						outputOptions += '<tr class="cloned">\n';
					else if (index === 'button' && index2 === 'icon')
						outputOptions += '<tr class="hidden">\n';
					else
						outputOptions += '<tr>\n';
					outputOptions += '<th><label for="et-' + index2 + '">'+ defaultSettings[index][index2]['name'] +'</label></th>\n';
					outputOptions += '<td>';

					if (defaultSettings[index][index2]['type'] === 'select') {
						var optionsArray = defaultSettings[index][index2]['options'].split('|');

						outputOptions += '\n<select name="et-'+index2+'" id="et-'+index2+'">\n';

						for (var index3 in optionsArray) {
							selected = (optionsArray[index3] === defaultSettings[index][index2]['defaultvalue']) ? ' selected="selected"' : '';
							outputOptions += '<option value="'+optionsArray[index3]+'"'+ selected +'>'+optionsArray[index3]+'</option>\n';
						}

						outputOptions += '</select>\n';
					}

					if (defaultSettings[index][index2]['type'] === 'text') {
						cloned = '';
						if (defaultSettings[index][index2]['clone'] === 'cloned') cloned = "[]";
						outputOptions += '\n<input type="text" name="et-'+index2+cloned+'" id="et-'+index2+'" value="'+defaultSettings[index][index2]['defaultvalue']+'" />\n';
					}

					if (defaultSettings[index][index2]['type'] === 'textarea') {
						cloned = '';
						if (defaultSettings[index][index2]['clone'] === 'cloned') cloned = "[]";
						outputOptions += '<textarea name="et-'+index2+cloned+'" id="et-'+index2+'" cols="40" rows="10">'+defaultSettings[index][index2]['defaultvalue']+'</textarea>';
					}

					outputOptions += '\n<br/><small>'+ defaultSettings[index][index2]['description'] +'</small>';
					outputOptions += '\n</td>';

				}


			var width = jQuery(window).width(),
				tbHeight = jQuery(window).height(),
				tbWidth = ( 720 < width ) ? 720 : width;

			tbWidth = tbWidth - 80;
			tbHeight = tbHeight - 84;

			var tbOptions = "<div id='et_shortcodes_div'><form id='et_shortcodes'><table id='shortcodes_table' class='form-table et-"+ tag +"'>";
			tbOptions += outputOptions;
			tbOptions += '</table>\n<p class="submit">\n<input type="button" id="shortcodes-submit" class="button-primary" value="Ok" name="submit" /></p>\n</form></div>';

			var form = jQuery(tbOptions);

			var table = form.find('table');
			form.appendTo('body').hide();


			if (tag === 'tabs') {
				$moreTabs = jQuery('<p><a href="#" id="et_add_more_tabs"><?php esc_html_e( '+ Add One More Tab', $themename ); ?></a></p>').appendTo('form#et_shortcodes tbody');
				$moreTabsLink = jQuery('a#et_add_more_tabs');

				$moreTabsLink.bind('click',function() {
					var clonedElements = jQuery('form#et_shortcodes .cloned');

					newElements = clonedElements.slice(0,2).clone();

					var cloneNumber = clonedElements.length,
						labelNum = cloneNumber / 2;

					newElements.each(function(index){
						if ( index === 0 ) jQuery(this).css({'border-top':'1px solid #eeeeee'});

						var label = jQuery(this).find('label').attr('for'),
							newLabel = label + labelNum;

						jQuery(this).find('label').attr('for',newLabel);
						jQuery(this).find('input, textarea').attr('id',newLabel);
					});

					newElements.appendTo('form#et_shortcodes tbody');
					$moreTabs.appendTo('form#et_shortcodes tbody');
					return false;
				});
			}


			form.find('#shortcodes-submit').click(function(){

				var shortcode = '['+tag;

				for( var index in defaultSettings[tag]) {
					var value = table.find('#et-' + index).val();
					if (index === 'content') {
						content = value;
						continue;
					}

					if (defaultSettings[tag][index]['clone'] !== undefined) {
						content = 'cloned';
						continue;
					}

					if ( value !== defaultSettings[tag][index]['defaultvalue'] )
						shortcode += ' ' + index + '="' + value + '"';

				}

				var $et_slidertype = jQuery('#et-slidertype').val();

				shortcode += '] ' + "\n";

				if (content != '') {

					if (tag === 'tabs') {

						var $et_form = jQuery('form#et_shortcodes'),
							tabsOutput = '',
							$et_slidertype = jQuery('#et-slidertype').val();

						if ($et_slidertype === 'images') {
							prefix = 'image';
							dimensions = ' width="' + jQuery('#et-imagewidth').val() + '"'+' height="' + jQuery('#et-imageheight').val() + '"';
						} else {
							prefix = '';
							dimensions = '';
						}

						tabsOutput += '['+prefix+'tabcontainer]\n';
						$et_form.find("input[name='et-tabtext[]']").each(function(){
							tabsOutput += '['+prefix+'tabtext]'+jQuery(this).val()+'[/'+prefix+'tabtext]\n';
						});
						tabsOutput += '[/'+prefix+'tabcontainer]\n';

						if ($et_slidertype === 'simple' || $et_slidertype === 'images') tabsOutput = '';

						if ($et_slidertype != 'simple' && $et_slidertype != 'images') tabsOutput += '[tabcontent]\n';
						$et_form.find("textarea[name='et-tabcontent[]']").each(function(){
							tabsOutput += '['+prefix+'tab'+dimensions+']'+jQuery(this).val()+'[/'+prefix+'tab]'+"\n";
						});

						if ($et_slidertype != 'simple' && $et_slidertype != 'images') tabsOutput += '[/tabcontent]\n';

						content = tabsOutput;
					}

					if (tag === 'author') {
						var $et_form = jQuery('form#et_shortcodes');

						imageurl = $et_form.find('#et-imageurl').val();
						timthumb = $et_form.find('#et-timthumb').val();
						content = $et_form.find('#et-content').val();

						shortcode = "[author]\n[author_image timthumb='"+timthumb+"']"+imageurl+"[/author_image]\n[author_info]"+content+"[/author_info]\n";
						content = '';
					}

					shortcode += content;
					shortcode += '[/'+tag+'] ' + "\n";
				}

				tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode + ' ');

				tb_remove();
			});

			tb_show( 'ET ' + tag + ' Shortcode', '#TB_inline?width=' + tbWidth + '&height=' + tbHeight + '&inlineId=et_shortcodes_div' );
			jQuery('#et_shortcodes_div').remove();
			outputOptions = '';
		}

		jQuery(document).ready(function(){
			var buttonTypeField = jQuery('table.et-button select#et-type');

			buttonTypeField.live('change',function() {
				var optionsSmallButton = ['blue','lightblue','teal','silver','black','pink','purple','orange','green','red'],
					optionsBigButton = ['blue','purple','orange','green','red','teal'],
					options = '';

				if (jQuery(this).val() === 'big') {
					for (var i = 0; i < optionsBigButton.length; i++) {
						options += '<option value="' + optionsBigButton[i] + '">' + optionsBigButton[i] + '</option>';
					}

					if (!jQuery('select#et-icon').parents('tr.hidden').length) jQuery('select#et-icon').parents('tr').addClass('hidden');
					if (jQuery('select#et-color').parents('tr.hidden').length) jQuery('select#et-color').parents('tr').removeClass('hidden');
				}

				if (jQuery(this).val() === 'small') {
					for (var i = 0; i < optionsSmallButton.length; i++) {
						options += '<option value="' + optionsSmallButton[i] + '">' + optionsSmallButton[i] + '</option>';
					}
					if (!jQuery('select#et-icon').parents('tr.hidden').length) jQuery('select#et-icon').parents('tr').addClass('hidden');
					if (jQuery('select#et-color').parents('tr.hidden').length) jQuery('select#et-color').parents('tr').removeClass('hidden');
				}

				if (jQuery(this).val() === 'icon') {
					if (jQuery('select#et-icon').parents('tr.hidden').length) jQuery('select#et-icon').parents('tr').removeClass('hidden');

					if (!jQuery('select#et-color').parents('tr.hidden').length) jQuery('select#et-color').parents('tr').addClass('hidden');
				}

				if (options !== '') jQuery(this).parents('tbody').find('select#et-color').html(options);
			});

			var tabTypeField = jQuery('table.et-tabs select#et-slidertype');
			tabTypeField.live('change',function() {
				if (jQuery(this).val() === 'images') {
					if (!jQuery('.et-tabs #et-imagewidth').length) {
						$heightImage = jQuery('<tr><th><label for="et-imageheight"><?php esc_html_e( 'Image Height', $themename ); ?></label></th><td><input type="text" value="" id="et-imageheight" name="et-imageheight"><br><small></small></td></tr>').prependTo('form#et_shortcodes tbody');
						$widthImage = jQuery('<tr><th><label for="et-imagewidth"><?php esc_html_e( 'Image Width', $themename ); ?></label></th><td><input type="text" value="" id="et-imagewidth" name="et-imagewidth"><br><small></small></td></tr>').prependTo('form#et_shortcodes tbody');
					}

					if (typeof $heightImage != 'undefined') $heightImage.show();
					if (typeof $widthImage != 'undefined') $widthImage.show();

					jQuery('input[name^="et-tabtext"]').parents('tr.cloned').hide(); //hide tab text
				} else {
					if (typeof $heightImage != 'undefined') $heightImage.hide();
					if (typeof $widthImage != 'undefined') $widthImage.hide();

					if(jQuery(this).val() != 'simple') jQuery('input[name^="et-tabtext"]').parents('tr.cloned:hidden').show(); //show tab text
					else jQuery('input[name^="et-tabtext"]').parents('tr.cloned').hide();
				}
			});
		});
	</script>
<?php } ?>