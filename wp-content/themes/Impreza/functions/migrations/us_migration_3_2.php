<?php

class us_migration_3_2 extends US_Migration_Translator {

	private $clients_items = '';
	private $clients_processed = FALSE;

	// Content
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
	}

	public function translate_us_logos( &$name, &$params, &$content ) {
		if ( ! $this->clients_processed ) {
			// Register us_clients
			register_post_type( 'us_client', array(
				'labels' => array(
					'name' => 'Clients Logos',
					'singular_name' => 'Client Logo',
					'add_new' => 'Add Client Logo',
				),
				'public' => FALSE,
				'publicly_queryable' => FALSE,
				'exclude_from_search' => FALSE,
				'show_in_nav_menus' => FALSE,
				'show_ui' => TRUE,
				'has_archive' => FALSE,
				'query_var' => FALSE,
				'supports' => array( 'title', 'thumbnail' ),
				'can_export' => TRUE,
				'capability_type' => 'us_client',
				'map_meta_cap' => TRUE,
				'menu_icon' => 'dashicons-awards',
			) );

			// Get all us_clients posts
			$args = array(
				'posts_per_page' => - 1,
				'post_type' => 'us_client',
				'post_status' => 'publish',
				'numberposts' => -1,
			);

			$clients = get_posts( $args );
			$items = array();
			foreach ( $clients as $client ) {
				if ( has_post_thumbnail( $client ) ) {
					$tnail = wp_get_attachment_image_src( get_post_thumbnail_id( $client->ID ), 'medium' );
					if ( $tnail ) {
						$item = array(
							'image' => get_post_thumbnail_id( $client->ID ),
						);
						$url = usof_meta( 'us_client_url', array(), $client->ID );
						if ( $url != '' ) {
							$target = usof_meta( 'us_client_new_tab', array(), $client->ID ) ? 'target:%20_blank|' : '|';
							$item['link'] = 'url:' . urlencode($url) . '||' . $target;
						}

						$items[] = $item;
					}
				}
			}

			if ( count( $items ) > 0 ) {
				$this->clients_items = urlencode( json_encode( $items ) );
			}

			$this->clients_processed = TRUE;


			// Deregister us_clients
			global $wp_post_types;
			if ( isset( $wp_post_types[ 'us_clients' ] ) ) {
				unset( $wp_post_types[ 'us_clients' ] );
			}
		}

		$params['items'] = $this->clients_items;

		return TRUE;

	}

}
