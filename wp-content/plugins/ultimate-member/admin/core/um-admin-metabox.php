<?php

class UM_Admin_Metabox {

	function __construct() {

		$this->slug = 'ultimatemember';
		
		$this->in_edit = false;
		$this->edit_mode_value = null;

		add_action('admin_head', array(&$this, 'admin_head'), 9);
		add_action('admin_footer', array(&$this, 'load_modal_content'), 9);

		add_action( 'load-post.php', array(&$this, 'add_metabox'), 9 );
		add_action( 'load-post-new.php', array(&$this, 'add_metabox'), 9 );

	}
	
	/***
	***	@Boolean check if we're viewing UM backend
	***/
	function is_UM_admin(){
		global $current_screen;
		$screen_id = $current_screen->id;
		if ( is_admin() && ( strstr( $screen_id, 'ultimatemember'  ) || strstr( $screen_id, 'um_') || strstr($screen_id, 'user') || strstr($screen_id, 'profile') ) )
			return true;
		return false;
	}
	
	/***
	***	@check that we're on a custom post type supported by UM
	***/
	function is_plugin_post_type(){
		if (isset($_REQUEST['post_type'])){
			$post_type = $_REQUEST['post_type'];
			if ( in_array($post_type, array('um_form','um_role','um_directory'))){
				return true;
			}
		} else if ( isset($_REQUEST['action'] ) && $_REQUEST['action'] == 'edit') {
			$post_type = get_post_type();
			if ( in_array($post_type, array('um_form','um_role','um_directory'))){
				return true;
			}
		}
		return false;
	}
	
	/***
	***	@Gets the role meta
	***/
	function get_custom_post_meta($id){
		$all_meta = get_post_custom($id);
		foreach($all_meta as $k=>$v){
			if (strstr($k, '_um_')){
				$um_meta[$k] = $v;
			}
		}
		if (isset($um_meta))
			return $um_meta;
	}
	
	/***
	***	@Runs on admin head
	***/
	function admin_head(){
		global $post;
		if ( $this->is_plugin_post_type() && isset($post->ID) ){
			$this->postmeta = $this->get_custom_post_meta($post->ID);
		}
	}
	
	/***
	***	@add a helper tooltip
	***/
	function _tooltip( $text ){
	
		$output = '<span class="um-admin-tip n">';
		$output .= '<span class="um-admin-tipsy-n" title="'.$text.'"><i class="dashicons dashicons-editor-help"></i></span>';
		$output .= '</span>';
		
		return $output;
	
	}
	
	/***
	***	@add a helper tooltip
	***/
	function tooltip( $text, $e = false ){
	
		?>
		
		<span class="um-admin-tip">
			<?php if ($e == 'e' ) { ?>
			<span class="um-admin-tipsy-e" title="<?php echo $text; ?>"><i class="dashicons dashicons-editor-help"></i></span>
			<?php } else { ?>
			<span class="um-admin-tipsy-w" title="<?php echo $text; ?>"><i class="dashicons dashicons-editor-help"></i></span>
			<?php } ?>
		</span>
		
		<?php
	
	}
	
	/***
	***	@on/off UI
	***/
	function ui_on_off( $id, $default=0, $is_conditional=false, $cond1='', $cond1_show='', $cond1_hide='', $yes='', $no='' ) {

		$meta = (string)get_post_meta( get_the_ID(), $id, true );
		if ( $meta === '0' && $default > 0 ) {
			$default = $meta;
		}
		
		$yes = ( !empty( $yes ) ) ? $yes : __('Yes');
		$no = ( !empty( $no ) ) ? $no : __('No');
		
		if (isset($this->postmeta[$id][0]) || $meta ) {
			$active = ( isset( $this->postmeta[$id][0] ) ) ? $this->postmeta[$id][0] : $meta;
		} else {
			$active = $default;
		}
		
		if ($is_conditional == true) {
			$is_conditional = ' class="um-adm-conditional" data-cond1="'.$cond1.'" data-cond1-show="'.$cond1_show.'" data-cond1-hide="'.$cond1_hide.'"';
		}
		
		?>

		<span class="um-admin-yesno">
			<span class="btn pos-<?php echo $active; ?>"></span>
			<span class="yes" data-value="1"><?php echo $yes; ?></span>
			<span class="no" data-value="0"><?php echo $no; ?></span>
			<input type="hidden" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo $active; ?>" <?php echo $is_conditional; ?> />
		</span>
	
		<?php
	}

	/***
	***	@Init the metaboxes
	***/
	function add_metabox() {
		global $current_screen;
		
		if( $current_screen->id == 'um_form'){
			add_action( 'add_meta_boxes', array(&$this, 'add_metabox_form'), 1 );
			add_action( 'save_post', array(&$this, 'save_metabox_form'), 10, 2 );
		}
		
		if( $current_screen->id == 'um_role'){
			add_action( 'add_meta_boxes', array(&$this, 'add_metabox_role'), 1 );
			add_action( 'save_post', array(&$this, 'save_metabox_role'), 10, 2 );
		}
		
		if( $current_screen->id == 'um_directory'){
			add_action( 'add_meta_boxes', array(&$this, 'add_metabox_directory'), 1 );
			add_action( 'save_post', array(&$this, 'save_metabox_directory'), 10, 2 );
		}
		
	}
	
	/***
	***	@load a directory metabox
	***/
	function load_metabox_directory( $object, $box ) {
		global $ultimatemember;
		$box['id'] = str_replace('um-admin-form-','', $box['id']);
		include_once um_path . 'admin/templates/directory/'. $box['id'] . '.php';
		wp_nonce_field( basename( __FILE__ ), 'um_admin_save_metabox_directory_nonce' );
	}
	
	/***
	***	@load a role metabox
	***/
	function load_metabox_role( $object, $box ) {
		global $ultimatemember, $post;

		$box['id'] = str_replace('um-admin-form-','', $box['id']);

		if ( $box['id'] == 'builder' ) {
			$UM_Builder = new UM_Admin_Builder();
			$UM_Builder->form_id = get_the_ID();
		}
		
		preg_match('#\{.*?\}#s', $box['id'], $matches);
		
		if ( isset($matches[0]) ){
			$path = $matches[0];
			$box['id'] = preg_replace('~(\\{[^}]+\\})~','', $box['id'] );
		} else {
			$path = um_path;
		}
		
		$path = str_replace('{','', $path );
		$path = str_replace('}','', $path );
		
		include_once $path . 'admin/templates/role/'. $box['id'] . '.php';
		wp_nonce_field( basename( __FILE__ ), 'um_admin_save_metabox_role_nonce' );
	}
	
	/***
	***	@load a form metabox
	***/
	function load_metabox_form( $object, $box ) {
		global $ultimatemember, $post;

		$box['id'] = str_replace('um-admin-form-','', $box['id']);

		if ( $box['id'] == 'builder' ) {
			$UM_Builder = new UM_Admin_Builder();
			$UM_Builder->form_id = get_the_ID();
		}
		
		preg_match('#\{.*?\}#s', $box['id'], $matches);
		
		if ( isset($matches[0]) ){
			$path = $matches[0];
			$box['id'] = preg_replace('~(\\{[^}]+\\})~','', $box['id'] );
		} else {
			$path = um_path;
		}
		
		$path = str_replace('{','', $path );
		$path = str_replace('}','', $path );
		
		include_once $path . 'admin/templates/form/'. $box['id'] . '.php';
		wp_nonce_field( basename( __FILE__ ), 'um_admin_save_metabox_form_nonce' );
	}
	
	/***
	***	@add directory metabox
	***/
	function add_metabox_directory() {

		add_meta_box('um-admin-form-general', __('General Options'), array(&$this, 'load_metabox_directory'), 'um_directory', 'normal', 'default');
		add_meta_box('um-admin-form-profile', __('Profile Card'), array(&$this, 'load_metabox_directory'), 'um_directory', 'normal', 'default');
		add_meta_box('um-admin-form-search', __('Search Options'), array(&$this, 'load_metabox_directory'), 'um_directory', 'normal', 'default');
		add_meta_box('um-admin-form-pagination', __('Results &amp; Pagination'), array(&$this, 'load_metabox_directory'), 'um_directory', 'normal', 'default');
		
		add_meta_box('um-admin-form-shortcode', __('Shortcode'), array(&$this, 'load_metabox_directory'), 'um_directory', 'side', 'default');
		
		add_meta_box('um-admin-form-appearance', __('Styling: General'), array(&$this, 'load_metabox_directory'), 'um_directory', 'side', 'default');
		
		add_meta_box('um-admin-form-profile_card', __('Styling: Profile Card'), array(&$this, 'load_metabox_directory'), 'um_directory', 'side', 'default');

	}
	
	/***
	***	@add role metabox
	***/
	function add_metabox_role() {

		add_meta_box('um-admin-form-sync', __('Sync with WordPress Role','ultimate-member'), array(&$this, 'load_metabox_role'), 'um_role', 'side', 'default');
		
		add_meta_box('um-admin-form-admin', __('Administrative Permissions','ultimate-member'), array(&$this, 'load_metabox_role'), 'um_role', 'normal', 'default');
		
		add_meta_box('um-admin-form-general', __('General Permissions','ultimate-member'), array(&$this, 'load_metabox_role'), 'um_role', 'normal', 'default');
		
		add_meta_box('um-admin-form-profile', __('Profile Access','ultimate-member'), array(&$this, 'load_metabox_role'), 'um_role', 'normal', 'default');
		
		add_meta_box('um-admin-form-home', __('Homepage Options','ultimate-member'), array(&$this, 'load_metabox_role'), 'um_role', 'normal', 'default');
		
		add_meta_box('um-admin-form-register', __('Registration Options','ultimate-member'), array(&$this, 'load_metabox_role'), 'um_role', 'normal', 'default');
		
		add_meta_box('um-admin-form-login', __('Login Options','ultimate-member'), array(&$this, 'load_metabox_role'), 'um_role', 'normal', 'default');
		
		add_meta_box('um-admin-form-logout', __('Logout Options','ultimate-member'), array(&$this, 'load_metabox_role'), 'um_role', 'normal', 'default');
		
		add_meta_box('um-admin-form-delete', __('Delete Options','ultimate-member'), array(&$this, 'load_metabox_role'), 'um_role', 'normal', 'default');
	
		do_action('um_admin_custom_role_metaboxes');
		
	}
	
	/***
	***	@add form metabox
	***/
	function add_metabox_form() {
		
		add_meta_box('um-admin-form-mode', __('Select Form Type'), array(&$this, 'load_metabox_form'), 'um_form', 'normal', 'default');
		add_meta_box('um-admin-form-builder', __('Form Builder'), array(&$this, 'load_metabox_form'), 'um_form', 'normal', 'default');
		add_meta_box('um-admin-form-shortcode', __('Shortcode'), array(&$this, 'load_metabox_form'), 'um_form', 'side', 'default');
		
		add_meta_box('um-admin-form-register_customize', __('Customize this form'), array(&$this, 'load_metabox_form'), 'um_form', 'side', 'default');
		add_meta_box('um-admin-form-register_css', __('Custom CSS'), array(&$this, 'load_metabox_form'), 'um_form', 'side', 'default');
		
		do_action('um_admin_custom_register_metaboxes');

		add_meta_box('um-admin-form-profile_customize', __('Customize this form'), array(&$this, 'load_metabox_form'), 'um_form', 'side', 'default');
		add_meta_box('um-admin-form-profile_settings', __('User Meta'), array(&$this, 'load_metabox_form'), 'um_form', 'side', 'default');
		add_meta_box('um-admin-form-profile_css', __('Custom CSS'), array(&$this, 'load_metabox_form'), 'um_form', 'side', 'default');
		
		do_action('um_admin_custom_profile_metaboxes');
		
		add_meta_box('um-admin-form-login_customize', __('Customize this form'), array(&$this, 'load_metabox_form'), 'um_form', 'side', 'default');
		add_meta_box('um-admin-form-login_settings', __('Options'), array(&$this, 'load_metabox_form'), 'um_form', 'side', 'default');
		add_meta_box('um-admin-form-login_css', __('Custom CSS'), array(&$this, 'load_metabox_form'), 'um_form', 'side', 'default');
		
		do_action('um_admin_custom_login_metaboxes');

	}
	
	/***
	***	@save directory metabox
	***/
	function save_metabox_directory( $post_id, $post ) {
		global $wpdb;

		// validate nonce
		if ( !isset( $_POST['um_admin_save_metabox_directory_nonce'] ) || !wp_verify_nonce( $_POST['um_admin_save_metabox_directory_nonce'], basename( __FILE__ ) ) ) return $post_id;

		// validate post type
		if ( $post->post_type != 'um_directory' ) return $post_id;
		
		// validate user
		$post_type = get_post_type_object( $post->post_type );
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) ) return $post_id;

		$where = array( 'ID' => $post_id );
		if (empty($_POST['post_title'])) $_POST['post_title'] = 'Directory #'.$post_id;
        $wpdb->update( $wpdb->posts, array( 'post_title' => $_POST['post_title'] ), $where );
		
		// save
		delete_post_meta( $post_id, '_um_roles' );
		delete_post_meta( $post_id, '_um_tagline_fields' );
		delete_post_meta( $post_id, '_um_reveal_fields' );
		delete_post_meta( $post_id, '_um_search_fields' );
		delete_post_meta( $post_id, '_um_roles_can_search' );
		delete_post_meta( $post_id, '_um_show_these_users' );
		foreach( $_POST as $k => $v ) {
			if ( $k == '_um_show_these_users' && trim( $_POST[ $k ] ) ) {
				$v = preg_split('/[\r\n]+/', $v, -1, PREG_SPLIT_NO_EMPTY);
			}
			if (strstr($k, '_um_')){
				update_post_meta( $post_id, $k, $v);
			}
		}
		
	}
	
	/***
	***	@save role metabox
	***/
	function save_metabox_role( $post_id, $post ) {
		global $wpdb;

		// validate nonce
		if ( !isset( $_POST['um_admin_save_metabox_role_nonce'] ) || !wp_verify_nonce( $_POST['um_admin_save_metabox_role_nonce'], basename( __FILE__ ) ) ) return $post_id;

		// validate post type
		if ( $post->post_type != 'um_role' ) return $post_id;
		
		// validate user
		$post_type = get_post_type_object( $post->post_type );
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) ) return $post_id;

		$where = array( 'ID' => $post_id );
		if (empty($_POST['post_title'])) $_POST['post_title'] = 'Role #'.$post_id;
        $wpdb->update( $wpdb->posts, array( 'post_title' => $_POST['post_title'], 'post_name' => sanitize_title( $_POST['post_title'] ) ), $where );
		
		// save
		delete_post_meta( $post_id, '_um_can_view_roles' );
		delete_post_meta( $post_id, '_um_can_edit_roles' );
		delete_post_meta( $post_id, '_um_can_delete_roles' );
		
		do_action('um_admin_before_saving_role_meta', $post_id );
		
		do_action('um_admin_before_save_role', $post_id, $post );
		
		foreach( $_POST as $k => $v ) {
			if (strstr($k, '_um_')){
				update_post_meta( $post_id, $k, $v);
			}
		}

		do_action('um_admin_after_editing_role', $post_id, $post);
		
		do_action('um_admin_after_save_role', $post_id, $post );
		
	}
	
	/***
	***	@save form metabox
	***/
	function save_metabox_form( $post_id, $post ) {
		global $wpdb;

		// validate nonce
		if ( !isset( $_POST['um_admin_save_metabox_form_nonce'] ) || !wp_verify_nonce( $_POST['um_admin_save_metabox_form_nonce'], basename( __FILE__ ) ) ) return $post_id;

		// validate post type
		if ( $post->post_type != 'um_form' ) return $post_id;
		
		// validate user
		$post_type = get_post_type_object( $post->post_type );
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) ) return $post_id;

		$where = array( 'ID' => $post_id );
		if (empty($_POST['post_title'])) $_POST['post_title'] = 'Form #'.$post_id;
        $wpdb->update( $wpdb->posts, array( 'post_title' => $_POST['post_title'] ), $where );
		
		// save
		delete_post_meta( $post_id, '_um_profile_metafields' );
		foreach( $_POST as $k => $v ) {
			if (strstr($k, '_um_')){
				update_post_meta( $post_id, $k, $v);
			}
		}

	}
	
	/***
	***	@Load modal content
	***/
	function load_modal_content(){
	
		global 	$ultimatemember;
		
		$screen = get_current_screen();
		if ( $this->is_UM_admin() ) {
			foreach( glob( um_path . 'admin/templates/modal/*.php' ) as $modal_content) {
				include_once $modal_content;
			}
		}
		
		// needed on forms only
		if ( !isset( $this->is_loaded ) && isset( $screen->id ) && strstr( $screen->id, 'um_form' ) ) {
		
			$settings['textarea_rows'] = 8;
			
			echo '<div class="um-hidden-editor-edit" style="display:none;">';
			wp_editor( '', 'um_editor_edit', $settings );
			echo '</div>';
			
			echo '<div class="um-hidden-editor-new" style="display:none;">';
			wp_editor( '', 'um_editor_new', $settings );
			echo '</div>';
			
		}

	}
	
	/***
	***	@Show field input for edit
	***/
	function field_input ( $attribute, $form_id=null, $field_args = array() ) {
	
		global 	$ultimatemember;
		
		if ( $this->in_edit == true ) { // we're editing a field
			$real_attr = substr($attribute, 1);
			$this->edit_mode_value = (isset( $this->edit_array[ $real_attr ] ) ) ?  $this->edit_array[ $real_attr ] : null;
		}

		switch($attribute) {
		
			default:
			
				do_action("um_admin_field_edit_hook{$attribute}", $this->edit_mode_value);
				
				break;
				
			case '_visibility':
				?>
				
					<p><label for="_visibility">Visibility <?php $this->tooltip( __('Select where this field should appear. This option should only be changed on the profile form and allows you to show a field in one mode only (edit or view) or in both modes.','ultimate-member') ); ?></label>
						<select name="_visibility" id="_visibility" class="umaf-selectjs" style="width: 100%">
							<option value="all"  <?php selected( 'all', $this->edit_mode_value ); ?>>View everywhere</option>
							<option value="edit" <?php selected( 'edit', $this->edit_mode_value ); ?>>Edit mode only</option>
							<option value="view" <?php selected( 'view', $this->edit_mode_value ); ?>>View mode only</option>
						</select>
					</p>
					
				<?php
				break;
				
			case '_conditional_action':
			case '_conditional_action1':
			case '_conditional_action2':
			case '_conditional_action3':
			case '_conditional_action4':
				?>
				
					<p>
						<select name="<?php echo $attribute; ?>" id="<?php echo $attribute; ?>" class="umaf-selectjs" style="width: 90px">
	
							<option></option>
							
							<?php
							$actions = array('show','hide');
							foreach( $actions as $action ) {
							?>
							
							<option value="<?php echo $action; ?>" <?php selected( $action, $this->edit_mode_value ); ?>><?php echo $action; ?></option>
							
							<?php } ?>

						</select>
						
						&nbsp;&nbsp;<?php _e('If'); ?>
					</p>
				
				<?php
				break;
				
			case '_conditional_field':
			case '_conditional_field1':
			case '_conditional_field2':
			case '_conditional_field3':
			case '_conditional_field4':
				?>
				
					<p>
						<select name="<?php echo $attribute; ?>" id="<?php echo $attribute; ?>" class="umaf-selectjs" style="width: 150px">
							
							<option></option>
							
							<?php
							$fields = $ultimatemember->query->get_attr( 'custom_fields', $form_id );
							foreach( $fields as $key => $array ) {
								if ( isset( $array['title'] ) && isset( $this->edit_array['metakey'] ) && $key != $this->edit_array['metakey'] ) {
							?>
							
							<option value="<?php echo $key; ?>" <?php selected( $key, $this->edit_mode_value ); ?>><?php echo $array['title']; ?></option>
							
							<?php } } ?>

						</select>
					</p>
						
				<?php
				break;
				
			case '_conditional_operator':
			case '_conditional_operator1':
			case '_conditional_operator2':
			case '_conditional_operator3':
			case '_conditional_operator4':
				?>
				
					<p>
						<select name="<?php echo $attribute; ?>" id="<?php echo $attribute; ?>" class="umaf-selectjs" style="width: 150px">
	
							<option></option>

							<?php
							$operators = array('empty','not empty','equals to','not equals','greater than','less than','contains');
							foreach( $operators as $operator ) {
							?>
							
							<option value="<?php echo $operator; ?>" <?php selected( $operator, $this->edit_mode_value ); ?>><?php echo $operator; ?></option>
							
							<?php } ?>

						</select>
					</p>
				
				<?php
				break;
				
			case '_conditional_value':
			case '_conditional_value1':
			case '_conditional_value2':
			case '_conditional_value3':
			case '_conditional_value4':
				?>
				
					<p>
						<input type="text" name="<?php echo $attribute; ?>" id="<?php echo $attribute; ?>" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : 0; ?>" placeholder="<?php _e('Value'); ?>" style="width: 150px!important;position: relative;top: -1px;" />
					</p>
					
				<?php
				break;
				
			case '_validate':
				?>
				
					<p><label for="_validate">Validate <?php $this->tooltip('Does this field require a special validation'); ?></label>
						<select name="_validate" id="_validate" data-placeholder="Select a validation type..." class="umaf-selectjs um-adm-conditional" data-cond1='custom' data-cond1-show='_custom_validate' style="width: 100%">
						
						<option value="" <?php selected( '', $this->edit_mode_value ); ?>></option>	
						
						<?php foreach( $ultimatemember->builtin->validation_types() as $key => $name ) { ?>
							<?php 
								$continue = apply_filters("um_builtin_validation_types_continue_loop", true, $key, $form_id, $field_args );
							if( $continue ){ ?>
							<option value="<?php echo $key; ?>" <?php selected( $key, $this->edit_mode_value ); ?>><?php echo $name; ?></option>
							<?php } ?>
						<?php } ?>

						</select>
					</p>
					
				<?php
				break;
				
			case '_custom_validate':
				?>
				
					<p class="_custom_validate"><label for="_custom_validate">Custom Action <?php $this->tooltip('If you want to apply your custom validation, you can use action hooks to add custom validation. Please refer to documentation for further details.'); ?></label>
						<input type="text" name="_custom_validate" id="_custom_validate" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : ''; ?>" />
					</p>
					
				<?php
				break;
				
			case '_icon':
				
				if ( $this->set_field_type == 'row' ) {
					$back = 'UM_edit_row';
				
				?>
					
					<p class="_heading_text"><label for="_icon">Icon <?php $this->tooltip('Select an icon to appear in the field. Leave blank if you do not want an icon to show in the field.'); ?></label>
						
						<a href="#" class="button" data-modal="UM_fonticons" data-modal-size="normal" data-dynamic-content="um_admin_fonticon_selector" data-arg1="" data-arg2="" data-back="<?php echo $back; ?>">Choose Icon</a>
						
						<span class="um-admin-icon-value"><?php if ( $this->edit_mode_value ) { ?><i class="<?php echo $this->edit_mode_value; ?>"></i><?php } else { ?>No Icon<?php } ?></span>
						
						<input type="hidden" name="_icon" id="_icon" value="<?php echo (isset( $this->edit_mode_value ) ) ? $this->edit_mode_value : ''; ?>" />
						
						<?php if ( $this->edit_mode_value ) { ?>
						<span class="um-admin-icon-clear show"><i class="um-icon-android-cancel"></i></span>
						<?php } else { ?>
						<span class="um-admin-icon-clear"><i class="um-icon-android-cancel"></i></span>
						<?php } ?>
						
					</p>
				
				<?php } else {
				
				if ( $this->in_edit ) {
					$back = 'UM_edit_field';
				} else {
					$back = 'UM_add_field';
				}
				
				?>

				<div class="um-admin-tri">

					<p><label for="_icon">Icon <?php $this->tooltip('Select an icon to appear in the field. Leave blank if you do not want an icon to show in the field.'); ?></label>
						
						<a href="#" class="button" data-modal="UM_fonticons" data-modal-size="normal" data-dynamic-content="um_admin_fonticon_selector" data-arg1="" data-arg2="" data-back="<?php echo $back; ?>">Choose Icon</a>
						
						<span class="um-admin-icon-value"><?php if ( $this->edit_mode_value ) { ?><i class="<?php echo $this->edit_mode_value; ?>"></i><?php } else { ?>No Icon<?php } ?></span>
						
						<input type="hidden" name="_icon" id="_icon" value="<?php echo (isset( $this->edit_mode_value ) ) ? $this->edit_mode_value : ''; ?>" />
						
						<?php if ( $this->edit_mode_value ) { ?>
						<span class="um-admin-icon-clear show"><i class="um-icon-android-cancel"></i></span>
						<?php } else { ?>
						<span class="um-admin-icon-clear"><i class="um-icon-android-cancel"></i></span>
						<?php } ?>
						
					</p>

				</div>
				
				<?php
				
				}
				
				break;
				
			case '_css_class':
				?>
				
					<p><label for="_css_class">CSS Class <?php $this->tooltip('Specify a custom CSS class to be applied to this element'); ?></label>
						<input type="text" name="_css_class" id="_css_class" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : ''; ?>" />
					</p>
					
				<?php
				break;
				
			case '_width':
				?>
				
					<p><label for="_width">Thickness (in pixels) <?php $this->tooltip('This is the width in pixels, e.g. 4 or 2, etc'); ?></label>
						<input type="text" name="_width" id="_width" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : 4; ?>" />
					</p>
					
				<?php
				break;
				
			case '_divider_text':
				?>
				
					<p><label for="_divider_text">Optional Text <?php $this->tooltip( __('Optional text to include with the divider','ultimate-member') ); ?></label>
						<input type="text" name="_divider_text" id="_divider_text" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : ''; ?>" />
					</p>
					
				<?php
				break;
				
			case '_padding':
				?>
				
					<p><label for="_padding">Padding <?php $this->tooltip('Set padding for this section'); ?></label>
						<input type="text" name="_padding" id="_padding" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : '0px 0px 0px 0px'; ?>" />
					</p>
					
				<?php
				break;
				
			case '_margin':
				?>
				
					<p><label for="_margin">Margin <?php $this->tooltip('Set margin for this section'); ?></label>
						<input type="text" name="_margin" id="_margin" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : '0px 0px 30px 0px'; ?>" />
					</p>
					
				<?php
				break;
				
			case '_border':
				?>
				
					<p><label for="_border">Border <?php $this->tooltip('Set border for this section'); ?></label>
						<input type="text" name="_border" id="_border" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : '0px 0px 0px 0px'; ?>" />
					</p>
					
				<?php
				break;
				
			case '_borderstyle':
				?>
				
					<p><label for="_borderstyle">Style <?php $this->tooltip('Choose the border style'); ?></label>
						<select name="_borderstyle" id="_borderstyle" class="umaf-selectjs" style="width: 100%">
							<option value="solid"  <?php selected( 'solid', $this->edit_mode_value ); ?>>Solid</option>
							<option value="dotted" <?php selected( 'dotted', $this->edit_mode_value ); ?>>Dotted</option>
							<option value="dashed" <?php selected( 'dashed', $this->edit_mode_value ); ?>>Dashed</option>
							<option value="double" <?php selected( 'double', $this->edit_mode_value ); ?>>Double</option>
						</select>
					</p>
					
				<?php
				break;
				
			case '_borderradius':
				?>
				
					<p><label for="_borderradius">Border Radius <?php $this->tooltip('Rounded corners can be applied by setting a pixels value here. e.g. 5px'); ?></label>
						<input type="text" name="_borderradius" id="_borderradius" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : '0px'; ?>" />
					</p>
					
				<?php
				break;
				
			case '_bordercolor':
				?>
				
					<p><label for="_bordercolor">Border Color <?php $this->tooltip('Give a color to this border'); ?></label>
						<input type="text" name="_bordercolor" id="_bordercolor" class="um-admin-colorpicker" data-default-color="" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : ''; ?>" />
					</p>
					
				<?php
				break;
				
			case '_heading':
				?>

					<p><label for="_heading">Enable Row Heading <?php $this->tooltip('Whether to enable a heading for this row'); ?></label>
						<?php if ( isset( $this->edit_mode_value ) ) $this->ui_on_off('_heading', $this->edit_mode_value, true, 1, '_heading_text', 'xxx' ); else  $this->ui_on_off('_heading', 0, true, 1, '_heading_text', 'xxx'); ?>
					</p>
				
				<?php
				break;
				
			case '_heading_text':
				?>
				
					<p class="_heading_text"><label for="_heading_text">Heading Text <?php $this->tooltip('Enter the row heading text here'); ?></label>
						<input type="text" name="_heading_text" id="_heading_text" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : ''; ?>" />
					</p>
					
				<?php
				break;
				
			case '_background':
				?>
				
					<p><label for="_background">Background Color <?php $this->tooltip('This will be the background of entire section'); ?></label>
						<input type="text" name="_background" id="_background" class="um-admin-colorpicker" data-default-color="" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : ''; ?>" />
					</p>
					
				<?php
				break;
				
			case '_heading_background_color':
				?>
				
					<p class="_heading_text"><label for="_heading_background_color">Heading Background Color <?php $this->tooltip('This will be the background of the heading section'); ?></label>
						<input type="text" name="_heading_background_color" id="_heading_background_color" class="um-admin-colorpicker" data-default-color="" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : ''; ?>" />
					</p>
					
				<?php
				break;
				
			case '_heading_text_color':
				?>
				
					<p class="_heading_text"><label for="_heading_text_color">Heading Text Color <?php $this->tooltip('This will be the text color of heading part only'); ?></label>
						<input type="text" name="_heading_text_color" id="_heading_text_color" class="um-admin-colorpicker" data-default-color="" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : ''; ?>" />
					</p>
					
				<?php
				break;
				
			case '_text_color':
				?>
				
					<p><label for="_text_color">Text Color <?php $this->tooltip('This will be the text color of entire section'); ?></label>
						<input type="text" name="_text_color" id="_text_color" class="um-admin-colorpicker" data-default-color="" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : ''; ?>" />
					</p>
					
				<?php
				break;
				
			case '_icon_color':
				?>
				
					<p class="_heading_text"><label for="_icon_color">Icon Color <?php $this->tooltip('This will be the color of selected icon. By default It will be the same color as heading text color'); ?></label>
						<input type="text" name="_icon_color" id="_icon_color" class="um-admin-colorpicker" data-default-color="" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : ''; ?>" />
					</p>
					
				<?php
				break;
				
			case '_color':
				?>
				
					<p><label for="_color">Color <?php $this->tooltip('Select a color for this divider'); ?></label>
						<input type="text" name="_color" id="_color" class="um-admin-colorpicker" data-default-color="#eeeeee" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : '#eeeeee'; ?>" />
					</p>
					
				<?php
				break;
				
			case '_url_text':
				?>
				
					<p><label for="_url_text">URL Alt Text <?php $this->tooltip('Entering custom text here will replace the url with a text link'); ?></label>
						<input type="text" name="_url_text" id="_url_text" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : ''; ?>" />
					</p>
					
				<?php
				break;
				
			case '_url_target':
				?>
				
					<p><label for="_url_target">Link Target <?php $this->tooltip('Choose whether to open this link in same window or in a new window'); ?></label>
						<select name="_url_target" id="_url_target" class="umaf-selectjs" style="width: 100%">
							<option value="_blank" <?php selected( '_blank', $this->edit_mode_value ); ?>>Open in new window</option>
							<option value="_self"  <?php selected( '_self', $this->edit_mode_value ); ?>>Same window</option>
						</select>
					</p>
					
				<?php
				break;
				
			case '_url_rel':
				?>
				
					<p><label for="_url_rel">SEO Follow <?php $this->tooltip('Whether to follow or nofollow this link by search engines'); ?></label>
						<select name="_url_rel" id="_url_rel" class="umaf-selectjs" style="width: 100%">
							<option value="follow"  <?php selected( 'follow', $this->edit_mode_value ); ?>>Follow</option>
							<option value="nofollow" <?php selected( 'nofollow', $this->edit_mode_value ); ?>>No-Follow</option>
						</select>
					</p>
					
				<?php
				break;
				
			case '_force_good_pass':
				?>

					<p><label for="_force_good_pass">Force strong password? <?php $this->tooltip( __('Turn on to force users to create a strong password (A combination of one lowercase letter, one uppercase letter, and one number). If turned on this option is only applied to register forms and not to login forms.','ultimate-member') ); ?></label>
						<?php if ( isset( $this->edit_mode_value ) ) $this->ui_on_off('_force_good_pass', $this->edit_mode_value ); else  $this->ui_on_off('_force_good_pass', 0 ); ?>
					</p>
				
				<?php
				break;
				
			case '_force_confirm_pass':
				?>

					<p><label for="_force_confirm_pass">Automatically add a confirm password field? <?php $this->tooltip( __('Turn on to add a confirm password field. If turned on the confirm password field will only show on register forms and not on login forms.','ultimate-member') ); ?></label>
						<?php if ( isset( $this->edit_mode_value ) ) $this->ui_on_off('_force_confirm_pass', $this->edit_mode_value ); else  $this->ui_on_off('_force_confirm_pass', 1 ); ?>
					</p>
				
				<?php
				break;
				
			case '_style':
				?>
				
					<p><label for="_style">Style <?php $this->tooltip('This is the line-style of divider'); ?></label>
						<select name="_style" id="_style" class="umaf-selectjs" style="width: 100%">
							<option value="solid"  <?php selected( 'solid', $this->edit_mode_value ); ?>>Solid</option>
							<option value="dotted" <?php selected( 'dotted', $this->edit_mode_value ); ?>>Dotted</option>
							<option value="dashed" <?php selected( 'dashed', $this->edit_mode_value ); ?>>Dashed</option>
							<option value="double" <?php selected( 'double', $this->edit_mode_value ); ?>>Double</option>
						</select>
					</p>
					
				<?php
				break;
				
			case '_intervals':
			
				?>
				
					<p><label for="_intervals">Time Intervals (in minutes) <?php $this->tooltip('Choose the minutes interval between each time in the time picker.'); ?></label>
						<input type="text" name="_intervals" id="_intervals" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : 60; ?>" placeholder="e.g. 30, 60, 120" />
					</p>
					
				<?php
				break;
			
				
			case '_format':
			
				if ( $this->set_field_type == 'date' ) { 
				?>
				
					<p><label for="_format">Date User-Friendly Format <?php $this->tooltip('The display format of the date which is visible to user.'); ?></label>
						<select name="_format" id="_format" class="umaf-selectjs" style="width: 100%">
							<option value="j M Y" <?php selected( 'j M Y', $this->edit_mode_value ); ?>><?php echo $ultimatemember->datetime->get_time('j M Y'); ?></option>
							<option value="M j Y" <?php selected( 'M j Y', $this->edit_mode_value ); ?>><?php echo $ultimatemember->datetime->get_time('M j Y'); ?></option>
							<option value="j F Y" <?php selected( 'j F Y', $this->edit_mode_value ); ?>><?php echo $ultimatemember->datetime->get_time('j F Y'); ?></option>
							<option value="F j Y" <?php selected( 'F j Y', $this->edit_mode_value ); ?>><?php echo $ultimatemember->datetime->get_time('F j Y'); ?></option>
						</select>
					</p>
					
				<?php } else { ?>
				
					<p><label for="_format">Time Format <?php $this->tooltip('Choose the displayed time-format for this field'); ?></label>
						<select name="_format" id="_format" class="umaf-selectjs" style="width: 100%">
							<option value="g:i a" <?php selected( 'g:i a', $this->edit_mode_value ); ?>><?php echo $ultimatemember->datetime->get_time('g:i a'); ?> ( 12-hr format )</option>
							<option value="g:i A" <?php selected( 'g:i A', $this->edit_mode_value ); ?>><?php echo $ultimatemember->datetime->get_time('g:i A'); ?> ( 12-hr format )</option>
							<option value="H:i"  <?php selected( 'H:i', $this->edit_mode_value ); ?>><?php echo $ultimatemember->datetime->get_time('H:i'); ?> ( 24-hr format )</option>
						</select>
					</p>
					
				<?php
				}
				break;
				
			case '_pretty_format':
				?>
				
					<p><label for="_pretty_format">Displayed Date Format <?php $this->tooltip('Whether you wish to show the date in full or only show the years e.g. 25 Years'); ?></label>
						<select name="_pretty_format" id="_pretty_format" class="umaf-selectjs" style="width: 100%">
							<option value="0" <?php selected( 0, $this->edit_mode_value ); ?>>Show full date</option>
							<option value="1" <?php selected( 1, $this->edit_mode_value ); ?>>Show years only</option>
						</select>
					</p>
					
				<?php
				break;
				
			case '_disabled_weekdays':
			
				if ( isset( $this->edit_mode_value ) && is_array( $this->edit_mode_value ) ) {
					$values = $this->edit_mode_value;
				} else {
					$values = array('');
				}
				?>
				
					<p><label for="_disabled_weekdays">Disable specific weekdays <?php $this->tooltip('Disable specific week days from being available for selection in this date picker'); ?></label>
						<select name="_disabled_weekdays[]" id="_disabled_weekdays" class="umaf-selectjs" multiple="multiple" style="width: 100%">
							<option value="1" <?php if ( in_array( 1, $values ) ) { echo 'selected'; } ?>>Sunday</option>
							<option value="2" <?php if ( in_array( 2, $values ) ) { echo 'selected'; } ?>>Monday</option>
							<option value="3" <?php if ( in_array( 3, $values ) ) { echo 'selected'; } ?>>Tuesday</option>
							<option value="4" <?php if ( in_array( 4, $values ) ) { echo 'selected'; } ?>>Wednesday</option>
							<option value="5" <?php if ( in_array( 5, $values ) ) { echo 'selected'; } ?>>Thursday</option>
							<option value="6" <?php if ( in_array( 6, $values ) ) { echo 'selected'; } ?>>Friday</option>
							<option value="7" <?php if ( in_array( 7, $values ) ) { echo 'selected'; } ?>>Saturday</option>
						</select>
					</p>
					
				<?php
				break;
				
			case '_years':
				?>
			
					<p class="_years"><label for="_years">Number of Years to pick from <?php $this->tooltip('Number of years available for the date selection. Default to last 50 years'); ?></label>
						<input type="text" name="_years" id="_years" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : 50; ?>" />
					</p>
					
				<?php
				break;
				
			case '_years_x':
				?>
				
					<p class="_years"><label for="_years_x">Years Selection <?php $this->tooltip('This decides which years should be shown relative to today date'); ?></label>
						<select name="_years_x" id="_years_x" class="umaf-selectjs" style="width: 100%">
							<option value="equal"  <?php selected( 'equal', $this->edit_mode_value ); ?>>Equal years before / after today</option>
							<option value="past" <?php selected( 'past', $this->edit_mode_value ); ?>>Past years only</option>
							<option value="future" <?php selected( 'future', $this->edit_mode_value ); ?>>Future years only</option>
						</select>
					</p>
					
				<?php
				break;
				
			case '_range_start':
				?>
				
					<p class="_date_range"><label for="_range_start">Date Range Start <?php $this->tooltip('Set the minimum date/day in range in the format YYYY/MM/DD'); ?></label>
						<input type="text" name="_range_start" id="_range_start" value="<?php echo $this->edit_mode_value; ?>" placeholder="YYYY/MM/DD" />
					</p>
					
				<?php
				break;
				
			case '_range_end':
				?>
					
					<p class="_date_range"><label for="_range_end">Date Range End <?php $this->tooltip('Set the maximum date/day in range in the format YYYY/MM/DD'); ?></label>
						<input type="text" name="_range_end" id="_range_end" value="<?php echo $this->edit_mode_value; ?>" placeholder="YYYY/MM/DD" />
					</p>
					
				<?php
				break;
				
			case '_range':
				?>
				
					<p><label for="_range">Set Date Range <?php $this->tooltip('Whether to show a specific number of years or specify a date range to be available for the date picker.'); ?></label>
						<select name="_range" id="_range" class="umaf-selectjs um-adm-conditional" data-cond1='years' data-cond1-show='_years' data-cond2="date_range" data-cond2-show="_date_range" style="width: 100%">
							<option value="years" <?php selected( 'years', $this->edit_mode_value ); ?>>Fixed Number of Years</option>
							<option value="date_range" <?php selected( 'date_range', $this->edit_mode_value ); ?>>Specific Date Range</option>
						</select>
					</p>
					
				<?php
				break;
				
			case '_content':
			
				if ( $this->set_field_type == 'shortcode' ) {
				
				?>
				
					<p><label for="_content">Enter Shortcode <?php $this->tooltip('Enter the shortcode in the following textarea and it will be displayed on the fields'); ?></label>
						<textarea name="_content" id="_content" placeholder="e.g. [my_custom_shortcode]"><?php echo $this->edit_mode_value; ?></textarea>
					</p>
					
				<?php
				
					} else {

				?>
				
				<div class="um-admin-editor-h"><label>Content Editor <?php $this->tooltip('Edit the content of this field here'); ?></label></div>
				
				<div class="um-admin-editor"><!-- editor dynamically loaded here --></div>
				
				<?php
				
				}
				
				break;
				
			case '_crop':
				?>
				
					<p><label for="_crop">Crop Feature <?php $this->tooltip('Enable/disable crop feature for this image upload and define ratio'); ?></label>
						<select name="_crop" id="_crop" class="umaf-selectjs" style="width: 100%">
							<option value="0" <?php selected( '0', $this->edit_mode_value ); ?>>Turn Off (Default)</option>
							<option value="1" <?php selected( '1', $this->edit_mode_value ); ?>>Crop and force 1:1 ratio</option>
							<option value="3" <?php selected( '3', $this->edit_mode_value ); ?>>Crop and force user-defined ratio</option>
						</select>
					</p>
					
				<?php
				break;
				
			case '_allowed_types':
			
				if ( $this->set_field_type == 'image' ) {
				
					if ( isset( $this->edit_mode_value ) && is_array( $this->edit_mode_value ) ) {
						$values = $this->edit_mode_value;
					} else {
						$values = array('png','jpeg','jpg','gif');
					}
				?>
				
					<p><label for="_allowed_types">Allowed Image Types <?php $this->tooltip('Select the image types that you want to allow to be uploaded via this field.'); ?></label>
						<select name="_allowed_types[]" id="_allowed_types" class="umaf-selectjs" multiple="multiple" style="width: 100%">
							<?php foreach( $ultimatemember->files->allowed_image_types() as $e => $n ) { ?>
							<option value="<?php echo $e; ?>" <?php if ( in_array( $e, $values ) ) { echo 'selected'; } ?>><?php echo $n; ?></option>
							<?php } ?>
						</select>
					</p>
				
				<?php
				
				} else {
				
					if ( isset( $this->edit_mode_value ) && is_array( $this->edit_mode_value ) ) {
						$values = $this->edit_mode_value;
					} else {
						$values = array('pdf','txt');
					}
				
				?>
				
					<p><label for="_allowed_types">Allowed File Types <?php $this->tooltip('Select the image types that you want to allow to be uploaded via this field.'); ?></label>
						<select name="_allowed_types[]" id="_allowed_types" class="umaf-selectjs" multiple="multiple" style="width: 100%">
							<?php foreach( $ultimatemember->files->allowed_file_types() as $e => $n ) { ?>
							<option value="<?php echo $e; ?>" <?php if ( in_array( $e, $values ) ) { echo 'selected'; } ?>><?php echo $n; ?></option>
							<?php } ?>
						</select>
					</p>
					
				<?php
				
				}
				
				break;
			
			case '_upload_text':
			
				if ( $this->set_field_type == 'image' ) $value = 'Drag &amp; Drop Photo';
				if ( $this->set_field_type == 'file' ) $value = 'Drag &amp; Drop File';
				
				?>
				
					<p><label for="_upload_text">Upload Box Text <?php $this->tooltip('This is the headline that appears in the upload box for this field'); ?></label>
						<input type="text" name="_upload_text" id="_upload_text" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : $value; ?>" />
					</p>
				
				<?php
				break;
				
			case '_upload_help_text':
				?>
				
					<p><label for="_upload_help_text">Additional Instructions Text <?php $this->tooltip('If you need to add information or secondary line below the headline of upload box, enter it here'); ?></label>
						<input type="text" name="_upload_help_text" id="_upload_help_text" value="<?php echo $this->edit_mode_value; ?>" />
					</p>
				
				<?php
				break;
				
			case '_button_text':
				?>
				
					<p><label for="_button_text">Upload Box Text <?php $this->tooltip('The text that appears on the button. e.g. Upload'); ?></label>
						<input type="text" name="_button_text" id="_button_text" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : 'Upload'; ?>" />
					</p>
				
				<?php
				break;
				
			case '_max_size':
				?>
				
					<p><label for="_max_size">Maximum Size in bytes <?php $this->tooltip('The maximum size for image that can be uploaded through this field. Leave empty for unlimited size.'); ?></label>
						<input type="text" name="_max_size" id="_max_size" value="<?php echo $this->edit_mode_value; ?>" />
					</p>
				
				<?php
				break;
				
			case '_height':
				?>
				
					<p><label for="_height">Textarea Height <?php $this->tooltip('The height of textarea in pixels. Default is 100 pixels'); ?></label>
						<input type="text" name="_height" id="_height" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : '100px'; ?>" />
					</p>
				
				<?php
				break;
				
			case '_spacing':
				?>
				
					<p><label for="_spacing">Spacing <?php $this->tooltip('This is the required spacing in pixels. e.g. 20px'); ?></label>
						<input type="text" name="_spacing" id="_spacing" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : '20px'; ?>" />
					</p>
				
				<?php
				break;
				
			case '_is_multi':
				?>
				
					<p><label for="_is_multi">Allow multiple selections <?php $this->tooltip('Enable/disable multiple selections for this field'); ?></label>
						<?php 
						if ( isset( $this->edit_mode_value ) ) {
						$this->ui_on_off('_is_multi', $this->edit_mode_value, true, 1, '_max_selections', 'xxx');
						} else {
						$this->ui_on_off('_is_multi', 0, true, 1, '_max_selections', 'xxx');
						}
						?>
					</p>
					
				<?php
				break;
				
			case '_max_selections':
				?>
				
					<p class="_max_selections"><label for="_max_selections">Maximum number of selections <?php $this->tooltip('Enter a number here to force a maximum number of selections by user for this field'); ?></label>
						<input type="text" name="_max_selections" id="_max_selections" value="<?php echo $this->edit_mode_value; ?>" />
					</p>
				
				<?php
				break;
				
			case '_min_selections':
				?>
				
					<p class="_min_selections"><label for="_min_selections">Minimum number of selections <?php $this->tooltip('Enter a number here to force a minimum number of selections by user for this field'); ?></label>
						<input type="text" name="_min_selections" id="_min_selections" value="<?php echo $this->edit_mode_value; ?>" />
					</p>
				
				<?php
				break;
				
			case '_max_entries':
				?>
				
					<p class="_max_entries"><label for="_max_selections">Maximum number of entries <?php $this->tooltip('This is the max number of entries the user can add via field group.'); ?></label>
						<input type="text" name="_max_entries" id="_max_entries" value="<?php echo ( $this->edit_mode_value ) ? $this->edit_mode_value : 10; ?>" />
					</p>
				
				<?php
				break;
				
			case '_max_words':
				?>
				
					<p><label for="_max_words">Maximum allowed words <?php $this->tooltip('If you want to enable a maximum number of words to be input in this textarea. Leave empty to disable this setting'); ?></label>
						<input type="text" name="_max_words" id="_max_words" value="<?php echo $this->edit_mode_value; ?>" />
					</p>
				
				<?php
				break;
				
			case '_min':
				?>
				
					<p><label for="_min">Minimum Number <?php $this->tooltip( __('Minimum number that can be entered in this field','ultimate-member') ); ?></label>
						<input type="text" name="_min" id="_min" value="<?php echo $this->edit_mode_value; ?>" />
					</p>
				
				<?php
				break;
				
			case '_max':
				?>
				
					<p><label for="_max">Maximum Number <?php $this->tooltip( __('Maximum number that can be entered in this field','ultimate-member') ); ?></label>
						<input type="text" name="_max" id="_max" value="<?php echo $this->edit_mode_value; ?>" />
					</p>
				
				<?php
				break;
				
			case '_min_chars':
				?>
				
					<p><label for="_min_chars">Minimum length <?php $this->tooltip('If you want to enable a minimum number of characters to be input in this field. Leave empty to disable this setting'); ?></label>
						<input type="text" name="_min_chars" id="_min_chars" value="<?php echo $this->edit_mode_value; ?>" />
					</p>
				
				<?php
				break;
				
			case '_max_chars':
				?>
				
					<p><label for="_max_chars">Maximum length <?php $this->tooltip('If you want to enable a maximum number of characters to be input in this field. Leave empty to disable this setting'); ?></label>
						<input type="text" name="_max_chars" id="_max_chars" value="<?php echo $this->edit_mode_value; ?>" />
					</p>
				
				<?php
				break;
				
			case '_html':
				?>

				<p><label for="_html">Does this textarea accept HTML? <?php $this->tooltip('Turn on/off HTML tags for this textarea'); ?></label>
					<?php if ( isset( $this->edit_mode_value ) ) $this->ui_on_off('_html', $this->edit_mode_value ); else  $this->ui_on_off('_html', 0); ?>
				</p>
				
				<?php
				break;
				
			case '_options':

				if ( isset( $this->edit_mode_value ) && is_array( $this->edit_mode_value ) ) {
					$values = implode("\n", $this->edit_mode_value);
				} else if ( $this->edit_mode_value ) {
					$values = $this->edit_mode_value;
				} else {
					$values = '';
				}
				
				?>
				
					<p><label for="_options">Edit Choices <?php $this->tooltip('Enter one choice per line. This will represent the available choices or selections available for user.'); ?></label>
						<textarea name="_options" id="_options"><?php echo $values; ?></textarea>
					</p>
					
				<?php
				break;
		
			case '_title':
				?>
				
					<p><label for="_title">Title <?php $this->tooltip('This is the title of the field for your reference in the backend. The title will not appear on the front-end of your website.'); ?></label>
						<input type="text" name="_title" id="_title" value="<?php echo htmlspecialchars($this->edit_mode_value, ENT_QUOTES); ?>" />
					</p>
				
				<?php
				break;
		
			case '_id':

				?>
			
					<p style="display:none"><label for="_id">Unique ID</label>
						<input type="text" name="_id" id="_id" value="<?php echo $this->edit_mode_value; ?>" />
					</p>

				<?php
				
				break;
				
			case '_metakey':
			
				if ( $this->in_edit ) { 
				
				?>
			
					<p><label for="_metakey">Meta Key <?php $this->tooltip('The meta key cannot be changed for duplicated fields or when editing an existing field. If you require a different meta key please create a new field.'); ?></label>
						<input type="text" name="_metakey_locked" id="_metakey_locked" value="<?php echo $this->edit_mode_value; ?>" disabled />
					</p>
					
				<?php } else { ?>
				
					<p><label for="_metakey">Meta Key <?php $this->tooltip('A meta key is required to store the entered info in this field in the database. The meta key should be unique to this field and be written in lowercase with an underscore ( _ ) separating words e.g country_list or job_title'); ?></label>
						<input type="text" name="_metakey" id="_metakey" value="" />
					</p>
				
				<?php
				
				}
				
				break;
				
			case '_help':
				?>
				
					<p><label for="_help">Help Text <?php $this->tooltip('This is the text that appears in a tooltip when a user hovers over the info icon. Help text is useful for providing users with more information about what they should enter in the field. Leave blank if no help text is needed for field.'); ?></label>
						<input type="text" name="_help" id="_help" value="<?php echo $this->edit_mode_value; ?>" />
					</p>
				
				<?php
				break;
				
			case '_default':
				?>
				
					<?php if ( $this->set_field_type == 'textarea' ) { ?>
				
					<p><label for="_default">Default Text <?php $this->tooltip('Text to display by default in this field'); ?></label>
						<textarea name="_default" id="_default"><?php echo $this->edit_mode_value; ?></textarea>
					</p>
					
					<?php } elseif ( $this->set_field_type == 'rating' ) { ?>
					
					<p><label for="_default">Default Rating <?php $this->tooltip('If you wish the rating field to be prefilled with a number of stars, enter it here.'); ?></label>
						<input type="text" name="_default" id="_default" value="<?php echo $this->edit_mode_value; ?>" />
					</p>
					
					<?php } else { ?>
					
					<p><label for="_default">Default Value <?php $this->tooltip('This option allows you to pre-fill the field with a default value prior to the user entering a value in the field. Leave blank to have no default value'); ?></label>
						<input type="text" name="_default" id="_default" value="<?php echo $this->edit_mode_value; ?>" />
					</p>
					
					<?php } ?>
				
				<?php
				break;
				
			case '_label':
				?>
				
					<p><label for="_label">Label <?php $this->tooltip('The field label is the text that appears above the field on your front-end form. Leave blank to not show a label above field.'); ?></label>
						<input type="text" name="_label" id="_label" value="<?php echo htmlspecialchars($this->edit_mode_value, ENT_QUOTES); ?>" />
					</p>
					
				<?php
				break;
				
			case '_placeholder':
				?>
					
					<p><label for="_placeholder">Placeholder <?php $this->tooltip('This is the text that appears within the field e.g please enter your email address. Leave blank to not show any placeholder text.'); ?></label>
						<input type="text" name="_placeholder" id="_placeholder" value="<?php echo htmlspecialchars($this->edit_mode_value, ENT_QUOTES); ?>" />
					</p>
		
				<?php
				break;
					
			case '_public':
				?>
				
					<p><label for="_public">Privacy <?php $this->tooltip('Field privacy allows you to select who can view this field on the front-end. The site admin can view all fields regardless of the option set here.'); ?></label>
						<select name="_public" id="_public" class="umaf-selectjs um-adm-conditional" data-cond1='-2' data-cond1-show='_roles' data-cond2='-3' data-cond2-show='_roles'  style="width: 100%">
							<option value="1" <?php selected( 1, $this->edit_mode_value ); ?>>Everyone</option>
							<option value="2" <?php selected( 2, $this->edit_mode_value ); ?>>Members</option>
							<option value="-1" <?php selected( -1, $this->edit_mode_value ); ?>>Only visible to profile owner and admins</option>
							<option value="-3" <?php selected( -3, $this->edit_mode_value ); ?>>Only visible to profile owner and specific roles</option>
							<option value="-2" <?php selected( -2, $this->edit_mode_value ); ?>>Only specific member roles</option>
						</select>
					</p>
					
				<?php
				break;
					
			case '_roles':
				
				if ( isset( $this->edit_mode_value ) && is_array( $this->edit_mode_value ) ) {
					$values = $this->edit_mode_value;
				} else {
					$values = array('');
				}
				
				?>
			
					<p class="_roles"><label for="_roles">Select member roles <?php $this->tooltip('Select the member roles that can view this field on the front-end.'); ?></label>
						<select name="_roles[]" id="_roles" class="umaf-selectjs" style="width: 100%" multiple="multiple">
							
							<?php foreach($ultimatemember->query->get_roles() as $key => $value) { ?>
							
							<option value="<?php echo $key; ?>" <?php if ( in_array( $key, $values ) ) { echo 'selected'; } ?>><?php echo $value; ?></option>
							
							<?php } ?>
							
						</select>
					</p>
				
				<?php
				break;
				
			case '_required':
			
				if ( $this->set_field_type == 'password' )
					$def_required = 1;
				else 
					$def_required = 0;
					
				?>
				
				<div class="um-admin-tri">

					<p><label for="_required">Is this field required? <?php $this->tooltip('This option allows you to set whether the field must be filled in before the form can be processed.'); ?></label>
						<?php if ( isset( $this->edit_mode_value ) ) $this->ui_on_off('_required', $this->edit_mode_value ); else  $this->ui_on_off('_required', $def_required); ?>
					</p>
					
				</div>
				
				<?php
				break;
					
			case '_editable':
				?>
				
				<div class="um-admin-tri">

					<p><label for="_editable">Can user edit this field? <?php $this->tooltip('This option allows you to set whether or not the user can edit the information in this field.'); ?></label>
						<?php if ( isset( $this->edit_mode_value ) ) $this->ui_on_off('_editable', $this->edit_mode_value ); else  $this->ui_on_off('_editable', 1); ?>
					</p>
					
				</div>
				
				<?php
				break;
				
			case '_number':
				?>
				
					<p><label for="_number">Rating System <?php $this->tooltip('Choose whether you want a 5-stars or 10-stars ratings based here.'); ?></label>
						<select name="_number" id="_number" class="umaf-selectjs" style="width: 100%">
							<option value="5" <?php selected( 5, $this->edit_mode_value ); ?>>5  stars rating system</option>
							<option value="10" <?php selected( 10, $this->edit_mode_value ); ?>>10 stars rating system</option>
						</select>
					</p>
					
				<?php
				break;

			case '_custom_dropdown_options_source':
				?>
					
					<p><label for="_custom_dropdown_options_source">Choices Callback<?php $this->tooltip('Add a callback source to retrieve choices.'); ?></label>
						<input type="text" name="_custom_dropdown_options_source" id="_custom_dropdown_options_source" value="<?php echo htmlspecialchars($this->edit_mode_value, ENT_QUOTES); ?>" />
					</p>
		
				<?php
				break;


			case '_parent_dropdown_relationship':
				?>
					
					<p><label for="_parent_dropdown_relationship">Parent Option<?php $this->tooltip('Dynamically populates the option based from selected parent option.'); ?></label>
						<select name="_parent_dropdown_relationship" id="_parent_dropdown_relationship" class="umaf-selectjs" style="width: 100%">
							<option value="">No Selected</option>
							<?php 
							if ( $ultimatemember->builtin->custom_fields ) {
								foreach ($ultimatemember->builtin->custom_fields as $field_key => $array) {
									if( in_array( $array['type'], array( 'select' ) )
										&& $field_args['metakey'] != $array['metakey'] ){
	                                    echo "<option value='".$array['metakey']."' ".selected( $array['metakey'], $this->edit_mode_value  ).">".$array['title']."</option>";
	                                }
								}
							}
							
							?>
						</select>
					</p>
		
				<?php
				break;

				
		}
		
	}
	
}