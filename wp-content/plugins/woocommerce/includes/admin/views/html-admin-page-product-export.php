<?php
/**
 * Admin View: Product Export
 *
 * @package WooCommerce\Admin\Export
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wp_enqueue_script( 'wc-product-export' );

$exporter = new WC_Product_CSV_Exporter();
?>
<div class="wrap woocommerce">
	<h1><?php esc_html_e( 'Export Products', 'woocommerce' ); ?></h1>

	<div class="woocommerce-exporter-wrapper">
		<form class="woocommerce-exporter">
			<header>
				<span class="spinner is-active"></span>
				<h2><?php esc_html_e( 'Export products to a CSV file', 'woocommerce' ); ?></h2>
				<p><?php esc_html_e( 'This tool allows you to generate and download a CSV file containing a list of all products.', 'woocommerce' ); ?></p>
			</header>
			<section>
				<table class="form-table woocommerce-exporter-options">
					<tbody>
						<tr>
							<th scope="row">
								<label for="woocommerce-exporter-columns"><?php esc_html_e( 'Which columns should be exported?', 'woocommerce' ); ?></label>
							</th>
							<td>
								<select id="woocommerce-exporter-columns" class="woocommerce-exporter-columns wc-enhanced-select" style="width:100%;" multiple data-placeholder="<?php esc_attr_e( 'Export all columns', 'woocommerce' ); ?>">
									<?php
									foreach ( $exporter->get_default_column_names() as $column_id => $column_name ) {
										echo '<option value="' . esc_attr( $column_id ) . '">' . esc_html( $column_name ) . '</option>';
									}
									?>
									<option value="downloads"><?php esc_html_e( 'Downloads', 'woocommerce' ); ?></option>
									<option value="attributes"><?php esc_html_e( 'Attributes', 'woocommerce' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="woocommerce-exporter-types"><?php esc_html_e( 'Which product types should be exported?', 'woocommerce' ); ?></label>
							</th>
							<td>
								<select id="woocommerce-exporter-types" class="woocommerce-exporter-types wc-enhanced-select" style="width:100%;" multiple data-placeholder="<?php esc_attr_e( 'Export all products', 'woocommerce' ); ?>">
									<?php
									foreach ( WC_Admin_Exporters::get_product_types() as $value => $label ) {
										echo '<option value="' . esc_attr( $value ) . '">' . esc_html( $label ) . '</option>';
									}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="woocommerce-exporter-category"><?php esc_html_e( 'Which product category should be exported?', 'woocommerce' ); ?></label>
							</th>
							<td>
								<select id="woocommerce-exporter-category" class="woocommerce-exporter-category wc-enhanced-select" style="width:100%;" multiple data-placeholder="<?php esc_attr_e( 'Export all categories', 'woocommerce' ); ?>">
								<?php
								$categories = get_categories(
									array(
										'taxonomy'   => 'product_cat',
										'hide_empty' => false,
									)
								);
								foreach ( $categories as $category ) {
									echo '<option value="' . esc_attr( $category->slug ) . '">' . esc_html( $category->name ) . '</option>';
								}
								?>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="woocommerce-exporter-meta"><?php esc_html_e( 'Export custom meta?', 'woocommerce' ); ?></label>
							</th>
							<td>
								<input type="checkbox" id="woocommerce-exporter-meta" value="1" />
								<label for="woocommerce-exporter-meta"><?php esc_html_e( 'Yes, export all custom meta', 'woocommerce' ); ?></label>
							</td>
						</tr>
						<?php do_action( 'woocommerce_product_export_row' ); ?>
					</tbody>
				</table>
				<progress class="woocommerce-exporter-progress" max="100" value="0"></progress>
			</section>
			<div class="wc-actions">
				<button type="submit" class="woocommerce-exporter-button button button-primary" value="<?php esc_attr_e( 'Generate CSV', 'woocommerce' ); ?>"><?php esc_html_e( 'Generate CSV', 'woocommerce' ); ?></button>
			</div>
		</form>
	</div>
</div>
