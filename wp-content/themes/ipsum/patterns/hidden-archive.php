<?php
/**
 * Title: All Archives
 * Slug: ipsum/hidden-archive
 * Template Types: archive, category, tag
 * Inserter: no
 * Description: The All Archives template for categories, tags, authors and dates. It has the header, the archive title over the Archive Compact listing, the footer, and the search bar pinned to the bottom.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:template-part {"slug":"header"} /-->

<!-- wp:group {"tagName":"main","metadata":{"name":"<?php echo esc_html_x( 'Body', 'Group block defining the main content area', 'ipsum' ); ?>"},"layout":{"type":"default"}} -->
<main class="wp-block-group"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Section Title', 'Group containing the archive title', 'ipsum' ); ?>"},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:query-title {"type":"archive"} /--></div>
<!-- /wp:group -->

<!-- wp:pattern {"slug":"ipsum/archive-compact"} /--></main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer"} /-->

<!-- wp:template-part {"slug":"search-bar","className":"is-style-sticky-bottom"} /-->
