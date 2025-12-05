<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$document = Plugin::$instance->documents->get( Plugin::$instance->editor->get_post_id() );
?>
<script type="text/template" id="tmpl-elementor-panel">
	<div id="elementor-panel-state-loading">
		<i class="eicon-loading eicon-animation-spin"></i>
	</div>
	<header id="elementor-panel-header-wrapper"></header>
	<main id="elementor-panel-content-wrapper"></main>
	<footer id="elementor-panel-footer">
		<div class="elementor-panel-container"></div>
	</footer>
	<div id="elementor-mode-switcher"></div>
</script>

<script type="text/template" id="tmpl-elementor-panel-menu">
	<div id="elementor-panel-page-menu-content"></div>
	<# if ( elementor.config.document.panel.needHelpUrl ) { #>
	<div id="elementor-panel__editor__help">
		<a id="elementor-panel__editor__help__link" href="{{{ elementor.config.document.panel.needHelpUrl }}}" target="_blank">
			<?php echo esc_html__( 'Need Help', 'elementor' ); ?>
			<i class="eicon-help-o" aria-hidden="true"></i>
		</a>
	</div>
	<# } #>
</script>

<script type="text/template" id="tmpl-elementor-panel-menu-group">
	<div class="elementor-panel-menu-group-title">{{{ title }}}</div>
	<div class="elementor-panel-menu-items"></div>
</script>

<script type="text/template" id="tmpl-elementor-panel-menu-item">
	<div class="elementor-panel-menu-item-icon">
		<i class="{{ icon }}"></i>
	</div>
	<# if ( 'undefined' === typeof type || 'link' !== type ) { #>
		<div class="elementor-panel-menu-item-title">{{{ title }}}</div>
	<# } else {
		let target = ( 'undefined' !== typeof newTab && newTab ) ? '_blank' : '_self';
	#>
		<a href="{{ link }}" target="{{ target }}"><div class="elementor-panel-menu-item-title">{{{ title }}}</div></a>
	<# } #>
</script>

<script type="text/template" id="tmpl-elementor-exit-dialog">
	<div><?php echo esc_html__( 'Now you can choose where you want to go on the site from the following options', 'elementor' ); ?></div>
	<div>
		<?php printf(
			/* translators: 1: Opening HTML <a> tag, 2: closing HTML <a> tag. */
			esc_html__( 'Any time you can change the settings in %1$sUser Preferences%2$s', 'elementor' ),
			'<a id="user-preferences">',
			'</a>'
		); ?>
	</div>
	<select id="exit-to-preferences"></select> <!--  Adding options by JS  -->
</script>

<script type="text/template" id="tmpl-elementor-panel-header">
	<button id="elementor-panel-header-menu-button" class="elementor-header-button" aria-label="<?php echo esc_attr__( 'Menu', 'elementor' ); ?>">
		<i class="elementor-icon eicon-menu-bar tooltip-target" aria-hidden="true" data-tooltip="<?php echo esc_attr__( 'Menu', 'elementor' ); ?>"></i>
	</button>
	<h2 id="elementor-panel-header-title"></h2>
	<# const extraClass = $e.components.get( 'document/elements' ).utils.allowAddingWidgets() ? '' : 'elementor-visibility-hidden'; #>
	<button id="elementor-panel-header-add-button" class="elementor-header-button {{{ extraClass }}}" aria-label="<?php echo esc_attr__( 'Widgets Panel', 'elementor' ); ?>">
		<i class="elementor-icon eicon-apps tooltip-target" aria-hidden="true" data-tooltip="<?php echo esc_attr__( 'Widgets Panel', 'elementor' ); ?>"></i>
	</button>
</script>

<script type="text/template" id="tmpl-elementor-panel-footer-content">
	<button id="elementor-panel-footer-settings" class="elementor-panel-footer-tool elementor-leave-open tooltip-target" data-tooltip="<?php echo esc_attr__( 'Settings', 'elementor' ); ?>" aria-label="<?php
	printf(
		/* translators: %s: Document title. */
		esc_attr__( '%s Settings', 'elementor' ),
		esc_attr( $document::get_title() )
	); ?>">
		<i class="eicon-cog" aria-hidden="true"></i>
	</button>
	<# if ( $e.components.get( 'document/elements' ).utils.showNavigator() ) { #>
	<button id="elementor-panel-footer-navigator" class="elementor-panel-footer-tool tooltip-target" data-tooltip="<?php echo esc_attr__( 'Structure', 'elementor' ); ?>" aria-label="<?php echo esc_attr__( 'Structure', 'elementor' ); ?>">
		<i class="eicon-navigator" aria-hidden="true"></i>
	</button>
	<# } #>
	<button id="elementor-panel-footer-history" class="elementor-panel-footer-tool elementor-leave-open tooltip-target" data-tooltip="<?php echo esc_attr__( 'History', 'elementor' ); ?>" aria-label="<?php echo esc_attr__( 'History', 'elementor' ); ?>">
		<i class="eicon-history" aria-hidden="true"></i>
	</button>
	<button id="elementor-panel-footer-responsive" class="elementor-panel-footer-tool elementor-toggle-state tooltip-target" data-tooltip="<?php echo esc_attr__( 'Responsive Mode', 'elementor' ); ?>" aria-label="<?php echo esc_attr__( 'Responsive Mode', 'elementor' ); ?>">
		<i class="eicon-device-responsive" aria-hidden="true"></i>
	</button>
	<button id="elementor-panel-footer-saver-preview" class="elementor-panel-footer-tool tooltip-target" data-tooltip="<?php echo esc_attr__( 'Preview Changes', 'elementor' ); ?>" aria-label="<?php echo esc_attr__( 'Preview Changes', 'elementor' ); ?>">
		<span id="elementor-panel-footer-saver-preview-label">
			<i class="eicon-preview-medium" aria-hidden="true"></i>
		</span>
	</button>
	<div id="elementor-panel-footer-saver-publish" class="elementor-panel-footer-tool">
		<# const publishTitle = $e.components.get( 'document/elements' ).utils.getTitleForPublishButton(); #>
		<button id="elementor-panel-saver-button-publish" class="elementor-button e-primary elementor-disabled" title="{{{ publishTitle }}}">
			<span class="elementor-state-icon">
				<i class="eicon-loading eicon-animation-spin" aria-hidden="true"></i>
			</span>
			<span id="elementor-panel-saver-button-publish-label">
				<?php echo esc_html__( 'Publish', 'elementor' ); ?>
			</span>
		</button>
	</div>
	<div id="elementor-panel-footer-saver-options" class="elementor-panel-footer-tool elementor-toggle-state">
		<button id="elementor-panel-saver-button-save-options" class="elementor-button e-primary tooltip-target elementor-disabled" data-tooltip="<?php echo esc_attr__( 'Save Options', 'elementor' ); ?>" data-tooltip-offset="7" aria-label="<?php echo esc_attr__( 'Save Options', 'elementor' ); ?>">
			<i class="eicon-chevron-right" aria-hidden="true"></i>
		</button>
		<div class="elementor-panel-footer-sub-menu-wrapper">
			<p class="elementor-last-edited-wrapper">
				<span class="elementor-state-icon">
					<i class="eicon-loading eicon-animation-spin" aria-hidden="true"></i>
				</span>
				<span class="elementor-last-edited">
				</span>
			</p>
			<div class="elementor-panel-footer-sub-menu">
				<div id="elementor-panel-footer-sub-menu-item-save-draft" class="elementor-panel-footer-sub-menu-item elementor-disabled">
					<i class="elementor-icon eicon-save" aria-hidden="true"></i>
					<span class="elementor-title"><?php echo esc_html__( 'Save Draft', 'elementor' ); ?></span>
				</div>
				<div id="elementor-panel-footer-sub-menu-item-save-template" class="elementor-panel-footer-sub-menu-item">
					<i class="elementor-icon eicon-folder" aria-hidden="true"></i>
					<span class="elementor-title"><?php echo esc_html__( 'Save as Template', 'elementor' ); ?></span>
				</div>
				<# if ( $e.components.get( 'document/elements' ).utils.showCopyAndShareButton() ) { #>
				<div id="elementor-panel-footer-sub-menu-item-copy-share-link" class="elementor-panel-footer-sub-menu-item">
					<i class="elementor-icon eicon-link" aria-hidden="true"></i>
					<span class="elementor-title"><?php echo esc_html__( 'Copy and Share Link', 'elementor' ); ?></span>
				</div>
				<# } #>
			</div>
		</div>
	</div>
</script>

<script type="text/template" id="tmpl-elementor-mode-switcher-content">
	<label for="elementor-mode-switcher-preview-input" id="elementor-mode-switcher-preview" title="<?php echo esc_attr__( 'Hide Panel', 'elementor' ); ?>">
		<i class="eicon eicon-angle-left" aria-hidden="true" tabindex="0"></i>
		<span class="elementor-screen-only"><?php echo esc_html__( 'Hide Panel', 'elementor' ); ?></span>
	</label>
	<input id="elementor-mode-switcher-preview-input" type="checkbox">
</script>

<script type="text/template" id="tmpl-editor-content">
	<div class="elementor-panel-navigation">
		<# _.each( elementData.tabs_controls, function( tabTitle, tabSlug ) {
			if ( 'content' !== tabSlug && ! elementor.userCan( 'design' ) ) {
				return;
			}
			$e.bc.ensureTab( 'panel/editor', tabSlug );
			#>
			<button class="elementor-component-tab elementor-panel-navigation-tab elementor-tab-control-{{ tabSlug }}" data-tab="{{ tabSlug }}">
				<span>{{{ tabTitle }}}</span>
			</button>
		<# } ); #>
	</div>
	<# if ( elementData.reload_preview ) { #>
		<div class="elementor-update-preview">
			<div class="elementor-update-preview-title"><?php echo esc_html__( 'Update changes to page', 'elementor' ); ?></div>
			<div class="elementor-update-preview-button-wrapper">
				<button class="elementor-update-preview-button elementor-button"><?php echo esc_html__( 'Apply', 'elementor' ); ?></button>
			</div>
		</div>
	<# } #>
	<div id="elementor-controls"></div>
	<# if ( elementData.help_url ) { #>
		<div id="elementor-panel__editor__help">
			<a id="elementor-panel__editor__help__link" href="{{ elementData.help_url }}" target="_blank">
				<?php echo esc_html__( 'Need Help', 'elementor' ); ?>
				<i class="eicon-help-o" aria-hidden="true"></i>
			</a>
		</div>
	<# } #>

	<# if ( elementData.upsale_data && elementData.upsale_data.condition ) { #>
	<div class="elementor-nerd-box elementor-nerd-box--upsale">
		<# if ( elementData.upsale_data.image ) { #>
		<img class="elementor-nerd-box-icon" src="{{ elementData.upsale_data.image }}" loading="lazy" alt="{{ elementData.upsale_data.image_alt }}" />
		<# } #>
		<# if ( elementData.upsale_data.title ) { #>
		<div class="elementor-nerd-box-title">{{{ elementData.upsale_data.title }}}</div>
		<# } #>
		<# if ( elementData.upsale_data.description ) { #>
		<div class="elementor-nerd-box-message">{{{ elementData.upsale_data.description }}}</div>
		<# } #>
		<# if ( elementData.upsale_data.upgrade_url && elementData.upsale_data.upgrade_text ) { #>
		<a class="elementor-button go-pro" href="{{ elementData.upsale_data.upgrade_url }}" target="_blank">{{{ elementData.upsale_data.upgrade_text }}}</a>
		<# } #>
	</div>
	<# } #>
</script>

<script type="text/template" id="tmpl-elementor-panel-schemes-disabled">
	<img class="elementor-nerd-box-icon" src="<?php Utils::print_unescaped_internal_string( ELEMENTOR_ASSETS_URL . 'images/information.svg' ); ?>" loading="lazy" alt="<?php echo esc_attr__( 'Elementor', 'elementor' ); ?>" />
	<div class="elementor-nerd-box-title">{{{ '<?php echo esc_html__( '%s are disabled', 'elementor' ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?>'.replace( '%s', disabledTitle ) }}}</div>
	<div class="elementor-nerd-box-message"><?php
		printf(
			/* translators: %1$s Link open tag, %2$s: Link close tag. */
			esc_html__( 'You can enable it from the %1$sElementor settings page%2$s.', 'elementor' ),
			'<a href="' . esc_url( Settings::get_url() ) . '" target="_blank">',
			'</a>'
		);
		?></div>
</script>

<script type="text/template" id="tmpl-elementor-panel-scheme-color-item">
	<div class="elementor-panel-scheme-color-picker-placeholder"></div>
	<div class="elementor-panel-scheme-color-title">{{{ title }}}</div>
</script>

<script type="text/template" id="tmpl-elementor-panel-scheme-typography-item">
	<div class="elementor-panel-heading">
		<div class="elementor-panel-heading-toggle">
			<i class="eicon-caret-right" aria-hidden="true"></i>
		</div>
		<div class="elementor-panel-heading-title">{{{ title }}}</div>
	</div>
	<div class="elementor-panel-scheme-typography-items elementor-panel-box-content">
		<?php
		$scheme_fields_keys = Group_Control_Typography::get_scheme_fields_keys();

		$typography_group = Plugin::$instance->controls_manager->get_control_groups( 'typography' );
		$typography_fields = $typography_group->get_fields();

		$scheme_fields = array_intersect_key( $typography_fields, array_flip( $scheme_fields_keys ) );

		foreach ( $scheme_fields as $option_name => $option ) :
			?>
			<div class="elementor-panel-scheme-typography-item elementor-control elementor-control-type-select">
				<div class="elementor-panel-scheme-item-title elementor-control-title"><?php echo esc_html( $option['label'] ); ?></div>
				<div class="elementor-panel-scheme-typography-item-value elementor-control-input-wrapper">
					<?php if ( 'select' === $option['type'] ) : ?>
						<select name="<?php echo esc_attr( $option_name ); ?>" class="elementor-panel-scheme-typography-item-field">
							<?php foreach ( $option['options'] as $field_key => $field_value ) : ?>
								<option value="<?php echo esc_attr( $field_key ); ?>"><?php echo esc_html( $field_value ); ?></option>
							<?php endforeach; ?>
						</select>
					<?php elseif ( 'font' === $option['type'] ) : ?>
						<select name="<?php echo esc_attr( $option_name ); ?>" class="elementor-panel-scheme-typography-item-field">
							<option value=""><?php echo esc_html__( 'Default', 'elementor' ); ?></option>
							<?php foreach ( Fonts::get_font_groups() as $group_type => $group_label ) : ?>
								<optgroup label="<?php echo esc_attr( $group_label ); ?>">
									<?php foreach ( Fonts::get_fonts_by_groups( [ $group_type ] ) as $font_title => $font_type ) : ?>
										<option value="<?php echo esc_attr( $font_title ); ?>"><?php echo esc_html( $font_title ); ?></option>
									<?php endforeach; ?>
								</optgroup>
							<?php endforeach; ?>
						</select>
					<?php elseif ( 'text' === $option['type'] ) : ?>
						<input name="<?php echo esc_attr( $option_name ); ?>" class="elementor-panel-scheme-typography-item-field" />
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</script>

<script type="text/template" id="tmpl-elementor-control-responsive-switchers">
	<div class="elementor-control-responsive-switchers">
		<div class="elementor-control-responsive-switchers__holder">
		<#
			const activeBreakpoints = elementor.config.responsive.activeBreakpoints,
				devicesForDisplay = elementor.breakpoints.getActiveBreakpointsList( { largeToSmall: true, withDesktop: true } );

			var devices = responsive.devices || devicesForDisplay;

			_.each( devices, function( device ) {
				// The 'Desktop' label is made accessible via the global config because it needs to be translated.
				var deviceLabel = 'desktop' === device ? '<?php esc_html_e( 'Desktop', 'elementor' ); ?>' : activeBreakpoints[ device ].label,
					tooltipDir = "<?php echo is_rtl() ? 'e' : 'w'; ?>";
			#>
				<button class="elementor-responsive-switcher tooltip-target elementor-responsive-switcher-{{ device }}" data-device="{{ device }}" data-tooltip="{{ deviceLabel }}" data-tooltip-pos="{{ tooltipDir }}" aria-label="{{ deviceLabel }}">
					<i class="{{ elementor.config.responsive.icons_map[ device ] }}" aria-hidden="true"></i>
				</button>
			<# } );
		#>
		</div>
	</div>
</script>

<script type="text/template" id="tmpl-elementor-control-dynamic-switcher">
	<button class="elementor-control-dynamic-switcher elementor-control-unit-1" data-tooltip="<?php echo esc_attr__( 'Dynamic Tags', 'elementor' ); ?>" aria-label="<?php echo esc_attr__( 'Dynamic Tags', 'elementor' ); ?>">
		<i class="eicon-database" aria-hidden="true"></i>
	</button>
</script>

<script type="text/template" id="tmpl-elementor-control-element-color-picker">
	<button class="elementor-control-element-color-picker e-control-tool" data-tooltip="<?php echo esc_attr__( 'Color Sampler', 'elementor' ); ?>" aria-label="<?php echo esc_attr__( 'Color Sampler', 'elementor' ); ?>">
		<i class="eicon-eyedropper" aria-hidden="true"></i>
	</button>
</script>

<script type="text/template" id="tmpl-elementor-control-dynamic-cover">
	<div class="elementor-dynamic-cover__settings">
		<i class="eicon-{{ hasSettings ? 'wrench' : 'database' }}"></i>
	</div>
	<div class="elementor-dynamic-cover__title" title="{{{ title + ' ' + content }}}">{{{ title + ' ' + content }}}</div>
	<# if ( isRemovable ) { #>
		<div class="elementor-dynamic-cover__remove">
			<i class="eicon-close-circle"></i>
		</div>
	<# } #>
</script>

<script type="text/template" id="tmpl-elementor-dynamic-tags-promo">
	<div class="elementor-tags-list__teaser">
		<div class="elementor-tags-list__group-title elementor-tags-list__teaser-title">
			<i class="eicon-info-circle"></i><?php echo esc_html__( 'Elementor Dynamic Content', 'elementor' ); ?>
		</div>
		<div class="elementor-tags-list__teaser-text">
			<?php echo esc_html__( 'You’re missing out!', 'elementor' ); ?><br />
			<?php echo esc_html__( 'Get more dynamic capabilities by incorporating dozens of Elementor\'s native dynamic tags.', 'elementor' ); ?>
			<a href="{{{ promotionUrl }}}" class="elementor-tags-list__teaser-link" target="_blank">
				<?php echo esc_html__( 'Upgrade', 'elementor' ); ?>
			</a>
		</div>
	</div>
</script>
