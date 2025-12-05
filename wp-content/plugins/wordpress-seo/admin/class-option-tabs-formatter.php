<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Options\Tabs
 */

use Yoast\WP\SEO\Presenters\Admin\Beta_Badge_Presenter;
use Yoast\WP\SEO\Presenters\Admin\Premium_Badge_Presenter;

/**
 * Class WPSEO_Option_Tabs_Formatter.
 */
class WPSEO_Option_Tabs_Formatter {

	/**
	 * Retrieves the path to the view of the tab.
	 *
	 * @param WPSEO_Option_Tabs $option_tabs Option Tabs to get base from.
	 * @param WPSEO_Option_Tab  $tab         Tab to get name from.
	 *
	 * @return string
	 */
	public function get_tab_view( WPSEO_Option_Tabs $option_tabs, WPSEO_Option_Tab $tab ) {
		return WPSEO_PATH . 'admin/views/tabs/' . $option_tabs->get_base() . '/' . $tab->get_name() . '.php';
	}

	/**
	 * Outputs the option tabs.
	 *
	 * @param WPSEO_Option_Tabs $option_tabs Option Tabs to get tabs from.
	 *
	 * @return void
	 */
	public function run( WPSEO_Option_Tabs $option_tabs ) {

		echo '<h2 class="nav-tab-wrapper" id="wpseo-tabs">';
		foreach ( $option_tabs->get_tabs() as $tab ) {
			$label = esc_html( $tab->get_label() );

			if ( $tab->is_beta() ) {
				$label = '<span style="margin-right:4px;">' . $label . '</span>' . new Beta_Badge_Presenter( $tab->get_name() );
			}
			elseif ( $tab->is_premium() ) {
				$label = '<span style="margin-right:4px;">' . $label . '</span>' . new Premium_Badge_Presenter( $tab->get_name() );
			}

			printf(
				'<a class="nav-tab" id="%1$s" href="%2$s">%3$s</a>',
				esc_attr( $tab->get_name() . '-tab' ),
				esc_url( '#top#' . $tab->get_name() ),
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: we do this on purpose
				$label
			);
		}
		echo '</h2>';

		foreach ( $option_tabs->get_tabs() as $tab ) {
			$identifier = $tab->get_name();

			$class = 'wpseotab ' . ( $tab->has_save_button() ? 'save' : 'nosave' );
			printf( '<div id="%1$s" class="%2$s">', esc_attr( $identifier ), esc_attr( $class ) );

			$tab_filter_name = sprintf( '%s_%s', $option_tabs->get_base(), $tab->get_name() );

			/**
			 * Allows to override the content that is display on the specific option tab.
			 *
			 * @internal For internal Yoast SEO use only.
			 *
			 * @param string|null       $tab_contents The content that should be displayed for this tab. Leave empty for default behaviour.
			 * @param WPSEO_Option_Tabs $option_tabs  The registered option tabs.
			 * @param WPSEO_Option_Tab  $tab          The tab that is being displayed.
			 */
			$option_tab_content = apply_filters( 'wpseo_option_tab-' . $tab_filter_name, null, $option_tabs, $tab );
			if ( ! empty( $option_tab_content ) ) {
				echo wp_kses_post( $option_tab_content );
			}

			if ( empty( $option_tab_content ) ) {
				// Output the settings view for all tabs.
				$tab_view = $this->get_tab_view( $option_tabs, $tab );

				if ( is_file( $tab_view ) ) {
					$yform = Yoast_Form::get_instance();
					require $tab_view;
				}
			}

			echo '</div>';
		}
	}
}
