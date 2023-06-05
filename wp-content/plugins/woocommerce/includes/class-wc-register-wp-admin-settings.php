<?php
/**
 * Take settings registered for WP-Admin and hooks them up to the REST API
 *
 * @package  WooCommerce\Classes
 * @version  3.0.0
 * @since    3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register WP admin settings class.
 */
class WC_Register_WP_Admin_Settings {

	/**
	 * Contains the current class to pull settings from.
	 * Either a admin page object or WC_Email object
	 *
	 * @var WC_Register_WP_Admin_Settings
	 */
	protected $object;

	/**
	 * Hooks into the settings API and starts registering our settings.
	 *
	 * @since 3.0.0
	 * @param WC_Email|WC_Settings_Page $object The object that contains the settings to register.
	 * @param string                    $type   Type of settings to register (email or page).
	 */
	public function __construct( $object, $type ) {
		if ( ! is_object( $object ) ) {
			return;
		}

		$this->object = $object;

		if ( 'page' === $type ) {
			add_filter( 'woocommerce_settings_groups', array( $this, 'register_page_group' ) );
			add_filter( 'woocommerce_settings-' . $this->object->get_id(), array( $this, 'register_page_settings' ) );
		} elseif ( 'email' === $type ) {
			add_filter( 'woocommerce_settings_groups', array( $this, 'register_email_group' ) );
			add_filter( 'woocommerce_settings-email_' . $this->object->id, array( $this, 'register_email_settings' ) );
		}
	}

	/**
	 * Register's all of our different notification emails as sub groups
	 * of email settings.
	 *
	 * @since  3.0.0
	 * @param  array $groups Existing registered groups.
	 * @return array
	 */
	public function register_email_group( $groups ) {
		$groups[] = array(
			'id'          => 'email_' . $this->object->id,
			'label'       => $this->object->title,
			'description' => $this->object->description,
			'parent_id'   => 'email',
		);
		return $groups;
	}

	/**
	 * Registers all of the setting form fields for emails to each email type's group.
	 *
	 * @since  3.0.0
	 * @param  array $settings Existing registered settings.
	 * @return array
	 */
	public function register_email_settings( $settings ) {
		foreach ( $this->object->form_fields as $id => $setting ) {
			$setting['id']         = $id;
			$setting['option_key'] = array( $this->object->get_option_key(), $id );
			$new_setting           = $this->register_setting( $setting );
			if ( $new_setting ) {
				$settings[] = $new_setting;
			}
		}
		return $settings;
	}

	/**
	 * Registers a setting group, based on admin page ID & label as parent group.
	 *
	 * @since  3.0.0
	 * @param  array $groups Array of previously registered groups.
	 * @return array
	 */
	public function register_page_group( $groups ) {
		$groups[] = array(
			'id'    => $this->object->get_id(),
			'label' => $this->object->get_label(),
		);
		return $groups;
	}

	/**
	 * Registers settings to a specific group.
	 *
	 * @since  3.0.0
	 * @param  array $settings Existing registered settings.
	 * @return array
	 */
	public function register_page_settings( $settings ) {
		/**
		 * WP admin settings can be broken down into separate sections from
		 * a UI standpoint. This will grab all the sections associated with
		 * a particular setting group (like 'products') and register them
		 * to the REST API.
		 */
		$sections = $this->object->get_sections();
		if ( empty( $sections ) ) {
			// Default section is just an empty string, per admin page classes.
			$sections = array( ''  => '' );
		}

		/**
		 * We are using 'WC_Settings_Page::get_settings' on purpose even thought it's deprecated.
		 * See the method documentation for an explanation.
		 */

		foreach ( $sections as $section => $section_label ) {
			$settings_from_section = $this->object->get_settings( $section );
			foreach ( $settings_from_section as $setting ) {
				if ( ! isset( $setting['id'] ) ) {
					continue;
				}
				$setting['option_key'] = $setting['id'];
				$new_setting           = $this->register_setting( $setting );
				if ( $new_setting ) {
					$settings[] = $new_setting;
				}
			}
		}
		return $settings;
	}

	/**
	 * Register a setting into the format expected for the Settings REST API.
	 *
	 * @since 3.0.0
	 * @param  array $setting Setting data.
	 * @return array|bool
	 */
	public function register_setting( $setting ) {
		if ( ! isset( $setting['id'] ) ) {
			return false;
		}

		$description = '';
		if ( ! empty( $setting['desc'] ) ) {
			$description = $setting['desc'];
		} elseif ( ! empty( $setting['description'] ) ) {
			$description = $setting['description'];
		}

		$new_setting = array(
			'id'          => $setting['id'],
			'label'       => ( ! empty( $setting['title'] ) ? $setting['title'] : '' ),
			'description' => $description,
			'type'        => $setting['type'],
			'option_key'  => $setting['option_key'],
		);

		if ( isset( $setting['default'] ) ) {
			$new_setting['default'] = $setting['default'];
		}
		if ( isset( $setting['options'] ) ) {
			$new_setting['options'] = $setting['options'];
		}
		if ( isset( $setting['desc_tip'] ) ) {
			if ( true === $setting['desc_tip'] ) {
				$new_setting['tip'] = $description;
			} elseif ( ! empty( $setting['desc_tip'] ) ) {
				$new_setting['tip'] = $setting['desc_tip'];
			}
		}

		return $new_setting;
	}

}
