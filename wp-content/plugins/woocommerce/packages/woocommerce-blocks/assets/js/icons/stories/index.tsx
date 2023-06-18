/**
 * External dependencies
 */
import type { Story } from '@storybook/react';
import { useState } from '@wordpress/element';
import { Icon } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import * as icons from '../index';
const { ...availableIcons } = icons;

export const Library: Story = ( args ) => {
	const [ filter, setFilter ] = useState( '' );

	const filteredIcons = Object.entries( availableIcons ).reduce(
		( acc: Record< string, unknown >, [ name, icon ] ) => {
			if ( name.includes( filter ) ) {
				acc[ name ] = icon;
			}
			return acc;
		},
		{} as Partial< typeof availableIcons >
	);

	return (
		<div style={ { padding: '20px' } }>
			<label htmlFor="filter-icons" style={ { paddingRight: '30px' } }>
				Filter Icons
			</label>
			<input
				id="filter-icons"
				type="search"
				value={ filter }
				placeholder="Icon name"
				onChange={ ( event ) => setFilter( event.target.value ) }
			/>
			<div
				style={ {
					display: 'flex',
					alignItems: 'bottom',
					flexWrap: 'wrap',
				} }
			>
				{ Object.entries( filteredIcons ).map( ( [ name, icon ] ) => {
					return (
						<div
							key={ name }
							style={ {
								display: 'flex',
								flexDirection: 'column',
								width: '25%',
								padding: '25px 0 25px 0',
							} }
						>
							<strong
								style={ {
									width: '200px',
								} }
							>
								{ name }
							</strong>
							<div
								style={ {
									display: 'flex',
									alignItems: 'center',
								} }
							>
								<Icon
									className={ args.className }
									icon={ icon }
								/>
								<Icon
									className={ args.className }
									style={ { paddingLeft: '10px' } }
									icon={ icon }
									size={ 36 }
								/>
								<Icon
									className={ args.className }
									style={ { paddingLeft: '10px' } }
									icon={ icon }
									size={ 48 }
								/>
							</div>
						</div>
					);
				} ) }
			</div>
		</div>
	);
};
Library.parameters = {
	controls: { include: [], hideNoControlsWarning: true },
};
Library.storyName = 'Icon Library';
