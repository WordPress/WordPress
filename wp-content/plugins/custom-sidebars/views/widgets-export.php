<?php
/**
 * Contents of the Import/Export popup in the widgets screen.
 *
 * This file is included in widgets.php.
 */
?>

<div class="wpmui-form module-export">
	<h2 class="no-pad-top"><?php _e( 'Export', CSB_LANG ); ?></h2>
	<form class="frm-export">
		<input type="hidden" name="do" value="export" />
		<p>
			<i class="dashicons dashicons-info light"></i>
			<?php _e(
				'This will generate a complete export file containing all ' .
				'your sidebars and the current sidebar configuration.', CSB_LANG
			); ?>
		</p>
		<p>
			<label for="description"><?php _e( 'Optional description for the export file:' ); ?></label><br />
			<textarea id="description" name="export-description" placeholder="" cols="80" rows="3"></textarea>
		</p>
		<p>
			<button class="button-primary">
				<i class="dashicons dashicons-download"></i> <?php _e( 'Export', CSB_LANG ); ?>
			</button>
		</p>
	</form>
	<hr />
	<h2><?php _e( 'Import', CSB_LANG ); ?></h2>
	<form class="frm-preview-import">
		<input type="hidden" name="do" value="preview-import" />
		<p>
			<label for="import-file"><?php _e( 'Export file', CSB_LANG ); ?></label>
			<input type="file" id="import-file" name="data" />
		</p>
		<p>
			<button class="button-primary">
				<i class="dashicons dashicons-upload"></i> <?php _e( 'Preview', CSB_LANG ); ?>
			</button>
		</p>
	</form>
	<div class="pro-layer">
		<?php printf(
			__(
				'Import / Export functionality is available<br />' .
				'in the <b>PRO</b> version of this plugin.<br />' .
				'<a href="%1$s" target="_blank">Learn more</a>', CSB_LANG
				),
				CustomSidebars::$pro_url
		); ?>
	</div>
</div>