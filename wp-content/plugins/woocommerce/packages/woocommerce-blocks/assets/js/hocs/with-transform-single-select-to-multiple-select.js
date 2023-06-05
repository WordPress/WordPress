/**
 * External dependencies
 */
import { Component } from '@wordpress/element';
import { createHigherOrderComponent } from '@wordpress/compose';
import PropTypes from 'prop-types';

/**
 * HOC that transforms a single select to a multiple select.
 *
 * @param {Function} OriginalComponent Component being wrapped.
 */
const withTransformSingleSelectToMultipleSelect = createHigherOrderComponent(
	( OriginalComponent ) => {
		class WrappedComponent extends Component {
			render() {
				const { selected } = this.props;
				const isNil = selected === null || selected === undefined;

				return Array.isArray( selected ) ? (
					<OriginalComponent { ...this.props } />
				) : (
					<OriginalComponent
						{ ...this.props }
						selected={ isNil ? [] : [ selected ] }
					/>
				);
			}
		}
		WrappedComponent.propTypes = {
			selected: PropTypes.oneOfType( [
				PropTypes.number,
				PropTypes.string,
			] ),
		};
		WrappedComponent.defaultProps = {
			selected: null,
		};
		return WrappedComponent;
	},
	'withTransformSingleSelectToMultipleSelect'
);

export default withTransformSingleSelectToMultipleSelect;
