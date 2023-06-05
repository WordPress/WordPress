/**
 * External dependencies
 */
import classnames from 'classnames';
import { useInstanceId } from '@wordpress/compose';
/**
 * Internal dependencies
 */
import RadioControlOption from './option';
import type { RadioControlProps } from './types';
import './style.scss';

const RadioControl = ( {
	className = '',
	id,
	selected = '',
	onChange,
	options = [],
}: RadioControlProps ): JSX.Element | null => {
	const instanceId = useInstanceId( RadioControl );
	const radioControlId = id || instanceId;

	if ( ! options.length ) {
		return null;
	}

	return (
		<div
			className={ classnames(
				'wc-block-components-radio-control',
				className
			) }
		>
			{ options.map( ( option ) => (
				<RadioControlOption
					key={ `${ radioControlId }-${ option.value }` }
					name={ `radio-control-${ radioControlId }` }
					checked={ option.value === selected }
					option={ option }
					onChange={ ( value: string ) => {
						onChange( value );
						if ( typeof option.onChange === 'function' ) {
							option.onChange( value );
						}
					} }
				/>
			) ) }
		</div>
	);
};

export default RadioControl;
export { default as RadioControlOption } from './option';
export { default as RadioControlOptionLayout } from './option-layout';
