<?php
/**
 * Twenty8teen repeating one to many Customizer control class
 * @package Twenty8teen
 */

class Twenty8teen_Customize_Repeat_One_Many_Control extends WP_Customize_Control {
	/**
	 * This type of customize control is used to create repeating inputs for
	 * one to many relationships.
	 * The value in PHP is an associative array of arrays. It is converted to a
	 * JSON array of objects for input in the Customizer.
	 */
	public $type = 'repeat-one-many';
	/**
	 * The list of values to select from for the "many".
	 */
	public $value_choices = array();
	protected $key_list = '';
	protected $value_list = '';

	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
		add_filter( "customize_sanitize_{$this->setting->id}", array( $this, 'sanitize_one_one' ), 9, 1 );
		add_filter( "customize_sanitize_js_{$this->setting->id}", array( $this, 'sanitize_js_many_one' ), 9, 1 );
		$this->key_list = $this->choices ? 'customize-'. $this->id . '-key-list' : '';
		$this->value_list = $this->value_choices ? 'customize-'. $this->id . '-value-list' : '';
	}

	/**
	 * Enqueue scripts/styles.
	 */
	public function enqueue() {
		wp_enqueue_style( 'twenty8teen-customize-one-many', get_template_directory_uri() .
      '/css/customize-one-many.css', array(), '20181219' );
		wp_enqueue_script( 'twenty8teen-customize-one-many', get_template_directory_uri() .
      '/js/customize-one-many.js', array( 'jquery', 'customize-controls' ), '20181215' );
	}

	/**
	 * Generates a one to one input of the control content.
	 */
	public function build_one_one_content( $one_key, $one_value ) {
		$out = '
		<div class="repeat-one-one submitbox">
			<input type="text" value="' . esc_attr( $one_key ) . '"
				 class="one-key" list="' . $this->key_list . '" />
			<input type="text" value="' . esc_attr( $one_value ) . '"
				class="one-value" list="'. $this->value_list . '" />
			<span class="button-link item-delete submitdelete" >X</span>
		</div>';
		return $out;
	}

	/**
	 * Displays the control content.
	 */
	public function render_content() {
		if ( ! empty( $this->label ) ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php endif;
		if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		<?php endif;
		if ( ! empty( $this->key_list ) ) {
			$list = '<datalist id="'. $this->key_list . '">';
			$out = '<ul style="font-style:normal; -moz-columns:2; -webkit-columns:2; columns:2">';
			foreach ( $this->choices as $one_key => $choice ) {
				$list .= '<option value="' . esc_attr( $one_key ) . '">' . esc_html( $choice ) . '</option>';
				$out .= '<li>' . esc_html( $choice . ' (' . $one_key . ')' ) . '</li>';
			}
			echo $out . '</ul>' . $list . '</datalist>';
		}
		if ( ! empty( $this->value_list ) ) {
			$list = '<datalist id="'. $this->value_list . '">';
			foreach ( $this->value_choices as $key => $choice ) {
				$list .= '<option value="' . esc_attr( $key ) . '">' . esc_html( $choice ) . '</option>';
			}
			echo $list . '</datalist>';
		}

		$one_many = $this->value();
		// Expect an array.
		if ( ! is_array( $one_many ) ) {
			$one_many = array( $one_many );
		}	?>

		<div class="customize-repeat-one-many">
		 	<?php
	 		foreach ( $one_many as $one_key => $many ) {
				if ( ! is_array( $many ) ) {
					$many = array( $many );
				}
	 			foreach ( $many as $one_value ) {
					echo $this->build_one_one_content( $one_key, $one_value );
				}
	 		}
		 	?>
		</div>
		<button type="button" class="button add-content button-default">
			<?php esc_html_e( 'Add', 'twenty8teen' ); ?></button>
		<input  type="hidden" <?php echo $this->link(); ?>
			id="<?php echo esc_attr( '_customize-input-' . $this->id ); ?>"
			value="" />

	<?php }

	/**
	 * Set the parameters passed to the JavaScript via JSON.
	 */
	public function to_json() {
		parent::to_json();
		$this->json['newInputContent'] = $this->build_one_one_content( '', '' );
	}

	/**
	 * Sanitize the setting value for javascript, (called as a filter).
	 */
	public function sanitize_js_many_one( $value ) {
    $one_one = array();
 		foreach ( $value as $one_key => $many ) {
			if ( ! is_array( $many ) ) {
				$many = array( $many );
			}
 			foreach ( $many as $one_value ) {
				$one_one[] = array( 'one_key' => $one_key, 'one_value' => $one_value );
			}
		}
		return $one_one;
	}

	/**
	 * Sanitize the control input, (called as a filter).
	 */
	public function sanitize_one_one( $input ) {
		$new = array();
		foreach ( $input as $obj ) {
			$one_key = trim( $obj['one_key'] );
			$one_value = trim( $obj['one_value'] );
			if ( $one_key && $one_value ) {  // Both need to be non-empty.
				if ( ! isset( $new[$one_key] ) ) {
					$new[$one_key] = array();
				}
				$new[$one_key][] = $one_value;
			}
		}
		return $new;
	}
}
