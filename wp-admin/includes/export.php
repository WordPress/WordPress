<?php
/**
 * WordPress Export Administration API
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Version number for the export format.
 *
 * Bump this when something changes that might affect compatibility.
 *
 * @since unknown
 * @var string
 */
define('WXR_VERSION', '1.0');

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $args
 */
function export_wp( $args = array() ) {
	global $wpdb, $post_ids, $post, $wp_taxonomies;
	
	if ( ! is_array( $args ) )
		$args = array( 'author' => $args );
	
	$defaults = array( 'author' => null, 'taxonomy' => null, 'post_type' => null, 'post_status' => null, 'start_date' => null, 'end_date' => null );
	$args = wp_parse_args( $args, $defaults );
	
	extract($args);
	
	do_action('export_wp');
	
	if( strlen( $start_date ) > 4 && strlen( $end_date ) > 4 )
		$filename = 'wordpress.' . $start_date . '.' . $end_date . '.xml';
	else
		$filename = 'wordpress.' . date( 'Y-m-d' ) . '.xml';
	
	header( 'Content-Description: File Transfer' );
	header( 'Content-Disposition: attachment; filename=' . $filename );
	header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );
	
	if ( $post_type && $post_type != 'all' )
		$where = $wpdb->prepare("WHERE post_type = %s ", $post_type);
	else
		$where = "WHERE post_type != 'revision' ";
	
	if ( $author && $author != 'all' ) {
		$author_id = (int) $author;
		$where .= $wpdb->prepare( "AND post_author = %d ", $author_id );
	}
	
	if ( $start_date && $start_date != 'all' )
		$where .= $wpdb->prepare( "AND post_date >= %s ", $start_date );
	
	if ( $end_date && $end_date != 'all' )
		$where .= $wpdb->prepare( "AND post_date < %s ", $end_date );
	
	if ( $taxonomy && is_array( $taxonomy ) ) {
		foreach ( $taxonomy as $term_id ) {
			if ( $term_id != 'all' )
				$where .= $wpdb->prepare( "AND ID IN (SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d) ", $term_id );
		}
	}
	
	if ( $post_status && $post_status != 'all' )
		$where .= $wpdb->prepare( "AND post_status = %s", $status );
	
	// grab a snapshot of post IDs, just in case it changes during the export
	$post_ids = $wpdb->get_col( "SELECT ID FROM $wpdb->posts $where ORDER BY post_date_gmt ASC" );
	
	$categories = (array) get_categories( array( 'get' => 'all' ) );
	$tags = (array) get_tags( array( 'get' => 'all' ) );
	
	$custom_taxonomies = $wp_taxonomies;
	unset( $custom_taxonomies['category'] );
	unset( $custom_taxonomies['post_tag'] );
	unset( $custom_taxonomies['link_category'] );
	$custom_taxonomies = array_keys( $custom_taxonomies );
	$terms = (array) get_terms( $custom_taxonomies, array( 'get' => 'all' ) );
	
	/**
	 * {@internal Missing Short Description}}
	 *
	 * @since unknown
	 *
	 * @param unknown_type $categories
	 */
	function wxr_missing_parents( $categories ) {
		if ( ! is_array( $categories ) || empty( $categories ) )
			return array();
	
		foreach ( $categories as $category ){
			$parents[$category->term_id] = $category->parent;
		}
	
		$parents = array_unique( array_diff( $parents, array_keys( $parents ) ) );
	
		if ( $zero = array_search( '0', $parents ) )
			unset( $parents[$zero] );
	
		return $parents;
	}
	
	while ( $parents = wxr_missing_parents( $categories ) ) {
		$found_parents = get_categories( array( 'include' => join( ', ', $parents) ) );
		if ( is_array( $found_parents ) && count( $found_parents ) )
			$categories = array_merge( $categories, $found_parents );
		else
			break;
	}
	
	// Put them in order to be inserted with no child going before its parent
	$pass = 0;
	$passes = 1000 + count( $categories );
	while ( ( $cat = array_shift( $categories ) ) && ++$pass < $passes ) {
		if ( $cat->parent == 0 || isset( $cats[$cat->parent] ) )
			$cats[$cat->term_id] = $cat;
		else
			$categories[] = $cat;
	}
	unset( $categories );
	
	/**
	 * Place string in CDATA tag.
	 *
	 * @since unknown
	 *
	 * @param string $str String to place in XML CDATA tag.
	 */
	function wxr_cdata( $str ) {
		if ( seems_utf8( $str ) == false )
			$str = utf8_encode( $str );
	
		// $str = ent2ncr(esc_html($str));
		$str = "<![CDATA[$str" . ( ( substr( $str, -1 ) == ']' ) ? ' ' : '') . "]]>";
	
		return $str;
	}
	
	/**
	 * {@internal Missing Short Description}}
	 *
	 * @since unknown
	 *
	 * @return string Site URL.
	 */
	function wxr_site_url() {
		global $current_site;
	
		// mu: the base url
		if ( isset( $current_site->domain ) )
			return network_home_url();
		// wp: the blog url
		else
			return get_bloginfo_rss( 'url' );
	}
	
	/**
	 * {@internal Missing Short Description}}
	 *
	 * @since unknown
	 *
	 * @param object $c Category Object
	 */
	function wxr_cat_name( $c ) {
		if ( empty( $c->name ) )
			return;
	
		echo '<wp:cat_name>' . wxr_cdata( $c->name ) . '</wp:cat_name>';
	}
	
	/**
	 * {@internal Missing Short Description}}
	 *
	 * @since unknown
	 *
	 * @param object $c Category Object
	 */
	function wxr_category_description( $c ) {
		if ( empty( $c->description ) )
			return;
	
		echo '<wp:category_description>' . wxr_cdata($c->description) . '</wp:category_description>';
	}
	
	/**
	 * {@internal Missing Short Description}}
	 *
	 * @since unknown
	 *
	 * @param object $t Tag Object
	 */
	function wxr_tag_name( $t ) {
		if ( empty( $t->name ) )
			return;
	
		echo '<wp:tag_name>' . wxr_cdata($t->name) . '</wp:tag_name>';
	}
	
	/**
	 * {@internal Missing Short Description}}
	 *
	 * @since unknown
	 *
	 * @param object $t Tag Object
	 */
	function wxr_tag_description( $t ) {
		if ( empty( $t->description ) )
			return;
	
		echo '<wp:tag_description>' . wxr_cdata($t->description) . '</wp:tag_description>';
	}
	
	/**
	 * {@internal Missing Short Description}}
	 *
	 * @since unknown
	 *
	 * @param object $t Term Object
	 */
	function wxr_term_name( $t ) {
		if ( empty( $t->name ) )
			return;
	
		echo '<wp:term_name>' . wxr_cdata($t->name) . '</wp:term_name>';
	}
	
	/**
	 * {@internal Missing Short Description}}
	 *
	 * @since unknown
	 *
	 * @param object $t Term Object
	 */
	function wxr_term_description( $t ) {
		if ( empty( $t->description ) )
			return;
	
		echo '<wp:term_description>' . wxr_cdata($t->description) . '</wp:term_description>';
	}
	
	/**
	 * {@internal Missing Short Description}}
	 *
	 * @since unknown
	 */
	function wxr_post_taxonomy() {
		global $post;
	
		$the_list = '';
		$filter = 'rss';
	
		$taxonomies = get_object_taxonomies( 'post' );
		$terms = wp_get_post_terms( $post->ID, $taxonomies );
		foreach ( (array) $terms as $term ) {
			$domain = ( 'post_tag' == $term->taxonomy ) ? 'tag' : $term->taxonomy;
			$term_name = sanitize_term_field( 'name', $term->name, $term->term_id, $term->taxonomy, $filter );
			// Back compat.
			if ( 'category' == $term->taxonomy )
				$the_list .= "\n\t\t<category><![CDATA[$term_name]]></category>\n";
			elseif ( 'post_tag' == $term->taxonomy )
				$the_list .= "\n\t\t<category domain=\"$domain\"><![CDATA[$term_name]]></category>\n";
			// forwards compatibility as above
			$the_list .= "\n\t\t<category domain=\"$domain\" nicename=\"{$term->slug}\"><![CDATA[$term_name]]></category>\n";
		}
		echo $the_list;
	}
	
	echo '<?xml version="1.0" encoding="' . get_bloginfo('charset') . '"?' . ">\n";
	
	?>
<!-- This is a WordPress eXtended RSS file generated by WordPress as an export of your blog. -->
<!-- It contains information about your blog's posts, comments, and categories. -->
<!-- You may use this file to transfer that content from one site to another. -->
<!-- This file is not intended to serve as a complete backup of your blog. -->

<!-- To import this information into a WordPress blog follow these steps. -->
<!-- 1. Log in to that blog as an administrator. -->
<!-- 2. Go to Tools: Import in the blog's admin panels (or Manage: Import in older versions of WordPress). -->
<!-- 3. Choose "WordPress" from the list. -->
<!-- 4. Upload this file using the form provided on that page. -->
<!-- 5. You will first be asked to map the authors in this export file to users -->
<!--    on the blog.  For each author, you may choose to map to an -->
<!--    existing user on the blog or to create a new user -->
<!-- 6. WordPress will then import each of the posts, comments, and categories -->
<!--    contained in this file into your blog -->

<?php the_generator( 'export' );?>
<rss version="2.0"
	xmlns:excerpt="http://wordpress.org/export/<?php echo WXR_VERSION; ?>/excerpt/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:wp="http://wordpress.org/export/<?php echo WXR_VERSION; ?>/"
>

<channel>
	<title><?php bloginfo_rss( 'name' ); ?></title>
	<link><?php bloginfo_rss( 'url' ) ?></link>
	<description><?php bloginfo_rss( 'description' ) ?></description>
	<pubDate><?php echo mysql2date( 'D, d M Y H:i:s +0000', get_lastpostmodified( 'GMT' ), false ); ?></pubDate>
	<generator>http://wordpress.org/?v=<?php bloginfo_rss( 'version' ); ?></generator>
	<language><?php echo get_option( 'rss_language' ); ?></language>
	<wp:wxr_version><?php echo WXR_VERSION; ?></wp:wxr_version>
	<wp:base_site_url><?php echo wxr_site_url(); ?></wp:base_site_url>
	<wp:base_blog_url><?php bloginfo_rss( 'url' ); ?></wp:base_blog_url>
	<?php if ( $cats ) : foreach ( $cats as $c ) : ?>
	<wp:category><wp:category_nicename><?php echo $c->slug; ?></wp:category_nicename><wp:category_parent><?php echo $c->parent ? $cats[$c->parent]->name : ''; ?></wp:category_parent><?php wxr_cat_name( $c ); ?><?php wxr_category_description( $c ); ?></wp:category>
	<?php endforeach; endif; ?>
	<?php if ( $tags ) : foreach ( $tags as $t ) : ?>
	<wp:tag><wp:tag_slug><?php echo $t->slug; ?></wp:tag_slug><?php wxr_tag_name( $t ); ?><?php wxr_tag_description( $t ); ?></wp:tag>
	<?php endforeach; endif; ?>
	<?php if ( $terms ) : foreach ( $terms as $t ) : ?>
	<wp:term><wp:term_taxonomy><?php echo $t->taxonomy; ?></wp:term_taxonomy><wp:term_slug><?php echo $t->slug; ?></wp:term_slug><wp:term_parent><?php echo $t->parent ? $custom_taxonomies[$t->parent]->name : ''; ?></wp:term_parent><?php wxr_term_name( $t ); ?><?php wxr_term_description( $t ); ?></wp:term>
	<?php endforeach; endif; ?>
	
	<?php do_action( 'rss2_head' ); ?>

	<?php if ( $post_ids ) {
	global $wp_query;
	$wp_query->in_the_loop = true;  // Fake being in the loop.
	
	// fetch 20 posts at a time rather than loading the entire table into memory
	while ( $next_posts = array_splice( $post_ids, 0, 20 ) ) {
	$where = "WHERE ID IN (" . join( ',', $next_posts ) . ")";
	$posts = $wpdb->get_results( "SELECT * FROM $wpdb->posts $where ORDER BY post_date_gmt ASC" );
	
	// Begin Loop
	foreach ($posts as $post) {
	setup_postdata( $post );

	$is_sticky = 0;
	if ( is_sticky( $post->ID ) )
		$is_sticky = 1;

	?>
	<item>
		<title><?php echo apply_filters( 'the_title_rss', $post->post_title ); ?></title>
		<link><?php the_permalink_rss() ?></link>
		<pubDate><?php echo mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', true ), false ); ?></pubDate>
		<dc:creator><?php echo wxr_cdata( get_the_author() ); ?></dc:creator>
		<?php wxr_post_taxonomy() ?>
	
		<guid isPermaLink="false"><?php the_guid(); ?></guid>
		<description></description>
		<content:encoded><?php echo wxr_cdata( apply_filters( 'the_content_export', $post->post_content ) ); ?></content:encoded>
		<excerpt:encoded><?php echo wxr_cdata( apply_filters( 'the_excerpt_export', $post->post_excerpt ) ); ?></excerpt:encoded>
		<wp:post_id><?php echo $post->ID; ?></wp:post_id>
		<wp:post_date><?php echo $post->post_date; ?></wp:post_date>
		<wp:post_date_gmt><?php echo $post->post_date_gmt; ?></wp:post_date_gmt>
		<wp:comment_status><?php echo $post->comment_status; ?></wp:comment_status>
		<wp:ping_status><?php echo $post->ping_status; ?></wp:ping_status>
		<wp:post_name><?php echo $post->post_name; ?></wp:post_name>
		<wp:status><?php echo $post->post_status; ?></wp:status>
		<wp:post_parent><?php echo $post->post_parent; ?></wp:post_parent>
		<wp:menu_order><?php echo $post->menu_order; ?></wp:menu_order>
		<wp:post_type><?php echo $post->post_type; ?></wp:post_type>
		<wp:post_password><?php echo $post->post_password; ?></wp:post_password>
		<wp:is_sticky><?php echo $is_sticky; ?></wp:is_sticky>
		<?php
		if ( $post->post_type == 'attachment' ) { ?>
		<wp:attachment_url><?php echo wp_get_attachment_url( $post->ID ); ?></wp:attachment_url>
		<?php } ?>
		<?php
		$postmeta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE post_id = %d", $post->ID ) );
		if ( $postmeta ) {
		?>
		<?php foreach( $postmeta as $meta ) { ?>
		<wp:postmeta>
		<wp:meta_key><?php echo $meta->meta_key; ?></wp:meta_key>
		<wp:meta_value><?php echo wxr_cdata( $meta->meta_value ); ?></wp:meta_value>
		</wp:postmeta>
		<?php } ?>
		<?php } ?>
		<?php
		$comments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d", $post->ID ) );
		if ( $comments ) { foreach ( $comments as $c ) { ?>
		<wp:comment>
		<wp:comment_id><?php echo $c->comment_ID; ?></wp:comment_id>
		<wp:comment_author><?php echo wxr_cdata( $c->comment_author ); ?></wp:comment_author>
		<wp:comment_author_email><?php echo $c->comment_author_email; ?></wp:comment_author_email>
		<wp:comment_author_url><?php echo esc_url_raw( $c->comment_author_url ); ?></wp:comment_author_url>
		<wp:comment_author_IP><?php echo $c->comment_author_IP; ?></wp:comment_author_IP>
		<wp:comment_date><?php echo $c->comment_date; ?></wp:comment_date>
		<wp:comment_date_gmt><?php echo $c->comment_date_gmt; ?></wp:comment_date_gmt>
		<wp:comment_content><?php echo wxr_cdata( $c->comment_content ) ?></wp:comment_content>
		<wp:comment_approved><?php echo $c->comment_approved; ?></wp:comment_approved>
		<wp:comment_type><?php echo $c->comment_type; ?></wp:comment_type>
		<wp:comment_parent><?php echo $c->comment_parent; ?></wp:comment_parent>
		<wp:comment_user_id><?php echo $c->user_id; ?></wp:comment_user_id>
		</wp:comment>
		<?php } } ?>
	</item>
	<?php 
	} 
	}
} ?>
</channel>
</rss>
<?php
}

?>
