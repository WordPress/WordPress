<?php
/**
 * HTML API: WP_HTML_Doctype_Info class
 *
 * @package WordPress
 * @subpackage HTML-API
 * @since 6.7.0
 */

/**
 * Core class used by the HTML API to represent a DOCTYPE declaration.
 *
 * This class parses DOCTYPE tokens for the full parser in the HTML Processor.
 * Most code interacting with HTML won't need to parse DOCTYPE declarations;
 * the HTML Processor is one exception. Consult the HTML Processor for proper
 * parsing of an HTML document.
 *
 * A DOCTYPE declaration may indicate its document compatibility mode, which impacts
 * the structure of the following HTML as well as the behavior of CSS class selectors.
 * There are three possible modes:
 *
 *  - "no-quirks" and "limited-quirks" modes (also called "standards mode").
 *  - "quirks" mode.
 *
 * These modes mostly determine whether CSS class name selectors match values in the
 * HTML `class` attribute in an ASCII-case-insensitive way (quirks mode), or whether
 * they match only when byte-for-byte identical (no-quirks mode).
 *
 * All HTML documents should start with the standard HTML5 DOCTYPE: `<!DOCTYPE html>`.
 *
 * > DOCTYPEs are required for legacy reasons. When omitted, browsers tend to use a different
 * > rendering mode that is incompatible with some specifications. Including the DOCTYPE in a
 * > document ensures that the browser makes a best-effort attempt at following the
 * > relevant specifications.
 *
 * @see https://html.spec.whatwg.org/#the-doctype
 *
 * DOCTYPE declarations comprise four properties: a name, public identifier, system identifier,
 * and an indication of which document compatibility mode they would imply if an HTML parser
 * hadn't already determined it from other information.
 *
 * @see https://html.spec.whatwg.org/#the-initial-insertion-mode
 *
 * Historically, the DOCTYPE declaration was used in SGML documents to instruct a parser how
 * to interpret the various tags and entities within a document. Its role in HTML diverged
 * from how it was used in SGML and no meaning should be back-read into HTML based on how it
 * is used in SGML, XML, or XHTML documents.
 *
 * @see https://www.iso.org/standard/16387.html
 *
 * @since 6.7.0
 *
 * @access private
 *
 * @see WP_HTML_Processor
 */
class WP_HTML_Doctype_Info {
	/**
	 * Name of the DOCTYPE: should be "html" for HTML documents.
	 *
	 * This value should be considered "read only" and not modified.
	 *
	 * Historically the DOCTYPE name indicates name of the document's root element.
	 *
	 *     <!DOCTYPE html>
	 *               ╰──┴── name is "html".
	 *
	 * @see https://html.spec.whatwg.org/#tokenization
	 *
	 * @since 6.7.0
	 *
	 * @var string|null
	 */
	public $name = null;

	/**
	 * Public identifier of the DOCTYPE.
	 *
	 * This value should be considered "read only" and not modified.
	 *
	 * The public identifier is optional and should not appear in HTML documents.
	 * A `null` value indicates that no public identifier was present in the DOCTYPE.
	 *
	 * Historically the presence of the public identifier indicated that a document
	 * was meant to be shared between computer systems and the value indicated to a
	 * knowledgeable parser how to find the relevant document type definition (DTD).
	 *
	 *     <!DOCTYPE html PUBLIC "public id goes here in quotes">
	 *               │  │         ╰─── public identifier ─────╯
	 *               ╰──┴── name is "html".
	 *
	 * @see https://html.spec.whatwg.org/#tokenization
	 *
	 * @since 6.7.0
	 *
	 * @var string|null
	 */
	public $public_identifier = null;

	/**
	 * System identifier of the DOCTYPE.
	 *
	 * This value should be considered "read only" and not modified.
	 *
	 * The system identifier is optional and should not appear in HTML documents.
	 * A `null` value indicates that no system identifier was present in the DOCTYPE.
	 *
	 * Historically the system identifier specified where a relevant document type
	 * declaration for the given document is stored and may be retrieved.
	 *
	 *     <!DOCTYPE html SYSTEM "system id goes here in quotes">
	 *               │  │         ╰──── system identifier ────╯
	 *               ╰──┴── name is "html".
	 *
	 * If a public identifier were provided it would indicate to a knowledgeable
	 * parser how to interpret the system identifier.
	 *
	 *     <!DOCTYPE html PUBLIC "public id goes here in quotes" "system id goes here in quotes">
	 *               │  │         ╰─── public identifier ─────╯   ╰──── system identifier ────╯
	 *               ╰──┴── name is "html".
	 *
	 * @see https://html.spec.whatwg.org/#tokenization
	 *
	 * @since 6.7.0
	 *
	 * @var string|null
	 */
	public $system_identifier = null;

	/**
	 * Which document compatibility mode this DOCTYPE declaration indicates.
	 *
	 * This value should be considered "read only" and not modified.
	 *
	 * When an HTML parser has not already set the document compatibility mode,
	 * (e.g. "quirks" or "no-quirks" mode), it will be inferred from the properties
	 * of the appropriate DOCTYPE declaration, if one exists. The DOCTYPE can
	 * indicate one of three possible document compatibility modes:
	 *
	 *  - "no-quirks" and "limited-quirks" modes (also called "standards" mode).
	 *  - "quirks" mode (also called `CSS1Compat` mode).
	 *
	 * An appropriate DOCTYPE is one encountered in the "initial" insertion mode,
	 * before the HTML element has been opened and before finding any other
	 * DOCTYPE declaration tokens.
	 *
	 * @see https://html.spec.whatwg.org/#the-initial-insertion-mode
	 *
	 * @since 6.7.0
	 *
	 * @var string One of "no-quirks", "limited-quirks", or "quirks".
	 */
	public $indicated_compatibility_mode;

	/**
	 * Constructor.
	 *
	 * This class should not be instantiated directly.
	 * Use the static {@see self::from_doctype_token} method instead.
	 *
	 * The arguments to this constructor correspond to the "DOCTYPE token"
	 * as defined in the HTML specification.
	 *
	 * > DOCTYPE tokens have a name, a public identifier, a system identifier,
	 * > and a force-quirks flag. When a DOCTYPE token is created, its name, public identifier,
	 * > and system identifier must be marked as missing (which is a distinct state from the
	 * > empty string), and the force-quirks flag must be set to off (its other state is on).
	 *
	 * @see https://html.spec.whatwg.org/multipage/parsing.html#tokenization
	 *
	 * @since 6.7.0
	 *
	 * @param string|null $name              Name of the DOCTYPE.
	 * @param string|null $public_identifier Public identifier of the DOCTYPE.
	 * @param string|null $system_identifier System identifier of the DOCTYPE.
	 * @param bool        $force_quirks_flag Whether the force-quirks flag is set for the token.
	 */
	private function __construct(
		?string $name,
		?string $public_identifier,
		?string $system_identifier,
		bool $force_quirks_flag
	) {
		$this->name              = $name;
		$this->public_identifier = $public_identifier;
		$this->system_identifier = $system_identifier;

		/*
		 * > If the DOCTYPE token matches one of the conditions in the following list,
		 * > then set the Document to quirks mode:
		 */

		/*
		 * > The force-quirks flag is set to on.
		 */
		if ( $force_quirks_flag ) {
			$this->indicated_compatibility_mode = 'quirks';
			return;
		}

		/*
		 * Normative documents will contain the literal `<!DOCTYPE html>` with no
		 * public or system identifiers; short-circuit to avoid extra parsing.
		 */
		if ( 'html' === $name && null === $public_identifier && null === $system_identifier ) {
			$this->indicated_compatibility_mode = 'no-quirks';
			return;
		}

		/*
		 * > The name is not "html".
		 *
		 * The tokenizer must report the name in lower case even if provided in
		 * the document in upper case; thus no conversion is required here.
		 */
		if ( 'html' !== $name ) {
			$this->indicated_compatibility_mode = 'quirks';
			return;
		}

		/*
		 * Set up some variables to handle the rest of the conditions.
		 *
		 * > set...the public identifier...to...the empty string if the public identifier was missing.
		 * > set...the system identifier...to...the empty string if the system identifier was missing.
		 * >
		 * > The system identifier and public identifier strings must be compared...
		 * > in an ASCII case-insensitive manner.
		 * >
		 * > A system identifier whose value is the empty string is not considered missing
		 * > for the purposes of the conditions above.
		 */
		$system_identifier_is_missing = null === $system_identifier;
		$public_identifier            = null === $public_identifier ? '' : strtolower( $public_identifier );
		$system_identifier            = null === $system_identifier ? '' : strtolower( $system_identifier );

		/*
		 * > The public identifier is set to…
		 */
		if (
			'-//w3o//dtd w3 html strict 3.0//en//' === $public_identifier ||
			'-/w3c/dtd html 4.0 transitional/en' === $public_identifier ||
			'html' === $public_identifier
		) {
			$this->indicated_compatibility_mode = 'quirks';
			return;
		}

		/*
		 * > The system identifier is set to…
		 */
		if ( 'http://www.ibm.com/data/dtd/v11/ibmxhtml1-transitional.dtd' === $system_identifier ) {
			$this->indicated_compatibility_mode = 'quirks';
			return;
		}

		/*
		 * All of the following conditions depend on matching the public identifier.
		 * If the public identifier is empty, none of the following conditions will match.
		 */
		if ( '' === $public_identifier ) {
			$this->indicated_compatibility_mode = 'no-quirks';
			return;
		}

		/*
		 * > The public identifier starts with…
		 *
		 * @todo Optimize this matching. It shouldn't be a large overall performance issue,
		 *       however, as only a single DOCTYPE declaration token should ever be parsed,
		 *       and normative documents will have exited before reaching this condition.
		 */
		if (
			str_starts_with( $public_identifier, '+//silmaril//dtd html pro v0r11 19970101//' ) ||
			str_starts_with( $public_identifier, '-//as//dtd html 3.0 aswedit + extensions//' ) ||
			str_starts_with( $public_identifier, '-//advasoft ltd//dtd html 3.0 aswedit + extensions//' ) ||
			str_starts_with( $public_identifier, '-//ietf//dtd html 2.0 level 1//' ) ||
			str_starts_with( $public_identifier, '-//ietf//dtd html 2.0 level 2//' ) ||
			str_starts_with( $public_identifier, '-//ietf//dtd html 2.0 strict level 1//' ) ||
			str_starts_with( $public_identifier, '-//ietf//dtd html 2.0 strict level 2//' ) ||
			str_starts_with( $public_identifier, '-//ietf//dtd html 2.0 strict//' ) ||
			str_starts_with( $public_identifier, '-//ietf//dtd html 2.0//' ) ||
			str_starts_with( $public_identifier, '-//ietf//dtd html 2.1e//' ) ||
			str_starts_with( $public_identifier, '-//ietf//dtd html 3.0//' ) ||
			str_starts_with( $public_identifier, '-//ietf//dtd html 3.2 final//' ) ||
			str_starts_with( $public_identifier, '-//ietf//dtd html 3.2//' ) ||
			str_starts_with( $public_identifier, '-//ietf//dtd html 3//' ) ||
			str_starts_with( $public_identifier, '-//ietf//dtd html level 0//' ) ||
			str_starts_with( $public_identifier, '-//ietf//dtd html level 1//' ) ||
			str_starts_with( $public_identifier, '-//ietf//dtd html level 2//' ) ||
			str_starts_with( $public_identifier, '-//ietf//dtd html level 3//' ) ||
			str_starts_with( $public_identifier, '-//ietf//dtd html strict level 0//' ) ||
			str_starts_with( $public_identifier, '-//ietf//dtd html strict level 1//' ) ||
			str_starts_with( $public_identifier, '-//ietf//dtd html strict level 2//' ) ||
			str_starts_with( $public_identifier, '-//ietf//dtd html strict level 3//' ) ||
			str_starts_with( $public_identifier, '-//ietf//dtd html strict//' ) ||
			str_starts_with( $public_identifier, '-//ietf//dtd html//' ) ||
			str_starts_with( $public_identifier, '-//metrius//dtd metrius presentational//' ) ||
			str_starts_with( $public_identifier, '-//microsoft//dtd internet explorer 2.0 html strict//' ) ||
			str_starts_with( $public_identifier, '-//microsoft//dtd internet explorer 2.0 html//' ) ||
			str_starts_with( $public_identifier, '-//microsoft//dtd internet explorer 2.0 tables//' ) ||
			str_starts_with( $public_identifier, '-//microsoft//dtd internet explorer 3.0 html strict//' ) ||
			str_starts_with( $public_identifier, '-//microsoft//dtd internet explorer 3.0 html//' ) ||
			str_starts_with( $public_identifier, '-//microsoft//dtd internet explorer 3.0 tables//' ) ||
			str_starts_with( $public_identifier, '-//netscape comm. corp.//dtd html//' ) ||
			str_starts_with( $public_identifier, '-//netscape comm. corp.//dtd strict html//' ) ||
			str_starts_with( $public_identifier, "-//o'reilly and associates//dtd html 2.0//" ) ||
			str_starts_with( $public_identifier, "-//o'reilly and associates//dtd html extended 1.0//" ) ||
			str_starts_with( $public_identifier, "-//o'reilly and associates//dtd html extended relaxed 1.0//" ) ||
			str_starts_with( $public_identifier, '-//sq//dtd html 2.0 hotmetal + extensions//' ) ||
			str_starts_with( $public_identifier, '-//softquad software//dtd hotmetal pro 6.0::19990601::extensions to html 4.0//' ) ||
			str_starts_with( $public_identifier, '-//softquad//dtd hotmetal pro 4.0::19971010::extensions to html 4.0//' ) ||
			str_starts_with( $public_identifier, '-//spyglass//dtd html 2.0 extended//' ) ||
			str_starts_with( $public_identifier, '-//sun microsystems corp.//dtd hotjava html//' ) ||
			str_starts_with( $public_identifier, '-//sun microsystems corp.//dtd hotjava strict html//' ) ||
			str_starts_with( $public_identifier, '-//w3c//dtd html 3 1995-03-24//' ) ||
			str_starts_with( $public_identifier, '-//w3c//dtd html 3.2 draft//' ) ||
			str_starts_with( $public_identifier, '-//w3c//dtd html 3.2 final//' ) ||
			str_starts_with( $public_identifier, '-//w3c//dtd html 3.2//' ) ||
			str_starts_with( $public_identifier, '-//w3c//dtd html 3.2s draft//' ) ||
			str_starts_with( $public_identifier, '-//w3c//dtd html 4.0 frameset//' ) ||
			str_starts_with( $public_identifier, '-//w3c//dtd html 4.0 transitional//' ) ||
			str_starts_with( $public_identifier, '-//w3c//dtd html experimental 19960712//' ) ||
			str_starts_with( $public_identifier, '-//w3c//dtd html experimental 970421//' ) ||
			str_starts_with( $public_identifier, '-//w3c//dtd w3 html//' ) ||
			str_starts_with( $public_identifier, '-//w3o//dtd w3 html 3.0//' ) ||
			str_starts_with( $public_identifier, '-//webtechs//dtd mozilla html 2.0//' ) ||
			str_starts_with( $public_identifier, '-//webtechs//dtd mozilla html//' )
		) {
			$this->indicated_compatibility_mode = 'quirks';
			return;
		}

		/*
		 * > The system identifier is missing and the public identifier starts with…
		 */
		if (
			$system_identifier_is_missing && (
				str_starts_with( $public_identifier, '-//w3c//dtd html 4.01 frameset//' ) ||
				str_starts_with( $public_identifier, '-//w3c//dtd html 4.01 transitional//' )
			)
		) {
			$this->indicated_compatibility_mode = 'quirks';
			return;
		}

		/*
		 * > Otherwise, if the DOCTYPE token matches one of the conditions in
		 * > the following list, then set the Document to limited-quirks mode.
		 */

		/*
		 * > The public identifier starts with…
		 */
		if (
			str_starts_with( $public_identifier, '-//w3c//dtd xhtml 1.0 frameset//' ) ||
			str_starts_with( $public_identifier, '-//w3c//dtd xhtml 1.0 transitional//' )
		) {
			$this->indicated_compatibility_mode = 'limited-quirks';
			return;
		}

		/*
		 * > The system identifier is not missing and the public identifier starts with…
		 */
		if (
			! $system_identifier_is_missing && (
				str_starts_with( $public_identifier, '-//w3c//dtd html 4.01 frameset//' ) ||
				str_starts_with( $public_identifier, '-//w3c//dtd html 4.01 transitional//' )
			)
		) {
			$this->indicated_compatibility_mode = 'limited-quirks';
			return;
		}

		$this->indicated_compatibility_mode = 'no-quirks';
	}

	/**
	 * Creates a WP_HTML_Doctype_Info instance by parsing a raw DOCTYPE declaration token.
	 *
	 * Use this method to parse a DOCTYPE declaration token and get access to its properties
	 * via the returned WP_HTML_Doctype_Info class instance. The provided input must parse
	 * properly as a DOCTYPE declaration, though it must not represent a valid DOCTYPE.
	 *
	 * Example:
	 *
	 *     // Normative HTML DOCTYPE declaration.
	 *     $doctype = WP_HTML_Doctype_Info::from_doctype_token( '<!DOCTYPE html>' );
	 *     'no-quirks' === $doctype->indicated_compatibility_mode;
	 *
	 *     // A nonsensical DOCTYPE is still valid, and will indicate "quirks" mode.
	 *     $doctype = WP_HTML_Doctype_Info::from_doctype_token( '<!doctypeJSON SILLY "nonsense\'>' );
	 *     'quirks' === $doctype->indicated_compatibility_mode;
	 *
	 *     // Textual quirks present in raw HTML are handled appropriately.
	 *     $doctype = WP_HTML_Doctype_Info::from_doctype_token( "<!DOCTYPE\nhtml\n>" );
	 *     'no-quirks' === $doctype->indicated_compatibility_mode;
	 *
	 *     // Anything other than a proper DOCTYPE declaration token fails to parse.
	 *     null === WP_HTML_Doctype_Info::from_doctype_token( ' <!DOCTYPE>' );
	 *     null === WP_HTML_Doctype_Info::from_doctype_token( '<!DOCTYPE ><p>' );
	 *     null === WP_HTML_Doctype_Info::from_doctype_token( '<!TYPEDOC>' );
	 *     null === WP_HTML_Doctype_Info::from_doctype_token( 'html' );
	 *     null === WP_HTML_Doctype_Info::from_doctype_token( '<?xml version="1.0" encoding="UTF-8" ?>' );
	 *
	 * @since 6.7.0
	 *
	 * @param string $doctype_html The complete raw DOCTYPE HTML string, e.g. `<!DOCTYPE html>`.
	 *
	 * @return WP_HTML_Doctype_Info|null A WP_HTML_Doctype_Info instance will be returned if the
	 *                                   provided DOCTYPE HTML is a valid DOCTYPE. Otherwise, null.
	 */
	public static function from_doctype_token( string $doctype_html ): ?self {
		$doctype_name      = null;
		$doctype_public_id = null;
		$doctype_system_id = null;

		$end = strlen( $doctype_html ) - 1;

		/*
		 * This parser combines the rules for parsing DOCTYPE tokens found in the HTML
		 * specification for the DOCTYPE related tokenizer states.
		 *
		 * @see https://html.spec.whatwg.org/#doctype-state
		 */

		/*
		 * - Valid DOCTYPE HTML token must be at least `<!DOCTYPE>` assuming a complete token not
		 *   ending in end-of-file.
		 * - It must start with an ASCII case-insensitive match for `<!DOCTYPE`.
		 * - The only occurrence of `>` must be the final byte in the HTML string.
		 */
		if (
			$end < 9 ||
			0 !== substr_compare( $doctype_html, '<!DOCTYPE', 0, 9, true )
		) {
			return null;
		}

		$at = 9;
		// Is there one and only one `>`?
		if ( '>' !== $doctype_html[ $end ] || ( strcspn( $doctype_html, '>', $at ) + $at ) < $end ) {
			return null;
		}

		/*
		 * Perform newline normalization and ensure the $end value is correct after normalization.
		 *
		 * @see https://html.spec.whatwg.org/#preprocessing-the-input-stream
		 * @see https://infra.spec.whatwg.org/#normalize-newlines
		 */
		$doctype_html = str_replace( "\r\n", "\n", $doctype_html );
		$doctype_html = str_replace( "\r", "\n", $doctype_html );
		$end          = strlen( $doctype_html ) - 1;

		/*
		 * In this state, the doctype token has been found and its "content" optionally including the
		 * name, public identifier, and system identifier is between the current position and the end.
		 *
		 *     "<!DOCTYPE...declaration...>"
		 *               ╰─ $at           ╰─ $end
		 *
		 * It's also possible that the declaration part is empty.
		 *
		 *               ╭─ $at
		 *     "<!DOCTYPE>"
		 *               ╰─ $end
		 *
		 * Rules for parsing ">" which terminates the DOCTYPE do not need to be considered as they
		 * have been handled above in the condition that the provided DOCTYPE HTML must contain
		 * exactly one ">" character in the final position.
		 */

		/*
		 *
		 * Parsing effectively begins in "Before DOCTYPE name state". Ignore whitespace and
		 * proceed to the next state.
		 *
		 * @see https://html.spec.whatwg.org/#before-doctype-name-state
		 */
		$at += strspn( $doctype_html, " \t\n\f\r", $at );

		if ( $at >= $end ) {
			return new self( $doctype_name, $doctype_public_id, $doctype_system_id, true );
		}

		$name_length  = strcspn( $doctype_html, " \t\n\f\r", $at, $end - $at );
		$doctype_name = str_replace( "\0", "\u{FFFD}", strtolower( substr( $doctype_html, $at, $name_length ) ) );

		$at += $name_length;
		$at += strspn( $doctype_html, " \t\n\f\r", $at, $end - $at );
		if ( $at >= $end ) {
			return new self( $doctype_name, $doctype_public_id, $doctype_system_id, false );
		}

		/*
		 * "After DOCTYPE name state"
		 *
		 * Find a case-insensitive match for "PUBLIC" or "SYSTEM" at this point.
		 * Otherwise, set force-quirks and enter bogus DOCTYPE state (skip the rest of the doctype).
		 *
		 * @see https://html.spec.whatwg.org/#after-doctype-name-state
		 */
		if ( $at + 6 >= $end ) {
			return new self( $doctype_name, $doctype_public_id, $doctype_system_id, true );
		}

		/*
		 * > If the six characters starting from the current input character are an ASCII
		 * > case-insensitive match for the word "PUBLIC", then consume those characters
		 * > and switch to the after DOCTYPE public keyword state.
		 */
		if ( 0 === substr_compare( $doctype_html, 'PUBLIC', $at, 6, true ) ) {
			$at += 6;
			$at += strspn( $doctype_html, " \t\n\f\r", $at, $end - $at );
			if ( $at >= $end ) {
				return new self( $doctype_name, $doctype_public_id, $doctype_system_id, true );
			}
			goto parse_doctype_public_identifier;
		}

		/*
		 * > Otherwise, if the six characters starting from the current input character are an ASCII
		 * > case-insensitive match for the word "SYSTEM", then consume those characters and switch
		 * > to the after DOCTYPE system keyword state.
		 */
		if ( 0 === substr_compare( $doctype_html, 'SYSTEM', $at, 6, true ) ) {
			$at += 6;
			$at += strspn( $doctype_html, " \t\n\f\r", $at, $end - $at );
			if ( $at >= $end ) {
				return new self( $doctype_name, $doctype_public_id, $doctype_system_id, true );
			}
			goto parse_doctype_system_identifier;
		}

		/*
		 * > Otherwise, this is an invalid-character-sequence-after-doctype-name parse error.
		 * > Set the current DOCTYPE token's force-quirks flag to on. Reconsume in the bogus
		 * > DOCTYPE state.
		 */
		return new self( $doctype_name, $doctype_public_id, $doctype_system_id, true );

		parse_doctype_public_identifier:
		/*
		 * The parser should enter "DOCTYPE public identifier (double-quoted) state" or
		 * "DOCTYPE public identifier (single-quoted) state" by finding one of the valid quotes.
		 * Anything else forces quirks mode and ignores the rest of the contents.
		 *
		 * @see https://html.spec.whatwg.org/#doctype-public-identifier-(double-quoted)-state
		 * @see https://html.spec.whatwg.org/#doctype-public-identifier-(single-quoted)-state
		 */
		$closer_quote = $doctype_html[ $at ];

		/*
		 * > This is a missing-quote-before-doctype-public-identifier parse error. Set the
		 * > current DOCTYPE token's force-quirks flag to on. Reconsume in the bogus DOCTYPE state.
		 */
		if ( '"' !== $closer_quote && "'" !== $closer_quote ) {
			return new self( $doctype_name, $doctype_public_id, $doctype_system_id, true );
		}

		++$at;

		$identifier_length = strcspn( $doctype_html, $closer_quote, $at, $end - $at );
		$doctype_public_id = str_replace( "\0", "\u{FFFD}", substr( $doctype_html, $at, $identifier_length ) );

		$at += $identifier_length;
		if ( $at >= $end || $closer_quote !== $doctype_html[ $at ] ) {
			return new self( $doctype_name, $doctype_public_id, $doctype_system_id, true );
		}

		++$at;

		/*
		 * "Between DOCTYPE public and system identifiers state"
		 *
		 * Advance through whitespace between public and system identifiers.
		 *
		 * @see https://html.spec.whatwg.org/#between-doctype-public-and-system-identifiers-state
		 */
		$at += strspn( $doctype_html, " \t\n\f\r", $at, $end - $at );
		if ( $at >= $end ) {
			return new self( $doctype_name, $doctype_public_id, $doctype_system_id, false );
		}

		parse_doctype_system_identifier:
		/*
		 * The parser should enter "DOCTYPE system identifier (double-quoted) state" or
		 * "DOCTYPE system identifier (single-quoted) state" by finding one of the valid quotes.
		 * Anything else forces quirks mode and ignores the rest of the contents.
		 *
		 * @see https://html.spec.whatwg.org/#doctype-system-identifier-(double-quoted)-state
		 * @see https://html.spec.whatwg.org/#doctype-system-identifier-(single-quoted)-state
		 */
		$closer_quote = $doctype_html[ $at ];

		/*
		 * > This is a missing-quote-before-doctype-system-identifier parse error. Set the
		 * > current DOCTYPE token's force-quirks flag to on. Reconsume in the bogus DOCTYPE state.
		 */
		if ( '"' !== $closer_quote && "'" !== $closer_quote ) {
			return new self( $doctype_name, $doctype_public_id, $doctype_system_id, true );
		}

		++$at;

		$identifier_length = strcspn( $doctype_html, $closer_quote, $at, $end - $at );
		$doctype_system_id = str_replace( "\0", "\u{FFFD}", substr( $doctype_html, $at, $identifier_length ) );

		$at += $identifier_length;
		if ( $at >= $end || $closer_quote !== $doctype_html[ $at ] ) {
			return new self( $doctype_name, $doctype_public_id, $doctype_system_id, true );
		}

		return new self( $doctype_name, $doctype_public_id, $doctype_system_id, false );
	}
}
