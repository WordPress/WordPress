<?php
/**
 * Contents of the Location popup in the widgets screen.
 * User can define default locations where the custom sidebar will be used.
 *
 * This file is included in widgets.php.
 */

$sidebars = CustomSidebars::get_sidebars( 'theme' );

/**
 * Output the input fields to configure replacements for a single sidebar.
 *
 * @since  2.0
 * @param  array $sidebar Details provided by CustomSidebars::get_sidebar().
 * @param  string $prefix Category specific prefix used for input field ID/Name.
 * @param  string $cat_name Used in label: "Replace sidebar for <cat_name>".
 * @param  string $class Optinal classname added to the wrapper element.
 */
function _show_replaceable( $sidebar, $prefix, $cat_name, $class = '' ) {
	$base_id = 'cs-' . $prefix;
	$inp_id = $base_id . '-' . $sidebar['id'];
	$inp_name = 'cs[' . $prefix . '][' . $sidebar['id'] . ']';
	$sb_id = $sidebar['id'];
	$class = (empty( $class ) ? '' : ' ' . $class);

	?>
	<div
		class="cs-replaceable <?php echo esc_attr( $sb_id . $class ); ?>"
		data-lbl-used="<?php _e( 'Replaced by another sidebar:', CSB_LANG ); ?>"
		>
		<label for="<?php echo esc_attr( $inp_id ); ?>">
			<input type="checkbox"
				id="<?php echo esc_attr( $inp_id ); ?>"
				class="detail-toggle"
				/>
			<?php printf(
				__( 'As <strong>%1$s</strong> for selected %2$s', CSB_LANG ),
				$sidebar['name'],
				$cat_name
			); ?>
		</label>
		<div class="details">
			<select
				class="cs-datalist <?php echo esc_attr( $base_id ); ?>"
				name="<?php echo esc_attr( $inp_name ); ?>[]"
				multiple="multiple"
				placeholder="<?php echo esc_attr(
					sprintf(
						__( 'Click here to pick available %1$s', CSB_LANG ),
						$cat_name
					)
				); ?>"
			>
			</select>
		</div>
	</div>
	<?php

}

?>

<form class="frm-location wpmui-form">
	<input type="hidden" name="do" value="set-location" />
	<input type="hidden" name="sb" class="sb-id" value="" />

	<div class="cs-title">
		<h3 class="no-pad-top">
			<span class="sb-name">...</span>
		</h3>
	</div>
	<p>
		<i class="dashicons dashicons-info light"></i>
		<?php printf(
			__(
			'To attach this sidebar to a unique Post or Page please visit ' .
			'that <a href="%1$s">Post</a> or <a href="%2$s">Page</a> & set it ' .
			'up via the sidebars metabox.', CSB_LANG
			),
			admin_url( 'edit.php' ),
			admin_url( 'edit.php?post_type=page' )
		); ?>
	</p>

	<?php
	/**
	 * =========================================================================
	 * Box 1: SINGLE entries (single pages, categories)
	 */
	?>
	<div class="wpmui-box">
		<h3>
			<a href="#" class="toggle" title="<?php _e( 'Click to toggle' ); /* This is a Wordpress default language */ ?>"><br></a>
			<span><?php _e( 'For all Single Entries matching selected criteria', CSB_LANG ); ?></span>
		</h3>
		<div class="inside">
			<p><?php _e( 'These replacements will be applied to every single post that matches a certain post type or category.', CSB_LANG ); ?>

			<div class="cs-half">
			<?php
			/**
			 * ========== SINGLE -- Categories ========== *
			 */
			foreach ( $sidebars as $sb_id => $details ) {
				$cat_name = __( 'categories', CSB_LANG );
				_show_replaceable( $details, 'cat', $cat_name );
			}
			?>
			</div>

			<div class="cs-half">
			<?php
			/**
			 * ========== SINGLE -- Post-Type ========== *
			 */
			foreach ( $sidebars as $sb_id => $details ) {
				$cat_name = __( 'Post Types', CSB_LANG );
				_show_replaceable( $details, 'pt', $cat_name );
			}
			?>
			</div>

		</div>
	</div>

	<?php
	/**
	 * =========================================================================
	 * Box 2: ARCHIVE pages
	 */
	?>
	<div class="wpmui-box closed">
		<h3>
			<a href="#" class="toggle" title="<?php _e( 'Click to toggle' ); /* This is a Wordpress default language */ ?>"><br></a>
			<span><?php _e( 'For Archives', CSB_LANG ); ?></span>
		</h3>
		<div class="inside">
			<p><?php _e( 'These replacements will be applied to Archive Type posts and pages.', CSB_LANG ); ?>

			<h3 class="wpmui-tabs">
				<a href="#tab-arch" class="tab active"><?php _e( 'Archive Types', CSB_LANG ); ?></a>
				<a href="#tab-catg" class="tab"><?php _e( 'Category Archives', CSB_LANG ); ?></a>
			</h3>
			<div class="wpmui-tab-contents">
				<div id="tab-arch" class="tab active">
					<?php
					/**
					 * ========== ARCHIVE -- Special ========== *
					 */
					foreach ( $sidebars as $sb_id => $details ) {
						$cat_name = __( 'Archive Types', CSB_LANG );
						_show_replaceable( $details, 'arc', $cat_name );
					}
					?>
				</div>
				<div id="tab-catg" class="tab">
					<?php
					/**
					 * ========== ARCHIVE -- Category ========== *
					 */
					foreach ( $sidebars as $sb_id => $details ) {
						$cat_name = __( 'Category Archives', CSB_LANG );
						_show_replaceable( $details, 'arc-cat', $cat_name );
					}
					?>
				</div>
			</div>
		</div>
	</div>

	<div class="buttons">
		<button type="button" class="button-link btn-cancel"><?php _e( 'Cancel', CSB_LANG ); ?></button>
		<button type="button" class="button-primary btn-save"><?php _e( 'Save Changes', CSB_LANG ); ?></button>
	</div>
</form>
