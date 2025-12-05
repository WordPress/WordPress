<?php

add_action(
	'init',
	'wpcf7_init_block_editor_assets',
	10, 0
);

function wpcf7_init_block_editor_assets() {
	register_block_type(
		wpcf7_plugin_path( 'includes/block-editor' )
	);
}
