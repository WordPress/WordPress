/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import './style.scss';

interface TextareaProps {
	className?: string;
	disabled: boolean;
	onTextChange: ( newText: string ) => void;
	placeholder: string;
	value: string;
}

export const Textarea = ( {
	className = '',
	disabled = false,
	onTextChange,
	placeholder,
	value = '',
}: TextareaProps ): JSX.Element => (
	<textarea
		className={ classnames( 'wc-block-components-textarea', className ) }
		disabled={ disabled }
		onChange={ ( event ) => {
			onTextChange( event.target.value );
		} }
		placeholder={ placeholder }
		rows={ 2 }
		value={ value }
	/>
);
