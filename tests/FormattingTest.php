<?php
/**
 * Test suite for wp-includes/formatting.php
 *
 * Captures the current behavior of all major formatting functions,
 * including edge cases and quirks.
 *
 * Run: composer test:formatting
 */

use PHPUnit\Framework\TestCase;

class FormattingTest extends TestCase {

    // ════════════════════════════════════════════════════════════════
    // zeroise()
    // ════════════════════════════════════════════════════════════════

    public function test_zeroise_pads_number() {
        $this->assertSame( '0010', zeroise( 10, 4 ) );
    }

    public function test_zeroise_no_padding_when_already_long_enough() {
        $this->assertSame( '5000', zeroise( 5000, 4 ) );
    }

    public function test_zeroise_single_digit() {
        $this->assertSame( '007', zeroise( 7, 3 ) );
    }

    public function test_zeroise_zero() {
        $this->assertSame( '000', zeroise( 0, 3 ) );
    }

    public function test_zeroise_threshold_one() {
        $this->assertSame( '5', zeroise( 5, 1 ) );
    }

    public function test_zeroise_negative_number() {
        // Edge case: negative numbers - sprintf pads the string representation.
        $this->assertSame( '-1', zeroise( -1, 2 ) );
    }

    public function test_zeroise_large_threshold() {
        $this->assertSame( '00000000001', zeroise( 1, 11 ) );
    }

    // ════════════════════════════════════════════════════════════════
    // backslashit()
    // ════════════════════════════════════════════════════════════════

    public function test_backslashit_adds_backslashes_before_letters() {
        $this->assertSame( '\\h\\e\\l\\l\\o', backslashit( 'hello' ) );
    }

    public function test_backslashit_leading_digit_gets_double_backslash() {
        $result = backslashit( '1abc' );
        $this->assertSame( '\\\\1\\a\\b\\c', $result );
    }

    public function test_backslashit_no_leading_digit_no_double_backslash() {
        $result = backslashit( 'a1b' );
        $this->assertSame( '\\a1\\b', $result );
    }

    public function test_backslashit_empty_string() {
        $this->assertSame( '', backslashit( '' ) );
    }

    public function test_backslashit_only_digits() {
        // Leading digit gets \\, rest are digits so no letter-escaping.
        $this->assertSame( '\\\\123', backslashit( '123' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // trailingslashit() / untrailingslashit()
    // ════════════════════════════════════════════════════════════════

    public function test_trailingslashit_adds_slash() {
        $this->assertSame( '/path/', trailingslashit( '/path' ) );
    }

    public function test_trailingslashit_does_not_double_slash() {
        $this->assertSame( '/path/', trailingslashit( '/path/' ) );
    }

    public function test_trailingslashit_replaces_backslash() {
        $this->assertSame( '/path/', trailingslashit( '/path\\' ) );
    }

    public function test_trailingslashit_multiple_trailing_slashes() {
        $this->assertSame( '/path/', trailingslashit( '/path///' ) );
    }

    public function test_untrailingslashit_removes_forward_slash() {
        $this->assertSame( '/path', untrailingslashit( '/path/' ) );
    }

    public function test_untrailingslashit_removes_backslash() {
        $this->assertSame( '/path', untrailingslashit( '/path\\' ) );
    }

    public function test_untrailingslashit_removes_multiple_slashes() {
        $this->assertSame( '/path', untrailingslashit( '/path//\\/' ) );
    }

    public function test_untrailingslashit_empty_string() {
        $this->assertSame( '', untrailingslashit( '' ) );
    }

    public function test_trailingslashit_empty_string() {
        $this->assertSame( '/', trailingslashit( '' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // sanitize_title_with_dashes()
    // ════════════════════════════════════════════════════════════════

    public function test_sanitize_title_with_dashes_basic() {
        $this->assertSame( 'hello-world', sanitize_title_with_dashes( 'Hello World' ) );
    }

    public function test_sanitize_title_with_dashes_special_chars() {
        $this->assertSame( 'hello-world', sanitize_title_with_dashes( 'Hello & World!' ) );
    }

    public function test_sanitize_title_with_dashes_multiple_spaces() {
        $this->assertSame( 'hello-world', sanitize_title_with_dashes( 'Hello    World' ) );
    }

    public function test_sanitize_title_with_dashes_html_tags_stripped() {
        $this->assertSame( 'hello-world', sanitize_title_with_dashes( '<b>Hello</b> World' ) );
    }

    public function test_sanitize_title_with_dashes_preserves_percent_encoded_octets() {
        $this->assertSame( 'hello-%c3%a9', sanitize_title_with_dashes( 'Hello %C3%A9' ) );
    }

    public function test_sanitize_title_with_dashes_removes_lone_percent() {
        $this->assertSame( 'hello-100-off', sanitize_title_with_dashes( 'Hello 100% off' ) );
    }

    public function test_sanitize_title_with_dashes_context_save_converts_entities() {
        // In 'save' context, &nbsp; becomes a dash.
        $result = sanitize_title_with_dashes( 'hello&nbsp;world', '', 'save' );
        $this->assertSame( 'hello-world', $result );
    }

    public function test_sanitize_title_with_dashes_context_save_converts_forward_slash() {
        $result = sanitize_title_with_dashes( 'hello/world', '', 'save' );
        $this->assertSame( 'hello-world', $result );
    }

    public function test_sanitize_title_with_dashes_removes_html_entities() {
        $this->assertSame( 'caf', sanitize_title_with_dashes( 'caf&eacute;' ) );
    }

    public function test_sanitize_title_with_dashes_dots_become_dashes() {
        $this->assertSame( 'file-name-txt', sanitize_title_with_dashes( 'file.name.txt' ) );
    }

    public function test_sanitize_title_with_dashes_trims_leading_trailing_dashes() {
        $this->assertSame( 'hello', sanitize_title_with_dashes( '--hello--' ) );
    }

    public function test_sanitize_title_with_dashes_empty_string() {
        $this->assertSame( '', sanitize_title_with_dashes( '' ) );
    }

    public function test_sanitize_title_with_dashes_underscores_preserved() {
        $this->assertSame( 'hello_world', sanitize_title_with_dashes( 'hello_world' ) );
    }

    public function test_sanitize_title_with_dashes_unicode_lowercase() {
        $result = sanitize_title_with_dashes( 'Café' );
        // Accented characters get percent-encoded, lowercased.
        $this->assertStringContainsString( 'caf', $result );
    }

    // ════════════════════════════════════════════════════════════════
    // sanitize_key()
    // ════════════════════════════════════════════════════════════════

    public function test_sanitize_key_lowercases() {
        $this->assertSame( 'hello_world', sanitize_key( 'Hello_World' ) );
    }

    public function test_sanitize_key_strips_special_chars() {
        $this->assertSame( 'helloworld', sanitize_key( 'Hello World!' ) );
    }

    public function test_sanitize_key_allows_dashes_and_underscores() {
        $this->assertSame( 'my-key_name', sanitize_key( 'My-Key_Name' ) );
    }

    public function test_sanitize_key_allows_numbers() {
        $this->assertSame( 'key123', sanitize_key( 'Key123' ) );
    }

    public function test_sanitize_key_empty_string() {
        $this->assertSame( '', sanitize_key( '' ) );
    }

    public function test_sanitize_key_non_scalar_returns_empty() {
        $this->assertSame( '', sanitize_key( array( 'not', 'a', 'key' ) ) );
    }

    public function test_sanitize_key_null_returns_empty() {
        $this->assertSame( '', sanitize_key( null ) );
    }

    public function test_sanitize_key_strips_unicode() {
        $this->assertSame( 'caf', sanitize_key( 'Café' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // sanitize_html_class()
    // ════════════════════════════════════════════════════════════════

    public function test_sanitize_html_class_basic() {
        $this->assertSame( 'my-class', sanitize_html_class( 'my-class' ) );
    }

    public function test_sanitize_html_class_strips_special_chars() {
        $this->assertSame( 'myclass', sanitize_html_class( 'my class!' ) );
    }

    public function test_sanitize_html_class_preserves_case() {
        $this->assertSame( 'MyClass', sanitize_html_class( 'MyClass' ) );
    }

    public function test_sanitize_html_class_strips_percent_encoded() {
        $this->assertSame( 'hello', sanitize_html_class( 'he%20llo' ) );
    }

    public function test_sanitize_html_class_fallback_used_when_empty() {
        $this->assertSame( 'fallback', sanitize_html_class( '!!!', 'fallback' ) );
    }

    public function test_sanitize_html_class_fallback_also_sanitized() {
        $this->assertSame( 'fall-back', sanitize_html_class( '!!!', 'fall-back' ) );
    }

    public function test_sanitize_html_class_empty_string_no_fallback() {
        $this->assertSame( '', sanitize_html_class( '' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // sanitize_locale_name()
    // ════════════════════════════════════════════════════════════════

    public function test_sanitize_locale_name_basic() {
        $this->assertSame( 'en_US', sanitize_locale_name( 'en_US' ) );
    }

    public function test_sanitize_locale_name_strips_special() {
        $this->assertSame( 'enUS', sanitize_locale_name( 'en.US;evil' ) );
    }

    public function test_sanitize_locale_name_allows_dashes() {
        $this->assertSame( 'zh-CN', sanitize_locale_name( 'zh-CN' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // sanitize_user()
    // ════════════════════════════════════════════════════════════════

    public function test_sanitize_user_basic() {
        $this->assertSame( 'john', sanitize_user( 'john' ) );
    }

    public function test_sanitize_user_strips_tags() {
        $this->assertSame( 'john', sanitize_user( '<b>john</b>' ) );
    }

    public function test_sanitize_user_strips_percent_encoded() {
        $this->assertSame( 'john', sanitize_user( 'jo%68n' ) );
    }

    public function test_sanitize_user_strict_only_allows_alphanumeric() {
        $this->assertSame( 'john.doe', sanitize_user( 'john.doe$!', true ) );
    }

    public function test_sanitize_user_strict_allows_at_sign() {
        $this->assertSame( 'user@domain', sanitize_user( 'user@domain', true ) );
    }

    public function test_sanitize_user_consolidates_whitespace() {
        $this->assertSame( 'john doe', sanitize_user( "john  \t doe" ) );
    }

    public function test_sanitize_user_trims() {
        $this->assertSame( 'john', sanitize_user( '  john  ' ) );
    }

    public function test_sanitize_user_strips_html_entities() {
        $this->assertSame( 'john doe', sanitize_user( 'john&amp; doe' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // sanitize_sql_orderby()
    // ════════════════════════════════════════════════════════════════

    public function test_sanitize_sql_orderby_simple_column() {
        $this->assertSame( 'post_date', sanitize_sql_orderby( 'post_date' ) );
    }

    public function test_sanitize_sql_orderby_with_direction() {
        $this->assertSame( 'post_date ASC', sanitize_sql_orderby( 'post_date ASC' ) );
    }

    public function test_sanitize_sql_orderby_multiple_columns() {
        $this->assertSame( 'col1 ASC, col2 DESC', sanitize_sql_orderby( 'col1 ASC, col2 DESC' ) );
    }

    public function test_sanitize_sql_orderby_rand() {
        $this->assertSame( 'RAND()', sanitize_sql_orderby( 'RAND()' ) );
    }

    public function test_sanitize_sql_orderby_backtick_columns() {
        $this->assertSame( '`post_date` ASC', sanitize_sql_orderby( '`post_date` ASC' ) );
    }

    public function test_sanitize_sql_orderby_injection_returns_false() {
        $this->assertFalse( sanitize_sql_orderby( 'post_date; DROP TABLE' ) );
    }

    public function test_sanitize_sql_orderby_subquery_returns_false() {
        $this->assertFalse( sanitize_sql_orderby( '(SELECT 1)' ) );
    }

    public function test_sanitize_sql_orderby_empty_string() {
        $this->assertFalse( sanitize_sql_orderby( '' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // is_email()
    // ════════════════════════════════════════════════════════════════

    public function test_is_email_valid() {
        $this->assertSame( 'user@example.com', is_email( 'user@example.com' ) );
    }

    public function test_is_email_too_short() {
        $this->assertFalse( is_email( 'a@b.c' ) );
    }

    public function test_is_email_no_at() {
        $this->assertFalse( is_email( 'userexample.com' ) );
    }

    public function test_is_email_invalid_local_chars() {
        $this->assertFalse( is_email( 'us er@example.com' ) );
    }

    public function test_is_email_consecutive_dots_in_domain() {
        $this->assertFalse( is_email( 'user@example..com' ) );
    }

    public function test_is_email_leading_dot_in_domain() {
        $this->assertFalse( is_email( 'user@.example.com' ) );
    }

    public function test_is_email_no_dot_in_domain() {
        $this->assertFalse( is_email( 'user@localhost' ) );
    }

    public function test_is_email_leading_hyphen_in_subdomain() {
        $this->assertFalse( is_email( 'user@-example.com' ) );
    }

    public function test_is_email_special_local_chars_allowed() {
        // The + and . are allowed in the local part.
        $this->assertSame( 'user+tag@example.com', is_email( 'user+tag@example.com' ) );
    }

    public function test_is_email_local_with_dots() {
        $this->assertSame( 'first.last@example.com', is_email( 'first.last@example.com' ) );
    }

    public function test_is_email_at_in_first_position_fails() {
        // @ at position 0 means nothing before the @.
        $this->assertFalse( is_email( '@example.com' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // sanitize_email()
    // ════════════════════════════════════════════════════════════════

    public function test_sanitize_email_valid() {
        $this->assertSame( 'user@example.com', sanitize_email( 'user@example.com' ) );
    }

    public function test_sanitize_email_strips_invalid_chars() {
        // Spaces in local part get stripped, not rejected.
        $result = sanitize_email( 'us er@example.com' );
        $this->assertSame( 'user@example.com', $result );
    }

    public function test_sanitize_email_too_short() {
        $this->assertSame( '', sanitize_email( 'a@b' ) );
    }

    public function test_sanitize_email_no_at() {
        $this->assertSame( '', sanitize_email( 'noatsign' ) );
    }

    public function test_sanitize_email_consecutive_dots_removed_from_domain() {
        // consecutive dots are removed; if that empties the domain, returns ''.
        $this->assertSame( '', sanitize_email( 'user@..' ) );
    }

    public function test_sanitize_email_domain_hyphens_trimmed() {
        $result = sanitize_email( 'user@-example-.com' );
        $this->assertSame( 'user@example.com', $result );
    }

    public function test_sanitize_email_preserves_plus_in_local() {
        $this->assertSame( 'user+tag@example.com', sanitize_email( 'user+tag@example.com' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // wp_specialchars_decode()
    // ════════════════════════════════════════════════════════════════

    public function test_wp_specialchars_decode_entities() {
        $this->assertSame( '<>&', wp_specialchars_decode( '&lt;&gt;&amp;' ) );
    }

    public function test_wp_specialchars_decode_double_quotes() {
        $this->assertSame( '"hello"', wp_specialchars_decode( '&quot;hello&quot;', ENT_COMPAT ) );
    }

    public function test_wp_specialchars_decode_single_quotes() {
        $this->assertSame( "'hello'", wp_specialchars_decode( '&#039;hello&#039;', 'single' ) );
    }

    public function test_wp_specialchars_decode_both_quotes() {
        $this->assertSame( '"\'hello\'"', wp_specialchars_decode( '&quot;&#039;hello&#039;&quot;', ENT_QUOTES ) );
    }

    public function test_wp_specialchars_decode_no_entities() {
        $this->assertSame( 'hello world', wp_specialchars_decode( 'hello world' ) );
    }

    public function test_wp_specialchars_decode_empty_string() {
        $this->assertSame( '', wp_specialchars_decode( '' ) );
    }

    public function test_wp_specialchars_decode_zero_padded_entities() {
        // &#0060; should be decoded to <
        $this->assertSame( '<', wp_specialchars_decode( '&#0060;' ) );
    }

    public function test_wp_specialchars_decode_hex_entity_case_insensitive() {
        // &#X26; should be decoded to &
        $this->assertSame( '&', wp_specialchars_decode( '&#X26;' ) );
    }

    public function test_wp_specialchars_decode_no_ampersand_returns_fast() {
        $text = 'plain text no ampersand';
        $this->assertSame( $text, wp_specialchars_decode( $text ) );
    }

    // Edge case: non-standard quote_style becomes ENT_QUOTES.
    public function test_wp_specialchars_decode_unknown_quote_style() {
        $result = wp_specialchars_decode( '&quot;&#039;', 999 );
        $this->assertSame( '"\'', $result );
    }

    // ════════════════════════════════════════════════════════════════
    // _wp_specialchars()
    // ════════════════════════════════════════════════════════════════

    public function test_wp_specialchars_encodes_ampersand() {
        $this->assertSame( '&amp;', _wp_specialchars( '&', ENT_NOQUOTES ) );
    }

    public function test_wp_specialchars_encodes_lt_gt() {
        $this->assertSame( '&lt;b&gt;', _wp_specialchars( '<b>', ENT_NOQUOTES ) );
    }

    public function test_wp_specialchars_ent_compat_encodes_double_quotes() {
        $this->assertStringContainsString( '&quot;', _wp_specialchars( '"', ENT_COMPAT ) );
    }

    public function test_wp_specialchars_ent_quotes_encodes_both() {
        $result = _wp_specialchars( '"\'', ENT_QUOTES );
        $this->assertStringContainsString( '&quot;', $result );
        $this->assertStringContainsString( '&#039;', $result );
    }

    public function test_wp_specialchars_empty_string() {
        $this->assertSame( '', _wp_specialchars( '' ) );
    }

    public function test_wp_specialchars_no_special_chars() {
        $this->assertSame( 'hello', _wp_specialchars( 'hello' ) );
    }

    public function test_wp_specialchars_single_string_quote_style() {
        // 'single' quote style encodes only single quotes.
        $result = _wp_specialchars( "it's a \"test\"", 'single' );
        $this->assertStringContainsString( '&#039;', $result );
    }

    public function test_wp_specialchars_double_string_quote_style() {
        $result = _wp_specialchars( '"test"', 'double' );
        $this->assertStringContainsString( '&quot;', $result );
    }

    public function test_wp_specialchars_empty_quote_style_treated_as_noquotes() {
        // Empty or falsy quote_style => ENT_NOQUOTES.
        $result = _wp_specialchars( '"hello"', 0 );
        $this->assertSame( '"hello"', $result );
    }

    // ════════════════════════════════════════════════════════════════
    // wp_check_invalid_utf8()
    // ════════════════════════════════════════════════════════════════

    public function test_wp_check_invalid_utf8_valid_string() {
        $this->assertSame( 'hello', wp_check_invalid_utf8( 'hello' ) );
    }

    public function test_wp_check_invalid_utf8_valid_utf8() {
        $this->assertSame( 'café', wp_check_invalid_utf8( 'café' ) );
    }

    public function test_wp_check_invalid_utf8_empty_string() {
        $this->assertSame( '', wp_check_invalid_utf8( '' ) );
    }

    public function test_wp_check_invalid_utf8_invalid_returns_empty() {
        $invalid = "hello \xC0 world";
        $this->assertSame( '', wp_check_invalid_utf8( $invalid ) );
    }

    public function test_wp_check_invalid_utf8_strip_replaces_with_replacement_char() {
        $invalid = "hello \xC0 world";
        $result  = wp_check_invalid_utf8( $invalid, true );
        // When stripping, invalid bytes replaced by U+FFFD (�).
        $this->assertStringContainsString( 'hello', $result );
        $this->assertStringContainsString( 'world', $result );
    }

    // ════════════════════════════════════════════════════════════════
    // force_balance_tags()
    // ════════════════════════════════════════════════════════════════

    public function test_force_balance_tags_closes_unclosed() {
        $this->assertSame( '<b>hello</b>', force_balance_tags( '<b>hello' ) );
    }

    public function test_force_balance_tags_removes_extra_closing() {
        $this->assertSame( 'hello', force_balance_tags( 'hello</b>' ) );
    }

    public function test_force_balance_tags_already_balanced() {
        $this->assertSame( '<div>hello</div>', force_balance_tags( '<div>hello</div>' ) );
    }

    public function test_force_balance_tags_nested() {
        $this->assertSame( '<div><b>hello</b></div>', force_balance_tags( '<div><b>hello</div>' ) );
    }

    public function test_force_balance_tags_self_closing_br() {
        $result = force_balance_tags( '<br>' );
        $this->assertSame( '<br />', $result );
    }

    public function test_force_balance_tags_self_closing_img() {
        $result = force_balance_tags( '<img src="test.jpg">' );
        $this->assertSame( '<img src="test.jpg" />', $result );
    }

    public function test_force_balance_tags_love_heart_edge_case() {
        // <3 should not be treated as a tag.
        $result = force_balance_tags( 'I <3 WP' );
        $this->assertSame( 'I &lt;3 WP', $result );
    }

    public function test_force_balance_tags_empty_string() {
        $this->assertSame( '', force_balance_tags( '' ) );
    }

    public function test_force_balance_tags_non_nestable_same_tag() {
        // Two consecutive <b> tags: the second should close the first.
        $result = force_balance_tags( '<b>one<b>two</b>' );
        $this->assertSame( '<b>one</b><b>two</b>', $result );
    }

    public function test_force_balance_tags_nestable_div() {
        // <div> is nestable, so two <div> should stay nested.
        $result = force_balance_tags( '<div><div>inner</div></div>' );
        $this->assertSame( '<div><div>inner</div></div>', $result );
    }

    public function test_force_balance_tags_custom_element() {
        // Custom elements (with hyphen) should be balanced.
        $result = force_balance_tags( '<my-component>hello' );
        $this->assertSame( '<my-component>hello</my-component>', $result );
    }

    // ════════════════════════════════════════════════════════════════
    // convert_chars()
    // ════════════════════════════════════════════════════════════════

    public function test_convert_chars_lone_ampersand() {
        $this->assertSame( '&#038; hello', convert_chars( '& hello' ) );
    }

    public function test_convert_chars_preserves_valid_entity() {
        $this->assertSame( '&amp; hello', convert_chars( '&amp; hello' ) );
    }

    public function test_convert_chars_no_ampersand() {
        $this->assertSame( 'hello world', convert_chars( 'hello world' ) );
    }

    public function test_convert_chars_preserves_numeric_entity() {
        $this->assertSame( '&#8217; test', convert_chars( '&#8217; test' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // _deep_replace()
    // ════════════════════════════════════════════════════════════════

    public function test_deep_replace_basic() {
        $this->assertSame( 'hello world', _deep_replace( 'foo', 'hello fooworld' ) );
    }

    public function test_deep_replace_nested_pattern() {
        // %0%0%0DDD with search=%0D => first pass leaves %0%0DD, second %0D, third ''
        $this->assertSame( '', _deep_replace( '%0D', '%0%0%0DDD' ) );
    }

    public function test_deep_replace_array_search() {
        $this->assertSame( 'hello', _deep_replace( array( '%0d', '%0a' ), 'hel%0dlo%0a' ) );
    }

    public function test_deep_replace_no_match() {
        $this->assertSame( 'hello', _deep_replace( 'xyz', 'hello' ) );
    }

    public function test_deep_replace_empty_subject() {
        $this->assertSame( '', _deep_replace( 'a', '' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // normalize_whitespace()
    // ════════════════════════════════════════════════════════════════

    public function test_normalize_whitespace_collapses_spaces() {
        $this->assertSame( 'hello world', normalize_whitespace( 'hello   world' ) );
    }

    public function test_normalize_whitespace_converts_cr_to_lf() {
        $this->assertSame( "hello\nworld", normalize_whitespace( "hello\r\nworld" ) );
    }

    public function test_normalize_whitespace_collapses_newlines() {
        $this->assertSame( "hello\nworld", normalize_whitespace( "hello\n\n\nworld" ) );
    }

    public function test_normalize_whitespace_tabs_become_spaces() {
        $this->assertSame( 'hello world', normalize_whitespace( "hello\tworld" ) );
    }

    public function test_normalize_whitespace_trims() {
        $this->assertSame( 'hello', normalize_whitespace( '  hello  ' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // wp_strip_all_tags()
    // ════════════════════════════════════════════════════════════════

    public function test_wp_strip_all_tags_basic() {
        $this->assertSame( 'hello world', wp_strip_all_tags( '<p>hello <b>world</b></p>' ) );
    }

    public function test_wp_strip_all_tags_strips_script_contents() {
        $this->assertSame( 'before after', wp_strip_all_tags( 'before<script>alert("xss")</script> after' ) );
    }

    public function test_wp_strip_all_tags_strips_style_contents() {
        $this->assertSame( 'text', wp_strip_all_tags( 'text<style>.foo{color:red}</style>' ) );
    }

    public function test_wp_strip_all_tags_remove_breaks() {
        $this->assertSame( 'hello world', wp_strip_all_tags( "hello\nworld", true ) );
    }

    public function test_wp_strip_all_tags_null_returns_empty() {
        $this->assertSame( '', wp_strip_all_tags( null ) );
    }

    public function test_wp_strip_all_tags_empty_string() {
        $this->assertSame( '', wp_strip_all_tags( '' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // sanitize_text_field()
    // ════════════════════════════════════════════════════════════════

    public function test_sanitize_text_field_basic() {
        $this->assertSame( 'hello world', sanitize_text_field( 'hello world' ) );
    }

    public function test_sanitize_text_field_strips_tags() {
        $this->assertSame( 'hello', sanitize_text_field( '<b>hello</b>' ) );
    }

    public function test_sanitize_text_field_strips_newlines() {
        $this->assertSame( 'hello world', sanitize_text_field( "hello\nworld" ) );
    }

    public function test_sanitize_text_field_trims() {
        $this->assertSame( 'hello', sanitize_text_field( '  hello  ' ) );
    }

    public function test_sanitize_text_field_strips_percent_encoded() {
        $this->assertSame( 'hello', sanitize_text_field( 'he%6Clo' ) );
    }

    public function test_sanitize_text_field_array_returns_empty() {
        $this->assertSame( '', sanitize_text_field( array() ) );
    }

    public function test_sanitize_text_field_object_returns_empty() {
        $this->assertSame( '', sanitize_text_field( new \stdClass() ) );
    }

    // ════════════════════════════════════════════════════════════════
    // sanitize_textarea_field()
    // ════════════════════════════════════════════════════════════════

    public function test_sanitize_textarea_field_preserves_newlines() {
        $this->assertSame( "hello\nworld", sanitize_textarea_field( "hello\nworld" ) );
    }

    public function test_sanitize_textarea_field_strips_tags() {
        $this->assertSame( 'hello', sanitize_textarea_field( '<b>hello</b>' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // sanitize_mime_type()
    // ════════════════════════════════════════════════════════════════

    public function test_sanitize_mime_type_basic() {
        $this->assertSame( 'image/jpeg', sanitize_mime_type( 'image/jpeg' ) );
    }

    public function test_sanitize_mime_type_strips_special_chars() {
        $this->assertSame( 'image/jpeg', sanitize_mime_type( 'image/jpeg; evil' ) );
    }

    public function test_sanitize_mime_type_allows_plus() {
        $this->assertSame( 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet+xml',
            sanitize_mime_type( 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet+xml' ) );
    }

    public function test_sanitize_mime_type_allows_star() {
        $this->assertSame( 'text/*', sanitize_mime_type( 'text/*' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // sanitize_hex_color()
    // ════════════════════════════════════════════════════════════════

    public function test_sanitize_hex_color_six_digit() {
        $this->assertSame( '#ff0000', sanitize_hex_color( '#ff0000' ) );
    }

    public function test_sanitize_hex_color_three_digit() {
        $this->assertSame( '#f00', sanitize_hex_color( '#f00' ) );
    }

    public function test_sanitize_hex_color_uppercase() {
        $this->assertSame( '#FF0000', sanitize_hex_color( '#FF0000' ) );
    }

    public function test_sanitize_hex_color_empty_string() {
        $this->assertSame( '', sanitize_hex_color( '' ) );
    }

    public function test_sanitize_hex_color_no_hash_returns_null() {
        $this->assertNull( sanitize_hex_color( 'ff0000' ) );
    }

    public function test_sanitize_hex_color_invalid_returns_null() {
        $this->assertNull( sanitize_hex_color( '#gg0000' ) );
    }

    public function test_sanitize_hex_color_too_long_returns_null() {
        $this->assertNull( sanitize_hex_color( '#ff00001' ) );
    }

    public function test_sanitize_hex_color_four_digits_returns_null() {
        $this->assertNull( sanitize_hex_color( '#ff00' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // sanitize_hex_color_no_hash()
    // ════════════════════════════════════════════════════════════════

    public function test_sanitize_hex_color_no_hash_valid() {
        $this->assertSame( 'ff0000', sanitize_hex_color_no_hash( 'ff0000' ) );
    }

    public function test_sanitize_hex_color_no_hash_strips_hash() {
        $this->assertSame( 'ff0000', sanitize_hex_color_no_hash( '#ff0000' ) );
    }

    public function test_sanitize_hex_color_no_hash_empty() {
        $this->assertSame( '', sanitize_hex_color_no_hash( '' ) );
    }

    public function test_sanitize_hex_color_no_hash_invalid() {
        $this->assertNull( sanitize_hex_color_no_hash( 'xyz' ) );
    }

    public function test_sanitize_hex_color_no_hash_three_digits() {
        $this->assertSame( 'f00', sanitize_hex_color_no_hash( 'f00' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // maybe_hash_hex_color()
    // ════════════════════════════════════════════════════════════════

    public function test_maybe_hash_hex_color_adds_hash() {
        $this->assertSame( '#ff0000', maybe_hash_hex_color( 'ff0000' ) );
    }

    public function test_maybe_hash_hex_color_already_hashed() {
        $this->assertSame( '#ff0000', maybe_hash_hex_color( '#ff0000' ) );
    }

    public function test_maybe_hash_hex_color_invalid_returned_as_is() {
        $this->assertSame( 'not-a-color', maybe_hash_hex_color( 'not-a-color' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // wp_make_link_relative()
    // ════════════════════════════════════════════════════════════════

    public function test_wp_make_link_relative_basic() {
        $this->assertSame( '/page', wp_make_link_relative( 'http://example.com/page' ) );
    }

    public function test_wp_make_link_relative_https() {
        $this->assertSame( '/page', wp_make_link_relative( 'https://example.com/page' ) );
    }

    public function test_wp_make_link_relative_root() {
        $this->assertSame( '/', wp_make_link_relative( 'http://example.com/' ) );
    }

    public function test_wp_make_link_relative_no_path() {
        $this->assertSame( '', wp_make_link_relative( 'http://example.com' ) );
    }

    public function test_wp_make_link_relative_protocol_relative() {
        $this->assertSame( '/page', wp_make_link_relative( '//example.com/page' ) );
    }

    public function test_wp_make_link_relative_already_relative() {
        $this->assertSame( '/page', wp_make_link_relative( '/page' ) );
    }

    public function test_wp_make_link_relative_with_query() {
        $this->assertSame( '/page?foo=bar', wp_make_link_relative( 'http://example.com/page?foo=bar' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // url_shorten()
    // ════════════════════════════════════════════════════════════════

    public function test_url_shorten_strips_protocol() {
        $this->assertSame( 'example.com', url_shorten( 'https://example.com/' ) );
    }

    public function test_url_shorten_strips_www() {
        $this->assertSame( 'example.com', url_shorten( 'http://www.example.com/' ) );
    }

    public function test_url_shorten_truncates_long_urls() {
        $long_url = 'https://example.com/a-very-long-path-that-exceeds-the-limit';
        $result   = url_shorten( $long_url );
        $this->assertStringEndsWith( '&hellip;', $result );
        $this->assertLessThanOrEqual( 35, strlen( str_replace( '&hellip;', '...', $result ) ) );
    }

    public function test_url_shorten_custom_length() {
        $result = url_shorten( 'https://example.com/page', 15 );
        $this->assertStringEndsWith( '&hellip;', $result );
    }

    public function test_url_shorten_short_enough_no_truncation() {
        $this->assertSame( 'ex.co', url_shorten( 'https://ex.co/' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // tag_escape()
    // ════════════════════════════════════════════════════════════════

    public function test_tag_escape_basic() {
        $this->assertSame( 'div', tag_escape( 'div' ) );
    }

    public function test_tag_escape_uppercase_lowered() {
        $this->assertSame( 'div', tag_escape( 'DIV' ) );
    }

    public function test_tag_escape_strips_invalid_chars() {
        $this->assertSame( 'div', tag_escape( 'div<script>' ) );
    }

    public function test_tag_escape_allows_hyphens() {
        $this->assertSame( 'my-element', tag_escape( 'my-element' ) );
    }

    public function test_tag_escape_allows_colons() {
        $this->assertSame( 'xml:tag', tag_escape( 'xml:tag' ) );
    }

    public function test_tag_escape_allows_underscores() {
        $this->assertSame( 'my_tag', tag_escape( 'my_tag' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // wp_slash() / wp_unslash()
    // ════════════════════════════════════════════════════════════════

    public function test_wp_slash_string() {
        $this->assertSame( "it\'s", wp_slash( "it's" ) );
    }

    public function test_wp_slash_array() {
        $result = wp_slash( array( "it's", 'hello' ) );
        $this->assertSame( array( "it\'s", 'hello' ), $result );
    }

    public function test_wp_slash_non_string_untouched() {
        $this->assertSame( 42, wp_slash( 42 ) );
        $this->assertTrue( wp_slash( true ) );
        $this->assertNull( wp_slash( null ) );
    }

    public function test_wp_unslash_string() {
        $this->assertSame( "it's", wp_unslash( "it\'s" ) );
    }

    public function test_wp_unslash_array() {
        $result = wp_unslash( array( "it\'s", 'hello' ) );
        $this->assertSame( array( "it's", 'hello' ), $result );
    }

    public function test_wp_slash_then_unslash_roundtrip() {
        $original = "O'Reilly \"Books\" \\path";
        $this->assertSame( $original, wp_unslash( wp_slash( $original ) ) );
    }

    // ════════════════════════════════════════════════════════════════
    // stripslashes_deep()
    // ════════════════════════════════════════════════════════════════

    public function test_stripslashes_deep_string() {
        $this->assertSame( "it's", stripslashes_deep( "it\'s" ) );
    }

    public function test_stripslashes_deep_nested_array() {
        $input  = array( "it\'s", array( "she\'s" ) );
        $expect = array( "it's", array( "she's" ) );
        $this->assertSame( $expect, stripslashes_deep( $input ) );
    }

    public function test_stripslashes_deep_non_string_in_array() {
        $input  = array( 42, true, null );
        $this->assertSame( $input, stripslashes_deep( $input ) );
    }

    public function test_stripslashes_deep_object() {
        $obj       = new \stdClass();
        $obj->name = "it\'s";
        $result    = stripslashes_deep( $obj );
        $this->assertSame( "it's", $result->name );
    }

    // ════════════════════════════════════════════════════════════════
    // map_deep()
    // ════════════════════════════════════════════════════════════════

    public function test_map_deep_scalar() {
        $this->assertSame( 'HELLO', map_deep( 'hello', 'strtoupper' ) );
    }

    public function test_map_deep_array() {
        $result = map_deep( array( 'hello', 'world' ), 'strtoupper' );
        $this->assertSame( array( 'HELLO', 'WORLD' ), $result );
    }

    public function test_map_deep_nested_array() {
        $result = map_deep( array( 'a', array( 'b', 'c' ) ), 'strtoupper' );
        $this->assertSame( array( 'A', array( 'B', 'C' ) ), $result );
    }

    public function test_map_deep_object() {
        $obj       = new \stdClass();
        $obj->name = 'hello';
        $result    = map_deep( $obj, 'strtoupper' );
        $this->assertSame( 'HELLO', $result->name );
    }

    // ════════════════════════════════════════════════════════════════
    // iso8601_timezone_to_offset()
    // ════════════════════════════════════════════════════════════════

    public function test_iso8601_timezone_z_is_zero() {
        $this->assertSame( 0, iso8601_timezone_to_offset( 'Z' ) );
    }

    public function test_iso8601_timezone_positive_offset() {
        // +0100 = 1 hour = 3600 seconds.
        $this->assertEquals( 3600, iso8601_timezone_to_offset( '+0100' ) );
    }

    public function test_iso8601_timezone_negative_offset() {
        // -0530 = -(5h + 30m) = -(5*3600 + 30*60) = -19800... but the code has a quirk.
        // Actually the code does: $minutes = (int) substr( $timezone, 3, 4 ) / 60
        // substr('+0530', 3, 4) = '30', so minutes = 30/60 = 0.5.
        // offset = -1 * 3600 * (5 + 0.5) = -1 * 3600 * 5.5 = -19800.
        $this->assertEquals( -19800, iso8601_timezone_to_offset( '-0530' ) );
    }

    public function test_iso8601_timezone_positive_no_minutes() {
        // +0200 = 2 hours = 7200.
        $this->assertEquals( 7200, iso8601_timezone_to_offset( '+0200' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // wp_html_excerpt()
    // ════════════════════════════════════════════════════════════════

    public function test_wp_html_excerpt_basic() {
        $this->assertSame( 'hello', wp_html_excerpt( 'hello world', 5 ) );
    }

    public function test_wp_html_excerpt_strips_tags() {
        $this->assertSame( 'hello', wp_html_excerpt( '<b>hello</b> world', 5 ) );
    }

    public function test_wp_html_excerpt_with_more() {
        $this->assertSame( 'hello...', wp_html_excerpt( 'hello world', 5, '...' ) );
    }

    public function test_wp_html_excerpt_full_string_no_more() {
        $this->assertSame( 'hi', wp_html_excerpt( 'hi', 10 ) );
    }

    public function test_wp_html_excerpt_strips_partial_entity() {
        // If the excerpt ends in the middle of an entity like &amp, strip it.
        $this->assertSame( 'test', wp_html_excerpt( 'test&amp;more', 8 ) );
    }

    // ════════════════════════════════════════════════════════════════
    // wp_basename()
    // ════════════════════════════════════════════════════════════════

    public function test_wp_basename_basic() {
        $this->assertSame( 'file.txt', wp_basename( '/path/to/file.txt' ) );
    }

    public function test_wp_basename_with_suffix() {
        $this->assertSame( 'file', wp_basename( '/path/to/file.txt', '.txt' ) );
    }

    public function test_wp_basename_unicode_path() {
        $this->assertSame( 'café.txt', wp_basename( '/path/café.txt' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // capital_P_dangit()
    // ════════════════════════════════════════════════════════════════

    public function test_capital_p_dangit_corrects_wordpress() {
        // Outside of the_title/wp_title filter context, replacements are position-sensitive.
        $this->assertSame( ' WordPress', capital_P_dangit( ' Wordpress' ) );
    }

    public function test_capital_p_dangit_preserves_correct_spelling() {
        $this->assertSame( ' WordPress rocks', capital_P_dangit( ' WordPress rocks' ) );
    }

    public function test_capital_p_dangit_after_gt() {
        $this->assertSame( '>WordPress', capital_P_dangit( '>Wordpress' ) );
    }

    public function test_capital_p_dangit_after_paren() {
        $this->assertSame( '(WordPress', capital_P_dangit( '(Wordpress' ) );
    }

    public function test_capital_p_dangit_mid_word_not_replaced() {
        // "MyWordpress" - no space/bracket before it, so not replaced.
        $this->assertSame( 'MyWordpress', capital_P_dangit( 'MyWordpress' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // get_url_in_content()
    // ════════════════════════════════════════════════════════════════

    public function test_get_url_in_content_basic() {
        $html = '<a href="http://example.com">Link</a>';
        $this->assertSame( 'http://example.com', get_url_in_content( $html ) );
    }

    public function test_get_url_in_content_multiple_links_returns_first() {
        $html = '<a href="http://first.com">1</a><a href="http://second.com">2</a>';
        $this->assertSame( 'http://first.com', get_url_in_content( $html ) );
    }

    public function test_get_url_in_content_no_link() {
        $this->assertFalse( get_url_in_content( '<p>no link here</p>' ) );
    }

    public function test_get_url_in_content_empty_string() {
        $this->assertFalse( get_url_in_content( '' ) );
    }

    public function test_get_url_in_content_empty_href() {
        $this->assertFalse( get_url_in_content( '<a href="">empty</a>' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // links_add_target()
    // ════════════════════════════════════════════════════════════════

    public function test_links_add_target_basic() {
        $html   = '<a href="http://example.com">Link</a>';
        $result = links_add_target( $html );
        $this->assertStringContainsString( 'target="_blank"', $result );
    }

    public function test_links_add_target_replaces_existing() {
        $html   = '<a href="http://example.com" target="_self">Link</a>';
        $result = links_add_target( $html );
        $this->assertStringContainsString( 'target="_blank"', $result );
        $this->assertStringNotContainsString( '_self', $result );
    }

    public function test_links_add_target_custom_target() {
        $html   = '<a href="http://example.com">Link</a>';
        $result = links_add_target( $html, '_self' );
        $this->assertStringContainsString( 'target="_self"', $result );
    }

    // ════════════════════════════════════════════════════════════════
    // sanitize_file_name()
    // ════════════════════════════════════════════════════════════════

    public function test_sanitize_file_name_basic() {
        $this->assertSame( 'file.txt', sanitize_file_name( 'file.txt' ) );
    }

    public function test_sanitize_file_name_spaces_become_dashes() {
        $this->assertSame( 'my-file.txt', sanitize_file_name( 'my file.txt' ) );
    }

    public function test_sanitize_file_name_strips_special_chars() {
        $result = sanitize_file_name( 'file?name[1].txt' );
        $this->assertStringNotContainsString( '?', $result );
        $this->assertStringNotContainsString( '[', $result );
    }

    public function test_sanitize_file_name_trims_dots_dashes_underscores() {
        $this->assertSame( 'file.txt', sanitize_file_name( '...file.txt...' ) );
    }

    public function test_sanitize_file_name_consecutive_dashes_collapsed() {
        $this->assertSame( 'a-b.txt', sanitize_file_name( 'a---b.txt' ) );
    }

    public function test_sanitize_file_name_removes_accents() {
        $result = sanitize_file_name( 'café.txt' );
        $this->assertSame( 'cafe.txt', $result );
    }

    // ════════════════════════════════════════════════════════════════
    // remove_accents()
    // ════════════════════════════════════════════════════════════════

    public function test_remove_accents_basic_latin() {
        $this->assertSame( 'cafe', remove_accents( 'café' ) );
    }

    public function test_remove_accents_uppercase() {
        $this->assertSame( 'A', remove_accents( 'À' ) );
    }

    public function test_remove_accents_german_umlaut() {
        $this->assertSame( 'A', remove_accents( 'Ä' ) );
    }

    public function test_remove_accents_ligatures() {
        $this->assertSame( 'AE', remove_accents( 'Æ' ) );
        $this->assertSame( 'ae', remove_accents( 'æ' ) );
    }

    public function test_remove_accents_thorn() {
        $this->assertSame( 'TH', remove_accents( 'Þ' ) );
        $this->assertSame( 'th', remove_accents( 'þ' ) );
    }

    public function test_remove_accents_ascii_unchanged() {
        $this->assertSame( 'Hello World 123', remove_accents( 'Hello World 123' ) );
    }

    public function test_remove_accents_empty_string() {
        $this->assertSame( '', remove_accents( '' ) );
    }

    public function test_remove_accents_eszett() {
        $this->assertSame( 's', remove_accents( 'ß' ) );
    }

    public function test_remove_accents_oe_ligature() {
        $this->assertSame( 'OE', remove_accents( 'Œ' ) );
        $this->assertSame( 'oe', remove_accents( 'œ' ) );
    }

    public function test_remove_accents_n_tilde() {
        $this->assertSame( 'n', remove_accents( 'ñ' ) );
        $this->assertSame( 'N', remove_accents( 'Ñ' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // convert_invalid_entities()
    // ════════════════════════════════════════════════════════════════

    public function test_convert_invalid_entities_euro() {
        $this->assertSame( '&#8364;', convert_invalid_entities( '&#128;' ) );
    }

    public function test_convert_invalid_entities_unchanged_valid() {
        $this->assertSame( '&#200;', convert_invalid_entities( '&#200;' ) );
    }

    public function test_convert_invalid_entities_129_stripped() {
        $this->assertSame( '', convert_invalid_entities( '&#129;' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // htmlentities2()
    // ════════════════════════════════════════════════════════════════

    public function test_htmlentities2_encodes_entities() {
        $result = htmlentities2( '<b>hello</b>' );
        $this->assertStringNotContainsString( '<b>', $result );
    }

    public function test_htmlentities2_preserves_existing_entities() {
        // Already-encoded &amp; should not double-encode.
        $result = htmlentities2( '&amp;' );
        $this->assertSame( '&amp;', $result );
    }

    // ════════════════════════════════════════════════════════════════
    // wp_sprintf() — custom sprintf with filter support
    // ════════════════════════════════════════════════════════════════

    public function test_wp_sprintf_basic() {
        $this->assertSame( 'Hello World', wp_sprintf( '%s World', 'Hello' ) );
    }

    public function test_wp_sprintf_multiple_args() {
        $this->assertSame( 'a-b', wp_sprintf( '%s-%s', 'a', 'b' ) );
    }

    public function test_wp_sprintf_literal_percent() {
        $this->assertSame( '100%', wp_sprintf( '100%%' ) );
    }

    public function test_wp_sprintf_numbered_args() {
        $this->assertSame( 'b-a', wp_sprintf( '%2$s-%1$s', 'a', 'b' ) );
    }

    public function test_wp_sprintf_no_args() {
        $this->assertSame( 'hello', wp_sprintf( 'hello' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // wp_sprintf_l() — list localization
    // ════════════════════════════════════════════════════════════════

    public function test_wp_sprintf_l_two_items() {
        $result = wp_sprintf_l( '%l', array( 'a', 'b' ) );
        $this->assertSame( 'a and b', $result );
    }

    public function test_wp_sprintf_l_three_items() {
        $result = wp_sprintf_l( '%l', array( 'a', 'b', 'c' ) );
        $this->assertSame( 'a, b, and c', $result );
    }

    public function test_wp_sprintf_l_single_item() {
        $result = wp_sprintf_l( '%l', array( 'only' ) );
        $this->assertSame( 'only', $result );
    }

    public function test_wp_sprintf_l_empty_returns_empty() {
        $result = wp_sprintf_l( '%l', array() );
        $this->assertSame( '', $result );
    }

    public function test_wp_sprintf_l_with_trailing_text() {
        $result = wp_sprintf_l( '%l are here', array( 'a', 'b' ) );
        $this->assertSame( 'a and b are here', $result );
    }

    public function test_wp_sprintf_l_not_starting_with_l() {
        // If pattern doesn't start with %l, return as-is.
        $result = wp_sprintf_l( 'hello %l', array( 'a', 'b' ) );
        $this->assertSame( 'hello %l', $result );
    }

    // ════════════════════════════════════════════════════════════════
    // sanitize_trackback_urls()
    // ════════════════════════════════════════════════════════════════

    public function test_sanitize_trackback_urls_valid() {
        $result = sanitize_trackback_urls( "http://example.com\nhttps://test.com" );
        $this->assertStringContainsString( 'http://example.com', $result );
        $this->assertStringContainsString( 'https://test.com', $result );
    }

    public function test_sanitize_trackback_urls_strips_non_http() {
        $result = sanitize_trackback_urls( "ftp://evil.com\nhttp://good.com" );
        $this->assertStringNotContainsString( 'ftp://', $result );
        $this->assertStringContainsString( 'http://good.com', $result );
    }

    public function test_sanitize_trackback_urls_empty() {
        $this->assertSame( '', sanitize_trackback_urls( '' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // esc_html() / esc_attr() (thin wrappers around _wp_specialchars)
    // ════════════════════════════════════════════════════════════════

    public function test_esc_html_escapes_tags() {
        $this->assertSame( '&lt;script&gt;', esc_html( '<script>' ) );
    }

    public function test_esc_html_escapes_quotes() {
        $this->assertStringContainsString( '&quot;', esc_html( '"hello"' ) );
    }

    public function test_esc_attr_escapes_tags() {
        $this->assertSame( '&lt;script&gt;', esc_attr( '<script>' ) );
    }

    public function test_esc_attr_escapes_quotes() {
        $this->assertStringContainsString( '&quot;', esc_attr( '"hello"' ) );
    }

    public function test_esc_html_empty_string() {
        $this->assertSame( '', esc_html( '' ) );
    }

    public function test_esc_attr_plain_text() {
        $this->assertSame( 'hello', esc_attr( 'hello' ) );
    }

    // ════════════════════════════════════════════════════════════════
    // esc_textarea()
    // ════════════════════════════════════════════════════════════════

    public function test_esc_textarea_escapes_html() {
        $result = esc_textarea( '<b>"Hello"</b>' );
        $this->assertStringContainsString( '&lt;', $result );
        $this->assertStringContainsString( '&quot;', $result );
    }

    // ════════════════════════════════════════════════════════════════
    // esc_xml()
    // ════════════════════════════════════════════════════════════════

    public function test_esc_xml_escapes_entities() {
        $result = esc_xml( '<tag attr="val">&</tag>' );
        $this->assertStringContainsString( '&lt;', $result );
        $this->assertStringContainsString( '&amp;', $result );
    }

    public function test_esc_xml_preserves_cdata() {
        $cdata = '<![CDATA[<raw content>]]>';
        $result = esc_xml( 'before' . $cdata . 'after' );
        $this->assertStringContainsString( $cdata, $result );
    }

    // ════════════════════════════════════════════════════════════════
    // utf8_uri_encode()
    // ════════════════════════════════════════════════════════════════

    public function test_utf8_uri_encode_ascii_unchanged() {
        $this->assertSame( 'hello', utf8_uri_encode( 'hello' ) );
    }

    public function test_utf8_uri_encode_non_ascii_encoded() {
        $result = utf8_uri_encode( 'café' );
        $this->assertStringContainsString( '%', $result );
        $this->assertStringStartsWith( 'caf', $result );
    }

    public function test_utf8_uri_encode_with_length_limit() {
        $result = utf8_uri_encode( 'hello world', 5 );
        $this->assertSame( 'hello', $result );
    }

    public function test_utf8_uri_encode_ascii_encoding_flag() {
        $result = utf8_uri_encode( 'a<b', 0, true );
        $this->assertStringContainsString( '%3C', $result );
    }

    // ════════════════════════════════════════════════════════════════
    // wp_pre_kses_less_than()
    // ════════════════════════════════════════════════════════════════

    public function test_wp_pre_kses_less_than_valid_tag() {
        // Valid tags pass through.
        $this->assertSame( '<b>', wp_pre_kses_less_than( '<b>' ) );
    }

    public function test_wp_pre_kses_less_than_lone_lt() {
        // A lone < without closing > is escaped.
        $result = wp_pre_kses_less_than( 'x < y' );
        $this->assertStringContainsString( '&lt;', $result );
    }

    // ════════════════════════════════════════════════════════════════
    // Edge cases & quirks
    // ════════════════════════════════════════════════════════════════

    /**
     * Edge case: zeroise uses %s format, not %d, so strings are padded too.
     */
    public function test_zeroise_with_string_input() {
        $this->assertSame( '00abc', zeroise( 'abc', 5 ) );
    }

    /**
     * Edge case: stripslashes_from_strings_only returns non-strings unchanged.
     */
    public function test_stripslashes_from_strings_only_non_string() {
        $this->assertSame( 42, stripslashes_from_strings_only( 42 ) );
        $this->assertTrue( stripslashes_from_strings_only( true ) );
    }

    /**
     * Edge case: sanitize_key with integer input.
     */
    public function test_sanitize_key_integer() {
        $this->assertSame( '123', sanitize_key( 123 ) );
    }

    /**
     * Edge case: sanitize_key with boolean.
     */
    public function test_sanitize_key_boolean() {
        // true becomes '1' when cast to string, then lowercased.
        $this->assertSame( '1', sanitize_key( true ) );
        // false becomes '' when cast to string.
        $this->assertSame( '', sanitize_key( false ) );
    }

    /**
     * Edge case: trailingslashit on just a slash.
     */
    public function test_trailingslashit_only_slash() {
        $this->assertSame( '/', trailingslashit( '/' ) );
    }

    /**
     * Edge case: untrailingslashit removes all trailing slashes, leaving root as empty.
     */
    public function test_untrailingslashit_only_slashes() {
        $this->assertSame( '', untrailingslashit( '///' ) );
    }

    /**
     * Edge case: force_balance_tags with HTML comment edge case.
     * The function converts '< !--' to '<    !--' during processing,
     * then back to '< !--' at the end.
     */
    public function test_force_balance_tags_comment_workaround() {
        $text   = '< !-- not a comment -->';
        $result = force_balance_tags( $text );
        $this->assertSame( '< !-- not a comment -->', $result );
    }

    /**
     * Edge case: _deep_replace with empty search string.
     */
    public function test_deep_replace_empty_search() {
        // empty search '' would cause infinite loop if str_replace matched,
        // but str_replace with '' search returns subject unchanged (count=0).
        $this->assertSame( 'hello', _deep_replace( '', 'hello' ) );
    }

    /**
     * Edge case: is_email with exactly 6 characters (minimum).
     */
    public function test_is_email_minimum_valid_length() {
        // 'a@b.co' is 6 chars, has @, local valid, domain has 2 subs.
        $this->assertSame( 'a@b.co', is_email( 'a@b.co' ) );
    }

    /**
     * Edge case: sanitize_hex_color with only hash.
     */
    public function test_sanitize_hex_color_only_hash() {
        $this->assertNull( sanitize_hex_color( '#' ) );
    }

    /**
     * Edge case: wp_make_link_relative with non-HTTP protocol.
     */
    public function test_wp_make_link_relative_ftp_unchanged() {
        $this->assertSame( 'ftp://example.com/file', wp_make_link_relative( 'ftp://example.com/file' ) );
    }

    /**
     * Edge case: convert_chars only replaces lone &, not &entity;
     */
    public function test_convert_chars_mixed() {
        $result = convert_chars( 'Tom & Jerry &amp; friends' );
        $this->assertSame( 'Tom &#038; Jerry &amp; friends', $result );
    }

    /**
     * Edge case: sanitize_sql_orderby with whitespace-only.
     */
    public function test_sanitize_sql_orderby_whitespace_only() {
        $this->assertFalse( sanitize_sql_orderby( '   ' ) );
    }

    /**
     * Edge case: url_shorten with no protocol.
     */
    public function test_url_shorten_no_protocol() {
        $this->assertSame( 'example.com/page', url_shorten( 'example.com/page/' ) );
    }

    /**
     * Edge case: map_deep with empty array.
     */
    public function test_map_deep_empty_array() {
        $this->assertSame( array(), map_deep( array(), 'strtoupper' ) );
    }

    /**
     * Edge case: wp_slash double-slashes.
     */
    public function test_wp_slash_double_slashing() {
        $this->assertSame( "it\\\\'s", wp_slash( "it\\'s" ) );
    }

    /**
     * Edge case: normalize_whitespace with only whitespace.
     */
    public function test_normalize_whitespace_only_whitespace() {
        $this->assertSame( '', normalize_whitespace( "   \t\n\r  " ) );
    }

    /**
     * Edge case: sanitize_title_with_dashes with only special chars.
     */
    public function test_sanitize_title_with_dashes_only_special() {
        $this->assertSame( '', sanitize_title_with_dashes( '!@#$%^&*()' ) );
    }

    /**
     * Edge case: sanitize_html_class with numbers starting the classname.
     */
    public function test_sanitize_html_class_leading_number() {
        // CSS classes starting with a number are invalid CSS, but the function doesn't enforce this.
        $this->assertSame( '123class', sanitize_html_class( '123class' ) );
    }

    /**
     * Edge case: wp_html_excerpt with count 0.
     */
    public function test_wp_html_excerpt_zero_length() {
        $this->assertSame( '...', wp_html_excerpt( 'hello', 0, '...' ) );
    }

    /**
     * Edge case: sanitize_file_name with only dots.
     */
    public function test_sanitize_file_name_only_dots() {
        // All dots get collapsed to '.', then trimmed.
        // After trim('.-_'), result is empty.
        $result = sanitize_file_name( '...' );
        $this->assertSame( '', $result );
    }

    /**
     * Edge case: sanitize_user with only HTML entities.
     */
    public function test_sanitize_user_only_entities() {
        $this->assertSame( '', sanitize_user( '&amp;&lt;' ) );
    }
}
