<?php
namespace Elementor\Core\Utils\ImportExport;

use Elementor\Core\Utils\ImportExport\Parsers\WXR_Parser;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Originally made by WordPress part of WordPress/Importer.
 * https://plugins.trac.wordpress.org/browser/wordpress-importer/trunk/class-wp-import.php
 *
 * What was done:
 * Reformat of the code.
 * Changed text domain.
 * Changed methods visibility.
 * Changed method from `get_authors_from_import` to `set_authors_from_import`.
 * Changed method from `get_author_mapping` to `set_author_mapping`.
 * Removed use of '$_POST' the input 'options' will be passed via constructor args.
 * Removed echos, UI and print methods, all echos replaced with `$this->output` append.
 * Removed `die` ( exit(s) ).
 */

if ( ! class_exists( 'WP_Importer' ) ) {
	$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';

	if ( file_exists( $class_wp_importer ) ) {
		require $class_wp_importer;
	}
}

if ( ! function_exists( 'wp_import_cleanup' ) ) {
	require_once ABSPATH . 'wp-admin/includes/import.php';
}

class WP_Import extends \WP_Importer {
	const DEFAULT_BUMP_REQUEST_TIMEOUT = 60;
	const DEFAULT_ALLOW_CREATE_USERS = true;
	const DEFAULT_IMPORT_ATTACHMENT_SIZE_LIMIT = 0; // 0 = unlimited.

	/**
	 * @var string
	 */
	private $requested_file_path;

	/**
	 * @var array
	 */
	private $args;

	/**
	 * @var array
	 */
	private $output = [
		'status' => 'failed',
		'errors' => [],
	];

	/**
	 * WXR attachment ID
	 *
	 * @var string
	 */
	private $id;

	/**
	 * Information to import from WXR file.
	 */
	// phpcs:ignore Squiz.Commenting.VariableComment.MissingVar
	private $version;
	private $authors = [];
	private $posts = [];
	private $terms = [];
	private $base_url = '';
	private $page_on_front;
	private $base_blog_url = '';

	/**
	 * Mappings from old information to new.
	 */
	// phpcs:ignore Squiz.Commenting.VariableComment.MissingVar
	private $processed_taxonomies;
	private $processed_terms = [];
	private $processed_posts = [];
	private $processed_authors = [];
	private $author_mapping = [];
	private $processed_menu_items = [];
	private $post_orphans = [];
	private $menu_item_orphans = [];
	private $mapped_terms_slug = [];

	private $fetch_attachments = false;
	private $url_remap = [];
	private $featured_images = [];

	/**
	 * @var array[] [meta_key => meta_value] Meta value that should be set for every imported post.
	 */
	private $posts_meta = [];

	/**
	 * @var array[] [meta_key => meta_value] Meta value that should be set for every imported term.
	 */
	private $terms_meta = [];

	/**
	 * Parses filename from a Content-Disposition header value.
	 *
	 * As per RFC6266:
	 *
	 *     content-disposition = "Content-Disposition" ":"
	 *                            disposition-type *( ";" disposition-parm )
	 *
	 *     disposition-type    = "inline" | "attachment" | disp-ext-type
	 *                         ; case-insensitive
	 *     disp-ext-type       = token
	 *
	 *     disposition-parm    = filename-parm | disp-ext-parm
	 *
	 *     filename-parm       = "filename" "=" value
	 *                         | "filename*" "=" ext-value
	 *
	 *     disp-ext-parm       = token "=" value
	 *                         | ext-token "=" ext-value
	 *     ext-token           = <the characters in token, followed by "*">
	 *
	 * @param string[] $disposition_header List of Content-Disposition header values.
	 *
	 * @return string|null Filename if available, or null if not found.
	 * @link  http://tools.ietf.org/html/rfc2388
	 * @link  http://tools.ietf.org/html/rfc6266
	 *
	 * @see WP_REST_Attachments_Controller::get_filename_from_disposition()
	 */
	protected static function get_filename_from_disposition( $disposition_header ) {
		// Get the filename.
		$filename = null;

		foreach ( $disposition_header as $value ) {
			$value = trim( $value );

			if ( strpos( $value, ';' ) === false ) {
				continue;
			}

			list( $type, $attr_parts ) = explode( ';', $value, 2 );

			$attr_parts = explode( ';', $attr_parts );
			$attributes = [];

			foreach ( $attr_parts as $part ) {
				if ( strpos( $part, '=' ) === false ) {
					continue;
				}

				list( $key, $value ) = explode( '=', $part, 2 );

				$attributes[ trim( $key ) ] = trim( $value );
			}

			if ( empty( $attributes['filename'] ) ) {
				continue;
			}

			$filename = trim( $attributes['filename'] );

			// Unquote quoted filename, but after trimming.
			if ( substr( $filename, 0, 1 ) === '"' && substr( $filename, -1, 1 ) === '"' ) {
				$filename = substr( $filename, 1, -1 );
			}
		}

		return $filename;
	}

	/**
	 * Retrieves file extension by mime type.
	 *
	 * @param string $mime_type Mime type to search extension for.
	 *
	 * @return string|null File extension if available, or null if not found.
	 */
	protected static function get_file_extension_by_mime_type( $mime_type ) {
		static $map = null;

		if ( is_array( $map ) ) {
			return isset( $map[ $mime_type ] ) ? $map[ $mime_type ] : null;
		}

		$mime_types = wp_get_mime_types();
		$map = array_flip( $mime_types );

		// Some types have multiple extensions, use only the first one.
		foreach ( $map as $type => $extensions ) {
			$map[ $type ] = strtok( $extensions, '|' );
		}

		return isset( $map[ $mime_type ] ) ? $map[ $mime_type ] : null;
	}

	/**
	 * The main controller for the actual import stage.
	 *
	 * @param string $file Path to the WXR file for importing
	 */
	private function import( $file ) {
		add_filter( 'import_post_meta_key', function ( $key ) {
			return $this->is_valid_meta_key( $key );
		} );
		add_filter( 'http_request_timeout', function () {
			return self::DEFAULT_BUMP_REQUEST_TIMEOUT;
		} );

		if ( ! $this->import_start( $file ) ) {
			return;
		}

		$this->set_author_mapping();

		wp_suspend_cache_invalidation( true );
		$imported_summary = [
			'terms' => $this->process_terms(),
			'posts' => $this->process_posts(),
		];
		wp_suspend_cache_invalidation( false );

		// Update incorrect/missing information in the DB.
		$this->backfill_parents();
		$this->backfill_attachment_urls();
		$this->remap_featured_images();

		$this->import_end();

		$is_some_succeed = false;
		foreach ( $imported_summary as $item ) {
			if ( $item > 0 ) {
				$is_some_succeed = true;
				break;
			}
		}

		if ( $is_some_succeed ) {
			$this->output['status'] = 'success';
			$this->output['summary'] = $imported_summary;
		}
	}

	/**
	 * Parses the WXR file and prepares us for the task of processing parsed data.
	 *
	 * @param string $file Path to the WXR file for importing
	 */
	private function import_start( $file ) {
		if ( ! is_file( $file ) ) {
			$this->output['errors'] = [ esc_html__( 'The file does not exist, please try again.', 'elementor' ) ];

			return false;
		}

		$import_data = $this->parse( $file );

		if ( is_wp_error( $import_data ) ) {
			$this->output['errors'] = [ $import_data->get_error_message() ];

			return false;
		}

		$this->version = $import_data['version'];
		$this->set_authors_from_import( $import_data );
		$this->posts = $this->filter_import_posts( $import_data['posts'] );
		$this->terms = $import_data['terms'];
		$this->base_url = esc_url( $import_data['base_url'] );
		$this->base_blog_url = esc_url( $import_data['base_blog_url'] );
		$this->page_on_front = $import_data['page_on_front'];

		wp_defer_term_counting( true );
		wp_defer_comment_counting( true );

		do_action( 'import_start' );

		return true;
	}

	private function filter_import_posts( array $posts ): array {
		if ( isset( $this->args['include'] ) ) {
			$filtered_posts = [];

			foreach ( $posts as $post ) {
				if ( in_array( $post['post_id'], $this->args['include'] ) ) {
					$filtered_posts[] = $post;
				}
			}

			return $filtered_posts;
		}

		return $posts;
	}

	/**
	 * Performs post-import cleanup of files and the cache
	 */
	private function import_end() {
		wp_import_cleanup( $this->id );

		wp_cache_flush();

		foreach ( get_taxonomies() as $tax ) {
			delete_option( "{$tax}_children" );
			_get_term_hierarchy( $tax );
		}

		wp_defer_term_counting( false );
		wp_defer_comment_counting( false );

		do_action( 'import_end' );
	}

	/**
	 * Retrieve authors from parsed WXR data and set it to `$this->>authors`.
	 *
	 * Uses the provided author information from WXR 1.1 files
	 * or extracts info from each post for WXR 1.0 files
	 *
	 * @param array $import_data Data returned by a WXR parser
	 */
	private function set_authors_from_import( $import_data ) {
		if ( ! empty( $import_data['authors'] ) ) {
			$this->authors = $import_data['authors'];
			// No author information, grab it from the posts.
		} else {
			foreach ( $import_data['posts'] as $post ) {
				$login = sanitize_user( $post['post_author'], true );

				if ( empty( $login ) ) {
					$this->output['errors'][] = sprintf(
						/* translators: %s: Post author. */
						esc_html__( 'Failed to import author %s. Their posts will be attributed to the current user.', 'elementor' ),
						$post['post_author']
					);
					continue;
				}

				if ( ! isset( $this->authors[ $login ] ) ) {
					$this->authors[ $login ] = [
						'author_login' => $login,
						'author_display_name' => $post['post_author'],
					];
				}
			}
		}
	}

	/**
	 * Map old author logins to local user IDs based on decisions made
	 * in import options form. Can map to an existing user, create a new user
	 * or falls back to the current user in case of error with either of the previous
	 */
	private function set_author_mapping() {
		if ( ! isset( $this->args['imported_authors'] ) ) {
			return;
		}

		$create_users = apply_filters( 'import_allow_create_users', self::DEFAULT_ALLOW_CREATE_USERS );

		foreach ( (array) $this->args['imported_authors'] as $i => $old_login ) {
			// Multisite adds strtolower to sanitize_user. Need to sanitize here to stop breakage in process_posts.
			$sanitized_old_login = sanitize_user( $old_login, true );
			$old_id = isset( $this->authors[ $old_login ]['author_id'] ) ? (int) $this->authors[ $old_login ]['author_id'] : false;

			if ( ! empty( $this->args['user_map'][ $i ] ) ) {
				$user = get_userdata( (int) $this->args['user_map'][ $i ] );
				if ( isset( $user->ID ) ) {
					if ( $old_id ) {
						$this->processed_authors[ $old_id ] = $user->ID;
					}
					$this->author_mapping[ $sanitized_old_login ] = $user->ID;
				}
			} elseif ( $create_users ) {
				$user_id = 0;
				if ( ! empty( $this->args['user_new'][ $i ] ) ) {
					$user_id = wp_create_user( $this->args['user_new'][ $i ], wp_generate_password() );
				} elseif ( '1.0' !== $this->version ) {
					$user_data = [
						'user_login' => $old_login,
						'user_pass' => wp_generate_password(),
						'user_email' => isset( $this->authors[ $old_login ]['author_email'] ) ? $this->authors[ $old_login ]['author_email'] : '',
						'display_name' => $this->authors[ $old_login ]['author_display_name'],
						'first_name' => isset( $this->authors[ $old_login ]['author_first_name'] ) ? $this->authors[ $old_login ]['author_first_name'] : '',
						'last_name' => isset( $this->authors[ $old_login ]['author_last_name'] ) ? $this->authors[ $old_login ]['author_last_name'] : '',
					];
					$user_id = wp_insert_user( $user_data );
				}

				if ( ! is_wp_error( $user_id ) ) {
					if ( $old_id ) {
						$this->processed_authors[ $old_id ] = $user_id;
					}
					$this->author_mapping[ $sanitized_old_login ] = $user_id;
				} else {
					$error = sprintf(
						/* translators: %s: Author display name. */
						esc_html__( 'Failed to create new user for %s. Their posts will be attributed to the current user.', 'elementor' ),
						$this->authors[ $old_login ]['author_display_name']
					);

					if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
						$error .= PHP_EOL . $user_id->get_error_message();
					}

					$this->output['errors'][] = $error;
				}
			}

			// Failsafe: if the user_id was invalid, default to the current user.
			if ( ! isset( $this->author_mapping[ $sanitized_old_login ] ) ) {
				if ( $old_id ) {
					$this->processed_authors[ $old_id ] = (int) get_current_user_id();
				}
				$this->author_mapping[ $sanitized_old_login ] = (int) get_current_user_id();
			}
		}
	}

	/**
	 * Create new terms based on import information
	 *
	 * Doesn't create a term its slug already exists
	 *
	 * @return array|array[] the ids of succeed/failed imported terms.
	 */
	private function process_terms() {
		$result = [
			'succeed' => [],
			'failed' => [],
		];

		$this->terms = apply_filters( 'wp_import_terms', $this->terms );

		if ( empty( $this->terms ) ) {
			return $result;
		}

		foreach ( $this->terms as $term ) {

			// if the term already exists in the correct taxonomy leave it alone
			$term_id = term_exists( $term['slug'], $term['term_taxonomy'] );
			if ( $term_id ) {
				if ( is_array( $term_id ) ) {
					$term_id = $term_id['term_id'];
				}

				if ( isset( $term['term_id'] ) ) {
					if ( 'nav_menu' === $term['term_taxonomy'] ) {
						// BC - support old kits that the menu terms are part of the 'nav_menu_item' post type
						// and not part of the taxonomies.
						if ( ! empty( $this->processed_taxonomies[ $term['term_taxonomy'] ] ) ) {
							foreach ( $this->processed_taxonomies[ $term['term_taxonomy'] ] as $processed_term ) {
								$old_slug   = $processed_term['old_slug'];
								$new_slug = $processed_term['new_slug'];

								$this->mapped_terms_slug[ $old_slug ] = $new_slug;
								$result['succeed'][ $old_slug ] = $new_slug;
							}
							continue;
						} else {
							$term = $this->handle_duplicated_nav_menu_term( $term );
						}
					} else {
						$this->processed_terms[ (int) $term['term_id'] ] = (int) $term_id;
						$result['succeed'][ (int) $term['term_id'] ] = (int) $term_id;
						continue;
					}
				}
			}

			if ( empty( $term['term_parent'] ) ) {
				$parent = 0;
			} else {
				$parent = term_exists( $term['term_parent'], $term['term_taxonomy'] );
				if ( is_array( $parent ) ) {
					$parent = $parent['term_id'];
				}
			}

			$description = isset( $term['term_description'] ) ? $term['term_description'] : '';
			$args = [
				'slug' => $term['slug'],
				'description' => wp_slash( $description ),
				'parent' => (int) $parent,
			];

			$id = wp_insert_term( wp_slash( $term['term_name'] ), $term['term_taxonomy'], $args );
			if ( ! is_wp_error( $id ) ) {
				if ( isset( $term['term_id'] ) ) {
					$this->processed_terms[ (int) $term['term_id'] ] = $id['term_id'];
					$result['succeed'][ (int) $term['term_id'] ] = $id['term_id'];

					$this->update_term_meta( $id['term_id'] );
				}
			} else {
				/* translators: 1: Term taxonomy, 2: Term name. */
				$error = sprintf( esc_html__( 'Failed to import %1$s %2$s', 'elementor' ), $term['term_taxonomy'], $term['term_name'] );

				if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
					$error .= PHP_EOL . $id->get_error_message();
				}

				$result['failed'][] = $id;
				$this->output['errors'][] = $error;
				continue;
			}

			$this->process_termmeta( $term, $id['term_id'] );
		}

		unset( $this->terms );

		return $result;
	}

	/**
	 * Add metadata to imported term.
	 *
	 * @param array $term    Term data from WXR import.
	 * @param int   $term_id ID of the newly created term.
	 */
	private function process_termmeta( $term, $term_id ) {
		if ( ! function_exists( 'add_term_meta' ) ) {
			return;
		}

		if ( ! isset( $term['termmeta'] ) ) {
			$term['termmeta'] = [];
		}

		/**
		 * Filters the metadata attached to an imported term.
		 *
		 * @param array $termmeta Array of term meta.
		 * @param int   $term_id  ID of the newly created term.
		 * @param array $term     Term data from the WXR import.
		 */
		$term['termmeta'] = apply_filters( 'wp_import_term_meta', $term['termmeta'], $term_id, $term );

		if ( empty( $term['termmeta'] ) ) {
			return;
		}

		foreach ( $term['termmeta'] as $meta ) {
			/**
			 * Filters the meta key for an imported piece of term meta.
			 *
			 * @param string $meta_key Meta key.
			 * @param int    $term_id  ID of the newly created term.
			 * @param array  $term     Term data from the WXR import.
			 */
			$key = apply_filters( 'import_term_meta_key', $meta['key'], $term_id, $term );
			if ( ! $key ) {
				continue;
			}

			// Export gets meta straight from the DB so could have a serialized string
			$value = maybe_unserialize( $meta['value'] );

			add_term_meta( $term_id, wp_slash( $key ), wp_slash_strings_only( $value ) );

			/**
			 * Fires after term meta is imported.
			 *
			 * @param int    $term_id ID of the newly created term.
			 * @param string $key     Meta key.
			 * @param mixed  $value   Meta value.
			 */
			do_action( 'import_term_meta', $term_id, $key, $value );
		}
	}

	/**
	 * Create new posts based on import information
	 *
	 * Posts marked as having a parent which doesn't exist will become top level items.
	 * Doesn't create a new post if: the post type doesn't exist, the given post ID
	 * is already noted as imported or a post with the same title and date already exists.
	 * Note that new/updated terms, comments and meta are imported for the last of the above.
	 *
	 * @return array the ids of succeed/failed imported posts.
	 */
	private function process_posts() {
		$result = [
			'succeed' => [],
			'failed' => [],
		];

		$this->posts = apply_filters( 'wp_import_posts', $this->posts );

		foreach ( $this->posts as $post ) {
			$post = apply_filters( 'wp_import_post_data_raw', $post );

			if ( ! post_type_exists( $post['post_type'] ) ) {
				/* translators: 1: Post title, 2: Post type. */
				$this->output['errors'][] = sprintf( esc_html__( 'Failed to import %1$s: Invalid post type %2$s', 'elementor' ), $post['post_title'], $post['post_type'] );
				do_action( 'wp_import_post_exists', $post );
				continue;
			}

			if ( isset( $this->processed_posts[ $post['post_id'] ] ) && ! empty( $post['post_id'] ) ) {
				continue;
			}

			if ( 'auto-draft' === $post['status'] ) {
				continue;
			}

			if ( 'nav_menu_item' === $post['post_type'] ) {
				$result['succeed'] += $this->process_menu_item( $post );

				continue;
			}

			$post_type_object = get_post_type_object( $post['post_type'] );

			$post_parent = (int) $post['post_parent'];
			if ( $post_parent ) {
				// if we already know the parent, map it to the new local ID.
				if ( isset( $this->processed_posts[ $post_parent ] ) ) {
					$post_parent = $this->processed_posts[ $post_parent ];
					// otherwise record the parent for later.
				} else {
					$this->post_orphans[ (int) $post['post_id'] ] = $post_parent;
					$post_parent = 0;
				}
			}

			// Map the post author.
			$author = sanitize_user( $post['post_author'], true );
			if ( isset( $this->author_mapping[ $author ] ) ) {
				$author = $this->author_mapping[ $author ];
			} else {
				$author = (int) get_current_user_id();
			}

			$postdata = [
				'post_author' => $author,
				'post_content' => $post['post_content'],
				'post_excerpt' => $post['post_excerpt'],
				'post_title' => $post['post_title'],
				'post_status' => $post['status'],
				'post_name' => $post['post_name'],
				'comment_status' => $post['comment_status'],
				'ping_status' => $post['ping_status'],
				'guid' => $post['guid'],
				'post_parent' => $post_parent,
				'menu_order' => $post['menu_order'],
				'post_type' => $post['post_type'],
				'post_password' => $post['post_password'],
			];

			$original_post_id = $post['post_id'];
			$postdata = apply_filters( 'wp_import_post_data_processed', $postdata, $post );

			$postdata = wp_slash( $postdata );

			if ( 'attachment' === $postdata['post_type'] ) {
				$remote_url = ! empty( $post['attachment_url'] ) ? $post['attachment_url'] : $post['guid'];

				// try to use _wp_attached file for upload folder placement to ensure the same location as the export site
				// e.g. location is 2003/05/image.jpg but the attachment post_date is 2010/09, see media_handle_upload()
				$postdata['upload_date'] = $post['post_date'];
				if ( isset( $post['postmeta'] ) ) {
					foreach ( $post['postmeta'] as $meta ) {
						if ( '_wp_attached_file' === $meta['key'] ) {
							if ( preg_match( '%^[0-9]{4}/[0-9]{2}%', $meta['value'], $matches ) ) {
								$postdata['upload_date'] = $matches[0];
							}
							break;
						}
					}
				}

				$post_id = $this->process_attachment( $postdata, $remote_url );
				$comment_post_id = $post_id;
			} else {
				$post_id = wp_insert_post( $postdata, true );

				$this->update_post_meta( $post_id );

				$comment_post_id = $post_id;
				do_action( 'wp_import_insert_post', $post_id, $original_post_id, $postdata, $post );
			}

			if ( is_wp_error( $post_id ) ) {
				/* translators: 1: Post type singular label, 2: Post title. */
				$error = sprintf( __( 'Failed to import %1$s %2$s', 'elementor' ), $post_type_object->labels->singular_name, $post['post_title'] );

				if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
					$error .= PHP_EOL . $post_id->get_error_message();
				}

				$result['failed'][] = $original_post_id;

				$this->output['errors'][] = $error;

				continue;
			}

			$result['succeed'][ $original_post_id ] = $post_id;

			if ( 1 === $post['is_sticky'] ) {
				stick_post( $post_id );
			}

			if ( $this->page_on_front === $original_post_id ) {
				update_option( 'page_on_front', $post_id );
			}

			// Map pre-import ID to local ID.
			$this->processed_posts[ (int) $post['post_id'] ] = (int) $post_id;

			if ( ! isset( $post['terms'] ) ) {
				$post['terms'] = [];
			}

			$post['terms'] = apply_filters( 'wp_import_post_terms', $post['terms'], $post_id, $post );

			// add categories, tags and other terms
			if ( ! empty( $post['terms'] ) ) {
				$terms_to_set = [];
				foreach ( $post['terms'] as $term ) {
					// back compat with WXR 1.0 map 'tag' to 'post_tag'
					$taxonomy = ( 'tag' === $term['domain'] ) ? 'post_tag' : $term['domain'];
					$term_exists = term_exists( $term['slug'], $taxonomy );
					$term_id = is_array( $term_exists ) ? $term_exists['term_id'] : $term_exists;
					if ( ! $term_id ) {
						$t = wp_insert_term( $term['name'], $taxonomy, [ 'slug' => $term['slug'] ] );
						if ( ! is_wp_error( $t ) ) {
							$term_id = $t['term_id'];

							$this->update_term_meta( $term_id );

							do_action( 'wp_import_insert_term', $t, $term, $post_id, $post );
						} else {
							/* translators: 1: Taxonomy name, 2: Term name. */
							$error = sprintf( esc_html__( 'Failed to import %1$s %2$s', 'elementor' ), $taxonomy, $term['name'] );

							if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
								$error .= PHP_EOL . $t->get_error_message();
							}

							$this->output['errors'][] = $error;

							do_action( 'wp_import_insert_term_failed', $t, $term, $post_id, $post );
							continue;
						}
					}
					$terms_to_set[ $taxonomy ][] = (int) $term_id;
				}

				foreach ( $terms_to_set as $tax => $ids ) {
					$tt_ids = wp_set_post_terms( $post_id, $ids, $tax );
					do_action( 'wp_import_set_post_terms', $tt_ids, $ids, $tax, $post_id, $post );
				}
				unset( $post['terms'], $terms_to_set );
			}

			if ( ! isset( $post['comments'] ) ) {
				$post['comments'] = [];
			}

			$post['comments'] = apply_filters( 'wp_import_post_comments', $post['comments'], $post_id, $post );

			// Add/update comments.
			if ( ! empty( $post['comments'] ) ) {
				$num_comments = 0;
				$inserted_comments = [];
				foreach ( $post['comments'] as $comment ) {
					$comment_id = $comment['comment_id'];
					$newcomments[ $comment_id ]['comment_post_ID'] = $comment_post_id;
					$newcomments[ $comment_id ]['comment_author'] = $comment['comment_author'];
					$newcomments[ $comment_id ]['comment_author_email'] = $comment['comment_author_email'];
					$newcomments[ $comment_id ]['comment_author_IP'] = $comment['comment_author_IP'];
					$newcomments[ $comment_id ]['comment_author_url'] = $comment['comment_author_url'];
					$newcomments[ $comment_id ]['comment_date'] = $comment['comment_date'];
					$newcomments[ $comment_id ]['comment_date_gmt'] = $comment['comment_date_gmt'];
					$newcomments[ $comment_id ]['comment_content'] = $comment['comment_content'];
					$newcomments[ $comment_id ]['comment_approved'] = $comment['comment_approved'];
					$newcomments[ $comment_id ]['comment_type'] = $comment['comment_type'];
					$newcomments[ $comment_id ]['comment_parent'] = $comment['comment_parent'];
					$newcomments[ $comment_id ]['commentmeta'] = isset( $comment['commentmeta'] ) ? $comment['commentmeta'] : [];
					if ( isset( $this->processed_authors[ $comment['comment_user_id'] ] ) ) {
						$newcomments[ $comment_id ]['user_id'] = $this->processed_authors[ $comment['comment_user_id'] ];
					}
				}

				ksort( $newcomments );

				foreach ( $newcomments as $key => $comment ) {
					if ( isset( $inserted_comments[ $comment['comment_parent'] ] ) ) {
						$comment['comment_parent'] = $inserted_comments[ $comment['comment_parent'] ];
					}

					$comment_data = wp_slash( $comment );
					unset( $comment_data['commentmeta'] ); // Handled separately, wp_insert_comment() also expects `comment_meta`.
					$comment_data = wp_filter_comment( $comment_data );

					$inserted_comments[ $key ] = wp_insert_comment( $comment_data );

					do_action( 'wp_import_insert_comment', $inserted_comments[ $key ], $comment, $comment_post_id, $post );

					foreach ( $comment['commentmeta'] as $meta ) {
						$value = maybe_unserialize( $meta['value'] );

						add_comment_meta( $inserted_comments[ $key ], wp_slash( $meta['key'] ), wp_slash_strings_only( $value ) );
					}

					$num_comments++;
				}
				unset( $newcomments, $inserted_comments, $post['comments'] );
			}

			if ( ! isset( $post['postmeta'] ) ) {
				$post['postmeta'] = [];
			}

			$post['postmeta'] = apply_filters( 'wp_import_post_meta', $post['postmeta'], $post_id, $post );

			// Add/update post meta.
			if ( ! empty( $post['postmeta'] ) ) {
				foreach ( $post['postmeta'] as $meta ) {
					$key = apply_filters( 'import_post_meta_key', $meta['key'], $post_id, $post );
					$value = false;

					if ( '_edit_last' === $key ) {
						if ( isset( $this->processed_authors[ (int) $meta['value'] ] ) ) {
							$value = $this->processed_authors[ (int) $meta['value'] ];
						} else {
							$key = false;
						}
					}

					if ( $key ) {
						// Export gets meta straight from the DB so could have a serialized string.
						if ( ! $value ) {
							$value = maybe_unserialize( $meta['value'] );
						}

						add_post_meta( $post_id, wp_slash( $key ), wp_slash_strings_only( $value ) );

						do_action( 'import_post_meta', $post_id, $key, $value );

						// If the post has a featured image, take note of this in case of remap.
						if ( '_thumbnail_id' === $key ) {
							$this->featured_images[ $post_id ] = (int) $value;
						}
					}
				}
			}
		}

		unset( $this->posts );

		return $result;
	}

	/**
	 * Attempt to create a new menu item from import data
	 *
	 * Fails for draft, orphaned menu items and those without an associated nav_menu
	 * or an invalid nav_menu term. If the post type or term object which the menu item
	 * represents doesn't exist then the menu item will not be imported (waits until the
	 * end of the import to retry again before discarding).
	 *
	 * @param array $item Menu item details from WXR file
	 */
	private function process_menu_item( $item ) {
		$result = [];

		// Skip draft, orphaned menu items.
		if ( 'draft' === $item['status'] ) {
			return;
		}

		$menu_slug = false;
		if ( isset( $item['terms'] ) ) {
			// Loop through terms, assume first nav_menu term is correct menu.
			foreach ( $item['terms'] as $term ) {
				if ( 'nav_menu' === $term['domain'] ) {
					$menu_slug = $term['slug'];
					break;
				}
			}
		}

		// No nav_menu term associated with this menu item.
		if ( ! $menu_slug ) {
			$this->output['errors'][] = esc_html__( 'Menu item skipped due to missing menu slug', 'elementor' );

			return $result;
		}

		// If menu was already exists, refer the items to the duplicated menu created.
		if ( array_key_exists( $menu_slug, $this->mapped_terms_slug ) ) {
			$menu_slug = $this->mapped_terms_slug[ $menu_slug ];
		}

		$menu_id = term_exists( $menu_slug, 'nav_menu' );
		if ( ! $menu_id ) {
			/* translators: %s: Menu slug. */
			$this->output['errors'][] = sprintf( esc_html__( 'Menu item skipped due to invalid menu slug: %s', 'elementor' ), $menu_slug );

			return $result;
		} else {
			$menu_id = is_array( $menu_id ) ? $menu_id['term_id'] : $menu_id;
		}

		$post_meta_key_value = [];
		foreach ( $item['postmeta'] as $meta ) {
			$post_meta_key_value[ $meta['key'] ] = $meta['value'];
		}

		$_menu_item_type = $post_meta_key_value['_menu_item_type'];
		$_menu_item_url = $post_meta_key_value['_menu_item_url'];

		// Skip menu items 'taxonomy' type, when the taxonomy is not exits.
		if ( 'taxonomy' === $_menu_item_type && ! taxonomy_exists( $post_meta_key_value['_menu_item_object'] ) ) {
			return $result;
		}

		// Skip menu items 'post_type' type, when the post type is not exits.
		if ( 'post_type' === $_menu_item_type && ! post_type_exists( $post_meta_key_value['_menu_item_object'] ) ) {
			return $result;
		}

		$_menu_item_object_id = $post_meta_key_value['_menu_item_object_id'];
		if ( 'taxonomy' === $_menu_item_type && isset( $this->processed_terms[ (int) $_menu_item_object_id ] ) ) {
			$_menu_item_object_id = $this->processed_terms[ (int) $_menu_item_object_id ];
		} elseif ( 'post_type' === $_menu_item_type && isset( $this->processed_posts[ (int) $_menu_item_object_id ] ) ) {
			$_menu_item_object_id = $this->processed_posts[ (int) $_menu_item_object_id ];
		} elseif ( 'custom' === $_menu_item_type ) {
				$_menu_item_url = Url::migrate( $_menu_item_url, $this->base_blog_url );
		} else {
			return $result;
		}

		$_menu_item_menu_item_parent = $post_meta_key_value['_menu_item_menu_item_parent'];
		if ( isset( $this->processed_menu_items[ (int) $_menu_item_menu_item_parent ] ) ) {
			$_menu_item_menu_item_parent = $this->processed_menu_items[ (int) $_menu_item_menu_item_parent ];
		} elseif ( $_menu_item_menu_item_parent ) {
			$this->menu_item_orphans[ (int) $item['post_id'] ] = (int) $_menu_item_menu_item_parent;
			$_menu_item_menu_item_parent = 0;
		}

		// wp_update_nav_menu_item expects CSS classes as a space separated string
		$_menu_item_classes = maybe_unserialize( $post_meta_key_value['_menu_item_classes'] );
		if ( is_array( $_menu_item_classes ) ) {
			$_menu_item_classes = implode( ' ', $_menu_item_classes );
		}

		$args = [
			'menu-item-object-id' => $_menu_item_object_id,
			'menu-item-object' => $post_meta_key_value['_menu_item_object'],
			'menu-item-parent-id' => $_menu_item_menu_item_parent,
			'menu-item-position' => (int) $item['menu_order'],
			'menu-item-type' => $_menu_item_type,
			'menu-item-title' => $item['post_title'],
			'menu-item-url' => $_menu_item_url,
			'menu-item-description' => $item['post_content'],
			'menu-item-attr-title' => $item['post_excerpt'],
			'menu-item-target' => $post_meta_key_value['_menu_item_target'],
			'menu-item-classes' => $_menu_item_classes,
			'menu-item-xfn' => $post_meta_key_value['_menu_item_xfn'],
			'menu-item-status' => $item['status'],
		];

		$id = wp_update_nav_menu_item( $menu_id, 0, $args );
		if ( $id && ! is_wp_error( $id ) ) {
			$this->processed_menu_items[ (int) $item['post_id'] ] = (int) $id;
			$result[ $item['post_id'] ] = $id;

			$this->update_post_meta( $id );
		}

		return $result;
	}

	/**
	 * If fetching attachments is enabled then attempt to create a new attachment
	 *
	 * @param array  $post Attachment post details from WXR
	 * @param string $url  URL to fetch attachment from
	 *
	 * @return int|WP_Error Post ID on success, WP_Error otherwise
	 */
	private function process_attachment( $post, $url ) {
		if ( ! $this->fetch_attachments ) {
			return new WP_Error( 'attachment_processing_error', esc_html__( 'Fetching attachments is not enabled', 'elementor' ) );
		}

		// if the URL is absolute, but does not contain address, then upload it assuming base_site_url.
		if ( preg_match( '|^/[\w\W]+$|', $url ) ) {
			$url = rtrim( $this->base_url, '/' ) . $url;
		}

		$upload = $this->fetch_remote_file( $url, $post );
		if ( is_wp_error( $upload ) ) {
			return $upload;
		}

		$info = wp_check_filetype( $upload['file'] );
		if ( $info ) {
			$post['post_mime_type'] = $info['type'];
		} else {
			return new WP_Error( 'attachment_processing_error', esc_html__( 'Invalid file type', 'elementor' ) );
		}

		$post['guid'] = $upload['url'];

		// As per wp-admin/includes/upload.php.
		$post_id = wp_insert_attachment( $post, $upload['file'] );

		$this->update_post_meta( $post_id );

		wp_update_attachment_metadata( $post_id, wp_generate_attachment_metadata( $post_id, $upload['file'] ) );

		// Remap resized image URLs, works by stripping the extension and remapping the URL stub.
		if ( preg_match( '!^image/!', $info['type'] ) ) {
			$parts = pathinfo( $url );
			$name = basename( $parts['basename'], ".{$parts['extension']}" ); // PATHINFO_FILENAME in PHP 5.2

			$parts_new = pathinfo( $upload['url'] );
			$name_new = basename( $parts_new['basename'], ".{$parts_new['extension']}" );

			$this->url_remap[ $parts['dirname'] . '/' . $name ] = $parts_new['dirname'] . '/' . $name_new;
		}

		return $post_id;
	}

	/**
	 * Attempt to download a remote file attachment
	 *
	 * @param string $url  URL of item to fetch
	 * @param array  $post Attachment details
	 *
	 * @return array|WP_Error Local file location details on success, WP_Error otherwise
	 */
	private function fetch_remote_file( $url, $post ) {
		// Extract the file name from the URL.
		$file_name = basename( wp_parse_url( $url, PHP_URL_PATH ) );

		if ( ! $file_name ) {
			$file_name = md5( $url );
		}

		$tmp_file_name = wp_tempnam( $file_name );
		if ( ! $tmp_file_name ) {
			return new WP_Error( 'import_no_file', esc_html__( 'Could not create temporary file.', 'elementor' ) );
		}

		// Fetch the remote URL and write it to the placeholder file.
		$remote_response = wp_safe_remote_get( $url, [
			'timeout' => 300,
			'stream' => true,
			'filename' => $tmp_file_name,
			'headers' => [
				'Accept-Encoding' => 'identity',
			],
		] );

		if ( is_wp_error( $remote_response ) ) {
			@unlink( $tmp_file_name );

			return new WP_Error( 'import_file_error', sprintf( /* translators: 1: WordPress error message, 2: WordPress error code. */ esc_html__( 'Request failed due to an error: %1$s (%2$s)', 'elementor' ), esc_html( $remote_response->get_error_message() ), esc_html( $remote_response->get_error_code() ) ) );
		}

		$remote_response_code = (int) wp_remote_retrieve_response_code( $remote_response );

		// Make sure the fetch was successful.
		if ( 200 !== $remote_response_code ) {
			@unlink( $tmp_file_name );

			return new WP_Error( 'import_file_error', sprintf( /* translators: 1: HTTP error message, 2: HTTP error code. */ esc_html__( 'Remote server returned the following unexpected result: %1$s (%2$s)', 'elementor' ), get_status_header_desc( $remote_response_code ), esc_html( $remote_response_code ) ) );
		}

		$headers = wp_remote_retrieve_headers( $remote_response );

		// Request failed.
		if ( ! $headers ) {
			@unlink( $tmp_file_name );

			return new WP_Error( 'import_file_error', esc_html__( 'Remote server did not respond', 'elementor' ) );
		}

		$filesize = (int) filesize( $tmp_file_name );

		if ( 0 === $filesize ) {
			@unlink( $tmp_file_name );

			return new WP_Error( 'import_file_error', esc_html__( 'Zero size file downloaded', 'elementor' ) );
		}

		if ( ! isset( $headers['content-encoding'] ) && isset( $headers['content-length'] ) && $filesize !== (int) $headers['content-length'] ) {
			@unlink( $tmp_file_name );

			return new WP_Error( 'import_file_error', esc_html__( 'Downloaded file has incorrect size', 'elementor' ) );
		}

		$max_size = (int) apply_filters( 'import_attachment_size_limit', self::DEFAULT_IMPORT_ATTACHMENT_SIZE_LIMIT );
		if ( ! empty( $max_size ) && $filesize > $max_size ) {
			@unlink( $tmp_file_name );

			/* translators: %s: Max file size. */
			return new WP_Error( 'import_file_error', sprintf( esc_html__( 'Remote file is too large, limit is %s', 'elementor' ), size_format( $max_size ) ) );
		}

		// Override file name with Content-Disposition header value.
		if ( ! empty( $headers['content-disposition'] ) ) {
			$file_name_from_disposition = self::get_filename_from_disposition( (array) $headers['content-disposition'] );
			if ( $file_name_from_disposition ) {
				$file_name = $file_name_from_disposition;
			}
		}

		// Set file extension if missing.
		$file_ext = pathinfo( $file_name, PATHINFO_EXTENSION );
		if ( ! $file_ext && ! empty( $headers['content-type'] ) ) {
			$extension = self::get_file_extension_by_mime_type( $headers['content-type'] );
			if ( $extension ) {
				$file_name = "{$file_name}.{$extension}";
			}
		}

		// Handle the upload like _wp_handle_upload() does.
		$wp_filetype = wp_check_filetype_and_ext( $tmp_file_name, $file_name );
		$ext = empty( $wp_filetype['ext'] ) ? '' : $wp_filetype['ext'];
		$type = empty( $wp_filetype['type'] ) ? '' : $wp_filetype['type'];
		$proper_filename = empty( $wp_filetype['proper_filename'] ) ? '' : $wp_filetype['proper_filename'];

		// Check to see if wp_check_filetype_and_ext() determined the filename was incorrect.
		if ( $proper_filename ) {
			$file_name = $proper_filename;
		}

		if ( ( ! $type || ! $ext ) && ! current_user_can( 'unfiltered_upload' ) ) {
			return new WP_Error( 'import_file_error', esc_html__( 'Sorry, this file type is not permitted for security reasons.', 'elementor' ) );
		}

		$uploads = wp_upload_dir( $post['upload_date'] );
		if ( ! ( $uploads && false === $uploads['error'] ) ) {
			return new WP_Error( 'upload_dir_error', $uploads['error'] );
		}

		// Move the file to the uploads dir.
		$file_name = wp_unique_filename( $uploads['path'], $file_name );
		$new_file = $uploads['path'] . "/$file_name";
		$move_new_file = copy( $tmp_file_name, $new_file );

		if ( ! $move_new_file ) {
			@unlink( $tmp_file_name );

			return new WP_Error( 'import_file_error', esc_html__( 'The uploaded file could not be moved', 'elementor' ) );
		}

		// Set correct file permissions.
		$stat = stat( dirname( $new_file ) );
		$perms = $stat['mode'] & 0000666;
		chmod( $new_file, $perms );

		$upload = [
			'file' => $new_file,
			'url' => $uploads['url'] . "/$file_name",
			'type' => $wp_filetype['type'],
			'error' => false,
		];

		// Keep track of the old and new urls so we can substitute them later.
		$this->url_remap[ $url ] = $upload['url'];
		$this->url_remap[ $post['guid'] ] = $upload['url']; // r13735, really needed?
		// Keep track of the destination if the remote url is redirected somewhere else.
		if ( isset( $headers['x-final-location'] ) && $headers['x-final-location'] !== $url ) {
			$this->url_remap[ $headers['x-final-location'] ] = $upload['url'];
		}

		return $upload;
	}

	/**
	 * Attempt to associate posts and menu items with previously missing parents
	 *
	 * An imported post's parent may not have been imported when it was first created
	 * so try again. Similarly for child menu items and menu items which were missing
	 * the object (e.g. post) they represent in the menu
	 */
	private function backfill_parents() {
		global $wpdb;

		// Find parents for post orphans.
		foreach ( $this->post_orphans as $child_id => $parent_id ) {
			$local_child_id = false;
			$local_parent_id = false;

			if ( isset( $this->processed_posts[ $child_id ] ) ) {
				$local_child_id = $this->processed_posts[ $child_id ];
			}
			if ( isset( $this->processed_posts[ $parent_id ] ) ) {
				$local_parent_id = $this->processed_posts[ $parent_id ];
			}

			if ( $local_child_id && $local_parent_id ) {
				$wpdb->update( $wpdb->posts, [ 'post_parent' => $local_parent_id ], [ 'ID' => $local_child_id ], '%d', '%d' );
				clean_post_cache( $local_child_id );
			}
		}

		// Find parents for menu item orphans.
		foreach ( $this->menu_item_orphans as $child_id => $parent_id ) {
			$local_child_id = 0;
			$local_parent_id = 0;
			if ( isset( $this->processed_menu_items[ $child_id ] ) ) {
				$local_child_id = $this->processed_menu_items[ $child_id ];
			}
			if ( isset( $this->processed_menu_items[ $parent_id ] ) ) {
				$local_parent_id = $this->processed_menu_items[ $parent_id ];
			}

			if ( $local_child_id && $local_parent_id ) {
				update_post_meta( $local_child_id, '_menu_item_menu_item_parent', (int) $local_parent_id );
			}
		}
	}

	/**
	 * Use stored mapping information to update old attachment URLs
	 */
	private function backfill_attachment_urls() {
		global $wpdb;
		// Make sure we do the longest urls first, in case one is a substring of another.
		uksort( $this->url_remap, function ( $a, $b ) {
			// Return the difference in length between two strings.
			return strlen( $b ) - strlen( $a );
		} );

		foreach ( $this->url_remap as $from_url => $to_url ) {
			// Remap urls in post_content.
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)", $from_url, $to_url ) );
			// Remap enclosure urls.
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_key='enclosure'", $from_url, $to_url ) );
		}
	}

	/**
	 * Update _thumbnail_id meta to new, imported attachment IDs
	 */
	private function remap_featured_images() {
		// Cycle through posts that have a featured image.
		foreach ( $this->featured_images as $post_id => $value ) {
			if ( isset( $this->processed_posts[ $value ] ) ) {
				$new_id = $this->processed_posts[ $value ];
				// Only update if there's a difference.
				if ( $new_id !== $value ) {
					update_post_meta( $post_id, '_thumbnail_id', $new_id );
				}
			}
		}
	}

	/**
	 * Parse a WXR file
	 *
	 * @param string $file Path to WXR file for parsing
	 *
	 * @return array Information gathered from the WXR file
	 */
	private function parse( $file ) {
		$parser = new WXR_Parser();

		return $parser->parse( $file );
	}

	/**
	 * Decide if the given meta key maps to information we will want to import
	 *
	 * @param string $key The meta key to check
	 *
	 * @return string|bool The key if we do want to import, false if not
	 */
	private function is_valid_meta_key( $key ) {
		// Skip attachment metadata since we'll regenerate it from scratch.
		// Skip _edit_lock as not relevant for import
		if ( in_array( $key, [ '_wp_attached_file', '_wp_attachment_metadata', '_edit_lock' ] ) ) {
			return false;
		}

		return $key;
	}

	/**
	 * @param $term
	 * @return mixed
	 */
	private function handle_duplicated_nav_menu_term( $term ) {
		$duplicate_slug = $term['slug'] . '-duplicate';
		$duplicate_name = $term['term_name'] . ' duplicate';

		while ( term_exists( $duplicate_slug, 'nav_menu' ) ) {
			$duplicate_slug .= '-duplicate';
			$duplicate_name .= ' duplicate';
		}

		$this->mapped_terms_slug[ $term['slug'] ] = $duplicate_slug;

		$term['slug'] = $duplicate_slug;
		$term['term_name'] = $duplicate_name;

		return $term;
	}

	/**
	 * Add all term_meta to specified term.
	 *
	 * @param $term_id
	 * @return void
	 */
	private function update_term_meta( $term_id ) {
		foreach ( $this->terms_meta as $meta_key => $meta_value ) {
			update_term_meta( $term_id, $meta_key, $meta_value );
		}
	}

	/**
	 * Add all post_meta to specified term.
	 *
	 * @param $post_id
	 * @return void
	 */
	private function update_post_meta( $post_id ) {
		foreach ( $this->posts_meta as $meta_key => $meta_value ) {
			update_post_meta( $post_id, $meta_key, $meta_value );
		}
	}

	public function run() {
		$this->import( $this->requested_file_path );

		return $this->output;
	}

	/**
	 * @param $file
	 * @param $args
	 */
	public function __construct( $file, $args = [] ) {
		if ( ! function_exists( 'wp_tempnam' ) || ! function_exists( 'wp_upload_dir' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		if ( ! function_exists( 'wp_insert_term' ) ) {
			require_once ABSPATH . 'wp-admin/includes/taxonomy.php';
		}

		if ( ! function_exists( 'wp_insert_attachment' ) || ! function_exists( 'wp_update_attachment_metadata' ) || ! function_exists( 'wp_generate_attachment_metadata' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		if ( ! function_exists( 'wp_update_nav_menu_item' ) ) {
			require_once ABSPATH . 'wp-admin/includes/nav-menu.php';
		}

		if ( ! function_exists( 'wp_create_user' ) || ! function_exists( 'wp_insert_user' ) ) {
			require_once ABSPATH . 'wp-admin/includes/user.php';
		}

		if ( ! function_exists( 'wp_import_cleanup' ) ) {
			require_once ABSPATH . 'wp-admin/includes/import.php';
		}

		if ( ! function_exists( 'stick_post' ) ) {
			require_once ABSPATH . 'wp-admin/includes/post.php';
		}

		$this->requested_file_path = $file;
		$this->args = $args;

		if ( ! empty( $this->args['fetch_attachments'] ) ) {
			$this->fetch_attachments = true;
		}

		if ( isset( $this->args['posts'] ) && is_array( $this->args['posts'] ) ) {
			$this->processed_posts = $this->args['posts'];
		}

		if ( isset( $this->args['terms'] ) && is_array( $this->args['terms'] ) ) {
			$this->processed_terms = $this->args['terms'];
		}

		if ( isset( $this->args['taxonomies'] ) && is_array( $this->args['taxonomies'] ) ) {
			$this->processed_taxonomies = $this->args['taxonomies'];
		}

		if ( ! empty( $this->args['posts_meta'] ) ) {
			$this->posts_meta = $this->args['posts_meta'];
		}

		if ( ! empty( $this->args['terms_meta'] ) ) {
			$this->terms_meta = $this->args['terms_meta'];
		}
	}
}
