<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Abstract Email Class
 *
 * WooCommerce Email Class which is extended by specific email template classes to add emails to WooCommerce
 *
 * @class 		WC_Email
 * @version		2.0.0
 * @package		WooCommerce/Abstracts
 * @author 		WooThemes
 * @category	Abstract Class
 * @extends 	WC_Settings_API
 */
abstract class WC_Email extends WC_Settings_API {

	/** @var string Payment method ID. */
	var $id;

	/** @var string Payment method title. */
	var $title;

	/** @var string 'yes' if the method is enabled. */
	var $enabled;

	/** @var string Description for the gateway. */
	var $description;

	/** @var string plain text template path */
	var $template_plain;

	/** @var string html template path */
	var $template_html;

	/** @var string template path */
	var $template_base;

	/** @var string recipients for the email */
	var $recipient;

	/** @var string heading for the email content */
	var $heading;

	/** @var string subject for the email */
	var $subject;

	/** @var object this email is for, for example a customer, product, or email */
	var $object;

	/** @var array strings to find in subjects/headings */
	var $find;

	/** @var array strings to replace in subjects/headings */
	var $replace;

	/** @var string For multipart emails */
	var $mime_boundary;

	/** @var string For multipart emails */
	var $mime_boundary_header;

	/** @var bool true when email is being sent */
	var $sending;

	/**
     *  List of preg* regular expression patterns to search for,
     *  used in conjunction with $replace.
     *  https://raw.github.com/ushahidi/wp-silcc/master/class.html2text.inc
     *
     *  @var array $search
     *  @access public
     *  @see $replace
     */
    var $plain_search = array(
        "/\r/",                                  // Non-legal carriage return
        '/&(nbsp|#160);/i',                      // Non-breaking space
        '/&(quot|rdquo|ldquo|#8220|#8221|#147|#148);/i',
		                                         // Double quotes
        '/&(apos|rsquo|lsquo|#8216|#8217);/i',   // Single quotes
        '/&gt;/i',                               // Greater-than
        '/&lt;/i',                               // Less-than
        '/&#38;/i',                              // Ampersand
        '/&#038;/i',                             // Ampersand
        '/&amp;/i',                              // Ampersand
        '/&(copy|#169);/i',                      // Copyright
        '/&(trade|#8482|#153);/i',               // Trademark
        '/&(reg|#174);/i',                       // Registered
        '/&(mdash|#151|#8212);/i',               // mdash
        '/&(ndash|minus|#8211|#8722);/i',        // ndash
        '/&(bull|#149|#8226);/i',                // Bullet
        '/&(pound|#163);/i',                     // Pound sign
        '/&(euro|#8364);/i',                     // Euro sign
        '/&#36;/',                               // Dollar sign
        '/&[^&;]+;/i',                           // Unknown/unhandled entities
        '/[ ]{2,}/'                              // Runs of spaces, post-handling
    );

    /**
     *  List of pattern replacements corresponding to patterns searched.
     *
     *  @var array $replace
     *  @access public
     *  @see $search
     */
    var $plain_replace = array(
        '',                                     // Non-legal carriage return
        ' ',                                    // Non-breaking space
        '"',                                    // Double quotes
        "'",                                    // Single quotes
        '>',
        '<',
        '&',
        '&',
        '&',
        '(c)',
        '(tm)',
        '(R)',
        '--',
        '-',
        '*',
        '£',
        'EUR',                                  // Euro sign. € ?
        '$',                                    // Dollar sign
        '',                                     // Unknown/unhandled entities
        ' '                                     // Runs of spaces, post-handling
    );

	/**
	 * Constructor
	 *
	 * @access public
	 */
	function __construct() {

		// Init settings
		$this->init_form_fields();
		$this->init_settings();

		// Save settings hook
		add_action( 'woocommerce_update_options_email_' . $this->id, array( $this, 'process_admin_options' ) );

		// Default template base if not declared in child constructor
		if ( is_null( $this->template_base ) ) {
			$this->template_base = WC()->plugin_path() . '/templates/';
		}

		// Settings
		$this->heading 			= $this->get_option( 'heading', $this->heading );
		$this->subject      	= $this->get_option( 'subject', $this->subject );
		$this->email_type     	= $this->get_option( 'email_type' );
		$this->enabled   		= $this->get_option( 'enabled' );

		// Find/replace
		$this->find = array( '{blogname}', '{site_title}' );
		$this->replace = array( $this->get_blogname(), $this->get_blogname() );

		// For multipart messages
		add_filter( 'phpmailer_init', array( $this, 'handle_multipart' ) );

		// For default inline styles
		add_filter( 'woocommerce_email_style_inline_tags', array( $this, 'style_inline_tags' ) );
		add_filter( 'woocommerce_email_style_inline_h1_tag', array( $this, 'style_inline_h1_tag' ) );
		add_filter( 'woocommerce_email_style_inline_h2_tag', array( $this, 'style_inline_h2_tag' ) );
		add_filter( 'woocommerce_email_style_inline_h3_tag', array( $this, 'style_inline_h3_tag' ) );
		add_filter( 'woocommerce_email_style_inline_a_tag', array( $this, 'style_inline_a_tag' ) );
		add_filter( 'woocommerce_email_style_inline_img_tag', array( $this, 'style_inline_img_tag' ) );
	}

	/**
	 * handle_multipart function.
	 *
	 * @access public
	 * @param PHPMailer $mailer
	 * @return PHPMailer
	 */
	function handle_multipart( $mailer )  {

		if ( $this->sending && $this->get_email_type() == 'multipart' ) {

			$mailer->AltBody = wordwrap( preg_replace( $this->plain_search, $this->plain_replace, strip_tags( $this->get_content_plain() ) ) );
			//$mailer->AltBody = wordwrap( html_entity_decode( strip_tags( $this->get_content_plain() ) ), 70 );
			$this->sending = false;
		}

		return $mailer;
	}

	/**
	 * format_string function.
	 *
	 * @access public
	 * @param mixed $string
	 * @return string
	 */
	function format_string( $string ) {
		return str_replace( $this->find, $this->replace, $string );
	}
	/**
	 * get_subject function.
	 *
	 * @access public
	 * @return string
	 */
	function get_subject() {
		return apply_filters( 'woocommerce_email_subject_' . $this->id, $this->format_string( $this->subject ), $this->object );
	}

	/**
	 * get_heading function.
	 *
	 * @access public
	 * @return string
	 */
	function get_heading() {
		return apply_filters( 'woocommerce_email_heading_' . $this->id, $this->format_string( $this->heading ), $this->object );
	}

	/**
	 * get_recipient function.
	 *
	 * @access public
	 * @return string
	 */
	function get_recipient() {
		return apply_filters( 'woocommerce_email_recipient_' . $this->id, $this->recipient, $this->object );
	}

	/**
	 * get_headers function.
	 *
	 * @access public
	 * @return string
	 */
	function get_headers() {
		return apply_filters( 'woocommerce_email_headers', "Content-Type: " . $this->get_content_type() . "\r\n", $this->id, $this->object );
	}

	/**
	 * get_attachments function.
	 *
	 * @access public
	 * @return array
	 */
	function get_attachments() {
		return apply_filters( 'woocommerce_email_attachments', array(), $this->id, $this->object );
	}

	/**
	 * get_type function.
	 *
	 * @access public
	 * @return string
	 */
	function get_email_type() {
		return $this->email_type ? $this->email_type : 'plain';
	}

	/**
	 * get_content_type function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_type() {
		switch ( $this->get_email_type() ) {
			case "html" :
				return 'text/html';
			case "multipart" :
				return 'multipart/alternative';
			default :
				return 'text/plain';
		}
	}

	/**
	 * Proxy to parent's get_option and attempt to localize the result using gettext.
	 * @access public
	 * @param string $key
	 * @param mixed  $empty_value
	 * @return string
	 */
	function get_option( $key, $empty_value = null ) {
		return __( parent::get_option( $key, $empty_value ) );
	}

	/**
	 * Checks if this email is enabled and will be sent.
	 *
	 * @access public
	 * @return bool
	 */
	function is_enabled() {
		$enabled = $this->enabled == "yes" ? true : false;

		return apply_filters( 'woocommerce_email_enabled_' . $this->id, $enabled, $this->object );
	}

	/**
	 * get_blogname function.
	 *
	 * @access public
	 * @return string
	 */
	function get_blogname() {
		return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}

	/**
	 * get_content function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content() {

		$this->sending = true;

		if ( $this->get_email_type() == 'plain' ) {
			$email_content = preg_replace( $this->plain_search, $this->plain_replace, strip_tags( $this->get_content_plain() ) );
		} else {
			$email_content = $this->style_inline( $this->get_content_html() );
		}

		return wordwrap( $email_content, 70 );
	}

	/**
	 * style_inline_tags function.
	 *
	 * @access public
	 * @param array $tags
	 * @return array
	 */
	function style_inline_tags($tags) {
		return array_unique( array_merge( $tags, array( 'h1', 'h2', 'h3', 'a', 'img' ) ) );
	}

	/**
	 * style_inline_h1_tag function.
	 * @access public
	 * @param array $styles
	 * @return array
	 */
	function style_inline_h1_tag($styles) {
		$styles['color'] = get_option( 'woocommerce_email_text_color' );
		$styles['display'] = 'block';
		$styles['font-family'] = 'Arial';
		$styles['font-size'] = '34px';
		$styles['font-weight'] = 'bold';
		$styles['margin-top'] = '10px';
		$styles['margin-right'] = '0';
		$styles['margin-bottom'] = '10px';
		$styles['margin-left'] = '0';
		$styles['text-align'] = 'left';
		$styles['line-height'] = '150%';

		return $styles;
	}

	/**
	 * style_inline_h2_tag function.
	 * @access public
	 * @param array $styles
	 * @return array
	 */
	function style_inline_h2_tag($styles) {
		$styles['color'] = get_option( 'woocommerce_email_text_color' );
		$styles['display'] = 'block';
		$styles['font-family'] = 'Arial';
		$styles['font-size'] = '30px';
		$styles['font-weight'] = 'bold';
		$styles['margin-top'] = '10px';
		$styles['margin-right'] = '0';
		$styles['margin-bottom'] = '10px';
		$styles['margin-left'] = '0';
		$styles['text-align'] = 'left';
		$styles['line-height'] = '150%';

		return $styles;
	}

	/**
	 * style_inline_h3_tag function.
	 *
	 * @access public
	 * @param array $styles
	 * @return array
	 */
	function style_inline_h3_tag($styles) {
		$styles['color'] = get_option( 'woocommerce_email_text_color' );
		$styles['display'] = 'block';
		$styles['font-family'] = 'Arial';
		$styles['font-size'] = '26px';
		$styles['font-weight'] = 'bold';
		$styles['margin-top'] = '10px';
		$styles['margin-right'] = '0';
		$styles['margin-bottom'] = '10px';
		$styles['margin-left'] = '0';
		$styles['text-align'] = 'left';
		$styles['line-height'] = '150%';

		return $styles;
	}

	/**
	 * @param array $styles
	 * @return array
	 */
	function style_inline_a_tag($styles) {
		$styles['color'] = get_option( 'woocommerce_email_text_color' );
		$styles['font-weight'] = 'normal';
		$styles['text-decoration'] = 'underline';

		return $styles;
	}

	/**
	 * style_inline_img_tag function.
	 *
	 * @access public
	 * @param array $styles
	 * @return array
	 */
	function style_inline_img_tag($styles) {
		$styles['display'] = 'inline';
		$styles['border'] = 'none';
		$styles['font-size'] = '14px';
		$styles['font-weight'] = 'bold';
		$styles['height'] = 'auto';
		$styles['line-height'] = '100%';
		$styles['outline'] = 'none';
		$styles['text-decoration'] = 'none';
		$styles['text-transform'] = 'capitalize';

		return $styles;
	}

	/**
	 * get_style_inline_tags function.
	 *
	 * @access public
	 * @return array
	 */
	function get_style_inline_tags() {
		return apply_filters( 'woocommerce_email_style_inline_tags', array() );
	}

	/**
	 * get_style_inline_for_tag function.
	 *
	 * @access public
	 * @param string $tag
	 * @return string
	 */
	function get_style_inline_for_tag($tag) {
		$styles = apply_filters( 'woocommerce_email_style_inline_' . $tag . '_tag',  array() );
		$css = array();

		foreach( $styles as $property => $value ) {
			$css[] = $property . ':' . $value;
		}

		return implode('; ', $css);
	}

	/**
	 * Apply inline styles to dynamic content.
	 *
	 * @access public
	 * @param mixed $content
	 * @return string
	 */
	function style_inline( $content ) {
		if ( ! class_exists( 'DOMDocument' ) ) {
			return $content;
		}

		$dom = new DOMDocument();
		libxml_use_internal_errors( true );
    		@$dom->loadHTML( $content );
    		libxml_clear_errors();

		foreach( $this->get_style_inline_tags() as $tag ) {
			$nodes = $dom->getElementsByTagName($tag);

			foreach( $nodes as $node ) {
				if ( ! $node->hasAttribute( 'style' ) ) {
					$node->setAttribute( 'style', $this->get_style_inline_for_tag($tag) );
				}
			}
		}

		$content = $dom->saveHTML();

		return $content;
	}

	/**
	 * get_content_plain function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_plain() {}

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_html() {}

	/**
	 * Get from name for email.
	 *
	 * @access public
	 * @return string
	 */
	function get_from_name() {
		return wp_specialchars_decode( esc_html( get_option( 'woocommerce_email_from_name' ) ), ENT_QUOTES );
	}

	/**
	 * Get from email address.
	 *
	 * @access public
	 * @return string
	 */
	function get_from_address() {
		return sanitize_email( get_option( 'woocommerce_email_from_address' ) );
	}

	/**
	 * Send the email.
	 *
	 * @access public
	 * @param mixed $to
	 * @param mixed $subject
	 * @param mixed $message
	 * @param string $headers
	 * @param string $attachments
	 * @return bool
	 */
	function send( $to, $subject, $message, $headers, $attachments ) {
		add_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
		add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

		$return = wp_mail( $to, $subject, $message, $headers, $attachments );

		remove_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
		remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
		
		return $return;
	}

    /**
     * Initialise Settings Form Fields - these are generic email options most will use.
     *
     * @access public
     * @return void
     */
    function init_form_fields() {
    	$this->form_fields = array(
			'enabled' => array(
				'title' 		=> __( 'Enable/Disable', 'woocommerce' ),
				'type' 			=> 'checkbox',
				'label' 		=> __( 'Enable this email notification', 'woocommerce' ),
				'default' 		=> 'yes'
			),
			'subject' => array(
				'title' 		=> __( 'Email subject', 'woocommerce' ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'Defaults to <code>%s</code>', 'woocommerce' ), $this->subject ),
				'placeholder' 	=> '',
				'default' 		=> ''
			),
			'heading' => array(
				'title' 		=> __( 'Email heading', 'woocommerce' ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'Defaults to <code>%s</code>', 'woocommerce' ), $this->heading ),
				'placeholder' 	=> '',
				'default' 		=> ''
			),
			'email_type' => array(
				'title' 		=> __( 'Email type', 'woocommerce' ),
				'type' 			=> 'select',
				'description' 	=> __( 'Choose which format of email to send.', 'woocommerce' ),
				'default' 		=> 'html',
				'class'			=> 'email_type',
				'options'		=> array(
					'plain' 		=> __( 'Plain text', 'woocommerce' ),
					'html' 			=> __( 'HTML', 'woocommerce' ),
					'multipart' 	=> __( 'Multipart', 'woocommerce' ),
				)
			)
		);
    }

	/**
	 * Admin Panel Options Processing
	 * - Saves the options to the DB
	 *
	 * @since 1.0.0
	 * @access public
	 * @return bool
	 */
    public function process_admin_options() {

    	// Save regular options
    	parent::process_admin_options();

    	// Save templates
		if ( ! empty( $_POST['template_html_code'] ) && ! empty( $this->template_html ) ) {

			$saved	= false;
			$file	= get_stylesheet_directory() . '/woocommerce/' . $this->template_html;
			$code 	= stripslashes( $_POST['template_html_code'] );

			if ( is_writeable( $file ) ) {
				$f = fopen( $file, 'w+' );
				if ( $f !== FALSE ) {
    				fwrite( $f, $code );
    				fclose( $f );
    				$saved = true;
    			}
			}

			if ( ! $saved ) {
    			$redirect = add_query_arg( 'wc_error', urlencode( __( 'Could not write to template file.', 'woocommerce' ) ) );
    			wp_redirect( $redirect );
    			exit;
    		}
		}
		if ( ! empty( $_POST['template_plain_code'] ) && ! empty( $this->template_plain ) ) {

    		$saved	= false;
			$file	= get_stylesheet_directory() . '/woocommerce/' . $this->template_plain;
			$code 	= stripslashes( $_POST['template_plain_code'] );

			if ( is_writeable( $file ) ) {
				$f = fopen( $file, 'w+' );
				if ( $f !== FALSE ) {
    				fwrite( $f, $code );
    				fclose( $f );
    				$saved = true;
    			}
			}

			if ( ! $saved ) {
    			$redirect = add_query_arg( 'wc_error', __( 'Could not write to template file.', 'woocommerce' ) );
    			wp_redirect( $redirect );
    			exit;
    		}
		}
    }

	/**
	 * Admin Options
	 *
	 * Setup the gateway settings screen.
	 * Override this in your gateway.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	function admin_options() {

		// Handle any actions
		if ( ! empty( $this->template_html ) || ! empty( $this->template_plain ) ) {

			if ( ! empty( $_GET['move_template'] ) && ( $template = esc_attr( basename( $_GET['move_template'] ) ) ) ) {
				if ( ! empty( $this->$template ) ) {
					if (  wp_mkdir_p( dirname( get_stylesheet_directory() . '/woocommerce/' . $this->$template ) ) && ! file_exists( get_stylesheet_directory() . '/woocommerce/' . $this->$template ) ) {
						// Locate template file
						$core_file		= $this->template_base . $this->$template;
						$template_file	= apply_filters( 'woocommerce_locate_core_template', $core_file, $this->$template, $this->template_base );

						// Copy template file
						copy( $template_file, get_stylesheet_directory() . '/woocommerce/' . $this->$template );
						echo '<div class="updated fade"><p>' . __( 'Template file copied to theme.', 'woocommerce' ) . '</p></div>';
					}
				}
			}

			if ( ! empty( $_GET['delete_template'] ) && ( $template = esc_attr( basename( $_GET['delete_template'] ) ) ) ) {
				if ( ! empty( $this->$template ) ) {
					if ( file_exists( get_stylesheet_directory() . '/woocommerce/' . $this->$template ) ) {
						unlink( get_stylesheet_directory() . '/woocommerce/' . $this->$template );
						echo '<div class="updated fade"><p>' . __( 'Template file deleted from theme.', 'woocommerce' ) . '</p></div>';
					}
				}
			}

		}

		?>
		<h3><?php echo ( ! empty( $this->title ) ) ? $this->title : __( 'Settings','woocommerce' ) ; ?></h3>

		<?php echo ( ! empty( $this->description ) ) ? wpautop( $this->description ) : ''; ?>

		<table class="form-table">
			<?php $this->generate_settings_html(); ?>
		</table>

		<?php if ( ! empty( $this->template_html ) || ! empty( $this->template_plain ) ) { ?>
			<div id="template">
			<?php
				$templates = array(
					'template_html' 	=> __( 'HTML template', 'woocommerce' ),
					'template_plain' 	=> __( 'Plain text template', 'woocommerce' )
				);
				foreach ( $templates as $template => $title ) :
					if ( empty( $this->$template ) ) {
						continue;
					}

					$local_file		= get_stylesheet_directory() . '/woocommerce/' . $this->$template;
					$core_file		= $this->template_base . $this->$template;
					$template_file	= apply_filters( 'woocommerce_locate_core_template', $core_file, $this->$template, $this->template_base );
					?>
					<div class="template <?php echo $template; ?>">

						<h4><?php echo wp_kses_post( $title ); ?></h4>

						<?php if ( file_exists( $local_file ) ) { ?>

							<p>
								<a href="#" class="button toggle_editor"></a>

								<?php if ( is_writable( $local_file ) ) : ?>
									<a href="<?php echo remove_query_arg( array( 'move_template', 'saved' ), add_query_arg( 'delete_template', $template ) ); ?>" class="delete_template button"><?php _e( 'Delete template file', 'woocommerce' ); ?></a>
								<?php endif; ?>

								<?php printf( __( 'This template has been overridden by your theme and can be found in: <code>%s</code>.', 'woocommerce' ), 'yourtheme/woocommerce/' . $this->$template ); ?>
							</p>

							<div class="editor" style="display:none">

								<textarea class="code" cols="25" rows="20" <?php if ( ! is_writable( $local_file ) ) : ?>readonly="readonly" disabled="disabled"<?php else : ?>data-name="<?php echo $template . '_code'; ?>"<?php endif; ?>><?php echo file_get_contents( $local_file ); ?></textarea>

							</div>

						<?php } elseif ( file_exists( $template_file ) ) { ?>

							<p>
								<a href="#" class="button toggle_editor"></a>

								<?php if ( ( is_dir( get_stylesheet_directory() . '/woocommerce/emails/' ) && is_writable( get_stylesheet_directory() . '/woocommerce/emails/' ) ) || is_writable( get_stylesheet_directory() ) ) { ?>
									<a href="<?php echo remove_query_arg( array( 'delete_template', 'saved' ), add_query_arg( 'move_template', $template ) ); ?>" class="button"><?php _e( 'Copy file to theme', 'woocommerce' ); ?></a>
								<?php } ?>

								<?php printf( __( 'To override and edit this email template copy <code>%s</code> to your theme folder: <code>%s</code>.', 'woocommerce' ), plugin_basename( $template_file ) , 'yourtheme/woocommerce/' . $this->$template ); ?>
							</p>

							<div class="editor" style="display:none">

								<textarea class="code" readonly="readonly" disabled="disabled" cols="25" rows="20"><?php echo file_get_contents( $template_file ); ?></textarea>

							</div>

						<?php } else { ?>

							<p><?php _e( 'File was not found.', 'woocommerce' ); ?></p>

						<?php } ?>

					</div>
					<?php
				endforeach;
			?>
			</div>
			<?php
			wc_enqueue_js("
				jQuery('select.email_type').change(function(){

					var val = jQuery( this ).val();

					jQuery('.template_plain, .template_html').show();

					if ( val != 'multipart' && val != 'html' )
						jQuery('.template_html').hide();

					if ( val != 'multipart' && val != 'plain' )
						jQuery('.template_plain').hide();

				}).change();

				var view = '" . esc_js( __( 'View template', 'woocommerce' ) ) . "';
				var hide = '" . esc_js( __( 'Hide template', 'woocommerce' ) ) . "';

				jQuery('a.toggle_editor').text( view ).toggle( function() {
					jQuery( this ).text( hide ).closest('.template').find('.editor').slideToggle();
					return false;
				}, function() {
					jQuery( this ).text( view ).closest('.template').find('.editor').slideToggle();
					return false;
				} );

				jQuery('a.delete_template').click(function(){
					var answer = confirm('" . esc_js( __( 'Are you sure you want to delete this template file?', 'woocommerce' ) ) . "');

					if (answer)
						return true;

					return false;
				});

				jQuery('.editor textarea').change(function(){
					var name = jQuery(this).attr( 'data-name' );

					if ( name )
						jQuery(this).attr( 'name', name );
				});
			");
		}
	}
}
