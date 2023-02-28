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
#[AllowDynamicProperties]
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
	 * ID.
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
	 * Origin of the content when the content has been customized.
	 * When customized, origin takes on the value of source and source becomes
	 * 'custom'.
	 *
	 * @since 5.9.0
	 * @var string
	 */
	public $origin;

	/**
	 * Post ID.
	 *
	 * @since 5.8.0
	 * @var int|null
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
	 * @var bool
	 */
	public $has_theme_file;

	/**
	 * Whether a template is a custom template.
	 *
	 * @since 5.9.0
	 *
	 * @var bool
	 */
	public $is_custom = true;

	/**
	 * Author.
	 *
	 * A value of 0 means no author.
	 *
	 * @since 5.9.0
	 * @var int
	 */
	public $author;

	/**
	 * Post types.
	 *
	 * @since 5.9.0
	 * @var array
	 */
	public $post_types;

	/**
	 * Area.
	 *
	 * @since 5.9.0
	 * @var string
	 */
	public $area;
}
