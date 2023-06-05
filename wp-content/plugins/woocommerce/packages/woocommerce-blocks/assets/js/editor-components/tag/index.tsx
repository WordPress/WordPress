/**
 * External dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import classnames from 'classnames';
import { Button, Popover } from '@wordpress/components';
import { Icon, cancelCircleFilled } from '@wordpress/icons';
import { decodeEntities } from '@wordpress/html-entities';
import { useInstanceId } from '@wordpress/compose';
import { useState } from '@wordpress/element';

/**
 * This component can be used to show an item styled as a "tag", optionally with an `X` + "remove"
 * or with a popover that is shown on click.
 */
const Tag = ( {
	id,
	label,
	popoverContents,
	remove,
	screenReaderLabel,
	className = '',
}: {
	// Additional CSS classes.
	className?: string;
	// The ID for this item, used in the remove function.
	id: string | number;
	// The name for this item, displayed as the tag's text.
	label: string;
	// Contents to display on click in a popover
	popoverContents?: JSX.Element;
	// A function called when the remove X is clicked. If not used, no X icon will display.
	remove?: ( id: string | number ) => () => void;
	// A more descriptive label for screen reader users. Defaults to the `name` prop.
	screenReaderLabel?: string;
} ): JSX.Element | null => {
	const [ isVisible, setIsVisible ] = useState( false );
	const instanceId = useInstanceId( Tag );
	screenReaderLabel = screenReaderLabel || label;
	if ( ! label ) {
		// A null label probably means something went wrong
		return null;
	}
	label = decodeEntities( label );
	const classes = classnames( 'woocommerce-tag', className, {
		'has-remove': !! remove,
	} );
	const labelId = `woocommerce-tag__label-${ instanceId }`;
	const labelTextNode = (
		<>
			<span className="screen-reader-text">{ screenReaderLabel }</span>
			<span aria-hidden="true">{ label }</span>
		</>
	);

	return (
		<span className={ classes }>
			{ popoverContents ? (
				<Button
					className="woocommerce-tag__text"
					id={ labelId }
					onClick={ () => setIsVisible( true ) }
				>
					{ labelTextNode }
				</Button>
			) : (
				<span className="woocommerce-tag__text" id={ labelId }>
					{ labelTextNode }
				</span>
			) }
			{ popoverContents && isVisible && (
				<Popover onClose={ () => setIsVisible( false ) }>
					{ popoverContents }
				</Popover>
			) }
			{ remove && (
				<Button
					className="woocommerce-tag__remove"
					onClick={ remove( id ) }
					label={ sprintf(
						// Translators: %s label.
						__( 'Remove %s', 'woo-gutenberg-products-block' ),
						label
					) }
					aria-describedby={ labelId }
				>
					<Icon
						icon={ cancelCircleFilled }
						size={ 20 }
						className="clear-icon"
					/>
				</Button>
			) }
		</span>
	);
};

export default Tag;
