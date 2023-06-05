/**
 * External dependencies
 */
import { Component } from '@wordpress/element';
import { createHigherOrderComponent } from '@wordpress/compose';
import { getProduct } from '@woocommerce/editor-components/utils';

/**
 * Internal dependencies
 */
import { formatError } from '../base/utils/errors.js';

/**
 * HOC that queries a product for a component.
 *
 * @param {Function} OriginalComponent Component being wrapped.
 */
const withProduct = createHigherOrderComponent( ( OriginalComponent ) => {
	return class WrappedComponent extends Component {
		state = {
			error: null,
			loading: false,
			product:
				this.props.attributes.productId === 'preview'
					? this.props.attributes.previewProduct
					: null,
		};

		componentDidMount() {
			this.loadProduct();
		}

		componentDidUpdate( prevProps ) {
			if (
				prevProps.attributes.productId !==
				this.props.attributes.productId
			) {
				this.loadProduct();
			}
		}

		loadProduct = () => {
			const { productId } = this.props.attributes;

			if ( productId === 'preview' ) {
				return;
			}

			if ( ! productId ) {
				this.setState( { product: null, loading: false, error: null } );
				return;
			}

			this.setState( { loading: true } );

			getProduct( productId )
				.then( ( product ) => {
					this.setState( { product, loading: false, error: null } );
				} )
				.catch( async ( e ) => {
					const error = await formatError( e );

					this.setState( { product: null, loading: false, error } );
				} );
		};

		render() {
			const { error, loading, product } = this.state;

			return (
				<OriginalComponent
					{ ...this.props }
					error={ error }
					getProduct={ this.loadProduct }
					isLoading={ loading }
					product={ product }
				/>
			);
		}
	};
}, 'withProduct' );

export default withProduct;
