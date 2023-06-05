/**
 * External dependencies
 */
import { Component } from '@wordpress/element';
import { createHigherOrderComponent } from '@wordpress/compose';
import { getCategories } from '@woocommerce/editor-components/utils';

/**
 * Internal dependencies
 */
import { formatError } from '../base/utils/errors.js';

/**
 * HOC that queries categories for a component.
 *
 * @param {Function} OriginalComponent Component being wrapped.
 */
const withCategories = createHigherOrderComponent( ( OriginalComponent ) => {
	return class WrappedComponent extends Component {
		constructor() {
			super( ...arguments );
			this.state = {
				error: null,
				loading: false,
				categories: [],
			};
			this.loadCategories = this.loadCategories.bind( this );
		}

		componentDidMount() {
			this.loadCategories();
		}

		loadCategories() {
			this.setState( { loading: true } );

			getCategories()
				.then( ( categories ) => {
					this.setState( {
						categories,
						loading: false,
						error: null,
					} );
				} )
				.catch( async ( e ) => {
					const error = await formatError( e );

					this.setState( {
						categories: [],
						loading: false,
						error,
					} );
				} );
		}

		render() {
			const { error, loading, categories } = this.state;

			return (
				<OriginalComponent
					{ ...this.props }
					error={ error }
					isLoading={ loading }
					categories={ categories }
				/>
			);
		}
	};
}, 'withCategories' );

export default withCategories;
