/**
 * External dependencies
 */
import { Component } from '@wordpress/element';

class CheckoutSlotErrorBoundary extends Component {
	state = { errorMessage: '', hasError: false };

	static getDerivedStateFromError( error ) {
		if (
			typeof error.statusText !== 'undefined' &&
			typeof error.status !== 'undefined'
		) {
			return {
				errorMessage: (
					<>
						<strong>{ error.status }</strong>
						{ ': ' + error.statusText }
					</>
				),
				hasError: true,
			};
		}

		return { errorMessage: error.message, hasError: true };
	}

	render() {
		const { renderError } = this.props;
		const { errorMessage, hasError } = this.state;

		if ( hasError ) {
			if ( typeof renderError === 'function' ) {
				return renderError( errorMessage );
			}
			return <p>{ errorMessage }</p>;
		}

		return this.props.children;
	}
}

export default CheckoutSlotErrorBoundary;
