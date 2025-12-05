<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor settings controls.
 *
 * Elementor settings controls handler class responsible for creating the final
 * HTML for various input field types used in Elementor settings pages.
 *
 * @since 1.0.0
 */
class Settings_Controls {

	/**
	 * Render settings control.
	 *
	 * Generates the final HTML on the frontend for any given field based on
	 * the field type (text, select, checkbox, raw HTML, etc.).
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param array $field Optional. Field data. Default is an empty array.
	 */
	public static function render( $field = [] ) {
		if ( empty( $field ) || empty( $field['id'] ) ) {
			return;
		}

		$defaults = [
			'type' => '',
			'attributes' => [],
			'std' => '',
			'desc' => '',
		];

		$field = array_merge( $defaults, $field );

		$method_name = $field['type'];

		if ( ! method_exists( __CLASS__, $method_name ) ) {
			$method_name = 'text';
		}

		self::$method_name( $field );
	}

	/**
	 * Render text control.
	 *
	 * Generates the final HTML for text controls.
	 *
	 * @since 2.0.0
	 * @access private
	 * @static
	 *
	 * @param array $field Field data.
	 */
	private static function text( array $field ) {
		if ( empty( $field['attributes']['class'] ) ) {
			$field['attributes']['class'] = 'regular-text';
		}

		?>
		<input type="<?php echo esc_attr( $field['type'] ); ?>" id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo esc_attr( $field['id'] ); ?>" value="<?php echo esc_attr( get_option( $field['id'], $field['std'] ) ); ?>" <?php Utils::print_html_attributes( $field['attributes'] ); ?>/>
		<?php
		if ( ! empty( $field['sub_desc'] ) ) :
			echo wp_kses_post( $field['sub_desc'] );
		endif;
		?>
		<?php if ( ! empty( $field['desc'] ) ) : ?>
			<p class="description"><?php echo wp_kses_post( $field['desc'] ); ?></p>
			<?php
		endif;
	}

	/**
	 * Render checkbox control.
	 *
	 * Generates the final HTML for checkbox controls.
	 *
	 * @since 2.0.0
	 * @access private
	 * @static
	 *
	 * @param array $field Field data.
	 */
	private static function checkbox( array $field ) {
		?>
		<label>
			<input type="<?php echo esc_attr( $field['type'] ); ?>" id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo esc_attr( $field['id'] ); ?>" value="<?php echo esc_attr( $field['value'] ); ?>"<?php checked( $field['value'], get_option( $field['id'], $field['std'] ) ); ?> />
			<?php
			if ( ! empty( $field['sub_desc'] ) ) :
				echo wp_kses_post( $field['sub_desc'] );
			endif;
			?>
		</label>
		<?php if ( ! empty( $field['desc'] ) ) : ?>
			<p class="description"><?php echo wp_kses_post( $field['desc'] ); ?></p>
			<?php
		endif;
	}

	/**
	 * Render checkbox list control.
	 *
	 * Generates the final HTML for checkbox list controls.
	 *
	 * @since 2.0.0
	 * @access private
	 * @static
	 *
	 * @param array $field Field data.
	 */
	private static function checkbox_list( array $field ) {
		$old_value = get_option( $field['id'], $field['std'] );
		if ( ! is_array( $old_value ) ) {
			$old_value = [];
		}

		foreach ( $field['options'] as $option_key => $option_value ) :
			?>
			<label>
				<input type="checkbox" name="<?php echo esc_attr( $field['id'] ); ?>[]" value="<?php echo esc_attr( $option_key ); ?>"<?php checked( in_array( $option_key, $old_value ), true ); ?> />
				<?php echo wp_kses_post( $option_value ); ?>
			</label><br />
		<?php endforeach; ?>
		<?php if ( ! empty( $field['desc'] ) ) : ?>
			<p class="description"><?php echo wp_kses_post( $field['desc'] ); ?></p>
			<?php
		endif;
	}

	/**
	 * Render select control.
	 *
	 * Generates the final HTML for select controls.
	 *
	 * @since 2.0.0
	 * @access private
	 * @static
	 *
	 * @param array $field Field data.
	 */
	private static function select( array $field ) {
		$old_value = get_option( $field['id'], $field['std'] );
		?>
		<select name="<?php echo esc_attr( $field['id'] ); ?>">
			<?php if ( ! empty( $field['show_select'] ) ) : ?>
				<option value="">— <?php echo esc_html__( 'Select', 'elementor' ); ?> —</option>
			<?php endif; ?>

			<?php foreach ( $field['options'] as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>"<?php esc_attr( selected( $value, $old_value ) ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>

		<?php if ( ! empty( $field['desc'] ) ) : ?>
			<p class="description"><?php echo wp_kses_post( $field['desc'] ); ?></p>
			<?php
		endif;
	}

	/**
	 * Render checkbox list control for CPT.
	 *
	 * Generates the final HTML for checkbox list controls populated with Custom Post Types.
	 *
	 * @since 2.0.0
	 * @access private
	 * @static
	 *
	 * @param array $field Field data.
	 */
	private static function checkbox_list_cpt( array $field ) {
		$defaults = [
			'exclude' => [],
		];
		$field = array_merge( $defaults, $field );

		$post_types_objects = get_post_types(
			[
				'public' => true,
			], 'objects'
		);

		/**
		 * Filters the list of post type objects used by Elementor.
		 *
		 * @since 2.8.0
		 *
		 * @param array $post_types_objects List of post type objects used by Elementor.
		 */
		$post_types_objects = apply_filters( 'elementor/settings/controls/checkbox_list_cpt/post_type_objects', $post_types_objects );

		$field['options'] = [];
		foreach ( $post_types_objects as $cpt_slug => $post_type ) {
			if ( in_array( $cpt_slug, $field['exclude'], true ) ) {
				continue;
			}

			$field['options'][ $cpt_slug ] = $post_type->labels->name;
		}

		self::checkbox_list( $field );
	}

	/**
	 * Render checkbox list control for user roles.
	 *
	 * Generates the final HTML for checkbox list controls populated with user roles.
	 *
	 * @since 2.0.0
	 * @access private
	 * @static
	 *
	 * @param array $field Field data.
	 */
	private static function checkbox_list_roles( array $field ) {
		$defaults = [
			'exclude' => [],
		];
		$field = array_merge( $defaults, $field );

		$field['options'] = [];
		$roles = get_editable_roles();

		if ( is_multisite() ) {
			$roles = [
				'super_admin' => [
					'name' => esc_html__( 'Super Admin', 'elementor' ),
				],
			] + $roles;
		}

		foreach ( $roles as $role_slug => $role_data ) {
			if ( in_array( $role_slug, $field['exclude'] ) ) {
				continue;
			}

			$field['options'][ $role_slug ] = $role_data['name'];
		}

		self::checkbox_list( $field );
	}

	/**
	 * Render raw HTML control.
	 *
	 * Generates the final HTML for raw HTML controls.
	 *
	 * @since 2.0.0
	 * @access private
	 * @static
	 *
	 * @param array $field Field data.
	 */
	private static function raw_html( array $field ) {
		if ( empty( $field['html'] ) ) {
			return;
		}
		?>
		<div id="<?php echo esc_attr( $field['id'] ); ?>">

			<?php // PHPCS - This is a Raw HTML control, it is not escaped on purpose. ?>
			<div><?php echo $field['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php
			if ( ! empty( $field['sub_desc'] ) ) :
				echo wp_kses_post( $field['sub_desc'] );
			endif;
			?>
			<?php if ( ! empty( $field['desc'] ) ) : ?>
				<p class="description"><?php echo wp_kses_post( $field['desc'] ); ?></p>
			<?php endif; ?>
			</div>
		<?php
	}
}
