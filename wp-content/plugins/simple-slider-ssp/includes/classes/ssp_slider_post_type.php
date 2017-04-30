<?php

class SSP_SLIDER_POST_TYPE {

	function __construct( $do_start = false ) {

		if ( $do_start )
			$this->start();

	}

	function start() {

		$this->hooks();
		$this->filters();

	}

	function hooks() {

		add_action( 'init', array( $this, 'register_post_type' ) );

		//print stylesheet only if post type is SLIDER_PLUGIN_SLIDER_POST_TYPE
		//add_action( 'admin_head', array( $this, 'admin_head_stylesheet' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );

		//print script only if post type is SLIDER_PLUGIN_SLIDER_POST_TYPE
		add_action( 'admin_head', array( $this, 'admin_head_script' ) );

		//print custom column data, for slides column the number of slides in a
		//slider
		add_action( sprintf( 'manage_%s_posts_custom_column',
				SLIDER_PLUGIN_SLIDER_POST_TYPE ),
			array( $this, 'custom_columns' ),
			10, 2  );


		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		//remove meta boxes from SLIDER_PLUGIN_SLIDER_POST_TYPE post type
		add_action( 'admin_menu', array( $this, 'remove_meta_boxes' ) );

		add_action( 'save_post', array( $this, 'save_post' ) );

		add_action( 'admin_footer', array( $this, 'intro_box' ) );

	}

	function filters() {

		add_filter( 'post_updated_messages',
			array( $this, 'custom_update_messages' ) );

		add_filter( 'post_row_actions',
			array( $this, 'remove_post_row_actions' ) );

		//remove the column date and add new custom column slides
		add_filter( sprintf( 'manage_edit-%s_columns',
				SLIDER_PLUGIN_SLIDER_POST_TYPE ),
			array( $this, 'modify_custom_columns' ) );


		add_filter( 'attachment_fields_to_edit',
			array( $this, 'remove_media_upload_fields' ), 10, 2 );

		add_filter( 'attachment_fields_to_save',
			array( $this,  '_save_attachment_url' ), 10, 2 );

		add_filter( 'attachment_fields_to_edit',
			array( $this, '_replace_attachment_url' ), 10, 2 );

		/**
		 * Show the 'Insert into Post' button for visual editor in
		 * slider post type for HTML slide type
		 * */
		add_filter( 'get_media_item_args',
			array( $this, 'add_insert_into_post_button' ) );

		add_filter( 'media_upload_tabs', 
			array( $this, 'remove_tab_from_media_upload_modal' ) );

	}

	function register_post_type() {

		$labels = array(
			'name' => _x( 'Sliders', 'post type general name' ),
			'singular_name' => _x( 'Slider', 'post type singular name' ),
			'add_new' => _x( 'Add New', 'Slider' ),
			'add_new_item' => __( 'Add New Slider' ),
			'edit_item' => __( 'Edit Slider' ),
			'new_item' => __( 'New Slider' ),
			'all_items' => __( 'All Slider' ),
			'view_item' => __( 'View Slider' ),
			'search_items' => __( 'Search Slider' ),
			'not_found' =>  __( 'No Sliders found' ),
			'not_found_in_trash' => __( 'No Sliders found in Trash' ),
			'parent_item_colon' => '',
			'menu_name' => __( 'WP Slider' )

		);

		$args = array(
			'labels' => $labels,
			'public' => false,
			'publicly_queryable' => false,
			'show_ui' => true,
			'show_in_menu' => false,
			'query_var' => false,
			'rewrite' => false,
			'capability_type' => 'post',
			'has_archive' => false,
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array( 'title' )
		);

		register_post_type( SLIDER_PLUGIN_SLIDER_POST_TYPE, $args );

	}

	function remove_meta_boxes() {

		remove_meta_box( 'submitdiv', SLIDER_PLUGIN_SLIDER_POST_TYPE, 'side' );

	}

	function add_meta_boxes() {

		add_meta_box(
			'ssp_slides_meta_box',
			__( 'Slides', SLIDER_PLUGIN_PREFIX ),
			array( $this, 'slides_meta_box' ),
			SLIDER_PLUGIN_SLIDER_POST_TYPE,
			'normal',
			'high'
		);

		add_meta_box(
			'ssp_custom_publish_meta_box',
			__( 'Save', SLIDER_PLUGIN_PREFIX ),
			array( $this, 'custom_publish_meta_box' ),
			SLIDER_PLUGIN_SLIDER_POST_TYPE,
			'side'
		);

		add_meta_box(
			'ssp_shortcode_meta_box',
			__( 'Shortcode', SLIDER_PLUGIN_PREFIX ),
			array( $this, 'shortcode_meta_box' ),
			SLIDER_PLUGIN_SLIDER_POST_TYPE,
			'side'
		);

		add_meta_box(
			'ssp_slider_options_metabox',
			__( 'Options', SLIDER_PLUGIN_PREFIX ),
			array( $this, 'options_meta_box' ),
			SLIDER_PLUGIN_SLIDER_POST_TYPE
		);

	}

	function slides_meta_box( $post ) {

		$slider_id = $post->ID;

		$slides = get_post_meta( $slider_id, 'slides', true );

		$default_slide = array(
			'label' => '',
			'type' => 'image',
			'attachment' => '',
			'image' => '',
			'html' => ''
		);

		if ( ! $slides )
			$slides = array();

		if ( ! $slides )
			$style_display = 'display: block';
		else
			$style_display = 'display: none';


		include muneeb_ssp_view_path( __FUNCTION__ );

	}

	function custom_publish_meta_box( $post ) {

		$slider_id = $post->ID;

		$post_status = get_post_status( $slider_id );
		$delete_link = get_delete_post_link( $slider_id );

		$nonce = wp_create_nonce( 'ssp_slider_nonce' );


		include muneeb_ssp_view_path( __FUNCTION__ );

	}

	function shortcode_meta_box( $post ) {

		$slider_id = $post->ID;

		if ( get_post_status( $slider_id ) !== 'publish' ) {

			echo __( '<p>Please click on the Create Slider button to get the slider shortcode</p>', SLIDER_PLUGIN_PREFIX );

			return;

		}

		$slider_title = get_the_title( $slider_id );

		$shortcode = sprintf( "[%s id='%s' name='%s']", SLIDER_PLUGIN_SLIDER_SHORTCODE, $slider_id, $slider_title );

		include muneeb_ssp_view_path( __FUNCTION__ );
	}

	function options_meta_box( $post ) {

		$slider_id = $post->ID;

		$active_skin = get_post_meta( $slider_id, 'skin', true );

		$skins = array();

		//default image slider skin
		$skins[] = array(
			'name' => __( 'Default Image Skin', SLIDER_PLUGIN_PREFIX ),
			'path' => 'default',
			'description' => ''
		);

		$slider_options = get_post_meta( $slider_id, 'options', true );

		if ( ! $slider_options )
			$slider_options = self::default_options();

		$skins = $this->get_skins( $skins );

		$skins = apply_filters( 'ssp_skins_array', $skins );

		include muneeb_ssp_view_path( __FUNCTION__ );

	}

	function save_post( $post_id ) {

		if ( ! current_user_can( 'edit_post', $post_id ) )
			return;

		if ( wp_is_post_revision( $post_id ) )
			return;
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;
		
		if ( ! isset( $_POST['post_type_is_ssp_slider'] ) )
			return;
		
		if ( ! wp_verify_nonce( $_POST['ssp_slider_nonce'], 'ssp_slider_nonce' ) )
			return;

		$slider_id = $post_id;

		if ( ! $this->validate_page() )
			return FALSE;

		$slider_skin = $_POST['skin'];

		$slides = array();

		$image = array(
			'id' => NULL,
			'url' => NULL,
			'alt' => NULL,
			'link' => NULL,
			'caption' => NULL,
			'sizes' => array(
				'thumbnail' => NULL,
				'medium' => NULL,
				'large' => NULL,
				'full' => NULL
			)
		);

		//$slider_options_default = self::default_options();

		//$slider_options = wp_parse_args( $_POST['slider_options'],
		//    $slider_options_default );

		$slider_options = $_POST['slider_options'];

		foreach ( $slider_options as $key => $option ):

			if ( $option === "true" )
				$slider_options[$key] = true;

			if ( $option === "false" )
				$slider_options[$key] = false;

		endforeach;

		if ( ! isset( $slider_options['direction_nav'] ) )
			$slider_options['direction_nav'] = false;

		if ( ! isset( $slider_options['control_nav'] ) )
			$slider_options['control_nav'] = false;

		if ( ! isset( $slider_options['keyboard_nav'] ) )
			$slider_options['keyboard_nav'] = false;

		if ( ! isset( $slider_options['touch_nav'] ) )
			$slider_options['touch_nav'] = false;

		if ( ! isset( $slider_options['caption_box'] ) )
			$slider_options['caption_box'] = false;

		if ( ! isset( $slider_options['linkable'] ) )
			$slider_options['linkable'] = false;

		if ( ! isset( $slider_options['linkable'] ) )
			$slider_options['linkable'] = false;

		if ( ! isset( $slider_options['pause_on_hover'] ) )
			$slider_options['pause_on_hover'] = false;

		if ( ! isset( $slider_options['thumbnail_navigation'] ) )
			$slider_options['thumbnail_navigation'] = false;

		$slider_options = apply_filters( 'ssp_before_save_slider_options' , 
						$slider_options, $slider_id );

		if ( ! isset( $_POST['slides'] )  )
			$_POST['slides'] = $slides;


		foreach ( $_POST['slides'] as $key => $slide ) {

			if ( empty( $slide['type'] ) )
				$slide['type'] = 'image';

			if ( ! empty( $slide['attachment'] ) ) {

				$image['id'] = $slide['attachment'];

				$image['url'] = wp_get_attachment_url( $image['id'] );

				$image['alt'] = get_post_meta( $image['id'],
					'_wp_attachment_image_alt', true );

				$image['link'] = get_post_meta( $image['id'],
					'_wp_attachment_url', true );

				$image['title'] = get_the_title( $image['id'] );

				$image['caption'] = get_post_field( 'post_excerpt', $image['id'] );

				$sizes = get_intermediate_image_sizes();
				$sizes[] = 'full';
				
				foreach ( $sizes as $size ) {
					$img = wp_get_attachment_image_src(
						$image['id'] , $size );
					$image['sizes'][$size] = $img[0];
				}

			}

			$slide['image'] = $image;

			$slides[] = $slide;

		}



		update_post_meta( $slider_id, 'slides', $slides );
		update_post_meta( $slider_id, 'skin', $slider_skin );
		update_post_meta( $slider_id, 'options', $slider_options );

		do_action( 'ssp_save_slider_data', $slider_id );

	}

	function custom_update_messages( $messages ) {

		global $post;
		$messages[SLIDER_PLUGIN_SLIDER_POST_TYPE] = array(
			0 => '',
			1 =>  __( 'Slider updated.' ),
			2 => __( 'Custom field updated.' ),
			3 => __( 'Custom field deleted.' ),
			4 => __( 'Slider updated.' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Slider restored to revision from %s' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => __( 'Slider created.' ),
			7 => __( 'Slider saved.' ),
			8 => '',
			9 => sprintf( __( 'Slider scheduled for: <strong>%1$s</strong>.' ),
				date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) ),
			10 => __( 'Slider draft updated.' )
		);

		return $messages;

	}

	function remove_post_row_actions( $actions ) {

		if ( ! $this->validate_page() )
			return $actions;

		unset( $actions['view'] );
		unset( $actions['inline hide-if-no-js'] );
		unset( $actions['pgcache_purge'] );

		return $actions;

	}

	function modify_custom_columns( $columns ) {

		unset( $columns['date'] );

		return array_merge( $columns,
			array(
				'slides' => __( 'Slides', SLIDER_PLUGIN_PREFIX )
			)
		);
		
	}

	function custom_columns( $column, $slider_id ) {

		switch ( $column ) {

		case 'slides':
			if ( ! muneeb_ssp_get_slides( $slider_id ) )
				echo "0";
			else
				echo count( muneeb_ssp_get_slides( $slider_id ) );

			break;

		}

	}

	function admin_head_stylesheet() {

		if ( ! $this->validate_page() )
			return FALSE;

		echo '<style>';

		include SLIDER_PLUGIN_CSS_DIRECTORY . 'ssp_slider.css';

		echo '</style>';

	}

	function admin_head_script() {

		if ( ! $this->validate_page() )
			return FALSE;

		echo '<script>';

		include SLIDER_PLUGIN_JS_DIRECTORY . 'ssp_slider.js';

		echo '</script>';

	}

	function admin_enqueue() {

		if ( ! $this->validate_page() )
			return FALSE;

		wp_enqueue_style( 'ssp_slider.css',
			plugins_url( 'css/ssp_slider.css', SLIDER_PLUGIN_MAIN_FILE ) );

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_script( 'media-upload' );

		wp_dequeue_script( 'autosave' );

	}

	function remove_media_upload_fields( $fields, $post ) {

		unset( $fields['post_content'] );
		return $fields;

	}

	function _save_attachment_url( $post, $attachment ) {

		if ( isset( $attachment['url'] ) )
			update_post_meta( $post['ID'], '_wp_attachment_url', esc_url_raw( $attachment['url'] ) );

		return $post;

	}

	function _replace_attachment_url( $form_fields, $post ) {

		if ( isset( $form_fields['url']['html'] ) ) {

			$url = get_post_meta( $post->ID, '_wp_attachment_url', true );
			if ( ! empty( $url ) )
				$form_fields['url']['html'] = preg_replace( "/value='.*?'/", "value='$url'", $form_fields['url']['html'] );

		}

		return $form_fields;

	}

	function validate_page() {

		if ( isset( $_GET['post_type'] ) )
			if ( $_GET['post_type'] == SLIDER_PLUGIN_SLIDER_POST_TYPE )
				return TRUE;

		if ( get_post_type() === SLIDER_PLUGIN_SLIDER_POST_TYPE )
				return TRUE;

		return FALSE;

	}

	public static function default_options() {

		$default_options = array(
			'slideshow' => true,
			'direction' => 'horizontal',
			'height' => '',
			'h_responsive' => true,
			'control_nav' => true,
			'direction_nav' => true,
			'keyboard_nav' => true,
			'touch_nav' => true,
			'cycle_speed' => 5,
			'animation_speed' => 0.6,
			'animation' => 'slide',
			'pause_on_hover' => true,
			'w_responsive' => true,
			'width' => '',
			'caption_box' => false,
			'linkable' => false,
			'thumbnail_navigation' => false
		);

		return apply_filters( 'ssp_default_slider_options', $default_options );

	}

	function add_insert_into_post_button( $args ) {

		if ( get_post_type() == SLIDER_PLUGIN_SLIDER_POST_TYPE )
			$args['send'] = true;

		return $args;

	}

	function get_skins( $skins = NULL ) {

		//return the skins from the wp-content/ssp_skins directory

		if ( ! $skins )
			$skins = array();

		$skins_dir_path = WP_CONTENT_DIR .
			DIRECTORY_SEPARATOR . 'ssp_skins' ;

		try {
			$skins_iterator = new RecursiveDirectoryIterator( $skins_dir_path );

			foreach ( new RecursiveIteratorIterator( $skins_iterator ) as $skin ) {
				
				if ( basename( $skin ) == 'slider.php' ) {

					$skin_data = get_plugin_data( $skin );

					$temp_skin = array();

					if ( $skin_data['Name'] == ''  )
						$temp_skin['name'] = basename( dirname( $skin ) );
					else
						$temp_skin['name'] = $skin_data['Name'];

					$temp_skin['path'] = dirname( $skin );
					$temp_skin['description'] = $skin_data['Description'];

					$skins[] = $temp_skin;

				}

			}
		} catch ( UnexpectedValueException $e ) { }

		return $skins;

	}

	function remove_tab_from_media_upload_modal( $tabs ) {

		if ( ! isset( $_GET['slider_id'] ) ) return $tabs;

		$post_type = get_post_type( $_GET['slider_id'] );

		if ( $post_type !== SLIDER_PLUGIN_SLIDER_POST_TYPE )
			return $tabs;

		unset( $tabs['type_url'] );
		unset( $tabs['gallery'] );

		return $tabs;

	}

	function intro_box() {

		if ( get_post_type() !== SLIDER_PLUGIN_SLIDER_POST_TYPE )
			return FALSE;

		include muneeb_ssp_view_path( __FUNCTION__ );

	}

}