<?php
/**
 * The Template Name: Home Page
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Twenty Minutes
 */

get_header(); ?>

<div id="content">
  <?php
    $twenty_minutes_hidcatslide = get_theme_mod('twenty_minutes_hide_categorysec', false);
    $twenty_minutes_slidersection = get_theme_mod('twenty_minutes_slidersection');

    if ($twenty_minutes_hidcatslide && $twenty_minutes_slidersection) { ?>
    <section id="catsliderarea">
      <div class="catwrapslider">
        <div class="owl-carousel">
          <?php if( get_theme_mod('twenty_minutes_slidersection',false) ) { ?>
          <?php $twenty_minutes_queryvar = new WP_Query('cat='.esc_attr(get_theme_mod('twenty_minutes_slidersection',false)));
            while( $twenty_minutes_queryvar->have_posts() ) : $twenty_minutes_queryvar->the_post(); ?>
              <div class="slidesection">
                <?php
                  if (has_post_thumbnail()) {
                      the_post_thumbnail('full');
                  } else {
                      echo '<div class="slider-img-color"></div>';
                  }
                ?>
                <div class="slider-box">
                  <h1><a href="<?php echo esc_url( get_permalink() );?>"><?php the_title(); ?></a></h1>
                  <div class="read-btn">
                    <?php 
                    $twenty_minutes_button_text = get_theme_mod('twenty_minutes_button_text', 'Read More');
                    $twenty_minutes_button_link_slider = get_theme_mod('twenty_minutes_button_link_slider', ''); 
                    if (empty($twenty_minutes_button_link_slider)) {
                        $twenty_minutes_button_link_slider = get_permalink();
                    }
                    if ($twenty_minutes_button_text || !empty($twenty_minutes_button_link_slider)) { ?>
                      <?php if(get_theme_mod('twenty_minutes_button_text','Read More') != ''){ ?>
                        <a href="<?php echo esc_url($twenty_minutes_button_text); ?>">
                          <?php echo esc_html($twenty_minutes_button_text); ?>
                            <span class="screen-reader-text"><?php echo esc_html($twenty_minutes_button_text); ?></span>
                        </a>
                      <?php } ?>
                    <?php } ?>
                  </div>
                </div>
              </div>
            <?php endwhile; wp_reset_postdata(); ?>
          <?php } ?>
        </div>
      </div>
      <div class="clear"></div>
    </section>
  <?php } ?>

  <?php
      $twenty_minutes_show_serv_sec = get_theme_mod('twenty_minutes_show_serv_sec', false);
      $twenty_minutes_services_section = get_theme_mod('twenty_minutes_services_section');

      if ($twenty_minutes_show_serv_sec && $twenty_minutes_services_section) { ?>
    <section id="second-sec">
      <div class="container">
        <?php if ( get_theme_mod('twenty_minutes_section_text') != "") { ?>
          <h2><?php echo esc_html(get_theme_mod('twenty_minutes_section_text','')); ?></h2>
        <?php } ?>
        <?php if ( get_theme_mod('twenty_minutes_section_title') != "") { ?>
          <h3><?php echo esc_html(get_theme_mod('twenty_minutes_section_title','')); ?></h3>
          <div class="line-box"></div>
        <?php } ?>
        <div class="inner-main-box">
          <div class="row m-0">
            <?php if( get_theme_mod('twenty_minutes_services_section',false) ) { ?>
            <?php $twenty_minutes_queryvar = new WP_Query('cat='.esc_attr(get_theme_mod('twenty_minutes_services_section',false)));
              while( $twenty_minutes_queryvar->have_posts() ) : $twenty_minutes_queryvar->the_post(); ?>
                <div class="col-lg-4 col-md-4">
                  <div class="inner-service-box">
                    <?php the_post_thumbnail( 'full' ); ?>
                    <div class="title-box">
                      <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                    </div>
                  </div>
                </div>
              <?php endwhile; wp_reset_postdata(); ?>
            <?php } ?>
          </div>
        </div>
      </div>
    </section>
  <?php } ?>

  <section id="content-creation">
    <div class="container">
      <?php while ( have_posts() ) : the_post(); ?>
        <?php the_content(); ?>
      <?php endwhile; // end of the loop. ?>
    </div>
  </section>
</div>

<?php get_footer(); ?>
