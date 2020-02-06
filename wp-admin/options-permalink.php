<?php
/**
 * Permalink Settings Administration Screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( __( 'Sorry, you are not allowed to manage options for this site.' ) );
}

$title       = __( 'Permalink Settings' );
$parent_file = 'options-general.php';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( 'Overview' ),
		'content' => '<p>' . __( 'Permalinks are the permanent URLs to your individual pages and blog posts, as well as your category and tag archives. A permalink is the web address used to link to your content. The URL to each post should be permanent, and never change &#8212; hence the name permalink.' ) . '</p>' .
			'<p>' . __( 'This screen allows you to choose your permalink structure. You can choose from common settings or create custom URL structures.' ) . '</p>' .
			'<p>' . __( 'You must click the Save Changes button at the bottom of the screen for new settings to take effect.' ) . '</p>',
	)
);

get_current_screen()->add_help_tab(
	array(
		'id'      => 'permalink-settings',
		'title'   => __( 'Permalink Settings' ),
		'content' => '<p>' . __( 'Permalinks can contain useful information, such as the post date, title, or other elements. You can choose from any of the suggested permalink formats, or you can craft your own if you select Custom Structure.' ) . '</p>' .
			'<p>' . sprintf(
				/* translators: %s: Percent sign (%). */
				__( 'If you pick an option other than Plain, your general URL path with structure tags (terms surrounded by %s) will also appear in the custom structure field and your path can be further modified there.' ),
				'<code>%</code>'
			) . '</p>' .
			'<p>' . sprintf(
				/* translators: 1: %category%, 2: %tag% */
				__( 'When you assign multiple categories or tags to a post, only one can show up in the permalink: the lowest numbered category. This applies if your custom structure includes %1$s or %2$s.' ),
				'<code>%category%</code>',
				'<code>%tag%</code>'
			) . '</p>' .
			'<p>' . __( 'You must click the Save Changes button at the bottom of the screen for new settings to take effect.' ) . '</p>',
	)
);

get_current_screen()->add_help_tab(
	array(
		'id'      => 'custom-structures',
		'title'   => __( 'Custom Structures' ),
		'content' => '<p>' . __( 'The Optional fields let you customize the &#8220;category&#8221; and &#8220;tag&#8221; base names that will appear in archive URLs. For example, the page listing all posts in the &#8220;Uncategorized&#8221; category could be <code>/topics/uncategorized</code> instead of <code>/category/uncategorized</code>.' ) . '</p>' .
			'<p>' . __( 'You must click the Save Changes button at the bottom of the screen for new settings to take effect.' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/article/settings-permalinks-screen/">Documentation on Permalinks Settings</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/article/using-permalinks/">Documentation on Using Permalinks</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/">Support</a>' ) . '</p>'
);

$home_path           = get_home_path();
$iis7_permalinks     = iis7_supports_permalinks();
$permalink_structure = get_option( 'permalink_structure' );

$prefix      = '';
$blog_prefix = '';
if ( ! got_url_rewrite() ) {
	$prefix = '/index.php';
}

/**
 * In a subdirectory configuration of multisite, the `/blog` prefix is used by
 * default on the main site to avoid collisions with other sites created on that
 * network. If the `permalink_structure` option has been changed to remove this
 * base prefix, WordPress core can no longer account for the possible collision.
 */
if ( is_multisite() && ! is_subdomain_install() && is_main_site() && 0 === strpos( $permalink_structure, '/blog/' ) ) {
	$blog_prefix = '/blog';
}

$category_base   = get_option( 'category_base' );
$tag_base        = get_option( 'tag_base' );
$update_required = false;

if ( $iis7_permalinks ) {
	if ( ( ! file_exists( $home_path . 'web.config' ) && win_is_writable( $home_path ) ) || win_is_writable( $home_path . 'web.config' ) ) {
		$writable = true;
	} else {
		$writable = false;
	}
} elseif ( $is_nginx ) {
	$writable = false;
} else {
	if ( ( ! file_exists( $home_path . '.htaccess' ) && is_writable( $home_path ) ) || is_writable( $home_path . '.htaccess' ) ) {
		$writable = true;
	} else {
		$writable        = false;
		$existing_rules  = array_filter( extract_from_markers( $home_path . '.htaccess', 'WordPress' ) );
		$new_rules       = array_filter( explode( "\n", $wp_rewrite->mod_rewrite_rules() ) );
		$update_required = ( $new_rules !== $existing_rules );
	}
}

$using_index_permalinks = $wp_rewrite->using_index_permalinks();

if ( isset( $_POST['permalink_structure'] ) || isset( $_POST['category_base'] ) ) {
	check_admin_referer( 'update-permalink' );

	if ( isset( $_POST['permalink_structure'] ) ) {
		if ( isset( $_POST['selection'] ) && 'custom' != $_POST['selection'] ) {
			$permalink_structure = $_POST['selection'];
		} else {
			$permalink_structure = $_POST['permalink_structure'];
		}

		if ( ! empty( $permalink_structure ) ) {
			$permalink_structure = preg_replace( '#/+#', '/', '/' . str_replace( '#', '', $permalink_structure ) );
			if ( $prefix && $blog_prefix ) {
				$permalink_structure = $prefix . preg_replace( '#^/?index\.php#', '', $permalink_structure );
			} else {
				$permalink_structure = $blog_prefix . $permalink_structure;
			}
		}

		$permalink_structure = sanitize_option( 'permalink_structure', $permalink_structure );

		$wp_rewrite->set_permalink_structure( $permalink_structure );
	}

	if ( isset( $_POST['category_base'] ) ) {
		$category_base = $_POST['category_base'];
		if ( ! empty( $category_base ) ) {
			$category_base = $blog_prefix . preg_replace( '#/+#', '/', '/' . str_replace( '#', '', $category_base ) );
		}
		$wp_rewrite->set_category_base( $category_base );
	}

	if ( isset( $_POST['tag_base'] ) ) {
		$tag_base = $_POST['tag_base'];
		if ( ! empty( $tag_base ) ) {
			$tag_base = $blog_prefix . preg_replace( '#/+#', '/', '/' . str_replace( '#', '', $tag_base ) );
		}
		$wp_rewrite->set_tag_base( $tag_base );
	}

	$message = __( 'Permalink structure updated.' );

	if ( $iis7_permalinks ) {
		if ( $permalink_structure && ! $using_index_permalinks && ! $writable ) {
			$message = sprintf(
				/* translators: %s: web.config */
				__( 'You should update your %s file now.' ),
				'<code>web.config</code>'
			);
		} elseif ( $permalink_structure && ! $using_index_permalinks && $writable ) {
			$message = sprintf(
				/* translators: %s: web.config */
				__( 'Permalink structure updated. Remove write access on %s file now!' ),
				'<code>web.config</code>'
			);
		}
	} elseif ( ! $is_nginx && $permalink_structure && ! $using_index_permalinks && ! $writable && $update_required ) {
		$message = sprintf(
			/* translators: %s: .htaccess */
			__( 'You should update your %s file now.' ),
			'<code>.htaccess</code>'
		);
	}

	if ( ! get_settings_errors() ) {
		add_settings_error( 'general', 'settings_updated', $message, 'success' );
	}

	set_transient( 'settings_errors', get_settings_errors(), 30 );

	wp_redirect( admin_url( 'options-permalink.php?settings-updated=true' ) );
	exit;
}

flush_rewrite_rules();

require_once ABSPATH . 'wp-admin/admin-header.php';
?>
<div class="wrap">
<h1><?php echo esc_html( $title ); ?></h1>

<form name="form" action="options-permalink.php" method="post">
<?php wp_nonce_field( 'update-permalink' ); ?>

	<p>
	<?php
		printf(
			/* translators: %s: Documentation URL. */
			__( 'WordPress offers you the ability to create a custom URL structure for your permalinks and archives. Custom URL structures can improve the aesthetics, usability, and forward-compatibility of your links. A <a href="%s">number of tags are available</a>, and here are some examples to get you started.' ),
			__( 'https://wordpress.org/support/article/using-permalinks/' )
		);
		?>
	</p>

<?php
if ( is_multisite() && ! is_subdomain_install() && is_main_site() && 0 === strpos( $permalink_structure, '/blog/' ) ) {
	$permalink_structure = preg_replace( '|^/?blog|', '', $permalink_structure );
	$category_base       = preg_replace( '|^/?blog|', '', $category_base );
	$tag_base            = preg_replace( '|^/?blog|', '', $tag_base );
}

$structures = array(
	0 => '',
	1 => $prefix . '/%year%/%monthnum%/%day%/%postname%/',
	2 => $prefix . '/%year%/%monthnum%/%postname%/',
	3 => $prefix . '/' . _x( 'archives', 'sample permalink base' ) . '/%post_id%',
	4 => $prefix . '/%postname%/',
);
?>
<h2 class="title"><?php _e( 'Common Settings' ); ?></h2>
<table class="form-table permalink-structure">
	<tr>
		<th scope="row"><label><input name="selection" type="radio" value="" <?php checked( '', $permalink_structure ); ?> /> <?php _e( 'Plain' ); ?></label></th>
		<td><code><?php echo get_option( 'home' ); ?>/?p=123</code></td>
	</tr>
	<tr>
		<th scope="row"><label><input name="selection" type="radio" value="<?php echo esc_attr( $structures[1] ); ?>" <?php checked( $structures[1], $permalink_structure ); ?> /> <?php _e( 'Day and name' ); ?></label></th>
		<td><code><?php echo get_option( 'home' ) . $blog_prefix . $prefix . '/' . gmdate( 'Y' ) . '/' . gmdate( 'm' ) . '/' . gmdate( 'd' ) . '/' . _x( 'sample-post', 'sample permalink structure' ) . '/'; ?></code></td>
	</tr>
	<tr>
		<th scope="row"><label><input name="selection" type="radio" value="<?php echo esc_attr( $structures[2] ); ?>" <?php checked( $structures[2], $permalink_structure ); ?> /> <?php _e( 'Month and name' ); ?></label></th>
		<td><code><?php echo get_option( 'home' ) . $blog_prefix . $prefix . '/' . gmdate( 'Y' ) . '/' . gmdate( 'm' ) . '/' . _x( 'sample-post', 'sample permalink structure' ) . '/'; ?></code></td>
	</tr>
	<tr>
		<th scope="row"><label><input name="selection" type="radio" value="<?php echo esc_attr( $structures[3] ); ?>" <?php checked( $structures[3], $permalink_structure ); ?> /> <?php _e( 'Numeric' ); ?></label></th>
		<td><code><?php echo get_option( 'home' ) . $blog_prefix . $prefix . '/' . _x( 'archives', 'sample permalink base' ) . '/123'; ?></code></td>
	</tr>
	<tr>
		<th scope="row"><label><input name="selection" type="radio" value="<?php echo esc_attr( $structures[4] ); ?>" <?php checked( $structures[4], $permalink_structure ); ?> /> <?php _e( 'Post name' ); ?></label></th>
		<td><code><?php echo get_option( 'home' ) . $blog_prefix . $prefix . '/' . _x( 'sample-post', 'sample permalink structure' ) . '/'; ?></code></td>
	</tr>
	<tr>
		<th scope="row">
			<label><input name="selection" id="custom_selection" type="radio" value="custom" <?php checked( ! in_array( $permalink_structure, $structures ) ); ?> />
			<?php _e( 'Custom Structure' ); ?>
			</label>
		</th>
		<td>
			<code><?php echo get_option( 'home' ) . $blog_prefix; ?></code>
			<input name="permalink_structure" id="permalink_structure" type="text" value="<?php echo esc_attr( $permalink_structure ); ?>" class="regular-text code" />
			<div class="available-structure-tags hide-if-no-js">
				<div id="custom_selection_updated" aria-live="assertive" class="screen-reader-text"></div>
				<?php
				$available_tags = array(
					/* translators: %s: Permalink structure tag. */
					'year'     => __( '%s (The year of the post, four digits, for example 2004.)' ),
					/* translators: %s: Permalink structure tag. */
					'monthnum' => __( '%s (Month of the year, for example 05.)' ),
					/* translators: %s: Permalink structure tag. */
					'day'      => __( '%s (Day of the month, for example 28.)' ),
					/* translators: %s: Permalink structure tag. */
					'hour'     => __( '%s (Hour of the day, for example 15.)' ),
					/* translators: %s: Permalink structure tag. */
					'minute'   => __( '%s (Minute of the hour, for example 43.)' ),
					/* translators: %s: Permalink structure tag. */
					'second'   => __( '%s (Second of the minute, for example 33.)' ),
					/* translators: %s: Permalink structure tag. */
					'post_id'  => __( '%s (The unique ID of the post, for example 423.)' ),
					/* translators: %s: Permalink structure tag. */
					'postname' => __( '%s (The sanitized post title (slug).)' ),
					/* translators: %s: Permalink structure tag. */
					'category' => __( '%s (Category slug. Nested sub-categories appear as nested directories in the URL.)' ),
					/* translators: %s: Permalink structure tag. */
					'author'   => __( '%s (A sanitized version of the author name.)' ),
				);

				/**
				 * Filters the list of available permalink structure tags on the Permalinks settings page.
				 *
				 * @since 4.8.0
				 *
				 * @param string[] $available_tags An array of key => value pairs of available permalink structure tags.
				 */
				$available_tags = apply_filters( 'available_permalink_structure_tags', $available_tags );

				/* translators: %s: Permalink structure tag. */
				$structure_tag_added = __( '%s added to permalink structure' );

				/* translators: %s: Permalink structure tag. */
				$structure_tag_already_used = __( '%s (already used in permalink structure)' );

				if ( ! empty( $available_tags ) ) :
					?>
					<p><?php _e( 'Available tags:' ); ?></p>
					<ul role="list">
						<?php
						foreach ( $available_tags as $tag => $explanation ) {
							?>
							<li>
								<button type="button"
										class="button button-secondary"
										aria-label="<?php echo esc_attr( sprintf( $explanation, $tag ) ); ?>"
										data-added="<?php echo esc_attr( sprintf( $structure_tag_added, $tag ) ); ?>"
										data-used="<?php echo esc_attr( sprintf( $structure_tag_already_used, $tag ) ); ?>">
									<?php echo '%' . $tag . '%'; ?>
								</button>
							</li>
							<?php
						}
						?>
					</ul>
				<?php endif; ?>
			</div>
		</td>
	</tr>
</table>

<h2 class="title"><?php _e( 'Optional' ); ?></h2>
<p>
<?php
/* translators: %s: Placeholder that must come at the start of the URL. */
printf( __( 'If you like, you may enter custom structures for your category and tag URLs here. For example, using <code>topics</code> as your category base would make your category links like <code>%s/topics/uncategorized/</code>. If you leave these blank the defaults will be used.' ), get_option( 'home' ) . $blog_prefix . $prefix );
?>
</p>

<table class="form-table" role="presentation">
	<tr>
		<th><label for="category_base"><?php /* translators: Prefix for category permalinks. */ _e( 'Category base' ); ?></label></th>
		<td><?php echo $blog_prefix; ?> <input name="category_base" id="category_base" type="text" value="<?php echo esc_attr( $category_base ); ?>" class="regular-text code" /></td>
	</tr>
	<tr>
		<th><label for="tag_base"><?php _e( 'Tag base' ); ?></label></th>
		<td><?php echo $blog_prefix; ?> <input name="tag_base" id="tag_base" type="text" value="<?php echo esc_attr( $tag_base ); ?>" class="regular-text code" /></td>
	</tr>
	<?php do_settings_fields( 'permalink', 'optional' ); ?>
</table>

<?php do_settings_sections( 'permalink' ); ?>

<?php submit_button(); ?>
</form>
<?php if ( ! is_multisite() ) { ?>
	<?php
	if ( $iis7_permalinks ) :
		if ( isset( $_POST['submit'] ) && $permalink_structure && ! $using_index_permalinks && ! $writable ) :
			if ( file_exists( $home_path . 'web.config' ) ) :
				?>
<p>
				<?php
				printf(
					/* translators: 1: web.config, 2: Documentation URL, 3: CTRL + a, 4: Element code. */
					__( 'If your %1$s file was <a href="%2$s">writable</a>, we could do this automatically, but it isn&#8217;t so this is the url rewrite rule you should have in your %1$s file. Click in the field and press %3$s to select all. Then insert this rule inside of the %4$s element in %1$s file.' ),
					'<code>web.config</code>',
					__( 'https://wordpress.org/support/article/changing-file-permissions/' ),
					'<kbd>CTRL + a</kbd>',
					'<code>/&lt;configuration&gt;/&lt;system.webServer&gt;/&lt;rewrite&gt;/&lt;rules&gt;</code>'
				);
				?>
</p>
<form action="options-permalink.php" method="post">
				<?php wp_nonce_field( 'update-permalink' ); ?>
	<p><textarea rows="9" class="large-text readonly" name="rules" id="rules" readonly="readonly"><?php echo esc_textarea( $wp_rewrite->iis7_url_rewrite_rules() ); ?></textarea></p>
</form>
<p>
				<?php
				printf(
					/* translators: %s: web.config */
					__( 'If you temporarily make your %s file writable for us to generate rewrite rules automatically, do not forget to revert the permissions after rule has been saved.' ),
					'<code>web.config</code>'
				);
				?>
</p>
		<?php else : ?>
<p>
			<?php
			printf(
				/* translators: 1: Documentation URL, 2: web.config, 3: CTRL + a */
				__( 'If the root directory of your site was <a href="%1$s">writable</a>, we could do this automatically, but it isn&#8217;t so this is the url rewrite rule you should have in your %2$s file. Create a new file, called %2$s in the root directory of your site. Click in the field and press %3$s to select all. Then insert this code into the %2$s file.' ),
				__( 'https://wordpress.org/support/article/changing-file-permissions/' ),
				'<code>web.config</code>',
				'<kbd>CTRL + a</kbd>'
			);
			?>
</p>
<form action="options-permalink.php" method="post">
			<?php wp_nonce_field( 'update-permalink' ); ?>
	<p><textarea rows="18" class="large-text readonly" name="rules" id="rules" readonly="readonly"><?php echo esc_textarea( $wp_rewrite->iis7_url_rewrite_rules( true ) ); ?></textarea></p>
</form>
<p>
			<?php
			printf(
				/* translators: %s: web.config */
				__( 'If you temporarily make your site&#8217;s root directory writable for us to generate the %s file automatically, do not forget to revert the permissions after the file has been created.' ),
				'<code>web.config</code>'
			);
			?>
</p>
		<?php endif; ?>
	<?php endif; ?>
<?php elseif ( $is_nginx ) : ?>
	<p><?php _e( '<a href="https://wordpress.org/support/article/nginx/">Documentation on Nginx configuration</a>.' ); ?></p>
	<?php
else :
	if ( $permalink_structure && ! $using_index_permalinks && ! $writable && $update_required ) :
		?>
<p>
		<?php
		printf(
			/* translators: 1: .htaccess, 2: Documentation URL, 3: CTRL + a */
			__( 'If your %1$s file was <a href="%2$s">writable</a>, we could do this automatically, but it isn&#8217;t so these are the mod_rewrite rules you should have in your %1$s file. Click in the field and press %3$s to select all.' ),
			'<code>.htaccess</code>',
			__( 'https://wordpress.org/support/article/changing-file-permissions/' ),
			'<kbd>CTRL + a</kbd>'
		);
		?>
</p>
<form action="options-permalink.php" method="post">
		<?php wp_nonce_field( 'update-permalink' ); ?>
	<p><textarea rows="6" class="large-text readonly" name="rules" id="rules" readonly="readonly"><?php echo esc_textarea( $wp_rewrite->mod_rewrite_rules() ); ?></textarea></p>
</form>
	<?php endif; ?>
<?php endif; ?>
<?php } // End if ! is_multisite(). ?>

</div>

<?php require_once ABSPATH . 'wp-admin/admin-footer.php'; ?>
