<div class="wrap woocommerce wc_addons_wrap">
	<div class="icon32 icon32-posts-product" id="icon-woocommerce"><br /></div>
	<h2>
		<?php _e( 'WooCommerce Add-ons/Extensions', 'woocommerce' ); ?>
		<a href="http://www.woothemes.com/product-category/woocommerce-extensions/" class="add-new-h2"><?php _e( 'Browse all extensions', 'woocommerce' ); ?></a>
		<a href="http://www.woothemes.com/product-category/themes/woocommerce/" class="add-new-h2"><?php _e( 'Browse themes', 'woocommerce' ); ?></a>
	</h2>
	<?php if ( $addons ) : ?>
		<ul class="subsubsub">
			<?php
				$links = array(
					''                         => __( 'Popular', 'woocommerce' ),
					'payment-gateways'         => __( 'Gateways', 'woocommerce' ),
					'shipping-methods'         => __( 'Shipping', 'woocommerce' ),
					'import-export-extensions' => __( 'Import/export', 'woocommerce' ),
					'product-extensions'       => __( 'Products', 'woocommerce' ),
					'marketing-extensions'     => __( 'Marketing', 'woocommerce' ),
					'accounting-extensions'	   => __( 'Acounting', 'woocommerce' ),
					'free-extensions'          => __( 'Free', 'woocommerce' ),
					'third-party-extensions'   => __( 'Third-party', 'woocommerce' ),
				);

				$i = 0;

				foreach ( $links as $link => $name ) {
					$i ++;
					?><li><a class="<?php if ( $view == $link ) echo 'current'; ?>" href="<?php echo admin_url( 'admin.php?page=wc-addons&view=' . esc_attr( $link ) ); ?>"><?php echo $name; ?></a><?php if ( $i != sizeof( $links ) ) echo ' |'; ?></li><?php
				}
			?>
		</ul>
		<br class="clear" />
		<ul class="products">
		<?php
			switch ( $view ) {
				case '':
					$addons = $addons->popular;
				break;
				case 'payment-gateways':
					$addons = $addons->{'payment-gateways'};
				break;
				case 'shipping-methods':
					$addons = $addons->{'shipping-methods'};
				break;
				case 'import-export-extensions':
					$addons = $addons->{'import-export'};
				break;
				case 'product-extensions':
					$addons = $addons->product;
				break;
				case 'marketing-extensions':
					$addons = $addons->marketing;
				break;
				case 'accounting-extensions':
					$addons = $addons->accounting;
				break;
				case 'free-extensions':
					$addons = $addons->free;
				break;
				case 'third-party-extensions':
					$addons = $addons->{'third-party'};
				break;
			}

			foreach ( $addons as $addon ) {
				echo '<li class="product">';
				echo '<a href="' . $addon->link . '">';
				if ( ! empty( $addon->image ) ) {
					echo '<img src="' . $addon->image . '"/>';
				} else {
					echo '<h3>' . $addon->title . '</h3>';
				}
				echo '<span class="price">' . $addon->price . '</span>';
				echo '<p>' . $addon->excerpt . '</p>';
				echo '</a>';
				echo '</li>';
			}
		?>
		</ul>
	<?php else : ?>

		<p><?php printf( __( 'Our catalog of WooCommerce Extensions can be found on WooThemes.com here: <a href="%s">WooCommerce Extensions Catalog</a>', 'woocommerce' ), 'http://www.woothemes.com/product-category/woocommerce-extensions/' ); ?></p>

	<?php endif; ?>
</div>