<?php
namespace HelloElementor\Includes\Customizer;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Hello_Customizer_Upsell extends \WP_Customize_Section {

	public $heading;

	public $description;

	public $button_text;

	public $button_url;

	/**
	 * Render the section, and the controls that have been added to it.
	 */
	protected function render() {
		?>
		<li id="accordion-section-<?php echo esc_attr( $this->id ); ?>" class="accordion-section control-section control-section-<?php echo esc_attr( $this->id ); ?> cannot-expand">
			<h3 class="accordion-section-title"><?php echo esc_html( $this->heading ); ?></h3>
			<p class="accordion-section-description"><?php echo esc_html( $this->description ); ?></p>
			<div class="accordion-section-buttons">
				<a href="<?php echo esc_url( $this->button_url ); ?>" target="_blank"><?php echo esc_html( $this->button_text ); ?></a>
			</div>
		</li>
		<?php
	}
}
