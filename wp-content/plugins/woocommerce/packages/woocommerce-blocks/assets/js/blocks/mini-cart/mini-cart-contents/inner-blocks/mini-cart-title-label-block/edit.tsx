/**
 * External dependencies
 */
import { RichText, useBlockProps } from '@wordpress/block-editor';

interface Attributes {
	attributes: {
		label: string;
	};
	setAttributes: ( attributes: Record< string, unknown > ) => void;
}

export const Edit = ( {
	attributes: { label },
	setAttributes,
}: Attributes ): JSX.Element => {
	const blockProps = useBlockProps();

	return (
		<span { ...blockProps }>
			<RichText
				allowedFormats={ [] }
				value={ label }
				onChange={ ( newLabel ) =>
					setAttributes( { label: newLabel } )
				}
			/>
		</span>
	);
};

export const Save = (): JSX.Element => {
	return <div { ...useBlockProps.save() }></div>;
};
