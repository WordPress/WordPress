/**
 * External dependencies
 */
import { range } from 'lodash';
import { __, sprintf } from '@wordpress/i18n';
import { Component } from '@wordpress/element';
import { ToolbarGroup } from '@wordpress/components';

/**
 * Internal dependencies
 */
import HeadingLevelIcon from './heading-level-icon';

/**
 * HeadingToolbar component.
 *
 * Allows the heading level to be chosen for a title block.
 */
class HeadingToolbar extends Component {
	createLevelControl( targetLevel, selectedLevel, onChange ) {
		const isActive = targetLevel === selectedLevel;
		return {
			icon: <HeadingLevelIcon level={ targetLevel } />,
			title: sprintf(
				/* translators: %s: heading level e.g: "2", "3", "4" */
				__( 'Heading %d', 'woocommerce' ),
				targetLevel
			),
			isActive,
			onClick: () => onChange( targetLevel ),
		};
	}

	render() {
		const {
			isCollapsed = true,
			minLevel,
			maxLevel,
			selectedLevel,
			onChange,
		} = this.props;

		return (
			<ToolbarGroup
				isCollapsed={ isCollapsed }
				icon={ <HeadingLevelIcon level={ selectedLevel } /> }
				controls={ range( minLevel, maxLevel ).map( ( index ) =>
					this.createLevelControl( index, selectedLevel, onChange )
				) }
			/>
		);
	}
}

export default HeadingToolbar;
