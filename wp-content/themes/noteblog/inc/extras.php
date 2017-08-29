<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package noteblog
 *
 * Please browse readme.txt for credits and forking information
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function noteblog_body_classes( $classes ) {
  // Adds a class of group-blog to blogs with more than 1 published author.
  if ( is_multi_author() ) {
    $classes[] = 'group-blog';
  }

  return $classes;
}
add_filter( 'body_class', 'noteblog_body_classes' );

if ( ! function_exists( 'noteblog_header_menu' ) ) :
    /**
     * Header menu (should you choose to use one)
     */
  function noteblog_header_menu() {
      // display the WordPress Custom Menu if available
    wp_nav_menu(array(
      'theme_location'    => 'primary',
      'depth'             => 2,
      'container'         => 'div',
      'container_class'   => 'collapse navbar-collapse navbar-ex1-collapse',
      'menu_class'        => 'nav navbar-nav',
      'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
      'walker'            => new wp_bootstrap_navwalker()
      ));
  } /* end header menu */
  endif;



/**
 * Adds the URL to the top level navigation menu item
 */
function  noteblog_add_top_level_menu_url( $atts, $item, $args ){
  if ( isset($args->has_children) && $args->has_children  ) {
    $atts['href'] = ! empty( $item->url ) ? $item->url : '';
  }
  return $atts;
}
add_filter( 'nav_menu_link_attributes', 'noteblog_add_top_level_menu_url', 99, 3 );





/** BACKEND **/

add_action( 'admin_menu', 'noteblog_register_backend' );
function noteblog_register_backend() {
  add_theme_page( __('About Noteblog', 'noteblog'), __('Noteblog', 'noteblog'), 'edit_theme_options', 'about-noteblog.php', 'noteblog_backend');
}

function noteblog_backend(){ ?>
<div class="text-centering">
  <div class="backend-css customize-noteblog">
    <h2><?php echo __( 'Welcome to Noteblog', 'noteblog' ); ?></p></h2>
    <p><?php echo __( 'If you have questions or need help with anything theme related please', 'noteblog' ); ?><br> <a href="https://lighthouseseooptimization.github.io/wordpress/noteblog#contact" target="_blank"><?php echo __( 'Email us here', 'noteblog' ); ?></a> or <?php echo __( 'write to us directly at: Beseenseo@gmail.com', 'noteblog' ); ?></p>
  </div>
</div>

<h2 class="headline-second"><?php echo __( 'Quick Links', 'noteblog' ); ?></h2>
<div class="text-centering">
 <div class="backend-css">
 <a class="wide-button-noteblog" href="<?php echo admin_url('/customize.php'); ?>" target="_blank">Customize Theme Design</a><br>
  <a class="wide-button-noteblog" href="#demoanchor">Noteblog F.A.Q</a><br>
  <a class="wide-button-noteblog" href="https://lighthouseseooptimization.github.io/wordpress/noteblog/" target="_blank">Read About Noteblog Pro</a><br>
  <a class="wide-button-noteblog" href="https://lighthouseseooptimization.github.io/wordpress/noteblog/#newslettersection" target="_blank">Noteblog Theme Newsletter</a><br>
  <a class="wide-button-noteblog" href="https://lighthouseseooptimization.github.io/wordpress/noteblog#contact" target="_blank">Contact Us</a>


</div>
</div>
<div class="text-centering"><br><br>
  <a href="https://lighthouseseooptimization.github.io/wordpress/noteblog/" target="_blank">
    <img src="https://lighthouseseooptimization.github.io/wordpress/noteblog/img/large-noteblog-info.png" alt="">
  </a>
</div>
<h2 class="headline-second" id="demoanchor"><?php echo __( 'F.A.Q & Documentation', 'noteblog' ); ?></h2>
<section class="ac-container">
  <div>
    <input id="ac-40" name="accordion-40" type="radio">
    <label for="ac-40"><?php echo __( 'Making your website like the demo', 'noteblog' ); ?></label>
    <article class="ac-large">
     <p><em><?php echo __( 'How to set up your website like on our demo', 'noteblog' ); ?></em></p>
    <ol>
      <li><p><?php echo __( 'Go to "Appearance" > "Customize" in the WordPress admin menu.', 'noteblog' ); ?></p></li>
      <li><p><?php echo __( 'Under "Site identity" pick a title and a tagline & choose "Display site title and tagline"', 'noteblog' ); ?></p></li>
      <li><p><?php echo __( 'Go to Front Page Header and fill out a title and a tagline text', 'noteblog' ); ?></p></li>
      <li><p><?php echo __( 'Go to Global Theme Color and choose default or pick a new one', 'noteblog' ); ?></p></li>
    </ol>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-41" name="accordion-41" type="radio">
    <label for="ac-41"> <?php echo __( 'How to set up plugins', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p>- <a href="http://www.wpbeginner.com/plugins/how-to-install-and-setup-wordpress-seo-plugin-by-yoast/"> <?php echo __( 'How to set up Yoast', 'noteblog' ); ?></a></p>
      <p>- <a href="http://nerdynerdnerdz.com/4119/how-to-setup-autoptimize-plugin-in-wordpress/"> <?php echo __( 'How to set up Autoptimize', 'noteblog' ); ?></a></p>
      <p>- <a href="http://www.wpbeginner.com/beginners-guide/how-to-install-and-setup-wp-super-cache-for-beginners/"> <?php echo __( 'How to set up WP Super Cache', 'noteblog' ); ?></a></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-48" name="accordion-48" type="radio">
    <label for="ac-48"> <?php echo __( 'How to change footer copyright text', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p>- <?php echo __( 'Go to Appearance > Customize > Footer and fill in Footer Copyright Text', 'noteblog' ); ?></a></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-1" name="accordion-1" type="radio">
     <label for="ac-1"><?php echo __( 'Adding a logo', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Site Identity > Select Logo', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-2" name="accordion-2" type="radio">
    <label for="ac-2"><?php echo __( 'Adding a title to the header image/color', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Site Identity > Site Title', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-3" name="accordion-3" type="radio">
    <label for="ac-3"><?php echo __( 'Adding a tagline to the header image/color', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Front Page Header', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-4" name="accordion-4" type="radio">
    <label for="ac-4"><?php echo __( 'Adding a Site Icon / Fav Icon', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Site Identity > Site Icon', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-5" name="accordion-5" type="radio">
     <label for="ac-5"><?php echo __( 'Changing header text color', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Colors > Header Text Color', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-49" name="accordion-49" type="radio">
    <label for="ac-49"><?php echo __( 'Changing background color on footer widget area', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Footer', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-6" name="accordion-6" type="radio">
     <label for="ac-6"><?php echo __( 'Changing header background color', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Colors > Header background Color', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-7" name="accordion-7" type="radio">
     <label for="ac-7"><?php echo __( 'Adding a header image', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Header Image > Upload or pick a suggested', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-8" name="accordion-8" type="radio">
     <label for="ac-8"><?php echo __( 'Changing background color', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Colors > background color', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-10" name="accordion-10" type="radio">
     <label for="ac-10"><?php echo __( 'Changing Theme Color', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Accent Color > Select a color', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-11" name="accordion-11" type="radio">
    <label for="ac-11"><?php echo __( 'Adding a widget', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Widgets ', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-13" name="accordion-13" type="radio">
     <label for="ac-13"><?php echo __( 'Using full width theme', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'While editing a page, under Page Attributes, choose Full Width Template ', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-14" name="accordion-14" type="radio">
     <label for="ac-14"><?php echo __( 'Changing Footer Widget Title Color', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Footer', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-15" name="accordion-15" type="radio">
     <label for="ac-15"><?php echo __( 'Changing footer copyright section background color', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Footer', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-16" name="accordion-16" type="radio">
     <label for="ac-16"><?php echo __( 'Changing footer copyright section text color', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Footer', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-17" name="accordion-17" type="radio">
     <label for="ac-17"><?php echo __( 'Changing Sidebar background color', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Sidebar', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-45" name="accordion-45" type="radio">
    <label for="ac-45"><?php echo __( 'Changing sidebar headline color', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Sidebar', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-18" name="accordion-18" type="radio">
     <label for="ac-18"><?php echo __( 'Changing sidebar link color', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Sidebar', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-19" name="accordion-19" type="radio">
     <label for="ac-19"><?php echo __( 'Changing sidebar link border color', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Sidebar', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-20" name="accordion-20" type="radio">
    <label for="ac-20"><?php echo __( 'Changing navigation background color', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Navigation', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-22" name="accordion-22" type="radio">
    <label for="ac-22"><?php echo __( 'Changing navigation link color', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Navigation', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-23" name="accordion-23" type="radio">
     <label for="ac-23"><?php echo __( 'Changing navigation logo color', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Navigation', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-24" name="accordion-24" type="radio">
    <label for="ac-24"><?php echo __( 'Changing post & page headline color ', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Post & Page', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-25" name="accordion-25" type="radio">
     <label for="ac-25"><?php echo __( 'Changing post & page content color', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Post & Page', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-26" name="accordion-26" type="radio">
     <label for="ac-26"><?php echo __( 'Changing post author byline color', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Post & Page', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-27" name="accordion-27" type="radio">
    <label for="ac-27"><?php echo __( 'Adding top widgets', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Widgets', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-28" name="accordion-28" type="radio">
     <label for="ac-28"><?php echo __( 'Adding bottom widgets', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Widgets', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-29" name="accordion-29" type="radio">
   <label for="ac-29"><?php echo __( 'Adding Footer widgets', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Widgets', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-30" name="accordion-30" type="radio">
    <label for="ac-30"><?php echo __( 'Adding Sidebar widgets', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Widgets', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-31" name="accordion-31" type="radio">
   <label for="ac-31"><?php echo __( 'Changing design on top widgets', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > top widgets design', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-32" name="accordion-32" type="radio">
    <label for="ac-32"><?php echo __( 'Changing design on bottom widgets', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > bottom widgets design', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-33" name="accordion-33" type="radio">
     <label for="ac-33"><?php echo __( 'Adding custom CSS', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Additional CSS', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-50" name="accordion-50" type="radio">
     <label for="ac-50"><?php echo __( 'Adding custom header text', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Front page: Header', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<section class="ac-container">
  <div>
    <input id="ac-51" name="accordion-51" type="radio">
     <label for="ac-51"><?php echo __( 'Adding an author image on posts', 'noteblog' ); ?></label>
    <article class="ac-large">
      <p><?php echo __( 'In the WordPress admin menu click Appearance > Customize > Post & Pages and paste in the link to your author image, 50x50 size is recommended.', 'noteblog' ); ?></p>
    </article>
  </div>
</section>

<?php }

