<?php
namespace Elementor;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<script type="text/template" id="tmpl-elementor-template-library-header-actions">
	<?php if ( User::is_current_user_can_upload_json() ) { ?>
		<div id="elementor-template-library-header-import" class="elementor-templates-modal__header__item">
			<i class="eicon-upload-circle-o" aria-hidden="true"></i>
			<span class="elementor-screen-only"><?php echo esc_html__( 'Import Template', 'elementor' ); ?></span>
		</div>
	<?php } ?>
	<div id="elementor-template-library-header-sync" class="elementor-templates-modal__header__item">
		<i class="eicon-sync" aria-hidden="true"></i>
		<span class="elementor-screen-only"><?php echo esc_html__( 'Sync Library', 'elementor' ); ?></span>
	</div>
	<div id="elementor-template-library-header-save" class="elementor-templates-modal__header__item">
		<i class="eicon-save-o" aria-hidden="true"></i>
		<span class="elementor-screen-only"><?php echo esc_html__( 'Save', 'elementor' ); ?></span>
	</div>
</script>

<script type="text/template" id="tmpl-elementor-template-library-header-menu">
	<# jQuery.each( tabs, ( tab, args ) => { #>
		<div class="elementor-component-tab elementor-template-library-menu-item" data-tab="{{{ tab }}}">{{{ args.title }}}</div>
	<# } ); #>
</script>

<script type="text/template" id="tmpl-elementor-template-library-header-preview">
	<div id="elementor-template-library-header-preview-insert-wrapper" class="elementor-templates-modal__header__item">
		{{{ elementor.templates.layout.getTemplateActionButton( obj ) }}}
	</div>
</script>

<script type="text/template" id="tmpl-elementor-template-library-header-back">
	<i class="eicon-chevron-left" aria-hidden="true"></i>
	<span><?php echo esc_html__( 'Back to Library', 'elementor' ); ?></span>
</script>

<script type="text/template" id="tmpl-elementor-template-library-loading">
	<div class="elementor-loader-wrapper">
		<div class="elementor-loader">
			<div class="elementor-loader-boxes">
				<div class="elementor-loader-box"></div>
				<div class="elementor-loader-box"></div>
				<div class="elementor-loader-box"></div>
				<div class="elementor-loader-box"></div>
			</div>
		</div>
		<div class="elementor-loading-title"><?php echo esc_html__( 'Loading', 'elementor' ); ?></div>
	</div>
</script>

<script type="text/template" id="tmpl-elementor-template-library-templates">
	<#
		var activeSource = elementor.templates.getFilter('source');
		/**
		* Filter template source.
		*
		* @param bool   isRemote     - If `true` the source is a remote source.
		* @param string activeSource - The current template source.
		*/
		const isRemote = elementor.hooks.applyFilters( 'templates/source/is-remote', activeSource === 'remote', activeSource );
	#>
	<div id="elementor-template-library-toolbar">
		<# if ( isRemote ) {
			var activeType = elementor.templates.getFilter('type');
			#>
			<div id="elementor-template-library-filter-toolbar-remote" class="elementor-template-library-filter-toolbar">
				<# if ( 'page' === activeType ) { #>
					<div id="elementor-template-library-order">
						<input type="radio" id="elementor-template-library-order-new" class="elementor-template-library-order-input" name="elementor-template-library-order" value="date">
						<label for="elementor-template-library-order-new" class="elementor-template-library-order-label"><?php echo esc_html__( 'New', 'elementor' ); ?></label>
						<input type="radio" id="elementor-template-library-order-trend" class="elementor-template-library-order-input" name="elementor-template-library-order" value="trendIndex">
						<label for="elementor-template-library-order-trend" class="elementor-template-library-order-label"><?php echo esc_html__( 'Trend', 'elementor' ); ?></label>
						<input type="radio" id="elementor-template-library-order-popular" class="elementor-template-library-order-input" name="elementor-template-library-order" value="popularityIndex">
						<label for="elementor-template-library-order-popular" class="elementor-template-library-order-label"><?php echo esc_html__( 'Popular', 'elementor' ); ?></label>
					</div>
				<# } else if ( 'lb' !== activeType ) {
					var config = elementor.templates.getConfig( activeType );
					if ( config.categories ) { #>
						<div id="elementor-template-library-filter">
							<select id="elementor-template-library-filter-subtype" class="elementor-template-library-filter-select" data-elementor-filter="subtype">
								<option></option>
								<# config.categories.forEach( function( category ) {
									var selected = category === elementor.templates.getFilter( 'subtype' ) ? ' selected' : '';
									#>
									<option value="{{ category }}"{{{ selected }}}>{{{ category }}}</option>
								<# } ); #>
							</select>
						</div>
					<# }
				} #>
				<div id="elementor-template-library-my-favorites">
					<# var checked = elementor.templates.getFilter( 'favorite' ) ? ' checked' : ''; #>
					<input id="elementor-template-library-filter-my-favorites" type="checkbox"{{{ checked }}}>
					<label id="elementor-template-library-filter-my-favorites-label" for="elementor-template-library-filter-my-favorites">
						<i class="eicon" aria-hidden="true"></i>
						<?php echo esc_html__( 'My Favorites', 'elementor' ); ?>
					</label>
				</div>
			</div>
		<# } else { #>
			<div id="elementor-template-library-filter-toolbar-local" class="elementor-template-library-filter-toolbar">
				<div id="elementor-template-library-filter">
					<div class="elementor-template-library-filter-select-source">
						<div class="source-option<# if ( activeSource === 'local' ) { #> selected<# } #>" data-source="local">
							<i class="eicon-header" aria-hidden="true"></i>
							<?php echo esc_html__( 'Site templates', 'elementor' ); ?>
						</div>
						<div class="source-option<# if ( activeSource === 'cloud' ) { #> selected<# } #>" data-source="cloud">
							<i class="eicon-library-cloud-empty" aria-hidden="true"></i>
							<?php echo esc_html__( 'Cloud templates', 'elementor' ); ?>
							<#
								const tabIcon = elementor.templates.hasCloudLibraryQuota()
									? '<span class="new-badge"><?php echo esc_html__( 'New', 'elementor' ); ?></span>'
									: '<span class="new-badge"><i class="eicon-upgrade-crown" style="margin-inline-end: 0;"></i> <?php echo esc_html__( 'Pro', 'elementor' ); ?></span>';

								print( tabIcon );
							#>
						</div>
					</div>
				</div>
			</div>
		<# } #>

		<div class="elementor-template-library-filter-toolbar-side-actions">
				<# if ( 'cloud' === activeSource ) { #>
					<div id="elementor-template-library-add-new-folder" class="elementor-template-library-action-item">
						<i class="eicon-folder-plus" aria-hidden="true"></i>
						<span class="elementor-screen-only"><?php echo esc_html__( 'Create a New Folder', 'elementor' ); ?></span>
					</div>
					<span class="divider"></span>
					<div id="elementor-template-library-view-grid" class="elementor-template-library-action-item">
						<i class="eicon-library-grid" aria-hidden="true"></i>
						<span class="elementor-screen-only"><?php echo esc_html__( 'Grid view', 'elementor' ); ?></span>
					</div>
					<div id="elementor-template-library-view-list" class="elementor-template-library-action-item">
						<i class="eicon-library-list" aria-hidden="true"></i>
						<span class="elementor-screen-only"><?php echo esc_html__( 'List view', 'elementor' ); ?></span>
					</div>
				<# } #>
			<div id="elementor-template-library-filter-text-wrapper">
				<label for="elementor-template-library-filter-text" class="elementor-screen-only"><?php echo esc_html__( 'Search Templates:', 'elementor' ); ?></label>
				<input id="elementor-template-library-filter-text" placeholder="<?php echo esc_attr__( 'Search', 'elementor' ); ?>">
				<i class="eicon-search"></i>
			</div>
		</div>
	</div>
	<# if ( 'local' === activeSource || 'cloud' === activeSource ) { #>
		<div class="toolbar-container">
				<div class="bulk-selection-action-bar">
					<span class="clear-bulk-selections"><i class="eicon-editor-close"></i></span>
					<span class="selected-count"></span>
					<# if ( elementor.templates.hasCloudLibraryQuota() && ! elementor.templates.cloudLibraryIsDeactivated() ) { #>
					<span class="bulk-copy"><i class="eicon-library-copy" aria-hidden="true" title="<?php esc_attr_e( 'Copy', 'elementor' ); ?>"></i></span>
					<span class="bulk-move"><i class="eicon-library-move"  aria-hidden="true" title="<?php esc_attr_e( 'Move', 'elementor' ); ?>"></i></span>
					<# } #>
					<span class="bulk-delete"><i class="eicon-library-delete" aria-hidden="true" title="<?php esc_attr_e( 'Delete', 'elementor' ); ?>"></i></span>
				</div>
			<div id="elementor-template-library-navigation-container"></div>

			<# if ( 'cloud' === activeSource ) { #>
				<div class="quota-progress-container">
					<span class="quota-progress-info">
						<?php echo esc_html__( 'Usage', 'elementor' ); ?>
					</span>
					<div class="progress-bar-container">
						<div class="quota-progress-bar quota-progress-bar-normal">
							<div class="quota-progress-bar-fill"></div>
						</div>
						<span class="quota-warning"></span>
					</div>
					<div class="quota-progress-bar-value"></div>
				</div>
			<# } #>
		</div>
		<div id="elementor-template-library-order-toolbar-local">
			<div class="elementor-template-library-local-column-1">
				<input type="checkbox" id="bulk-select-all">
				<input type="radio" id="elementor-template-library-order-local-title" class="elementor-template-library-order-input" name="elementor-template-library-order-local" value="title" data-default-ordering-direction="asc">
				<label for="elementor-template-library-order-local-title" class="elementor-template-library-order-label"><?php echo esc_html__( 'Name', 'elementor' ); ?></label>
			</div>
			<div class="elementor-template-library-local-column-2">
				<input type="radio" id="elementor-template-library-order-local-type" class="elementor-template-library-order-input" name="elementor-template-library-order-local" value="type" data-default-ordering-direction="asc">
				<label for="elementor-template-library-order-local-type" class="elementor-template-library-order-label"><?php echo esc_html__( 'Type', 'elementor' ); ?></label>
			</div>
			<div class="elementor-template-library-local-column-3">
				<# if ( 'cloud' !== activeSource ) { #>
					<input type="radio" id="elementor-template-library-order-local-author" class="elementor-template-library-order-input" name="elementor-template-library-order-local" value="author" data-default-ordering-direction="asc">
				<# } #>
				<label for="elementor-template-library-order-local-author" class="elementor-template-library-order-label"><?php echo esc_html__( 'Created By', 'elementor' ); ?></label>
			</div>
			<div class="elementor-template-library-local-column-4">
				<input type="radio" id="elementor-template-library-order-local-date" class="elementor-template-library-order-input" name="elementor-template-library-order-local" value="date">
				<label for="elementor-template-library-order-local-date" class="elementor-template-library-order-label"><?php echo esc_html__( 'Creation Date', 'elementor' ); ?></label>
			</div>
			<div class="elementor-template-library-local-column-5">
				<div class="elementor-template-library-order-label"><?php echo esc_html__( 'Actions', 'elementor' ); ?></div>
			</div>
		</div>
	<# } #>
	<div id="elementor-template-library-templates-container"></div>
	<# if ( isRemote ) { #>
		<div id="elementor-template-library-footer-banner">
			<img class="elementor-nerd-box-icon" src="<?php
				Utils::print_unescaped_internal_string( ELEMENTOR_ASSETS_URL . 'images/information.svg' );
			?>" loading="lazy" alt="<?php echo esc_attr__( 'Elementor', 'elementor' ); ?>" />
			<div class="elementor-excerpt"><?php echo esc_html__( 'Stay tuned! More awesome templates coming real soon.', 'elementor' ); ?></div>
		</div>
	<# } #>
	<# if ( 'cloud' === activeSource ) { #>
		<div id="elementor-template-library-load-more-anchor" class="elementor-visibility-hidden"><i class="eicon-loading eicon-animation-spin"></i></div>
	<# } #>
</script>

<script type="text/template" id="tmpl-elementor-template-library-navigation-container">
	<button class="elementor-template-library-navigation-back-button elementor-button e-button">
		<i class="eicon-chevron-left"></i>
		<?php echo esc_html__( 'Back', 'elementor' ); ?>
	</button>
	<span class="elementor-template-library-current-folder-title"></span>
</script>

<script type="text/template" id="tmpl-elementor-template-library-template-remote">
	<div class="elementor-template-library-template-body">
		<?php // 'lp' stands for Landing Pages Library type. ?>
		<# if ( 'page' === type || 'lp' === type ) { #>
			<div class="elementor-template-library-template-screenshot" style="background-image: url({{ thumbnail }});"></div>
		<# } else { #>
			<img src="{{ thumbnail }}" loading="lazy">
		<# } #>
		<div class="elementor-template-library-template-preview">
			<i class="eicon-zoom-in-bold" aria-hidden="true"></i>
		</div>
	</div>
	<div class="elementor-template-library-template-footer">
		{{{ elementor.templates.layout.getTemplateActionButton( obj ) }}}
		<div class="elementor-template-library-template-name">{{{ title }}} - {{{ type }}}</div>
		<div class="elementor-template-library-favorite">
			<input id="elementor-template-library-template-{{ template_id }}-favorite-input" class="elementor-template-library-template-favorite-input" type="checkbox"{{ favorite ? " checked" : "" }}>
			<label for="elementor-template-library-template-{{ template_id }}-favorite-input" class="elementor-template-library-template-favorite-label">
				<i class="eicon-heart-o" aria-hidden="true"></i>
				<span class="elementor-screen-only"><?php echo esc_html__( 'Favorite', 'elementor' ); ?></span>
			</label>
		</div>
	</div>
</script>

<script type="text/template" id="tmpl-elementor-template-library-template-local">
	<#
		const activeSource = elementor.templates.getFilter('source');
		const view = elementor.templates.getFilter('view') ?? elementor.templates.getViewSelection() ?? 'list';

		if ( ( 'cloud' === activeSource && view === 'list' ) || 'local' === activeSource ) {
	#>
		<div class="elementor-template-library-template-name elementor-template-library-local-column-1">
			<input type="checkbox" class="bulk-selection-item-checkbox" data-template_id="{{ template_id }}" data-type="{{ type }}" data-status="{{ status }}">
			<# if ( 'cloud' === activeSource ) {
				const sourceIcon = typeof subType !== 'undefined' && 'FOLDER' === subType
					? '<i class="eicon-library-folder" aria-hidden="true"></i>'
					: 'locked' === status
						? '<i class="eicon-lock-outline" aria-hidden="true" title="<?php esc_attr_e( 'Upgrade to get more storage space or delete old templates to make room.', 'elementor' ); ?>"></i>'
						: '<i class="eicon-global-colors" aria-hidden="true"></i>';

					print( sourceIcon );
			} #>
			<span>{{ title }}</span>
		</div>
		<div class="elementor-template-library-template-meta elementor-template-library-template-type elementor-template-library-local-column-2">{{{ elementor.translate( type ) }}}</div>
		<div class="elementor-template-library-template-meta elementor-template-library-template-author elementor-template-library-local-column-3">{{{ author }}}</div>
		<div class="elementor-template-library-template-meta elementor-template-library-template-date elementor-template-library-local-column-4">{{{ human_date }}}</div>
		<div class="elementor-template-library-template-controls elementor-template-library-local-column-5">
		<#
			const previewClass = typeof subType !== 'undefined' && 'FOLDER' !== subType
				? 'elementor-hidden'
				: '';
		#>
		<div class="elementor-template-library-template-preview elementor-button e-btn-txt {{{previewClass}}}">
		<#
			const actionText = typeof subType === 'undefined' || 'FOLDER' !== subType
				? '<?php echo esc_html__( 'Preview', 'elementor' ); ?>'
				: '<?php echo esc_html__( 'Open', 'elementor' ); ?>';
		#>
			<i class="eicon-preview-medium" aria-hidden="true"></i>
			<span class="elementor-template-library-template-control-title">{{{ actionText }}}</span>
		</div>
		<# if ( typeof subType === 'undefined' || 'FOLDER' !== subType ) { #>
		<button class="elementor-template-library-template-action elementor-template-library-template-insert elementor-button e-primary e-btn-txt">
			<i class="eicon-library-download" aria-hidden="true"></i>
			<span class="elementor-button-title"><?php echo esc_html__( 'Insert', 'elementor' ); ?></span>
		</button>
		<# } #>
		<div class="elementor-template-library-template-more-toggle">
			<i class="eicon-ellipsis-h" aria-hidden="true"></i>
			<span class="elementor-screen-only"><?php echo esc_html__( 'More actions', 'elementor' ); ?></span>
		</div>
		<div class="elementor-template-library-template-more">
				<# if ( ( typeof subType === 'undefined' || 'FOLDER' !== subType ) && elementor.templates.hasCloudLibraryQuota() && ! elementor.templates.cloudLibraryIsDeactivated() ) { #>
					<div class="elementor-template-library-template-move">
						<i class="eicon-library-move" aria-hidden="true"></i>
						<span class="elementor-template-library-template-control-title"><?php echo esc_html__( 'Move to', 'elementor' ); ?></span>
					</div>
					<div class="elementor-template-library-template-copy">
						<i class="eicon-library-copy" aria-hidden="true"></i>
						<span class="elementor-template-library-template-control-title"><?php echo esc_html__( 'Copy to', 'elementor' ); ?></span>
					</div>
				<# } #>
			<div class="elementor-template-library-template-export">
				<a href="{{ export_link }}">
					<i class="eicon-library-download" aria-hidden="true"></i>
					<span class="elementor-template-library-template-control-title"><?php echo esc_html__( 'Export', 'elementor' ); ?></span>
				</a>
			</div>
			<div class="elementor-template-library-template-rename">
				<i class="eicon-library-edit" aria-hidden="true"></i>
				<span class="elementor-template-library-template-control-title"><?php echo esc_html__( 'Rename', 'elementor' ); ?></span>
			</div>
			<div class="elementor-template-library-template-delete">
				<i class="eicon-library-delete" aria-hidden="true"></i>
				<span class="elementor-template-library-template-control-title"><?php echo esc_html__( 'Delete', 'elementor' ); ?></span>
			</div>
		</div>
	</div>
	<# } else {
		if ( typeof subType !== 'undefined' && 'FOLDER' === subType ) {
	#>
		<div class="elementor-template-library-template-type-icon">
			<i class="eicon-library-folder-view" aria-hidden="true"></i>
			<span class="elementor-screen-only"><?php echo esc_html__( 'Folder', 'elementor' ); ?></span>
		</div>
		<div class="elementor-template-library-template-name">
			<span>{{ title }}</span>
		</div>
		<div class="elementor-template-library-template-more-toggle">
			<i class="eicon-ellipsis-v" aria-hidden="true"></i>
			<span class="elementor-screen-only"><?php echo esc_html__( 'More actions', 'elementor' ); ?></span>
		</div>
		<div class="elementor-template-library-template-more" style="display: none;">
			<div class="elementor-template-library-template-export">
				<a href="{{ export_link }}">
					<i class="eicon-library-download" aria-hidden="true"></i>
					<span class="elementor-template-library-template-control-title"><?php echo esc_html__( 'Export', 'elementor' ); ?></span>
				</a>
			</div>
			<div class="elementor-template-library-template-rename">
				<i class="eicon-library-edit" aria-hidden="true"></i>
				<span class="elementor-template-library-template-control-title"><?php echo esc_html__( 'Rename', 'elementor' ); ?></span>
			</div>
			<div class="elementor-template-library-template-delete">
				<i class="eicon-library-delete" aria-hidden="true"></i>
				<span class="elementor-template-library-template-control-title"><?php echo esc_html__( 'Delete', 'elementor' ); ?></span>
			</div>
		</div>
		<# } else { #>
			<#
				const imageSource = preview_url || '<?php echo esc_html( ELEMENTOR_ASSETS_URL . 'images/placeholder-cloud-grid.png' ); ?>';
			#>
			<div class="elementor-template-library-template-thumbnail">
				<img src="{{{ imageSource }}}"/>
				<div class="elementor-template-library-template-preview"></div>
			</div>
			<div class="elementor-template-library-card-footer">
				<div class="elementor-template-library-template-name">
					<# if ( 'locked' === status ) { #>
						<i class="eicon-lock-outline" aria-hidden="true" title="<?php esc_attr_e( 'Upgrade to get more storage space or delete old templates to make room.', 'elementor' ); ?>"></i>
					<# } #>
					<span>{{ title }}</span>
				</div>
				<div class="elementor-template-library-template-card-footer-overlay">
					<button class="elementor-template-library-template-action elementor-template-library-template-insert elementor-button e-primary">
						<i class="eicon-library-download" aria-hidden="true"></i>
						<span class="elementor-button-title"><?php echo esc_html__( 'Insert', 'elementor' ); ?></span>
					</button>
					<div class="elementor-template-library-template-card-footer-overlay-info">
						<div class="elementor-template-library-template-meta">{{{ author }}}</div>
						<div class="elementor-template-library-template-meta">{{{ human_date }}}</div>
					</div>
				</div>
				<div class="elementor-template-library-template-more-toggle">
					<i class="eicon-ellipsis-v" aria-hidden="true"></i>
					<span class="elementor-screen-only"><?php echo esc_html__( 'More actions', 'elementor' ); ?></span>
				</div>
				<div class="elementor-template-library-template-more" style="display: none;">
					<div class="elementor-template-library-template-move">
						<i class="eicon-library-move" aria-hidden="true"></i>
						<span class="elementor-template-library-template-control-title"><?php echo esc_html__( 'Move to', 'elementor' ); ?></span>
					</div>
					<div class="elementor-template-library-template-copy">
						<i class="eicon-library-copy" aria-hidden="true"></i>
						<span class="elementor-template-library-template-control-title"><?php echo esc_html__( 'Copy to', 'elementor' ); ?></span>
					</div>
					<div class="elementor-template-library-template-export">
						<a href="{{ export_link }}">
							<i class="eicon-library-download" aria-hidden="true"></i>
							<span class="elementor-template-library-template-control-title"><?php echo esc_html__( 'Export', 'elementor' ); ?></span>
						</a>
					</div>
					<div class="elementor-template-library-template-rename">
						<i class="eicon-library-edit" aria-hidden="true"></i>
						<span class="elementor-template-library-template-control-title"><?php echo esc_html__( 'Rename', 'elementor' ); ?></span>
					</div>
					<div class="elementor-template-library-template-delete">
						<i class="eicon-library-delete" aria-hidden="true"></i>
						<span class="elementor-template-library-template-control-title"><?php echo esc_html__( 'Delete', 'elementor' ); ?></span>
					</div>
				</div>
			</div>
	<# } } #>
</script>

<script type="text/template" id="tmpl-elementor-template-library-insert-button">
	<a class="elementor-template-library-template-action elementor-template-library-template-insert elementor-button e-primary">
		<i class="eicon-library-download" aria-hidden="true"></i>
		<span class="elementor-button-title"><?php echo esc_html__( 'Insert', 'elementor' ); ?></span>
	</a>
</script>

<script type="text/template" id="tmpl-elementor-template-library-apply-ai-button">
	<a class="elementor-template-library-template-action elementor-template-library-template-apply-ai elementor-button e-primary">
		<i class="eicon-file-download" aria-hidden="true"></i>
		<span class="elementor-button-title"><?php echo esc_html__( 'Apply', 'elementor' ); ?></span>
	</a>
</script>

<script type="text/template" id="tmpl-elementor-template-library-insert-and-ai-variations-buttons">
	<a class="elementor-template-library-template-action elementor-template-library-template-insert elementor-button e-primary">
		<i class="eicon-library-download" aria-hidden="true"></i>
		<span class="elementor-button-title"><?php echo esc_html__( 'Insert', 'elementor' ); ?></span>
	</a>
	<a class="elementor-template-library-template-action elementor-template-library-template-generate-variation elementor-button e-btn-txt e-btn-txt-border">
		<i class="eicon-ai" aria-hidden="true"></i>
		<span class="elementor-button-title"><?php echo esc_html__( 'Generate Variations', 'elementor' ); ?></span>
	</a>
</script>

<script type="text/template" id="tmpl-elementor-template-library-upgrade-plan-button">
	<a
		class="elementor-template-library-template-action elementor-button go-pro"
		href="{{{ promotionLink }}}"
		target="_blank"
	>
		<span class="elementor-button-title">{{{ promotionText }}}</span>
	</a>
</script>

<script type="text/template" id="tmpl-elementor-template-library-save-template">
	<div class="elementor-template-library-blank-icon">
		<#
			const templateIcon = typeof icon === 'undefined' ? '<i class="eicon-library-upload" aria-hidden="true"></i>' : icon;
			print( templateIcon );
		#>
		<span class="elementor-screen-only"><?php echo esc_html__( 'Save', 'elementor' ); ?></span>
	</div>
	<div class="elementor-template-library-blank-title">{{{ title }}}</div>
	<div class="elementor-template-library-blank-message">{{{ description }}}</div>
	<form id="elementor-template-library-save-template-form">
		<input type="hidden" name="post_id" value="<?php echo esc_attr( get_the_ID() ); ?>">
		<# if ( typeof canSaveToCloud === 'undefined' || ! canSaveToCloud ) { #>
		<input id="elementor-template-library-save-template-name" name="title" placeholder="<?php echo esc_attr__( 'Enter Template Name', 'elementor' ); ?>" required>
		<button id="elementor-template-library-save-template-submit" class="elementor-button e-primary">
			<span class="elementor-state-icon">
				<i class="eicon-loading eicon-animation-spin" aria-hidden="true"></i>
			</span>
			<?php echo esc_html__( 'Save', 'elementor' ); ?>
		</button>
		<# } else { #>
		<div class="cloud-library-form-inputs">
			<input id="elementor-template-library-save-template-name" name="title" placeholder="<?php echo esc_attr__( 'Give your template a name', 'elementor' ); ?>" required>
			<div class="source-selections">
				<div class="cloud-folder-selection-dropdown">
					<div class="cloud-folder-selection-dropdown-list"></div>
				</div>
				<div class="source-selections-input cloud">
					<input type="checkbox" id="cloud" name="cloud" value="cloud">
					<label for="cloud"> <?php echo esc_html__( 'Cloud Templates', 'elementor' ); ?></label> <span class="divider">/</span>  <div class="ellipsis-container"><i class="eicon-ellipsis-h"></i></div>
					<span class="selected-folder">
						<span class="selected-folder-text"></span>
						<i class="eicon-editor-close" aria-hidden="true"></i>
					</span>
					<# if ( elementor.config.library_connect.is_connected ) { #>
						<#
							const goLink = elementor.templates.hasCloudLibraryQuota()
								? 'https://go.elementor.com/go-pro-cloud-templates-save-to-100-usage-badge'
								: 'https://go.elementor.com/go-pro-cloud-templates-save-to-free-badge/';
						#>
					<span class="upgrade-badge">
						<a href="{{{ goLink }}}" target="_blank">
							<i class="eicon-upgrade-crown"></i><?php echo esc_html__( 'Upgrade', 'elementor' ); ?>
						</a>
					</span>
					<i class="eicon-info upgrade-tooltip" aria-hidden="true"></i>
					<# } else { #>
					<span class="connect-badge">
						<span class="connect-divider">|</span>
						<a id="elementor-template-library-connect__badge" href="{{{ elementorAppConfig?.[ 'cloud-library' ]?.library_connect_url }}}">
							<?php echo esc_html__( 'Connect', 'elementor' ); ?>
						</a>
					</span>
					<# } #>
				</div>
				<div class="source-selections-input local">
					<input type="checkbox" id="local" name="local" value="local">
					<label for="local"> <?php echo esc_html__( 'Site Templates', 'elementor' ); ?></label><br>
				</div>
				<input type="hidden" name="parentId" id="parentId" />
			</div>
			<div class="quota-cta">
				<p>
					<?php echo esc_html__( 'You’ve saved 100% of the templates in your plan.', 'elementor' ); ?>
					<br>
					<?php printf(
					/* translators: %s is the "Upgrade now" link */
						esc_html__( 'To get more space %s', 'elementor' ),
						'<a href="https://go.elementor.com/go-pro-cloud-templates-save-to-100-usage-notice">' . esc_html__( 'Upgrade now', 'elementor' ) . '</a>'
					); ?>
				</p>
			</div>
			<button id="elementor-template-library-save-template-submit" class="elementor-button e-primary">
				<span class="elementor-state-icon">
					<i class="eicon-loading eicon-animation-spin" aria-hidden="true"></i>
				</span>
				{{{ saveBtnText }}}
			</button>
		</div>
		<# } #>
	</form>
	<div class="elementor-template-library-blank-footer">
		<?php echo esc_html__( 'Learn more about the', 'elementor' ); ?>
		<a class="elementor-template-library-blank-footer-link" href="https://go.elementor.com/docs-library/" target="_blank"><?php echo esc_html__( 'Template Library', 'elementor' ); ?></a>
	</div>
</script>

<script type="text/template" id="tmpl-elementor-template-library-import">
	<form id="elementor-template-library-import-form">
		<div class="elementor-template-library-blank-icon">
			<i class="eicon-library-upload" aria-hidden="true"></i>
		</div>
		<div class="elementor-template-library-blank-title"><?php echo esc_html__( 'Import Template to Your Library', 'elementor' ); ?></div>
		<div class="elementor-template-library-blank-message"><?php echo esc_html__( 'Drag & drop your .JSON or .zip template file', 'elementor' ); ?></div>
		<div id="elementor-template-library-import-form-or"><?php echo esc_html__( 'or', 'elementor' ); ?></div>
		<label for="elementor-template-library-import-form-input" id="elementor-template-library-import-form-label" class="elementor-button e-primary"><?php echo esc_html__( 'Select File', 'elementor' ); ?></label>
		<input id="elementor-template-library-import-form-input" type="file" name="file" accept=".json,.zip" required/>
		<div class="elementor-template-library-blank-footer">
			<?php echo esc_html__( 'Learn more about the', 'elementor' ); ?>
			<a class="elementor-template-library-blank-footer-link" href="https://go.elementor.com/docs-library/" target="_blank"><?php echo esc_html__( 'Template Library', 'elementor' ); ?></a>
		</div>
	</form>
</script>

<script type="text/template" id="tmpl-elementor-template-library-templates-empty">
	<div class="elementor-template-library-blank-icon"></div>
	<div class="elementor-template-library-blank-title"></div>
	<div class="elementor-template-library-blank-message"></div>

	<div class="elementor-template-library-cloud-empty__button"></div>

	<div class="elementor-template-library-blank-footer">
		<?php echo esc_html__( 'Learn more about the', 'elementor' ); ?>
		<a class="elementor-template-library-blank-footer-link" href="https://go.elementor.com/docs-library/" target="_blank"><?php echo esc_html__( 'Template Library', 'elementor' ); ?></a>
	</div>
</script>

<script type="text/template" id="tmpl-elementor-template-library-preview">
	<iframe></iframe>
</script>

<script type="text/template" id="tmpl-elementor-template-library-connect">
	<div id="elementor-template-library-connect-logo" class="e-logo-wrapper">
		<i class="eicon-elementor" aria-hidden="true"></i>
	</div>
	<div class="elementor-template-library-blank-title">
		{{{ title }}}
	</div>
	<div class="elementor-template-library-blank-message">
		{{{ message }}}
	</div>
		<?php
		$url = Plugin::$instance->common->get_component( 'connect' )->get_app( 'library' )->get_admin_url( 'authorize', [
			'utm_source' => 'template-library',
			'utm_medium' => 'wp-dash',
			'utm_campaign' => 'library-connect',
			'utm_content' => '%%template_type%%', // will be replaced in the frontend
		] );
		?>
	<a id="elementor-template-library-connect__button" class="elementor-button e-primary" href="<?php echo esc_url( $url ); ?>">
		{{{ button }}}
	</a>
	<?php
	$base_images_url = $this->get_assets_base_url() . '/assets/images/library-connect/';

	$images = [ 'left-1', 'left-2', 'right-1', 'right-2' ];

	foreach ( $images as $image ) : ?>
		<img id="elementor-template-library-connect__background-image-<?php Utils::print_unescaped_internal_string( $image ); ?>" class="elementor-template-library-connect__background-image" src="<?php Utils::print_unescaped_internal_string( $base_images_url . $image ); ?>.png" draggable="false" loading="lazy" />
	<?php endforeach; ?>
</script>

<script type="text/template" id="tmpl-elementor-template-library-connect-states">
	<#
		const activeSource = elementor.templates.getFilter( 'source' );
	#>
	<div id="elementor-template-library-filter-toolbar-local" class="elementor-template-library-filter-toolbar" style="padding-block-end:80px;">
		<div id="elementor-template-library-filter">
			<div class="elementor-template-library-filter-select-source">
				<div class="source-option<# if ( activeSource === 'local' ) { #> selected<# } #>" data-source="local">
					<i class="eicon-header" aria-hidden="true"></i>
					<?php echo esc_html__( 'Site templates', 'elementor' ); ?>
				</div>
				<div class="source-option<# if ( activeSource === 'cloud' ) { #> selected<# } #>" data-source="cloud">
					<i class="eicon-library-cloud-empty" aria-hidden="true"></i>
					<?php echo esc_html__( 'Cloud templates', 'elementor' ); ?>
					<#
						const tabIcon = elementor.templates.hasCloudLibraryQuota()
							? '<span class="new-badge"><?php echo esc_html__( 'New', 'elementor' ); ?></span>'
							: '<span class="new-badge"><i class="eicon-upgrade-crown" style="margin-inline-end: 0;"></i> <?php echo esc_html__( 'Pro', 'elementor' ); ?></span>';

						print( tabIcon );
					#>
				</div>
			</div>
		</div>
	</div>
	<div class="elementor-template-library-blank-icon"></div>
	<div class="elementor-template-library-blank-title"></div>
	<div class="elementor-template-library-blank-message"></div>

	<div class="elementor-template-library-cloud-empty__button"></div>
</script>
