<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Metabox
 */

/**
 * Generates and displays an additional metabox section.
 */
class WPSEO_Metabox_Section_Additional implements WPSEO_Metabox_Section {

	/**
	 * Name of the section, used as an identifier in the HTML.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Content of the tab's section.
	 *
	 * @var string
	 */
	public $content;

	/**
	 * HTML to use in the tab header.
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
	 * Represents the content class.
	 *
	 * @var string
	 */
	private $content_class;

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
		$this->name            = $name;
		$this->content         = $content;
		$default_options       = [
			'link_class'      => '',
			'link_aria_label' => '',
			'content_class'   => 'wpseo-form',
		];
		$options               = wp_parse_args( $options, $default_options );
		$this->link_content    = $link_content;
		$this->link_class      = $options['link_class'];
		$this->link_aria_label = $options['link_aria_label'];
		$this->content_class   = $options['content_class'];
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
			$this->link_content
		);
	}

	/**
	 * Outputs the section content.
	 *
	 * @return void
	 */
	public function display_content() {
		$html  = sprintf(
			'<div role="tabpanel" id="wpseo-meta-section-%1$s" aria-labelledby="wpseo-meta-tab-%1$s" tabindex="0" class="wpseo-meta-section %2$s">',
			esc_attr( $this->name ),
			esc_attr( $this->content_class )
		);
		$html .= $this->content;
		$html .= '</div>';
		echo $html;
	}
}
