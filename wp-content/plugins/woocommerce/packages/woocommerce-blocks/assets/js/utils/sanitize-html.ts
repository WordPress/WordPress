/**
 * External dependencies
 */
import DOMPurify from 'dompurify';

const ALLOWED_TAGS = [ 'a', 'b', 'em', 'i', 'strong', 'p', 'br' ];
const ALLOWED_ATTR = [ 'target', 'href', 'rel', 'name', 'download' ];

export const sanitizeHTML = (
	html: string,
	config?: { tags?: typeof ALLOWED_TAGS; attr?: typeof ALLOWED_ATTR }
) => {
	const tagsValue = config?.tags || ALLOWED_TAGS;
	const attrValue = config?.attr || ALLOWED_ATTR;

	return DOMPurify.sanitize( html, {
		ALLOWED_TAGS: tagsValue,
		ALLOWED_ATTR: attrValue,
	} );
};
