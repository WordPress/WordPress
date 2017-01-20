<?php
/**
 * Customize API: WP_Widget_Area_Customize_Control class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */

/**
 * Widget Area Customize Control class.
 *
 * @since 3.9.0
 *
 * @see WP_Customize_Control
 */
class WP_Widget_Area_Customize_Control extends WP_Customize_Control {

	/**
	 * Customize control type.
	 *
	 * @since 3.9.0
	 * @access public
	 * @var string
	 */
	public $type = 'sidebar_widgets';

	/**
	 * Sidebar ID.
	 *
	 * @since 3.9.0
	 * @access public
	 * @var int|string
	 */
	public $sidebar_id;

	/**
	 * Refreshes the parameters passed to the JavaScript via JSON.
	 *
	 * @since 3.9.0
	 * @access public
	 */
	public function to_json() {
		parent::to_json();
		$exported_properties = array( 'sidebar_id' );
		foreach ( $exported_properties as $key ) {
			$this->json[ $key ] = $this->$key;
		}
	}

	/**
	 * Renders the control's content.
	 *
	 * @since 3.9.0
	 * @access public
	 */
	public function render_content() {
		$id = 'reorder-widgets-desc-' . str_replace( array( '[', ']' ), array( '-', '' ), $this->id );
		?>
		<button type="button" class="button add-new-widget" aria-expanded="false" aria-controls="available-widgets">
			<?php _e( 'Add a Widget' ); ?>
		</button>
		<button type="button" class="button-link reorder-toggle" aria-label="<?php esc_attr_e( 'Reorder widgets' ); ?>" aria-describedby="<?php echo esc_attr( $id ); ?>">
			<span class="reorder"><?php _e( 'Reorder' ); ?></span>
			<span class="reorder-done"><?php _e( 'Done' ); ?></span>
		</button>
		<p class="screen-reader-text" id="<?php echo esc_attr( $id ); ?>"><?php _e( 'When in reorder mode, additional controls to reorder widgets will be available in the widgets list above.' ); ?></p>
		<?php
	}
}
