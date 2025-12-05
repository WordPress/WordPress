<?php
namespace Elementor;

use Elementor\Core\Breakpoints\Breakpoint;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// TODO: Use API data instead of this static array, once it is available.
$active_breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();
$active_devices = Plugin::$instance->breakpoints->get_active_devices_list( [ 'reverse' => true ] );

$breakpoint_classes_map = array_intersect_key( Plugin::$instance->breakpoints->get_responsive_icons_classes_map(), array_flip( $active_devices ) );
?>

<script type="text/template" id="tmpl-elementor-templates-responsive-bar">
	<div id="e-responsive-bar__center">
		<div id="e-responsive-bar-switcher" class="e-responsive-bar--pipe">
			<?php foreach ( $active_devices as $device_key ) {
				if ( 'desktop' === $device_key ) {
					$tooltip_label = esc_html__( 'Desktop <br> Settings added for the base device will apply to all breakpoints unless edited', 'elementor' );
				} elseif ( 'widescreen' === $device_key ) {
					$tooltip_label = sprintf(
						/* translators: %d: Breakpoint screen size. */
						esc_html__( 'Widescreen <br> Settings added for the Widescreen device will apply to screen sizes %dpx and up', 'elementor' ),
						$active_breakpoints[ $device_key ]->get_value()
					);
				} else {
					$tooltip_label = sprintf(
						/* translators: %1$s: Device name, %2$s: Breakpoint screen size. */
						esc_html__( '%1$s <br> Settings added for the %1$s device will apply to %2$spx screens and down', 'elementor' ),
						$active_breakpoints[ $device_key ]->get_label(), $active_breakpoints[ $device_key ]->get_value()
					);
				}
				printf( '<label
					id="e-responsive-bar-switcher__option-%1$s"
					class="e-responsive-bar-switcher__option"
					for="e-responsive-bar-switch-%1$s"
					data-tooltip="%2$s">

					<input type="radio" name="breakpoint" id="e-responsive-bar-switch-%1$s" value="%1$s">
					<i class="%3$s" aria-hidden="true"></i>
					<span class="screen-reader-text">%2$s</span>
				</label>', esc_attr( $device_key ), esc_attr( $tooltip_label ), esc_attr( $breakpoint_classes_map[ $device_key ] ) );
			} ?>
			</div>
			<div id="e-responsive-bar-scale">
				<div id="e-responsive-bar-scale__minus"></div>
				<div id="e-responsive-bar-scale__value-wrapper"><span id="e-responsive-bar-scale__value">100</span>%</div>
				<div id="e-responsive-bar-scale__plus"><i class="eicon-plus" aria-hidden="true"></i></div>
				<div id="e-responsive-bar-scale__reset"><i class="eicon-undo" aria-hidden="true"></i></div>
			</div>
		</div>
		<div id="e-responsive-bar__end">
			<div id="e-responsive-bar__size-inputs-wrapper" class="e-flex e-align-items-center">
				<label for="e-responsive-bar__input-width">W</label>
				<input type="number" id="e-responsive-bar__input-width" class="e-responsive-bar__input-size" autocomplete="off">
				<label for="e-responsive-bar__input-height">H</label>
				<input type="number" id="e-responsive-bar__input-height" class="e-responsive-bar__input-size" autocomplete="off">
			</div>
			<button id="e-responsive-bar__settings-button" class="e-responsive-bar__button e-responsive-bar--pipe" data-tooltip="<?php echo esc_attr__( 'Manage Breakpoints', 'elementor' ); ?>" aria-label="<?php echo esc_attr__( 'Manage Breakpoints', 'elementor' ); ?>">
				<i class="eicon-cog" aria-hidden="true"></i>
			</button>
			<button id="e-responsive-bar__close-button" class="e-responsive-bar__button" data-tooltip="<?php echo esc_attr__( 'Close', 'elementor' ); ?>" aria-label="<?php echo esc_attr__( 'Close', 'elementor' ); ?>">
				<i class="eicon-close" aria-hidden="true"></i>
			</button>
		</div>
</script>
