<?php
/**
 * Twenty Twenty Custom CSS
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since 1.0.0
 */

if ( ! function_exists( 'twentytwenty_generate_css' ) ) {

	/**
	 * Generate CSS.
	 *
	 * @param string $selector The CSS selector.
	 * @param string $style The CSS style.
	 * @param string $value The CSS value.
	 * @param string $prefix The CSS prefix.
	 * @param string $suffix The CSS suffix.
	 * @param bool   $echo Echo the styles.
	 */
	function twentytwenty_generate_css( $selector, $style, $value, $prefix = '', $suffix = '', $echo = true ) {

		$return = '';

		/*
		 * Bail early if we have no $selector elements or properties and $value.
		 */
		if ( ! $value || ! $selector ) {

			return;
		}

		$return = sprintf( '%s { %s: %s; }', $selector, $style, $prefix . $value . $suffix );

		if ( $echo ) {

			echo $return; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- We need to double check this, but for now, we want to pass PHPCS ;)

		}

		return $return;

	}
}

if ( ! function_exists( 'twentytwenty_get_customizer_css' ) ) {

	/**
	 * Get CSS Built from Customizer Options.
	 * Build CSS reflecting colors, fonts and other options set in the Customizer, and return them for output.
	 *
	 * @param string $type Whether to return CSS for the "front-end", "block-editor" or "classic-editor".
	 */
	function twentytwenty_get_customizer_css( $type = 'front-end' ) {

		// Get variables.
		$body           = sanitize_hex_color( twentytwenty_get_color_for_area( 'content', 'text' ) );
		$body_default   = '#000000';
		$accent         = sanitize_hex_color( twentytwenty_get_color_for_area( 'content', 'accent' ) );
		$accent_default = '#cd2653';
		/**
		 * Filters the CSS selectors targeting button component on frontend to apply common css
		 *
		 * @since 1.0.0
		 *
		 * @param string $string   The comma separated string of all CSS selectors targeting button component
		 */
		$buttons_targets = apply_filters( 'twentytwenty_buttons_targets_front_end', 'button, .button, .faux-button, .wp-block-button__link, .wp-block-file__button, input[type=\'button\'], input[type=\'reset\'], input[type=\'submit\']' );

		// Header.
		$header_footer_text   = sanitize_hex_color( twentytwenty_get_color_for_area( 'header-footer', 'text' ) );
		$header_footer_accent = sanitize_hex_color( twentytwenty_get_color_for_area( 'header-footer', 'accent' ) );

		// Cover.
		$cover         = sanitize_hex_color( get_theme_mod( 'cover_template_overlay_text_color' ) );
		$cover_default = '#ffffff';

		// Background.
		$background         = sanitize_hex_color_no_hash( get_theme_mod( 'background_color' ) );
		$background_default = 'f5efe0';

		ob_start();

		/**
		 * Note – Styles are applied in this order:
		 * 1. Element specific
		 * 2. Helper classes
		 *
		 * This enables all helper classes to overwrite base element styles,
		 * meaning that any color classes applied in the block editor will
		 * have a higher priority than the base element styles.
		*/

		// Front-End Styles.
		if ( 'front-end' === $type ) {

			// Auto-calculated colors.
			$elements_definitions = twentytwenty_get_elements_array();
			foreach ( $elements_definitions as $context => $props ) {
				foreach ( $props as $key => $definitions ) {
					foreach ( $definitions as $property => $elements ) {
						/*
						 * If we don't have an elements array or it is empty
						 * then skip this iteration early;
						 */
						if ( ! is_array( $elements ) || empty( $elements ) ) {
							continue;
						}
						$val = twentytwenty_get_color_for_area( $context, $key );
						if ( $val ) {
							twentytwenty_generate_css( implode( ',', $elements ), $property, $val );
						}
					}
				}
			}

			if ( $cover && $cover !== $cover_default ) {
				twentytwenty_generate_css( '.overlay-header .header-inner', 'color', $cover );
				twentytwenty_generate_css( '.cover-header .entry-header *', 'color', $cover );
			}

			// Block Editor Styles.
		} elseif ( 'block-editor' === $type ) {

			// Colors.
			// Accent color.
			if ( $accent && $accent !== $accent_default ) {
				twentytwenty_generate_css( '.editor-styles-wrapper a, .editor-styles-wrapper .has-drop-cap:not(:focus):first-letter', 'color', $accent );
				twentytwenty_generate_css( '.editor-styles-wrapper blockquote, .editor-styles-wrapper .wp-block-quote', 'border-color', $accent, '', ' !important' );
				twentytwenty_generate_css( '.editor-styles-wrapper .wp-block-file .wp-block-file__textlink', 'color', $accent );
				twentytwenty_generate_css( $buttons_targets, 'background', $accent );
				twentytwenty_generate_css( '.editor-styles-wrapper .wp-block-button.is-style-outline .wp-block-button__link', 'border-color', $accent );
				twentytwenty_generate_css( '.editor-styles-wrapper .wp-block-button.is-style-outline .wp-block-button__link', 'color', $accent );
			}

			// Background color.
			if ( $background && $background !== $background_default ) {
				twentytwenty_generate_css( '.editor-styles-wrapper', 'background', '#' . $background );
			}

			// Text color.
			if ( $body && $body !== $body_default ) {
				twentytwenty_generate_css( 'body .editor-styles-wrapper, body .editor-post-title__block, body .editor-post-title__input, body textarea, .editor-post-title__block .editor-post-title__input', 'color', $body );
			}
		} elseif ( 'classic-editor' === $type ) {

			// Colors.
			// Accent color.
			if ( $accent && $accent !== $accent_default ) {
				twentytwenty_generate_css( 'body#tinymce.wp-editor a', 'color', $accent );
				twentytwenty_generate_css( 'body#tinymce.wp-editor blockquote, body#tinymce.wp-editor .wp-block-quote', 'border-color', $accent, '', ' !important' );
				twentytwenty_generate_css( $buttons_targets, 'background-color', $accent );
			}

			// Background color.
			if ( $background && $background !== $background_default ) {
				twentytwenty_generate_css( 'body#tinymce.wp-editor', 'background', '#' . $background );
			}
		}

		// Return the results.
		return ob_get_clean();

	}
}
