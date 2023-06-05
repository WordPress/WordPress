<?php 

add_filter( 'agni_importer_exporter_parser', 'agni_importer_content_parser', 10, 3 );


function agni_importer_content_parser( $content, $contentChoice, $options ) {

	$result = '';

	// if( $contentChoice !== 'menus' ){
	//     $result = array(  'success' => true, 'data' => 'tested' );
	// }
	// else{
	//     $result = apply_filters( 'agni_content_menus', $content, $options );
	// }

	// return $result;

	switch ($contentChoice) {
		case 'posts':
			/**
			 * Processing the posts from parsed content
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$result = apply_filters( 'agni_content_posts', $content, $options );
			break;
		case 'categories':
			/**
			 * Processing the categories from parsed content
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$result = apply_filters( 'agni_content_categories', $content, $options );
			break;
		case 'tags':
			/**
			 * Processing the tags from parsed content
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$result = apply_filters( 'agni_content_tags', $content, $options );
			break;
		case 'comments':
			/**
			 * Processing the comments from parsed content
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$result = apply_filters( 'agni_content_comments', $content, $options );
			break;
		case 'pages':
			/**
			 * Processing the pages from parsed content
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$result = apply_filters( 'agni_content_pages', $content, $options );
			break;
		case 'media':
			/**
			 * Processing the media from parsed content
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$result = apply_filters( 'agni_content_attachments', $content, $options );
			break;
		case 'products':
			/**
			 * Processing the prducts from parsed content
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$result = apply_filters( 'agni_content_products', $content, $options );
			break;
		case 'products_categories':
			/**
			 * Processing the product categories from parsed content
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$result = apply_filters( 'agni_content_products_categories', $content, $options );
			break;
		case 'products_tags':
			/**
			 * Processing the product tags from parsed content
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$result = apply_filters( 'agni_content_products_tags', $content, $options );
			break;
		case 'products_reviews':
			/**
			 * Processing the product reviews from parsed content
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$result = apply_filters( 'agni_content_products_reviews', $content, $options );
			break;
		case 'products_attributes':
			/**
			 * Processing the product attributes from parsed content
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$result = apply_filters( 'agni_content_products_attributes', $content, $options );
			break;
		case 'blocks':
			/**
			 * Processing the reusable blocks from parsed content
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$result = apply_filters( 'agni_content_blocks', $content, $options );
			break;
		case 'menus':
			/**
			 * Processing the menus from parsed content
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$result = apply_filters( 'agni_content_menus', $content, $options );
			break;
		case 'theme_options':
			/**
			 * Processing the theme options from parsed content
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$result = apply_filters( 'agni_content_theme_options', $content, $options );
			break;
		case 'set_homepage':
			/**
			 * Processing the homepage settings from parsed content
			 * 
			 * @since Bagberry 1.0
			 * 
			 */
			$result = apply_filters( 'agni_content_set_homepage', $content, $options );
			break;
		
		default:
			break;
	}

	return $result;

}
