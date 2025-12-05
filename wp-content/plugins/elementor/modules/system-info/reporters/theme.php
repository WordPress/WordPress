<?php
namespace Elementor\Modules\System_Info\Reporters;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor theme report.
 *
 * Elementor system report handler class responsible for generating a report for
 * the theme.
 *
 * @since 1.0.0
 */
class Theme extends Base {

	/**
	 * Theme.
	 *
	 * Holds the sites theme object.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var \WP_Theme WordPress theme object.
	 */
	private $theme = null;

	/**
	 * Get theme reporter title.
	 *
	 * Retrieve theme reporter title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Reporter title.
	 */
	public function get_title() {
		return 'Theme';
	}

	/**
	 * Get theme report fields.
	 *
	 * Retrieve the required fields for the theme report.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Required report fields with field ID and field label.
	 */
	public function get_fields() {
		$fields = [
			'name' => 'Name',
			'version' => 'Version',
			'author' => 'Author',
			'is_child_theme' => 'Child Theme',
		];

		if ( $this->get_parent_theme() ) {
			$parent_fields = [
				'parent_name' => 'Parent Theme Name',
				'parent_version' => 'Parent Theme Version',
				'parent_author' => 'Parent Theme Author',
			];
			$fields = array_merge( $fields, $parent_fields );
		}

		return $fields;
	}

	/**
	 * Get theme.
	 *
	 * Retrieve the theme.
	 *
	 * @since 1.0.0
	 * @deprecated 3.1.0 Use `get_theme()` method instead.
	 * @access protected
	 *
	 * @return \WP_Theme WordPress theme object.
	 */
	protected function _get_theme() {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.1.0', 'get_theme()' );

		return $this->get_theme();
	}

	/**
	 * Get theme.
	 *
	 * Retrieve the theme.
	 *
	 * @since 3.1.0
	 * @access private
	 *
	 * @return \WP_Theme WordPress theme object.
	 */
	private function get_theme() {
		if ( is_null( $this->theme ) ) {
			$this->theme = wp_get_theme();
		}
		return $this->theme;
	}

	/**
	 * Get parent theme.
	 *
	 * Retrieve the parent theme.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return \WP_Theme|false WordPress theme object, or false if the current theme is not a child theme.
	 */
	protected function get_parent_theme() {
		return $this->get_theme()->parent();
	}

	/**
	 * Get theme name.
	 *
	 * Retrieve the theme name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value The theme name.
	 * }
	 */
	public function get_name() {
		return [
			'value' => $this->get_theme()->get( 'Name' ),
		];
	}

	/**
	 * Get theme author.
	 *
	 * Retrieve the theme author.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value The theme author.
	 * }
	 */
	public function get_author() {
		return [
			'value' => $this->get_theme()->get( 'Author' ),
		];
	}

	/**
	 * Get theme version.
	 *
	 * Retrieve the theme version.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value The theme version.
	 * }
	 */
	public function get_version() {
		return [
			'value' => $this->get_theme()->get( 'Version' ),
		];
	}

	/**
	 * Is the theme is a child theme.
	 *
	 * Whether the theme is a child theme.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value          Yes if the theme is a child theme, No otherwise.
	 *    @type string $recommendation Theme source code modification recommendation.
	 * }
	 */
	public function get_is_child_theme() {
		$is_child_theme = is_child_theme();

		$result = [
			'value' => $is_child_theme ? 'Yes' : 'No',
		];

		if ( ! $is_child_theme ) {
			$result['recommendation'] = sprintf(
				/* translators: %s: WordPress child themes documentation. */
				__( 'If you want to modify the source code of your theme, we recommend using a <a href="%s">child theme</a>.', 'elementor' ),
				'https://go.elementor.com/wordpress-child-themes/'
			);
		}

		return $result;
	}

	/**
	 * Get parent theme version.
	 *
	 * Retrieve the parent theme version.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value The parent theme version.
	 * }
	 */
	public function get_parent_version() {
		return [
			'value' => $this->get_parent_theme()->get( 'Version' ),
		];
	}

	/**
	 * Get parent theme author.
	 *
	 * Retrieve the parent theme author.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value The parent theme author.
	 * }
	 */
	public function get_parent_author() {
		return [
			'value' => $this->get_parent_theme()->get( 'Author' ),
		];
	}

	/**
	 * Get parent theme name.
	 *
	 * Retrieve the parent theme name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value The parent theme name.
	 * }
	 */
	public function get_parent_name() {
		return [
			'value' => $this->get_parent_theme()->get( 'Name' ),
		];
	}
}
