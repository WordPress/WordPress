/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { BlockControls } from '@wordpress/block-editor';
import { ToolbarGroup } from '@wordpress/components';

interface EditorBlockControlsProps {
	isEditing: boolean;
	setIsEditing: ( isEditing: boolean ) => void;
}

const EditorBlockControls = ( {
	isEditing,
	setIsEditing,
}: EditorBlockControlsProps ) => {
	return (
		<BlockControls>
			<ToolbarGroup
				controls={ [
					{
						icon: 'edit',
						title: __(
							'Edit selected product',
							'woo-gutenberg-products-block'
						),
						onClick: () => setIsEditing( ! isEditing ),
						isActive: isEditing,
					},
				] }
			/>
		</BlockControls>
	);
};

export default EditorBlockControls;
