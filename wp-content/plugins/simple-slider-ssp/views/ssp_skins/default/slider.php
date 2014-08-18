<?php
/**
Plugin Name: Default Skin
**/
?>

<div class="flexslider ssp_slider_default" id="slider_<?php echo esc_attr( $slider_id ) ?>" data-slider_id="<?php echo esc_attr( $slider_id ) ?>" data-slider_options="<?php echo esc_attr(json_encode( $slider_settings )); ?>">

<ul class="slides ssp_slider wsp_default_skin">

<?php foreach( $slides as $slide ):

      if ( isset( $shortcode_atts['link_target'] ) )
        $target = $shortcode_atts['link_target'];
      else
        $target = "_self";
?>
  <li class="slide" data-thumb="<?php echo $slide['image']['sizes']['medium'] ?>">

    <?php do_action( 'ssp_skin_slide_start' ) ?>
    
    <?php if ( $slider_settings['linkable'] ): ?>
      <?php if ( isset( $shortcode_atts['size'] ) ): ?>
        <a href="<?php echo $slide['image']['link'] ?>" target="<?php echo $target ?>"><img class="slide_image" src="<?php echo $slide['image']['sizes'][$shortcode_atts['size']] ?>" /></a>
      <?php else: ?>
        <a href="<?php echo $slide['image']['link'] ?>" target="<?php echo $target ?>"><img alt="<?php echo $slide['image']['alt'] ?>" class="slide_image" src="<?php echo $slide['image']['url']; ?>" /></a>
      <?php endif; ?>
    <?php else: ?>
      <?php if ( isset( $shortcode_atts['size'] ) ): ?>
        <img alt="<?php echo $slide['image']['alt'] ?>" class="slide_image" src="<?php echo $slide['image']['sizes'][$shortcode_atts['size']] ?>" />
      <?php else: ?>
        <img alt="<?php echo $slide['image']['alt'] ?>" class="slide_image" src="<?php echo $slide['image']['url']; ?>" />
      <?php endif; ?>
    <?php endif; ?>

    <?php if ( ! empty( $slide['image']['caption'] ) ): ?>
      <?php if ( $slider_settings['caption_box'] ): ?>
        <p class="flex-caption">
          <?php if ( $slider_settings['linkable'] ): ?>
            <a href="<?php echo $slide['image']['link'] ?>">
              <strong><?php echo $slide['image']['caption'] ?></strong>
            </a>
          <?php else: ?>
            <strong>
              <?php echo $slide['image']['caption'] ?>
            </strong>
          <?php endif; ?>
        </p>
      <?php endif; ?>
    <?php endif; ?>

    <?php do_action( 'ssp_skin_slide_end' ) ?>

  </li>
<?php endforeach; ?>

</ul>

</div>