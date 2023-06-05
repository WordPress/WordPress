/**
 * External dependencies
 */
import { Button } from '@wordpress/components';
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import './style.scss';

function TextToolbarButton( { className = '', ...props } ) {
	const classes = classnames( 'wc-block-text-toolbar-button', className );
	return <Button className={ classes } { ...props } />;
}

export default TextToolbarButton;
