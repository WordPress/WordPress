/**
 * External dependencies
 */
import trimHtml from 'trim-html';

type Markers = {
	end: number;
	middle: number;
	start: number;
};

/**
 * Truncate some HTML content to a given length.
 *
 * @param {string} html     HTML that will be truncated.
 * @param {number} length   Length to truncate the string to.
 * @param {string} ellipsis Character to append to truncated content.
 */
export const truncateHtml = (
	html: string,
	length: number,
	ellipsis = '...'
): string => {
	const trimmed = trimHtml( html, {
		suffix: ellipsis,
		limit: length,
	} );

	return trimmed.html;
};

/**
 * Move string markers. Used by calculateLength.
 *
 * @param {Markers} markers       Markers for clamped content.
 * @param {number}  currentHeight Current height of clamped content.
 * @param {number}  maxHeight     Max height of the clamped content.
 */
const moveMarkers = (
	markers: Markers,
	currentHeight: number,
	maxHeight: number
): Markers => {
	if ( currentHeight <= maxHeight ) {
		markers.start = markers.middle + 1;
	} else {
		markers.end = markers.middle - 1;
	}

	return markers;
};

/**
 * Calculate how long the content can be based on the maximum number of lines allowed, and client height.
 *
 * @param {string}      originalContent Content to be clamped.
 * @param {HTMLElement} targetElement   Element which will contain the clamped content.
 * @param {number}      maxHeight       Max height of the clamped content.
 */
const calculateLength = (
	originalContent: string,
	targetElement: HTMLElement,
	maxHeight: number
): number => {
	let markers: Markers = {
		start: 0,
		middle: 0,
		end: originalContent.length,
	};

	while ( markers.start <= markers.end ) {
		markers.middle = Math.floor( ( markers.start + markers.end ) / 2 );

		// We set the innerHTML directly in the DOM here so we can reliably check the clientHeight later in moveMarkers.
		targetElement.innerHTML = truncateHtml(
			originalContent,
			markers.middle
		);

		markers = moveMarkers( markers, targetElement.clientHeight, maxHeight );
	}

	return markers.middle;
};

/**
 * Clamp lines calculates the height of a line of text and then limits it to the
 * value of the lines prop. Content is updated once limited.
 *
 * @param {string}      originalContent Content to be clamped.
 * @param {HTMLElement} targetElement   Element which will contain the clamped content.
 * @param {number}      maxHeight       Max height of the clamped content.
 * @param {string}      ellipsis        Character to append to clamped content.
 * @return {string} clamped content
 */
export const clampLines = (
	originalContent: string,
	targetElement: HTMLElement,
	maxHeight: number,
	ellipsis: string
): string => {
	const length = calculateLength( originalContent, targetElement, maxHeight );

	return truncateHtml( originalContent, length - ellipsis.length, ellipsis );
};
