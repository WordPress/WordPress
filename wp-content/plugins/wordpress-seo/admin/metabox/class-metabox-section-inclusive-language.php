<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Generates and displays the React root element for a metabox section.
 */
class WPSEO_Metabox_Section_Inclusive_Language implements WPSEO_Metabox_Section {

	/**
	 * Name of the section, used as an identifier in the HTML.
	 *
	 * @var string
	 */
	public $name = 'inclusive-language';

	/**
	 * Outputs the section link.
	 *
	 * @return void
	 */
	public function display_link() {
		printf(
			'<li role="presentation"><a role="tab" href="#wpseo-meta-section-%1$s" id="wpseo-meta-tab-%1$s" aria-controls="wpseo-meta-section-%1$s" class="wpseo-meta-section-link">
				<div class="wpseo-score-icon-container" id="wpseo-inclusive-language-score-icon"></div><span>%2$s</span></a></li>',
			esc_attr( $this->name ),
			esc_html__( 'Inclusive language', 'wordpress-seo' )
		);
	}

	/**
	 * Outputs the section content.
	 *
	 * @return void
	 */
	public function display_content() {
		printf(
			'<div role="tabpanel" id="wpseo-meta-section-%1$s" aria-labelledby="wpseo-meta-tab-%1$s" tabindex="0" class="wpseo-meta-section">',
			esc_attr( $this->name )
		);
		echo '<div id="wpseo-metabox-inclusive-language-root" class="wpseo-metabox-root"></div>', '</div>';
	}
}
