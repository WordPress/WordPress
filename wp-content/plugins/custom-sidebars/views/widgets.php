<?php
/**
 * Updates the default widgets page of the admin area.
 * There are some HTML to be added for having all the functionality, so we
 * include it at the begining of the page, and it's placed later via js.
 */
?>

<div id="cs-widgets-extra">

	<?php /*
	============================================================================
	===== WIDGET head
	============================================================================
	*/ ?>
	<div id="cs-title-options">
		<h2><?php _e( 'Sidebars', CSB_LANG ); ?></h2>
		<div id="cs-options" class="csb cs-options">
			<button type="button" class="button button-primary cs-action btn-create-sidebar">
				<i class="dashicons dashicons-plus-alt"></i>
				<?php _e( 'Create a new sidebar', CSB_LANG ); ?>
			</button>
			<a href="#" class="cs-action btn-export"><?php _e( 'Import / Export Sidebars', CSB_LANG ); ?></a>
		</div>
	</div>


	<?php /*
	============================================================================
	===== LANGUAGE
	============================================================================
	*/ ?>
	<script>
	csSidebarsData = {
		'title_edit': "<?php _e( 'Edit [Sidebar]', CSB_LANG ); ?>",
		'title_new': "<?php _e( 'New Custom Sidebar', CSB_LANG ); ?>",
		'btn_edit': "<?php _e( 'Save Changes', CSB_LANG ); ?>",
		'btn_new': "<?php _e( 'Create Sidebar', CSB_LANG ); ?>",
		'title_delete': "<?php _e( 'Delete Sidebar', CSB_LANG ); ?>",
		'title_location': "<?php _e( 'Define where you want this sidebar to appear.', CSB_LANG ); ?>",
		'title_export': "<?php _e( 'Import / Export Sidebars', CSB_LANG ); ?>",
		'custom_sidebars': "<?php _e( 'Custom Sidebars', CSB_LANG ); ?>",
		'theme_sidebars': "<?php _e( 'Theme Sidebars', CSB_LANG ); ?>",
		'ajax_error': "<?php _e( 'Couldn\'t load data from WordPress...', CSB_LANG ); ?>",
		'lbl_replaceable': "<?php _e( 'This sidebar can be replaced on certain pages', CSB_LANG ); ?>",
		'replace_tip': "<?php _e( 'Activate this option to replace the sidebar with one of your custom sidebars.', CSB_LANG ); ?>",
		'replaceable': <?php echo json_encode( CustomSidebars::get_options( 'modifiable' ) ); ?>
	};
	</script>


	<?php /*
	============================================================================
	===== TOOLBAR for custom sidebars
	============================================================================
	*/ ?>
	<div class="cs-custom-sidebar cs-toolbar">
		<a
			class="cs-tool delete-sidebar"
			data-action="delete"
			href="#"
			title="<?php _e( 'Delete this sidebar.', CSB_LANG ); ?>"
			>
			<i class="dashicons dashicons-trash"></i>
		</a>
		<span class="cs-separator">|</span>
		<a
			class="cs-tool"
			data-action="edit"
			href="#"
			title="<?php _e( 'Edit this sidebar.', CSB_LANG ); ?>"
			>
			<?php _e( 'Edit', CSB_LANG ); ?>
		</a>
		<span class="cs-separator">|</span>
		<a
			class="cs-tool"
			data-action="location"
			href="#"
			title="<?php _e( 'Where do you want to show the sidebar?', CSB_LANG ); ?>"
			>
			<?php _e( 'Sidebar Location', CSB_LANG ); ?>
		</a>
		<span class="cs-separator">|</span>
	</div>


	<?php /*
	============================================================================
	===== TOOLBAR for theme sidebars
	============================================================================
	*/ ?>
	<div class="cs-theme-sidebar cs-toolbar">
		<label
			for="cs-replaceable"
			class="cs-tool btn-replaceable"
			data-action="replaceable"
			data-on="<?php _e( 'This sidebar can be replaced on certain pages', CSB_LANG ); ?>"
			data-off="<?php _e( 'This sidebar will always be same on all pages', CSB_LANG ); ?>"
			>
			<span class="icon"></span>
			<input
				type="checkbox"
				id=""
				class="has-label chk-replaceable"
				/>
			<span class="is-label">
				<?php _e( 'Allow this sidebar to be replaced', CSB_LANG ); ?>
			</span>
		</label>
		<span class="cs-separator">|</span>
		<span class="">
			<a
				class="cs-tool"
				data-action="location"
				href="#"
				title="<?php _e( 'Where do you want to show the sidebar?', CSB_LANG ); ?>"
				>
				<?php _e( 'Sidebar Location', CSB_LANG ); ?>
			</a>
			<span class="cs-separator">|</span>
		</span>
	</div>


	<?php /*
	============================================================================
	===== DELETE SIDEBAR confirmation
	============================================================================
	*/ ?>
	<div class="cs-delete">
	<?php include CSB_VIEWS_DIR . 'widgets-delete.php'; ?>
	</div>


	<?php /*
	============================================================================
	===== ADD/EDIT SIDEBAR
	============================================================================
	*/ ?>
	<div class="cs-editor">
	<?php include CSB_VIEWS_DIR . 'widgets-editor.php'; ?>
	</div>


	<?php /*
	============================================================================
	===== EXPORT
	============================================================================
	*/ ?>
	<div class="cs-export">
	<?php include CSB_VIEWS_DIR . 'widgets-export.php'; ?>
	</div>

	<?php /*
	============================================================================
	===== LOCATION popup.
	============================================================================
	*/ ?>
	<div class="cs-location">
	<?php include CSB_VIEWS_DIR . 'widgets-location.php'; ?>
	</div>

 </div>
