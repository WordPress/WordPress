/**
 * External dependencies
 */
import ErrorPlaceholder, {
	ErrorObject,
} from '@woocommerce/editor-components/error-placeholder';
import type { Block } from '@wordpress/blocks';
import type { ComponentType } from 'react';

/**
 * Internal dependencies
 */
import { BLOCK_NAMES } from './constants';
import { getClassPrefixFromName } from './utils';

interface APIErrorRequiredProps {
	error: ErrorObject;
	isLoading: boolean;
	name: string;
}

interface APIErrorProductProps extends APIErrorRequiredProps {
	getCategory: never;
	getProduct(): void;
}

interface APIErrorCategoryProps extends APIErrorRequiredProps {
	getCategory(): void;
	getProduct: never;
}

type APIErrorProps< T extends Block > =
	| ( T & APIErrorProductProps )
	| ( T & APIErrorCategoryProps );

export const withApiError =
	< T extends Block >( Component: ComponentType< T > ) =>
	( props: APIErrorProps< T > ) => {
		const { error, isLoading, name } = props;

		const className = getClassPrefixFromName( name );
		const onRetry =
			name === BLOCK_NAMES.featuredCategory
				? props.getCategory
				: props.getProduct;

		if ( error ) {
			return (
				<ErrorPlaceholder
					className={ `${ className }-error` }
					error={ error }
					isLoading={ isLoading }
					onRetry={ onRetry }
				/>
			);
		}

		return <Component { ...props } />;
	};
