/**
 * External dependencies
 */
import { count } from '@wordpress/wordcount';
import { autop } from '@wordpress/autop';

/**
 * Generates the summary text from a string of text.
 *
 * @param {string} source    Source text.
 * @param {number} maxLength Limit number of countType returned if text has multiple paragraphs.
 * @param {string} countType What is being counted. One of words, characters_excluding_spaces, or characters_including_spaces.
 * @return {string} Generated summary.
 */
export const generateSummary = (
	source,
	maxLength = 15,
	countType = 'words'
) => {
	const sourceWithParagraphs = autop( source );
	const sourceWordCount = count( sourceWithParagraphs, countType );

	if ( sourceWordCount <= maxLength ) {
		return sourceWithParagraphs;
	}

	const firstParagraph = getFirstParagraph( sourceWithParagraphs );
	const firstParagraphWordCount = count( firstParagraph, countType );

	if ( firstParagraphWordCount <= maxLength ) {
		return firstParagraph;
	}

	if ( countType === 'words' ) {
		return trimWords( firstParagraph, maxLength );
	}

	return trimCharacters(
		firstParagraph,
		maxLength,
		countType === 'characters_including_spaces'
	);
};

/**
 * Get first paragraph from some HTML text, or return whole string.
 *
 * @param {string} source Source text.
 * @return {string} First paragraph found in string.
 */
const getFirstParagraph = ( source ) => {
	const pIndex = source.indexOf( '</p>' );

	if ( pIndex === -1 ) {
		return source;
	}

	return source.substr( 0, pIndex + 4 );
};

/**
 * Remove HTML tags from a string.
 *
 * @param {string} htmlString String to remove tags from.
 * @return {string} Plain text string.
 */
const removeTags = ( htmlString ) => {
	const tagsRegExp = /<\/?[a-z][^>]*?>/gi;
	return htmlString.replace( tagsRegExp, '' );
};

/**
 * Remove trailing punctuation and append some characters to a string.
 *
 * @param {string} text     Text to append to.
 * @param {string} moreText Text to append.
 * @return {string} String with appended characters.
 */
const appendMoreText = ( text, moreText ) => {
	return text.replace( /[\s|\.\,]+$/i, '' ) + moreText;
};

/**
 * Limit words in string and returned trimmed version.
 *
 * @param {string} text      Text to trim.
 * @param {number} maxLength Number of countType to limit to.
 * @param {string} moreText  Appended to the trimmed string.
 * @return {string} Trimmed string.
 */
const trimWords = ( text, maxLength, moreText = '&hellip;' ) => {
	const textToTrim = removeTags( text );
	const trimmedText = textToTrim
		.split( ' ' )
		.splice( 0, maxLength )
		.join( ' ' );

	return autop( appendMoreText( trimmedText, moreText ) );
};

/**
 * Limit characters in string and returned trimmed version.
 *
 * @param {string}  text          Text to trim.
 * @param {number}  maxLength     Number of countType to limit to.
 * @param {boolean} includeSpaces Should spaces be included in the count.
 * @param {string}  moreText      Appended to the trimmed string.
 * @return {string} Trimmed string.
 */
const trimCharacters = (
	text,
	maxLength,
	includeSpaces = true,
	moreText = '&hellip;'
) => {
	const textToTrim = removeTags( text );
	const trimmedText = textToTrim.slice( 0, maxLength );

	if ( includeSpaces ) {
		return autop( appendMoreText( trimmedText, moreText ) );
	}

	const matchSpaces = trimmedText.match( /([\s]+)/g );
	const spaceCount = matchSpaces ? matchSpaces.length : 0;
	const trimmedTextExcludingSpaces = textToTrim.slice(
		0,
		maxLength + spaceCount
	);

	return autop( appendMoreText( trimmedTextExcludingSpaces, moreText ) );
};
