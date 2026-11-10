<?php
/**
 * Title: Search Bar
 * Slug: ipsum/hidden-search-bar
 * Inserter: no
 * Description: The Search Bar template part. It has a search button on the right that opens into a field. Most templates place it after the footer and pin it to the bottom of the screen.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Search Bar', 'Name of the search bar group', 'ipsum' ); ?>"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:search {"label":"<?php esc_attr_e( 'Search', 'ipsum' ); ?>","showLabel":false,"buttonText":"<?php esc_attr_e( 'Search', 'ipsum' ); ?>","buttonPosition":"button-only","buttonUseIcon":true,"ariaLabel":"<?php echo esc_attr_x( 'Site', 'Accessible name of the search form', 'ipsum' ); ?>","className":"is-style-outline"} /--></div>
<!-- /wp:group -->
