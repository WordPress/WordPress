<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

class AgniImporterExporter {

	public function __construct() {

		$this->includes();

		add_action( 'admin_menu', array($this, 'agni_importer_menu'), 99 );

		add_action( 'rest_api_init', array($this, 'register_api'), 10 );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );


		add_filter( 'rest_attachment_collection_params', array($this, 'agni_export_collection_limits'), 10, 1 );

	}

	public function agni_export_collection_limits( $query_params ) {
		$query_params['per_page']['maximum'] = 300;

		return $query_params;
	}

	public function agni_importer_menu() {

		add_theme_page( esc_html__( 'Demo Importer', 'bagberry' ), esc_html__( 'Demo Importer', 'bagberry' ), 'edit_theme_options', 'agni_importer', array( $this, 'agni_importer_menu_page'), 99 );
	}

	public function includes() {
		require_once 'class-plugins-installer.php';
		require_once 'class-parser.php';
		require_once 'import-content-processor.php';
	}

	public function register_api() {

		$current_user_can = current_user_can( 'edit_theme_options' );

		register_rest_route( 'agni-import-export/v1', '/install_activate_plugins', array(
			'methods' => WP_REST_Server::CREATABLE,
			'callback' => array( 'Agni_Plugins_Installer', 'install_activate_plugins' ),
			'permission_callback' => function() use( $current_user_can) {
				return $current_user_can;
			},
		) );

		register_rest_route( 'agni-import-export/v1', '/get_import_data', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_import_data' ), 
			'permission_callback' => function() use( $current_user_can) {
				return $current_user_can;
			},
		) );

		register_rest_route( 'agni-import-export/v1', '/import_content', array(
			'methods' => WP_REST_Server::CREATABLE,
			'callback' => array( $this, 'import_content' ),
			'permission_callback' => function() use( $current_user_can) {
				return $current_user_can;
			},
		) );


		register_rest_route( 'agni-import-export/v1', '/get_mapped_content', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_mapped_content' ),
			'permission_callback' => function() use( $current_user_can) {
				return $current_user_can;
			},
		) );


		register_rest_route( 'agni-import-export/v1', '/get_total_media', array(
			'methods' => WP_REST_Server::EDITABLE,
			'callback' => array( $this, 'get_total_media' ),
			'permission_callback' => function() use( $current_user_can) {
				return $current_user_can;
			},
		) );

	}



	public function get_import_data() {

		$get_demo_content = $this->get_demo_content();

		wp_send_json_success( $get_demo_content );

		die();

	}

	public function import_content( WP_REST_Request $request ) {

		$result = '';

		$params = $request->get_params();

		// print_r( $params );

		$individualChoices = array();

		$contentName = $params['content'];
		$options = $params['options'];

		if ( isset( $params['individual'] ) ) {
			// $individual = $params['individual'];
			$contentName = $params['individual']['content'];
			$individualChoices = $params['individual']['values'];
		}

		// print_r( $individual );

		$get_demo_content = $this->get_demo_content();

		$get_content = array();
		$get_images = array();
		foreach ($get_demo_content as $key => $content) {
			if ($content['name'] == $contentName) {
				if ( !empty( $individualChoices ) ) {
					foreach ( $content['content'] as $key => $post) { 
						if ( in_array( $post['id'], $individualChoices ) ) {
							$get_content[] = $post;

							$get_images = array_merge( $get_images, $this->get_images($contentName, $post) );
						};
					}
					
				} else {
					$get_content = $content['content'];

					if ( isset( $params['individual'] ) ) {
						foreach ( $content['content'] as $key => $post) { 

							$get_images = array_merge( $get_images, $this->get_images($contentName, $post) );
							
						}
					}
				}
			}
		}

		$get_images = array_unique( $get_images );


		if ( !isset( $params['individual'] ) || ( !empty( $get_images ) && $options['media'] ) ) {
			$get_images_content = array();
			// foreach( $get_demo_content['media']['content'] as $key => $media){ 
			//     if( in_array( $media['id'], $get_images ) ){
			//         $get_images_content[] = $media;
			//     }
			// }

			foreach ($get_demo_content as $key => $content) {
				if ( 'media' == $content['name'] ) {
					foreach ( $content['content'] as $key => $media) { 
						if ( in_array( $media['id'], $get_images ) ) {
							$get_images_content[] = $media;
						}
					}
				}
			}

			
			/**
			 * Parsing the image received while import
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$result = apply_filters( 'agni_importer_exporter_parser', $get_images_content, 'media', $options );

		}


		if ( ( isset( $params['individual'] ) && 'pages' == $contentName ) ) {

			/**
			 * Parsing the image received while import
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$result = apply_filters( 'agni_importer_exporter_parser', $get_content, $contentName, $options );
		} else if ( $result['success']) {

			/**
			 * Parsing the image received while import
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$result = apply_filters( 'agni_importer_exporter_parser', $get_content, $contentName, $options );
		}

		return wp_send_json( $result );

	}

	public function get_images( $contentName, $post ) {
		$get_images = array();

		if ( 'products' == $contentName ) {
			if ( !empty( $post['images'] ) ) {
				foreach ($post['images'] as $key => $image) {
					$get_images[] = $image['id'];
				}
			}
			if ( !empty( $post['variations_products'] ) ) {
				foreach ($post['variations_products'] as $key => $variable_product) {
					$get_images[] = $variable_product['image']['id'];

					foreach ($variable_product['meta_data'] as $key => $meta) {
						foreach ($meta['value'] as $value) {
							$get_images[] = $value;
						}
					}
				}
			}
		} else {
			if ( 0 !== $post['featured_media'] ) {
				$get_images[] = $post['featured_media'];
			}
		}

		return $get_images;
	}


	public function get_mapped_content() {

		$mapped_content = get_option( 'agni_importer_exporter_demo_content_mapping' );

		return wp_send_json_success( $mapped_content );
	}

	public function get_total_media( WP_REST_Request $request ) {

		$params = $request->get_params();

		$individualChoices = array();
		$contentName = $params['content'];

		if ( !empty( $params['values'] ) ) {
			$individualChoices = explode( ',', $params['values'] );
		}

		$get_demo_content = $this->get_demo_content();

		$get_images = array();
		foreach ($get_demo_content as $key => $content) {
			if ($content['name'] == $contentName) {
				if ( !empty( $individualChoices ) ) {
					foreach ( $content['content'] as $key => $post) { 
						if ( in_array( $post['id'], $individualChoices ) ) {
							$get_images = array_merge( $get_images, $this->get_images($contentName, $post) );
						};
					}
					
				} else {
					foreach ( $content['content'] as $key => $post) { 
						$get_images = array_merge( $get_images, $this->get_images($contentName, $post) );
						
					}
				}
			}
		}

		$get_images = array_unique( $get_images );

		if ( !empty( $get_images ) ) {
			$get_images_content = array();

			foreach ($get_demo_content as $key => $content) {
				if ( 'media' == $content['name'] ) {
					foreach ( $content['content'] as $key => $media) { 
						if ( in_array( $media['id'], $get_images ) ) {
							$get_images_content[] = $media['id'];
						}
					}
				}
			}

			wp_send_json_success( $get_images_content );

		}

		wp_send_json_success( $get_images );
	}

	public function get_export_url( $path = '' ) {

		$agni_import_dir = get_template_directory() . '/inc/agni-importer-exporter/demo/export.json'; 

		return $agni_import_dir;
	}

	public function get_demo_content() {

		$demo_json = $this->get_export_url(); // we should replace with remote url request to get json data from server.
		$demo_content_array = '';
		
		$demo_content_array = json_decode( file_get_contents( $demo_json ), true );
	   
		return $demo_content_array;
	}


	public function agni_importer_menu_page() {


		wp_enqueue_style( 'agni-importer-exporter-style');
		wp_enqueue_script( 'agni-importer-exporter-script');
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'Demo Importer', 'bagberry' ); ?></h1>
			<div id="agni-importer-exporter" class="agni-importer-exporter"></div>
		</div>
		<?php

	}


	public function admin_enqueue_scripts() {


		$my_theme = wp_get_theme();

		$active_plugins = get_option('active_plugins');


		wp_enqueue_media();

		wp_register_style( 'agni-importer-exporter-style', get_template_directory_uri() . '/assets/css/admin/main.css', array(), $my_theme->Version );
		// wp_style_data
		
		wp_register_script( 'agni-importer-exporter-script', get_template_directory_uri() . '/assets/js/admin/import.js', array(), $my_theme->Version, true );
		wp_localize_script('agni-importer-exporter-script', 'agni_import_export', array(
			'nonce' => wp_create_nonce('wp_rest'),
			'siteurl' => esc_url_raw( home_url() ),
			'resturl' => esc_url_raw( rest_url() ),
			'theme_name' => $my_theme->Name,
			'theme_author' => $my_theme->display( 'Author', false ),
			'theme_version' => $my_theme->Version,
			'template_dir' => get_template_directory_uri(),
			'activeplugins' => $active_plugins,
			
		));
	}

	
}

$AgniImporterExporter = new AgniImporterExporter();
