/**
 * External dependencies
 */
import { useColorProps } from '@woocommerce/base-hooks';
import { isString } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import Block from './block';
import { parseAttributes } from './utils';

const BlockWrapper = ( props: Record< string, unknown > ) => {
	const colorProps = useColorProps( props );

	return (
		<div
			className={ isString( props.className ) ? props.className : '' }
			style={ { ...colorProps.style } }
		>
			<Block isEditor={ false } attributes={ parseAttributes( props ) } />
		</div>
	);
};

export default BlockWrapper;
