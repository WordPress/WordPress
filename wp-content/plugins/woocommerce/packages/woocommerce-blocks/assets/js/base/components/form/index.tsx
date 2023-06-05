/**
 * External dependencies
 */
import classnames from 'classnames';
import type { FormEvent } from 'react';

interface FormProps {
	className: string;
	children?: React.ReactChildren;
	onSubmit: ( event: FormEvent ) => void;
}

const Form = ( {
	className,
	children,
	onSubmit = ( event ) => void event,
}: FormProps ): JSX.Element => {
	const formOnSubmit = ( event: FormEvent ) => {
		event.preventDefault();
		onSubmit( event );
	};

	return (
		<form
			className={ classnames( 'wc-block-components-form', className ) }
			onSubmit={ formOnSubmit }
		>
			{ children }
		</form>
	);
};

export default Form;
