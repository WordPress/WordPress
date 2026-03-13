<?php

if ( ! class_exists( 'WP_Customize_Control' ) )
    return NULL;

/**
 * A class to create a dropdown for all categories in your WordPress site
 */
 class Twenty_Minutes_Category_Dropdown_Custom_Control extends WP_Customize_Control {
    private $cats = false;

    public function __construct($manager, $id, $args = array(), $options = array()){
      $this->cats = get_categories($options);
      parent::__construct( $manager, $id, $args );
    }

    /**
     * Render the content of the category dropdown
     *
     * @return HTML
     */
    public function render_content(){
      if(!empty($this->cats)) { ?>
        <label>
          <span class="customize-category-select-control"><?php echo esc_html( $this->label ); ?></span>
          <select <?php $this->link(); ?>>
            <option value="0"><?php esc_html_e('Select Category','twenty-minutes'); ?> </option>
            <?php
              foreach ( $this->cats as $cat )
              {
                printf('<option value="%s" %s>%s</option>', esc_attr( $cat->term_id ), selected($this->value(), esc_attr( $cat->term_id ), false), esc_html( $cat->name ) );
              }
            ?>
          </select>
        </label>
    <?php }
    }
  }
  // Customizer slider control
  class Twenty_Minutes_Slider_Custom_Control extends WP_Customize_Control {
    public $type = 'slider_control';
    public function enqueue() {
      wp_enqueue_script( 'twenty-minutes-controls-js', trailingslashit( esc_url(get_template_directory_uri()) ) . 'js/custom-controls.js', array( 'jquery', 'jquery-ui-core' ), '1.0', true );
      wp_enqueue_style( 'twenty-minutes-controls-css', trailingslashit( esc_url(get_template_directory_uri()) ) . 'css/custom-controls.css', array(), '1.0', 'all' );
    }
    public function render_content() {
    ?>
      <div class="slider-custom-control">
        <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span><input type="number" id="<?php echo esc_attr( $this->id ); ?>" name="<?php echo esc_attr( $this->id ); ?>" value="<?php echo esc_attr( $this->value() ); ?>" class="customize-control-slider-value"  <?php $this->link(); ?> />
        <div class="slider" slider-min-value="<?php echo esc_attr( $this->input_attrs['min'] ); ?>" slider-max-value="<?php echo esc_attr( $this->input_attrs['max'] ); ?>" slider-step-value="<?php echo esc_attr( $this->input_attrs['step'] ); ?>"></div><span class="slider-reset dashicons dashicons-image-rotate" slider-reset-value="<?php echo esc_attr( $this->value() ); ?>"></span>
      </div>
    <?php
    }
  }

?>