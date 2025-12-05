<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Generates the HTML for a metabox tab.
 */
class WPSEO_Metabox_Form_Tab implements WPSEO_Metabox_Tab {

	/**
	 * The tab identifier.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * The tab content.
	 *
	 * @var string
	 */
	private $content;

	/**
	 * The tab link content.
	 *
	 * @var string
	 */
	private $link_content;

	/**
	 * Additional tab content class.
	 *
	 * @var string
	 */
	private $tab_class;

	/**
	 * Additional tab link class.
	 *
	 * @var string
	 */
	private $link_class;

	/**
	 * Title attribute on the link span.
	 *
	 * @var string
	 */
	private $link_title;

	/**
	 * Arial label attribute on the link span.
	 *
	 * @var string
	 */
	private $link_aria_label;

	/**
	 * Does it contain a single tab.
	 *
	 * @var bool
	 */
	private $single;

	/**
	 * Constructor.
	 *
	 * @param string $name         The name of the tab, used as an identifier in the html.
	 * @param string $content      The tab content.
	 * @param string $link_content The text content of the tab link.
	 * @param array  $options      Optional link attributes.
	 */
	public function __construct( $name, $content, $link_content, array $options = [] ) {
		$default_options = [
			'tab_class'       => '',
			'link_class'      => '',
			'link_title'      => '',
			'link_aria_label' => '',
			'single'          => false,
		];

		$options = array_merge( $default_options, $options );

		$this->name            = $name;
		$this->content         = $content;
		$this->link_content    = $link_content;
		$this->tab_class       = $options['tab_class'];
		$this->link_class      = $options['link_class'];
		$this->link_title      = $options['link_title'];
		$this->link_aria_label = $options['link_aria_label'];
		$this->single          = $options['single'];
	}

	/**
	 * Returns the html for the tab link.
	 *
	 * @return string
	 */
	public function link() {

		$html = '<li class="%1$s%2$s"><a class="wpseo_tablink%3$s" href="#wpseo_%1$s"%4$s%5$s>%6$s</a></li>';

		if ( $this->single ) {
			$html = '<li class="%1$s%2$s"><span class="wpseo_tablink%3$s"%4$s%5$s>%6$s</span></li>';
		}

		return sprintf(
			$html,
			esc_attr( $this->name ),
			( $this->tab_class !== '' ) ? ' ' . esc_attr( $this->tab_class ) : '',
			( $this->link_class !== '' ) ? ' ' . esc_attr( $this->link_class ) : '',
			( $this->link_title !== '' ) ? ' title="' . esc_attr( $this->link_title ) . '"' : '',
			( $this->link_aria_label !== '' ) ? ' aria-label="' . esc_attr( $this->link_aria_label ) . '"' : '',
			$this->link_content
		);
	}

	/**
	 * Returns the html for the tab content.
	 *
	 * @return string
	 */
	public function content() {
		return sprintf(
			'<div id="%1$s" class="wpseotab %2$s">%3$s</div>',
			esc_attr( 'wpseo_' . $this->name ),
			esc_attr( $this->name ),
			$this->content
		);
	}
}
