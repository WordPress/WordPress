/**
 * External dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import { BlockAttributes } from './types';

type Props = {
	attributes: BlockAttributes;
};

const Save = ( { attributes }: Props ): JSX.Element | null => {
	if ( attributes.isDescendentOfQueryLoop ) {
		return null;
	}

	return (
		<div
			{ ...useBlockProps.save( {
				className: classnames( 'is-loading', attributes.className, {
					[ `has-custom-width wp-block-button__width-${ attributes.width }` ]:
						attributes.width,
				} ),
			} ) }
		/>
	);
};

export default Save;
