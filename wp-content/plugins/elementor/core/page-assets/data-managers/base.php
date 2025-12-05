<?php
namespace Elementor\Core\Page_Assets\Data_Managers;

use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Assets Data.
 *
 * @since 3.3.0
 */
abstract class Base {
	const ASSETS_DATA_KEY = '_elementor_assets_data';

	/**
	 * @var array
	 */
	protected $assets_data;

	/**
	 * @var string
	 */
	protected $content_type;

	/**
	 * @var string
	 */
	protected $assets_category;

	/**
	 * @var array
	 */
	private $assets_config;

	/**
	 * @var array
	 */
	private $files_data;

	/**
	 * Get Asset Content.
	 *
	 * Responsible for extracting the asset data from a certain file.
	 * Will be triggered automatically when the asset data does not exist, or when the asset version was changed.
	 *
	 * @since 3.3.0
	 * @access public
	 *
	 * @return string
	 */
	abstract protected function get_asset_content();

	/**
	 * Get Asset Key.
	 *
	 * The asset data will be saved in the DB under this key.
	 *
	 * @since 3.3.0
	 * @access protected
	 *
	 * @return string
	 */
	protected function get_key() {
		return $this->assets_config['key'];
	}

	/**
	 * Get Relative Version.
	 *
	 * The asset data will be re-evaluated according the version number.
	 *
	 * @since 3.3.0
	 * @access protected
	 *
	 * @return string
	 */
	protected function get_version() {
		return $this->assets_config['version'];
	}

	/**
	 * Get Asset Path.
	 *
	 * The asset data will be extracted from the file path.
	 *
	 * @since 3.3.0
	 * @access protected
	 *
	 * @return string
	 */
	protected function get_file_path() {
		return $this->assets_config['file_path'];
	}

	/**
	 * Get Config Data.
	 *
	 * Holds a unique data relevant for the specific assets category type.
	 *
	 * @since 3.3.0
	 * @access protected
	 *
	 * @return string|array
	 */
	protected function get_config_data( $key = '' ) {
		if ( isset( $this->assets_config['data'] ) ) {
			if ( $key ) {
				if ( isset( $this->assets_config['data'][ $key ] ) ) {
					return $this->assets_config['data'][ $key ];
				}

				return '';
			}

			return $this->assets_config['data'];
		}

		return [];
	}

	/**
	 * Set Asset Data.
	 *
	 * Responsible for setting the current asset data.
	 *
	 * @since 3.3.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function set_asset_data( $asset_key ) {
		if ( ! isset( $this->assets_data[ $asset_key ] ) ) {
			$this->assets_data[ $asset_key ] = [];
		}

		$this->assets_data[ $asset_key ]['content'] = $this->get_asset_content();
		$this->assets_data[ $asset_key ]['version'] = $this->get_version();

		$this->save_asset_data( $asset_key );
	}

	/**
	 * Save Asset Data.
	 *
	 * Responsible for saving the asset data in the DB.
	 *
	 * @since 3.3.0
	 * @access protected
	 *
	 * @param string $asset_key
	 *
	 * @return void
	 */
	protected function save_asset_data( $asset_key ) {
		$assets_data = $this->get_saved_assets_data();

		$content_type = $this->content_type;
		$assets_category = $this->assets_category;

		$assets_data[ $content_type ][ $assets_category ][ $asset_key ] = $this->assets_data[ $asset_key ];

		update_option( self::ASSETS_DATA_KEY, $assets_data );
	}

	/**
	 * Is Asset Version Changed.
	 *
	 * Responsible for comparing the saved asset data version to the current relative version.
	 *
	 * @since 3.3.0
	 * @access protected
	 *
	 * @param string $version
	 *
	 * @return boolean
	 */
	protected function is_asset_version_changed( $version ) {
		return $this->get_version() !== $version;
	}

	/**
	 * Get File Data.
	 *
	 * Getting a file content or size.
	 *
	 * @since 3.3.0
	 * @access protected
	 *
	 * @param string $data_type (exists|content|size).
	 * @param string $file_key - In case that the same file data is needed for multiple assets (like a JSON file), the file data key should be the same for all shared assets to make sure that the file is being read only once.
	 *
	 * @return string|number
	 */
	protected function get_file_data( $data_type, $file_key = '' ) {
		$asset_key = $file_key ? $file_key : $this->get_key();

		if ( isset( $this->files_data[ $asset_key ][ $data_type ] ) ) {
			return $this->files_data[ $asset_key ][ $data_type ];
		}

		if ( ! isset( $this->files_data[ $asset_key ] ) ) {
			$this->files_data[ $asset_key ] = [];
		}

		$asset_path = $this->get_file_path();

		if ( 'exists' === $data_type ) {
			$data = file_exists( $asset_path );
		} elseif ( 'content' === $data_type ) {
			$data = Utils::file_get_contents( $asset_path );

			if ( ! $data ) {
				$data = '';
			}
		} elseif ( 'size' === $data_type ) {
			$data = file_exists( $asset_path ) ? filesize( $asset_path ) : 0;
		}

		$this->files_data[ $asset_key ][ $data_type ] = $data;

		return $data;
	}

	/**
	 * Get Saved Assets Data.
	 *
	 * Getting the assets data from the DB.
	 *
	 * @since 3.3.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_saved_assets_data() {
		$assets_data = get_option( self::ASSETS_DATA_KEY, [] );

		$content_type = $this->content_type;
		$assets_category = $this->assets_category;

		if ( ! isset( $assets_data[ $content_type ] ) ) {
			$assets_data[ $content_type ] = [];
		}

		if ( ! isset( $assets_data[ $content_type ][ $assets_category ] ) ) {
			$assets_data[ $content_type ][ $assets_category ] = [];
		}
		return $assets_data;
	}

	/**
	 * Get Config.
	 *
	 * Getting the assets data config.
	 *
	 * @since 3.5.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_config( $data ) {
		return [];
	}

	/**
	 * Init Asset Data.
	 *
	 * Initialize the asset data and handles the asset content updates when needed.
	 *
	 * @since 3.3.0
	 * @access public
	 *
	 * @param array $config {
	 *     @type string 'key'
	 *     @type string 'version'
	 *     @type string 'file_path'
	 *     @type array 'data'
	 * }
	 *
	 * @return void
	 */
	public function init_asset_data( $config ) {
		$this->assets_config = $config;

		$asset_key = $config['key'];

		$asset_data = isset( $this->assets_data[ $asset_key ] ) ? $this->assets_data[ $asset_key ] : [];

		if ( ! $asset_data || $this->is_asset_version_changed( $asset_data['version'] ) ) {
			$this->set_asset_data( $asset_key );
		}
	}

	/**
	 * Get Asset Data From Config.
	 *
	 * Getting the asset data content from config.
	 *
	 * @since 3.3.0
	 * @access public
	 *
	 * @param array $config {
	 *     @type string 'key'
	 *     @type string 'version'
	 *     @type string 'file_path'
	 *     @type array 'data'
	 * }
	 *
	 * @return mixed
	 */
	public function get_asset_data_from_config( array $config ) {
		$this->init_asset_data( $config );

		$asset_key = $config['key'];

		return $this->assets_data[ $asset_key ]['content'];
	}

	/**
	 * Get Asset Data.
	 *
	 * Getting the asset data content.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function get_asset_data( array $data ) {
		$config = $this->get_config( $data );

		return $this->get_asset_data_from_config( $config );
	}

	public function __construct() {
		$assets_data = $this->get_saved_assets_data();

		$content_type = $this->content_type;
		$assets_category = $this->assets_category;

		$this->assets_data = $assets_data[ $content_type ][ $assets_category ];
	}
}
