<?php
namespace Elementor\Modules\Promotions\Controls;

use Elementor\Base_Data_Control;

class Promotion_Control extends Base_Data_Control {

	const TYPE = 'promotion_control';

	public function get_type() {
		return static::TYPE;
	}

	public function content_template() {
		?>
		<div data-promotion="{{{ data.name }}}" class="elementor-control-type-switcher elementor-label-inline e-control-promotion__wrapper">
			<div class="elementor-control-content">
				<div class="elementor-control-field">
					<# if ( data.label ) {#>
						<label for="<?php $this->print_control_uid(); ?>" class="elementor-control-title">{{{ data.label }}}</label>
					<# } #>
					<span class="e-control-promotion__lock-wrapper">
						<i class="eicon-lock"></i>
					</span>
					<div class="elementor-control-input-wrapper">
						<label class="elementor-switch elementor-control-unit-2 e-control-promotion-switch">
							<input type="checkbox" class="elementor-switch-input" disabled>
							<span class="elementor-switch-label" data-off="Off"></span>
							<span class="elementor-switch-handle"></span>
						</label>
					</div>
					<div class="e-promotion-react-wrapper" data-promotion="{{{ data.name }}}"></div>
				</div>
			</div>
		</div>
		<?php
	}
}
