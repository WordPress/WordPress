<?php

class UM_Menu {

	function __construct() {

	}
	
	/***
	***	@new menu
	***/
	function new_ui( $position, $element, $trigger, $items ) {
		
		?>
		
		<div class="um-dropdown" data-element="<?php echo $element; ?>" data-position="<?php echo $position; ?>" data-trigger="<?php echo $trigger; ?>">
			<div class="um-dropdown-b">
				<div class="um-dropdown-arr"><i class=""></i></div>
				<ul>
					<?php foreach( $items as $k => $v ) { ?>
					
					<li><?php echo $v; ?></li>
					
					<?php } ?>
				</ul>
			</div>
		</div>
					
		<?php
		
	}

}