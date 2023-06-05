/**
 * External dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';
import { Disabled } from '@wordpress/components';
import type { BlockEditProps } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import Block from './block';
import { Attributes } from './types';

const Edit = ( { attributes }: BlockEditProps< Attributes > ) => {
	const { className } = attributes;
	const blockProps = useBlockProps( {
		className,
	} );

	return (
		<>
			<div { ...blockProps }>
				<Disabled>
					<Block />
				</Disabled>
			</div>
		</>
	);
};

export default Edit;
