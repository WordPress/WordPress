/**
 * External dependencies
 */
import { useEffect, useRef } from '@wordpress/element';
import Button, { ButtonProps } from '@woocommerce/base-components/button';
import { RichText } from '@wordpress/block-editor';
import type { RefObject } from 'react';

export interface EditableButtonProps
	extends Omit< ButtonProps, 'onChange' | 'placeholder' | 'value' > {
	/**
	 * On change callback.
	 */
	onChange: ( value: string ) => void;
	/**
	 * The placeholder of the editable button.
	 */
	placeholder?: string;
	/**
	 * The current value of the editable button.
	 */
	value: string;
}

const EditableButton = ( {
	onChange,
	placeholder,
	value,
	...props
}: EditableButtonProps ) => {
	const button: RefObject< HTMLButtonElement > = useRef( null );

	// Fix a bug in Firefox that didn't allow to type spaces in editable buttons.
	// @see https://github.com/woocommerce/woocommerce-blocks/issues/8734
	useEffect( () => {
		const buttonEl = button?.current;

		if ( ! buttonEl ) {
			return;
		}

		const onKeyDown = ( event: KeyboardEvent ) => {
			// If the user typed something different than space, do nothing.
			if ( event.code !== 'Space' ) {
				return;
			}
			event.preventDefault();
			const selection = buttonEl.ownerDocument.getSelection();
			if ( selection && selection.rangeCount > 0 ) {
				// Get the caret position and insert a space.
				const range = selection.getRangeAt( 0 );
				range.deleteContents();
				const textNode = document.createTextNode( ' ' );
				range.insertNode( textNode );
				// Set the caret position after the space.
				range.setStartAfter( textNode );
				range.setEndAfter( textNode );
				selection.removeAllRanges();
				selection.addRange( range );
			}
		};

		buttonEl.addEventListener( 'keydown', onKeyDown );

		return () => {
			if ( ! buttonEl ) {
				return;
			}
			buttonEl.removeEventListener( 'keydown', onKeyDown );
		};
	}, [ onChange, value ] );

	return (
		<Button { ...props }>
			<span ref={ button }>
				<RichText
					multiline={ false }
					allowedFormats={ [] }
					value={ value }
					placeholder={ placeholder }
					onChange={ onChange }
				/>
			</span>
		</Button>
	);
};

export default EditableButton;
