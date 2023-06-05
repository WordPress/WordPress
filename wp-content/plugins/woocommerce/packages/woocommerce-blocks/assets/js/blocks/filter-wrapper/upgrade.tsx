/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { createBlock } from '@wordpress/blocks';
import type { BlockInstance } from '@wordpress/blocks';
import { useDispatch, useSelect } from '@wordpress/data';
import { Button } from '@wordpress/components';
import { Warning } from '@wordpress/block-editor';

interface UpgradeNoticeProps {
	clientId: string;
	setAttributes: ( attributes: Record< string, unknown > ) => void;
	attributes: Record< string, unknown >;
	filterType: undefined | string;
}

export const UpgradeNotice = ( {
	clientId,
	setAttributes,
	filterType,
	attributes,
}: UpgradeNoticeProps ) => {
	const { replaceBlock } = useDispatch( 'core/block-editor' );
	const { heading, headingLevel } = attributes;
	const isInsideFilterWrapper = useSelect(
		( select ) => {
			const { getBlockParentsByBlockName } =
				select( 'core/block-editor' );
			return (
				getBlockParentsByBlockName(
					clientId,
					'woocommerce/filter-wrapper'
				).length > 0
			);
		},
		[ clientId ]
	);

	const upgradeFilterBlockHandler = () => {
		const filterWrapperInnerBlocks: BlockInstance[] = [
			createBlock( `woocommerce/${ filterType }`, {
				...attributes,
				heading: '',
			} ),
		];

		if ( heading && heading !== '' ) {
			filterWrapperInnerBlocks.unshift(
				createBlock( 'core/heading', {
					content: heading,
					level: headingLevel ?? 2,
				} )
			);
		}

		replaceBlock(
			clientId,
			createBlock(
				'woocommerce/filter-wrapper',
				{
					heading,
					filterType,
				},
				[ ...filterWrapperInnerBlocks ]
			)
		);
		setAttributes( {
			heading: '',
			lock: {
				remove: true,
			},
		} );
	};

	if ( isInsideFilterWrapper || ! filterType ) {
		return null;
	}

	const actions = [
		<Button
			key="convert"
			onClick={ upgradeFilterBlockHandler }
			variant="primary"
		>
			{ __( 'Upgrade block', 'woo-gutenberg-products-block' ) }
		</Button>,
	];

	return (
		<Warning actions={ actions }>
			{ __(
				'Filter block: We have improved this block to make styling easier. Upgrade it using the button below.',
				'woo-gutenberg-products-block'
			) }
		</Warning>
	);
};
