/**
 * External dependencies
 */
import { useState } from '@wordpress/element';
import classNames from 'classnames';
import { Icon, chevronUp, chevronDown } from '@wordpress/icons';
import type { ReactNode, ReactElement } from 'react';

/**
 * Internal dependencies
 */
import './style.scss';

interface PanelProps {
	children?: ReactNode;
	className?: string;
	initialOpen?: boolean;
	hasBorder?: boolean;
	title?: ReactNode;
	titleTag?: keyof JSX.IntrinsicElements;
}

const Panel = ( {
	children,
	className,
	initialOpen = false,
	hasBorder = false,
	title,
	titleTag: TitleTag = 'div',
}: PanelProps ): ReactElement => {
	const [ isOpen, setIsOpen ] = useState< boolean >( initialOpen );

	return (
		<div
			className={ classNames( className, 'wc-block-components-panel', {
				'has-border': hasBorder,
			} ) }
		>
			<TitleTag>
				<button
					aria-expanded={ isOpen }
					className="wc-block-components-panel__button"
					onClick={ () => setIsOpen( ! isOpen ) }
				>
					<Icon
						aria-hidden="true"
						className="wc-block-components-panel__button-icon"
						icon={ isOpen ? chevronUp : chevronDown }
					/>
					{ title }
				</button>
			</TitleTag>
			{ isOpen && (
				<div className="wc-block-components-panel__content">
					{ children }
				</div>
			) }
		</div>
	);
};

export default Panel;
