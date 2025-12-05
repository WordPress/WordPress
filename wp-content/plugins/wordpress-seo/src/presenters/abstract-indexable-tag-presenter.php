<?php

namespace Yoast\WP\SEO\Presenters;

/**
 * Abstract presenter class for indexable tag presentations.
 *
 * @phpcs:disable Yoast.Files.FileName.InvalidClassFileName
 */
abstract class Abstract_Indexable_Tag_Presenter extends Abstract_Indexable_Presenter {

	public const META_PROPERTY_CONTENT = '<meta property="%2$s" content="%1$s"%3$s />';
	public const META_NAME_CONTENT     = '<meta name="%2$s" content="%1$s"%3$s />';
	public const LINK_REL_HREF         = '<link rel="%2$s" href="%1$s"%3$s />';
	public const DEFAULT_TAG_FORMAT    = self::META_NAME_CONTENT;

	/**
	 * The tag format including placeholders.
	 *
	 * @var string
	 */
	protected $tag_format = self::DEFAULT_TAG_FORMAT;

	/**
	 * The method of escaping to use.
	 *
	 * @var string
	 */
	protected $escaping = 'attribute';

	/**
	 * Returns a tag in the head.
	 *
	 * @return string The tag.
	 */
	public function present() {
		$value = $this->get();

		if ( ! \is_string( $value ) || $value === '' ) {
			return '';
		}

		/**
		 * There may be some classes that are derived from this class that do not use the $key property
		 * in their $tag_format string. In that case the key property will simply not be used.
		 */
		return \sprintf(
			$this->tag_format,
			$this->escape_value( $value ),
			$this->key,
			\is_admin_bar_showing() ? ' class="yoast-seo-meta-tag"' : ''
		);
	}

	/**
	 * Escaped the output.
	 *
	 * @param string $value The desired method of escaping; 'html', 'url' or 'attribute'.
	 *
	 * @return string The escaped value.
	 */
	protected function escape_value( $value ) {
		switch ( $this->escaping ) {
			case 'html':
				return \esc_html( $value );
			case 'url':
				return \esc_url( $value, null, 'attribute' );
			case 'attribute':
			default:
				return \esc_attr( $value );
		}
	}
}
