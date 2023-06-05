/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { ToggleControl, SelectControl } from '@wordpress/components';

export const getSharedContentControls = ( attributes, setAttributes ) => {
	const { contentVisibility } = attributes;
	return (
		<ToggleControl
			label={ __(
				'Show Sorting Dropdown',
				'woocommerce'
			) }
			checked={ contentVisibility.orderBy }
			onChange={ () =>
				setAttributes( {
					contentVisibility: {
						...contentVisibility,
						orderBy: ! contentVisibility.orderBy,
					},
				} )
			}
		/>
	);
};

export const getSharedListControls = ( attributes, setAttributes ) => {
	return (
		<SelectControl
			label={ __( 'Order Products By', 'woocommerce' ) }
			value={ attributes.orderby }
			options={ [
				{
					label: __(
						'Default sorting (menu order)',
						'woocommerce'
					),
					value: 'menu_order',
				},
				{
					label: __( 'Popularity', 'woocommerce' ),
					value: 'popularity',
				},
				{
					label: __(
						'Average rating',
						'woocommerce'
					),
					value: 'rating',
				},
				{
					label: __( 'Latest', 'woocommerce' ),
					value: 'date',
				},
				{
					label: __(
						'Price: low to high',
						'woocommerce'
					),
					value: 'price',
				},
				{
					label: __(
						'Price: high to low',
						'woocommerce'
					),
					value: 'price-desc',
				},
			] }
			onChange={ ( orderby ) => setAttributes( { orderby } ) }
		/>
	);
};
