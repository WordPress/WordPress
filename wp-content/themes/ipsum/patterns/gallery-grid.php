<?php
/**
 * Title: Gallery Grid
 * Slug: ipsum/gallery-grid
 * Categories: gallery
 * Viewport width: 1280
 * Description: A square photo grid at reading width, with a text tile linking to the gallery’s Instagram.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Gallery Grid', 'Name of the gallery grid group', 'ipsum' ); ?>"},"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:group {"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|30","left":"var:preset|spacing|30"}}},"layout":{"type":"grid","minimumColumnWidth":"10rem"}} -->
<div class="wp-block-group"><!-- wp:cover {"overlayColor":"theme-6","isUserOverlayColor":true,"metadata":{"name":"<?php echo esc_html_x( 'Grid Unit', 'Name of the gallery text tile', 'ipsum' ); ?>"},"style":{"dimensions":{"aspectRatio":"1"},"border":{"radius":{"topLeft":"5px","topRight":"5px","bottomLeft":"5px","bottomRight":"5px"}}},"textColor":"theme-1","layout":{"type":"constrained"}} -->
<div class="wp-block-cover has-theme-1-color has-text-color" style="border-top-left-radius:5px;border-top-right-radius:5px;border-bottom-left-radius:5px;border-bottom-right-radius:5px"><span aria-hidden="true" class="wp-block-cover__background has-theme-6-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Info Stack', 'Name of the tile info stack group', 'ipsum' ); ?>"},"style":{"dimensions":{"minHeight":"100%"},"spacing":{"blockGap":{"top":"0","left":"0"}},"elements":{"link":{"color":{"text":"var:preset|color|theme-1"}}}},"layout":{"type":"flex","orientation":"vertical","verticalAlignment":"center","justifyContent":"center"}} -->
<div class="wp-block-group has-link-color" style="min-height:100%"><!-- wp:heading {"fontSize":"medium"} -->
<h2 class="wp-block-heading has-medium-font-size"><?php esc_html_e( 'Instagram', 'ipsum' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"textAlign":"center"}},"fontSize":"medium"} -->
<p class="has-text-align-center has-medium-font-size"><a href="#"><?php esc_html_e( '@ipsum', 'ipsum' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover -->

<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/ipsum-metal-table.webp?ver=<?php echo esc_attr( wp_get_theme( get_template() )->get( 'Version' ) ); ?>" alt="<?php echo esc_attr_x( 'A dark metal tabletop on a single thin leg against a hazy sky.', 'Alt text for the metal table image', 'ipsum' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/ipsum-staircase.webp?ver=<?php echo esc_attr( wp_get_theme( get_template() )->get( 'Version' ) ); ?>" alt="<?php echo esc_attr_x( 'A black staircase climbing a white wall.', 'Alt text for the staircase image', 'ipsum' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/ipsum-pendant-lamp.webp?ver=<?php echo esc_attr( wp_get_theme( get_template() )->get( 'Version' ) ); ?>" alt="<?php echo esc_attr_x( 'A glowing pendant lamp above a warm orange room.', 'Alt text for the pendant lamp image', 'ipsum' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/ipsum-polygons.webp?ver=<?php echo esc_attr( wp_get_theme( get_template() )->get( 'Version' ) ); ?>" alt="<?php echo esc_attr_x( 'Rows of glossy black hollow cones catching the light.', 'Alt text for the polygons image', 'ipsum' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/ipsum-monobloc-front.webp?ver=<?php echo esc_attr( wp_get_theme( get_template() )->get( 'Version' ) ); ?>" alt="<?php echo esc_attr_x( 'A blurred plastic chair in blue and cream tones.', 'Alt text for the monobloc chair image', 'ipsum' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/ipsum-capital.webp?ver=<?php echo esc_attr( wp_get_theme( get_template() )->get( 'Version' ) ); ?>" alt="<?php echo esc_attr_x( 'A capital A pressed into brushed metal.', 'Alt text for the capital letter image', 'ipsum' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/ipsum-monobloc-back.webp?ver=<?php echo esc_attr( wp_get_theme( get_template() )->get( 'Version' ) ); ?>" alt="<?php echo esc_attr_x( 'A blurred plastic chair seen from behind, in iridescent pastel tones.', 'Alt text for the back view of the monobloc chair image', 'ipsum' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/ipsum-studio-closeup.webp?ver=<?php echo esc_attr( wp_get_theme( get_template() )->get( 'Version' ) ); ?>" alt="<?php echo esc_attr_x( 'Metal cylinders standing on a gray surface.', 'Alt text for the studio close-up image', 'ipsum' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/ipsum-lead-letter.webp?ver=<?php echo esc_attr( wp_get_theme( get_template() )->get( 'Version' ) ); ?>" alt="<?php echo esc_attr_x( 'The number 27 fading into blue and pink light.', 'Alt text for the lead letter image', 'ipsum' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/ipsum-facade.webp?ver=<?php echo esc_attr( wp_get_theme( get_template() )->get( 'Version' ) ); ?>" alt="<?php echo esc_attr_x( 'A blurred figure walking past an orange facade.', 'Alt text for the facade image', 'ipsum' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/ipsum-sculpture.webp?ver=<?php echo esc_attr( wp_get_theme( get_template() )->get( 'Version' ) ); ?>" alt="<?php echo esc_attr_x( 'A black ribbon-like sculpture curving across a pale background.', 'Alt text for the sculpture image', 'ipsum' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
