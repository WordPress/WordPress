/**
 * External dependencies
 */

import Rating from '@woocommerce/base-components/product-rating';

export const previewOptions = [
	{
		label: (
			<Rating
				className={ '' }
				key={ 5 }
				rating={ 5 }
				ratedProductsCount={ 5 }
			/>
		),
		value: '5',
	},
	{
		label: (
			<Rating
				className={ '' }
				key={ 4 }
				rating={ 4 }
				ratedProductsCount={ 4 }
			/>
		),
		value: '4',
	},
	{
		label: (
			<Rating
				className={ '' }
				key={ 3 }
				rating={ 3 }
				ratedProductsCount={ 3 }
			/>
		),
		value: '3',
	},
	{
		label: (
			<Rating
				className={ '' }
				key={ 2 }
				rating={ 2 }
				ratedProductsCount={ 2 }
			/>
		),
		value: '2',
	},
	{
		label: (
			<Rating
				className={ '' }
				key={ 1 }
				rating={ 1 }
				ratedProductsCount={ 1 }
			/>
		),
		value: '1',
	},
];
