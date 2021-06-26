<?php
/**
 * Blocks API: WP_Block_Template class
 *
 * @package WordPress
 * @since 5.8.0
 */

/**
 * Class representing a block template.
 *
 * @since 5.8.0
 */
class WP_Block_Template {

	/**
	 * Type: wp_template.
	 *
	 * @since 5.8.0
	 * @var string
	 */
	public $type;

	/**
	 * Theme.
	 *
	 * @since 5.8.0
	 * @var string
	 */
	public $theme;

	/**
	 * Template slug.
	 *
	 * @since 5.8.0
	 * @var string
	 */
	public $slug;

	/**
	 * Id.
	 *
	 * @since 5.8.0
	 * @var string
	 */
	public $id;

	/**
	 * Title.
	 *
	 * @since 5.8.0
	 * @var string
	 */
	public $title = '';

	/**
	 * Content.
	 *
	 * @since 5.8.0
	 * @var string
	 */
	public $content = '';

	/**
	 * Description.
	 *
	 * @since 5.8.0
	 * @var string
	 */
	public $description = '';

	/**
	 * Source of the content. `theme` and `custom` is used for now.
	 *
	 * @since 5.8.0
	 * @var string
	 */
	public $source = 'theme';

	/**
	 * Post Id.
	 *
	 * @since 5.8.0
	 * @var integer|null
	 */
	public $wp_id;

	/**
	 * Template Status.
	 *
	 * @since 5.8.0
	 * @var string
	 */
	public $status;

	/**
	 * Whether a template is, or is based upon, an existing template file.
	 *
	 * @since 5.8.0
	 * @var boolean
	 */
	public $has_theme_file;
}
