/**
 * External dependencies
 */
import { RawHTML, useMemo } from '@wordpress/element';
import { WordCountType } from '@woocommerce/block-settings';
import type { CSSProperties } from 'react';

/**
 * Internal dependencies
 */
import { generateSummary } from './utils';

interface SummaryProps {
	className?: string;
	source: string;
	maxLength?: number;
	countType?: WordCountType;
	style?: CSSProperties;
}
/**
 * Summary component.
 *
 * @param {Object}        props           Component props.
 * @param {string}        props.source    Source text.
 * @param {number}        props.maxLength Max length of the summary, using countType.
 * @param {string}        props.countType One of words, characters_excluding_spaces, or characters_including_spaces.
 * @param {string}        props.className Class name for rendered component.
 * @param {CSSProperties} props.style     Style Object for rendered component.
 *
 */
export const Summary = ( {
	source,
	maxLength = 15,
	countType = 'words',
	className = '',
	style = {},
}: SummaryProps ): JSX.Element => {
	const summaryText = useMemo( () => {
		return generateSummary( source, maxLength, countType );
	}, [ source, maxLength, countType ] );

	return (
		<RawHTML style={ style } className={ className }>
			{ summaryText }
		</RawHTML>
	);
};

export default Summary;
