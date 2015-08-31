<?php
/**
 * @package WPSEO\Admin
 */

if ( ! defined( 'WPSEO_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

$yform = Yoast_Form::get_instance();
$yform->set_option( 'wpseo_rss' );

echo '<p>' . __( "This feature is used to automatically add content to your RSS, more specifically, it's meant to add links back to your blog and your blog posts, so dumb scrapers will automatically add these links too, helping search engines identify you as the original source of the content.", 'wordpress-seo' ) . '</p>';

$textarea_atts = array(
	'cols' => '50',
	'rows' => '5',
);
$yform->textarea( 'rssbefore', __( 'Content to put before each post in the feed', 'wordpress-seo' ), '', $textarea_atts );
$yform->textarea( 'rssafter', __( 'Content to put after each post in the feed', 'wordpress-seo' ), '', $textarea_atts );
?>

<p><?php _e( 'You can use the following variables within the content, they will be replaced by the value on the right.', 'wordpress-seo' ); ?></p>
<table class="wpseo">
	<tr>
		<th><strong><code>%%AUTHORLINK%%</code></strong></th>
		<td><?php _e( 'A link to the archive for the post author, with the authors name as anchor text.', 'wordpress-seo' ); ?></td>
	</tr>
	<tr>
		<th><strong><code>%%POSTLINK%%</code></strong></th>
		<td><?php _e( 'A link to the post, with the title as anchor text.', 'wordpress-seo' ); ?></td>
	</tr>
	<tr>
		<th><strong><code>%%BLOGLINK%%</code></strong></th>
		<td><?php _e( "A link to your site, with your site's name as anchor text.", 'wordpress-seo' ); ?></td>
	</tr>
	<tr>
		<th><strong><code>%%BLOGDESCLINK%%</code></strong></th>
		<td><?php _e( "A link to your site, with your site's name and description as anchor text.", 'wordpress-seo' ); ?></td>
	</tr>
</table>
