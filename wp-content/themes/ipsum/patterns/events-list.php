<?php
/**
 * Title: Events List
 * Slug: ipsum/events-list
 * Categories: text
 * Viewport width: 1280
 * Description: A stack of event rows under dotted rules — title, date and place, with a reserve link on the right.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Events List', 'Name of the events list group', 'ipsum' ); ?>"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Events List Wrapper', 'Name of the events list wrapper group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":"var:preset|spacing|50"}},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Title and Description', 'Name of the title and description group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"top":"0"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group"><!-- wp:heading {"fontSize":"large"} -->
<h2 class="wp-block-heading has-large-font-size"><?php esc_html_e( 'Out of the browser', 'ipsum' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'The blog steps off the screen now and then. These are the dates.', 'ipsum' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Events Stack', 'Name of the events stack group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"0"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Event Row 1', 'Name of the first event row group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|20"},"padding":{"bottom":"var:preset|spacing|30"}},"border":{"top":{"width":"1px","style":"dotted"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group" style="border-top-style:dotted;border-top-width:1px;padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size"><?php esc_html_e( 'Essays, read aloud', 'ipsum' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Columns', 'Name of the event row columns group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"left":"0"}}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"top"}} -->
<div class="wp-block-group"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Column', 'Name of the event row column group', 'ipsum' ); ?>"},"style":{"layout":{"selfStretch":"fill","flexSize":null},"spacing":{"blockGap":{"top":"0"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Title and Date', 'Name of the title and date group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|30"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"small","fontFamily":"manrope"} -->
<p class="has-manrope-font-family has-small-font-size" style="font-style:normal;font-weight:700"><?php esc_html_e( 'Thu, Nov 12', 'ipsum' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:paragraph {"fontSize":"small","fontFamily":"manrope"} -->
<p class="has-manrope-font-family has-small-font-size"><?php esc_html_e( 'Porto, Portugal', 'ipsum' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Link and Icon', 'Name of the link and icon group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"left":"0"}}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"metadata":{"name":"<?php echo esc_html_x( 'Link', 'Name of the link paragraph', 'ipsum' ); ?>"},"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"small","fontFamily":"manrope"} -->
<p class="has-manrope-font-family has-small-font-size" style="font-style:normal;font-weight:700"><a href="#"><?php esc_html_e( 'Reserve a seat', 'ipsum' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:icon {"icon":"core/chevron-right-small"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Event Row 2', 'Name of the second event row group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|20"},"padding":{"bottom":"var:preset|spacing|30"}},"border":{"top":{"width":"1px","style":"dotted"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group" style="border-top-style:dotted;border-top-width:1px;padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Pictures, printed small', 'ipsum' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Columns', 'Name of the event row columns group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"left":"0"}}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"top"}} -->
<div class="wp-block-group"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Column', 'Name of the event row column group', 'ipsum' ); ?>"},"style":{"layout":{"selfStretch":"fill","flexSize":null},"spacing":{"blockGap":{"top":"0"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Title and Date', 'Name of the title and date group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|30"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"small","fontFamily":"manrope"} -->
<p class="has-manrope-font-family has-small-font-size" style="font-style:normal;font-weight:700"><?php esc_html_e( 'Sat, Nov 28', 'ipsum' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:paragraph {"fontSize":"small","fontFamily":"manrope"} -->
<p class="has-manrope-font-family has-small-font-size"><?php esc_html_e( 'Lisbon, Portugal', 'ipsum' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Link and Icon', 'Name of the link and icon group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"left":"0"}}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"metadata":{"name":"<?php echo esc_html_x( 'Link', 'Name of the link paragraph', 'ipsum' ); ?>"},"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"small","fontFamily":"manrope"} -->
<p class="has-manrope-font-family has-small-font-size" style="font-style:normal;font-weight:700"><a href="#"><?php esc_html_e( 'Reserve a seat', 'ipsum' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:icon {"icon":"core/chevron-right-small"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Event Row 3', 'Name of the third event row group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|20"},"padding":{"bottom":"var:preset|spacing|30"}},"border":{"top":{"width":"1px","style":"dotted"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group" style="border-top-style:dotted;border-top-width:1px;padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Sounds, played loud', 'ipsum' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Columns', 'Name of the event row columns group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"left":"0"}}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"top"}} -->
<div class="wp-block-group"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Column', 'Name of the event row column group', 'ipsum' ); ?>"},"style":{"layout":{"selfStretch":"fill","flexSize":null},"spacing":{"blockGap":{"top":"0"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Title and Date', 'Name of the title and date group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|30"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"small","fontFamily":"manrope"} -->
<p class="has-manrope-font-family has-small-font-size" style="font-style:normal;font-weight:700"><?php esc_html_e( 'Fri, Dec 11', 'ipsum' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:paragraph {"fontSize":"small","fontFamily":"manrope"} -->
<p class="has-manrope-font-family has-small-font-size"><?php esc_html_e( 'São Paulo, Brazil', 'ipsum' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Link and Icon', 'Name of the link and icon group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"left":"0"}}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"metadata":{"name":"<?php echo esc_html_x( 'Link', 'Name of the link paragraph', 'ipsum' ); ?>"},"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"small","fontFamily":"manrope"} -->
<p class="has-manrope-font-family has-small-font-size" style="font-style:normal;font-weight:700"><a href="#"><?php esc_html_e( 'Reserve a seat', 'ipsum' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:icon {"icon":"core/chevron-right-small"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Event Row 4', 'Name of the fourth event row group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|20"},"padding":{"bottom":"var:preset|spacing|30"}},"border":{"top":{"width":"1px","style":"dotted"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group" style="border-top-style:dotted;border-top-width:1px;padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size"><?php esc_html_e( 'Forever in draft: a conversation', 'ipsum' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Columns', 'Name of the event row columns group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"left":"0"}}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"top"}} -->
<div class="wp-block-group"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Column', 'Name of the event row column group', 'ipsum' ); ?>"},"style":{"layout":{"selfStretch":"fill","flexSize":null},"spacing":{"blockGap":{"top":"0"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Title and Date', 'Name of the title and date group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|30"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"small","fontFamily":"manrope"} -->
<p class="has-manrope-font-family has-small-font-size" style="font-style:normal;font-weight:700"><?php esc_html_e( 'Sat, Jan 9', 'ipsum' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:paragraph {"fontSize":"small","fontFamily":"manrope"} -->
<p class="has-manrope-font-family has-small-font-size"><?php esc_html_e( 'Online, from anywhere', 'ipsum' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Link and Icon', 'Name of the link and icon group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"left":"0"}}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"metadata":{"name":"<?php echo esc_html_x( 'Link', 'Name of the link paragraph', 'ipsum' ); ?>"},"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"small","fontFamily":"manrope"} -->
<p class="has-manrope-font-family has-small-font-size" style="font-style:normal;font-weight:700"><a href="#"><?php esc_html_e( 'Reserve a seat', 'ipsum' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:icon {"icon":"core/chevron-right-small"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
