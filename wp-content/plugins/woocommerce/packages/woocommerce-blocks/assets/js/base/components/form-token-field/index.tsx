/**
 * External dependencies
 */
import { FormTokenField as WPFormTokenField } from 'wordpress-components';
import classNames from 'classnames';

/**
 * Internal dependencies
 */
import './style.scss';

export interface Props {
	className?: string;
	disabled?: boolean;
	displayTransform?: ( value: string ) => string;
	label?: string;
	messages?: Record< string, string >;
	multiple?: boolean;
	onChange: ( value: string[] ) => void;
	placeholder?: string;
	saveTransform?: ( value: string ) => string;
	style?: React.CSSProperties;
	suggestions: string[];
	validateInput?: ( token: string ) => boolean;
	value: string[];
}

const FormTokenField = ( {
	className,
	style,
	suggestions,
	multiple = true,
	saveTransform = ( incompleteToken ) =>
		incompleteToken.trim().replace( /\s/g, '-' ),
	messages = {},
	validateInput = ( token: string ) => suggestions.includes( token ),
	label = '',
	...props
}: Props ) => {
	return (
		<div
			className={ classNames(
				'wc-blocks-components-form-token-field-wrapper',
				className,
				{
					'single-selection': ! multiple,
				}
			) }
			style={ style }
		>
			<WPFormTokenField
				label={ label }
				__experimentalExpandOnFocus={ true }
				__experimentalShowHowTo={ false }
				__experimentalValidateInput={ validateInput }
				saveTransform={ saveTransform }
				maxLength={ multiple ? undefined : 1 }
				suggestions={ suggestions }
				messages={ messages }
				{ ...props }
			/>
		</div>
	);
};

export default FormTokenField;
