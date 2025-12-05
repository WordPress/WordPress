<?php
namespace Elementor;

use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<script type="text/template" id="tmpl-elementor-hotkeys">
	<# var ctrlLabel = environment.mac ? '&#8984;' : 'Ctrl'; #>
	<div id="elementor-hotkeys__content">

		<div class="elementor-hotkeys__col">

			<h3 class="elementor-hotkeys__header"><?php echo esc_html__( 'Actions', 'elementor' ); ?></h3>

			<ul class="elementor-hotkeys__list">

				<li class="elementor-hotkeys__item">
					<div class="elementor-hotkeys__item--label"><?php echo esc_html__( 'Undo', 'elementor' ); ?></div>
					<div class="elementor-hotkeys__item--shortcut">
						<kbd>{{{ ctrlLabel }}}</kbd>
						<kbd>Z</kbd>
					</div>
				</li>

				<li class="elementor-hotkeys__item">
					<div class="elementor-hotkeys__item--label"><?php echo esc_html__( 'Redo', 'elementor' ); ?></div>
					<div class="elementor-hotkeys__item--shortcut">
						<kbd>{{{ ctrlLabel }}}</kbd>
						<kbd>Shift</kbd>
						<kbd>Z</kbd>
					</div>
				</li>

				<li class="elementor-hotkeys__item">
					<div class="elementor-hotkeys__item--label"><?php echo esc_html__( 'Copy', 'elementor' ); ?></div>
					<div class="elementor-hotkeys__item--shortcut">
						<kbd>{{{ ctrlLabel }}}</kbd>
						<kbd>C</kbd>
					</div>
				</li>

				<li class="elementor-hotkeys__item">
					<div class="elementor-hotkeys__item--label"><?php echo esc_html__( 'Paste', 'elementor' ); ?></div>
					<div class="elementor-hotkeys__item--shortcut">
						<kbd>{{{ ctrlLabel }}}</kbd>
						<kbd>V</kbd>
					</div>
				</li>

				<li class="elementor-hotkeys__item">
					<div class="elementor-hotkeys__item--label"><?php echo esc_html__( 'Paste Style', 'elementor' ); ?></div>
					<div class="elementor-hotkeys__item--shortcut">
						<kbd>{{{ ctrlLabel }}}</kbd>
						<kbd>Shift</kbd>
						<kbd>V</kbd>
					</div>
				</li>

				<li class="elementor-hotkeys__item">
					<div class="elementor-hotkeys__item--label"><?php echo esc_html__( 'Delete', 'elementor' ); ?></div>
					<div class="elementor-hotkeys__item--shortcut">
						<kbd>Delete</kbd>
					</div>
				</li>

				<li class="elementor-hotkeys__item">
					<div class="elementor-hotkeys__item--label"><?php echo esc_html__( 'Duplicate', 'elementor' ); ?></div>
					<div class="elementor-hotkeys__item--shortcut">
						<kbd>{{{ ctrlLabel }}}</kbd>
						<kbd>D</kbd>
					</div>
				</li>

				<li class="elementor-hotkeys__item">
					<div class="elementor-hotkeys__item--label"><?php echo esc_html__( 'Save', 'elementor' ); ?></div>
					<div class="elementor-hotkeys__item--shortcut">
						<kbd>{{{ ctrlLabel }}}</kbd>
						<kbd>S</kbd>
					</div>
				</li>

			</ul>

		</div>

		<div class="elementor-hotkeys__col">

			<h3 class="elementor-hotkeys__header"><?php echo esc_html__( 'Panels', 'elementor' ); ?></h3>

			<ul class="elementor-hotkeys__list">

				<li class="elementor-hotkeys__item">
					<div class="elementor-hotkeys__item--label"><?php echo esc_html__( 'Finder', 'elementor' ); ?></div>
					<div class="elementor-hotkeys__item--shortcut">
						<kbd>{{{ ctrlLabel }}}</kbd>
						<kbd>E</kbd>
					</div>
				</li>

				<li class="elementor-hotkeys__item">
					<div class="elementor-hotkeys__item--label"><?php echo esc_html__( 'Show / Hide Panel', 'elementor' ); ?></div>
					<div class="elementor-hotkeys__item--shortcut">
						<kbd>{{{ ctrlLabel }}}</kbd>
						<kbd>P</kbd>
					</div>
				</li>

				<li class="elementor-hotkeys__item">
					<div class="elementor-hotkeys__item--label"><?php echo esc_html__( 'Site Settings', 'elementor' ); ?></div>
					<div class="elementor-hotkeys__item--shortcut">
						<kbd>{{{ ctrlLabel }}}</kbd>
						<kbd>K</kbd>
					</div>
				</li>

				<li class="elementor-hotkeys__item">
					<div class="elementor-hotkeys__item--label"><?php echo esc_html__( 'Structure', 'elementor' ); ?></div>
					<div class="elementor-hotkeys__item--shortcut">
						<kbd>{{{ ctrlLabel }}}</kbd>
						<kbd>I</kbd>
					</div>
				</li>

				<li class="elementor-hotkeys__item">
					<div class="elementor-hotkeys__item--label"><?php echo esc_html__( 'Page Settings', 'elementor' ); ?></div>
					<div class="elementor-hotkeys__item--shortcut">
						<kbd>{{{ ctrlLabel }}}</kbd>
						<kbd>Shift</kbd>
						<kbd>Y</kbd>
					</div>
				</li>

				<li class="elementor-hotkeys__item">
					<div class="elementor-hotkeys__item--label"><?php echo esc_html__( 'History', 'elementor' ); ?></div>
					<div class="elementor-hotkeys__item--shortcut">
						<kbd>{{{ ctrlLabel }}}</kbd>
						<kbd>Shift</kbd>
						<kbd>H</kbd>
					</div>
				</li>

				<li class="elementor-hotkeys__item">
					<div class="elementor-hotkeys__item--label"><?php echo esc_html__( 'User Preferences', 'elementor' ); ?></div>
					<div class="elementor-hotkeys__item--shortcut">
						<kbd>{{{ ctrlLabel }}}</kbd>
						<kbd>Shift</kbd>
						<kbd>U</kbd>
					</div>
				</li>

			</ul>

		</div>

		<div class="elementor-hotkeys__col">

			<h3 class="elementor-hotkeys__header"><?php echo esc_html__( 'Go To', 'elementor' ); ?></h3>

			<ul class="elementor-hotkeys__list">

				<li class="elementor-hotkeys__item">
					<div class="elementor-hotkeys__item--label"><?php echo esc_html__( 'Responsive Mode', 'elementor' ); ?></div>
					<div class="elementor-hotkeys__item--shortcut">
						<kbd>{{{ ctrlLabel }}}</kbd>
						<kbd>Shift</kbd>
						<kbd>M</kbd>
					</div>
				</li>

				<li class="elementor-hotkeys__item">
					<div class="elementor-hotkeys__item--label"><?php echo esc_html__( 'Template Library', 'elementor' ); ?></div>
					<div class="elementor-hotkeys__item--shortcut">
						<kbd>{{{ ctrlLabel }}}</kbd>
						<kbd>Shift</kbd>
						<kbd>L</kbd>
					</div>
				</li>

				<?php if ( Utils::has_pro() ) : ?>
				<li class="elementor-hotkeys__item">
					<div class="elementor-hotkeys__item--label"><?php echo esc_html__( 'Notes', 'elementor' ); ?></div>
					<div class="elementor-hotkeys__item--shortcut">
						<kbd>Shift</kbd>
						<kbd>C</kbd>
					</div>
				</li>
				<?php endif ?>

				<li class="elementor-hotkeys__item">
					<div class="elementor-hotkeys__item--label"><?php echo esc_html__( 'Keyboard Shortcuts', 'elementor' ); ?></div>
					<div class="elementor-hotkeys__item--shortcut">
						<kbd>Shift</kbd>
						<kbd>?</kbd>
					</div>
				</li>

				<li class="elementor-hotkeys__item">
					<div class="elementor-hotkeys__item--label"><?php echo esc_html__( 'Quit', 'elementor' ); ?></div>
					<div class="elementor-hotkeys__item--shortcut">
						<kbd>Esc</kbd>
					</div>
				</li>

			</ul>

		</div>

	</div>
</script>
