<?php

class WPCF7_ContactForm {

	use WPCF7_SWV_SchemaHolder;
	use WPCF7_PipesHolder;

	const post_type = 'wpcf7_contact_form';

	private static $found_items = 0;
	private static $current = null;

	private $id;
	private $name;
	private $title;
	private $locale;
	private $properties = array();
	private $unit_tag;
	private $responses_count = 0;
	private $scanned_form_tags;
	private $shortcode_atts = array();
	private $hash = '';


	/**
	 * Returns count of contact forms found by the previous retrieval.
	 *
	 * @return int Count of contact forms.
	 */
	public static function count() {
		return self::$found_items;
	}


	/**
	 * Returns the contact form that is currently processed.
	 *
	 * @return WPCF7_ContactForm|null Current contact form object. Null if unset.
	 */
	public static function get_current() {
		return self::$current;
	}


	/**
	 * Registers the post type for contact forms.
	 */
	public static function register_post_type() {
		register_post_type( self::post_type, array(
			'labels' => array(
				'name' => __( 'Contact Forms', 'contact-form-7' ),
				'singular_name' => __( 'Contact Form', 'contact-form-7' ),
			),
			'rewrite' => false,
			'query_var' => false,
			'public' => false,
			'capability_type' => 'page',
			'capabilities' => array(
				'edit_post' => 'wpcf7_edit_contact_form',
				'read_post' => 'wpcf7_read_contact_form',
				'delete_post' => 'wpcf7_delete_contact_form',
				'edit_posts' => 'wpcf7_edit_contact_forms',
				'edit_others_posts' => 'wpcf7_edit_contact_forms',
				'publish_posts' => 'wpcf7_edit_contact_forms',
				'read_private_posts' => 'wpcf7_edit_contact_forms',
			),
		) );
	}


	/**
	 * Retrieves contact form data that match given conditions.
	 *
	 * @param string|array $args Optional. Arguments to be passed to WP_Query.
	 * @return array Array of WPCF7_ContactForm objects.
	 */
	public static function find( $args = '' ) {
		$defaults = array(
			'post_status' => 'any',
			'posts_per_page' => -1,
			'offset' => 0,
			'orderby' => 'ID',
			'order' => 'ASC',
		);

		$args = wp_parse_args( $args, $defaults );

		$args['post_type'] = self::post_type;

		$q = new WP_Query();
		$posts = $q->query( $args );

		self::$found_items = $q->found_posts;

		$objs = array();

		foreach ( $posts as $post ) {
			$objs[] = new self( $post );
		}

		return $objs;
	}


	/**
	 * Returns a contact form data filled by default template contents.
	 *
	 * @param string|array $options Optional. Contact form options.
	 * @return WPCF7_ContactForm A new contact form object.
	 */
	public static function get_template( $options = '' ) {
		$options = wp_parse_args( $options, array(
			'locale' => null,
			'title' => __( 'Untitled', 'contact-form-7' ),
		) );

		if ( ! isset( $options['locale'] ) ) {
			$options['locale'] = determine_locale();
		}

		$callback = static function ( $options ) {
			$contact_form = new self();
			$contact_form->title = $options['title'];
			$contact_form->locale = $options['locale'];

			$properties = $contact_form->get_properties();

			foreach ( $properties as $key => $value ) {
				$default_template = WPCF7_ContactFormTemplate::get_default( $key );

				if ( isset( $default_template ) ) {
					$properties[$key] = $default_template;
				}
			}

			$contact_form->properties = $properties;

			return $contact_form;
		};

		$contact_form = wpcf7_switch_locale(
			$options['locale'],
			$callback,
			$options
		);

		self::$current = apply_filters( 'wpcf7_contact_form_default_pack',
			$contact_form, $options
		);

		return self::$current;
	}


	/**
	 * Creates a WPCF7_ContactForm object and sets it as the current instance.
	 *
	 * @param WPCF7_ContactForm|WP_Post|int $post Object or post ID.
	 * @return WPCF7_ContactForm|null Contact form object. Null if unset.
	 */
	public static function get_instance( $post ) {
		$contact_form = null;

		if ( $post instanceof self ) {
			$contact_form = $post;
		} elseif ( ! empty( $post ) ) {
			$post = get_post( $post );

			if ( isset( $post ) and self::post_type === get_post_type( $post ) ) {
				$contact_form = new self( $post );
			}
		}

		return self::$current = $contact_form;
	}


	/**
	 * Generates a "unit-tag" for the given contact form ID.
	 *
	 * @return string Unit-tag.
	 */
	private static function generate_unit_tag( $id = 0 ) {
		static $global_count = 0;

		$global_count += 1;

		if ( in_the_loop() ) {
			$unit_tag = sprintf( 'wpcf7-f%1$d-p%2$d-o%3$d',
				absint( $id ),
				get_the_ID(),
				$global_count
			);
		} else {
			$unit_tag = sprintf( 'wpcf7-f%1$d-o%2$d',
				absint( $id ),
				$global_count
			);
		}

		return $unit_tag;
	}


	/**
	 * Constructor.
	 */
	private function __construct( $post = null ) {
		$post = get_post( $post );

		if ( $post and self::post_type === get_post_type( $post ) ) {
			$this->id = $post->ID;
			$this->name = $post->post_name;
			$this->title = $post->post_title;
			$this->locale = get_post_meta( $post->ID, '_locale', true );
			$this->hash = get_post_meta( $post->ID, '_hash', true );

			$this->construct_properties( $post );
			$this->upgrade();
		} else {
			$this->construct_properties();
		}

		do_action( 'wpcf7_contact_form', $this );
	}


	/**
	 * Magic method for property overloading.
	 */
	public function __get( $name ) {
		/* translators: 1: property name, 2: method name */
		$message = __( '<code>%1$s</code> property of a <code>WPCF7_ContactForm</code> object is <strong>no longer accessible</strong>. Use <code>%2$s</code> method instead.', 'contact-form-7' );

		if ( 'id' === $name ) {
			wp_trigger_error(
				'',
				sprintf( $message, 'id', 'id()' ),
				E_USER_DEPRECATED
			);

			return $this->id;
		} elseif ( 'title' === $name ) {
			wp_trigger_error(
				'',
				sprintf( $message, 'title', 'title()' ),
				E_USER_DEPRECATED
			);

			return $this->title;
		} elseif ( $prop = $this->prop( $name ) ) {
			wp_trigger_error(
				'',
				sprintf( $message, $name, 'prop(\'' . $name . '\')' ),
				E_USER_DEPRECATED
			);

			return $prop;
		}
	}


	/**
	 * Returns true if this contact form is not yet saved to the database.
	 */
	public function initial() {
		return empty( $this->id );
	}


	/**
	 * Constructs contact form properties. This is called only once
	 * from the constructor.
	 */
	private function construct_properties( $post = null ) {
		$builtin_properties = array(
			'form' => '',
			'mail' => array(),
			'mail_2' => array(),
			'messages' => array(),
			'additional_settings' => '',
		);

		$properties = apply_filters(
			'wpcf7_pre_construct_contact_form_properties',
			$builtin_properties, $this
		);

		// Filtering out properties with invalid name
		$properties = array_filter(
			$properties,
			static function ( $key ) {
				$sanitized_key = sanitize_key( $key );
				return $key === $sanitized_key;
			},
			ARRAY_FILTER_USE_KEY
		);

		foreach ( $properties as $name => $val ) {
			$prop = $this->retrieve_property( $name );

			if ( isset( $prop ) ) {
				$properties[$name] = $prop;
			}
		}

		$this->properties = $properties;

		foreach ( $properties as $name => $val ) {
			$properties[$name] = apply_filters(
				"wpcf7_contact_form_property_{$name}",
				$val, $this
			);
		}

		$this->properties = $properties;

		$properties = (array) apply_filters(
			'wpcf7_contact_form_properties',
			$properties, $this
		);

		$this->properties = $properties;
	}


	/**
	 * Retrieves contact form property of the specified name from the database.
	 *
	 * @param string $name Property name.
	 * @return array|string|null Property value. Null if property does not exist.
	 */
	private function retrieve_property( $name ) {
		$property = null;

		if ( ! $this->initial() ) {
			$post_id = $this->id;

			if ( metadata_exists( 'post', $post_id, '_' . $name ) ) {
				$property = get_post_meta( $post_id, '_' . $name, true );
			} elseif ( metadata_exists( 'post', $post_id, $name ) ) {
				$property = get_post_meta( $post_id, $name, true );
			}
		}

		return $property;
	}


	/**
	 * Returns the value for the given property name.
	 *
	 * @param string $name Property name.
	 * @return array|string|null Property value. Null if property does not exist.
	 */
	public function prop( $name ) {
		$props = $this->get_properties();
		return isset( $props[$name] ) ? $props[$name] : null;
	}


	/**
	 * Returns all the properties.
	 *
	 * @return array This contact form's properties.
	 */
	public function get_properties() {
		return (array) $this->properties;
	}


	/**
	 * Updates properties.
	 *
	 * @param array $properties New properties.
	 */
	public function set_properties( $properties ) {
		$defaults = $this->get_properties();

		$properties = wp_parse_args( $properties, $defaults );
		$properties = array_intersect_key( $properties, $defaults );

		$this->properties = $properties;
	}


	/**
	 * Returns ID of this contact form.
	 *
	 * @return int The ID.
	 */
	public function id() {
		return $this->id;
	}


	/**
	 * Returns unit-tag for this contact form.
	 *
	 * @return string Unit-tag.
	 */
	public function unit_tag() {
		return $this->unit_tag;
	}


	/**
	 * Returns name (slug) of this contact form.
	 *
	 * @return string Name.
	 */
	public function name() {
		return $this->name;
	}


	/**
	 * Returns title of this contact form.
	 *
	 * @return string Title.
	 */
	public function title() {
		return $this->title;
	}


	/**
	 * Set a title for this contact form.
	 *
	 * @param string $title Title.
	 */
	public function set_title( $title ) {
		$title = wp_strip_all_tags( $title, true );
		$title = wpcf7_strip_whitespaces( $title );

		if ( '' === $title ) {
			$title = __( 'Untitled', 'contact-form-7' );
		}

		$this->title = $title;
	}


	/**
	 * Returns the locale code of this contact form.
	 *
	 * @return string Locale code. Empty string if no valid locale is set.
	 */
	public function locale() {
		if ( wpcf7_is_valid_locale( $this->locale ) ) {
			return $this->locale;
		} else {
			return '';
		}
	}


	/**
	 * Sets a locale for this contact form.
	 *
	 * @param string $locale Locale code.
	 */
	public function set_locale( $locale ) {
		$locale = trim( $locale );

		if ( wpcf7_is_valid_locale( $locale ) ) {
			$this->locale = $locale;
		} else {
			$this->locale = 'en_US';
		}
	}


	/**
	 * Retrieves the random hash string tied to this contact form.
	 *
	 * @param int $length Length of hash string.
	 * @return string Hash string unique to this contact form.
	 */
	public function hash( $length = 7 ) {
		return substr( $this->hash, 0, absint( $length ) );
	}


	/**
	 * Returns the specified shortcode attribute value.
	 *
	 * @param string $name Shortcode attribute name.
	 * @return string|null Attribute value. Null if the attribute does not exist.
	 */
	public function shortcode_attr( $name ) {
		if ( isset( $this->shortcode_atts[$name] ) ) {
			return (string) $this->shortcode_atts[$name];
		}
	}


	/**
	 * Returns true if this contact form is identical to the submitted one.
	 */
	public function is_posted() {
		if ( ! WPCF7_Submission::get_instance() ) {
			return false;
		}

		$unit_tag = wpcf7_superglobal_post( '_wpcf7_unit_tag' );

		if ( empty( $unit_tag ) ) {
			return false;
		}

		return $this->unit_tag() === $unit_tag;
	}


	/**
	 * Generates HTML that represents a form.
	 *
	 * @param string|array $options Optional. Form options.
	 * @return string HTML output.
	 */
	public function form_html( $options = '' ) {
		$options = wp_parse_args( $options, array(
			'html_id' => '',
			'html_name' => '',
			'html_title' => '',
			'html_class' => '',
			'output' => 'form',
		) );

		$this->shortcode_atts = $options;

		if ( 'raw_form' === $options['output'] ) {
			return sprintf(
				'<pre class="wpcf7-raw-form"><code>%s</code></pre>',
				esc_html( $this->prop( 'form' ) )
			);
		}

		if (
			$this->is_true( 'subscribers_only' ) and
			! current_user_can( 'wpcf7_submit', $this->id() )
		) {
			$notice = sprintf(
				'<p class="wpcf7-subscribers-only">%s</p>',
				wp_kses_data( __( 'This contact form is available only for logged in users.', 'contact-form-7' ) )
			);

			return apply_filters( 'wpcf7_subscribers_only_notice', $notice, $this );
		}

		$this->unit_tag = self::generate_unit_tag( $this->id );

		$action_url = wpcf7_get_request_uri();

		if ( $frag = strstr( $action_url, '#' ) ) {
			$action_url = substr( $action_url, 0, -strlen( $frag ) );
		}

		$action_url .= '#' . $this->unit_tag();

		$action_url = apply_filters( 'wpcf7_form_action_url', $action_url );

		if (
			str_starts_with( $action_url, '//' ) or
			! str_starts_with( $action_url, '/' ) and
			! str_starts_with( $action_url, home_url() )
		) {
			return sprintf(
				'<p class="wpcf7-invalid-action-url"><strong>%1$s</strong> %2$s</p>',
				esc_html( __( 'Error:', 'contact-form-7' ) ),
				esc_html( __( 'Invalid action URL is detected.', 'contact-form-7' ) )
			);
		}

		$lang_tag = str_replace( '_', '-', $this->locale );

		if ( preg_match( '/^([a-z]+-[a-z]+)-/i', $lang_tag, $matches ) ) {
			$lang_tag = $matches[1];
		}

		$html = "\n" . sprintf( '<div %s>',
			wpcf7_format_atts( array(
				'class' => 'wpcf7 no-js',
				'id' => $this->unit_tag(),
				( get_option( 'html_type' ) === 'text/html' ) ? 'lang' : 'xml:lang'
					=> $lang_tag,
				'dir' => wpcf7_is_rtl( $this->locale ) ? 'rtl' : 'ltr',
				'data-wpcf7-id' => $this->id(),
			) )
		);

		$html .= "\n" . $this->screen_reader_response() . "\n";

		$id_attr = apply_filters( 'wpcf7_form_id_attr',
			preg_replace( '/[^A-Za-z0-9:._-]/', '', $options['html_id'] )
		);

		$name_attr = apply_filters( 'wpcf7_form_name_attr',
			preg_replace( '/[^A-Za-z0-9:._-]/', '', $options['html_name'] )
		);

		$title_attr = apply_filters( 'wpcf7_form_title_attr', $options['html_title'] );

		$class = 'wpcf7-form';

		if ( $this->is_posted() ) {
			$submission = WPCF7_Submission::get_instance();

			$data_status_attr = $this->form_status_class_name(
				$submission->get_status()
			);

			$class .= sprintf( ' %s', $data_status_attr );
		} else {
			$data_status_attr = 'init';
			$class .= ' init';
		}

		if ( $options['html_class'] ) {
			$class .= ' ' . $options['html_class'];
		}

		if ( $this->in_demo_mode() ) {
			$class .= ' demo';
		}

		$class = explode( ' ', $class );
		$class = array_map( 'sanitize_html_class', $class );
		$class = array_filter( $class );
		$class = array_unique( $class );
		$class = implode( ' ', $class );
		$class = apply_filters( 'wpcf7_form_class_attr', $class );

		$enctype = wpcf7_enctype_value( apply_filters( 'wpcf7_form_enctype', '' ) );
		$autocomplete = apply_filters( 'wpcf7_form_autocomplete', '' );

		$atts = array(
			'action' => esc_url( $action_url ),
			'method' => 'post',
			'class' => ( '' !== $class ) ? $class : null,
			'id' => ( '' !== $id_attr ) ? $id_attr : null,
			'name' => ( '' !== $name_attr ) ? $name_attr : null,
			'aria-label' => ( '' !== $title_attr )
				? $title_attr : __( 'Contact form', 'contact-form-7' ),
			'enctype' => ( '' !== $enctype ) ? $enctype : null,
			'autocomplete' => ( '' !== $autocomplete ) ? $autocomplete : null,
			'novalidate' => true,
			'data-status' => $data_status_attr,
		);

		$atts += (array) apply_filters( 'wpcf7_form_additional_atts', array() );

		$html .= sprintf( '<form %s>', wpcf7_format_atts( $atts ) ) . "\n";
		$html .= $this->form_hidden_fields();
		$html .= $this->form_elements();

		if ( ! $this->responses_count ) {
			$html .= $this->form_response_output();
		}

		$html .= "\n" . '</form>';
		$html .= "\n" . '</div>';

		return $html . "\n";
	}


	/**
	 * Returns the class name that matches the given form status.
	 */
	private function form_status_class_name( $status ) {
		switch ( $status ) {
			case 'init':
				$class = 'init';
				break;
			case 'validation_failed':
				$class = 'invalid';
				break;
			case 'acceptance_missing':
				$class = 'unaccepted';
				break;
			case 'spam':
				$class = 'spam';
				break;
			case 'aborted':
				$class = 'aborted';
				break;
			case 'mail_sent':
				$class = 'sent';
				break;
			case 'mail_failed':
				$class = 'failed';
				break;
			default:
				$class = sprintf(
					'custom-%s',
					preg_replace( '/[^0-9a-z]+/i', '-', $status )
				);
		}

		return $class;
	}


	/**
	 * Returns a set of hidden fields.
	 */
	private function form_hidden_fields() {
		$hidden_fields = array(
			'_wpcf7' => $this->id(),
			'_wpcf7_version' => WPCF7_VERSION,
			'_wpcf7_locale' => $this->locale(),
			'_wpcf7_unit_tag' => $this->unit_tag(),
			'_wpcf7_container_post' => 0,
			'_wpcf7_posted_data_hash' => '',
		);

		if ( in_the_loop() ) {
			$hidden_fields['_wpcf7_container_post'] = (int) get_the_ID();
		}

		if ( $this->nonce_is_active() and is_user_logged_in() ) {
			$hidden_fields['_wpnonce'] = wpcf7_create_nonce();
		}

		$hidden_fields += (array) apply_filters(
			'wpcf7_form_hidden_fields', array()
		);

		$formatter = new WPCF7_HTMLFormatter();

		$formatter->append_start_tag( 'fieldset', array(
			'class' => 'hidden-fields-container',
		) );

		foreach ( $hidden_fields as $name => $value ) {
			$formatter->append_start_tag( 'input', array(
				'type' => 'hidden',
				'name' => $name,
				'value' => $value,
			) );
		}

		return $formatter->output() . "\n";
	}


	/**
	 * Returns the visible response output for a form submission.
	 */
	public function form_response_output() {
		$status = 'init';
		$class = 'wpcf7-response-output';
		$content = '';

		if ( $this->is_posted() ) { // Post response output for non-AJAX
			$submission = WPCF7_Submission::get_instance();
			$status = $submission->get_status();
			$content = $submission->get_response();
		}

		$atts = array(
			'class' => trim( $class ),
			'aria-hidden' => 'true',
		);

		$output = sprintf( '<div %1$s>%2$s</div>',
			wpcf7_format_atts( $atts ),
			esc_html( $content )
		);

		$output = apply_filters( 'wpcf7_form_response_output',
			$output, $class, $content, $this, $status
		);

		$this->responses_count += 1;

		return $output;
	}


	/**
	 * Returns the response output that is only accessible from screen readers.
	 */
	public function screen_reader_response() {
		$primary_response = '';
		$validation_errors = array();

		if ( $this->is_posted() ) { // Post response output for non-AJAX
			$submission = WPCF7_Submission::get_instance();
			$primary_response = $submission->get_response();

			if ( $invalid_fields = $submission->get_invalid_fields() ) {
				foreach ( (array) $invalid_fields as $name => $field ) {
					$list_item = esc_html( $field['reason'] );

					if ( $field['idref'] ) {
						$list_item = sprintf(
							'<a href="#%1$s">%2$s</a>',
							esc_attr( $field['idref'] ),
							$list_item
						);
					}

					$validation_error_id = wpcf7_get_validation_error_reference(
						$name,
						$this->unit_tag()
					);

					if ( $validation_error_id ) {
						$list_item = sprintf(
							'<li id="%1$s">%2$s</li>',
							esc_attr( $validation_error_id ),
							$list_item
						);

						$validation_errors[] = $list_item;
					}
				}
			}
		}

		$primary_response = sprintf(
			'<p role="status" aria-live="polite" aria-atomic="true">%s</p>',
			esc_html( $primary_response )
		);

		$validation_errors = sprintf(
			'<ul>%s</ul>',
			implode( "\n", $validation_errors )
		);

		$output = sprintf(
			'<div class="screen-reader-response">%1$s %2$s</div>',
			$primary_response,
			$validation_errors
		);

		return $output;
	}


	/**
	 * Returns a validation error for the specified input field.
	 *
	 * @param string $name Input field name.
	 */
	public function validation_error( $name ) {
		$error = '';

		if ( $this->is_posted() ) {
			$submission = WPCF7_Submission::get_instance();

			if ( $invalid_field = $submission->get_invalid_field( $name ) ) {
				$error = trim( $invalid_field['reason'] );
			}
		}

		if ( ! $error ) {
			return $error;
		}

		$atts = array(
			'class' => 'wpcf7-not-valid-tip',
			'aria-hidden' => 'true',
		);

		$error = sprintf(
			'<span %1$s>%2$s</span>',
			wpcf7_format_atts( $atts ),
			esc_html( $error )
		);

		return apply_filters( 'wpcf7_validation_error', $error, $name, $this );
	}


	/**
	 * Replaces all form-tags in the form template with corresponding HTML.
	 *
	 * @return string Replaced form content.
	 */
	public function replace_all_form_tags() {
		$manager = WPCF7_FormTagsManager::get_instance();
		$form = $this->prop( 'form' );

		if ( wpcf7_autop_or_not() ) {
			$form = $manager->replace_with_placeholders( $form );
			$form = wpcf7_autop( $form );
			$form = $manager->restore_from_placeholders( $form );
		}

		$form = $manager->replace_all( $form );
		$this->scanned_form_tags = $manager->get_scanned_tags();

		return $form;
	}


	/**
	 * Replaces all form-tags in the form template with corresponding HTML.
	 *
	 * @deprecated 4.6 Use replace_all_form_tags()
	 *
	 * @return string Replaced form content.
	 */
	public function form_do_shortcode() {
		wpcf7_deprecated_function( __METHOD__, '4.6',
			'WPCF7_ContactForm::replace_all_form_tags'
		);

		return $this->replace_all_form_tags();
	}


	/**
	 * Scans form-tags from the form template.
	 *
	 * @param string|array|null $cond Optional. Filters. Default null.
	 * @return array Form-tags matching the given filter conditions.
	 */
	public function scan_form_tags( $cond = null ) {
		$manager = WPCF7_FormTagsManager::get_instance();

		if ( empty( $this->scanned_form_tags ) ) {
			$this->scanned_form_tags = $manager->scan( $this->prop( 'form' ) );
		}

		$tags = $this->scanned_form_tags;

		return $manager->filter( $tags, $cond );
	}


	/**
	 * Scans form-tags from the form template.
	 *
	 * @deprecated 4.6 Use scan_form_tags()
	 *
	 * @param string|array|null $cond Optional. Filters. Default null.
	 * @return array Form-tags matching the given filter conditions.
	 */
	public function form_scan_shortcode( $cond = null ) {
		wpcf7_deprecated_function( __METHOD__, '4.6',
			'WPCF7_ContactForm::scan_form_tags'
		);

		return $this->scan_form_tags( $cond );
	}


	/**
	 * Replaces all form-tags in the form template with corresponding HTML.
	 *
	 * @return string Replaced form content. wpcf7_form_elements filters applied.
	 */
	public function form_elements() {
		return apply_filters( 'wpcf7_form_elements',
			$this->replace_all_form_tags()
		);
	}


	/**
	 * Collects mail-tags available for this contact form.
	 *
	 * @param string|array $options Optional. Search options.
	 * @return array Mail-tag names.
	 */
	public function collect_mail_tags( $options = '' ) {
		$manager = WPCF7_FormTagsManager::get_instance();

		$options = wp_parse_args( $options, array(
			'include' => array(),
			'exclude' => $manager->collect_tag_types( 'not-for-mail' ),
		) );

		$tags = $this->scan_form_tags();
		$mailtags = array();

		foreach ( (array) $tags as $tag ) {
			$type = $tag->basetype;

			if ( empty( $type ) ) {
				continue;
			} elseif ( ! empty( $options['include'] ) ) {
				if ( ! in_array( $type, $options['include'], true ) ) {
					continue;
				}
			} elseif ( ! empty( $options['exclude'] ) ) {
				if ( in_array( $type, $options['exclude'], true ) ) {
					continue;
				}
			}

			$mailtags[] = $tag->name;
		}

		$mailtags = array_unique( $mailtags );
		$mailtags = array_filter( $mailtags );
		$mailtags = array_values( $mailtags );

		return apply_filters( 'wpcf7_collect_mail_tags', $mailtags, $options, $this );
	}


	/**
	 * Prints a mail-tag suggestion list.
	 *
	 * @param string $template_name Optional. Mail template name. Default 'mail'.
	 */
	public function suggest_mail_tags( $template_name = 'mail' ) {
		$mail = wp_parse_args( $this->prop( $template_name ),
			array(
				'active' => false,
				'recipient' => '',
				'sender' => '',
				'subject' => '',
				'body' => '',
				'additional_headers' => '',
				'attachments' => '',
				'use_html' => false,
				'exclude_blank' => false,
			)
		);

		$mail = array_filter( $mail );

		foreach ( (array) $this->collect_mail_tags() as $mail_tag ) {
			$pattern = sprintf(
				'/\[(_[a-z]+_)?%s([ \t]+[^]]+)?\]/',
				preg_quote( $mail_tag, '/' )
			);

			$used = preg_grep( $pattern, $mail );

			echo sprintf(
				'<span class="%1$s">[%2$s]</span>',
				'mailtag code ' . ( $used ? 'used' : 'unused' ),
				esc_html( $mail_tag )
			);
		}
	}


	/**
	 * Submits this contact form.
	 *
	 * @param string|array $options Optional. Submission options. Default empty.
	 * @return array Result of submission.
	 */
	public function submit( $options = '' ) {
		$options = wp_parse_args( $options, array(
			'skip_mail' => (
				$this->in_demo_mode() ||
				$this->is_true( 'skip_mail' ) ||
				! empty( $this->skip_mail )
			),
		) );

		if (
			$this->is_true( 'subscribers_only' ) and
			! current_user_can( 'wpcf7_submit', $this->id() )
		) {
			$result = array(
				'contact_form_id' => $this->id(),
				'status' => 'error',
				'message' => __( 'This contact form is available only for logged in users.', 'contact-form-7' ),
			);

			return $result;
		}

		$submission = WPCF7_Submission::get_instance( $this, array(
			'skip_mail' => $options['skip_mail'],
		) );

		$result = array(
			'contact_form_id' => $this->id(),
		);

		$result += $submission->get_result();

		if ( $this->in_demo_mode() ) {
			$result['demo_mode'] = true;
		}

		do_action( 'wpcf7_submit', $this, $result );

		return $result;
	}


	/**
	 * Returns message used for given status.
	 *
	 * @param string $status Status.
	 * @param bool $filter Optional. Whether filters are applied. Default true.
	 * @return string Message.
	 */
	public function message( $status, $filter = true ) {
		$messages = $this->prop( 'messages' );
		$message = isset( $messages[$status] ) ? $messages[$status] : '';

		if ( $filter ) {
			$message = $this->filter_message( $message, $status );
		}

		return $message;
	}


	/**
	 * Filters a message.
	 *
	 * @param string $message Message to filter.
	 * @param string $status Optional. Status. Default empty.
	 * @return string Filtered message.
	 */
	public function filter_message( $message, $status = '' ) {
		$message = wpcf7_mail_replace_tags( $message );
		$message = apply_filters( 'wpcf7_display_message', $message, $status );
		$message = wp_strip_all_tags( $message );

		return $message;
	}


	/**
	 * Returns the additional setting value searched by name.
	 *
	 * @param string $name Name of setting.
	 * @return string Additional setting value.
	 */
	public function pref( $name ) {
		$settings = $this->additional_setting( $name );

		if ( $settings ) {
			return $settings[0];
		}
	}


	/**
	 * Returns additional setting values searched by name.
	 *
	 * @param string $name Name of setting.
	 * @param int $max Maximum result item count.
	 * @return array Additional setting values.
	 */
	public function additional_setting( $name, $max = 1 ) {
		$settings = (array) explode( "\n", $this->prop( 'additional_settings' ) );

		$pattern = '/^([a-zA-Z0-9_]+)[\t ]*:(.*)$/';
		$count = 0;
		$values = array();

		foreach ( $settings as $setting ) {
			if ( preg_match( $pattern, $setting, $matches ) ) {
				if ( $matches[1] !== $name ) {
					continue;
				}

				if ( ! $max or $count < (int) $max ) {
					$values[] = trim( $matches[2] );
					$count += 1;
				}
			}
		}

		return $values;
	}


	/**
	 * Returns true if the specified setting has a truthy string value.
	 *
	 * @param string $name Name of setting.
	 * @return bool True if the setting value is 'on', 'true', or '1'.
	 */
	public function is_true( $name ) {
		return in_array(
			$this->pref( $name ),
			array( 'on', 'true', '1' ),
			true
		);
	}


	/**
	 * Returns true if this contact form is in the demo mode.
	 */
	public function in_demo_mode() {
		return $this->is_true( 'demo_mode' );
	}


	/**
	 * Returns true if nonce is active for this contact form.
	 */
	public function nonce_is_active() {
		$is_active = WPCF7_VERIFY_NONCE;

		if ( $this->is_true( 'subscribers_only' ) ) {
			$is_active = true;
		}

		return (bool) apply_filters( 'wpcf7_verify_nonce', $is_active, $this );
	}


	/**
	 * Returns true if the specified setting has a falsey string value.
	 *
	 * @param string $name Name of setting.
	 * @return bool True if the setting value is 'off', 'false', or '0'.
	 */
	public function is_false( $name ) {
		return in_array(
			$this->pref( $name ),
			array( 'off', 'false', '0' ),
			true
		);
	}


	/**
	 * Upgrades this contact form properties.
	 */
	private function upgrade() {
		$mail = $this->prop( 'mail' );

		if ( is_array( $mail ) and ! isset( $mail['recipient'] ) ) {
			$mail['recipient'] = get_option( 'admin_email' );
		}

		$this->properties['mail'] = $mail;

		$messages = $this->prop( 'messages' );

		if ( is_array( $messages ) ) {
			foreach ( wpcf7_messages() as $key => $arr ) {
				if ( ! isset( $messages[$key] ) ) {
					$messages[$key] = $arr['default'];
				}
			}
		}

		$this->properties['messages'] = $messages;
	}


	/**
	 * Stores this contact form properties to the database.
	 *
	 * @return int The post ID on success. The value 0 on failure.
	 */
	public function save() {
		$title = wp_slash( $this->title );
		$props = wp_slash( $this->get_properties() );

		$post_content = implode( "\n", wpcf7_array_flatten( $props ) );

		if ( $this->initial() ) {
			$post_id = wp_insert_post( array(
				'post_type' => self::post_type,
				'post_status' => 'publish',
				'post_title' => $title,
				'post_content' => trim( $post_content ),
			) );
		} else {
			$post_id = wp_update_post( array(
				'ID' => (int) $this->id,
				'post_status' => 'publish',
				'post_title' => $title,
				'post_content' => trim( $post_content ),
			) );
		}

		if ( $post_id ) {
			foreach ( $props as $prop => $value ) {
				update_post_meta( $post_id, '_' . $prop,
					wpcf7_normalize_newline_deep( $value )
				);
			}

			if ( wpcf7_is_valid_locale( $this->locale ) ) {
				update_post_meta( $post_id, '_locale', $this->locale );
			}

			add_post_meta( $post_id, '_hash',
				wpcf7_generate_contact_form_hash( $post_id ),
				true // Unique
			);

			if ( $this->initial() ) {
				$this->id = $post_id;
				do_action( 'wpcf7_after_create', $this );
			} else {
				do_action( 'wpcf7_after_update', $this );
			}

			do_action( 'wpcf7_after_save', $this );
		}

		return $post_id;
	}


	/**
	 * Makes a copy of this contact form.
	 *
	 * @return WPCF7_ContactForm New contact form object.
	 */
	public function copy() {
		$new = new self();
		$new->title = $this->title . '_copy';
		$new->locale = $this->locale;
		$new->properties = $this->properties;

		return apply_filters( 'wpcf7_copy', $new, $this );
	}


	/**
	 * Deletes this contact form.
	 *
	 * @return bool True if deletion succeeded, false otherwise.
	 */
	public function delete() {
		if ( $this->initial() ) {
			return false;
		}

		if ( wp_delete_post( $this->id, true ) ) {
			$this->id = 0;
			return true;
		}

		return false;
	}


	/**
	 * Returns a WordPress shortcode for this contact form.
	 */
	public function shortcode( $options = '' ) {
		$options = wp_parse_args( $options, array(
			'use_old_format' => false
		) );

		$title = str_replace( array( '"', '[', ']' ), '', $this->title );

		if ( $options['use_old_format'] ) {
			$old_unit_id = (int) get_post_meta( $this->id, '_old_cf7_unit_id', true );

			if ( $old_unit_id ) {
				$shortcode = sprintf(
					'[contact-form %1$d "%2$s"]',
					$old_unit_id,
					$title
				);
			} else {
				$shortcode = '';
			}
		} else {
			$shortcode = sprintf(
				'[contact-form-7 id="%1$s" title="%2$s"]',
				$this->hash(),
				$title
			);
		}

		return apply_filters( 'wpcf7_contact_form_shortcode',
			$shortcode, $options, $this
		);
	}
}
