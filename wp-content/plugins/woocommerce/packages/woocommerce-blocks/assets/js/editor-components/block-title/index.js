/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import { PlainText } from '@wordpress/block-editor';
import { withInstanceId } from '@wordpress/compose';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import './editor.scss';

const BlockTitle = ( {
	className,
	headingLevel,
	onChange,
	heading,
	instanceId,
} ) => {
	const TagName = `h${ headingLevel }`;
	return (
		<TagName className={ className }>
			<label
				className="screen-reader-text"
				htmlFor={ `block-title-${ instanceId }` }
			>
				{ __( 'Block title', 'woocommerce' ) }
			</label>
			<PlainText
				id={ `block-title-${ instanceId }` }
				className="wc-block-editor-components-title"
				value={ heading }
				onChange={ onChange }
				style={ { backgroundColor: 'transparent' } }
			/>
		</TagName>
	);
};

BlockTitle.propTypes = {
	/**
	 * Classname to add to title in addition to the defaults.
	 */
	className: PropTypes.string,
	/**
	 * The value of the heading.
	 */
	value: PropTypes.string,
	/**
	 * Callback to update the attribute when text is changed.
	 */
	onChange: PropTypes.func,
	/**
	 * Level of the heading tag (1, 2, 3... will render <h1>, <h2>, <h3>... elements).
	 */
	headingLevel: PropTypes.number,
};

export default withInstanceId( BlockTitle );
