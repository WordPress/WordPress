/**
 * External dependencies
 */
import classnames from 'classnames';
import { forwardRef, useState } from '@wordpress/element';
import { decodeEntities } from '@wordpress/html-entities';
import type { InputHTMLAttributes } from 'react';

/**
 * Internal dependencies
 */
import Label from '../label';
import './style.scss';

interface TextInputProps
	extends Omit<
		InputHTMLAttributes< HTMLInputElement >,
		'onChange' | 'onBlur'
	> {
	id: string;
	ariaLabel?: string;
	label?: string | undefined;
	ariaDescribedBy?: string | undefined;
	screenReaderLabel?: string;
	help?: string;
	feedback?: boolean | JSX.Element;
	autoComplete?: string | undefined;
	onChange: ( newValue: string ) => void;
	onBlur?: ( newValue: string ) => void;
}

const TextInput = forwardRef< HTMLInputElement, TextInputProps >(
	(
		{
			className,
			id,
			type = 'text',
			ariaLabel,
			ariaDescribedBy,
			label,
			screenReaderLabel,
			disabled,
			help,
			autoCapitalize = 'off',
			autoComplete = 'off',
			value = '',
			onChange,
			required = false,
			onBlur = () => {
				/* Do nothing */
			},
			feedback,
			...rest
		},
		ref
	) => {
		const [ isActive, setIsActive ] = useState( false );

		return (
			<div
				className={ classnames(
					'wc-block-components-text-input',
					className,
					{
						'is-active': isActive || value,
					}
				) }
			>
				<input
					type={ type }
					id={ id }
					value={ decodeEntities( value ) }
					ref={ ref }
					autoCapitalize={ autoCapitalize }
					autoComplete={ autoComplete }
					onChange={ ( event ) => {
						onChange( event.target.value );
					} }
					onFocus={ () => setIsActive( true ) }
					onBlur={ ( event ) => {
						onBlur( event.target.value );
						setIsActive( false );
					} }
					aria-label={ ariaLabel || label }
					disabled={ disabled }
					aria-describedby={
						!! help && ! ariaDescribedBy
							? id + '__help'
							: ariaDescribedBy
					}
					required={ required }
					{ ...rest }
				/>
				<Label
					label={ label }
					screenReaderLabel={ screenReaderLabel || label }
					wrapperElement="label"
					wrapperProps={ {
						htmlFor: id,
					} }
					htmlFor={ id }
				/>
				{ !! help && (
					<p
						id={ id + '__help' }
						className="wc-block-components-text-input__help"
					>
						{ help }
					</p>
				) }
				{ feedback }
			</div>
		);
	}
);

export default TextInput;
