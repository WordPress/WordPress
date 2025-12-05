<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Generates and displays the React root element for a metabox section.
 */
class WPSEO_Metabox_Section_React implements WPSEO_Metabox_Section {

	/**
	 * Name of the section, used as an identifier in the HTML.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Content to use before the React root node.
	 *
	 * @var string
	 */
	public $content;

	/**
	 * Content to use to display the button to open this content block.
	 *
	 * @var string
	 */
	private $link_content;

	/**
	 * Class to add to the link.
	 *
	 * @var string
	 */
	private $link_class;

	/**
	 * Aria label to use for the link.
	 *
	 * @var string
	 */
	private $link_aria_label;

	/**
	 * Additional html content to be displayed within the section.
	 *
	 * @var string
	 */
	private $html_after;

	/**
	 * Constructor.
	 *
	 * @param string $name         The name of the section, used as an identifier in the html.
	 *                             Can only contain URL safe characters.
	 * @param string $link_content The text content of the section link.
	 * @param string $content      Optional. Content to use above the React root element.
	 * @param array  $options      Optional link attributes.
	 */
	public function __construct( $name, $link_content, $content = '', array $options = [] ) {
		$this->name    = $name;
		$this->content = $content;

		$default_options = [
			'link_class'      => '',
			'link_aria_label' => '',
			'html_after'      => '',
		];

		$options = wp_parse_args( $options, $default_options );

		$this->link_content    = $link_content;
		$this->link_class      = $options['link_class'];
		$this->link_aria_label = $options['link_aria_label'];
		$this->html_after      = $options['html_after'];
	}

	/**
	 * Outputs the section link.
	 *
	 * @return void
	 */
	public function display_link() {
		printf(
			'<li role="presentation"><a role="tab" href="#wpseo-meta-section-%1$s" id="wpseo-meta-tab-%1$s" aria-controls="wpseo-meta-section-%1$s" class="wpseo-meta-section-link %2$s"%3$s>%4$s</a></li>',
			esc_attr( $this->name ),
			esc_attr( $this->link_class ),
			( $this->link_aria_label !== '' ) ? ' aria-label="' . esc_attr( $this->link_aria_label ) . '"' : '',
			wp_kses_post( $this->link_content )
		);
	}

	/**
	 * Outputs the section content.
	 *
	 * @return void
	 */
	public function display_content() {
		add_filter( 'wp_kses_allowed_html', [ 'WPSEO_Utils', 'extend_kses_post_with_forms' ] );
		add_filter( 'wp_kses_allowed_html', [ 'WPSEO_Utils', 'extend_kses_post_with_a11y' ] );

		printf(
			'<div role="tabpanel" id="wpseo-meta-section-%1$s" aria-labelledby="wpseo-meta-tab-%1$s" tabindex="0" class="wpseo-meta-section">',
			esc_attr( $this->name )
		);
		echo wp_kses_post( $this->content );
		echo '<div id="wpseo-metabox-root" class="wpseo-metabox-root"></div>';
		echo wp_kses_post( $this->html_after );
		echo '</div>';

		remove_filter( 'wp_kses_allowed_html', [ 'WPSEO_Utils', 'extend_kses_post_with_forms' ] );
		remove_filter( 'wp_kses_allowed_html', [ 'WPSEO_Utils', 'extend_kses_post_with_a11y' ] );
	}
}
