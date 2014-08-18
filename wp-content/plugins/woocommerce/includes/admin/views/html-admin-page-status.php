<div class="wrap woocommerce">
	<div class="icon32 icon32-woocommerce-status" id="icon-woocommerce"><br /></div><h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
		<?php
			$tabs = array(
				'status' => __( 'System Status', 'woocommerce' ),
				'tools'  => __( 'Tools', 'woocommerce' ),
			);
			foreach ( $tabs as $name => $label ) {
				echo '<a href="' . admin_url( 'admin.php?page=wc-status&tab=' . $name ) . '" class="nav-tab ';
				if ( $current_tab == $name ) echo 'nav-tab-active';
				echo '">' . $label . '</a>';
			}
		?>
	</h2><br/>
	<?php
		switch ( $current_tab ) {
			case "tools" :
				$this->status_tools();
			break;
			default :
				$this->status_report();
			break;
		}
	?>
</div>