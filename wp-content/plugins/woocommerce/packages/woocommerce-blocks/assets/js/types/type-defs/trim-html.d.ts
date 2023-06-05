interface Opts {
	/**
	 * Character limit
	 *
	 * @default 100
	 */
	limit: number;
	/**
	 * Link to full content
	 *
	 * @default ''
	 */
	moreLink: string;
	/**
	 * Text for the link to the full content
	 *
	 * @default '»'
	 */
	moreText: string;
	/**
	 * Whether to strip or preserve HTML tags
	 *
	 * @default true
	 */
	preserveTags: boolean;
	/**
	 * Whether to strip or preserve white space
	 *
	 * @default false
	 */
	preserveWhiteSpace: boolean;
	/**
	 * Suffix to add at the end of the trimmed text
	 */
	suffix: string;
	/**
	 * Break text if limit is reached within the boundaries of a word
	 *
	 * @default false
	 */
	wordBreak: boolean;
}

interface TrimResult {
	/**
	 * The trimmed HTML
	 */
	html: string;
	/**
	 * Whether the trimming was necessary
	 */
	more: boolean;
}

declare module 'trim-html' {
	export default function trimHtml(
		html: string,
		options: Partial< Opts >
	): TrimResult;
}
