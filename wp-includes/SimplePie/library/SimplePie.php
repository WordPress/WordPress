<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

use SimplePie\SimplePie as NamespacedSimplePie;

class_exists('SimplePie\SimplePie');

// @trigger_error(sprintf('Using the "SimplePie" class is deprecated since SimplePie 1.7.0, use "SimplePie\SimplePie" instead.'), \E_USER_DEPRECATED);

/** @phpstan-ignore-next-line */
if (\false) {
    /** @deprecated since SimplePie 1.7.0, use "SimplePie\SimplePie" instead */
    class SimplePie extends NamespacedSimplePie
    {
    }
}

/**
 * SimplePie Name
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::NAME instead.
 */
define('SIMPLEPIE_NAME', NamespacedSimplePie::NAME);

/**
 * SimplePie Version
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::VERSION instead.
 */
define('SIMPLEPIE_VERSION', NamespacedSimplePie::VERSION);

/**
 * SimplePie Build
 * @todo Hardcode for release (there's no need to have to call SimplePie_Misc::get_build() only every load of simplepie.inc)
 * @deprecated since SimplePie 1.7.0, use \SimplePie\Misc::get_build() instead.
 */
define('SIMPLEPIE_BUILD', gmdate('YmdHis', \SimplePie\Misc::get_build()));

/**
 * SimplePie Website URL
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::URL instead.
 */
define('SIMPLEPIE_URL', NamespacedSimplePie::URL);

/**
 * SimplePie Useragent
 * @see SimplePie::set_useragent()
 * @deprecated since SimplePie 1.7.0, use \SimplePie\Misc::get_default_useragent() instead.
 */
define('SIMPLEPIE_USERAGENT', \SimplePie\Misc::get_default_useragent());

/**
 * SimplePie Linkback
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::LINKBACK instead.
 */
define('SIMPLEPIE_LINKBACK', NamespacedSimplePie::LINKBACK);

/**
 * No Autodiscovery
 * @see SimplePie::set_autodiscovery_level()
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::LOCATOR_NONE instead.
 */
define('SIMPLEPIE_LOCATOR_NONE', NamespacedSimplePie::LOCATOR_NONE);

/**
 * Feed Link Element Autodiscovery
 * @see SimplePie::set_autodiscovery_level()
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::LOCATOR_AUTODISCOVERY instead.
 */
define('SIMPLEPIE_LOCATOR_AUTODISCOVERY', NamespacedSimplePie::LOCATOR_AUTODISCOVERY);

/**
 * Local Feed Extension Autodiscovery
 * @see SimplePie::set_autodiscovery_level()
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::LOCATOR_LOCAL_EXTENSION instead.
 */
define('SIMPLEPIE_LOCATOR_LOCAL_EXTENSION', NamespacedSimplePie::LOCATOR_LOCAL_EXTENSION);

/**
 * Local Feed Body Autodiscovery
 * @see SimplePie::set_autodiscovery_level()
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::LOCATOR_LOCAL_BODY instead.
 */
define('SIMPLEPIE_LOCATOR_LOCAL_BODY', NamespacedSimplePie::LOCATOR_LOCAL_BODY);

/**
 * Remote Feed Extension Autodiscovery
 * @see SimplePie::set_autodiscovery_level()
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::LOCATOR_REMOTE_EXTENSION instead.
 */
define('SIMPLEPIE_LOCATOR_REMOTE_EXTENSION', NamespacedSimplePie::LOCATOR_REMOTE_EXTENSION);

/**
 * Remote Feed Body Autodiscovery
 * @see SimplePie::set_autodiscovery_level()
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::LOCATOR_REMOTE_BODY instead.
 */
define('SIMPLEPIE_LOCATOR_REMOTE_BODY', NamespacedSimplePie::LOCATOR_REMOTE_BODY);

/**
 * All Feed Autodiscovery
 * @see SimplePie::set_autodiscovery_level()
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::LOCATOR_ALL instead.
 */
define('SIMPLEPIE_LOCATOR_ALL', NamespacedSimplePie::LOCATOR_ALL);

/**
 * No known feed type
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::TYPE_NONE instead.
 */
define('SIMPLEPIE_TYPE_NONE', NamespacedSimplePie::TYPE_NONE);

/**
 * RSS 0.90
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::TYPE_RSS_090 instead.
 */
define('SIMPLEPIE_TYPE_RSS_090', NamespacedSimplePie::TYPE_RSS_090);

/**
 * RSS 0.91 (Netscape)
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::TYPE_RSS_091_NETSCAPE instead.
 */
define('SIMPLEPIE_TYPE_RSS_091_NETSCAPE', NamespacedSimplePie::TYPE_RSS_091_NETSCAPE);

/**
 * RSS 0.91 (Userland)
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::TYPE_RSS_091_USERLAND instead.
 */
define('SIMPLEPIE_TYPE_RSS_091_USERLAND', NamespacedSimplePie::TYPE_RSS_091_USERLAND);

/**
 * RSS 0.91 (both Netscape and Userland)
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::TYPE_RSS_091 instead.
 */
define('SIMPLEPIE_TYPE_RSS_091', NamespacedSimplePie::TYPE_RSS_091);

/**
 * RSS 0.92
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::TYPE_RSS_092 instead.
 */
define('SIMPLEPIE_TYPE_RSS_092', NamespacedSimplePie::TYPE_RSS_092);

/**
 * RSS 0.93
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::TYPE_RSS_093 instead.
 */
define('SIMPLEPIE_TYPE_RSS_093', NamespacedSimplePie::TYPE_RSS_093);

/**
 * RSS 0.94
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::TYPE_RSS_094 instead.
 */
define('SIMPLEPIE_TYPE_RSS_094', NamespacedSimplePie::TYPE_RSS_094);

/**
 * RSS 1.0
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::TYPE_RSS_10 instead.
 */
define('SIMPLEPIE_TYPE_RSS_10', NamespacedSimplePie::TYPE_RSS_10);

/**
 * RSS 2.0
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::TYPE_RSS_20 instead.
 */
define('SIMPLEPIE_TYPE_RSS_20', NamespacedSimplePie::TYPE_RSS_20);

/**
 * RDF-based RSS
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::TYPE_RSS_RDF instead.
 */
define('SIMPLEPIE_TYPE_RSS_RDF', NamespacedSimplePie::TYPE_RSS_RDF);

/**
 * Non-RDF-based RSS (truly intended as syndication format)
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::TYPE_RSS_SYNDICATION instead.
 */
define('SIMPLEPIE_TYPE_RSS_SYNDICATION', NamespacedSimplePie::TYPE_RSS_SYNDICATION);

/**
 * All RSS
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::TYPE_RSS_ALL instead.
 */
define('SIMPLEPIE_TYPE_RSS_ALL', NamespacedSimplePie::TYPE_RSS_ALL);

/**
 * Atom 0.3
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::TYPE_ATOM_03 instead.
 */
define('SIMPLEPIE_TYPE_ATOM_03', NamespacedSimplePie::TYPE_ATOM_03);

/**
 * Atom 1.0
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::TYPE_ATOM_10 instead.
 */
define('SIMPLEPIE_TYPE_ATOM_10', NamespacedSimplePie::TYPE_ATOM_10);

/**
 * All Atom
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::TYPE_ATOM_ALL instead.
 */
define('SIMPLEPIE_TYPE_ATOM_ALL', NamespacedSimplePie::TYPE_ATOM_ALL);

/**
 * All feed types
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::TYPE_ALL instead.
 */
define('SIMPLEPIE_TYPE_ALL', NamespacedSimplePie::TYPE_ALL);

/**
 * No construct
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::CONSTRUCT_NONE instead.
 */
define('SIMPLEPIE_CONSTRUCT_NONE', NamespacedSimplePie::CONSTRUCT_NONE);

/**
 * Text construct
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::CONSTRUCT_TEXT instead.
 */
define('SIMPLEPIE_CONSTRUCT_TEXT', NamespacedSimplePie::CONSTRUCT_TEXT);

/**
 * HTML construct
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::CONSTRUCT_HTML instead.
 */
define('SIMPLEPIE_CONSTRUCT_HTML', NamespacedSimplePie::CONSTRUCT_HTML);

/**
 * XHTML construct
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::CONSTRUCT_XHTML instead.
 */
define('SIMPLEPIE_CONSTRUCT_XHTML', NamespacedSimplePie::CONSTRUCT_XHTML);

/**
 * base64-encoded construct
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::CONSTRUCT_BASE64 instead.
 */
define('SIMPLEPIE_CONSTRUCT_BASE64', NamespacedSimplePie::CONSTRUCT_BASE64);

/**
 * IRI construct
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::CONSTRUCT_IRI instead.
 */
define('SIMPLEPIE_CONSTRUCT_IRI', NamespacedSimplePie::CONSTRUCT_IRI);

/**
 * A construct that might be HTML
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::CONSTRUCT_MAYBE_HTML instead.
 */
define('SIMPLEPIE_CONSTRUCT_MAYBE_HTML', NamespacedSimplePie::CONSTRUCT_MAYBE_HTML);

/**
 * All constructs
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::CONSTRUCT_ALL instead.
 */
define('SIMPLEPIE_CONSTRUCT_ALL', NamespacedSimplePie::CONSTRUCT_ALL);

/**
 * Don't change case
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::SAME_CASE instead.
 */
define('SIMPLEPIE_SAME_CASE', NamespacedSimplePie::SAME_CASE);

/**
 * Change to lowercase
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::LOWERCASE instead.
 */
define('SIMPLEPIE_LOWERCASE', NamespacedSimplePie::LOWERCASE);

/**
 * Change to uppercase
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::UPPERCASE instead.
 */
define('SIMPLEPIE_UPPERCASE', NamespacedSimplePie::UPPERCASE);

/**
 * PCRE for HTML attributes
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::PCRE_HTML_ATTRIBUTE instead.
 */
define('SIMPLEPIE_PCRE_HTML_ATTRIBUTE', NamespacedSimplePie::PCRE_HTML_ATTRIBUTE);

/**
 * PCRE for XML attributes
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::PCRE_XML_ATTRIBUTE instead.
 */
define('SIMPLEPIE_PCRE_XML_ATTRIBUTE', NamespacedSimplePie::PCRE_XML_ATTRIBUTE);

/**
 * XML Namespace
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::NAMESPACE_XML instead.
 */
define('SIMPLEPIE_NAMESPACE_XML', NamespacedSimplePie::NAMESPACE_XML);

/**
 * Atom 1.0 Namespace
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::NAMESPACE_ATOM_10 instead.
 */
define('SIMPLEPIE_NAMESPACE_ATOM_10', NamespacedSimplePie::NAMESPACE_ATOM_10);

/**
 * Atom 0.3 Namespace
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::NAMESPACE_ATOM_03 instead.
 */
define('SIMPLEPIE_NAMESPACE_ATOM_03', NamespacedSimplePie::NAMESPACE_ATOM_03);

/**
 * RDF Namespace
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::NAMESPACE_RDF instead.
 */
define('SIMPLEPIE_NAMESPACE_RDF', NamespacedSimplePie::NAMESPACE_RDF);

/**
 * RSS 0.90 Namespace
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::NAMESPACE_RSS_090 instead.
 */
define('SIMPLEPIE_NAMESPACE_RSS_090', NamespacedSimplePie::NAMESPACE_RSS_090);

/**
 * RSS 1.0 Namespace
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::NAMESPACE_RSS_10 instead.
 */
define('SIMPLEPIE_NAMESPACE_RSS_10', NamespacedSimplePie::NAMESPACE_RSS_10);

/**
 * RSS 1.0 Content Module Namespace
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::NAMESPACE_RSS_10_MODULES_CONTENT instead.
 */
define('SIMPLEPIE_NAMESPACE_RSS_10_MODULES_CONTENT', NamespacedSimplePie::NAMESPACE_RSS_10_MODULES_CONTENT);

/**
 * RSS 2.0 Namespace
 * (Stupid, I know, but I'm certain it will confuse people less with support.)
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::NAMESPACE_RSS_20 instead.
 */
define('SIMPLEPIE_NAMESPACE_RSS_20', NamespacedSimplePie::NAMESPACE_RSS_20);

/**
 * DC 1.0 Namespace
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::NAMESPACE_DC_10 instead.
 */
define('SIMPLEPIE_NAMESPACE_DC_10', NamespacedSimplePie::NAMESPACE_DC_10);

/**
 * DC 1.1 Namespace
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::NAMESPACE_DC_11 instead.
 */
define('SIMPLEPIE_NAMESPACE_DC_11', NamespacedSimplePie::NAMESPACE_DC_11);

/**
 * W3C Basic Geo (WGS84 lat/long) Vocabulary Namespace
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::NAMESPACE_W3C_BASIC_GEO instead.
 */
define('SIMPLEPIE_NAMESPACE_W3C_BASIC_GEO', NamespacedSimplePie::NAMESPACE_W3C_BASIC_GEO);

/**
 * GeoRSS Namespace
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::NAMESPACE_GEORSS instead.
 */
define('SIMPLEPIE_NAMESPACE_GEORSS', NamespacedSimplePie::NAMESPACE_GEORSS);

/**
 * Media RSS Namespace
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::NAMESPACE_MEDIARSS instead.
 */
define('SIMPLEPIE_NAMESPACE_MEDIARSS', NamespacedSimplePie::NAMESPACE_MEDIARSS);

/**
 * Wrong Media RSS Namespace. Caused by a long-standing typo in the spec.
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::NAMESPACE_MEDIARSS_WRONG instead.
 */
define('SIMPLEPIE_NAMESPACE_MEDIARSS_WRONG', NamespacedSimplePie::NAMESPACE_MEDIARSS_WRONG);

/**
 * Wrong Media RSS Namespace #2. New namespace introduced in Media RSS 1.5.
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::NAMESPACE_MEDIARSS_WRONG2 instead.
 */
define('SIMPLEPIE_NAMESPACE_MEDIARSS_WRONG2', NamespacedSimplePie::NAMESPACE_MEDIARSS_WRONG2);

/**
 * Wrong Media RSS Namespace #3. A possible typo of the Media RSS 1.5 namespace.
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::NAMESPACE_MEDIARSS_WRONG3 instead.
 */
define('SIMPLEPIE_NAMESPACE_MEDIARSS_WRONG3', NamespacedSimplePie::NAMESPACE_MEDIARSS_WRONG3);

/**
 * Wrong Media RSS Namespace #4. New spec location after the RSS Advisory Board takes it over, but not a valid namespace.
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::NAMESPACE_MEDIARSS_WRONG4 instead.
 */
define('SIMPLEPIE_NAMESPACE_MEDIARSS_WRONG4', NamespacedSimplePie::NAMESPACE_MEDIARSS_WRONG4);

/**
 * Wrong Media RSS Namespace #5. A possible typo of the RSS Advisory Board URL.
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::NAMESPACE_MEDIARSS_WRONG5 instead.
 */
define('SIMPLEPIE_NAMESPACE_MEDIARSS_WRONG5', NamespacedSimplePie::NAMESPACE_MEDIARSS_WRONG5);

/**
 * iTunes RSS Namespace
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::NAMESPACE_ITUNES instead.
 */
define('SIMPLEPIE_NAMESPACE_ITUNES', NamespacedSimplePie::NAMESPACE_ITUNES);

/**
 * XHTML Namespace
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::NAMESPACE_XHTML instead.
 */
define('SIMPLEPIE_NAMESPACE_XHTML', NamespacedSimplePie::NAMESPACE_XHTML);

/**
 * IANA Link Relations Registry
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::IANA_LINK_RELATIONS_REGISTRY instead.
 */
define('SIMPLEPIE_IANA_LINK_RELATIONS_REGISTRY', NamespacedSimplePie::IANA_LINK_RELATIONS_REGISTRY);

/**
 * No file source
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::FILE_SOURCE_NONE instead.
 */
define('SIMPLEPIE_FILE_SOURCE_NONE', NamespacedSimplePie::FILE_SOURCE_NONE);

/**
 * Remote file source
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::FILE_SOURCE_REMOTE instead.
 */
define('SIMPLEPIE_FILE_SOURCE_REMOTE', NamespacedSimplePie::FILE_SOURCE_REMOTE);

/**
 * Local file source
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::FILE_SOURCE_LOCAL instead.
 */
define('SIMPLEPIE_FILE_SOURCE_LOCAL', NamespacedSimplePie::FILE_SOURCE_LOCAL);

/**
 * fsockopen() file source
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::FILE_SOURCE_FSOCKOPEN instead.
 */
define('SIMPLEPIE_FILE_SOURCE_FSOCKOPEN', NamespacedSimplePie::FILE_SOURCE_FSOCKOPEN);

/**
 * cURL file source
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::FILE_SOURCE_CURL instead.
 */
define('SIMPLEPIE_FILE_SOURCE_CURL', NamespacedSimplePie::FILE_SOURCE_CURL);

/**
 * file_get_contents() file source
 * @deprecated since SimplePie 1.7.0, use \SimplePie\SimplePie::FILE_SOURCE_FILE_GET_CONTENTS instead.
 */
define('SIMPLEPIE_FILE_SOURCE_FILE_GET_CONTENTS', NamespacedSimplePie::FILE_SOURCE_FILE_GET_CONTENTS);
