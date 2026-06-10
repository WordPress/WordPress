<?php
/**
 * Class 'WP_Email_Address'.
 *
 * @package WordPress
 * @since 7.1.0
 */

/**
 * WP_Email_Address Class.
 *
 * Represents a validated email address. The address may or may not be deliverable.
 *
 * Use the static factory method {@see WP_Email_Address::from_string()} to create instances
 * of this class rather than the constructor. This method only returns an instance for
 * validated email addresses, and `null` if the provided email address fails to validate.
 *
 * Example:
 *
 *     $email = WP_Email_Address::from_string( 'wordpress@wordpress.org' );
 *     'wordpress'     === $email->get_local_part();
 *     'wordpress.org' === $email->get_domain();
 *
 * @see self::from_string()        to parse and validate a provided email address.
 * @see self::get_localpart()      for the local part or mailbox of the address.
 * @see self::get_ascii_domain()   for an encoded version of the domain best suited for
 *                                 printing in contexts where other software reads it and
 *                                 decodes it, such as in an `<a href>` attribute.
 * @see self::get_unicode_domain() for a decoded version of the domain best suited for
 *                                 printing in contexts where humans read it, where any
 *                                 Unicode characters print as they are, not as punycode.
 *
 * @since 7.1.0
 */
final class WP_Email_Address {
	/**
	 * Regex for the local part when Unicode is not enabled.
	 *
	 * Matches the character set from the WHATWG email specification:
	 * https://html.spec.whatwg.org/multipage/input.html#email-state-(type=email)
	 *
	 * @since 7.1.0
	 * @var string
	 */
	const LOCAL_PART_ASCII_REGEX = '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+$/';

	/**
	 * Regex for the local part when Unicode is enabled.
	 *
	 * Extends the WHATWG character set to allow Unicode letters and numbers,
	 * and applies the same grapheme-cluster structure used for domain labels:
	 * each cluster must open with a non-combining character.
	 *
	 * @since 7.1.0
	 * @var string
	 */
	const LOCAL_PART_UNICODE_REGEX = '/^([\p{L}\p{N}.!#$%&\'*+\/=?^_`{|}~-]\p{M}*)+$/u';

	/**
	 * Pattern for a single ASCII domain label (no dot).
	 *
	 * Matches a label from the WHATWG email specification: starts and ends with
	 * a letter or digit; internal characters may include hyphens.
	 *
	 * @since 7.1.0
	 * @var string
	 */
	const DOMAIN_LABEL_ASCII = '[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?';

	/**
	 * Pattern for a single Unicode domain label (no dot).
	 *
	 * Extends the ASCII label pattern to allow Unicode letters and numbers,
	 * with grapheme-cluster structure: each cluster must open with a letter or
	 * digit (not a combining mark), followed by zero or more combining marks.
	 *
	 * @since 7.1.0
	 * @var string
	 */
	const DOMAIN_LABEL_UNICODE = '[\p{L}\p{N}]\p{M}*(?:(?:[\p{L}\p{N}-]\p{M}*)*[\p{L}\p{N}]\p{M}*)?';

	/**
	 * Regex for the domain when Unicode is not enabled.
	 *
	 * Assembled from {@see self::DOMAIN_LABEL_ASCII}: one label, then zero or
	 * more dot-separated labels.
	 *
	 * @since 7.1.0
	 * @var string
	 */
	const DOMAIN_ASCII_REGEX = '/^' . self::DOMAIN_LABEL_ASCII . '(?:\.' . self::DOMAIN_LABEL_ASCII . ')*$/';

	/**
	 * Regex for the domain when Unicode is enabled.
	 *
	 * Assembled from {@see self::DOMAIN_LABEL_UNICODE}: one label, then zero or
	 * more dot-prefixed labels.
	 *
	 * @since 7.1.0
	 * @var string
	 */
	const DOMAIN_UNICODE_REGEX = '/^' . self::DOMAIN_LABEL_UNICODE . '(?:\.' . self::DOMAIN_LABEL_UNICODE . ')*$/u';

	/**
	 * The local part of the email address (the portion before the '@').
	 *
	 * @since 7.1.0
	 * @var string
	 */
	private $localpart;

	/**
	 * The email domain using punycode transcription instead of Unicode characters.
	 *
	 * Example:
	 *
	 *     $email = WP_Email_Address::from_string( 'checkout@bücher.tld' );
	 *     'xn--bcher-kva.tld' === $email->get_ascii_domain();
	 *
	 * @see self::$decoded_domain
	 *
	 * @since 7.1.0
	 * @var string
	 */
	private $encoded_domain;

	/**
	 * The email domain, which may contain Unicode characters.
	 *
	 * Example:
	 *
	 *     $email = WP_Email_Address::from_string( 'checkout@bücher.tld' );
	 *     'bücher.tld' === $email->get_unicode_domain();
	 *
	 * @see self::$encoded_domain
	 *
	 * @since 7.1.0
	 * @var string
	 */
	private $decoded_domain;

	/**
	 * Private constructor. Use {@see WP_Email_Address::from_string()} to create instances.
	 *
	 * @since 7.1.0
	 * @private
	 *
	 * @param string $localpart           The local part of the email address.
	 * @param string $ascii_domain        The domain part of the email address, which may include punycode transcription.
	 * @param string|null $unicode_domain The domain part of the email address, which may contain Unicode characters, or
	 *                                    null if no Unicode translation exists.
	 */
	private function __construct( string $localpart, string $ascii_domain, ?string $unicode_domain ) {
		$this->localpart      = $localpart;
		$this->encoded_domain = $ascii_domain;
		$this->decoded_domain = $unicode_domain;
	}

	/**
	 * Creates a WP_Email_Address from a string.
	 *
	 * This method is intended to accept all strings that are considered valid email
	 * addresses by the WHATWG HTML specification for the `email` input type
	 * {@link https://html.spec.whatwg.org/multipage/input.html#email-state-(type=email)}
	 * and some additional addresses, while rejecting strings that are more likely to
	 * be typos, mispastes, or attacks. This class may reject a few address that are
	 * valid according to RFC 5322, but it always accepts an address if it's valid
	 * according to WHATWG. Put differently: If users can type an address into the
	 * major browsers of 2026, this class accepts them, if they can't (in 2026),
	 * this class may or may not.
	 *
	 * Example:
	 *
	 *     // Typical all-US-ASCII email address.
	 *     $email = WP_Email_Address::from_string( 'webmaster@example.com' );
	 *     'webmaster'   === $email->get_localpart();
	 *     'example.com' === $email->get_ascii_domain();
	 *     'example.com' === $email->get_unicode_domain();
	 *
	 *     // Punycode domains are always decoded.
	 *     $email = WP_Email_Address::from_string( 'books@xn--bcher-kva.de' );
	 *     'books'            === $email->get_localpart();
	 *     'xn--bcher-kva.de' === $email->get_ascii_domain();
	 *     'Bücher.de'        === $email->get_unicode_domain();
	 *
	 *     // Unicode localparts are accepted if Unicode addresses are requested (the default).
	 *     $email = WP_Email_Address::from_string( 'bücher@example.com' );
	 *     'bücher' === $email->get_localpart();
	 *
	 *     // Addresses with non-ASCII are rejected if ASCII-only addresses are requested.
	 *     null === WP_Email_Address::from_string( 'books@xn--bcher-kva.de', 'ascii' );
	 *     null === WP_Email_Address::from_string( 'bücher@xn--bcher-kva.de', 'ascii' );
	 *     null === WP_Email_Address::from_string( 'bücher@Bücher.de', 'ascii' );
	 *
	 *     // Some valid addresses (according to RFC 5322) are rejected.
	 *     null === WP_Email_Address::from_string( '"<iframe src=...>"@example.com' );
	 *
	 * Note! If an address contains punycode encodings but the required {@see idn_to_utf8()}
	 * function is missing (from the `intl` extension), this will reject that email address.
	 *
	 * @since 7.1.0
	 *
	 * @param string            $input         The email address string to parse.
	 * @param 'ascii'|'unicode' $character_set Allow only ASCII addresses or all valid Unicode addresses.
	 * @return WP_Email_Address|null A WP_Email_Address instance, or null if the input fails to validate.
	 */
	public static function from_string( string $input, string $character_set = 'unicode' ): ?WP_Email_Address {
		// There must be exactly one '@' sign.
		$at_pos = strpos( $input, '@' );
		if ( false === $at_pos || strrpos( $input, '@' ) !== $at_pos ) {
			return null;
		}

		$allow_unicode  = 'unicode' === $character_set;
		$localpart      = substr( $input, 0, $at_pos );
		$ascii_domain   = substr( $input, $at_pos + 1 );
		$domain_labels  = explode( '.', $ascii_domain );
		$local_pattern  = $allow_unicode ? self::LOCAL_PART_UNICODE_REGEX : self::LOCAL_PART_ASCII_REGEX;
		$domain_pattern = $allow_unicode ? self::DOMAIN_UNICODE_REGEX : self::DOMAIN_ASCII_REGEX;

		foreach ( $domain_labels as $label ) {
			// DNS limits each label to 63 octets.
			if ( strlen( $label ) > 63 ) {
				return null;
			}
		}

		/*
		 * Without support for decoding punycode it’s not possible to validate
		 * the email address, so abort if any domain labels require decoding.
		 *
		 * The pattern detects `xn--` prefixes and invalid ACE prefixes.
		 */
		$needs_decoding = 1 === preg_match( '/(?:^|\.)..--/', $ascii_domain );
		if ( $needs_decoding && ! function_exists( 'idn_to_utf8' ) ) {
			return null;
		}

		/*
		 * Validate each domain label, decode any punycode to UTF-8, and
		 * reassemble the decoded labels into the local $domain variable.
		 */
		if ( $needs_decoding ) {
			$decoded_labels = array();
			foreach ( $domain_labels as $label ) {
				// Decode punycode labels to their Unicode form for further validation.
				if ( str_starts_with( $label, 'xn--' ) ) {
					$label = idn_to_utf8( $label, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46 );
					if ( false === $label ) {
						return null;
					}
				} elseif ( 1 === preg_match( '/^..--/', $label ) ) {
					// Reject labels with a reserved ACE-like prefix (two chars followed by '--').
					return null;
				}
				$decoded_labels[] = $label;
			}
			$decoded_domain = implode( '.', $decoded_labels );
		} else {
			$decoded_domain = $ascii_domain;
		}

		// Without Unicode support, reject any non-ASCII byte in either part.
		if (
			! $allow_unicode &&
			(
				1 === preg_match( '/[\x80-\xff]/', $input ) ||
				1 === preg_match( '/[\x80-\xff]/', $decoded_domain )
			)
		) {
			return null;
		}

		// All parts must be valid UTF-8, regardless of whether Unicode is requested. (A valid ASCII string is also valid UTF-8.)
		if (
			! wp_is_valid_utf8( $localpart ) ||
			! wp_is_valid_utf8( $ascii_domain ) ||
			! wp_is_valid_utf8( $decoded_domain )
		) {
			return null;
		}

		// Validate the local part against the allowed character set.
		if ( 1 !== preg_match( $local_pattern, $localpart ) ) {
			/** This filter is documented in wp-includes/formatting.php */
			if ( ! apply_filters( 'is_email', false, $input, 'local_invalid_chars' ) ) {
				return null;
			}
		}

		// The domain must contain at least one dot.
		if ( ! str_contains( $ascii_domain, '.' ) ) {
			/** This filter is documented in wp-includes/formatting.php */
			if ( ! apply_filters( 'is_email', false, $input, 'domain_no_periods' ) ) {
				return null;
			}
		}

		// Validate the domain against the allowed structure.
		if ( 1 !== preg_match( $domain_pattern, $decoded_domain ) ) {
			return null;
		}

		return new self( $localpart, $ascii_domain, $decoded_domain );
	}

	/**
	 * Returns the local part of the email address (the portion before the '@').
	 *
	 * Example:
	 *
	 *     $email = WP_Email_Address::from_string( 'checkout@bücher.tld' );
	 *     'checkout' === $email->get_localpart();
	 *
	 * @since 7.1.0
	 *
	 * @return string The local part of the email address.
	 */
	public function get_localpart(): string {
		return $this->localpart;
	}

	/**
	 * Returns the ASCII form of the domain, suitable for contexts in which
	 * other software will be reading and decoding it. May contain punycode.
	 *
	 * Example:
	 *
	 *     $email = WP_Email_Address::from_string( 'checkout@bücher.tld' );
	 *     'xn--bcher-kva.tld' === $email->get_ascii_domain();
	 *
	 * Note! Do not mix a Unicode local part with an ASCII domain part.
	 *       Prefer to keep the entire address in one form.
	 *
	 * @see self::get_unicode_domain()
	 *
	 * @return string Form of domain for machines, potentially containing
	 *                punycode translation of Unicode characters.
	 */
	public function get_ascii_domain(): string {
		return $this->encoded_domain;
	}

	/**
	 * Returns the Unicode form of the domain, suitable for contexts in which
	 * humans will be reading it. May contain Unicode characters.
	 *
	 * Example:
	 *
	 *     $email = WP_Email_Address::from_string( 'checkout@bücher.tld' );
	 *     'bücher.tld' === $email->get_unicode_domain();
	 *
	 * Note! Do not mix a Unicode local part with an ASCII domain part.
	 *       Prefer to keep the entire address in one form.
	 *
	 * @see self::get_ascii_domain()
	 *
	 * @since 7.1.0
	 *
	 * @return string The domain part of the email address.
	 */
	public function get_unicode_domain(): string {
		return $this->decoded_domain;
	}

	/**
	 * Returns the complete email address for contexts in which software
	 * will read it; may contain punycode transliterated Unicode characters.
	 *
	 * Use this method in places such as an `<a href>` attribute where other
	 * software will decode the address.
	 *
	 * The returned value can always be passed to {@see WP_Email_Address::from_string()}
	 * and will produce an equivalent WP_Email_Address instance.
	 *
	 * @see self::get_unicode_address()
	 *
	 * @since 7.1.0
	 *
	 * @return string
	 */
	public function get_ascii_address(): string {
		return $this->localpart . '@' . $this->encoded_domain;
	}

	/**
	 * Returns the complete email address for contexts in which humans
	 * will read it; may contain Unicode characters in the domain.
	 *
	 * Use this method in places such as HTML text nodes which visually
	 * show the email address and domain.
	 *
	 * The returned value can always be passed to {@see WP_Email_Address::from_string()}
	 * and will produce an equivalent WP_Email_Address instance.
	 *
	 * @see self::get_ascii_address()
	 *
	 * @since 7.1.0
	 *
	 * @return string The complete email address.
	 */
	public function get_unicode_address(): string {
		return $this->localpart . '@' . $this->decoded_domain;
	}
}
