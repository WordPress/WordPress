<?php

if ( ! class_exists( 'WP_Customize_Control' ) )
    return NULL;

/**
 * Redirect to Background link
 *
 * @access public
 */
class WP_Background_Nav_Customize_Control extends WP_Customize_Control {

    public $type = 'background_nav';

    /**
     * Render the control's content.
     */
    public function render_content() {  ?>
        <label for="create-background-nav-submit"><span class="customize-control-title"><?php _e('Change theme background', 'travelify'); ?></span></label>
        <a href="<?php echo admin_url('customize.php?autofocus[control]=background_image'); ?>" class="button button-secondary" id="create-background-nav-submit" tabindex="0"><?php _e('Click Here', 'travelify'); ?></a><?php
    }

}

/**
 * Multi Select category customize control class.
 *
 * @access public
 */
class tavelify_Customize_Control_Multi_Select_Category extends WP_Customize_Control {

    /**
     * The type of customize control being rendered.
     *
     * @access public
     * @var    string
     */
    public $type = 'multi-select-cat';

    /**
     * Displays the control content.
     *
     * @access public
     * @return void
     */
    public function render_content() { ?>

        <label for="frontpage_posts_cats"><b><?php _e( 'Homepage posts categories:', 'travelify' ); ?></b></label>
        <small><?php _e( 'Only posts that belong to the categories selected here will be displayed on the front page.', 'travelify' ); ?></small><br><br><?php 

        $options = !is_array( $this->value() ) ? explode( ',', $this->value() ) : $this->value(); ?>

        <select <?php $this->link(); ?> name="travelify_theme_options[front_page_category][]" id="frontpage_posts_cats" multiple="multiple" class="select-multiple" style="width: 100%;">
            <option value="0" <?php if ( empty( $options ) ) { selected( true, true ); } ?>><?php _e( '--Disabled--', 'travelify' ); ?></option><?php

            $categories = get_categories();
            foreach ( $categories as $category) :?>
                <option value="<?php echo $category->cat_ID; ?>" <?php if ( in_array( $category->cat_ID, $options ) ) {echo 'selected="selected"';}?>><?php echo $category->cat_name; ?></option><?php 
            endforeach; ?>
        </select><br />

        <?php if ( !empty( $this->description ) ) : ?>
            <span class="description"><?php echo $this->description; ?></span>
        <?php endif; ?>
    <?php }
}

/**
 * Class to create a custom layout control
 */
class Travelify_Layout_Picker_Custom_Control extends WP_Customize_Control
{

    /**
     * Declare the control type.
     *
     * @access public
     * @var string
     */
    public $type = 'radio-image';

     /**
      * Render the control to be displayed in the Customizer.
      */
     public function render_content() {
             if ( empty( $this->choices ) ) {
                     return;
             }

             $name = $this->id;
             $images = array(
                        'no-sidebar' 		=> get_template_directory_uri().'/library/panel/images/no-sidebar.png',
                        'no-sidebar-full-width' => get_template_directory_uri().'/library/panel/images/no-sidebar-fullwidth.png',
                        'no-sidebar-one-column'	=> get_template_directory_uri().'/library/panel/images/one-column.png',
                        'left-sidebar' 		=> get_template_directory_uri().'/library/panel/images/left-sidebar.png',
                        'right-sidebar'         => get_template_directory_uri().'/library/panel/images/right-sidebar.png',
            )?>

             <span class="customize-control-title">
                    <?php echo esc_attr( $this->label ); ?>
                    <?php if ( ! empty( $this->description ) ) : ?>
                            <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
                    <?php endif; ?>
             </span>
             <div id="input_<?php echo $this->id; ?>" class="image">
                     <?php foreach ( $this->choices as $value => $label ) : ?>
                                <label for="<?php echo $this->id .'['.$value.']'; ?>">
                                    <img src="<?php echo $images[$value]; ?>" alt="<?php echo esc_attr( $value ); ?>" title="<?php echo esc_attr( $value ); ?>">
                                    <input class="image-select" type="radio" value="<?php echo esc_attr( $value ); ?>" id="<?php echo $this->id .'['. $value . ']'; ?>" name="<?php echo esc_attr( $name ); ?>" <?php $this->link(); checked( $this->value(), $value ); ?> />
                                    <span class='radio-text'><?php echo $label; ?></span>
                                </label>
                     <?php endforeach; ?>
             </div><?php
     }
}

/**
 * Class to create a custom Featured Slider control
 */
class Travelify_Featured_Slider_Custom_Control extends WP_Customize_Control
{
    /**
     * The type of customize control being rendered.
     *
     * @access public
     * @var    string
     */
    public $type = 'featured-slider';

    /**
      * Enqueue scripts and styles for the custom control.
      *
      * @access public
      */
    public function enqueue() {
        wp_enqueue_script( 'travelify_cloneya_js', get_template_directory_uri() . '/library/js/jquery-cloneya.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'travelify_custom_js', get_template_directory_uri() . '/library/js/customizer_custom.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-draggable' ) );
    }

    /**
     * Render the content on the theme customizer page
     */
    public function render_content()
    { ?>
                 <!-- Option for Featured Post Slider -->
           <div id="featuredslider">
               <h3><?php _e( 'Featured Slider', 'travelify' ); ?></h3>
               <ul class="featured-slider-sortable clone-wrapper"><?php
                    $options = get_option('travelify_theme_options');
                    $slider_count = ( isset($options[ 'featured_post_slider' ]) && count( $options[ 'featured_post_slider' ] ) > 0 ) ? count( $options[ 'featured_post_slider' ] ) : 3 ;

                    for ( $i = 1; $i <= $slider_count; $i++ ): ?>
                        <li class="toclone">
                            <label class="handle customize-control-title"><?php _e( 'Slide #', 'travelify' ); ?><span class="count"><?php echo absint( $i ); ?></span></label>
                            <input class="featured_post_slider" size=7 type="text" name="travelify_theme_options[featured_post_slider][<?php echo absint( $i ); ?>]" value="<?php if( isset($options[ 'featured_post_slider' ][$i] ) ) echo absint( $options[ 'featured_post_slider' ][$i] ); ?>" />
                            <a href="<?php bloginfo ( 'url' );?>/wp-admin/post.php?post=<?php if( isset($options[ 'featured_post_slider' ][$i] ) ) echo absint( $options[ 'featured_post_slider' ][ $i ] ); ?>&action=edit" class="button slider_edit" title="<?php esc_attr_e('Edit','travelify'); ?>" target="_blank"><p class="dashicons-before dashicons-edit"></p></a>

                            <a href="#" class="clone button-primary">+</a>
                            <a href="#" class="delete button">-</a>
                        </li>
                    <?php endfor; ?>
                </ul><?php
                $value = isset($options[ 'featured_post_slider' ])  ? json_encode($options['featured_post_slider']) : ''; ?>
                <input id="featured_slider" type="hidden" name="travelify_theme_options[featured_post_slider]" <?php echo $this->link()?> value="<?php echo $value; ?>"><br>

                <p><strong><?php _e( 'How to use the featured slider?', 'travelify' ); ?></strong><p/>
                <ul class="slider-note">
                        <li><?php _e( 'Create Post or Page and add featured image to it.', 'travelify' ); ?></li>
                        <li><?php _e( 'Add all the Post ID that you want to use in the featured slider. Post ID can be found at All Posts table in last column', 'travelify' ); ?></li>
                        <li><?php _e( 'Featured Slider will show featured images, Title and excerpt of the respected added post IDs.', 'travelify' ); ?></li>
                        <li><?php _e( 'The recommended image size is', 'travelify' ); ?><strong> 1018px x 460px.</strong></li>
                </ul>
            </div><!-- .featured-slider -->
<?php
    }
}

/**
 * Class to create a Travelify important links
 */
class Travelify_Important_Links extends WP_Customize_Control {

   public $type = "travelify-important-links";

   public function render_content() {
      //Add Theme instruction, Support Forum, Demo Link, Rating Link
      $important_links = array(
            'other_themes' => array(
            'link' => esc_url('https://colorlib.com/'),
            'text' => __('Other Themes', 'travelify'),
         ),
            'rate' => array(
            'link' => esc_url('http://wordpress.org/support/view/theme-reviews/travelify?filter=5'),
            'text' => __('Rate this Theme', 'travelify'),
         ),
            'theme_instruction' => array(
            'link' => esc_url('https://colorlib.com/wp/support/travelify/'),
            'text' => __('Theme Instructions', 'travelify'),
         ),
            'rating' => array(
            'link' => esc_url('https://wordpress.org/support/view/theme-reviews/travelify'),
            'text' => __('Rate This Theme', 'travelify'),
         ),
            'support' => array(
            'link' => esc_url('https://colorlib.com/wp/forums/'),
            'text' => __('Support', 'travelify'),
         ),
            'facebook' => array(
            'link' => esc_url('https://www.facebook.com/colorlib'),
            'text' => __('Facebook', 'travelify'),
         ),
            'twitter' => array(
            'link' => esc_url('http://twitter.com/colorlib/'),
            'text' => __('Twitter', 'travelify'),
         )
      );
      foreach ($important_links as $important_link) {
         echo '<p><a target="_blank" href="' . $important_link['link'] . '" >' . esc_attr($important_link['text']) . ' </a></p>';
      }
   }

}

/**
 * Add CSS for custom controls
 */
function travelify_customizer_custom_control_css() {
	?>
    <style>
        .customize-control-radio-image .image.ui-buttonset input[type=radio] { height: auto; }
        .customize-control-radio-image .image label { background: #fff; box-sizing: border-box; display: inline-block; width: 50%; float: left; line-height: 35px; padding: 5px 10px; }
        .customize-control-radio-image label img { border: 0; height: auto; width: 100%; opacity: 0.5; }
        .customize-control-radio-image label:hover img { opacity: 0.9; border-color: #999; }
        .customize-control-radio-image .image{ margin: 20px 0; }
        span.radio-text { display: inline-block; word-wrap: break-word; width: 83px; line-height: 1.2em; vertical-align: middle; }
        .featured-slider-sortable li { margin-top: 20px; }
        .featured-slider-sortable input[type="text"]{ width: 70px !important; }
        .featured-slider-sortable .dashicons-edit { margin: 3px 0;}
        .featured-slider-sortable label { cursor: move!important; display: inline-block; margin-right: 15px;}
        #featuredslider .slider-note { list-style-type: square; margin: 0 auto; width: 80%;}
        #customize-control-travelify_theme_options-transition_duration input[type="text"], #customize-control-travelify_theme_options-transition_delay input[type="text"], #customize-control-travelify_theme_options-slider_quantity input[type="text"]{ width: 40px !important; }
        #customize-control-travelify_theme_options-transition_duration span, #customize-control-travelify_theme_options-transition_delay span, #customize-control-travelify_theme_options-slider_quantity span{ display: inline; margin-right: 10px; }
        .featured-slider-sortable li a.clone { display: none; }
        .featured-slider-sortable li:last-child  a.clone { display: inline-block; }
        .featured-slider-sortable li:first-child  a.delete { display: none; }
        .featured-slider-sortable li a.slider_edit { padding: 0 5px; }
        li#accordion-section-travelify_important_links h3.accordion-section-title,
        li#accordion-section-travelify_important_links h3.accordion-section-title:focus { background-color: #00cc00 !important; color: #fff !important; }
        li#accordion-section-travelify_important_links h3.accordion-section-title:hover { background-color: #00b200 !important; color: #fff !important; }
        li#accordion-section-travelify_important_links h3.accordion-section-title:after { color: #fff !important; }
    </style><?php
}
add_action( 'customize_controls_print_styles', 'travelify_customizer_custom_control_css' ); ?>