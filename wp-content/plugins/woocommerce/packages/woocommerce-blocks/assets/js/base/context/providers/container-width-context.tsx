/**
 * External dependencies
 */
import { createContext, useContext } from '@wordpress/element';
import { useContainerQueries } from '@woocommerce/base-hooks';
import classNames from 'classnames';

export type ContainerWidthContextProps = {
	hasContainerWidth: boolean;
	containerClassName: string;
	isMobile: boolean;
	isSmall: boolean;
	isMedium: boolean;
	isLarge: boolean;
};

const ContainerWidthContext: React.Context< ContainerWidthContextProps > =
	createContext< ContainerWidthContextProps >( {
		hasContainerWidth: false,
		containerClassName: '',
		isMobile: false,
		isSmall: false,
		isMedium: false,
		isLarge: false,
	} );

export const useContainerWidthContext = (): ContainerWidthContextProps => {
	return useContext( ContainerWidthContext );
};

interface ContainerWidthContextProviderProps {
	children: JSX.Element | JSX.Element[];
	className: string;
}

/**
 * Provides an interface to useContainerQueries so children can see what size is being used by the
 * container.
 */
export const ContainerWidthContextProvider = ( {
	children,
	className = '',
}: ContainerWidthContextProviderProps ): JSX.Element => {
	const [ resizeListener, containerClassName ] = useContainerQueries();

	const contextValue = {
		hasContainerWidth: containerClassName !== '',
		containerClassName,
		isMobile: containerClassName === 'is-mobile',
		isSmall: containerClassName === 'is-small',
		isMedium: containerClassName === 'is-medium',
		isLarge: containerClassName === 'is-large',
	};

	return (
		<ContainerWidthContext.Provider value={ contextValue }>
			<div className={ classNames( className, containerClassName ) }>
				{ resizeListener }
				{ children }
			</div>
		</ContainerWidthContext.Provider>
	);
};
