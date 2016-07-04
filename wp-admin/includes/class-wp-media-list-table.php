<?php
/**
 * List Table API: WP_Media_List_Table class
 *
 * @package WordPress
 * @subpackage Administration
 * @since 3.1.0
 */

/**
 * Core class used to implement displaying media items in a list table.
 *
 * @since 3.1.0
 * @access private
 *
 * @see WP_List_Table
 */
class WP_Media_List_Table extends WP_List_Table {
	/**
	 * Holds the number of pending comments for each post.
	 *
	 * @since 4.4.0
	 * @var array
	 * @access protected
	 */
	protected $comment_pending_count = array();

	private $detached;

	private $is_trash;

	/**
	 * Constructor.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @see WP_List_Table::__construct() for more information on default arguments.
	 *
	 * @param array $args An associative array of arguments.
	 */
	public function __construct( $args = array() ) {
		$this->detached = ( isset( $_REQUEST['attachment-filter'] ) && 'detached' === $_REQUEST['attachment-filter'] );

		$this->modes = array(
			'list' => __( 'List View' ),
			'grid' => __( 'Grid View' )
		);

		parent::__construct( array(
			'plural' => 'media',
			'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
		) );
	}

	/**
	 *
	 * @return bool
	 */
	public function ajax_user_can() {
		return current_user_can('upload_files');
	}

	/**
	 *
	 * @global WP_Query $wp_query
	 * @global array    $post_mime_types
	 * @global array    $avail_post_mime_types
	 * @global string   $mode
	 */
	public function prepare_items() {
		global $wp_query, $post_mime_types, $avail_post_mime_types, $mode;

		list( $post_mime_types, $avail_post_mime_types ) = wp_edit_attachments_query( $_REQUEST );

 		$this->is_trash = isset( $_REQUEST['attachment-filter'] ) && 'trash' === $_REQUEST['attachment-filter'];

 		$mode = empty( $_REQUEST['mode'] ) ? 'list' : $_REQUEST['mode'];

		$this->set_pagination_args( array(
			'total_items' => $wp_query->found_posts,
			'total_pages' => $wp_query->max_num_pages,
			'per_page' => $wp_query->query_vars['posts_per_page'],
		) );
	}

	/**
	 * @global array $post_mime_types
	 * @global array $avail_post_mime_types
	 * @return array
	 */
	protected function get_views() {
		global $post_mime_types, $avail_post_mime_types;

		$type_links = array();

		$filter = empty( $_GET['attachment-filter'] ) ? '' : $_GET['attachment-filter'];

		$type_links['all'] = sprintf(
			'<option value=""%s>%s</option>',
			selected( $filter, true, false ),
			__( 'All media items' )
		);

		foreach ( $post_mime_types as $mime_type => $label ) {
			if ( ! wp_match_mime_types( $mime_type, $avail_post_mime_types ) ) {
				continue;
			}

			$selected = selected(
				$filter && 0 === strpos( $filter, 'post_mime_type:' ) &&
					wp_match_mime_types( $mime_type, str_replace( 'post_mime_type:', '', $filter ) ),
				true,
				false
			);

			$type_links[$mime_type] = sprintf(
				'<option value="post_mime_type:%s"%s>%s</option>',
				esc_attr( $mime_type ),
				$selected,
				$label[0]
			);
		}
		$type_links['detached'] = '<option value="detached"' . ( $this->detached ? ' selected="selected"' : '' ) . '>' . __( 'Unattached' ) . '</option>';

		if ( $this->is_trash || ( defined( 'MEDIA_TRASH') && MEDIA_TRASH ) ) {
			$type_links['trash'] = sprintf(
				'<option value="trash"%s>%s</option>',
				selected( 'trash' === $filter, true, false ),
				_x( 'Trash', 'attachment filter' )
			);
		}
		return $type_links;
	}

	/**
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {
		$actions = array();
		if ( MEDIA_TRASH ) {
			if ( $this->is_trash ) {
				$actions['untrash'] = __( 'Restore' );
				$actions['delete'] = __( 'Delete Permanently' );
			} else {
				$actions['trash'] = _x( 'Trash', 'verb' );
			}
		} else {
			$actions['delete'] = __( 'Delete Permanently' );
		}

		if ( $this->detached )
			$actions['attach'] = __( 'Attach' );

		return $actions;
	}

	/**
	 * @param string $which
	 */
	protected function extra_tablenav( $which ) {
		if ( 'bar' !== $which ) {
			return;
		}
?>
		<div class="actions">
<?php
		if ( ! is_singular() ) {
			if ( ! $this->is_trash ) {
				$this->months_dropdown( 'attachment' );
			}

			/** This action is documented in wp-admin/includes/class-wp-posts-list-table.php */
			do_action( 'restrict_manage_posts', $this->screen->post_type );

			submit_button( __( 'Filter' ), 'button', 'filter_action', false, array( 'id' => 'post-query-submit' ) );
		}

		if ( $this->is_trash && current_user_can( 'edit_others_posts' ) ) {
			submit_button( __( 'Empty Trash' ), 'apply', 'delete_all', false );
		} ?>
		</div>
<?php
	}

	/**
	 *
	 * @return string
	 */
	public function current_action() {
		if ( isset( $_REQUEST['found_post_id'] ) && isset( $_REQUEST['media'] ) )
			return 'attach';

		if ( isset( $_REQUEST['parent_post_id'] ) && isset( $_REQUEST['media'] ) )
			return 'detach';

		if ( isset( $_REQUEST['delete_all'] ) || isset( $_REQUEST['delete_all2'] ) )
			return 'delete_all';

		return parent::current_action();
	}

	/**
	 *
	 * @return bool
	 */
	public function has_items() {
		return have_posts();
	}

	/**
	 * @access public
	 */
	public function no_items() {
		_e( 'No media files found.' );
	}

	/**
	 * Override parent views so we can use the filter bar display.
	 *
	 * @global string $mode
	 */
	public function views() {
		global $mode;

		$views = $this->get_views();

		$this->screen->render_screen_reader_content( 'heading_views' );
?>
<div class="wp-filter">
	<div class="filter-items">
		<?php $this->view_switcher( $mode ); ?>

		<label for="attachment-filter" class="screen-reader-text"><?php _e( 'Filter by type' ); ?></label>
		<select class="attachment-filters" name="attachment-filter" id="attachment-filter">
			<?php
			if ( ! empty( $views ) ) {
				foreach ( $views as $class => $view ) {
					echo "\t$view\n";
				}
			}
			?>
		</select>

<?php
		$this->extra_tablenav( 'bar' );

		/** This filter is documented in wp-admin/inclues/class-wp-list-table.php */
		$views = apply_filters( "views_{$this->screen->id}", array() );

		// Back compat for pre-4.0 view links.
		if ( ! empty( $views ) ) {
			echo '<ul class="filter-links">';
			foreach ( $views as $class => $view ) {
				echo "<li class='$class'>$view</li>";
			}
			echo '</ul>';
		}
?>
	</div>

	<div class="search-form">
		<label for="media-search-input" class="screen-reader-text"><?php esc_html_e( 'Search Media' ); ?></label>
		<input type="search" placeholder="<?php esc_attr_e( 'Search' ) ?>" id="media-search-input" class="search" name="s" value="<?php _admin_search_query(); ?>"></div>
	</div>
	<?php
	}

	/**
	 *
	 * @return array
	 */
	public function get_columns() {
		$posts_columns = array();
		$posts_columns['cb'] = '<input type="checkbox" />';
		/* translators: column name */
		$posts_columns['title'] = _x( 'File', 'column name' );
		$posts_columns['author'] = __( 'Author' );

		$taxonomies = get_taxonomies_for_attachments( 'objects' );
		$taxonomies = wp_filter_object_list( $taxonomies, array( 'show_admin_column' => true ), 'and', 'name' );

		/**
		 * Filters the taxonomy columns for attachments in the Media list table.
		 *
		 * @since 3.5.0
		 *
		 * @param array  $taxonomies An array of registered taxonomies to show for attachments.
		 * @param string $post_type  The post type. Default 'attachment'.
		 */
		$taxonomies = apply_filters( 'manage_taxonomies_for_attachment_columns', $taxonomies, 'attachment' );
		$taxonomies = array_filter( $taxonomies, 'taxonomy_exists' );

		foreach ( $taxonomies as $taxonomy ) {
			if ( 'category' === $taxonomy ) {
				$column_key = 'categories';
			} elseif ( 'post_tag' === $taxonomy ) {
				$column_key = 'tags';
			} else {
				$column_key = 'taxonomy-' . $taxonomy;
			}
			$posts_columns[ $column_key ] = get_taxonomy( $taxonomy )->labels->name;
		}

		/* translators: column name */
		if ( !$this->detached ) {
			$posts_columns['parent'] = _x( 'Uploaded to', 'column name' );
			if ( post_type_supports( 'attachment', 'comments' ) )
				$posts_columns['comments'] = '<span class="vers comment-grey-bubble" title="' . esc_attr__( 'Comments' ) . '"><span class="screen-reader-text">' . __( 'Comments' ) . '</span></span>';
		}
		/* translators: column name */
		$posts_columns['date'] = _x( 'Date', 'column name' );
		/**
		 * Filters the Media list table columns.
		 *
		 * @since 2.5.0
		 *
		 * @param array $posts_columns An array of columns displayed in the Media list table.
		 * @param bool  $detached      Whether the list table contains media not attached
		 *                             to any posts. Default true.
		 */
		return apply_filters( 'manage_media_columns', $posts_columns, $this->detached );
	}

	/**
	 *
	 * @return array
	 */
	protected function get_sortable_columns() {
		return array(
			'title'    => 'title',
			'author'   => 'author',
			'parent'   => 'parent',
			'comments' => 'comment_count',
			'date'     => array( 'date', true ),
		);
	}

	/**
	 * Handles the checkbox column output.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param WP_Post $post The current WP_Post object.
	 */
	public function column_cb( $post ) {
		if ( current_user_can( 'edit_post', $post->ID ) ) { ?>
			<label class="screen-reader-text" for="cb-select-<?php echo $post->ID; ?>"><?php
				echo sprintf( __( 'Select %s' ), _draft_or_post_title() );
			?></label>
			<input type="checkbox" name="media[]" id="cb-select-<?php echo $post->ID; ?>" value="<?php echo $post->ID; ?>" />
		<?php }
	}

	/**
	 * Handles the title column output.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param WP_Post $post The current WP_Post object.
	 */
	public function column_title( $post ) {
		list( $mime ) = explode( '/', $post->post_mime_type );

		$title = _draft_or_post_title();
		$thumb = wp_get_attachment_image( $post->ID, array( 60, 60 ), true, array( 'alt' => '' ) );
		$link_start = $link_end = '';

		if ( current_user_can( 'edit_post', $post->ID ) && ! $this->is_trash ) {
			$link_start = sprintf(
				'<a href="%s" aria-label="%s">',
				get_edit_post_link( $post->ID ),
				/* translators: %s: attachment title */
				esc_attr( sprintf( __( '&#8220;%s&#8221; (Edit)' ), $title ) )
			);
			$link_end = '</a>';
		}

		$class = $thumb ? ' class="has-media-icon"' : '';
		?>
		<strong<?php echo $class; ?>>
			<?php
			echo $link_start;
			if ( $thumb ) : ?>
				<span class="media-icon <?php echo sanitize_html_class( $mime . '-icon' ); ?>"><?php echo $thumb; ?></span>
			<?php endif;
			echo $title . $link_end;
			_media_states( $post );
			?>
		</strong>
		<p class="filename">
			<span class="screen-reader-text"><?php _e( 'File name:' ); ?> </span>
			<?php
			$file = get_attached_file( $post->ID );
			echo esc_html( wp_basename( $file ) );
			?>
		</p>
		<?php
	}

	/**
	 * Handles the author column output.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param WP_Post $post The current WP_Post object.
	 */
	public function column_author( $post ) {
		printf( '<a href="%s">%s</a>',
			esc_url( add_query_arg( array( 'author' => get_the_author_meta('ID') ), 'upload.php' ) ),
			get_the_author()
		);
	}

	/**
	 * Handles the description column output.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param WP_Post $post The current WP_Post object.
	 */
	public function column_desc( $post ) {
		echo has_excerpt() ? $post->post_excerpt : '';
	}

	/**
	 * Handles the date column output.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param WP_Post $post The current WP_Post object.
	 */
	public function column_date( $post ) {
		if ( '0000-00-00 00:00:00' === $post->post_date ) {
			$h_time = __( 'Unpublished' );
		} else {
			$m_time = $post->post_date;
			$time = get_post_time( 'G', true, $post, false );
			if ( ( abs( $t_diff = time() - $time ) ) < DAY_IN_SECONDS ) {
				if ( $t_diff < 0 ) {
					$h_time = sprintf( __( '%s from now' ), human_time_diff( $time ) );
				} else {
					$h_time = sprintf( __( '%s ago' ), human_time_diff( $time ) );
				}
			} else {
				$h_time = mysql2date( __( 'Y/m/d' ), $m_time );
			}
		}

		echo $h_time;
	}

	/**
	 * Handles the parent column output.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param WP_Post $post The current WP_Post object.
	 */
	public function column_parent( $post ) {
		$user_can_edit = current_user_can( 'edit_post', $post->ID );

		if ( $post->post_parent > 0 ) {
			$parent = get_post( $post->post_parent );
		} else {
			$parent = false;
		}

		if ( $parent ) {
			$title = _draft_or_post_title( $post->post_parent );
			$parent_type = get_post_type_object( $parent->post_type );
			
			if ( $parent_type && $parent_type->show_ui && current_user_can( 'edit_post', $post->post_parent ) ) {
?>
				<strong><a href="<?php echo get_edit_post_link( $post->post_parent ); ?>">
					<?php echo $title ?></a></strong><?php
			} elseif ( $parent_type && current_user_can( 'read_post', $post->post_parent ) ) {
?>
				<strong><?php echo $title ?></strong><?php
			} else {
				_e( '(Private post)' );
			}

			if ( $user_can_edit ):
				$detach_url = add_query_arg( array(
					'parent_post_id' => $post->post_parent,
					'media[]' => $post->ID,
					'_wpnonce' => wp_create_nonce( 'bulk-' . $this->_args['plural'] )
				), 'upload.php' );
				printf(
					'<br /><a href="%s" class="hide-if-no-js detach-from-parent" aria-label="%s">%s</a>',
					$detach_url,
					/* translators: %s: title of the post the attachment is attached to */
					esc_attr( sprintf( __( 'Detach from &#8220;%s&#8221;' ), $title ) ),
					__( 'Detach' )
				);
			endif;
		} else {
			_e( '(Unattached)' ); ?>
			<?php if ( $user_can_edit ) {
				$title = _draft_or_post_title( $post->post_parent );
				printf(
					'<br /><a href="#the-list" onclick="findPosts.open( \'media[]\', \'%s\' ); return false;" class="hide-if-no-js" aria-label="%s">%s</a>',
					$post->ID,
					/* translators: %s: attachment title */
					esc_attr( sprintf( __( 'Attach &#8220;%s&#8221; to existing content' ), $title ) ),
					__( 'Attach' )
				);
			}
		}
	}

	/**
	 * Handles the comments column output.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param WP_Post $post The current WP_Post object.
	 */
	public function column_comments( $post ) {
		echo '<div class="post-com-count-wrapper">';

		if ( isset( $this->comment_pending_count[ $post->ID ] ) ) {
			$pending_comments = $this->comment_pending_count[ $post->ID ];
		} else {
			$pending_comments = get_pending_comments_num( $post->ID );
		}

		$this->comments_bubble( $post->ID, $pending_comments );

		echo '</div>';
	}

	/**
	 * Handles output for the default column.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param WP_Post $post        The current WP_Post object.
	 * @param string  $column_name Current column name.
	 */
	public function column_default( $post, $column_name ) {
		if ( 'categories' === $column_name ) {
			$taxonomy = 'category';
		} elseif ( 'tags' === $column_name ) {
			$taxonomy = 'post_tag';
		} elseif ( 0 === strpos( $column_name, 'taxonomy-' ) ) {
			$taxonomy = substr( $column_name, 9 );
		} else {
			$taxonomy = false;
		}

		if ( $taxonomy ) {
			$terms = get_the_terms( $post->ID, $taxonomy );
			if ( is_array( $terms ) ) {
				$out = array();
				foreach ( $terms as $t ) {
					$posts_in_term_qv = array();
					$posts_in_term_qv['taxonomy'] = $taxonomy;
					$posts_in_term_qv['term'] = $t->slug;

					$out[] = sprintf( '<a href="%s">%s</a>',
						esc_url( add_query_arg( $posts_in_term_qv, 'upload.php' ) ),
						esc_html( sanitize_term_field( 'name', $t->name, $t->term_id, $taxonomy, 'display' ) )
					);
				}
				/* translators: used between list items, there is a space after the comma */
				echo join( __( ', ' ), $out );
			} else {
				echo '<span aria-hidden="true">&#8212;</span><span class="screen-reader-text">' . get_taxonomy( $taxonomy )->labels->no_terms . '</span>';
			}

			return;
		}

		/**
		 * Fires for each custom column in the Media list table.
		 *
		 * Custom columns are registered using the {@see 'manage_media_columns'} filter.
		 *
		 * @since 2.5.0
		 *
		 * @param string $column_name Name of the custom column.
		 * @param int    $post_id     Attachment ID.
		 */
		do_action( 'manage_media_custom_column', $column_name, $post->ID );
	}

	/**
	 *
	 * @global WP_Post $post
	 */
	public function display_rows() {
		global $post, $wp_query;

		$post_ids = wp_list_pluck( $wp_query->posts, 'ID' );
		reset( $wp_query->posts );

		$this->comment_pending_count = get_pending_comments_num( $post_ids );

		add_filter( 'the_title','esc_html' );

		while ( have_posts() ) : the_post();
			if (
				( $this->is_trash && $post->post_status != 'trash' )
				|| ( ! $this->is_trash && $post->post_status === 'trash' )
			) {
				continue;
			}
			$post_owner = ( get_current_user_id() == $post->post_author ) ? 'self' : 'other';
		?>
			<tr id="post-<?php echo $post->ID; ?>" class="<?php echo trim( ' author-' . $post_owner . ' status-' . $post->post_status ); ?>">
				<?php $this->single_row_columns( $post ); ?>
			</tr>
		<?php
		endwhile;
	}

	/**
	 * Gets the name of the default primary column.
	 *
	 * @since 4.3.0
	 * @access protected
	 *
	 * @return string Name of the default primary column, in this case, 'title'.
	 */
	protected function get_default_primary_column_name() {
		return 'title';
	}

	/**
	 * @param WP_Post $post
	 * @param string  $att_title
	 *
	 * @return array
	 */
	private function _get_row_actions( $post, $att_title ) {
		$actions = array();

		if ( $this->detached ) {
			if ( current_user_can( 'edit_post', $post->ID ) ) {
				$actions['edit'] = sprintf(
					'<a href="%s" aria-label="%s">%s</a>',
					get_edit_post_link( $post->ID ),
					/* translators: %s: attachment title */
					esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $att_title ) ),
					__( 'Edit' )
				);
			}
			if ( current_user_can( 'delete_post', $post->ID ) ) {
				if ( EMPTY_TRASH_DAYS && MEDIA_TRASH ) {
					$actions['trash'] = sprintf(
						'<a href="%s" class="submitdelete" aria-label="%s">%s</a>',
						wp_nonce_url( "post.php?action=trash&amp;post=$post->ID", 'trash-post_' . $post->ID ),
						/* translators: %s: attachment title */
						esc_attr( sprintf( __( 'Move &#8220;%s&#8221; to the Trash' ), $att_title ) ),
						_x( 'Trash', 'verb' )
					);
				} else {
					$delete_ays = ! MEDIA_TRASH ? " onclick='return showNotice.warn();'" : '';
					$actions['delete'] = sprintf(
						'<a href="%s" class="submitdelete"%s aria-label="%s">%s</a>',
						wp_nonce_url( "post.php?action=delete&amp;post=$post->ID", 'delete-post_' . $post->ID ),
						$delete_ays,
						/* translators: %s: attachment title */
						esc_attr( sprintf( __( 'Delete &#8220;%s&#8221; permanently' ), $att_title ) ),
						__( 'Delete Permanently' )
					);
				}
			}
			$actions['view'] = sprintf(
				'<a href="%s" aria-label="%s" rel="permalink">%s</a>',
				get_permalink( $post->ID ),
				/* translators: %s: attachment title */
				esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $att_title ) ),
				__( 'View' )
			);

			if ( current_user_can( 'edit_post', $post->ID ) ) {
				$actions['attach'] = sprintf(
					'<a href="#the-list" onclick="findPosts.open( \'media[]\', \'%s\' ); return false;" class="hide-if-no-js" aria-label="%s">%s</a>',
					$post->ID,
					/* translators: %s: attachment title */
					esc_attr( sprintf( __( 'Attach &#8220;%s&#8221; to existing content' ), $att_title ) ),
					__( 'Attach' )
				);
			}
		}
		else {
			if ( current_user_can( 'edit_post', $post->ID ) && !$this->is_trash ) {
				$actions['edit'] = sprintf(
					'<a href="%s" aria-label="%s">%s</a>',
					get_edit_post_link( $post->ID ),
					/* translators: %s: attachment title */
					esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $att_title ) ),
					__( 'Edit' )
				);
			}
			if ( current_user_can( 'delete_post', $post->ID ) ) {
				if ( $this->is_trash ) {
					$actions['untrash'] = sprintf(
						'<a href="%s" class="submitdelete" aria-label="%s">%s</a>',
						wp_nonce_url( "post.php?action=untrash&amp;post=$post->ID", 'untrash-post_' . $post->ID ),
						/* translators: %s: attachment title */
						esc_attr( sprintf( __( 'Restore &#8220;%s&#8221; from the Trash' ), $att_title ) ),
						__( 'Restore' )
					);
				} elseif ( EMPTY_TRASH_DAYS && MEDIA_TRASH ) {
					$actions['trash'] = sprintf(
						'<a href="%s" class="submitdelete" aria-label="%s">%s</a>',
						wp_nonce_url( "post.php?action=trash&amp;post=$post->ID", 'trash-post_' . $post->ID ),
						/* translators: %s: attachment title */
						esc_attr( sprintf( __( 'Move &#8220;%s&#8221; to the Trash' ), $att_title ) ),
						_x( 'Trash', 'verb' )
					);
				}
				if ( $this->is_trash || ! EMPTY_TRASH_DAYS || ! MEDIA_TRASH ) {
					$delete_ays = ( !$this->is_trash && !MEDIA_TRASH ) ? " onclick='return showNotice.warn();'" : '';
					$actions['delete'] = sprintf(
						'<a href="%s" class="submitdelete"%s aria-label="%s">%s</a>',
						wp_nonce_url( "post.php?action=delete&amp;post=$post->ID", 'delete-post_' . $post->ID ),
						$delete_ays,
						/* translators: %s: attachment title */
						esc_attr( sprintf( __( 'Delete &#8220;%s&#8221; permanently' ), $att_title ) ),
						__( 'Delete Permanently' )
					);
				}
			}
			if ( ! $this->is_trash ) {
				$actions['view'] = sprintf(
					'<a href="%s" aria-label="%s" rel="permalink">%s</a>',
					get_permalink( $post->ID ),
					/* translators: %s: attachment title */
					esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $att_title ) ),
					__( 'View' )
				);
			}
		}

		/**
		 * Filters the action links for each attachment in the Media list table.
		 *
		 * @since 2.8.0
		 *
		 * @param array   $actions  An array of action links for each attachment.
		 *                          Default 'Edit', 'Delete Permanently', 'View'.
		 * @param WP_Post $post     WP_Post object for the current attachment.
		 * @param bool    $detached Whether the list table contains media not attached
		 *                          to any posts. Default true.
		 */
		return apply_filters( 'media_row_actions', $actions, $post, $this->detached );
	}

	/**
	 * Generates and displays row action links.
	 *
	 * @since 4.3.0
	 * @access protected
	 *
	 * @param object $post        Attachment being acted upon.
	 * @param string $column_name Current column name.
	 * @param string $primary     Primary column name.
	 * @return string Row actions output for media attachments.
	 */
	protected function handle_row_actions( $post, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return '';
		}

		$att_title = _draft_or_post_title();
		return $this->row_actions( $this->_get_row_actions( $post, $att_title ) );
	}
}
