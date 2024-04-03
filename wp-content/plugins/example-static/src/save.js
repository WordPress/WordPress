import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const blockProps = useBlockProps.save();
	return (
		<RichText.Content 
			{ ...blockProps } 
			tagName="p" 
			value={ attributes.content } 
		/>
	);
}