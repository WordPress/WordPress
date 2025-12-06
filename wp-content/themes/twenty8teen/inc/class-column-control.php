<?php
/**
 * Twenty8teen checkbox column Customizer control class
 * The idea is from http://justintadlock.com/archives/2015/05/26/multiple-checkbox-customizer-control
 * @package Twenty8teen
 */

class Twenty8teen_Customize_Column_Control extends WP_Customize_Control {
	/**
	 * This type of customize control is used to create a grid, one column at a time.
	 */
	public $type = 'checkbox-column';

	public $grid_label = '';
	public $column = '';
	/**
	 * Enqueue scripts/styles.
	 */
	public function enqueue() {
		wp_enqueue_style( 'twenty8teen-customize-column', get_template_directory_uri() .
      '/css/customize-column.css', array(), '20211216' );
		wp_enqueue_script( 'twenty8teen-customize-column', get_template_directory_uri() .
      '/js/customize-column.js', array( 'jquery', 'customize-controls' ), '20171231' );
		add_filter( "customize_sanitize_{$this->setting->id}", array( $this, 'sanitize_column' ), 9, 1 );
	}

	/**
	 * Displays the control content.
	 */
	public function render_content() {
		if ( empty( $this->choices ) || empty( $this->column ) ) {
			return;
		}

		// It is expected that only the first column control will have a grid label.
		if ( ! empty( $this->grid_label ) ) : ?>
			<hr style="width: 260px;">
			<span class="customize-control-title"><?php echo esc_html( $this->grid_label ); ?></span>
		<?php endif;

		if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		</li><li class="customize-control customize-control-checkbox-column" style="clear:both">
		<?php endif;

		$current = is_array( $this->value() ) ? $this->value() : explode( ' ', $this->value() );
		if ( ! empty( $this->grid_label ) ) :
			$labels = array_map( 'esc_html', array_values( $this->choices ) );
			?>
			<div class="customize-checkbox-column-labels">
				<?php echo join( '<br />', $labels ); ?>
			</div>
		<?php endif; ?>

		<div class="customize-checkbox-column">
			<span class="customize-col-header"> <?php echo esc_html( $this->column ); ?>
			</span>
			<?php foreach ( $this->choices as $value => $label ) : ?>
      	<input type="checkbox" value="<?php echo esc_attr( $value ); ?>"
					title="<?php echo esc_attr( $label .', '. $this->column ); ?>"
					<?php checked( in_array( $value, $current ) ); ?> />
				<?php endforeach; ?>
		</div>
		<input type="hidden" <?php $this->link(); ?>
			value="<?php echo esc_attr( join( ' ', $current ) ); ?>" />
	<?php }

	/**
	 * Sanitize the control input, (called as a filter).
	 */
	public function sanitize_column( $input ) {
		if ( is_string( $input ) ) {
			$delim = strpos( $input, ',' );
			$input = explode( ' ', substr( $input, ( $delim === false ? 0 : $delim +1 ) ) );
		}
		$valid = array_keys( $this->choices );
		return join( ' ', array_intersect( $input, $valid ) );
	}
}
