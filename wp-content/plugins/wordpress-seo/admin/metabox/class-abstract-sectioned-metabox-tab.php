<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Base class for metabox that consist of multiple sections.
 */
abstract class WPSEO_Abstract_Metabox_Tab_With_Sections implements WPSEO_Metabox_Section {

	/**
	 * Holds the name of the tab.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Holds the HTML of the tab header.
	 *
	 * @var string
	 */
	protected $link_content;

	/**
	 * Holds the name of the tab header.
	 *
	 * @var string
	 */
	protected $link_title;

	/**
	 * Holds the classname of the tab header.
	 *
	 * @var string
	 */
	protected $link_class;

	/**
	 * Holds the aria label of the tab header.
	 *
	 * @var string
	 */
	protected $link_aria_label;

	/**
	 * Constructor.
	 *
	 * @param string $name         The name of the section, used as an identifier in the html.
	 *                             Can only contain URL safe characters.
	 * @param string $link_content The text content of the section link.
	 * @param array  $options      Optional link attributes.
	 */
	public function __construct( $name, $link_content, array $options = [] ) {
		$default_options = [
			'link_title'      => '',
			'link_class'      => '',
			'link_aria_label' => '',
		];

		$options = array_merge( $default_options, $options );

		$this->name = $name;

		$this->link_content    = $link_content;
		$this->link_title      = $options['link_title'];
		$this->link_class      = $options['link_class'];
		$this->link_aria_label = $options['link_aria_label'];
	}

	/**
	 * Outputs the section link if any section has been added.
	 *
	 * @return void
	 */
	public function display_link() {
		if ( $this->has_sections() ) {
			printf(
				'<li role="presentation"><a role="tab" href="#wpseo-meta-section-%1$s" id="wpseo-meta-tab-%1$s" aria-controls="wpseo-meta-section-%1$s" class="wpseo-meta-section-link %2$s"%3$s%4$s>%5$s</a></li>',
				esc_attr( $this->name ),
				esc_attr( $this->link_class ),
				( $this->link_title !== '' ) ? ' title="' . esc_attr( $this->link_title ) . '"' : '',
				( $this->link_aria_label !== '' ) ? ' aria-label="' . esc_attr( $this->link_aria_label ) . '"' : '',
				$this->link_content
			);
		}
	}

	/**
	 * Checks whether the tab has any sections.
	 *
	 * @return bool Whether the tab has any sections
	 */
	abstract protected function has_sections();
}
