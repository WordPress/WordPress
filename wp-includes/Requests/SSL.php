<?php
/**
 * SSL utilities for Requests
 *
 * @package Requests
 * @subpackage Utilities
 */

/**
 * SSL utilities for Requests
 *
 * Collection of utilities for working with and verifying SSL certificates.
 *
 * @package Requests
 * @subpackage Utilities
 */
class Requests_SSL {
	/**
	 * Verify the certificate against common name and subject alternative names
	 *
	 * Unfortunately, PHP doesn't check the certificate against the alternative
	 * names, leading things like 'https://www.github.com/' to be invalid.
	 *
	 * @see https://tools.ietf.org/html/rfc2818#section-3.1 RFC2818, Section 3.1
	 *
	 * @throws Requests_Exception On not obtaining a match for the host (`fsockopen.ssl.no_match`)
	 * @param string $host Host name to verify against
	 * @param array $cert Certificate data from openssl_x509_parse()
	 * @return bool
	 */
	public static function verify_certificate($host, $cert) {
		$has_dns_alt = false;

		// Check the subjectAltName
		if (!empty($cert['extensions']) && !empty($cert['extensions']['subjectAltName'])) {
			$altnames = explode(',', $cert['extensions']['subjectAltName']);
			foreach ($altnames as $altname) {
				$altname = trim($altname);
				if (strpos($altname, 'DNS:') !== 0) {
					continue;
				}

				$has_dns_alt = true;

				// Strip the 'DNS:' prefix and trim whitespace
				$altname = trim(substr($altname, 4));

				// Check for a match
				if (self::match_domain($host, $altname) === true) {
					return true;
				}
			}
		}

		// Fall back to checking the common name if we didn't get any dNSName
		// alt names, as per RFC2818
		if (!$has_dns_alt && !empty($cert['subject']['CN'])) {
			// Check for a match
			if (self::match_domain($host, $cert['subject']['CN']) === true) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Verify that a reference name is valid
	 *
	 * Verifies a dNSName for HTTPS usage, (almost) as per Firefox's rules:
	 * - Wildcards can only occur in a name with more than 3 components
	 * - Wildcards can only occur as the last character in the first
	 *   component
	 * - Wildcards may be preceded by additional characters
	 *
	 * We modify these rules to be a bit stricter and only allow the wildcard
	 * character to be the full first component; that is, with the exclusion of
	 * the third rule.
	 *
	 * @param string $reference Reference dNSName
	 * @return boolean Is the name valid?
	 */
	public static function verify_reference_name($reference) {
		$parts = explode('.', $reference);

		// Check the first part of the name
		$first = array_shift($parts);

		if (strpos($first, '*') !== false) {
			// Check that the wildcard is the full part
			if ($first !== '*') {
				return false;
			}

			// Check that we have at least 3 components (including first)
			if (count($parts) < 2) {
				return false;
			}
		}

		// Check the remaining parts
		foreach ($parts as $part) {
			if (strpos($part, '*') !== false) {
				return false;
			}
		}

		// Nothing found, verified!
		return true;
	}

	/**
	 * Match a hostname against a dNSName reference
	 *
	 * @param string $host Requested host
	 * @param string $reference dNSName to match against
	 * @return boolean Does the domain match?
	 */
	public static function match_domain($host, $reference) {
		// Check if the reference is blocklisted first
		if (self::verify_reference_name($reference) !== true) {
			return false;
		}

		// Check for a direct match
		if ($host === $reference) {
			return true;
		}

		// Calculate the valid wildcard match if the host is not an IP address
		// Also validates that the host has 3 parts or more, as per Firefox's
		// ruleset.
		if (ip2long($host) === false) {
			$parts    = explode('.', $host);
			$parts[0] = '*';
			$wildcard = implode('.', $parts);
			if ($wildcard === $reference) {
				return true;
			}
		}

		return false;
	}
}
