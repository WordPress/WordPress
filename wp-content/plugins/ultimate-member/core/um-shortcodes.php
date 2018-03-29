<?php

class UM_Shortcodes {

	function __construct() {

		$this->message_mode = false;

		$this->loop = '';

		add_shortcode('ultimatemember', array(&$this, 'ultimatemember'  ), 1);

		add_shortcode('um_loggedin', array(&$this, 'um_loggedin'));
		add_shortcode('um_loggedout', array(&$this, 'um_loggedout'));
		add_shortcode('um_show_content', array(&$this, 'um_shortcode_show_content_for_role') );
		add_shortcode('ultimatemember_searchform', array(&$this, 'ultimatemember_searchform') );


		add_filter('body_class', array(&$this, 'body_class'), 0);

		$base_uri = apply_filters('um_emoji_base_uri', 'https://s.w.org/images/core/emoji/');

		$this->emoji[':)'] = $base_uri . '72x72/1f604.png';
		$this->emoji[':smiley:'] = $base_uri . '72x72/1f603.png';
		$this->emoji[':D'] = $base_uri . '72x72/1f600.png';
		$this->emoji[':$'] = $base_uri . '72x72/1f60a.png';
		$this->emoji[':relaxed:'] = $base_uri . '72x72/263a.png';
		$this->emoji[';)'] = $base_uri . '72x72/1f609.png';
		$this->emoji[':heart_eyes:'] = $base_uri . '72x72/1f60d.png';
		$this->emoji[':kissing_heart:'] = $base_uri . '72x72/1f618.png';
		$this->emoji[':kissing_closed_eyes:'] = $base_uri . '72x72/1f61a.png';
		$this->emoji[':kissing:'] = $base_uri . '72x72/1f617.png';
		$this->emoji[':kissing_smiling_eyes:'] = $base_uri . '72x72/1f619.png';
		$this->emoji[';P'] = $base_uri . '72x72/1f61c.png';
		$this->emoji[':P'] = $base_uri . '72x72/1f61b.png';
		$this->emoji[':stuck_out_tongue_closed_eyes:'] = $base_uri . '72x72/1f61d.png';
		$this->emoji[':flushed:'] = $base_uri . '72x72/1f633.png';
		$this->emoji[':grin:'] = $base_uri . '72x72/1f601.png';
		$this->emoji[':pensive:'] = $base_uri . '72x72/1f614.png';
		$this->emoji[':relieved:'] = $base_uri . '72x72/1f60c.png';
		$this->emoji[':unamused'] = $base_uri . '72x72/1f612.png';
		$this->emoji[':('] = $base_uri . '72x72/1f61e.png';
		$this->emoji[':persevere:'] = $base_uri . '72x72/1f623.png';
		$this->emoji[":'("] = $base_uri . '72x72/1f622.png';
		$this->emoji[':joy:'] = $base_uri . '72x72/1f602.png';
		$this->emoji[':sob:'] = $base_uri . '72x72/1f62d.png';
		$this->emoji[':sleepy:'] = $base_uri . '72x72/1f62a.png';
		$this->emoji[':disappointed_relieved:'] = $base_uri . '72x72/1f625.png';
		$this->emoji[':cold_sweat:'] = $base_uri . '72x72/1f630.png';
		$this->emoji[':sweat_smile:'] = $base_uri . '72x72/1f605.png';
		$this->emoji[':sweat:'] = $base_uri . '72x72/1f613.png';
		$this->emoji[':weary:'] = $base_uri . '72x72/1f629.png';
		$this->emoji[':tired_face:'] = $base_uri . '72x72/1f62b.png';
		$this->emoji[':fearful:'] = $base_uri . '72x72/1f628.png';
		$this->emoji[':scream:'] = $base_uri . '72x72/1f631.png';
		$this->emoji[':angry:'] = $base_uri . '72x72/1f620.png';
		$this->emoji[':rage:'] = $base_uri . '72x72/1f621.png';
		$this->emoji[':triumph'] = $base_uri . '72x72/1f624.png';
		$this->emoji[':confounded:'] = $base_uri . '72x72/1f616.png';
		$this->emoji[':laughing:'] = $base_uri . '72x72/1f606.png';
		$this->emoji[':yum:'] = $base_uri . '72x72/1f60b.png';
		$this->emoji[':mask:'] = $base_uri . '72x72/1f637.png';
		$this->emoji[':cool:'] = $base_uri . '72x72/1f60e.png';
		$this->emoji[':sleeping:'] = $base_uri . '72x72/1f634.png';
		$this->emoji[':dizzy_face:'] = $base_uri . '72x72/1f635.png';
		$this->emoji[':astonished:'] = $base_uri . '72x72/1f632.png';
		$this->emoji[':worried:'] = $base_uri . '72x72/1f61f.png';
		$this->emoji[':frowning:'] = $base_uri . '72x72/1f626.png';
		$this->emoji[':anguished:'] = $base_uri . '72x72/1f627.png';
		$this->emoji[':smiling_imp:'] = $base_uri . '72x72/1f608.png';
		$this->emoji[':imp:'] = $base_uri . '72x72/1f47f.png';
		$this->emoji[':open_mouth:'] = $base_uri . '72x72/1f62e.png';
		$this->emoji[':grimacing:'] = $base_uri . '72x72/1f62c.png';
		$this->emoji[':neutral_face:'] = $base_uri . '72x72/1f610.png';
		$this->emoji[':confused:'] = $base_uri . '72x72/1f615.png';
		$this->emoji[':hushed:'] = $base_uri . '72x72/1f62f.png';
		$this->emoji[':no_mouth:'] = $base_uri . '72x72/1f636.png';
		$this->emoji[':innocent:'] = $base_uri . '72x72/1f607.png';
		$this->emoji[':smirk:'] = $base_uri . '72x72/1f60f.png';
		$this->emoji[':expressionless:'] = $base_uri . '72x72/1f611.png';

	}

	/***
		***	@emoji support
	*/
	function emotize($content) {
		$content = stripslashes($content);
		foreach ($this->emoji as $code => $val) {
			$regex = str_replace(array('(', ')'), array("\\" . '(', "\\" . ')'), $code);
			$content = preg_replace('/(' . $regex . ')(\s|$)/', '<img src="' . $val . '" alt="' . $code . '" title="' . $code . '" class="emoji" />$2', $content);
		}
		return $content;
	}

	/***
		***	@extend body classes
	*/
	function body_class($classes) {
		global $ultimatemember;

		$array = $ultimatemember->permalinks->core;
		if (!$array) {
			return $classes;
		}

		foreach ($array as $slug => $info) {
			if (um_is_core_page($slug)) {

				$classes[] = 'um-page-' . $slug;

				if (is_user_logged_in()) {
					$classes[] = 'um-page-loggedin';
				} else {
					$classes[] = 'um-page-loggedout';
				}

			}
		}

		if( um_is_core_page('user') && um_is_user_himself() ){
			$classes[] = 'um-own-profile';
		}

		return $classes;
	}

	/***
		***	@Retrieve core login form
	*/
	function core_login_form() {
		$forms = get_posts(array('post_type' => 'um_form', 'posts_per_page' => 1, 'meta_key' => '_um_core', 'meta_value' => 'login'));
		$form_id = isset( $forms[0]->ID ) ? $forms[0]->ID: 0;

		return $form_id;
	}

	/***
		***	@load a compatible template
	*/
	function load_template($tpl) {
		global $ultimatemember;

		$loop = ($this->loop) ? $this->loop : '';

		if (isset($this->set_args) && is_array($this->set_args)) {
			$args = $this->set_args;
			extract($args);
		}

		$file = um_path . 'templates/' . $tpl . '.php';
		$theme_file = get_stylesheet_directory() . '/ultimate-member/templates/' . $tpl . '.php';

		if (file_exists($theme_file)) {
			$file = $theme_file;
		}

		if (file_exists($file)) {
			include $file;
		}

	}

	/***
		***	@Add class based on shortcode
	*/
	function get_class($mode, $args = array()) {

		global $ultimatemember;

		$classes = 'um-' . $mode;

		if (is_admin()) {
			$classes .= ' um-in-admin';
		}

		if (isset($ultimatemember->form->errors) && $ultimatemember->form->errors) {
			$classes .= ' um-err';
		}

		if ($ultimatemember->fields->editing == true) {
			$classes .= ' um-editing';
		}

		if ($ultimatemember->fields->viewing == true) {
			$classes .= ' um-viewing';
		}

		if (isset($args['template']) && $args['template'] != $args['mode']) {
			$classes .= ' um-' . $args['template'];
		}

		$classes = apply_filters('um_form_official_classes__hook', $classes);
		return $classes;
	}

	/***
		***	@Logged-in only content
	*/
	function um_loggedin($args = array(), $content = "") {
		global $ultimatemember;
		ob_start();

		$defaults = array(
			'lock_text' => __('This content has been restricted to logged in users only. Please <a href="{login_referrer}">login</a> to view this content.', 'ultimate-member'),
			'show_lock' => 'yes',
		);

		$args = wp_parse_args($args, $defaults);

		$args['lock_text'] = $this->convert_locker_tags($args['lock_text']);

		if (!is_user_logged_in()) {
			if ($args['show_lock'] == 'no') {
				echo '';
			} else {
				$ultimatemember->shortcodes->set_args = $args;
				$ultimatemember->shortcodes->load_template('login-to-view');
			}
		} else {
			echo do_shortcode($this->convert_locker_tags(wpautop($content)));
		}

		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	/***
		***	@Logged-out only content
	*/
	function um_loggedout($args = array(), $content = "") {
		global $ultimatemember;
		ob_start();

		// Hide for logged in users
		if (is_user_logged_in()) {
			echo '';
		} else {
			echo do_shortcode(wpautop($content));
		}

		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	/***
		***	@Shortcode
	*/
	function ultimatemember($args = array()) {
		return $this->load($args);
	}

	/***
		***	@Load a module with global function
	*/
	function load($args) {
		global $ultimatemember;
		ob_start();

		$defaults = array();
		$args = wp_parse_args($args, $defaults);

		// when to not continue
		$this->form_id = (isset($args['form_id'])) ? $args['form_id'] : null;
		if (!$this->form_id) {
			return;
		}

		$this->form_status = get_post_status($this->form_id);
		if ($this->form_status != 'publish') {
			return;
		}

		// get data into one global array
		$post_data = $ultimatemember->query->post_data($this->form_id);

		$args = apply_filters('um_pre_args_setup', $post_data);

		if (!isset($args['template'])) {
			$args['template'] = '';
		}

		if (isset($post_data['template']) && $post_data['template'] != $args['template']) {
			$args['template'] = $post_data['template'];
		}

		if (!$this->template_exists($args['template'])) {
			$args['template'] = $post_data['mode'];
		}

		if (!isset($post_data['template'])) {
			$post_data['template'] = $post_data['mode'];
		}

		$args = array_merge($post_data, $args);

		if (isset($args['use_globals']) && $args['use_globals'] == 1) {
			$args = array_merge($args, $this->get_css_args($args));
		} else {
			$args = array_merge($this->get_css_args($args), $args);
		}

		// filter for arguments
		$args = apply_filters('um_shortcode_args_filter', $args);

		extract($args, EXTR_SKIP);

		// for profiles only
		if ($mode == 'profile' && um_profile_id() && isset($args['role']) && $args['role'] &&
			$args['role'] != $ultimatemember->query->get_role_by_userid(um_profile_id())) {
			return;
		}

		// start loading the template here
		do_action("um_pre_{$mode}_shortcode", $args);

		do_action("um_before_form_is_loaded", $args);

		do_action("um_before_{$mode}_form_is_loaded", $args);

		$this->template_load($template, $args);

		$this->dynamic_css($args);

		if (um_get_requested_user() || $mode == 'logout') {
			um_reset_user();
		}

		do_action('um_after_everything_output');

		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	/***
		***	@Get dynamic css args
	*/
	function get_css_args($args) {
		$arr = um_styling_defaults($args['mode']);
		$arr = array_merge($arr, array('form_id' => $args['form_id'], 'mode' => $args['mode']));
		return $arr;
	}

	/***
		***	@Load dynamic css
	*/
	function dynamic_css($args = array()) {
		global $ultimatemember;
		extract($args);

		$global = um_path . 'assets/dynamic_css/dynamic_global.php';

		if (isset($mode)) {
			$file = um_path . 'assets/dynamic_css/dynamic_' . $mode . '.php';
		}

		include $global;

		if (isset($file) && file_exists($file)) {
			include $file;
		}

		if (isset($args['custom_css'])) {
			$css = $args['custom_css'];
			?><!-- ULTIMATE MEMBER FORM INLINE CSS BEGIN --><style type="text/css"><?php print $ultimatemember->styles->minify($css);?></style><!-- ULTIMATE MEMBER FORM INLINE CSS END --><?php
}

	}

	/***
		***	@Loads a template file
	*/
	function template_load($template, $args = array()) {
		global $ultimatemember;
		if (is_array($args)) {
			$ultimatemember->shortcodes->set_args = $args;
		}
		$ultimatemember->shortcodes->load_template($template);
	}

	/***
		***	@Checks if a template file exists
	*/
	function template_exists($template) {

		$file = um_path . 'templates/' . $template . '.php';
		$theme_file = get_stylesheet_directory() . '/ultimate-member/templates/' . $template . '.php';

		if (file_exists($theme_file) || file_exists($file)) {
			return true;
		}

		return false;
	}

	/***
		***	@Get File Name without path and extension
	*/
	function get_template_name($file) {
		$file = basename($file);
		$file = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file);
		return $file;
	}

	/***
		***	@Get Templates
	*/
	function get_templates($excluded = null) {

		if ($excluded) {
			$array[$excluded] = __('Default Template', 'ultimate-member');
		}

		$paths[] = glob(um_path . 'templates/' . '*.php');

		if (file_exists(get_stylesheet_directory() . '/ultimate-member/templates/')) {
			$paths[] = glob(get_stylesheet_directory() . '/ultimate-member/templates/' . '*.php');
		}

		if( isset( $paths ) && ! empty( $paths ) ){
			
			foreach ($paths as $k => $files) {

				if( isset( $files ) && ! empty( $files ) ){
					
					foreach ($files as $file) {

						$clean_filename = $this->get_template_name( $file );

						if ( 0 === strpos( $clean_filename, $excluded ) ) {

							$source = file_get_contents( $file );
							$tokens = token_get_all( $source );
							$comment = array(
								T_COMMENT, // All comments since PHP5
								T_DOC_COMMENT, // PHPDoc comments
							);
								foreach ( $tokens as $token ) {
									if ( in_array( $token[0], $comment ) && strstr( $token[1], '/* Template:' ) && $clean_filename != $excluded ) {
										$txt = $token[1];
										$txt = str_replace('/* Template: ', '', $txt );
										$txt = str_replace(' */', '', $txt );
										$array[ $clean_filename ] = $txt;
									}
								}

						}

					}

				}

			}
			
		}

		return $array;

	}

	/***
		***	@Get Shortcode for given form ID
	*/
	function get_shortcode($post_id) {
		$shortcode = '[ultimatemember form_id=' . $post_id . ']';
		return $shortcode;
	}

	/***
		***	@convert access lock tags
	*/
	function convert_locker_tags($str) {
		$str = um_convert_tags($str);
		return $str;
	}

	/***
		***	@convert user tags in a string
	*/
	function convert_user_tags($str) {

		$value = '';

		$pattern_array = array(
			'{first_name}',
			'{last_name}',
			'{display_name}',
			'{user_avatar_small}',
			'{username}',
		);

		$pattern_array = apply_filters('um_allowed_user_tags_patterns', $pattern_array);

		$matches = false;
		foreach ($pattern_array as $pattern) {

			if (preg_match($pattern, $str)) {

				$usermeta = str_replace('{', '', $pattern);
				$usermeta = str_replace('}', '', $usermeta);

				if ($usermeta == 'user_avatar_small') {
					$value = get_avatar(um_user('ID'), 40);
				} elseif (um_user($usermeta)) {
					$value = um_user($usermeta);
				}

				if ($usermeta == 'username') {
					$value = um_user('user_login');
				}

				$value = apply_filters("um_profile_tag_hook__{$usermeta}", $value, um_user('ID'));

				if ($value) {
					$str = preg_replace('/' . $pattern . '/', $value, $str);
				}

			}

		}

		return $str;
	}

	/**
	 * Shortcode: Show custom content to specific role
	 *
	 * Show content to specific roles
	 * [um_show_content roles='member'] <!-- insert content here -->  [/um_show_content]
	 * You can add multiple target roles, just use ',' e.g.  [um_show_content roles='member,candidates,pets']
	 *
	 * Hide content from specific roles
	 * [um_show_content not='contributors'] <!-- insert content here -->  [/um_show_content]
	 * You can add multiple target roles, just use ',' e.g.  [um_show_content roles='member,candidates,pets']
	 *
	 * @param  array $atts
	 * @param  string $content
	 * @return string
	 */
	function um_shortcode_show_content_for_role( $atts = array() , $content = '' ) {

		global $user_ID;

		if( ! is_user_logged_in() ) {
			return;
		}

	    $a = shortcode_atts( array(
	        'roles' => '',
	        'not' => '',
	        'is_profile' => false,
	    ), $atts );

	    if( $a['is_profile'] ){
	    	um_fetch_user( um_profile_id() );
	    }else{
	    	um_fetch_user( $user_ID );
	    }

	    

	    $current_user_role = um_user('role');

	    if( isset( $a['not'] ) && ! empty( $a['not'] ) && isset( $a['roles'] ) && ! empty( $a['roles'] ) ){
	    	return do_shortcode( $content );
	    }

	    if( isset( $a['not'] ) && ! empty( $a['not'] ) ){

	    	$not_in_roles = explode(",", $a['not'] );

			if( is_array( $not_in_roles ) &&  ! in_array( $current_user_role, $not_in_roles ) ){
					return do_shortcode( $content );
		    }

		}else{

		    $roles = explode(",", $a['roles'] );

		    if(is_array( $roles ) && in_array( $current_user_role, $roles )  ){
		    		return do_shortcode( $content );
		    }


		}

	    return '';
	}

	public function ultimatemember_searchform($args = array(), $content = "") {
		// turn off buffer
		ob_start();

		// load template
		$this->load_template( 'searchform' );

		// get the buffer
		$template = ob_get_contents();

		// clear the buffer
		ob_end_clean();

		return $template;
	}

}
