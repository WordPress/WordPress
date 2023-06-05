<?php 


add_filter( 'agni_content_posts', 'agni_import_export_content_posts', 10 , 2 );
add_filter( 'agni_content_categories', 'agni_import_export_content_categories', 10, 2 );
add_filter( 'agni_content_tags', 'agni_import_export_content_tags', 10, 2 );
add_filter( 'agni_content_comments', 'agni_import_export_content_comments', 10, 2 );
add_filter( 'agni_content_pages', 'agni_import_export_content_pages', 10, 2 );
add_filter( 'agni_content_attachments', 'agni_import_export_content_attachments', 10, 2 );
add_filter( 'agni_content_products_attributes', 'agni_import_export_content_products_attributes', 10, 2 );
add_filter( 'agni_content_products', 'agni_import_export_content_products', 10, 2 );
add_filter( 'agni_content_products_categories', 'agni_import_export_content_products_categories', 10, 2 );
add_filter( 'agni_content_products_tags', 'agni_import_export_content_products_tags', 10, 2 );
add_filter( 'agni_content_products_reviews', 'agni_import_export_content_products_reviews', 10, 2 );
add_filter( 'agni_content_blocks', 'agni_import_export_content_blocks', 10, 2 );
add_filter( 'agni_content_menus', 'agni_import_export_content_menus', 10, 2 );
add_filter( 'agni_content_theme_options', 'agni_import_export_content_theme_options', 10, 2 );
add_filter( 'agni_content_set_homepage', 'agni_import_export_content_set_homepage', 10, 2 );

add_filter( 'agni_content_insert_post', 'agni_import_export_content_insert_post', 10, 1 );
add_filter( 'agni_content_insert_term', 'agni_import_export_content_insert_term', 10, 2 );
add_filter( 'agni_content_insert_product', 'agni_import_export_content_insert_product', 10, 3 );
add_filter( 'agni_content_process_parsed_blocks', 'agni_import_export_process_parsed_blocks', 10, 2 );
add_filter( 'agni_content_process_rendered_blocks', 'agni_import_export_process_rendered_blocks', 10, 1 );


function agni_import_export_content_posts( $posts, $options ) {
	$prepare_options = array();

	$new_demo_content_options = get_option( 'agni_importer_exporter_demo_content_mapping' );

	foreach ($posts as $key => $post) {

		$existing_post = get_page_by_title( $post['title']['raw'], OBJECT, $post['type'] );
		
		if ( is_null( $existing_post ) ) {

			/**
			 * Insert post from the processed content
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$new_post_id = apply_filters( 'agni_content_insert_post', $post );

			$new_category = array();
			$new_tag = array();
			foreach ($post['categories'] as $key => $value) {
				$new_category[$key] = $new_demo_content_options['categories'][$value];
			}
			foreach ($post['tags'] as $key => $value) {
				$new_tag[$key] = $new_demo_content_options['tags'][$value];
			}

			wp_set_post_terms( $new_post_id, $new_category, 'category' );
			wp_set_post_terms( $new_post_id, $new_tag, 'post_tag' );


			if ( 0 !== $post['featured_media'] ) {
				update_post_meta( $new_post_id, '_thumbnail_id', $new_demo_content_options['media'][$post['featured_media']] );
			}
		} else {
			$new_post_id = $existing_post->ID;
		}


		$prepare_options[$post['id']] = $new_post_id;
	}

	agni_prepare_importer_exporter_options( $prepare_options, 'posts' );

	return agni_prepare_return_success_array( 'Posts added' );
}

function agni_import_export_content_categories( $categories, $options ) {

	$prepare_options = array();

	$new_demo_content_options = get_option( 'agni_importer_exporter_demo_content_mapping' );

	$new_category = array();
	foreach ($categories as $key => $category) {

		$term_exists = term_exists( $category['slug'], 'category' );

		if ( !$term_exists ) {
			
			
			/**
			 * Insert term from the processed content
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$new_category[$category['id']] = apply_filters( 'agni_content_insert_term', $category, 'category' );


		} else {
			$new_category[$category['id']] = $term_exists;

		}

		$new_term_id = $new_category[$category['id']]['term_id'];

		$prepare_options[$category['id']] = $new_term_id;
	}

	foreach ($categories as $key => $category) {    
		if ( 0 !== $category['parent'] ) {
			$new_parent_term_id = $prepare_options[$category['parent']];
			wp_update_term( $new_category[$category['id']]['term_id'], 'category', array(
				'parent' => $new_parent_term_id
			) );
		}
	}

	agni_prepare_importer_exporter_options( $prepare_options, 'categories' );
	
	return agni_prepare_return_success_array( 'Categories added' );
}

function agni_import_export_content_tags( $tags, $options ) {

	$prepare_options = array();

	$new_demo_content_options = get_option( 'agni_importer_exporter_demo_content_mapping' );

	$new_tag = array();
	foreach ($tags as $key => $tag) {

		$term_exists = term_exists( $tag['slug'], 'post_tag' );

		if ( !$term_exists ) {

			/**
			 * Insert term from the processed content
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$new_tag[$tag['id']] = apply_filters( 'agni_content_insert_term', $tag, 'post_tag' );

		} else {
			$new_tag[$tag['id']] = $term_exists;

		}

		$new_term_id = $new_tag[$tag['id']]['term_id'];

		$prepare_options[$tag['id']] = $new_term_id;
	}

	agni_prepare_importer_exporter_options( $prepare_options, 'tags' );

	return agni_prepare_return_success_array( 'Tags added' );
}

function agni_import_export_content_comments( $comments, $options ) {

	$prepare_options = array();

	$new_demo_content_options = get_option( 'agni_importer_exporter_demo_content_mapping' );
	
	foreach ($comments as $key => $comment) {

		$post_id = $new_demo_content_options['posts'][$comment['post']];

		$new_comment_id = wp_insert_comment(array(
			'comment_post_ID' => $post_id,
			'comment_parent' => $comment['parent'],
			'comment_author' => $comment['author_name'],
			'comment_author_url' => $comment['author_url'],
			'comment_author_email' => $comment['author_email'],
			'comment_date' => $comment['date'],
			'comment_date_gmt' => $comment['date_gmt'],
			'comment_content' => $comment['content']['raw'],
			'comment_approved' => ( 'approved' ==  $comment['status'] ) ? 1 : 0,
			'comment_type' => $comment['type'],
			'comment_meta' => $comment['meta']
		));

		$prepare_options[$comment['id']] = $new_comment_id;
	}


	foreach ($comments as $key => $comment) {    
		if ( 0 !==  $comment['parent'] ) {
			$new_parent_comment_id = $prepare_options[$comment['parent']];
			wp_update_comment(array(
				'comment_ID' => $prepare_options[$comment['id']],
				'comment_parent' => $new_parent_comment_id,
			));
		}
	}


	agni_prepare_importer_exporter_options( $prepare_options, 'comments' );

	return agni_prepare_return_success_array( 'Comments added' );
}

function agni_import_export_content_pages( $posts, $options ) {

	$prepare_options = array();

	$new_demo_content_options = get_option( 'agni_importer_exporter_demo_content_mapping' );

	foreach ($posts as $key => $post) {

		$existing_post = get_page_by_title( $post['title']['raw'], OBJECT, $post['type'] );
		
		if ( is_null( $existing_post ) ) {

			/**
			 * Insert post from the processed content
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$new_post_id = apply_filters( 'agni_content_insert_post', $post );


			if ( 0 !==  $post['featured_media'] ) {
				update_post_meta( $new_post_id, '_thumbnail_id', $new_demo_content_options['media'][$post['featured_media']] );
			}
		} else {
			$new_post_id = $existing_post->ID;
		}


		$prepare_options[$post['id']] = $new_post_id;
	}

	agni_prepare_importer_exporter_options( $prepare_options, 'pages' );


	return agni_prepare_return_success_array( 'Pages added' );
}

function agni_import_export_content_attachments( $attachments, $options ) {
	$prepare_options = array();

	foreach ($attachments as $key => $args) {

		$new_demo_content_options_media = array();
		$new_demo_content_options = get_option( 'agni_importer_exporter_demo_content_mapping' );

		if ( isset( $new_demo_content_options['media'] ) ) {
			$new_demo_content_options_media = $new_demo_content_options['media'];
		}


			$url = $args['source_url']; 

			$date = wp_date( 'Y/m', strtotime($args['date']) );

			$upload_dir = wp_upload_dir();

		if ( !file_exists( $upload_dir['basedir'] . '/' . $date . '/' . basename($url) ) ) {


			if ( !class_exists( 'WP_Http' ) ) {
				include_once( ABSPATH . WPINC . '/class-http.php' );
			}

			$http = new WP_Http();
			$response = $http->request( $url );


			if ( is_wp_error( $response ) ) {
				return array( 
					'success' => false,
					'data' => $response->get_error_message()
				);

				exit;
			}



			$upload = wp_upload_bits( basename($url), null, $response['body'], $date );

			// print_r( $upload );

			if ( !empty( $upload['error'] ) ) {
				return array( 
					'success' => false,
					'data' => $upload['error']
				);
			}
				
			// Get the path to the upload directory.
			$wp_upload_dir = wp_upload_dir();

			$file = $upload['file'];

			$media = array(
				'import_id'         => $args['id'],
				'post_date'         => $args['date'],
				'post_date_gmt'     => $args['date_gmt'],
				'post_modified'     => $args['modified'],
				'post_modified_gmt' => $args['modified_gmt'],
				'guid'              => $args['guid']['raw'],
				'post_title'        => $args['title']['raw'],
				'post_mime_type'    => $args['mime_type'],
				'post_type'         => $args['type'],
				'post_status'       => $args['status'], 
				'post_content'      => $args['description']['raw'],
				'post_excerpt'      => $args['caption']['raw'],
				'comment_status'    => $args['comment_status'],
				'ping_status'       => $args['ping_status'],
				'meta_input'        => $args['meta'],
			
			);
			


			$existing_attachment = get_page_by_title( $args['title']['raw'], OBJECT, $args['type'] );

			if ( is_null( $existing_attachment ) ) {
				$attach_id = wp_insert_attachment( $media, $file, $args['post'], 0, true );

				// require_once( ABSPATH . 'wp-admin/includes/image.php' );

				// $attach_data = wp_generate_attachment_metadata( $attach_id, $file );

				// wp_update_attachment_metadata( $attach_id,  $attach_data );

				update_post_meta($attach_id, '_wp_attachment_image_alt', $args['alt_text']);

			} else {
				$attach_id = $existing_attachment->ID;
			}

			$new_demo_content_options_media[$args['id']] = $attach_id;
		}
		// }

		agni_prepare_importer_exporter_options( $new_demo_content_options_media, 'media' );

		// $prepare_options[$args['id']] = $attach_id;

	}

	return agni_prepare_return_success_array( 'I am parsing Attachments' );
}

function agni_import_export_content_products_attributes( $attributes, $options ) {

	$prepare_options = array();

	foreach ($attributes as $key => $attribute) {

		$attribute_id = wc_create_attribute(
			array(
				'name'         => $attribute['name'],
				'slug'         => $attribute['slug'],
				'type'         => $attribute['type'],
				'order_by'     => $attribute['order_by'],
				'has_archives' => $attribute['has_archives'],
			)
		);

		$prepare_options['attributes'][$attribute['id']] = $attribute_id;

		foreach ($attribute['terms'] as $key => $term) {
			if ( !is_wp_error($attribute_id) ) {

				$term_exists = term_exists( $term['slug'], $attribute['slug'] );

				if ( !$term_exists ) {

					$new_term[$term['id']] = wp_insert_term( $term['name'], $attribute['slug'], array(
						'description' => $term['description'],
						'slug'        => $term['slug'],
						'menu_order'  => $term['menu_order'],
						'count'       => $term['count']
					) );


				$new_term_id = $new_term[$term['id']]['term_id'];

				$prepare_options['terms'][$term['id']] = $new_term_id;
				}
				// else{
				//     $new_term[$term['id']] = $term_exists;
				// }

				$new_term_id = $new_term[$term['id']]['term_id'];

				$prepare_options['terms'][$term['id']] = $new_term_id;
			}
			// else{
			//     echo $attribute_id->get_error_message();
			// }
		}
	}

	agni_prepare_importer_exporter_options( $prepare_options, 'products_attributes' );

	return agni_prepare_return_success_array( 'Products attributes added' );
}
function agni_import_export_content_products( $products, $options ) {

	$prepare_options = array();

	$new_demo_content_options = get_option( 'agni_importer_exporter_demo_content_mapping' );
	
	foreach ($products as $key => $product) {
			
		$existing_post = get_page_by_title( $product['name'], OBJECT, 'product' );

		if ( is_null( $existing_post ) ) {

			/**
			 * Insert product from the processed content
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$new_product_id = apply_filters( 'agni_content_insert_product', $product, 'product' );
			
			if ( !empty( $product['categories'] ) ) {
				$product_categories = array();
				foreach ($product['categories'] as $key => $category) {
					$product_categories[] = (int) $new_demo_content_options['products_categories'][$category['id']];
				}

				wp_set_post_terms( $new_product_id, $product_categories, 'product_cat' );
			}
			if ( !empty( $product['tags'] ) ) {
				$product_tags = array();
				foreach ($product['tags'] as $key => $tag) {
					$product_tags[] = (int) $new_demo_content_options['products_tags'][$tag['id']];
				}

				wp_set_post_terms( $new_product_id, $product_tags, 'product_tag' );
			}


			wp_set_post_terms( $new_product_id, $product['type'], 'product_type' );

			$meta_datas = array();

			foreach ($product['meta_data'] as $key => $meta) {
				$meta_datas[$meta['id']] = array( 'key' => $meta['key'], 'value' => $meta['value'] );
			}

			foreach ($meta_datas as $key => $meta) {
				update_post_meta( $new_product_id, $meta['key'], $meta['value'] ); 
			}

			$featured_thumbnail_id = '';
			$gallery_ids = array();

			if ( !empty($product['images']) ) {
				foreach ($product['images'] as $key => $image) {
					if ( 0 == $key ) {
						$featured_thumbnail_id = $new_demo_content_options['media'][$image['id']];
					} else {
						$gallery_ids[] = $new_demo_content_options['media'][$image['id']];
					}
				}
			}

			if ( !empty( $featured_thumbnail_id ) ) {
				update_post_meta( $new_product_id, '_thumbnail_id', $featured_thumbnail_id );
			}

			if ( !empty( $gallery_ids ) ) {
				update_post_meta( $new_product_id, '_product_image_gallery', implode(',', $gallery_ids) );
			}
		   

			if ( isset($product['type']) && ( 'variable' === $product['type'] ) ) {
				$set_product = new WC_Product_Variable($new_product_id);
			} elseif ( isset($product['type']) && ( 'grouped' === $product['type'] ) ) {
				$set_product = new WC_Product_Grouped($new_product_id);
			} elseif ( isset($product['type']) && ( 'external' === $product['type'] ) ) {
				$set_product = new WC_Product_External($new_product_id);
			} else {
				$set_product = new WC_Product_Simple($new_product_id); // "simple" By default
			} 

			if ( isset($product['downloadable']) && $product['downloadable'] ) {
				$set_product->set_downloads( isset($product['downloads']) ? $product['downloads'] : array() );
			}

			// $set_product->set_featured( true );
			
			$set_product->save();

			if ( ( 'variable' === $product['type'] ) && isset($product['variations_products']) ) {
				foreach ($product['variations_products'] as $key => $variation) {

					/**
					 * Insert product from the processed content
					 * 
					 * @since Bagberry 1.0
					 * 
					 */
					$variation_id = apply_filters( 'agni_content_insert_product', $variation, 'product_variation', $new_product_id );

					$prepare_options[$variation['id']] = $variation_id;


					update_post_meta( $variation_id, '_variation_description', $variation['description'] );


					if ( !empty($variation['image']) ) {
						update_post_meta( $variation_id, '_thumbnail_id', $new_demo_content_options['media'][$variation['image']['id']] );
					}

					$set_variation = new WC_Product_Variation($variation_id);

					if ( isset($variation['downloadable']) && $variation['downloadable'] ) {
						$set_variation->set_downloads( isset($variation['downloads']) ? $variation['downloads'] : array() );
					}

					$set_variation->save();
				}
			}
		} else {
			$new_product_id = $existing_post->ID;
			
		}
		
		$prepare_options[$product['id']] = $new_product_id;

	}


	agni_prepare_importer_exporter_options( $prepare_options, 'products' );

	return agni_prepare_return_success_array( 'Products added need revision for compare, addons products' );
}
function agni_import_export_content_products_categories( $categories, $options ) {

	$prepare_options = array();

	$new_demo_content_options = get_option( 'agni_importer_exporter_demo_content_mapping' );

	$new_category = array();
	foreach ($categories as $key => $category) {

		$term_exists = term_exists( $category['slug'], 'product_cat' );

		if ( !$term_exists ) {
			// $category['parent'] = 0;

			/**
			 * Insert term from the processed content
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$new_category[$category['id']] = apply_filters( 'agni_content_insert_term', $category, 'product_cat' );
			
		
			if ( !is_null($category['image']) ) {
				update_term_meta( $new_category[$category['id']]['term_id'], 'thumbnail_id', $new_demo_content_options['media'][$category['image']['id']] );
			}
		} else {
			$new_category[$category['id']] = $term_exists;

		}
		$new_term_id = $new_category[$category['id']]['term_id'];

		$prepare_options[$category['id']] = $new_term_id;
	}

	foreach ($categories as $key => $category) {    
		if ( 0 !== $category['parent'] ) {
			$new_parent_term_id = $prepare_options[$category['parent']];
			wp_update_term( $new_category[$category['id']]['term_id'], 'product_cat', array(
				'parent' => $new_parent_term_id
			) );
		}
	}

	agni_prepare_importer_exporter_options( $prepare_options, 'products_categories' );


	return agni_prepare_return_success_array( 'Product categories added' );
}
function agni_import_export_content_products_tags( $tags, $options ) {

	$prepare_options = array();

	$new_demo_content_options = get_option( 'agni_importer_exporter_demo_content_mapping' );

	$new_tag = array();
	foreach ($tags as $key => $tag) {

		$term_exists = term_exists( $tag['slug'], 'product_tag' );

		if ( !$term_exists ) {

			/**
			 * Insert term from the processed content
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$new_tag[$tag['id']] = apply_filters( 'agni_content_insert_term', $tag, 'product_tag' );

			
		} else {
			$new_tag[$tag['id']] = $term_exists;

		}

		$new_term_id = $new_tag[$tag['id']]['term_id'];

		$prepare_options[$tag['id']] = $new_term_id;
	}

	agni_prepare_importer_exporter_options( $prepare_options, 'products_tags' );

	return agni_prepare_return_success_array( 'Products tags added' );
}


function agni_import_export_content_products_reviews( $reviews, $options ) {

	$prepare_options = array();

	$new_demo_content_options = get_option( 'agni_importer_exporter_demo_content_mapping' );
   
	foreach ($reviews as $key => $review) {
		
		$product_id = $new_demo_content_options['products'][$review['product_id']];

		$review_id = wp_insert_comment(array(
			'comment_post_ID' => $product_id,
			'comment_author' => $review['reviewer'],
			'comment_author_email' => $review['reviewer_email'],
			'comment_date' => $review['date_created'],
			'comment_date_gmt' => $review['date_created_gmt'],
			'comment_content' => $review['review'],
			'comment_approved' => ( 'approved' == $review['status'] ) ? 1 : 0,
			'comment_type' => 'review',
		));
	  
		update_comment_meta($review_id, 'rating', $review['rating']);
		update_comment_meta($review_id, 'verified', $review['verified']);
	  
		$prepare_options[$review['product_id']] = $review_id;
	}

	agni_prepare_importer_exporter_options( $prepare_options, 'products_reviews' );

	return agni_prepare_return_success_array( 'Products reviews added' );
}


function agni_import_export_content_blocks( $blocks, $options ) {
	$prepare_options = array();

	$new_demo_content_options = get_option( 'agni_importer_exporter_demo_content_mapping' );

	foreach ($blocks as $key => $block) {

		$existing_post = get_page_by_title( $block['title']['raw'], OBJECT, $block['type'] );
		
		if ( is_null( $existing_post ) ) {

			/**
			 * Insert post from the processed content
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$new_post_id = apply_filters( 'agni_content_insert_post', $block );

		} else {
			$new_post_id = $existing_post->ID;
		}


		$prepare_options[$block['id']] = $new_post_id;
	}

	agni_prepare_importer_exporter_options( $prepare_options, 'blocks' );

	return agni_prepare_return_success_array( 'WP blocks added' );
}


function agni_import_export_content_menus( $menus, $options ) {

	$prepare_options = array();

	$new_demo_content_options = get_option( 'agni_importer_exporter_demo_content_mapping' );

	foreach ($menus as $key => $post) {
		
		$total_demo_ids = [];

		$total_demo_ids = $new_demo_content_options['pages'] + $new_demo_content_options['posts'] + $new_demo_content_options['products'];

		// print_r( parse_blocks( $post['content']['raw'] ) );
		$blocks = parse_blocks( $post['content']['raw'] );
			
		/**
		 * Modify url & id of menu items from the parsed block
		 * 
		 * @since Bagberry 1.0
		 * 
		 */
		$parsed_blocks = apply_filters( 'agni_content_process_parsed_blocks', $blocks, $total_demo_ids );
		
		/**
		 * Serialize parsed blocks
		 * 
		 * @since Bagberry 1.0
		 * 
		 */
		$post['content']['raw'] = apply_filters( 'agni_content_process_rendered_blocks', $parsed_blocks );

		$existing_post = get_page_by_title( $post['title']['raw'], OBJECT, $post['type'] );
		
		if ( is_null( $existing_post ) ) {

			/**
			 * Insert post from the processed content
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$new_post_id = apply_filters( 'agni_content_insert_post', $post );

		} else {
			$new_post_id = $existing_post->ID;
		}


		$prepare_options[$post['id']] = $new_post_id;
	}


	agni_prepare_importer_exporter_options( $prepare_options, 'menus' );
	
	return agni_prepare_return_success_array( 'Menus added' );
}


function agni_import_export_content_theme_options( $theme_options, $options ) {


	$new_demo_content_options = get_option( 'agni_importer_exporter_demo_content_mapping' );

	unset( $theme_options[0]['0'] );
	unset( $theme_options[0]['nav_menu_locations'] );
	unset( $theme_options[0]['custom_css_post_id'] );
	unset( $theme_options[0]['sidebars_widgets'] );

	foreach ($theme_options[0] as $setting_key => $setting_value) {
		$new_setting_value = $setting_value;

		switch ( $setting_key ) {
			case 'custom_logo':
				$new_setting_value = $new_demo_content_options['media'][$setting_value];
				break;
			default: 
		}

		set_theme_mod( $setting_key, $new_setting_value );
	}

	return agni_prepare_return_success_array( 'Theme options added' );
}

function agni_import_export_content_set_homepage( $pages, $options ) {

	$new_demo_content_options = get_option( 'agni_importer_exporter_demo_content_mapping' );

	$page_id = $pages[0]['id'];

	update_option( 'page_on_front', $new_demo_content_options['pages'][$page_id] );
	update_option( 'show_on_front', 'page' );

	return agni_prepare_return_success_array( 'Home Page assigned' );

}


function agni_import_export_process_parsed_blocks( $blocks, $total_demo_ids) {
	$new_blocks = array();

	foreach ($blocks as $key => $block) {
		if ( !empty( $block['blockName'] ) ) {
			
			if ( !empty( $total_demo_ids[$block['attrs']['id']] ) ) {
				switch ( $block['attrs']['kind'] ) {
					case 'post-type':
						$block['attrs']['id'] = $total_demo_ids[$block['attrs']['id']];
						$block['attrs']['url'] = get_permalink( $total_demo_ids[$block['attrs']['id']] );
						
						break;
					case 'custom':
						$block['attrs']['id'] = $total_demo_ids[$block['attrs']['id']];
						$block['attrs']['url'] = str_replace( 'https://demo.agnidesigns.com/bagberry', home_url(), $block['attrs']['url']);
						
						break;
					default:
						break;
				}
			}
			if ( !empty( $block['innerBlocks'] ) ) {

				/**
				 * Modify url & id of menu items from the parsed block
				 * 
				 * @since Bagberry 1.0
				 * 
				 */
				$block['innerBlocks'] = apply_filters( 'agni_content_process_parsed_blocks', $block['innerBlocks'], $total_demo_ids );
			}

			$new_blocks[] = $block;
		}
	}

	return $new_blocks;
}


function agni_import_export_process_rendered_blocks( $blocks) {
	$post_content_raw = '';

	foreach ($blocks as $block) {            
		// $post_content_raw .= '<!-- ' . str_replace('core/', 'wp:', $block['blockName']) . ' ' . json_encode( $block['attrs'] ) . ' -->';
		// $post_content_raw .= render_block($block);
		// // if( !empty( $block['innerBlocks'] ) ){
		// //     $post_content_raw .= apply_filters( 'agni_content_process_rendered_blocks', $block['innerBlocks'] );
		// // }

		$post_content_raw .= serialize_block( $block );
	}

	return $post_content_raw;
}




function agni_import_export_content_insert_post( $args) {
	$new_post = array(
		'import_id'         => $args['id'],
		'post_date'         => $args['date'],
		'post_date_gmt'     => $args['date_gmt'],
		'post_modified'     => $args['modified'],
		'post_modified_gmt' => $args['modified_gmt'],
		'guid'              => $args['guid']['raw'],
		'post_title'        => $args['title']['raw'],
		'post_type'         => $args['type'],
		'post_status'       => $args['status'], 
		// 'post_content'      => $args['content']['raw'],
		// 'post_excerpt'      => $args['excerpt']['raw'],
		'post_parent'       => $args['parent'],
		'comment_status'    => $args['comment_status'],
		'ping_status'       => $args['ping_status'],
		'post_password'     => $args['password'],
		'menu_order'        => $args['menu_order'],
		'page_template'     => $args['template'],
		'meta_input'        => $args['meta']

	);

	if ( 'page' !== $args['type'] ) {
		$new_post['post_category'] = $args['categories'];
		$new_post['tags_input'] = $args['tags'];
	}

	if ( !empty( $args['content']['raw'] ) ) {
		$new_post['post_content'] = wp_slash( $args['content']['raw'] );
	}
	if ( !empty( $args['excerpt']['raw'] ) ) {
		$new_post['post_excerpt'] = $args['excerpt']['raw'];
	}

	$existing_post = get_page_by_title( $args['title']['raw'], OBJECT, $args['type'] );
	// print_r( $existing_post );
	if ( is_null( $existing_post ) ) {
		$new_post_id = wp_insert_post( $new_post );
	} else {
		$new_post_id = $existing_post->ID;
	}

	
	if ( ( 0 !== $new_post_id ) && ( 'post' === $args['type'] ) ) {
		if ( isset( $args['format'] ) ) {
			set_post_format($new_post_id, $args['format'] );
		}
		if ( true == $args['sticky'] ) {
			$sticky_posts_list = get_option( 'sticky_posts' );
			array_push( $sticky_posts_list, $new_post_id );
			update_option( 'sticky_posts', $sticky_posts_list );
			
		}
	}

	return $new_post_id;

}


function agni_import_export_content_insert_term( $args, $taxonomy) {
	
	// $parent = get_term_by( 'slug', $args['parent_slug'], $taxonomy );
	
	$tax_args = array(
		'slug' => $args['slug'],
		'description' => $args['description']
	);

	$new_term_ids = wp_insert_term( $args['name'], $taxonomy, $tax_args );
	
	foreach ($args['meta'] as $key => $value) {
		update_term_meta( $new_term_ids['term_id'], $key, $value, true );
	}

	// print_r( $new_term_ids );

	return $new_term_ids;

}


function agni_import_export_content_insert_product( $args, $post_type, $parent = '') {


	$product_args = array(
		'import_id'         => $args['id'],
		'post_date'         => $args['date_created'],
		'post_date_gmt'     => $args['date_created_gmt'],
		'post_modified'     => $args['date_modified'],
		'post_modified_gmt' => $args['date_modified_gmt'],
		'post_title'        => $args['name'],
		'post_type'         => $post_type,
		'post_status'       => $args['status'], 
		'post_parent'       => !empty( $parent ) ? $parent : $args['parent_id'],
		'menu_order'        => $args['menu_order'],

	);

	if ( empty( $parent ) ) {
		$product_args['post_content'] = wp_slash( $args['description'] );
		$product_args['post_excerpt'] = $args['short_description'];
	}

	$post_id = wp_insert_post( $product_args );


	update_post_meta( $post_id, '_visibility', $args['catalog_visibility'] );
	update_post_meta( $post_id, '_stock_status', $args['stock_status']);
	update_post_meta( $post_id, 'total_sales', $args['total_sales'] );
	update_post_meta( $post_id, '_downloadable', $args['downloadable'] );
	update_post_meta( $post_id, '_download_limit', $args['download_limit'] );
	update_post_meta( $post_id, '_download_expiry', $args['download_expiry'] );
	update_post_meta( $post_id, '_virtual', $args['virtual'] );
	update_post_meta( $post_id, '_regular_price', $args['regular_price'] );
	update_post_meta( $post_id, '_sale_price', $args['sale_price'] );
	update_post_meta( $post_id, '_wc_average_rating', $args['average_rating'] );
	update_post_meta( $post_id, '_wc_rating_count', $args['rating_count'] );
	update_post_meta( $post_id, '_wc_review_count', $args['review_count'] );
	update_post_meta( $post_id, '_purchase_note', $args['purchase_note'] );
	update_post_meta( $post_id, '_featured', $args['featured'] );
	update_post_meta( $post_id, '_weight', $args['weight'] );
	update_post_meta( $post_id, '_length', $args['dimensions']['length'] );
	update_post_meta( $post_id, '_width', $args['dimensions']['width'] );
	update_post_meta( $post_id, '_height', $args['dimensions']['height'] );
	update_post_meta( $post_id, '_sku', $args['sku'] );
	
	if ( empty( $parent ) ) {
		update_post_meta( $post_id, '_product_attributes', agni_import_product_attributes( $post_id, $args['attributes'] ) );
	} else {
	// else if( $args['type'] === 'variable' ){
		agni_import_product_variation_attributes( $post_id, $args['attributes']);
	}

	update_post_meta( $post_id, '_sale_price_dates_from', $args['date_on_sale_from'] );
	update_post_meta( $post_id, '_sale_price_dates_to', $args['date_on_sale_to'] );
	update_post_meta( $post_id, '_price', $args['price'] );
	update_post_meta( $post_id, '_sold_individually', $args['sold_individually'] );
	update_post_meta( $post_id, '_manage_stock', $args['manage_stock'] );
	update_post_meta( $post_id, '_tax_status', $args['tax_status'] );
	update_post_meta( $post_id, '_tax_class', $args['tax_class'] );
	update_post_meta( $post_id, '_upsell_ids', $args['upsell_ids'] );
	update_post_meta( $post_id, '_cross_sell_ids', $args['cross_sell_ids'] );
	// update_post_meta( $post_id, '_related_ids', $args['related_ids'] );
	update_post_meta( $post_id, '_backorders', $args['backorders'] );

	foreach ( $args['meta_data'] as $meta ) {
		// print_r( $meta );
		update_post_meta( $post_id, $meta['key'], $meta['value'] );
	}
	
	wc_update_product_stock($post_id, $args['stock_quantity'], 'set');

	return $post_id;
}


function agni_import_product_attributes( $post_id, $attributes ) {

	$data = array();

	foreach ($attributes as $key => $attribute) {

		$attribute_name = strtolower($attribute['name']);

		if ( 0 !== $attribute['id'] ) {
			wp_set_object_terms( $post_id, $attribute['options'], 'pa_' . $attribute_name, true );

			$data['pa_' . $attribute_name] = array(
				'name' => 'pa_' . $attribute_name,
				'options' => $attribute['options'],
				'value' => '',
				'is_visible' => $attribute['visible'],
				'is_variation' => $attribute['variation'],
				'is_taxonomy' => true
			);
		} else {
			$data[$attribute_name] = array(
				'name' => $attribute['name'],
				// 'options' => $attribute['options'],
				'value' => wc_implode_text_attributes( $attribute['options'] ),
				'position' => $attribute['position'],
				'is_visible' => $attribute['visible'],
				'is_variation' => $attribute['variation'],
				'is_taxonomy' => false
			);
		}
		
	}
	
	return $data;

}

function agni_import_product_variation_attributes( $post_id, $attributes ) {
	
	foreach ($attributes as $key => $attribute) {

		$taxonomy = 'pa_' . strtolower( $attribute['name'] ); // The attribute taxonomy

		$term_slug = get_term_by('name', $attribute['option'], $taxonomy )->slug;

		update_post_meta( $post_id, 'attribute_' . $taxonomy, $term_slug );

	}
}

function agni_import_export_get_new_id( $existing_header_ids ) {

	$missing_header_ids = array();
	for ($i=min( $existing_header_ids ); $i < max( $existing_header_ids ); $i++) { 
		if ( !in_array( $i, $existing_header_ids ) ) {
			$missing_header_ids[] = $i;
		}
	}

	if ( !empty( $missing_header_ids ) ) {
		return min( $missing_header_ids );
	} else {
		return max( $existing_header_ids ) + 1;
	}
}


function agni_prepare_importer_exporter_options( $new_options, $slug ) {


	$new_demo_content_options = get_option( 'agni_importer_exporter_demo_content_mapping' );

	$new_demo_content_options[$slug] = $new_options;

	update_option( 'agni_importer_exporter_demo_content_mapping', $new_demo_content_options );

}

function agni_prepare_return_success_array( $string ) {
	return array(
		'success' => true,
		'data' => $string
	);
}

