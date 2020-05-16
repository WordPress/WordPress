<?php
/**
 * Core User Role & Capabilities API
 *
 * @package WordPress
 * @subpackage Users
 */

/**
 * Maps meta capabilities to primitive capabilities.
 *
 * This function also accepts an ID of an object to map against if the capability is a meta capability. Meta
 * capabilities such as `edit_post` and `edit_user` are capabilities used by this function to map to primitive
 * capabilities that a user or role has, such as `edit_posts` and `edit_others_posts`.
 *
 * Example usage:
 *
 *     map_meta_cap( 'edit_posts', $user->ID );
 *     map_meta_cap( 'edit_post', $user->ID, $post->ID );
 *     map_meta_cap( 'edit_post_meta', $user->ID, $post->ID, $meta_key );
 *
 * This does not actually compare whether the user ID has the actual capability,
 * just what the capability or capabilities are. Meta capability list value can
 * be 'delete_user', 'edit_user', 'remove_user', 'promote_user', 'delete_post',
 * 'delete_page', 'edit_post', 'edit_page', 'read_post', or 'read_page'.
 *
 * @since 2.0.0
 * @since 5.3.0 Formalized the existing and already documented `...$args` parameter
 *              by adding it to the function signature.
 *
 * @global array $post_type_meta_caps Used to get post type meta capabilities.
 *
 * @param string $cap     Capability name.
 * @param int    $user_id User ID.
 * @param mixed  ...$args Optional further parameters, typically starting with an object ID.
 * @return string[] Actual capabilities for meta capability.
 */
function map_meta_cap( $cap, $user_id, ...$args ) {
	$caps = array();

	switch ( $cap ) {
		case 'remove_user':
			// In multisite the user must be a super admin to remove themselves.
			if ( isset( $args[0] ) && $user_id == $args[0] && ! is_super_admin( $user_id ) ) {
				$caps[] = 'do_not_allow';
			} else {
				$caps[] = 'remove_users';
			}
			break;
		case 'promote_user':
		case 'add_users':
			$caps[] = 'promote_users';
			break;
		case 'edit_user':
		case 'edit_users':
			// Allow user to edit themselves.
			if ( 'edit_user' === $cap && isset( $args[0] ) && $user_id == $args[0] ) {
				break;
			}

			// In multisite the user must have manage_network_users caps. If editing a super admin, the user must be a super admin.
			if ( is_multisite() && ( ( ! is_super_admin( $user_id ) && 'edit_user' === $cap && is_super_admin( $args[0] ) ) || ! user_can( $user_id, 'manage_network_users' ) ) ) {
				$caps[] = 'do_not_allow';
			} else {
				$caps[] = 'edit_users'; // edit_user maps to edit_users.
			}
			break;
		case 'delete_post':
		case 'delete_page':
			$post = get_post( $args[0] );
			if ( ! $post ) {
				$caps[] = 'do_not_allow';
				break;
			}

			if ( 'revision' === $post->post_type ) {
				$caps[] = 'do_not_allow';
				break;
			}

			if ( ( get_option( 'page_for_posts' ) == $post->ID ) || ( get_option( 'page_on_front' ) == $post->ID ) ) {
				$caps[] = 'manage_options';
				break;
			}

			$post_type = get_post_type_object( $post->post_type );
			if ( ! $post_type ) {
				/* translators: 1: Post type, 2: Capability name. */
				_doing_it_wrong( __FUNCTION__, sprintf( __( 'The post type %1$s is not registered, so it may not be reliable to check the capability "%2$s" against a post of that type.' ), $post->post_type, $cap ), '4.4.0' );
				$caps[] = 'edit_others_posts';
				break;
			}

			if ( ! $post_type->map_meta_cap ) {
				$caps[] = $post_type->cap->$cap;
				// Prior to 3.1 we would re-call map_meta_cap here.
				if ( 'delete_post' === $cap ) {
					$cap = $post_type->cap->$cap;
				}
				break;
			}

			// If the post author is set and the user is the author...
			if ( $post->post_author && $user_id == $post->post_author ) {
				// If the post is published or scheduled...
				if ( in_array( $post->post_status, array( 'publish', 'future' ), true ) ) {
					$caps[] = $post_type->cap->delete_published_posts;
				} elseif ( 'trash' === $post->post_status ) {
					$status = get_post_meta( $post->ID, '_wp_trash_meta_status', true );
					if ( in_array( $status, array( 'publish', 'future' ), true ) ) {
						$caps[] = $post_type->cap->delete_published_posts;
					} else {
						$caps[] = $post_type->cap->delete_posts;
					}
				} else {
					// If the post is draft...
					$caps[] = $post_type->cap->delete_posts;
				}
			} else {
				// The user is trying to edit someone else's post.
				$caps[] = $post_type->cap->delete_others_posts;
				// The post is published or scheduled, extra cap required.
				if ( in_array( $post->post_status, array( 'publish', 'future' ), true ) ) {
					$caps[] = $post_type->cap->delete_published_posts;
				} elseif ( 'private' === $post->post_status ) {
					$caps[] = $post_type->cap->delete_private_posts;
				}
			}

			/*
			 * Setting the privacy policy page requires `manage_privacy_options`,
			 * so deleting it should require that too.
			 */
			if ( (int) get_option( 'wp_page_for_privacy_policy' ) === $post->ID ) {
				$caps = array_merge( $caps, map_meta_cap( 'manage_privacy_options', $user_id ) );
			}

			break;
		// edit_post breaks down to edit_posts, edit_published_posts, or
		// edit_others_posts.
		case 'edit_post':
		case 'edit_page':
			$post = get_post( $args[0] );
			if ( ! $post ) {
				$caps[] = 'do_not_allow';
				break;
			}

			if ( 'revision' === $post->post_type ) {
				$post = get_post( $post->post_parent );
				if ( ! $post ) {
					$caps[] = 'do_not_allow';
					break;
				}
			}

			$post_type = get_post_type_object( $post->post_type );
			if ( ! $post_type ) {
				/* translators: 1: Post type, 2: Capability name. */
				_doing_it_wrong( __FUNCTION__, sprintf( __( 'The post type %1$s is not registered, so it may not be reliable to check the capability "%2$s" against a post of that type.' ), $post->post_type, $cap ), '4.4.0' );
				$caps[] = 'edit_others_posts';
				break;
			}

			if ( ! $post_type->map_meta_cap ) {
				$caps[] = $post_type->cap->$cap;
				// Prior to 3.1 we would re-call map_meta_cap here.
				if ( 'edit_post' === $cap ) {
					$cap = $post_type->cap->$cap;
				}
				break;
			}

			// If the post author is set and the user is the author...
			if ( $post->post_author && $user_id == $post->post_author ) {
				// If the post is published or scheduled...
				if ( in_array( $post->post_status, array( 'publish', 'future' ), true ) ) {
					$caps[] = $post_type->cap->edit_published_posts;
				} elseif ( 'trash' === $post->post_status ) {
					$status = get_post_meta( $post->ID, '_wp_trash_meta_status', true );
					if ( in_array( $status, array( 'publish', 'future' ), true ) ) {
						$caps[] = $post_type->cap->edit_published_posts;
					} else {
						$caps[] = $post_type->cap->edit_posts;
					}
				} else {
					// If the post is draft...
					$caps[] = $post_type->cap->edit_posts;
				}
			} else {
				// The user is trying to edit someone else's post.
				$caps[] = $post_type->cap->edit_others_posts;
				// The post is published or scheduled, extra cap required.
				if ( in_array( $post->post_status, array( 'publish', 'future' ), true ) ) {
					$caps[] = $post_type->cap->edit_published_posts;
				} elseif ( 'private' === $post->post_status ) {
					$caps[] = $post_type->cap->edit_private_posts;
				}
			}

			/*
			 * Setting the privacy policy page requires `manage_privacy_options`,
			 * so editing it should require that too.
			 */
			if ( (int) get_option( 'wp_page_for_privacy_policy' ) === $post->ID ) {
				$caps = array_merge( $caps, map_meta_cap( 'manage_privacy_options', $user_id ) );
			}

			break;
		case 'read_post':
		case 'read_page':
			$post = get_post( $args[0] );
			if ( ! $post ) {
				$caps[] = 'do_not_allow';
				break;
			}

			if ( 'revision' === $post->post_type ) {
				$post = get_post( $post->post_parent );
				if ( ! $post ) {
					$caps[] = 'do_not_allow';
					break;
				}
			}

			$post_type = get_post_type_object( $post->post_type );
			if ( ! $post_type ) {
				/* translators: 1: Post type, 2: Capability name. */
				_doing_it_wrong( __FUNCTION__, sprintf( __( 'The post type %1$s is not registered, so it may not be reliable to check the capability "%2$s" against a post of that type.' ), $post->post_type, $cap ), '4.4.0' );
				$caps[] = 'edit_others_posts';
				break;
			}

			if ( ! $post_type->map_meta_cap ) {
				$caps[] = $post_type->cap->$cap;
				// Prior to 3.1 we would re-call map_meta_cap here.
				if ( 'read_post' === $cap ) {
					$cap = $post_type->cap->$cap;
				}
				break;
			}

			$status_obj = get_post_status_object( $post->post_status );
			if ( ! $status_obj ) {
				/* translators: 1: Post status, 2: Capability name. */
				_doing_it_wrong( __FUNCTION__, sprintf( __( 'The post status %1$s is not registered, so it may not be reliable to check the capability "%2$s" against a post with that status.' ), $post->post_status, $cap ), '5.4.0' );
				$caps[] = 'edit_others_posts';
				break;
			}

			if ( $status_obj->public ) {
				$caps[] = $post_type->cap->read;
				break;
			}

			if ( $post->post_author && $user_id == $post->post_author ) {
				$caps[] = $post_type->cap->read;
			} elseif ( $status_obj->private ) {
				$caps[] = $post_type->cap->read_private_posts;
			} else {
				$caps = map_meta_cap( 'edit_post', $user_id, $post->ID );
			}
			break;
		case 'publish_post':
			$post = get_post( $args[0] );
			if ( ! $post ) {
				$caps[] = 'do_not_allow';
				break;
			}

			$post_type = get_post_type_object( $post->post_type );
			if ( ! $post_type ) {
				/* translators: 1: Post type, 2: Capability name. */
				_doing_it_wrong( __FUNCTION__, sprintf( __( 'The post type %1$s is not registered, so it may not be reliable to check the capability "%2$s" against a post of that type.' ), $post->post_type, $cap ), '4.4.0' );
				$caps[] = 'edit_others_posts';
				break;
			}

			$caps[] = $post_type->cap->publish_posts;
			break;
		case 'edit_post_meta':
		case 'delete_post_meta':
		case 'add_post_meta':
		case 'edit_comment_meta':
		case 'delete_comment_meta':
		case 'add_comment_meta':
		case 'edit_term_meta':
		case 'delete_term_meta':
		case 'add_term_meta':
		case 'edit_user_meta':
		case 'delete_user_meta':
		case 'add_user_meta':
			list( $_, $object_type, $_ ) = explode( '_', $cap );
			$object_id                   = (int) $args[0];

			$object_subtype = get_object_subtype( $object_type, $object_id );

			if ( empty( $object_subtype ) ) {
				$caps[] = 'do_not_allow';
				break;
			}

			$caps = map_meta_cap( "edit_{$object_type}", $user_id, $object_id );

			$meta_key = isset( $args[1] ) ? $args[1] : false;

			if ( $meta_key ) {
				$allowed = ! is_protected_meta( $meta_key, $object_type );

				if ( ! empty( $object_subtype ) && has_filter( "auth_{$object_type}_meta_{$meta_key}_for_{$object_subtype}" ) ) {

					/**
					 * Filters whether the user is allowed to edit a specific meta key of a specific object type and subtype.
					 *
					 * The dynamic portions of the hook name, `$object_type`, `$meta_key`,
					 * and `$object_subtype`, refer to the metadata object type (comment, post, term or user),
					 * the meta key value, and the object subtype respectively.
					 *
					 * @since 4.9.8
					 *
					 * @param bool     $allowed   Whether the user can add the object meta. Default false.
					 * @param string   $meta_key  The meta key.
					 * @param int      $object_id Object ID.
					 * @param int      $user_id   User ID.
					 * @param string   $cap       Capability name.
					 * @param string[] $caps      Array of the user's capabilities.
					 */
					$allowed = apply_filters( "auth_{$object_type}_meta_{$meta_key}_for_{$object_subtype}", $allowed, $meta_key, $object_id, $user_id, $cap, $caps );
				} else {

					/**
					 * Filters whether the user is allowed to edit a specific meta key of a specific object type.
					 *
					 * Return true to have the mapped meta caps from `edit_{$object_type}` apply.
					 *
					 * The dynamic portion of the hook name, `$object_type` refers to the object type being filtered.
					 * The dynamic portion of the hook name, `$meta_key`, refers to the meta key passed to map_meta_cap().
					 *
					 * @since 3.3.0 As `auth_post_meta_{$meta_key}`.
					 * @since 4.6.0
					 *
					 * @param bool     $allowed   Whether the user can add the object meta. Default false.
					 * @param string   $meta_key  The meta key.
					 * @param int      $object_id Object ID.
					 * @param int      $user_id   User ID.
					 * @param string   $cap       Capability name.
					 * @param string[] $caps      Array of the user's capabilities.
					 */
					$allowed = apply_filters( "auth_{$object_type}_meta_{$meta_key}", $allowed, $meta_key, $object_id, $user_id, $cap, $caps );
				}

				if ( ! empty( $object_subtype ) ) {

					/**
					 * Filters whether the user is allowed to edit meta for specific object types/subtypes.
					 *
					 * Return true to have the mapped meta caps from `edit_{$object_type}` apply.
					 *
					 * The dynamic portion of the hook name, `$object_type` refers to the object type being filtered.
					 * The dynamic portion of the hook name, `$object_subtype` refers to the object subtype being filtered.
					 * The dynamic portion of the hook name, `$meta_key`, refers to the meta key passed to map_meta_cap().
					 *
					 * @since 4.6.0 As `auth_post_{$post_type}_meta_{$meta_key}`.
					 * @since 4.7.0 Renamed from `auth_post_{$post_type}_meta_{$meta_key}` to
					 *              `auth_{$object_type}_{$object_subtype}_meta_{$meta_key}`.
					 * @deprecated 4.9.8 Use {@see 'auth_{$object_type}_meta_{$meta_key}_for_{$object_subtype}'} instead.
					 *
					 * @param bool     $allowed   Whether the user can add the object meta. Default false.
					 * @param string   $meta_key  The meta key.
					 * @param int      $object_id Object ID.
					 * @param int      $user_id   User ID.
					 * @param string   $cap       Capability name.
					 * @param string[] $caps      Array of the user's capabilities.
					 */
					$allowed = apply_filters_deprecated( "auth_{$object_type}_{$object_subtype}_meta_{$meta_key}", array( $allowed, $meta_key, $object_id, $user_id, $cap, $caps ), '4.9.8', "auth_{$object_type}_meta_{$meta_key}_for_{$object_subtype}" );
				}

				if ( ! $allowed ) {
					$caps[] = $cap;
				}
			}
			break;
		case 'edit_comment':
			$comment = get_comment( $args[0] );
			if ( ! $comment ) {
				$caps[] = 'do_not_allow';
				break;
			}

			$post = get_post( $comment->comment_post_ID );

			/*
			 * If the post doesn't exist, we have an orphaned comment.
			 * Fall back to the edit_posts capability, instead.
			 */
			if ( $post ) {
				$caps = map_meta_cap( 'edit_post', $user_id, $post->ID );
			} else {
				$caps = map_meta_cap( 'edit_posts', $user_id );
			}
			break;
		case 'unfiltered_upload':
			if ( defined( 'ALLOW_UNFILTERED_UPLOADS' ) && ALLOW_UNFILTERED_UPLOADS && ( ! is_multisite() || is_super_admin( $user_id ) ) ) {
				$caps[] = $cap;
			} else {
				$caps[] = 'do_not_allow';
			}
			break;
		case 'edit_css':
		case 'unfiltered_html':
			// Disallow unfiltered_html for all users, even admins and super admins.
			if ( defined( 'DISALLOW_UNFILTERED_HTML' ) && DISALLOW_UNFILTERED_HTML ) {
				$caps[] = 'do_not_allow';
			} elseif ( is_multisite() && ! is_super_admin( $user_id ) ) {
				$caps[] = 'do_not_allow';
			} else {
				$caps[] = 'unfiltered_html';
			}
			break;
		case 'edit_files':
		case 'edit_plugins':
		case 'edit_themes':
			// Disallow the file editors.
			if ( defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT ) {
				$caps[] = 'do_not_allow';
			} elseif ( ! wp_is_file_mod_allowed( 'capability_edit_themes' ) ) {
				$caps[] = 'do_not_allow';
			} elseif ( is_multisite() && ! is_super_admin( $user_id ) ) {
				$caps[] = 'do_not_allow';
			} else {
				$caps[] = $cap;
			}
			break;
		case 'update_plugins':
		case 'delete_plugins':
		case 'install_plugins':
		case 'upload_plugins':
		case 'update_themes':
		case 'delete_themes':
		case 'install_themes':
		case 'upload_themes':
		case 'update_core':
			// Disallow anything that creates, deletes, or updates core, plugin, or theme files.
			// Files in uploads are excepted.
			if ( ! wp_is_file_mod_allowed( 'capability_update_core' ) ) {
				$caps[] = 'do_not_allow';
			} elseif ( is_multisite() && ! is_super_admin( $user_id ) ) {
				$caps[] = 'do_not_allow';
			} elseif ( 'upload_themes' === $cap ) {
				$caps[] = 'install_themes';
			} elseif ( 'upload_plugins' === $cap ) {
				$caps[] = 'install_plugins';
			} else {
				$caps[] = $cap;
			}
			break;
		case 'install_languages':
		case 'update_languages':
			if ( ! wp_is_file_mod_allowed( 'can_install_language_pack' ) ) {
				$caps[] = 'do_not_allow';
			} elseif ( is_multisite() && ! is_super_admin( $user_id ) ) {
				$caps[] = 'do_not_allow';
			} else {
				$caps[] = 'install_languages';
			}
			break;
		case 'activate_plugins':
		case 'deactivate_plugins':
		case 'activate_plugin':
		case 'deactivate_plugin':
			$caps[] = 'activate_plugins';
			if ( is_multisite() ) {
				// update_, install_, and delete_ are handled above with is_super_admin().
				$menu_perms = get_site_option( 'menu_items', array() );
				if ( empty( $menu_perms['plugins'] ) ) {
					$caps[] = 'manage_network_plugins';
				}
			}
			break;
		case 'resume_plugin':
			$caps[] = 'resume_plugins';
			break;
		case 'resume_theme':
			$caps[] = 'resume_themes';
			break;
		case 'delete_user':
		case 'delete_users':
			// If multisite only super admins can delete users.
			if ( is_multisite() && ! is_super_admin( $user_id ) ) {
				$caps[] = 'do_not_allow';
			} else {
				$caps[] = 'delete_users'; // delete_user maps to delete_users.
			}
			break;
		case 'create_users':
			if ( ! is_multisite() ) {
				$caps[] = $cap;
			} elseif ( is_super_admin( $user_id ) || get_site_option( 'add_new_users' ) ) {
				$caps[] = $cap;
			} else {
				$caps[] = 'do_not_allow';
			}
			break;
		case 'manage_links':
			if ( get_option( 'link_manager_enabled' ) ) {
				$caps[] = $cap;
			} else {
				$caps[] = 'do_not_allow';
			}
			break;
		case 'customize':
			$caps[] = 'edit_theme_options';
			break;
		case 'delete_site':
			if ( is_multisite() ) {
				$caps[] = 'manage_options';
			} else {
				$caps[] = 'do_not_allow';
			}
			break;
		case 'edit_term':
		case 'delete_term':
		case 'assign_term':
			$term_id = (int) $args[0];
			$term    = get_term( $term_id );
			if ( ! $term || is_wp_error( $term ) ) {
				$caps[] = 'do_not_allow';
				break;
			}

			$tax = get_taxonomy( $term->taxonomy );
			if ( ! $tax ) {
				$caps[] = 'do_not_allow';
				break;
			}

			if ( 'delete_term' === $cap && ( get_option( 'default_' . $term->taxonomy ) == $term->term_id ) ) {
				$caps[] = 'do_not_allow';
				break;
			}

			$taxo_cap = $cap . 's';

			$caps = map_meta_cap( $tax->cap->$taxo_cap, $user_id, $term_id );

			break;
		case 'manage_post_tags':
		case 'edit_categories':
		case 'edit_post_tags':
		case 'delete_categories':
		case 'delete_post_tags':
			$caps[] = 'manage_categories';
			break;
		case 'assign_categories':
		case 'assign_post_tags':
			$caps[] = 'edit_posts';
			break;
		case 'create_sites':
		case 'delete_sites':
		case 'manage_network':
		case 'manage_sites':
		case 'manage_network_users':
		case 'manage_network_plugins':
		case 'manage_network_themes':
		case 'manage_network_options':
		case 'upgrade_network':
			$caps[] = $cap;
			break;
		case 'setup_network':
			if ( is_multisite() ) {
				$caps[] = 'manage_network_options';
			} else {
				$caps[] = 'manage_options';
			}
			break;
		case 'update_php':
			if ( is_multisite() && ! is_super_admin( $user_id ) ) {
				$caps[] = 'do_not_allow';
			} else {
				$caps[] = 'update_core';
			}
			break;
		case 'export_others_personal_data':
		case 'erase_others_personal_data':
		case 'manage_privacy_options':
			$caps[] = is_multisite() ? 'manage_network' : 'manage_options';
			break;
		default:
			// Handle meta capabilities for custom post types.
			global $post_type_meta_caps;
			if ( isset( $post_type_meta_caps[ $cap ] ) ) {
				return map_meta_cap( $post_type_meta_caps[ $cap ], $user_id, ...$args );
			}

			// Block capabilities map to their post equivalent.
			$block_caps = array(
				'edit_blocks',
				'edit_others_blocks',
				'publish_blocks',
				'read_private_blocks',
				'delete_blocks',
				'delete_private_blocks',
				'delete_published_blocks',
				'delete_others_blocks',
				'edit_private_blocks',
				'edit_published_blocks',
			);
			if ( in_array( $cap, $block_caps, true ) ) {
				$cap = str_replace( '_blocks', '_posts', $cap );
			}

			// If no meta caps match, return the original cap.
			$caps[] = $cap;
	}

	/**
	 * Filters a user's capabilities depending on specific context and/or privilege.
	 *
	 * @since 2.8.0
	 *
	 * @param string[] $caps    Array of the user's capabilities.
	 * @param string   $cap     Capability name.
	 * @param int      $user_id The user ID.
	 * @param array    $args    Adds the context to the cap. Typically the object ID.
	 */
	return apply_filters( 'map_meta_cap', $caps, $cap, $user_id, $args );
}

/**
 * Returns whether the current user has the specified capability.
 *
 * This function also accepts an ID of an object to check against if the capability is a meta capability. Meta
 * capabilities such as `edit_post` and `edit_user` are capabilities used by the `map_meta_cap()` function to
 * map to primitive capabilities that a user or role has, such as `edit_posts` and `edit_others_posts`.
 *
 * Example usage:
 *
 *     current_user_can( 'edit_posts' );
 *     current_user_can( 'edit_post', $post->ID );
 *     current_user_can( 'edit_post_meta', $post->ID, $meta_key );
 *
 * While checking against particular roles in place of a capability is supported
 * in part, this practice is discouraged as it may produce unreliable results.
 *
 * Note: Will always return true if the current user is a super admin, unless specifically denied.
 *
 * @since 2.0.0
 * @since 5.3.0 Formalized the existing and already documented `...$args` parameter
 *              by adding it to the function signature.
 *
 * @see WP_User::has_cap()
 * @see map_meta_cap()
 *
 * @param string $capability Capability name.
 * @param mixed  ...$args    Optional further parameters, typically starting with an object ID.
 * @return bool Whether the current user has the given capability. If `$capability` is a meta cap and `$object_id` is
 *              passed, whether the current user has the given meta capability for the given object.
 */
function current_user_can( $capability, ...$args ) {
	$current_user = wp_get_current_user();

	if ( empty( $current_user ) ) {
		return false;
	}

	return $current_user->has_cap( $capability, ...$args );
}

/**
 * Returns whether the current user has the specified capability for a given site.
 *
 * This function also accepts an ID of an object to check against if the capability is a meta capability. Meta
 * capabilities such as `edit_post` and `edit_user` are capabilities used by the `map_meta_cap()` function to
 * map to primitive capabilities that a user or role has, such as `edit_posts` and `edit_others_posts`.
 *
 * Example usage:
 *
 *     current_user_can_for_blog( $blog_id, 'edit_posts' );
 *     current_user_can_for_blog( $blog_id, 'edit_post', $post->ID );
 *     current_user_can_for_blog( $blog_id, 'edit_post_meta', $post->ID, $meta_key );
 *
 * @since 3.0.0
 * @since 5.3.0 Formalized the existing and already documented `...$args` parameter
 *              by adding it to the function signature.
 *
 * @param int    $blog_id    Site ID.
 * @param string $capability Capability name.
 * @param mixed  ...$args    Optional further parameters, typically starting with an object ID.
 * @return bool Whether the user has the given capability.
 */
function current_user_can_for_blog( $blog_id, $capability, ...$args ) {
	$switched = is_multisite() ? switch_to_blog( $blog_id ) : false;

	$current_user = wp_get_current_user();

	if ( empty( $current_user ) ) {
		if ( $switched ) {
			restore_current_blog();
		}
		return false;
	}

	$can = $current_user->has_cap( $capability, ...$args );

	if ( $switched ) {
		restore_current_blog();
	}

	return $can;
}

/**
 * Returns whether the author of the supplied post has the specified capability.
 *
 * This function also accepts an ID of an object to check against if the capability is a meta capability. Meta
 * capabilities such as `edit_post` and `edit_user` are capabilities used by the `map_meta_cap()` function to
 * map to primitive capabilities that a user or role has, such as `edit_posts` and `edit_others_posts`.
 *
 * Example usage:
 *
 *     author_can( $post, 'edit_posts' );
 *     author_can( $post, 'edit_post', $post->ID );
 *     author_can( $post, 'edit_post_meta', $post->ID, $meta_key );
 *
 * @since 2.9.0
 * @since 5.3.0 Formalized the existing and already documented `...$args` parameter
 *              by adding it to the function signature.
 *
 * @param int|WP_Post $post       Post ID or post object.
 * @param string      $capability Capability name.
 * @param mixed       ...$args    Optional further parameters, typically starting with an object ID.
 * @return bool Whether the post author has the given capability.
 */
function author_can( $post, $capability, ...$args ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return false;
	}

	$author = get_userdata( $post->post_author );

	if ( ! $author ) {
		return false;
	}

	return $author->has_cap( $capability, ...$args );
}

/**
 * Returns whether a particular user has the specified capability.
 *
 * This function also accepts an ID of an object to check against if the capability is a meta capability. Meta
 * capabilities such as `edit_post` and `edit_user` are capabilities used by the `map_meta_cap()` function to
 * map to primitive capabilities that a user or role has, such as `edit_posts` and `edit_others_posts`.
 *
 * Example usage:
 *
 *     user_can( $user->ID, 'edit_posts' );
 *     user_can( $user->ID, 'edit_post', $post->ID );
 *     user_can( $user->ID, 'edit_post_meta', $post->ID, $meta_key );
 *
 * @since 3.1.0
 * @since 5.3.0 Formalized the existing and already documented `...$args` parameter
 *              by adding it to the function signature.
 *
 * @param int|WP_User $user       User ID or object.
 * @param string      $capability Capability name.
 * @param mixed       ...$args    Optional further parameters, typically starting with an object ID.
 * @return bool Whether the user has the given capability.
 */
function user_can( $user, $capability, ...$args ) {
	if ( ! is_object( $user ) ) {
		$user = get_userdata( $user );
	}

	if ( ! $user || ! $user->exists() ) {
		return false;
	}

	return $user->has_cap( $capability, ...$args );
}

/**
 * Retrieves the global WP_Roles instance and instantiates it if necessary.
 *
 * @since 4.3.0
 *
 * @global WP_Roles $wp_roles WordPress role management object.
 *
 * @return WP_Roles WP_Roles global instance if not already instantiated.
 */
function wp_roles() {
	global $wp_roles;

	if ( ! isset( $wp_roles ) ) {
		$wp_roles = new WP_Roles();
	}
	return $wp_roles;
}

/**
 * Retrieve role object.
 *
 * @since 2.0.0
 *
 * @param string $role Role name.
 * @return WP_Role|null WP_Role object if found, null if the role does not exist.
 */
function get_role( $role ) {
	return wp_roles()->get_role( $role );
}

/**
 * Add role, if it does not exist.
 *
 * @since 2.0.0
 *
 * @param string $role         Role name.
 * @param string $display_name Display name for role.
 * @param bool[] $capabilities List of capabilities keyed by the capability name,
 *                             e.g. array( 'edit_posts' => true, 'delete_posts' => false ).
 * @return WP_Role|null WP_Role object if role is added, null if already exists.
 */
function add_role( $role, $display_name, $capabilities = array() ) {
	if ( empty( $role ) ) {
		return;
	}
	return wp_roles()->add_role( $role, $display_name, $capabilities );
}

/**
 * Remove role, if it exists.
 *
 * @since 2.0.0
 *
 * @param string $role Role name.
 */
function remove_role( $role ) {
	wp_roles()->remove_role( $role );
}

/**
 * Retrieve a list of super admins.
 *
 * @since 3.0.0
 *
 * @global array $super_admins
 *
 * @return string[] List of super admin logins.
 */
function get_super_admins() {
	global $super_admins;

	if ( isset( $super_admins ) ) {
		return $super_admins;
	} else {
		return get_site_option( 'site_admins', array( 'admin' ) );
	}
}

/**
 * Determine if user is a site admin.
 *
 * @since 3.0.0
 *
 * @param int $user_id (Optional) The ID of a user. Defaults to the current user.
 * @return bool True if the user is a site admin.
 */
function is_super_admin( $user_id = false ) {
	if ( ! $user_id || get_current_user_id() == $user_id ) {
		$user = wp_get_current_user();
	} else {
		$user = get_userdata( $user_id );
	}

	if ( ! $user || ! $user->exists() ) {
		return false;
	}

	if ( is_multisite() ) {
		$super_admins = get_super_admins();
		if ( is_array( $super_admins ) && in_array( $user->user_login, $super_admins, true ) ) {
			return true;
		}
	} else {
		if ( $user->has_cap( 'delete_users' ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Grants Super Admin privileges.
 *
 * @since 3.0.0
 *
 * @global array $super_admins
 *
 * @param int $user_id ID of the user to be granted Super Admin privileges.
 * @return bool True on success, false on failure. This can fail when the user is
 *              already a super admin or when the `$super_admins` global is defined.
 */
function grant_super_admin( $user_id ) {
	// If global super_admins override is defined, there is nothing to do here.
	if ( isset( $GLOBALS['super_admins'] ) || ! is_multisite() ) {
		return false;
	}

	/**
	 * Fires before the user is granted Super Admin privileges.
	 *
	 * @since 3.0.0
	 *
	 * @param int $user_id ID of the user that is about to be granted Super Admin privileges.
	 */
	do_action( 'grant_super_admin', $user_id );

	// Directly fetch site_admins instead of using get_super_admins().
	$super_admins = get_site_option( 'site_admins', array( 'admin' ) );

	$user = get_userdata( $user_id );
	if ( $user && ! in_array( $user->user_login, $super_admins, true ) ) {
		$super_admins[] = $user->user_login;
		update_site_option( 'site_admins', $super_admins );

		/**
		 * Fires after the user is granted Super Admin privileges.
		 *
		 * @since 3.0.0
		 *
		 * @param int $user_id ID of the user that was granted Super Admin privileges.
		 */
		do_action( 'granted_super_admin', $user_id );
		return true;
	}
	return false;
}

/**
 * Revokes Super Admin privileges.
 *
 * @since 3.0.0
 *
 * @global array $super_admins
 *
 * @param int $user_id ID of the user Super Admin privileges to be revoked from.
 * @return bool True on success, false on failure. This can fail when the user's email
 *              is the network admin email or when the `$super_admins` global is defined.
 */
function revoke_super_admin( $user_id ) {
	// If global super_admins override is defined, there is nothing to do here.
	if ( isset( $GLOBALS['super_admins'] ) || ! is_multisite() ) {
		return false;
	}

	/**
	 * Fires before the user's Super Admin privileges are revoked.
	 *
	 * @since 3.0.0
	 *
	 * @param int $user_id ID of the user Super Admin privileges are being revoked from.
	 */
	do_action( 'revoke_super_admin', $user_id );

	// Directly fetch site_admins instead of using get_super_admins().
	$super_admins = get_site_option( 'site_admins', array( 'admin' ) );

	$user = get_userdata( $user_id );
	if ( $user && 0 !== strcasecmp( $user->user_email, get_site_option( 'admin_email' ) ) ) {
		$key = array_search( $user->user_login, $super_admins, true );
		if ( false !== $key ) {
			unset( $super_admins[ $key ] );
			update_site_option( 'site_admins', $super_admins );

			/**
			 * Fires after the user's Super Admin privileges are revoked.
			 *
			 * @since 3.0.0
			 *
			 * @param int $user_id ID of the user Super Admin privileges were revoked from.
			 */
			do_action( 'revoked_super_admin', $user_id );
			return true;
		}
	}
	return false;
}

/**
 * Filters the user capabilities to grant the 'install_languages' capability as necessary.
 *
 * A user must have at least one out of the 'update_core', 'install_plugins', and
 * 'install_themes' capabilities to qualify for 'install_languages'.
 *
 * @since 4.9.0
 *
 * @param bool[] $allcaps An array of all the user's capabilities.
 * @return bool[] Filtered array of the user's capabilities.
 */
function wp_maybe_grant_install_languages_cap( $allcaps ) {
	if ( ! empty( $allcaps['update_core'] ) || ! empty( $allcaps['install_plugins'] ) || ! empty( $allcaps['install_themes'] ) ) {
		$allcaps['install_languages'] = true;
	}

	return $allcaps;
}

/**
 * Filters the user capabilities to grant the 'resume_plugins' and 'resume_themes' capabilities as necessary.
 *
 * @since 5.2.0
 *
 * @param bool[] $allcaps An array of all the user's capabilities.
 * @return bool[] Filtered array of the user's capabilities.
 */
function wp_maybe_grant_resume_extensions_caps( $allcaps ) {
	// Even in a multisite, regular administrators should be able to resume plugins.
	if ( ! empty( $allcaps['activate_plugins'] ) ) {
		$allcaps['resume_plugins'] = true;
	}

	// Even in a multisite, regular administrators should be able to resume themes.
	if ( ! empty( $allcaps['switch_themes'] ) ) {
		$allcaps['resume_themes'] = true;
	}

	return $allcaps;
}

/**
 * Filters the user capabilities to grant the 'view_site_health_checks' capabilities as necessary.
 *
 * @since 5.2.2
 *
 * @param bool[]   $allcaps An array of all the user's capabilities.
 * @param string[] $caps    Required primitive capabilities for the requested capability.
 * @param array    $args {
 *     Arguments that accompany the requested capability check.
 *
 *     @type string    $0 Requested capability.
 *     @type int       $1 Concerned user ID.
 *     @type mixed  ...$2 Optional second and further parameters, typically object ID.
 * }
 * @param WP_User  $user    The user object.
 * @return bool[] Filtered array of the user's capabilities.
 */
function wp_maybe_grant_site_health_caps( $allcaps, $caps, $args, $user ) {
	if ( ! empty( $allcaps['install_plugins'] ) && ( ! is_multisite() || is_super_admin( $user->ID ) ) ) {
		$allcaps['view_site_health_checks'] = true;
	}

	return $allcaps;
}

return;

// Dummy gettext calls to get strings in the catalog.
/* translators: User role for administrators. */
_x( 'Administrator', 'User role' );
/* translators: User role for editors. */
_x( 'Editor', 'User role' );
/* translators: User role for authors. */
_x( 'Author', 'User role' );
/* translators: User role for contributors. */
_x( 'Contributor', 'User role' );
/* translators: User role for subscribers. */
_x( 'Subscriber', 'User role' );
