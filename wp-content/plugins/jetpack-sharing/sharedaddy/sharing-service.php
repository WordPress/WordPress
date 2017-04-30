<?php

include_once dirname( __FILE__ ).'/sharing-sources.php';

// define( 'WP_SHARING_PLUGIN_VERSION', JETPACK__VERSION ); // E-1
define( 'WP_SHARING_PLUGIN_VERSION', JETPACK_SHARING_VERSION ); // E-1

class Sharing_Service {
	private $global = false;
	var $default_sharing_label = '';

	public function __construct() {
		$this->default_sharing_label = __( 'Share this:', 'jetpack' );
	}

	/**
	 * Gets a generic list of all services, without any config
	 */
	public function get_all_services_blog() {
		$options  = get_option( 'sharing-options' );

		$all = $this->get_all_services();
		$services = array();

		foreach ( $all AS $id => $name ) {
			if ( isset( $all[$id] ) ) {
				$config = array();

				// Pre-load custom modules otherwise they won't know who they are
				if ( substr( $id, 0, 7 ) == 'custom-' && is_array( $options[$id] ) )
					$config = $options[$id];

				$services[$id] = new $all[$id]( $id, $config );
			}
		}

		return $services;
	}

	/**
	 * Gets a list of all available service names and classes
	 */
	private function get_all_services() {
		// Default services
		$services = array(
			'email'         => 'Share_Email',
			'print'         => 'Share_Print',
			'digg'          => 'Share_Digg',
			'facebook'      => 'Share_Facebook',
			'linkedin'      => 'Share_LinkedIn',
			'reddit'        => 'Share_Reddit',
			'stumbleupon'   => 'Share_Stumbleupon',
			'twitter'       => 'Share_Twitter',
			'press-this'    => 'Share_PressThis',
			'google-plus-1' => 'Share_GooglePlus1',
			'tumblr'        => 'Share_Tumblr',
			'pinterest'     => 'Share_Pinterest',
			'pocket'        => 'Share_Pocket',
		);

		// Add any custom services in
		$options = $this->get_global_options();
		foreach ( (array)$options['custom'] AS $custom_id ) {
			$services[$custom_id] = 'Share_Custom';
		}

		return apply_filters( 'sharing_services', $services );
	}

	public function new_service( $label, $url, $icon ) {
		// Validate
		$label = trim( wp_html_excerpt( wp_kses( $label, array() ), 30 ) );
		$url   = trim( esc_url_raw( $url ) );
		$icon  = trim( esc_url_raw( $icon ) );

		if ( $label && $url && $icon ) {
			$options = get_option( 'sharing-options' );
			if ( !is_array( $options ) )
				$options = array();

			$service_id = 'custom-'.time();

			// Add a new custom service
			$options['global']['custom'][] = $service_id;

			update_option( 'sharing-options', $options );

			// Create a custom service and set the options for it
			$service = new Share_Custom( $service_id, array( 'name' => $label, 'url' => $url, 'icon' => $icon ) );
			$this->set_service( $service_id, $service );

			// Return the service
			return $service;
		}

		return false;
	}

	public function delete_service( $service_id ) {
		$options = get_option( 'sharing-options' );
		if ( isset( $options[$service_id] ) )
			unset( $options[$service_id] );

		$key = array_search( $service_id, $options['global']['custom'] );
		if ( $key !== false )
			unset( $options['global']['custom'][$key] );

		update_option( 'sharing-options', $options );
		return true;
	}

	public function set_blog_services( array $visible, array $hidden ) {
		$services =  $this->get_all_services();
		// Validate the services
		$available = array_keys( $services );

		// Only allow services that we have defined
		$hidden  = array_intersect( $hidden, $available );
		$visible = array_intersect( $visible, $available );

		// Ensure we don't have the same ones in hidden and visible
		$hidden = array_diff( $hidden, $visible );

		do_action( 'sharing_get_services_state', array(
			'services'			=> $services,
			'available' 		=> $available,
			'hidden' 			=> $hidden,
			'visible' 			=> $visible,
			'currently_enabled' => $this->get_blog_services()
		) );

		update_option( 'sharing-services', array( 'visible' => $visible, 'hidden' => $hidden ) );
	}

	public function get_blog_services() {
		$options  = get_option( 'sharing-options' );
		$enabled  = get_option( 'sharing-services' );
		$services = $this->get_all_services();

		if ( !is_array( $options ) )
			$options = array( 'global' => $this->get_global_options() );

		$global = $options['global'];

		// Default services
		if ( !is_array( $enabled ) ) {
			$enabled = array(
				'visible' => array(),
				'hidden' => array()
			);

			$enabled = apply_filters( 'sharing_default_services', $enabled );
		}

		// Cleanup after any filters that may have produced duplicate services
		$enabled['visible'] = array_unique( $enabled['visible'] );
		$enabled['hidden']  = array_unique( $enabled['hidden'] );

		// Form the enabled services
		$blog = array( 'visible' => array(), 'hidden' => array() );

		foreach ( $blog AS $area => $stuff ) {
			foreach ( (array)$enabled[$area] AS $service ) {
				if ( isset( $services[$service] ) ) {
					$blog[$area][$service] = new $services[$service]( $service, array_merge( $global, isset( $options[$service] ) ? $options[$service] : array() ) );
				}
			}
		}

		$blog = apply_filters( 'sharing_services_enabled', $blog );

		// Add CSS for NASCAR
		if ( count( $blog['visible'] ) || count( $blog['hidden'] ) )
			add_filter( 'post_flair_block_css', 'post_flair_service_enabled_sharing' );

		// Convenience for checking if a service is present
		$blog['all'] = array_flip( array_merge( array_keys( $blog['visible'] ), array_keys( $blog['hidden'] ) ) );
		return $blog;
	}

	public function get_service( $service_name ) {
		$services = $this->get_blog_services();

		if ( isset( $services['visible'][$service_name] ) )
			return $services['visible'][$service_name];

		if ( isset( $services['hidden'][$service_name] ) )
			return $services['hidden'][$service_name];

		return false;
	}

	public function set_global_options( $data ) {
		$options = get_option( 'sharing-options' );

		// No options yet
		if ( !is_array( $options ) )
			$options = array();

		// Defaults
		$options['global'] = array(
			'button_style'  => 'icon-text',
			'sharing_label' => $this->default_sharing_label,
			'open_links'    => 'same',
			'show'          => array( 'post', 'page' ),
			'custom'        => isset( $options['global']['custom'] ) ? $options['global']['custom'] : array()
		);

		$options['global'] = apply_filters( 'sharing_default_global', $options['global'] );

		// Validate options and set from our data
		if ( isset( $data['button_style'] ) && in_array( $data['button_style'], array( 'icon-text', 'icon', 'text', 'official' ) ) )
			$options['global']['button_style'] = $data['button_style'];

		if ( isset( $data['sharing_label'] ) ) {
			if ( $this->default_sharing_label === $data['sharing_label'] ) {
				$options['global']['sharing_label'] = false;
			} else {
				$options['global']['sharing_label'] = trim( wp_kses( stripslashes( $data['sharing_label'] ), array() ) );
			}
		}

		if ( isset( $data['open_links'] ) && in_array( $data['open_links'], array( 'new', 'same' ) ) )
			$options['global']['open_links'] = $data['open_links'];

		$shows = array_values( get_post_types( array( 'public' => true ) ) );
		$shows[] = 'index';
		if ( isset( $data['show'] ) ) {
			if ( is_scalar( $data['show'] ) ) {
				switch ( $data['show'] ) {
				case 'posts' :
					$data['show'] = array( 'post', 'page' );
					break;
				case 'index' :
					$data['show'] = array( 'index' );
					break;
				case 'posts-index' :
					$data['show'] = array( 'post', 'page', 'index' );
					break;
				}
			}

			if ( $data['show'] = array_intersect( $data['show'], $shows ) ) {
				$options['global']['show'] = $data['show'];
			}
		} else {
			$options['global']['show'] = array();
		}

		update_option( 'sharing-options', $options );
		return $options['global'];
	}

	public function get_global_options() {
		if ( $this->global === false ) {
			$options = get_option( 'sharing-options' );

			if ( is_array( $options ) && isset( $options['global'] ) )
				$this->global = $options['global'];
			else
				$this->global = $this->set_global_options( $options['global'] );
		}

		if ( ! isset( $this->global['show'] ) ) {
			$this->global['show'] = array( 'post', 'page' );
		} elseif ( is_scalar( $this->global['show'] ) ) {
			switch ( $this->global['show'] ) {
			case 'posts' :
				$this->global['show'] = array( 'post', 'page' );
				break;
			case 'index' :
				$this->global['show'] = array( 'index' );
				break;
			case 'posts-index' :
				$this->global['show'] = array( 'post', 'page', 'index' );
				break;
			}
		}

		if ( false === $this->global['sharing_label'] ) {
			$this->global['sharing_label'] = $this->default_sharing_label;
		}

		return $this->global;
	}

	public function set_service( $id, Sharing_Source $service ) {
		// Update the options for this service
		$options = get_option( 'sharing-options' );

		// No options yet
		if ( !is_array( $options ) )
			$options = array();

		do_action( 'sharing_get_button_state', array( 'id' => $id, 'options' => $options, 'service' => $service ) );

		$options[$id] = $service->get_options();

		update_option( 'sharing-options', array_filter( $options ) );
	}

	// Soon to come to a .org plugin near you!
	public function get_total( $service_name = false, $post_id = false, $_blog_id = false ) {
		global $wpdb, $blog_id;
		if ( !$_blog_id ) {
			$_blog_id = $blog_id;
		}
		if ( $service_name == false ) {
			if ( $post_id > 0 ) {
				// total number of shares for this post
				return (int) $wpdb->get_var( $wpdb->prepare( "SELECT SUM( count ) FROM sharing_stats WHERE blog_id = %d AND post_id = %d", $_blog_id, $post_id ) );
			} else {
				// total number of shares for this blog
				return (int) $wpdb->get_var( $wpdb->prepare( "SELECT SUM( count ) FROM sharing_stats WHERE blog_id = %d", $_blog_id ) );
			}
		}

		if ( $post_id > 0 )
			return (int) $wpdb->get_var( $wpdb->prepare( "SELECT SUM( count ) FROM sharing_stats WHERE blog_id = %d AND post_id = %d AND share_service = %s", $_blog_id, $post_id, $service_name ) );
		else
			return (int) $wpdb->get_var( $wpdb->prepare( "SELECT SUM( count ) FROM sharing_stats WHERE blog_id = %d AND share_service = %s", $_blog_id, $service_name ) );
	}

	public function get_services_total( $post_id = false ) {
		$totals = array();
		$services = $this->get_blog_services();

		if ( !empty( $services ) && isset( $services[ 'all' ] ) )
			foreach( $services[ 'all' ] as $key => $value ) {
				$totals[$key] = new Sharing_Service_Total( $key, $this->get_total( $key, $post_id ) );
			}
		usort( $totals, array( 'Sharing_Service_Total', 'cmp' ) );

		return $totals;
	}

	public function get_posts_total() {
		$totals = array();
		global $wpdb, $blog_id;

		$my_data = $wpdb->get_results( $wpdb->prepare( "SELECT post_id as id, SUM( count ) as total FROM sharing_stats WHERE blog_id = %d GROUP BY post_id ORDER BY count DESC ", $blog_id ) );

		if ( !empty( $my_data ) )
			foreach( $my_data as $row )
				$totals[] = new Sharing_Post_Total( $row->id, $row->total );

		usort( $totals, array( 'Sharing_Post_Total', 'cmp' ) );

		return $totals;
	}
}

class Sharing_Service_Total {
	var $id 		= '';
	var $name 		= '';
	var $service	= '';
	var $total 		= 0;

	public function Sharing_Service_Total( $id, $total ) {
		$services 		= new Sharing_Service();
		$this->id 		= esc_html( $id );
		$this->service 	= $services->get_service( $id );
		$this->total 	= (int) $total;

		$this->name 	= $this->service->get_name();
	}

	static function cmp( $a, $b ) {
		if ( $a->total == $b->total )
			return $a->name < $b->name;
		return $a->total < $b->total;
	}
}

class Sharing_Post_Total {
	var $id		= 0;
	var $total	= 0;
	var $title 	= '';
	var $url	= '';

	public function Sharing_Post_Total( $id, $total ) {
		$this->id 		= (int) $id;
		$this->total 	= (int) $total;
		$this->title	= get_the_title( $this->id );
		$this->url		= get_permalink( $this->id );
	}

	static function cmp( $a, $b ) {
		if ( $a->total == $b->total )
			return $a->id < $b->id;
		return $a->total < $b->total;
	}
}

function sharing_register_post_for_share_counts( $post_id ) {
	global $jetpack_sharing_counts;

	if ( ! isset( $jetpack_sharing_counts ) || ! is_array( $jetpack_sharing_counts ) )
		$jetpack_sharing_counts = array();

	$jetpack_sharing_counts[ (int) $post_id ] = get_permalink( $post_id );
}

function sharing_add_footer() {
	global $jetpack_sharing_counts;

	if ( apply_filters( 'sharing_js', true ) ) {

		if ( is_array( $jetpack_sharing_counts ) && count( $jetpack_sharing_counts ) ) :
			$sharing_post_urls = array_filter( $jetpack_sharing_counts );
			if ( $sharing_post_urls ) :
?>

	<script type="text/javascript">
		WPCOM_sharing_counts = <?php echo json_encode( array_flip( $sharing_post_urls ) ); ?>
	</script>
<?php
			endif;
		endif;

		wp_enqueue_script( 'sharing-js' );
		$recaptcha__options = array( 'lang' => get_base_recaptcha_lang_code() );
		wp_localize_script('sharing-js', 'recaptcha_options', $recaptcha__options);
	}

	$sharer = new Sharing_Service();
	$enabled = $sharer->get_blog_services();
	foreach ( array_merge( $enabled['visible'], $enabled['hidden'] ) AS $service ) {
		$service->display_footer();
	}
}

function sharing_add_header() {
	$sharer = new Sharing_Service();
	$enabled = $sharer->get_blog_services();

	foreach ( array_merge( $enabled['visible'], $enabled['hidden'] ) AS $service ) {
		$service->display_header();
	}

	if ( count( $enabled['all'] ) > 0 ) {
		wp_enqueue_style( 'sharedaddy', plugin_dir_url( __FILE__ ) .'sharing.css', array(), WP_SHARING_PLUGIN_VERSION ); // E-1
		wp_enqueue_style( 'genericons' );
	}
			
}
add_action( 'wp_head', 'sharing_add_header', 1 );

function sharing_process_requests() {
	global $post;

	// Only process if: single post and share=X defined
	if ( ( is_page() || is_single() ) && isset( $_GET['share'] ) ) {
		$sharer = new Sharing_Service();

		$service = $sharer->get_service( $_GET['share'] );
		if ( $service ) {
			$service->process_request( $post, $_POST );
		}
	}
}
add_action( 'template_redirect', 'sharing_process_requests', 9 );

function sharing_display( $text = '', $echo = false ) {
	global $post, $wp_current_filter;

	if ( empty( $post ) )
		return $text;

	if ( is_preview() ) {
		return $text;
	}

	// Don't output flair on excerpts
	if ( in_array( 'get_the_excerpt', (array) $wp_current_filter ) ) {
		return $text;
	}

	// Don't allow flair to be added to the_content more than once (prevent infinite loops)
	$done = false;
	foreach ( $wp_current_filter as $filter ) {
		if ( 'the_content' == $filter ) {
			if ( $done )
				return $text;
			else
				$done = true;
		}
	}

	// check whether we are viewing the front page and whether the front page option is checked
	$options = get_option( 'sharing-options' );
	$display_options = $options['global']['show'];

	if ( is_front_page() && ( is_array( $display_options ) && ! in_array( 'index', $display_options ) ) )
		return $text;

	if ( is_attachment() && in_array( 'the_excerpt', (array) $wp_current_filter ) ) {
		// Many themes run the_excerpt() conditionally on an attachment page, then run the_content().
		// We only want to output the sharing buttons once.  Let's stick with the_content().
		return $text;
	}

	$sharer = new Sharing_Service();
	$global = $sharer->get_global_options();

	$show = false;
	if ( !is_feed() ) {
		if ( is_singular() && in_array( get_post_type(), $global['show'] ) ) {
			$show = true;
		} elseif ( in_array( 'index', $global['show'] ) && ( is_home() || is_archive() || is_search() || in_array( get_post_type(), $global['show'] ) ) ) {
			$show = true;
		}
	}

	// Pass through a filter for final say so
	$show = apply_filters( 'sharing_show', $show, $post );

	// Disabled for this post?
	$switched_status = get_post_meta( $post->ID, 'sharing_disabled', false );

	if ( !empty( $switched_status ) )
		$show = false;

	// Allow to be used on P2 ajax requests for latest posts.
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['action'] ) && 'get_latest_posts' == $_REQUEST['action'] )
		$show = true;

	$sharing_content = '';

	if ( $show ) {
		$enabled = apply_filters( 'sharing_enabled', $sharer->get_blog_services() );

		if ( count( $enabled['all'] ) > 0 ) {
			global $post;

			$dir = get_option( 'text_direction' );

			// Wrapper
			$sharing_content .= '<div class="sharedaddy sd-sharing-enabled"><div class="robots-nocontent sd-block sd-social sd-social-' . $global['button_style'] . ' sd-sharing">';
			if ( $global['sharing_label'] != '' )
				$sharing_content .= '<h3 class="sd-title">' . $global['sharing_label'] . '</h3>';
			$sharing_content .= '<div class="sd-content"><ul>';

			// Visible items
			$visible = '';
			foreach ( $enabled['visible'] as $id => $service ) {
				// Individual HTML for sharing service
				$visible .= '<li class="share-' . $service->get_class() . '">' . $service->get_display( $post ) . '</li>';
			}

			$parts = array();
			$parts[] = $visible;
			if ( count( $enabled['hidden'] ) > 0 ) {
				if ( count( $enabled['visible'] ) > 0 )
					$expand = __( 'More', 'jetpack' );
				else
					$expand = __( 'Share', 'jetpack' );
				$parts[] = '<li><a href="#" class="sharing-anchor sd-button share-more"><span>'.$expand.'</span></a></li>';
			}

			if ( $dir == 'rtl' )
				$parts = array_reverse( $parts );

			$sharing_content .= implode( '', $parts );
			$sharing_content .= '<li class="share-end"></li></ul>';

			if ( count( $enabled['hidden'] ) > 0 ) {
				$sharing_content .= '<div class="sharing-hidden"><div class="inner" style="display: none;';

				if ( count( $enabled['hidden'] ) == 1 )
					$sharing_content .= 'width:150px;';

				$sharing_content .= '">';

				if ( count( $enabled['hidden'] ) == 1 )
					$sharing_content .= '<ul style="background-image:none;">';
				else
					$sharing_content .= '<ul>';

				$count = 1;
				foreach ( $enabled['hidden'] as $id => $service ) {
					// Individual HTML for sharing service
					$sharing_content .= '<li class="share-'.$service->get_class().'">';
					$sharing_content .= $service->get_display( $post );
					$sharing_content .= '</li>';

					if ( ( $count % 2 ) == 0 )
						$sharing_content .= '<li class="share-end"></li>';

					$count ++;
				}

				// End of wrapper
				$sharing_content .= '<li class="share-end"></li></ul></div></div>';
			}

			$sharing_content .= '</div></div></div>';

			// Register our JS
			wp_register_script( 'sharing-js', plugin_dir_url( __FILE__ ).'sharing.js', array( 'jquery' ), '20121205' );
			add_action( 'wp_footer', 'sharing_add_footer' );
		}
	}

	if ( $echo )
		echo $text.$sharing_content;
	else
		return $text.$sharing_content;
}

add_filter( 'the_content', 'sharing_display', 19 );
add_filter( 'the_excerpt', 'sharing_display', 19 );
function get_base_recaptcha_lang_code() {

	$base_recaptcha_lang_code_mapping = array(
		'en'    => 'en',
		'nl'    => 'nl',
		'fr'    => 'fr',
		'fr-be' => 'fr',
		'fr-ca' => 'fr',
		'fr-ch' => 'fr',
		'de'    => 'de',
		'pt'    => 'pt',
		'pt-br' => 'pt',
		'ru'    => 'ru',
		'es'    => 'es',
		'tr'    => 'tr'
	);

	$blog_lang_code = function_exists( 'get_blog_lang_code' ) ? get_blog_lang_code() : get_bloginfo( 'language' );
	if( isset( $base_recaptcha_lang_code_mapping[ $blog_lang_code ] ) )
		return $base_recaptcha_lang_code_mapping[ $blog_lang_code ];

	// if no base mapping is found return default 'en'
	return 'en';
}

/*
Edits by Anas H. Sulaiman:
E-1: disconnect from jetpack
*/
