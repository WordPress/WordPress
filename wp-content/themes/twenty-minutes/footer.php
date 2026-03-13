<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Twenty Minutes
 */
?>
<div id="footer">
	<?php 
    $twenty_minutes_footer_widget_enabled = get_theme_mod('twenty_minutes_footer_widget', true);
    
    if ($twenty_minutes_footer_widget_enabled !== false && $twenty_minutes_footer_widget_enabled !== '') { ?>

    <?php 
        $twenty_minutes_widget_areas = get_theme_mod('twenty_minutes_footer_widget_areas', '4');
        if ($twenty_minutes_widget_areas == '3') {
            $twenty_minutes_cols = 'col-lg-4 col-md-6';
        } elseif ($twenty_minutes_widget_areas == '4') {
            $twenty_minutes_cols = 'col-lg-3 col-md-6';
        } elseif ($twenty_minutes_widget_areas == '2') {
            $twenty_minutes_cols = 'col-lg-6 col-md-6';
        } else {
            $twenty_minutes_cols = 'col-lg-12 col-md-12';
        }
    ?>

    <div class="footer-widget">
        <div class="container">
          <div class="row">
            <!-- Footer 1 -->
            <div class="<?php echo esc_attr($twenty_minutes_cols); ?> footer-block">
                <?php if (is_active_sidebar('footer-1')) : ?>
                    <?php dynamic_sidebar('footer-1'); ?>
                <?php else : ?>
                    <aside id="categories" class="widget py-3" role="complementary" aria-label="<?php esc_attr_e('footer1', 'twenty-minutes'); ?>">
                        <h3 class="widget-title"><?php esc_html_e('Categories', 'twenty-minutes'); ?></h3>
                        <ul>
                            <?php wp_list_categories('title_li='); ?>
                        </ul>
                    </aside>
                <?php endif; ?>
            </div>

            <!-- Footer 2 -->
            <div class="<?php echo esc_attr($twenty_minutes_cols); ?> footer-block">
                <?php if (is_active_sidebar('footer-2')) : ?>
                    <?php dynamic_sidebar('footer-2'); ?>
                <?php else : ?>
                    <aside id="archives" class="widget py-3" role="complementary" aria-label="<?php esc_attr_e('footer2', 'twenty-minutes'); ?>">
                        <h3 class="widget-title"><?php esc_html_e('Archives', 'twenty-minutes'); ?></h3>
                        <ul>
                            <?php wp_get_archives(array('type' => 'monthly')); ?>
                        </ul>
                    </aside>
                <?php endif; ?>
            </div>

            <!-- Footer 3 -->
            <div class="<?php echo esc_attr($twenty_minutes_cols); ?> footer-block">
                <?php if (is_active_sidebar('footer-3')) : ?>
                    <?php dynamic_sidebar('footer-3'); ?>
                <?php else : ?>
                    <aside id="meta" class="widget py-3" role="complementary" aria-label="<?php esc_attr_e('footer3', 'twenty-minutes'); ?>">
                        <h3 class="widget-title"><?php esc_html_e('Meta', 'twenty-minutes'); ?></h3>
                        <ul>
                            <?php wp_register(); ?>
                            <li><?php wp_loginout(); ?></li>
                            <?php wp_meta(); ?>
                        </ul>
                    </aside>
                <?php endif; ?>
            </div>

            <!-- Footer 4 -->
            <div class="<?php echo esc_attr($twenty_minutes_cols); ?> footer-block">
                <?php if (is_active_sidebar('footer-4')) : ?>
                    <?php dynamic_sidebar('footer-4'); ?>
                <?php else : ?>
                    <aside id="search-widget" class="widget py-3" role="complementary" aria-label="<?php esc_attr_e('footer4', 'twenty-minutes'); ?>">
                        <h3 class="widget-title"><?php esc_html_e('Search', 'twenty-minutes'); ?></h3>
                        <?php the_widget('WP_Widget_Search'); ?>
                    </aside>
                <?php endif; ?>
            </div>
          </div>
        </div>
    </div>

    <?php } ?>
  <div class="clear"></div>

  <div class="copywrap">
    <div class="container">
        <p>
            <a href="<?php 
            $twenty_minutes_copyright_link = get_theme_mod('twenty_minutes_copyright_link', '');
            if (empty($twenty_minutes_copyright_link)) {
                echo esc_url('https://www.theclassictemplates.com/products/free-twenty-minutes-wordpress-template');
            } else {
                echo esc_url($twenty_minutes_copyright_link);
            } ?>" target="_blank">
            <?php echo esc_html(get_theme_mod('twenty_minutes_copyright_line', __('Twenty Minutes WordPress Theme', 'twenty-minutes'))); ?>
            </a> 
            <?php echo esc_html('By Classic Templates', 'twenty-minutes'); ?>
        </p>
    </div>
  </div>
</div>

<?php if(get_theme_mod('twenty_minutes_scroll_hide',true)){ ?>
    <a id="button"><?php echo esc_html( get_theme_mod('twenty_minutes_scroll_text',__('TOP', 'twenty-minutes' )) ); ?></a>
<?php } ?>

<?php wp_footer(); ?>
</body>
</html>