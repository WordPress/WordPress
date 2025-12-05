<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<script type="text/template" id="tmpl-elementor-repeater-row">
	<div class="elementor-repeater-row-tools">
		<# if ( itemActions.drag_n_drop ) {  #>
			<button class="elementor-repeater-row-handle-sortable" aria-label="<?php echo esc_attr__( 'Drag & Drop', 'elementor' ); ?>">
				<i class="eicon-ellipsis-v" aria-hidden="true"></i>
			</button>
		<# } #>
		<button class="elementor-repeater-row-item-title"></button>
		<# if ( itemActions.duplicate ) {  #>
			<button class="elementor-repeater-row-tool elementor-repeater-tool-duplicate" aria-label="<?php echo esc_attr__( 'Duplicate', 'elementor' ); ?>">
				<i class="eicon-copy" aria-hidden="true"></i>
			</button>
		<# }
		if ( itemActions.remove ) {  #>
			<button class="elementor-repeater-row-tool elementor-repeater-tool-remove" aria-label="<?php echo esc_attr__( 'Remove', 'elementor' ); ?>">
				<i class="eicon-close" aria-hidden="true"></i>
			</button>
		<# } #>
	</div>
	<div class="elementor-repeater-row-controls"></div>
</script>
