/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { Modal } from 'wordpress-components';
import { useDebounce } from 'use-debounce';
import classNames from 'classnames';

/**
 * Internal dependencies
 */
import './style.scss';

interface DrawerProps {
	children: JSX.Element;
	className?: string;
	isOpen: boolean;
	onClose: () => void;
	slideIn?: boolean;
	slideOut?: boolean;
	title: string;
}

const Drawer = ( {
	children,
	className,
	isOpen,
	onClose,
	slideIn = true,
	slideOut = true,
	title,
}: DrawerProps ): JSX.Element | null => {
	const [ debouncedIsOpen ] = useDebounce< boolean >( isOpen, 300 );
	const isClosing = ! isOpen && debouncedIsOpen;

	if ( ! isOpen && ! isClosing ) {
		return null;
	}

	return (
		<Modal
			title={ title }
			focusOnMount={ true }
			onRequestClose={ onClose }
			className={ classNames( className, 'wc-block-components-drawer' ) }
			overlayClassName={ classNames(
				'wc-block-components-drawer__screen-overlay',
				{
					'wc-block-components-drawer__screen-overlay--is-hidden':
						! isOpen,
					'wc-block-components-drawer__screen-overlay--with-slide-in':
						slideIn,
					'wc-block-components-drawer__screen-overlay--with-slide-out':
						slideOut,
				}
			) }
			closeButtonLabel={ __( 'Close', 'woo-gutenberg-products-block' ) }
		>
			{ children }
		</Modal>
	);
};

export default Drawer;
