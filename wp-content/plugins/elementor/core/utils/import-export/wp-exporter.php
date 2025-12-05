<?php
namespace Elementor\Core\Utils\ImportExport;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Originally made by WordPress.
 *
 * What changed:
 *  Remove echos.
 *  Fix indents.
 *  Add methods
 *      indent.
 *      wxr_categories_list.
 *      wxr_tags_list.
 *      wxr_terms_list.
 *      wxr_posts_list.
 */
class WP_Exporter {
	const WXR_VERSION = '1.2';

	private static $default_args = [
		'content' => 'all',
		'author' => false,
		'category' => false,
		'start_date' => false,
		'end_date' => false,
		'status' => false,
		'offset' => 0,
		'limit' => -1,
		'meta_query' => [], // If specified `meta_key` then will include all post(s) that have this meta_key.
		'include' => [], // Array of post IDs to include in the export.
	];

	/**
	 * @var array
	 */
	private $args;

	/**
	 * @var \wpdb
	 */
	private $wpdb;

	private $terms;

	private $exported_posts = [];

	/**
	 * Run export, by requested args.
	 * Returns XML with exported data.
	 *
	 * @return array
	 */
	public function run() {
		if ( 'all' !== $this->args['content'] && post_type_exists( $this->args['content'] ) ) {
			$ptype = get_post_type_object( $this->args['content'] );
			if ( ! $ptype->can_export ) {
				$this->args['content'] = 'post';
			}

			$where = $this->wpdb->prepare( "{$this->wpdb->posts}.post_type = %s", $this->args['content'] );// phpcs:ignore
		} else {
			$post_types = get_post_types( [ 'can_export' => true ] );
			$esses = array_fill( 0, count( $post_types ), '%s' );

			$where = $this->wpdb->prepare( "{$this->wpdb->posts}.post_type IN (" . implode( ',', $esses ) . ')', $post_types );// phpcs:ignore
		}

		if ( $this->args['status'] && ( 'post' === $this->args['content'] || 'page' === $this->args['content'] || 'nav_menu_item' === $this->args['content'] ) ) {
			$where .= $this->wpdb->prepare( " AND {$this->wpdb->posts}.post_status = %s", $this->args['status'] );// phpcs:ignore
		} else {
			$where .= " AND {$this->wpdb->posts}.post_status != 'auto-draft'";
		}

		$join = '';
		if ( $this->args['category'] && 'post' === $this->args['content'] ) {
			$term = term_exists( $this->args['category'], 'category' );
			if ( $term ) {
				$join = "INNER JOIN {$this->wpdb->term_relationships} ON ({$this->wpdb->posts}.ID = {$this->wpdb->term_relationships}.object_id)";
				$where .= $this->wpdb->prepare( " AND {$this->wpdb->term_relationships}.term_taxonomy_id = %d", $term['term_taxonomy_id'] );// phpcs:ignore
			}
		}

		if ( in_array( $this->args['content'], [ 'post', 'page', 'attachment' ], true ) ) {
			if ( $this->args['author'] ) {
				$where .= $this->wpdb->prepare( " AND {$this->wpdb->posts}.post_author = %d", $this->args['author'] );// phpcs:ignore
			}

			if ( $this->args['start_date'] ) {
				$where .= $this->wpdb->prepare( " AND {$this->wpdb->posts}.post_date >= %s", gmdate( 'Y-m-d', strtotime( $this->args['start_date'] ) ) );// phpcs:ignore
			}

			if ( $this->args['end_date'] ) {
				$where .= $this->wpdb->prepare( " AND {$this->wpdb->posts}.post_date < %s", gmdate( 'Y-m-d', strtotime( '+1 month', strtotime( $this->args['end_date'] ) ) ) );// phpcs:ignore
			}
		}

		$limit = '';
		if ( -1 !== (int) $this->args['limit'] ) {
			$limit = 'LIMIT ' . (int) $this->args['limit'] . ' OFFSET ' . (int) $this->args['offset'];
		}

		if ( ! empty( $this->args['include'] ) ) {
			$include_ids = array_map( 'absint', $this->args['include'] );
			$include_placeholders = implode( ',', array_fill( 0, count( $include_ids ), '%d' ) );
			$where .= $this->wpdb->prepare( " AND {$this->wpdb->posts}.ID IN ($include_placeholders)", $include_ids ); // phpcs:ignore
		}

		if ( ! empty( $this->args['meta_query'] ) ) {
			if ( $join ) {
				$join .= ' ';
			}

			if ( $where ) {
				$where .= ' ';
			}

			$meta_query = new \WP_Meta_Query( $this->args['meta_query'] );

			global $wpdb;

			$query_clauses = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );

			$join .= $query_clauses['join'];
			$where .= $query_clauses['where'];
		}

		// Grab a snapshot of post IDs, just in case it changes during the export.
		$post_ids = $this->wpdb->get_col( "SELECT ID FROM {$this->wpdb->posts} $join WHERE $where $limit" );// phpcs:ignore
		$thumbnail_ids = [];

		if ( ! empty( $this->args['include_post_featured_image_as_attachment'] ) ) {
			foreach ( $post_ids as $post_id ) {
				$thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );

				if ( $thumbnail_id && ! in_array( $thumbnail_id, $post_ids, true ) ) {
					$thumbnail_ids [] = $thumbnail_id;
				}
			}
		}

		return [
			'ids' => $post_ids,
			'xml' => $this->get_xml_export( array_merge( $post_ids, $thumbnail_ids ) ),
			'posts' => $this->exported_posts,
		];
	}

	/**
	 * Return tabulation characters, by `$columns`.
	 *
	 * @param int $columns
	 *
	 * @return string
	 */
	private function indent( $columns = 1 ) {
		$output = '';

		for ( $i = 0; $i < $columns; $i++ ) {
			$output .= "\t";
		}

		return (string) $output;
	}

	/**
	 * Return wrapped given string in XML CDATA tag.
	 *
	 * @param string $str String to wrap in XML CDATA tag.
	 *
	 * @return string
	 */
	private function wxr_cdata( $str ) {
		$str = (string) $str;

		$is_valid_utf8 = wp_check_invalid_utf8( $str, true ) === $str;
		if ( ! $is_valid_utf8 ) {
			$str = mb_convert_encoding( $str, 'UTF-8', 'ISO-8859-1' );
		}

		$str = '<![CDATA[' . str_replace( ']]>', ']]]]><![CDATA[>', $str ) . ']]>';

		return $str;
	}

	/**
	 * Return the URL of the site.
	 *
	 * @return string Site URL.
	 */
	private function wxr_site_url() {
		if ( is_multisite() ) {
			// Multisite: the base URL.
			return network_home_url();
		} else {
			// WordPress (single site): the blog URL.
			return get_bloginfo_rss( 'url' );
		}
	}

	/**
	 * Return a cat_name XML tag from a given category object.
	 *
	 * @param \WP_Term $category Category Object.
	 *
	 * @return string
	 */
	private function wxr_cat_name( $category ) {
		if ( empty( $category->name ) ) {
			return '';
		}

		return $this->indent( 3 ) . '<wp:cat_name>' . $this->wxr_cdata( $category->name ) . '</wp:cat_name>' . PHP_EOL;
	}

	/**
	 * Return a category_description XML tag from a given category object.
	 *
	 * @param \WP_Term $category Category Object.
	 *
	 * @return string
	 */
	private function wxr_category_description( $category ) {
		if ( empty( $category->description ) ) {
			return '';
		}

		return $this->indent( 3 ) . '<wp:category_description>' . $this->wxr_cdata( $category->description ) . "</wp:category_description>\n";
	}

	/**
	 * Return a tag_name XML tag from a given tag object.
	 *
	 * @param \WP_Term $tag Tag Object.
	 *
	 * @return string
	 */
	private function wxr_tag_name( $tag ) {
		if ( empty( $tag->name ) ) {
			return '';
		}

		return $this->indent( 3 ) . '<wp:tag_name>' . $this->wxr_cdata( $tag->name ) . '</wp:tag_name>' . PHP_EOL;
	}

	/**
	 * Return a tag_description XML tag from a given tag object.
	 *
	 * @param \WP_Term $tag Tag Object.
	 *
	 * @return string
	 */
	private function wxr_tag_description( $tag ) {
		if ( empty( $tag->description ) ) {
			return '';
		}

		return $this->indent( 3 ) . '<wp:tag_description>' . $this->wxr_cdata( $tag->description ) . '</wp:tag_description>' . PHP_EOL;
	}

	/**
	 * Return a term_name XML tag from a given term object.
	 *
	 * @param \WP_Term $term Term Object.
	 *
	 * @return string
	 */
	private function wxr_term_name( $term ) {
		if ( empty( $term->name ) ) {
			return '';
		}

		return $this->indent( 3 ) . '<wp:term_name>' . $this->wxr_cdata( $term->name ) . '</wp:term_name>' . PHP_EOL;
	}

	/**
	 * Return a term_description XML tag from a given term object.
	 *
	 * @param \WP_Term $term Term Object.
	 *
	 * @return string
	 */
	private function wxr_term_description( $term ) {
		if ( empty( $term->description ) ) {
			return '';
		}

		return $this->indent( 3 ) . '<wp:term_description>' . $this->wxr_cdata( $term->description ) . '</wp:term_description>' . PHP_EOL;
	}

	/**
	 * Return term meta XML tags for a given term object.
	 *
	 * @param \WP_Term $term Term object.
	 *
	 * @return string
	 */
	private function wxr_term_meta( $term ) {
		$result = '';
		$termmeta = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->termmeta} WHERE term_id = %d", $term->term_id ) );// phpcs:ignore

		foreach ( $termmeta as $meta ) {
			/**
			 * Filters whether to selectively skip term meta used for WXR exports.
			 *
			 * Returning a truthy value from the filter will skip the current meta
			 * object from being exported.
			 *
			 * @since 4.6.0
			 *
			 * @param bool   $skip     Whether to skip the current piece of term meta. Default false.
			 * @param string $meta_key Current meta key.
			 * @param object $meta     Current meta object.
			 */
			if ( ! apply_filters( 'wxr_export_skip_termmeta', false, $meta->meta_key, $meta ) ) {
				$result .= sprintf( $this->indent( 3 ) . "<wp:termmeta>\n\t\t\t<wp:meta_key>%s</wp:meta_key>\n\t\t\t<wp:meta_value>%s</wp:meta_value>\n\t\t</wp:termmeta>\n", $this->wxr_cdata( $meta->meta_key ), $this->wxr_cdata( $meta->meta_value ) );
			}
		}

		return $result;
	}

	/**
	 * Return list of authors with posts.
	 *
	 * @param int[] $post_ids Optional. Array of post IDs to filter the query by.
	 *
	 * @return string
	 */
	private function wxr_authors_list( array $post_ids = null ) {
		$result = '';

		if ( ! empty( $post_ids ) ) {
			$post_ids = array_map( 'absint', $post_ids );
			$and = 'AND ID IN ( ' . implode( ', ', $post_ids ) . ')';
		} else {
			$and = '';
		}

		$authors = [];
		$results = $this->wpdb->get_results( "SELECT DISTINCT post_author FROM {$this->wpdb->posts} WHERE post_status != 'auto-draft' $and" );// phpcs:ignore
		foreach ( (array) $results as $r ) {
			$authors[] = get_userdata( $r->post_author );
		}

		$authors = array_filter( $authors );

		foreach ( $authors as $author ) {
			$result .= $this->indent( 2 ) . '<wp:author>' . PHP_EOL;

			$result .= $this->indent( 3 ) . '<wp:author_id>' . (int) $author->ID . '</wp:author_id>' . PHP_EOL;
			$result .= $this->indent( 3 ) . '<wp:author_login>' . $this->wxr_cdata( $author->user_login ) . '</wp:author_login>' . PHP_EOL;
			$result .= $this->indent( 3 ) . '<wp:author_email>' . $this->wxr_cdata( $author->user_email ) . '</wp:author_email>' . PHP_EOL;
			$result .= $this->indent( 3 ) . '<wp:author_display_name>' . $this->wxr_cdata( $author->display_name ) . '</wp:author_display_name>' . PHP_EOL;
			$result .= $this->indent( 3 ) . '<wp:author_first_name>' . $this->wxr_cdata( $author->first_name ) . '</wp:author_first_name>' . PHP_EOL;
			$result .= $this->indent( 3 ) . '<wp:author_last_name>' . $this->wxr_cdata( $author->last_name ) . '</wp:author_last_name>' . PHP_EOL;

			$result .= $this->indent( 2 ) . '</wp:author>' . PHP_EOL;
		}

		return $result;
	}

	/**
	 * Return list of categories.
	 *
	 * @param array $cats
	 *
	 * @return string
	 */
	private function wxr_categories_list( array $cats ) {
		$result = '';

		foreach ( $cats as $c ) {
			$result .= $this->indent( 2 ) . '<wp:category>' . PHP_EOL;

			$result .= $this->indent( 3 ) . '<wp:term_id>' . (int) $c->term_id . '</wp:term_id>' . PHP_EOL;
			$result .= $this->indent( 3 ) . '<wp:category_nicename>' . $this->wxr_cdata( $c->slug ) . '</wp:category_nicename>' . PHP_EOL;
			$result .= $this->indent( 3 ) . '<wp:category_parent>' . $this->wxr_cdata( $c->parent ? $cats[ $c->parent ]->slug : '' ) . '</wp:category_parent>' . PHP_EOL;
			$result .= $this->wxr_cat_name( $c ) .
				$this->wxr_category_description( $c ) .
				$this->wxr_term_meta( $c );

			$result .= $this->indent( 2 ) . '</wp:category>' . PHP_EOL;
		}

		return $result;
	}

	/**
	 * Return list of tags.
	 *
	 * @param array $tags
	 *
	 * @return string
	 */
	private function wxr_tags_list( array $tags ) {
		$result = '';

		foreach ( $tags as $t ) {
			$result .= $this->indent( 2 ) . '<wp:tag>' . PHP_EOL;

			$result .= $this->indent( 3 ) . '<wp:term_id>' . (int) $t->term_id . '</wp:term_id>' . PHP_EOL;
			$result .= $this->indent( 3 ) . '<wp:tag_slug>' . $this->wxr_cdata( $t->slug ) . '</wp:tag_slug>' . PHP_EOL;
			$result .= $this->wxr_tag_name( $t ) .
				$this->wxr_tag_description( $t ) .
				$this->wxr_term_meta( $t );

			$result .= $this->indent( 2 ) . '</wp:tag>' . PHP_EOL;
		}

		return $result;
	}

	/**
	 * Return list of terms.
	 *
	 * @param array $terms
	 *
	 * @return string
	 */
	private function wxr_terms_list( array $terms ) {
		$result = '';

		foreach ( $terms as $t ) {
			$result .= $this->indent( 2 ) . '<wp:term>' . PHP_EOL;

			$result .= $this->indent( 3 ) . '<wp:term_id>' . $this->wxr_cdata( $t->term_id ) . '</wp:term_id>' . PHP_EOL;
			$result .= $this->indent( 3 ) . '<wp:term_taxonomy>' . $this->wxr_cdata( $t->taxonomy ) . '</wp:term_taxonomy>' . PHP_EOL;
			$result .= $this->indent( 3 ) . '<wp:term_slug>' . $this->wxr_cdata( $t->slug ) . '</wp:term_slug>' . PHP_EOL;
			$result .= $this->indent( 3 ) . '<wp:term_parent>' . $this->wxr_cdata( $t->parent ? $terms[ $t->parent ]->slug : '' ) . '</wp:term_parent>' . PHP_EOL;
			$result .= $this->wxr_term_name( $t ) .
				$this->wxr_term_description( $t ) .
				$this->wxr_term_meta( $t );

			$result .= $this->indent( 2 ) . '</wp:term>' . PHP_EOL;
		}

		return $result;
	}

	/**
	 * Return list of posts, by requested `$post_ids`.
	 *
	 * @param array $post_ids
	 *
	 * @return string
	 */
	private function wxr_posts_list( array $post_ids ) {
		$result = '';

		if ( $post_ids ) {
			global $wp_query;

			// Fake being in the loop.
			$wp_query->in_the_loop = true;

			// Fetch 20 posts at a time rather than loading the entire table into memory.
			while ( $next_posts = array_splice( $post_ids, 0, 20 ) ) {
				$where = 'WHERE ID IN (' . implode( ',', $next_posts ) . ')';
				$posts = $this->wpdb->get_results( "SELECT * FROM {$this->wpdb->posts} $where" );// phpcs:ignore

				// Begin Loop.
				foreach ( $posts as $post ) {
					setup_postdata( $post );

					$this->exported_posts[ $post->ID ] = [
						'id' => $post->ID,
						'title' => $post->post_title,
					];

					$title = apply_filters( 'the_title_rss', $post->post_title );

					/**
					 * Filters the post content used for WXR exports.
					 *
					 * @since 2.5.0
					 *
					 * @param string $post_content Content of the current post.
					 */
					$content = $this->wxr_cdata( apply_filters( 'the_content_export', $post->post_content ) );

					/**
					 * Filters the post excerpt used for WXR exports.
					 *
					 * @since 2.6.0
					 *
					 * @param string $post_excerpt Excerpt for the current post.
					 */
					$excerpt = $this->wxr_cdata( apply_filters( 'the_excerpt_export', $post->post_excerpt ) );

					$result .= $this->indent( 2 ) . '<item>' . PHP_EOL;

					$result .= $this->indent( 3 ) . '<title>' . $title . '</title>' . PHP_EOL;
					$result .= $this->indent( 3 ) . '<link>' . esc_url( get_permalink() ) . '</link>' . PHP_EOL;
					$result .= $this->indent( 3 ) . '<pubDate>' . mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', true ), false ) . '</pubDate>' . PHP_EOL;
					$result .= $this->indent( 3 ) . '<dc:creator>' . $this->wxr_cdata( get_the_author_meta( 'login' ) ) . '</dc:creator>' . PHP_EOL;
					$result .= $this->indent( 3 ) . '<guid isPermaLink="false">' . $this->wxr_cdata( get_the_author_meta( 'login' ) ) . '</guid>' . PHP_EOL;
					$result .= $this->indent( 3 ) . '<description></description>' . PHP_EOL;
					$result .= $this->indent( 3 ) . '<content:encoded>' . $content . '</content:encoded>' . PHP_EOL;
					$result .= $this->indent( 3 ) . '<excerpt:encoded>' . $excerpt . '</excerpt:encoded>' . PHP_EOL;
					$result .= $this->indent( 3 ) . '<wp:post_id>' . (int) $post->ID . '</wp:post_id>' . PHP_EOL;
					$result .= $this->indent( 3 ) . '<wp:post_date>' . $this->wxr_cdata( $post->post_date ) . '</wp:post_date>' . PHP_EOL;
					$result .= $this->indent( 3 ) . '<wp:post_date_gmt>' . $this->wxr_cdata( $post->post_date_gmt ) . '</wp:post_date_gmt>' . PHP_EOL;
					$result .= $this->indent( 3 ) . '<wp:comment_status>' . $this->wxr_cdata( $post->comment_status ) . '</wp:comment_status>' . PHP_EOL;
					$result .= $this->indent( 3 ) . '<wp:ping_status>' . $this->wxr_cdata( $post->ping_status ) . '</wp:ping_status>' . PHP_EOL;
					$result .= $this->indent( 3 ) . '<wp:post_name>' . $this->wxr_cdata( $post->post_name ) . '</wp:post_name>' . PHP_EOL;
					$result .= $this->indent( 3 ) . '<wp:status>' . $this->wxr_cdata( $post->post_status ) . '</wp:status>' . PHP_EOL;
					$result .= $this->indent( 3 ) . '<wp:post_parent>' . $this->wxr_cdata( $post->post_parent ) . '</wp:post_parent>' . PHP_EOL;
					$result .= $this->indent( 3 ) . '<wp:menu_order>' . (int) $post->menu_order . '</wp:menu_order>' . PHP_EOL;
					$result .= $this->indent( 3 ) . '<wp:post_type>' . $this->wxr_cdata( $post->post_type ) . '</wp:post_type>' . PHP_EOL;
					$result .= $this->indent( 3 ) . '<wp:post_password>' . $this->wxr_cdata( $post->post_password ) . '</wp:post_password>' . PHP_EOL;
					$result .= $this->indent( 3 ) . '<wp:is_sticky>' . ( is_sticky( $post->ID ) ? 1 : 0 ) . '</wp:is_sticky>' . PHP_EOL;

					if ( 'attachment' === $post->post_type ) {
						$result .= $this->indent( 3 ) . '<wp:attachment_url>' . $this->wxr_cdata( wp_get_attachment_url( $post->ID ) ) . '</wp:attachment_url>' . PHP_EOL;
					}

					$result .= $this->wxr_post_taxonomy( $post );

					$postmeta = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->postmeta} WHERE post_id = %d", $post->ID ) );// phpcs:ignore
					foreach ( $postmeta as $meta ) {
						/**
						 * Filters whether to selectively skip post meta used for WXR exports.
						 *
						 * Returning a truthy value from the filter will skip the current meta
						 * object from being exported.
						 *
						 * @since 3.3.0
						 *
						 * @param bool   $skip     Whether to skip the current post meta. Default false.
						 * @param string $meta_key Current meta key.
						 * @param object $meta     Current meta object.
						 */
						if ( apply_filters( 'wxr_export_skip_postmeta', false, $meta->meta_key, $meta ) ) {
							continue;
						}

						$result .= $this->indent( 3 ) . '<wp:postmeta>' . PHP_EOL;

						$result .= $this->indent( 4 ) . '<wp:meta_key>' . $this->wxr_cdata( $meta->meta_key ) . '</wp:meta_key>' . PHP_EOL;
						$result .= $this->indent( 4 ) . '<wp:meta_value>' . $this->wxr_cdata( $meta->meta_value ) . '</wp:meta_value>' . PHP_EOL;

						$result .= $this->indent( 3 ) . '</wp:postmeta>' . PHP_EOL;
					}

					$_comments = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->comments} WHERE comment_post_ID = %d AND comment_approved <> 'spam'", $post->ID ) );// phpcs:ignore
					$comments  = array_map( 'get_comment', $_comments );
					foreach ( $comments as $c ) {

						$result .= $this->indent( 3 ) . '<wp:comment>' . PHP_EOL;

						$result .= $this->indent( 4 ) . '<wp:comment_id>' . (int) $c->comment_ID . '</wp:comment_id>' . PHP_EOL;
						$result .= $this->indent( 4 ) . '<wp:comment_author>' . $this->wxr_cdata( $c->comment_author ) . '</wp:comment_author>' . PHP_EOL;
						$result .= $this->indent( 4 ) . '<wp:comment_author_email>' . $this->wxr_cdata( $c->comment_author_email ) . '</wp:comment_author_email>' . PHP_EOL;
						$result .= $this->indent( 4 ) . '<wp:comment_author_url>' . $this->wxr_cdata( $c->comment_author_url ) . '</wp:comment_author_url>' . PHP_EOL;
						$result .= $this->indent( 4 ) . '<wp:comment_author_IP>' . $this->wxr_cdata( $c->comment_author_IP ) . '</wp:comment_author_IP>' . PHP_EOL;
						$result .= $this->indent( 4 ) . '<wp:comment_date>' . $this->wxr_cdata( $c->comment_date ) . '</wp:comment_date>' . PHP_EOL;
						$result .= $this->indent( 4 ) . '<wp:comment_date_gmt>' . $this->wxr_cdata( $c->comment_date_gmt ) . '</wp:comment_date_gmt>' . PHP_EOL;
						$result .= $this->indent( 4 ) . '<wp:comment_content>' . $this->wxr_cdata( $c->comment_content ) . '</wp:comment_content>' . PHP_EOL;
						$result .= $this->indent( 4 ) . '<wp:comment_approved>' . $this->wxr_cdata( $c->comment_approved ) . '</wp:comment_approved>' . PHP_EOL;
						$result .= $this->indent( 4 ) . '<wp:comment_type>' . $this->wxr_cdata( $c->comment_type ) . '</wp:comment_type>' . PHP_EOL;
						$result .= $this->indent( 4 ) . '<wp:comment_parent>' . $this->wxr_cdata( $c->comment_parent ) . '</wp:comment_parent>' . PHP_EOL;
						$result .= $this->indent( 4 ) . '<wp:comment_user_id>' . (int) $c->user_id . '</wp:comment_user_id>' . PHP_EOL;

						$c_meta = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->commentmeta} WHERE comment_id = %d", $c->comment_ID ) );// phpcs:ignore
						foreach ( $c_meta as $meta ) {
							/**
							 * Filters whether to selectively skip comment meta used for WXR exports.
							 *
							 * Returning a truthy value from the filter will skip the current meta
							 * object from being exported.
							 *
							 * @since 4.0.0
							 *
							 * @param bool   $skip     Whether to skip the current comment meta. Default false.
							 * @param string $meta_key Current meta key.
							 * @param object $meta     Current meta object.
							 */
							if ( apply_filters( 'wxr_export_skip_commentmeta', false, $meta->meta_key, $meta ) ) {
								continue;
							}

							$result .= $this->indent( 4 ) . '<wp:commentmeta>' . PHP_EOL;

							$result .= $this->indent( 5 ) . '<wp:meta_key>' . $this->wxr_cdata( $meta->meta_key ) . '</wp:meta_key>' . PHP_EOL;
							$result .= $this->indent( 5 ) . '<wp:meta_value>' . $this->wxr_cdata( $meta->meta_key ) . '</wp:meta_value>' . PHP_EOL;

							$result .= $this->indent( 4 ) . '</wp:commentmeta>' . PHP_EOL;
						}

						$result .= $this->indent( 3 ) . '</wp:comment>' . PHP_EOL;
					}

					$result .= $this->indent( 2 ) . '</item>' . PHP_EOL;
				}
			}
		}

		return $result;
	}

	/**
	 * Return all navigation menu terms
	 *
	 * @return string
	 */
	private function wxr_nav_menu_terms() {
		$nav_menus = wp_get_nav_menus();
		if ( empty( $nav_menus ) || ! is_array( $nav_menus ) ) {
			return '';
		}

		$result = '';

		foreach ( $nav_menus as $menu ) {
			$this->terms[ $menu->term_id ] = $menu;

			$result .= $this->indent( 2 ) . '<wp:term>' . PHP_EOL;
			$result .= $this->indent( 3 ) . '<wp:term_id>' . (int) $menu->term_id . '</wp:term_id>' . PHP_EOL;
			$result .= $this->indent( 3 ) . '<wp:term_taxonomy>nav_menu</wp:term_taxonomy>' . PHP_EOL;
			$result .= $this->indent( 3 ) . '<wp:term_slug>' . $this->wxr_cdata( $menu->slug ) . '</wp:term_slug>' . PHP_EOL;
			$result .= $this->indent( 3 ) . '<wp:term_name>' . $this->wxr_cdata( $menu->name ) . '</wp:term_name>' . PHP_EOL;
			$result .= $this->indent( 2 ) . '</wp:term>' . PHP_EOL;
		}

		return $result;
	}

	/**
	 * Return list of taxonomy terms, in XML tag format, associated with a post
	 *
	 * @param \WP_Post $post
	 *
	 * @return string
	 */
	private function wxr_post_taxonomy( $post ) {
		$result = '';

		$taxonomies = get_object_taxonomies( $post->post_type );

		if ( empty( $taxonomies ) ) {
			return $result;
		}

		$terms = wp_get_object_terms( $post->ID, $taxonomies );

		foreach ( (array) $terms as $term ) {
			$result .= $this->indent( 3 ) . "<category domain=\"{$term->taxonomy}\" nicename=\"{$term->slug}\">" . $this->wxr_cdata( $term->name ) . '</category>' . PHP_EOL;
		}

		return $result;
	}

	/**
	 * Get's the XML export.
	 *
	 * @param $post_ids
	 *
	 * @return string
	 */
	private function get_xml_export( array $post_ids ) {
		$charset = get_bloginfo( 'charset' );
		$generator = get_the_generator( 'export' );
		$wxr_version = self::WXR_VERSION;
		$wxr_site_url = $this->wxr_site_url();
		$rss_info_name = get_bloginfo_rss( 'name' );
		$rss_info_url = get_bloginfo_rss( 'url' );
		$rss_info_description = get_bloginfo_rss( 'description' );
		$rss_info_language = get_bloginfo_rss( 'language' );
		$pub_date = gmdate( 'D, d M Y H:i:s +0000' );

		$show_page_on_front = 'page' === get_option( 'show_on_front' );

		$page_on_front_xml = '';

		if ( $show_page_on_front ) {
			$page_on_front_id = (int) get_option( 'page_on_front' );

			if ( in_array( $page_on_front_id, $post_ids ) ) {
				$page_on_front_xml = "<wp:page_on_front>$page_on_front_id</wp:page_on_front>";
			}
		}

		$dynamic = $this->wxr_authors_list( $post_ids );

		ob_start();
		/** This action is documented in wp-includes/feed-rss2.php */
		do_action( 'rss2_head' );
		$rss2_head = ob_get_clean();

		$dynamic .= $rss2_head;

		if ( 'all' === $this->args['content'] || 'nav_menu_item' === $this->args['content'] ) {
			$dynamic .= $this->wxr_nav_menu_terms();
		}

		$dynamic .= $this->wxr_posts_list( $post_ids );

		$result = <<<EOT
<?xml version="1.0" encoding="$charset" ?>
<!-- This is a WordPress eXtended RSS file generated by WordPress as an export of your site. -->
<!-- It contains information about your site's posts, pages, comments, categories, and other content. -->
<!-- You may use this file to transfer that content from one site to another. -->
<!-- This file is not intended to serve as a complete backup of your site. -->

<!-- To import this information into a WordPress site follow these steps: -->
<!-- 1. Log in to that site as an administrator. -->
<!-- 2. Go to Tools: Import in the WordPress admin panel. -->
<!-- 3. Install the "WordPress" importer from the list. -->
<!-- 4. Activate & Run Importer. -->
<!-- 5. Upload this file using the form provided on that page. -->
<!-- 6. You will first be asked to map the authors in this export file to users -->
<!--    on the site. For each author, you may choose to map to an -->
<!--    existing user on the site or to create a new user. -->
<!-- 7. WordPress will then import each of the posts, pages, comments, categories, etc. -->
<!--    contained in this file into your site. -->
$generator
<rss version="2.0"
	xmlns:excerpt="http://wordpress.org/export/$wxr_version/excerpt/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:wp="http://wordpress.org/export/$wxr_version/"
>
	<channel>
		<title>$rss_info_name</title>
		<link>$rss_info_url</link>
		<description>$rss_info_description</description>
		<pubDate>$pub_date</pubDate>
		<language>$rss_info_language</language>
		<wp:wxr_version>$wxr_version</wp:wxr_version>
		<wp:base_site_url>$wxr_site_url</wp:base_site_url>
		<wp:base_blog_url>$rss_info_url</wp:base_blog_url>
		$page_on_front_xml
		$dynamic
	</channel>
</rss>
EOT;
		return $result;
	}

	public function __construct( array $args = [] ) {
		global $wpdb;

		$this->args = wp_parse_args( $args, self::$default_args );

		$this->wpdb = $wpdb;
	}
}
